<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetDocument;
use App\Models\AssetMutation;
use App\Models\AssetRepairPhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AssetController extends Controller
{
    private const IMPORT_HEADERS = [
        'no_aset_accurate', 'departemen', 'nama', 'jenis', 'merk', 'model', 'serial_number', 'cabang', 'lokasi',
        'pengguna', 'departemen_pengguna', 'status', 'tanggal_beli', 'garansi_sampai',
        'processor', 'ram', 'storage', 'os', 'spesifikasi_lain', 'catatan',
    ];

    public function index(Request $request)
    {
        $q = Asset::query()->with('user')->withCount('tickets')->orderByDesc('id');

        if ($request->filled('q')) {
            $term = '%'.$request->q.'%';
            $q->where(fn ($qq) => $qq
                ->where('code', 'like', $term)
                ->orWhere('legacy_code', 'like', $term)
                ->orWhere('accurate_no', 'like', $term)
                ->orWhere('name', 'like', $term)
                ->orWhere('serial_number', 'like', $term)
                ->orWhere('user_name', 'like', $term)
                ->orWhereHas('user', fn ($u) => $u->where('name', 'like', $term)));
        }
        foreach (['type', 'cabang', 'status', 'department'] as $f) {
            if ($request->filled($f)) {
                $q->where($f, $request->input($f));
            }
        }

        $sort = $request->input('sort');
        if (in_array($sort, ['purchase_asc', 'purchase_desc'], true)) {
            $q->reorder()->orderByRaw('purchase_date is null')->orderBy('purchase_date', $sort === 'purchase_asc' ? 'asc' : 'desc')->orderBy('id');
        }

        $assets = $q->with('documents')->paginate(20)->withQueryString();

        return view('assets.index', compact('assets'));
    }

    public function dashboard()
    {
        $today = now()->startOfDay();
        $live = fn () => Asset::query()->where('status', '!=', 'disposal');
        $group = fn (string $col) => $live()->whereNotNull($col)->selectRaw("$col as label, count(*) as total")
            ->groupBy($col)->orderByDesc('total')->pluck('total', 'label');

        $repairCost = function ($from) {
            return (float) \Illuminate\Support\Facades\DB::table('asset_repair_parts as p')
                ->join('asset_repairs as r', 'r.id', '=', 'p.asset_repair_id')
                ->where('r.repaired_at', '>=', $from)
                ->sum(\Illuminate\Support\Facades\DB::raw('p.qty * coalesce(p.cost, 0)'));
        };

        $audit = \App\Models\AssetAudit::current();
        $auditTotal = $audit?->assetsInScope()->count() ?? 0;
        $auditDone = $audit ? $audit->assetsInScope()->whereIn('id', $audit->items()->pluck('asset_id'))->count() : 0;

        $data = [
            'total'        => Asset::count(),
            'live'         => $live()->count(),
            'byStatus'     => Asset::selectRaw('status as label, count(*) as total')->groupBy('status')->pluck('total', 'label'),
            'byType'       => $group('type'),
            'byCabang'     => $group('cabang'),
            'byDepartment' => $group('department'),
            'warrantyExpired'  => $live()->whereNotNull('warranty_until')->where('warranty_until', '<', $today)->count(),
            'warrantySoon'     => $live()->whereBetween('warranty_until', [$today, $today->copy()->addDays(60)])->orderBy('warranty_until')->limit(8)->get(),
            'repairsMonth'     => \App\Models\AssetRepair::where('repaired_at', '>=', now()->startOfMonth())->count(),
            'costMonth'        => $repairCost(now()->startOfMonth()),
            'costYear'         => $repairCost(now()->startOfYear()),
            'topRepaired'      => Asset::withCount('repairs')->having('repairs_count', '>', 0)->orderByDesc('repairs_count')->limit(5)->get(),
            'recentRepairs'    => \App\Models\AssetRepair::with('asset')->latest('repaired_at')->latest('id')->limit(5)->get(),
            'recentMutations'  => \App\Models\AssetMutation::with('asset')->latest('mutated_at')->latest('id')->limit(5)->get(),
            'openTickets'      => \App\Models\Ticket::whereNotNull('asset_id')->whereIn('status', ['open', 'in_progress'])->count(),
            'audit'            => $audit,
            'auditTotal'       => $auditTotal,
            'auditDone'        => $auditDone,
            'noHolder'         => $live()->whereNull('user_id')->whereNull('user_name')->count(),
            'noPhoto'          => $live()->whereNull('photo_path')->count(),
            'noDepartment'     => $live()->whereNull('department')->count(),
            'noAccurate'       => $live()->whereNull('accurate_no')->count(),
            'docIncomplete'    => $live()->where(fn ($q) => $q->whereNull('photo_path')
                ->orWhereRaw('(select count(*) from asset_documents d where d.asset_id = assets.id) < 3'))->count(),
        ];

        return view('assets.dashboard', $data);
    }

    public function create()
    {
        return view('assets.form', ['asset' => new Asset(['status' => 'aktif']), 'users' => $this->users()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        unset($data['photo'], $data['remove_photo'], $data['doc_box'], $data['doc_serial'], $data['doc_receipt']);

        $asset = Asset::create($data + ['created_by' => Auth::id()]);
        $this->savePhoto($request, $asset);
        foreach (['box' => 'doc_box', 'serial' => 'doc_serial', 'receipt' => 'doc_receipt'] as $type => $field) {
            if ($request->hasFile($field)) {
                $this->saveDocument($asset, $type, $request->file($field));
            }
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'user',
            'tickets' => fn ($q) => $q->with('takenByUser')->limit(50),
            'repairs.parts', 'repairs.creator', 'repairs.ticket', 'repairs.photos',
            'mutations.creator', 'documents',
        ]);

        $users = $this->users();

        $openAudit = \App\Models\AssetAudit::current();

        // lampiran tiket (foto) yang bisa dipakai sebagai foto alasan perbaikan
        $ticketPhotos = \Illuminate\Support\Facades\DB::table('ticket_attachments')
            ->whereIn('ticket_id', $asset->tickets->pluck('id'))
            ->get(['id', 'ticket_id', 'file_name', 'file_path'])
            ->filter(fn ($f) => preg_match('/\.(jpe?g|png)$/i', $f->file_name))
            ->groupBy('ticket_id')
            ->map(fn ($rows) => $rows->map(fn ($f) => ['id' => $f->id, 'name' => $f->file_name, 'url' => asset('storage/'.$f->file_path)])->values());

        return view('assets.show', compact('asset', 'users', 'openAudit', 'ticketPhotos'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.form', ['asset' => $asset, 'users' => $this->users()]);
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $this->validated($request, $asset);
        unset($data['photo'], $data['remove_photo'], $data['doc_box'], $data['doc_serial'], $data['doc_receipt']);

        $location = [
            'cabang'          => $data['cabang'] ?? null,
            'location'        => $data['location'] ?? null,
            'user_id'         => $data['user_id'] ?? null,
            'user_name'       => $data['user_name'] ?? null,
            'user_department' => $data['user_department'] ?? null,
        ];
        unset($data['cabang'], $data['location'], $data['user_id'], $data['user_name'], $data['user_department']);

        $asset->update($data);
        $asset->mutateTo($location, 'Perubahan lewat edit data aset', null, Auth::id());
        $this->savePhoto($request, $asset);

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil diperbarui.');
    }

    public function destroy(Asset $asset)
    {
        if ($asset->tickets()->exists()) {
            return back()->with('error', 'Aset sudah punya riwayat tiket, tidak bisa dihapus. Ubah statusnya menjadi Disposal.');
        }

        $this->deletePhoto($asset);
        $asset->delete();

        return redirect()->route('assets.index')->with('success', 'Aset dihapus.');
    }

    public function labels(Request $request)
    {
        $ids = array_filter((array) $request->input('ids'), 'is_numeric');
        abort_if(empty($ids), 404);

        $assets = Asset::whereIn('id', $ids)->orderBy('code')->get();

        return view('assets.labels', compact('assets'));
    }

    // Hasil scan QR (publik): IT yang sudah login -> detail aset, selain itu -> halaman aset
    // publik yang hanya menampilkan kode, nama, dan pemegang aset
    public function scan(string $code)
    {
        $asset = Asset::byCode($code)->firstOrFail();

        if (Auth::check() && Auth::user()->role === 'it') {
            return redirect()->route('assets.show', $asset);
        }

        return view('assets.public', compact('asset'));
    }

    // Tombol "Buat Tiket" di halaman depan: wajib login, lalu form tiket terbuka dengan aset terisi
    public function scanTicket(string $code)
    {
        $asset = Asset::byCode($code)->firstOrFail();

        return redirect()->route('welcome', ['open_ticket' => 1, 'asset' => $asset->code]);
    }

    public function importForm()
    {
        return view('assets.import');
    }

    public function importTemplate()
    {
        $sheet = new Spreadsheet();
        $ws = $sheet->getActiveSheet();
        $ws->fromArray([
            self::IMPORT_HEADERS,
            ['FA-00123', 'IT', 'Laptop Finance 01', 'Laptop', 'Lenovo', 'ThinkPad E14', 'PF3ABC12', 'Head Office', 'Lantai 2', 'Budi', 'Finance', 'aktif', '2024-03-15', '2027-03-15', 'Core i5 gen 11', '8GB', '512GB SSD', 'Windows 11', '', ''],
        ]);

        return response()->streamDownload(function () use ($sheet) {
            (new Xlsx($sheet))->save('php://output');
        }, 'template-import-aset.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120']]);

        $rows = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet()->toArray(null, true, false, false);

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows) ?? []);
        $missing = array_diff(['nama', 'jenis'], $header);
        if ($missing) {
            return back()->with('error', 'Kolom wajib tidak ditemukan: '.implode(', ', $missing).'. Gunakan template yang disediakan.');
        }

        $created = 0;
        $updated = 0;
        $skipped = [];

        foreach ($rows as $i => $row) {
            $data = [];
            foreach ($header as $col => $name) {
                $data[$name] = isset($row[$col]) ? trim((string) $row[$col]) : '';
            }

            if (($data['nama'] ?? '') === '' && ($data['jenis'] ?? '') === '') {
                continue; // baris kosong
            }

            $line = $i + 2;

            if (($data['nama'] ?? '') === '' || ($data['jenis'] ?? '') === '') {
                $skipped[] = "baris {$line}: nama dan jenis wajib diisi";
                continue;
            }

            $accurateNo = $this->cleanAccurateNo($data['no_aset_accurate'] ?? '');
            $existing = $accurateNo ? Asset::where('accurate_no', $accurateNo)->first() : null;

            $serial = $data['serial_number'] ?? '';
            if ($serial !== '' && Asset::where('serial_number', $serial)->when($existing, fn ($q) => $q->where('id', '!=', $existing->id))->exists()) {
                $skipped[] = "baris {$line}: serial number {$serial} sudah terdaftar";
                continue;
            }

            $status = strtolower($data['status'] ?? '');

            // kolom pengguna: cocokkan dengan akun (nama/email), jika tidak ketemu simpan sebagai teks
            $holder = $data['pengguna'] ?? '';
            $holderUser = $holder !== ''
                ? \App\Models\User::whereRaw('lower(name) = ?', [strtolower($holder)])->orWhereRaw('lower(email) = ?', [strtolower($holder)])->first()
                : null;

            $attrs = [
                'accurate_no'    => $accurateNo,
                'name'           => $data['nama'],
                'type'           => $data['jenis'],
                'brand'          => $data['merk'] ?? null ?: null,
                'model'          => $data['model'] ?? null ?: null,
                'serial_number'  => $serial ?: null,
                'cabang'         => ($data['cabang'] ?? '') ?: null,
                'location'       => ($data['lokasi'] ?? '') ?: null,
                'user_id'        => $holderUser?->id,
                'user_name'      => $holderUser ? null : ($holder ?: null),
                'status'         => array_key_exists($status, Asset::STATUSES) ? $status : 'aktif',
                'purchase_date'  => $this->parseDate($data['tanggal_beli'] ?? ''),
                'warranty_until' => $this->parseDate($data['garansi_sampai'] ?? ''),
                'notes'          => ($data['catatan'] ?? '') ?: null,
                'user_department' => ($data['departemen_pengguna'] ?? '') ?: null,
                'spec_cpu'       => ($data['processor'] ?? '') ?: null,
                'spec_ram'       => ($data['ram'] ?? '') ?: null,
                'spec_storage'   => ($data['storage'] ?? '') ?: null,
                'spec_os'        => ($data['os'] ?? '') ?: null,
                'spec_other'     => ($data['spesifikasi_lain'] ?? '') ?: null,
            ];

            $dept = strtoupper($data['departemen'] ?? '');
            $dept = array_key_exists($dept, Asset::DEPARTMENTS) ? $dept : null;

            if ($existing) {
                // sudah ada (No. Aset Accurate sama): perbarui data, tanpa membuat dobel.
                // Kode aset tidak berubah.
                $existing->update($attrs + ($dept ? ['department' => $dept] : []));
                $updated++;
            } else {
                Asset::create($attrs + ['department' => $dept ?? 'IT', 'created_by' => Auth::id()]);
                $created++;
            }
        }

        $msg = "{$created} aset baru diimport".($updated ? ", {$updated} aset diperbarui" : '').'.';
        if ($skipped) {
            $msg .= ' Dilewati '.count($skipped).' baris: '.implode('; ', array_slice($skipped, 0, 5)).(count($skipped) > 5 ? '; ...' : '');
        }

        return redirect()->route('assets.index')->with(($created || $updated) ? 'success' : 'error', $msg);
    }

    public function reconcileForm()
    {
        return view('assets.reconcile', ['result' => null]);
    }

    /**
     * Bandingkan ekspor daftar aset Accurate dengan data helpdesk (hanya baca, tidak mengubah data).
     */
    public function reconcile(Request $request)
    {
        $request->validate(['file' => ['required', 'file', 'mimes:xlsx,xls,csv,txt', 'max:5120']]);

        $rows = IOFactory::load($request->file('file')->getRealPath())
            ->getActiveSheet()->toArray(null, true, false, false);

        $header = array_map(fn ($h) => strtolower(trim((string) $h)), array_shift($rows) ?? []);

        $noCol = $this->findColumn($header, ['no_aset_accurate', 'no. aset', 'no aset', 'nomor aset', 'kode aset', 'no_aset', 'asset no', 'asset number']);
        $nameCol = $this->findColumn($header, ['nama', 'nama aset', 'deskripsi', 'description', 'asset name']);

        if ($noCol === null) {
            return back()->with('error', 'Kolom nomor aset tidak ditemukan. Gunakan header "No. Aset" atau "no_aset_accurate".');
        }

        $accurate = [];
        foreach ($rows as $row) {
            $no = $this->normalizeNo($row[$noCol] ?? '');
            if ($no !== '') {
                $accurate[$no] = $nameCol !== null ? trim((string) ($row[$nameCol] ?? '')) : '';
            }
        }

        $assets = Asset::whereNotNull('accurate_no')->get()->keyBy(fn ($a) => $this->normalizeNo($a->accurate_no));

        $result = [
            'total_accurate' => count($accurate),
            'total_helpdesk' => $assets->count(),
            'matched'        => count(array_intersect_key($accurate, $assets->all())),
            'only_accurate'  => collect(array_diff_key($accurate, $assets->all()))
                ->map(fn ($name, $no) => ['no' => $no, 'name' => $name])->values(),
            'only_helpdesk'  => $assets->reject(fn ($a, $no) => isset($accurate[$no]))->values(),
            'disposed_still' => $assets->filter(fn ($a, $no) => isset($accurate[$no]) && $a->status === 'disposal')->values(),
        ];

        return view('assets.reconcile', compact('result'));
    }

    private function findColumn(array $header, array $candidates): ?int
    {
        foreach ($candidates as $c) {
            $i = array_search($c, $header, true);
            if ($i !== false) {
                return $i;
            }
        }

        return null;
    }

    public function storeRepair(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'repaired_at'    => ['required', 'date'],
            'problem'        => ['required', 'string', 'max:2000'],
            'action'         => ['nullable', 'string', 'max:2000'],
            'vendor'         => ['nullable', 'string', 'max:150'],
            'ticket_id'      => ['nullable', 'integer', Rule::exists('tickets', 'id')->where('asset_id', $asset->id)],
            'status_after'   => ['nullable', Rule::in(array_keys(Asset::STATUSES))],
            'photos'         => ['nullable', 'array', 'max:10'],
            'photos.*'       => ['file', 'mimes:jpg,jpeg', 'max:5120'],
            'ticket_photos'  => ['nullable', 'array'],
            'ticket_photos.*' => ['integer'],
            'parts'          => ['nullable', 'array'],
            'parts.*.name'   => ['nullable', 'string', 'max:255'],
            'parts.*.detail' => ['nullable', 'string', 'max:255'],
            'parts.*.qty'    => ['nullable', 'integer', 'min:1', 'max:999'],
            'parts.*.cost'   => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
        ]);

        $repair = $asset->repairs()->create([
            'repaired_at' => $data['repaired_at'],
            'problem'     => $data['problem'],
            'action'      => $data['action'] ?? null,
            'vendor'      => $data['vendor'] ?? null,
            'ticket_id'   => $data['ticket_id'] ?? null,
            'created_by'  => Auth::id(),
        ]);

        foreach ($data['parts'] ?? [] as $part) {
            if (trim((string) ($part['name'] ?? '')) === '') {
                continue;
            }

            $repair->parts()->create([
                'name'   => trim($part['name']),
                'detail' => ($part['detail'] ?? '') ?: null,
                'qty'    => $part['qty'] ?? 1,
                'cost'   => ($part['cost'] ?? '') !== '' ? $part['cost'] : null,
            ]);
        }

        foreach ($request->file('photos', []) as $photo) {
            $repair->photos()->create([
                'path'          => $photo->store("asset-repairs/{$repair->id}", 'local'),
                'original_name' => $photo->getClientOriginalName(),
            ]);
        }

        // foto dari lampiran tiket (claim) yang terhubung ke aset ini
        if (!empty($data['ticket_photos']) && !empty($data['ticket_id'])) {
            $files = \Illuminate\Support\Facades\DB::table('ticket_attachments')
                ->where('ticket_id', $data['ticket_id'])->whereIn('id', $data['ticket_photos'])->get();
            foreach ($files as $f) {
                $public = \Illuminate\Support\Facades\Storage::disk('public');
                if (!$public->exists($f->file_path)) {
                    continue;
                }
                $dest = "asset-repairs/{$repair->id}/".uniqid().'-'.basename($f->file_path);
                \Illuminate\Support\Facades\Storage::disk('local')->put($dest, $public->get($f->file_path));
                $repair->photos()->create(['path' => $dest, 'original_name' => $f->file_name]);
            }
        }

        if (!empty($data['status_after'])) {
            $asset->update(['status' => $data['status_after']]);
        }

        return redirect()->route('assets.show', $asset)->with(['success' => 'Perbaikan berhasil dicatat.', 'tab' => 'perbaikan']);
    }

    public function mutate(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'mutated_at'      => ['required', 'date'],
            'from_cabang'     => ['nullable', 'string', 'max:50'],
            'cabang'          => ['nullable', 'string', 'max:50'],
            'location'        => ['nullable', 'string', 'max:150'],
            'user_id'         => ['nullable', 'integer', 'exists:users,id'],
            'user_name'       => ['nullable', 'string', 'max:150'],
            'user_department' => ['nullable', 'string', 'max:150'],
            'reason'          => ['required', 'string', 'max:500'],
            'photo'           => ['nullable', 'file', 'mimes:jpg,jpeg', 'max:5120'],
            'receipt'         => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
        ], [
            'photo.mimes'   => 'Foto unit harus berformat JPG.',
            'receipt.mimes' => 'Tanda terima harus berformat PDF.',
        ]);

        $extra = ['from_cabang' => $data['from_cabang'] ?? null];
        if ($request->hasFile('photo')) {
            $extra['photo_path'] = $request->file('photo')->store("asset-mutations/{$asset->id}", 'local');
        }
        if ($request->hasFile('receipt')) {
            $extra['receipt_path'] = $request->file('receipt')->store("asset-mutations/{$asset->id}", 'local');
        }

        $changed = $asset->mutateTo($data, $data['reason'], $data['mutated_at'], Auth::id(), $extra);

        if (!$changed) {
            foreach (['photo_path', 'receipt_path'] as $k) {
                if (!empty($extra[$k])) {
                    \Illuminate\Support\Facades\Storage::disk('local')->delete($extra[$k]);
                }
            }
        }

        return redirect()->route('assets.show', $asset)->with([
            ($changed ? 'success' : 'error') => $changed ? 'Mutasi aset berhasil dicatat.' : 'Tidak ada perubahan lokasi, pengguna, atau departemen pengguna.',
            'tab' => 'mutasi',
        ]);
    }

    public function storeDocument(Request $request, Asset $asset, string $type)
    {
        abort_unless(array_key_exists($type, Asset::DOCUMENT_TYPES), 404);

        $request->validate(
            ['file' => ['required', 'file', 'mimes:'.($type === 'receipt' ? 'pdf' : 'jpg,jpeg'), 'max:'.($type === 'receipt' ? 10240 : 5120)]],
            ['file.mimes' => $type === 'receipt' ? 'Tanda terima harus berformat PDF.' : 'Foto harus berformat JPG.']
        );

        $this->saveDocument($asset, $type, $request->file('file'));

        return redirect()->route('assets.show', $asset)->with(['success' => 'Dokumen disimpan.', 'tab' => 'dokumen']);
    }

    public function destroyDocument(AssetDocument $document)
    {
        $asset = $document->asset;
        \Illuminate\Support\Facades\Storage::disk('local')->delete($document->path);
        $document->delete();

        return redirect()->route('assets.show', $asset)->with(['success' => 'Dokumen dihapus.', 'tab' => 'dokumen']);
    }

    public function showDocument(AssetDocument $document)
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->response($document->path, $document->original_name);
    }

    public function mutationFile(AssetMutation $mutation, string $kind)
    {
        $path = match ($kind) {
            'photo'   => $mutation->photo_path,
            'receipt' => $mutation->receipt_path,
            default   => null,
        };
        abort_unless($path, 404);

        return \Illuminate\Support\Facades\Storage::disk('local')->response($path);
    }

    public function repairPhoto(AssetRepairPhoto $photo)
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->response($photo->path, $photo->original_name);
    }

    private function saveDocument(Asset $asset, string $type, \Illuminate\Http\UploadedFile $file): void
    {
        $existing = $asset->documents()->where('type', $type)->first();
        if ($existing) {
            \Illuminate\Support\Facades\Storage::disk('local')->delete($existing->path);
        }

        $asset->documents()->updateOrCreate(['type' => $type], [
            'path'          => $file->store("asset-documents/{$asset->id}", 'local'),
            'original_name' => $file->getClientOriginalName(),
            'mime'          => $file->getMimeType(),
            'uploaded_by'   => Auth::id(),
        ]);
        $asset->unsetRelation('documents');
    }

    private function savePhoto(Request $request, Asset $asset): void
    {
        if ($request->hasFile('photo')) {
            $this->deletePhoto($asset);
            $asset->update(['photo_path' => $request->file('photo')->store('assets', 'public')]);
        } elseif ($request->boolean('remove_photo')) {
            $this->deletePhoto($asset);
            $asset->update(['photo_path' => null]);
        }
    }

    private function deletePhoto(Asset $asset): void
    {
        if ($asset->photo_path) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($asset->photo_path);
        }
    }

    private function users()
    {
        return \App\Models\User::orderBy('name')->get(['id', 'name', 'email']);
    }

    private function cleanAccurateNo(?string $value): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : $value;
    }

    // Normalisasi nomor untuk pencocokan (abaikan huruf besar/kecil dan spasi)
    private function normalizeNo(?string $value): string
    {
        return strtoupper(preg_replace('/\s+/', '', (string) $value));
    }

    private function parseDate(string $value): ?string
    {
        if ($value === '') {
            return null;
        }

        try {
            if (is_numeric($value)) {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            }

            return \Carbon\Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function validated(Request $request, ?Asset $asset = null): array
    {
        return $request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'type'           => ['required', 'string', 'max:50'],
            'brand'          => ['nullable', 'string', 'max:100'],
            'model'          => ['nullable', 'string', 'max:100'],
            'department'     => [$asset ? 'nullable' : 'required', Rule::in(array_keys(Asset::DEPARTMENTS))],
            'accurate_no'    => ['nullable', 'string', 'max:50', Rule::unique('assets', 'accurate_no')->ignore($asset?->id)],
            'serial_number'  => ['nullable', 'string', 'max:100', Rule::unique('assets', 'serial_number')->ignore($asset?->id)],
            'cabang'         => [$asset ? 'nullable' : 'required', 'string', 'max:50'],
            'location'       => ['nullable', 'string', 'max:150'],
            'user_id'        => ['nullable', 'integer', 'exists:users,id'],
            'user_name'      => ['nullable', 'string', 'max:150'],
            'status'         => ['required', Rule::in(array_keys(Asset::STATUSES))],
            'purchase_date'  => ['nullable', 'date'],
            'warranty_until' => ['nullable', 'date', 'after_or_equal:purchase_date'],
            'notes'          => ['nullable', 'string'],
            'spec_cpu'       => ['nullable', 'string', 'max:150'],
            'spec_ram'       => ['nullable', 'string', 'max:150'],
            'spec_storage'   => ['nullable', 'string', 'max:150'],
            'spec_os'        => ['nullable', 'string', 'max:150'],
            'spec_other'     => ['nullable', 'string', 'max:2000'],
            'user_department' => ['nullable', 'string', 'max:150'],
            'doc_box'        => ['nullable', 'file', 'mimes:jpg,jpeg', 'max:5120'],
            'doc_serial'     => ['nullable', 'file', 'mimes:jpg,jpeg', 'max:5120'],
            'doc_receipt'    => ['nullable', 'file', 'mimes:pdf', 'max:10240'],
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo'   => ['nullable', 'boolean'],
        ], [
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo.max'   => 'Ukuran foto maksimal 3 MB.',
            'doc_box.mimes'     => 'Foto dus harus berformat JPG.',
            'doc_serial.mimes'  => 'Foto serial number harus berformat JPG.',
            'doc_receipt.mimes' => 'Tanda terima harus berformat PDF.',
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Asset;
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
        'pengguna', 'status', 'tanggal_beli', 'garansi_sampai', 'catatan',
    ];

    public function index(Request $request)
    {
        $q = Asset::query()->with('user')->withCount('tickets')->orderByDesc('id');

        if ($request->filled('q')) {
            $term = '%'.$request->q.'%';
            $q->where(fn ($qq) => $qq
                ->where('code', 'like', $term)
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

        $assets = $q->paginate(20)->withQueryString();

        return view('assets.index', compact('assets'));
    }

    public function create()
    {
        return view('assets.form', ['asset' => new Asset(['status' => 'aktif']), 'users' => $this->users()]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        unset($data['photo'], $data['remove_photo']);

        $asset = Asset::create($data + ['created_by' => Auth::id()]);
        $this->savePhoto($request, $asset);

        return redirect()->route('assets.show', $asset)->with('success', 'Aset berhasil ditambahkan.');
    }

    public function show(Asset $asset)
    {
        $asset->load([
            'user',
            'tickets' => fn ($q) => $q->with('takenByUser')->limit(50),
            'repairs.parts', 'repairs.creator', 'repairs.ticket',
            'mutations.creator',
        ]);

        $users = $this->users();

        $openAudit = \App\Models\AssetAudit::current();

        return view('assets.show', compact('asset', 'users', 'openAudit'));
    }

    public function edit(Asset $asset)
    {
        return view('assets.form', ['asset' => $asset, 'users' => $this->users()]);
    }

    public function update(Request $request, Asset $asset)
    {
        $data = $this->validated($request, $asset);
        unset($data['photo'], $data['remove_photo']);

        $location = [
            'cabang'    => $data['cabang'] ?? null,
            'location'  => $data['location'] ?? null,
            'user_id'   => $data['user_id'] ?? null,
            'user_name' => $data['user_name'] ?? null,
        ];
        unset($data['cabang'], $data['location'], $data['user_id'], $data['user_name']);

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
        $asset = Asset::where('code', strtoupper($code))->firstOrFail();

        if (Auth::check() && Auth::user()->role === 'it') {
            return redirect()->route('assets.show', $asset);
        }

        return view('assets.public', compact('asset'));
    }

    // Tombol "Buat Tiket" di halaman depan: wajib login, lalu form tiket terbuka dengan aset terisi
    public function scanTicket(string $code)
    {
        $asset = Asset::where('code', strtoupper($code))->firstOrFail();

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
            ['FA-00123', 'IT', 'Laptop Finance 01', 'Laptop', 'Lenovo', 'ThinkPad E14', 'PF3ABC12', 'Head Office', 'Lantai 2', 'Budi', 'aktif', '2024-03-15', '2027-03-15', ''],
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

        if (!empty($data['status_after'])) {
            $asset->update(['status' => $data['status_after']]);
        }

        return redirect()->route('assets.show', $asset)->with('success', 'Perbaikan berhasil dicatat.');
    }

    public function mutate(Request $request, Asset $asset)
    {
        $data = $request->validate([
            'mutated_at' => ['required', 'date'],
            'cabang'     => ['nullable', 'string', 'max:50'],
            'location'   => ['nullable', 'string', 'max:150'],
            'user_id'    => ['nullable', 'integer', 'exists:users,id'],
            'user_name'  => ['nullable', 'string', 'max:150'],
            'reason'     => ['required', 'string', 'max:500'],
        ]);

        $changed = $asset->mutateTo($data, $data['reason'], $data['mutated_at'], Auth::id());

        return redirect()->route('assets.show', $asset)->with(
            $changed ? 'success' : 'error',
            $changed ? 'Mutasi aset berhasil dicatat.' : 'Tidak ada perubahan lokasi atau pengguna.'
        );
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
            'photo'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'remove_photo'   => ['nullable', 'boolean'],
        ], [
            'photo.image' => 'Foto harus berupa gambar.',
            'photo.mimes' => 'Foto harus berformat JPG, PNG, atau WEBP.',
            'photo.max'   => 'Ukuran foto maksimal 3 MB.',
        ]);
    }
}

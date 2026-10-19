<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetAudit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetAuditController extends Controller
{
    public function index()
    {
        $audits = AssetAudit::withCount('items')->with('creator')->latest('id')->get();

        return view('assets.audits.index', compact('audits'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'   => ['required', 'string', 'max:255'],
            'cabang' => ['nullable', 'string', 'max:50'],
        ]);

        if (AssetAudit::current()) {
            return back()->with('error', 'Masih ada opname yang berjalan. Tutup dulu sebelum membuat yang baru.');
        }

        $audit = AssetAudit::create($data + ['created_by' => Auth::id()]);

        return redirect()->route('assets.audits.show', $audit)->with('success', 'Opname dimulai.');
    }

    public function show(AssetAudit $audit)
    {
        $total = $audit->assetsInScope()->count();
        $scannedIds = $audit->items()->pluck('asset_id');

        $pending = $audit->assetsInScope()->whereNotIn('id', $scannedIds)->with('user')->orderBy('cabang')->orderBy('code')->get();
        $items = $audit->items()->with(['asset', 'scanner'])->latest('scanned_at')->limit(50)->get();
        // aset di luar cakupan (mis. cabang berbeda) yang ikut terpindai
        $scannedCount = $audit->assetsInScope()->whereIn('id', $scannedIds)->count();

        return view('assets.audits.show', compact('audit', 'total', 'scannedCount', 'pending', 'items'));
    }

    public function scan(Request $request, AssetAudit $audit)
    {
        $request->validate(['code' => ['required', 'string', 'max:100']]);

        // QR berisi URL .../assets/scan/AST-0001; pemindai bisa mengirim URL penuh atau hanya kodenya
        $code = strtoupper(trim(basename(parse_url(trim($request->code), PHP_URL_PATH) ?: $request->code)));
        $asset = Asset::where('code', $code)->first();

        if (!$asset) {
            return back()->with('error', "Kode {$code} tidak ditemukan.");
        }

        return $this->mark($audit, $asset, $request->input('note'));
    }

    public function verify(Request $request, AssetAudit $audit, Asset $asset)
    {
        return $this->mark($audit, $asset, $request->input('note'));
    }

    public function close(AssetAudit $audit)
    {
        if ($audit->isOpen()) {
            $audit->update(['closed_at' => now()]);
        }

        return redirect()->route('assets.audits.show', $audit)->with('success', 'Opname ditutup.');
    }

    private function mark(AssetAudit $audit, Asset $asset, ?string $note)
    {
        if (!$audit->isOpen()) {
            return back()->with('error', 'Opname ini sudah ditutup.');
        }

        $item = $audit->items()->firstOrCreate(
            ['asset_id' => $asset->id],
            ['scanned_by' => Auth::id(), 'scanned_at' => now(), 'note' => $note ? mb_substr($note, 0, 255) : null]
        );

        $msg = $item->wasRecentlyCreated
            ? "{$asset->code} {$asset->name} terverifikasi."
            : "{$asset->code} sudah terverifikasi sebelumnya.";

        return back()->with($item->wasRecentlyCreated ? 'success' : 'error', $msg);
    }
}

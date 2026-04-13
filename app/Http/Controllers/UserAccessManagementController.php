<?php

namespace App\Http\Controllers;

use App\Models\UserAccessManagement;
use App\Models\UserAccessMenu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserAccessManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status', '');
        $system = $request->get('kategori_system', '');

        $query = UserAccessManagement::with('menus')->latest();

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('nama_user', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhere('divisi', 'like', "%{$search}%")
                    ->orWhere('cabang', 'like', "%{$search}%")
                    ->orWhere('penanggung_jawab', 'like', "%{$search}%");
            });
        }

        if ($status !== '') {
            $query->where('status', $status);
        }

        if ($system !== '') {
            $query->where('kategori_system', $system);
        }

        $rows = $query->paginate(10)->withQueryString();

        return view('user-access-management.index', compact('rows', 'search', 'status', 'system'));
    }

    public function create()
    {
        return view('user-access-management.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_user' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'role' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
            'cabang' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive,resign',
            'tgl_resign' => 'nullable|date',
            'is_critical' => 'nullable|boolean',
            'kategori_system' => 'required|in:SYOP,SERVER,ACCURATE,JPAYROLL,HELPDESK,CRS',
            'approval_ceo' => 'nullable|boolean',
            'workflow_status' => 'required|in:draft,pending_approval,approved,rejected',
            'keterangan' => 'nullable|string',
            'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',

            'menus' => 'required|array|min:1',
            'menus.*.menu_name' => 'required|string|max:255',
            'menus.*.module' => 'nullable|string|max:255',
            'menus.*.can_create' => 'nullable|boolean',
            'menus.*.can_view' => 'nullable|boolean',
            'menus.*.can_update' => 'nullable|boolean',
            'menus.*.can_delete' => 'nullable|boolean',
            'menus.*.can_approve' => 'nullable|boolean',
        ]);

        DB::beginTransaction();

        try {
            $lampiranPath = null;

            if ($request->hasFile('lampiran')) {
                $lampiranPath = $request->file('lampiran')->store('user-access-management', 'public');
            }

            $approvalCeo = (bool) $request->boolean('approval_ceo');
            $isCritical = (bool) $request->boolean('is_critical');

            $header = UserAccessManagement::create([
                'nama_user' => $validated['nama_user'],
                'email' => $validated['email'] ?? null,
                'role' => $validated['role'] ?? null,
                'divisi' => $validated['divisi'] ?? null,
                'cabang' => $validated['cabang'] ?? null,
                'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
                'status' => $validated['status'],
                'tgl_resign' => $validated['tgl_resign'] ?? null,
                'is_critical' => $isCritical,
                'kategori_system' => $validated['kategori_system'],
                'approval_ceo' => $approvalCeo,
                'approval_at' => $approvalCeo ? now() : null,
                'workflow_status' => $validated['workflow_status'],
                'created_by' => auth()->user()->name ?? 'system',
                'updated_by' => auth()->user()->name ?? 'system',
                'keterangan' => $validated['keterangan'] ?? null,
                'lampiran' => $lampiranPath,
            ]);

            foreach ($validated['menus'] as $menu) {
                $header->menus()->create([
                    'menu_name' => $menu['menu_name'],
                    'module' => $menu['module'] ?? null,
                    'can_create' => !empty($menu['can_create']),
                    'can_view' => !empty($menu['can_view']),
                    'can_update' => !empty($menu['can_update']),
                    'can_delete' => !empty($menu['can_delete']),
                    'can_approve' => !empty($menu['can_approve']),
                ]);
            }

            DB::commit();

            return redirect()
                ->route('user-access-management.index')
                ->with('success', 'Data user access berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $row = UserAccessManagement::with('menus')->findOrFail($id);

        return view('user-access-management.show', compact('row'));
    }

    public function destroy($id)
    {
        $row = UserAccessManagement::findOrFail($id);

        if ($row->lampiran) {
            Storage::disk('public')->delete($row->lampiran);
        }

        $row->delete();

        return redirect()
            ->route('user-access-management.index')
            ->with('success', 'Data user access berhasil dihapus.');
    }

    public function edit($id)
{
    $row = UserAccessManagement::with('menus')->findOrFail($id);

    return view('user-access-management.edit', compact('row'));
}

public function update(Request $request, $id)
{
    $row = UserAccessManagement::with('menus')->findOrFail($id);

    $validated = $request->validate([
        'nama_user' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'role' => 'nullable|string|max:255',
        'divisi' => 'nullable|string|max:255',
        'cabang' => 'nullable|string|max:255',
        'penanggung_jawab' => 'nullable|string|max:255',
        'status' => 'required|in:active,inactive,resign',
        'tgl_resign' => 'nullable|date',
        'is_critical' => 'nullable|boolean',
        'kategori_system' => 'required|in:SYOP,SERVER,ACCURATE,JPAYROLL,HELPDESK,CRS',
        'approval_ceo' => 'nullable|boolean',
        'approval_at' => 'nullable|date',
        'workflow_status' => 'required|in:draft,pending_approval,approved,rejected',
        'disabled_by' => 'nullable|string|max:255',
        'disabled_at' => 'nullable|date',
        'keterangan' => 'nullable|string',
        'lampiran' => 'nullable|file|mimes:pdf,jpg,jpeg,png,xlsx,xls,doc,docx|max:5120',

        'menus' => 'required|array|min:1',
        'menus.*.menu_name' => 'required|string|max:255',
        'menus.*.module' => 'nullable|string|max:255',
        'menus.*.can_create' => 'nullable|boolean',
        'menus.*.can_view' => 'nullable|boolean',
        'menus.*.can_update' => 'nullable|boolean',
        'menus.*.can_delete' => 'nullable|boolean',
        'menus.*.can_approve' => 'nullable|boolean',
    ]);

    DB::beginTransaction();

    try {
        $lampiranPath = $row->lampiran;

        if ($request->hasFile('lampiran')) {
            if ($row->lampiran) {
                Storage::disk('public')->delete($row->lampiran);
            }

            $lampiranPath = $request->file('lampiran')->store('user-access-management', 'public');
        }

        $approvalCeo = (bool) $request->boolean('approval_ceo');
        $isCritical = (bool) $request->boolean('is_critical');

        $row->update([
            'nama_user' => $validated['nama_user'],
            'email' => $validated['email'] ?? null,
            'role' => $validated['role'] ?? null,
            'divisi' => $validated['divisi'] ?? null,
            'cabang' => $validated['cabang'] ?? null,
            'penanggung_jawab' => $validated['penanggung_jawab'] ?? null,
            'status' => $validated['status'],
            'tgl_resign' => $validated['tgl_resign'] ?? null,
            'is_critical' => $isCritical,
            'kategori_system' => $validated['kategori_system'],
            'approval_ceo' => $approvalCeo,
            'approval_at' => $validated['approval_at'] ?? ($approvalCeo ? now() : null),
            'workflow_status' => $validated['workflow_status'],
            'disabled_by' => $validated['disabled_by'] ?? null,
            'disabled_at' => $validated['disabled_at'] ?? null,
            'updated_by' => auth()->user()->name ?? 'system',
            'keterangan' => $validated['keterangan'] ?? null,
            'lampiran' => $lampiranPath,
        ]);

        // hapus detail lama lalu insert ulang
        $row->menus()->delete();

        foreach ($validated['menus'] as $menu) {
            $row->menus()->create([
                'menu_name' => $menu['menu_name'],
                'module' => $menu['module'] ?? null,
                'can_create' => isset($menu['can_create']) && (int) $menu['can_create'] === 1,
                'can_view' => isset($menu['can_view']) && (int) $menu['can_view'] === 1,
                'can_update' => isset($menu['can_update']) && (int) $menu['can_update'] === 1,
                'can_delete' => isset($menu['can_delete']) && (int) $menu['can_delete'] === 1,
                'can_approve' => isset($menu['can_approve']) && (int) $menu['can_approve'] === 1,
            ]);
        }

        DB::commit();

        return redirect()
            ->route('user-access-management.index')
            ->with('success', 'Data user access berhasil diperbarui.');
    } catch (\Throwable $e) {
        DB::rollBack();

        \Log::error('UPDATE USER ACCESS ERROR', [
            'message' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);

        return back()
            ->withInput()
            ->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
    }
}
}
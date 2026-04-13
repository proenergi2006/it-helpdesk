<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            🔎 Detail User Access
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">Detail User Access</h1>
                    <p class="text-sm text-slate-500">Informasi header dan menu akses user</p>
                </div>

                <a href="{{ route('user-access-management.index') }}"
                   class="bg-slate-700 hover:bg-slate-800 text-white px-5 py-2 rounded-xl font-semibold shadow">
                    Kembali
                </a>
            </div>

            <div class="bg-white p-6 rounded-2xl shadow mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 text-sm">
                    <div>
                        <span class="text-slate-500">Nama User</span>
                        <div class="font-semibold mt-1">{{ $row->nama_user }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Email</span>
                        <div class="font-semibold mt-1">{{ $row->email ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Role</span>
                        <div class="font-semibold mt-1">{{ $row->role ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Divisi</span>
                        <div class="font-semibold mt-1">{{ $row->divisi ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Cabang</span>
                        <div class="font-semibold mt-1">{{ $row->cabang ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Penanggung Jawab</span>
                        <div class="font-semibold mt-1">{{ $row->penanggung_jawab ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Status</span>
                        <div class="mt-1">
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $row->status === 'active'
                                    ? 'bg-emerald-100 text-emerald-700'
                                    : ($row->status === 'inactive'
                                        ? 'bg-amber-100 text-amber-700'
                                        : 'bg-rose-100 text-rose-700') }}">
                                {{ strtoupper($row->status) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-500">Tanggal Resign</span>
                        <div class="font-semibold mt-1">{{ $row->tgl_resign?->format('d/m/Y') ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Critical</span>
                        <div class="font-semibold mt-1">{{ $row->is_critical ? 'Y' : 'N' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Kategori System</span>
                        <div class="font-semibold mt-1">{{ $row->kategori_system }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Approval CEO</span>
                        <div class="font-semibold mt-1">{{ $row->approval_ceo ? 'Y' : 'N' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Approval At</span>
                        <div class="font-semibold mt-1">{{ $row->approval_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Workflow Status</span>
                        <div class="font-semibold mt-1">{{ $row->workflow_status }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Created By</span>
                        <div class="font-semibold mt-1">{{ $row->created_by ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Updated By</span>
                        <div class="font-semibold mt-1">{{ $row->updated_by ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Disabled By</span>
                        <div class="font-semibold mt-1">{{ $row->disabled_by ?? '-' }}</div>
                    </div>

                    <div>
                        <span class="text-slate-500">Disabled At</span>
                        <div class="font-semibold mt-1">{{ $row->disabled_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    </div>

                    <div class="md:col-span-3">
                        <span class="text-slate-500">Keterangan</span>
                        <div class="font-semibold mt-1">{{ $row->keterangan ?? '-' }}</div>
                    </div>

                    @if ($row->lampiran)
                        <div class="md:col-span-3">
                            <span class="text-slate-500">Lampiran</span>
                            <div class="mt-1">
                                <a href="{{ asset('storage/' . $row->lampiran) }}" target="_blank"
                                   class="text-blue-600 hover:underline font-semibold">
                                    Lihat Lampiran
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-lg font-bold text-slate-800">Detail Menu Akses</h2>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-4 text-left font-bold">Menu</th>
                                <th class="px-4 py-4 text-left font-bold">Module</th>
                                <th class="px-4 py-4 text-center font-bold">Create</th>
                                <th class="px-4 py-4 text-center font-bold">View</th>
                                <th class="px-4 py-4 text-center font-bold">Update</th>
                                <th class="px-4 py-4 text-center font-bold">Delete</th>
                                <th class="px-4 py-4 text-center font-bold">Approve</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($row->menus as $menu)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 font-semibold">{{ $menu->menu_name }}</td>
                                    <td class="px-4 py-4">{{ $menu->module ?? '-' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $menu->can_create ? 'Y' : 'N' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $menu->can_view ? 'Y' : 'N' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $menu->can_update ? 'Y' : 'N' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $menu->can_delete ? 'Y' : 'N' }}</td>
                                    <td class="px-4 py-4 text-center">{{ $menu->can_approve ? 'Y' : 'N' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                        Belum ada detail menu akses.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
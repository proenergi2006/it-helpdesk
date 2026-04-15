<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            👤 User Access Management
        </h2>
    </x-slot>

    @php
        $systemTabs = [
            '' => 'Semua',
            'SYOP' => 'SYOP',
            'SERVER' => 'SERVER',
            'ACCURATE' => 'ACCURATE',
            'JPAYROLL' => 'JPAYROLL',
            'HELPDESK' => 'HELPDESK',
            'CRS' => 'CRS',
        ];
    @endphp

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-100 text-emerald-700 px-4 py-3">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-100 text-rose-700 px-4 py-3">
                    {{ session('error') }}
                </div>
            @endif

            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-800">User Access</h1>
                    <p class="text-sm text-slate-500">Monitoring akses user internal per sistem</p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('user-access-management.export.excel', request()->query()) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white px-5 py-3 font-semibold shadow transition">
                        Export Excel
                    </a>

                    <a href="{{ route('user-access-management.export.pdf', request()->query()) }}"
                       class="inline-flex items-center justify-center rounded-xl bg-rose-500 hover:bg-rose-600 text-white px-5 py-3 font-semibold shadow transition">
                        Export PDF
                    </a>

                    <a href="{{ route('user-access-management.create') }}"
                       class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 font-semibold shadow transition">
                        + Tambah User Access
                    </a>
                </div>
            </div>

            {{-- FILTER --}}
            <div class="bg-white p-5 rounded-2xl shadow mb-6">
                <form method="GET" action="{{ route('user-access-management.index') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-3">
                    <div class="md:col-span-5">
                        <input type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama, email, divisi, cabang..."
                            class="w-full rounded-xl border-slate-300 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                    </div>

                    <div class="md:col-span-3">
                        <select name="status"
                            class="w-full rounded-xl border-slate-300 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua Status</option>
                            <option value="active" {{ $status == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ $status == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="resign" {{ $status == 'resign' ? 'selected' : '' }}>Resign</option>
                        </select>
                    </div>

                    <div class="md:col-span-3">
                        <select name="kategori_system"
                            class="w-full rounded-xl border-slate-300 focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua Sistem</option>
                            @foreach (['SYOP','SERVER','ACCURATE','JPAYROLL','HELPDESK','CRS'] as $item)
                                <option value="{{ $item }}" {{ $system == $item ? 'selected' : '' }}>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-1">
                        <button type="submit"
                            class="w-full rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-4 py-3 font-semibold">
                            Cari
                        </button>
                    </div>
                </form>
            </div>

            {{-- NAV TAB SYSTEM --}}
            <div class="bg-white rounded-2xl shadow p-4 mb-6">
                <div class="flex flex-wrap gap-2">
                    @foreach ($systemTabs as $key => $label)
                        @php
                            $count = $key === '' ? $allCount : ($systemCounts[$key] ?? 0);
                        @endphp
                        <a href="{{ route('user-access-management.index', array_merge(request()->except('page'), ['kategori_system' => $key])) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold transition
                           {{ $system === $key
                                ? 'bg-orange-500 text-white shadow'
                                : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            <span>{{ $label }}</span>
                            <span class="inline-flex items-center justify-center min-w-[24px] h-6 px-2 rounded-full text-xs
                                {{ $system === $key ? 'bg-white/20 text-white' : 'bg-white text-slate-700' }}">
                                {{ $count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-4 text-left font-bold text-slate-700 w-16">No</th>
                                <th class="px-4 py-4 text-left font-bold text-slate-700">User</th>
                                <th class="px-4 py-4 text-left font-bold text-slate-700">Role</th>
                                <th class="px-4 py-4 text-left font-bold text-slate-700">Divisi</th>
                                <th class="px-4 py-4 text-left font-bold text-slate-700">Sistem</th>
                                <th class="px-4 py-4 text-center font-bold text-slate-700">Status</th>
                                <th class="px-4 py-4 text-center font-bold text-slate-700">Critical</th>
                                <th class="px-4 py-4 text-center font-bold text-slate-700">CEO</th>
                                <th class="px-4 py-4 text-center font-bold text-slate-700">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($rows as $row)
                                <tr class="border-b hover:bg-slate-50 transition
                                    {{ $row->status === 'resign' ? 'bg-rose-50/60' : '' }}
                                    {{ $row->status === 'inactive' ? 'bg-amber-50/50' : '' }}">
                                    <td class="px-4 py-4 text-slate-700 font-semibold">
                                        {{ $rows->firstItem() + $loop->index }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800 uppercase">{{ $row->nama_user }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->email }}</div>
                                    </td>

                                    <td class="px-4 py-4 text-slate-700">{{ $row->role ?? '-' }}</td>

                                    <td class="px-4 py-4 text-slate-700">{{ $row->divisi ?? '-' }}</td>

                                    <td class="px-4 py-4">
                                        <span class="inline-flex items-center rounded-full bg-blue-100 text-blue-700 px-3 py-1 text-xs font-semibold">
                                            {{ $row->kategori_system }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex min-w-[96px] justify-center rounded-full px-3 py-1 text-xs font-semibold
                                            {{ $row->status === 'active'
                                                ? 'bg-emerald-500 text-white'
                                                : ($row->status === 'inactive'
                                                    ? 'bg-amber-500 text-white'
                                                    : 'bg-rose-500 text-white') }}">
                                            {{ strtoupper($row->status) }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-center text-lg">
                                        {{ $row->is_critical ? '🔥' : '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-center text-lg">
                                        {{ $row->approval_ceo ? '✅' : '-' }}
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-3">
                                            <a href="{{ route('user-access-management.show', $row->id) }}"
                                               title="Detail"
                                               class="group inline-flex items-center justify-center w-11 h-11 rounded-xl bg-blue-50 hover:bg-blue-100 border border-blue-100 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-5 h-5 text-blue-600 group-hover:text-blue-700"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M12 15a3 3 0 100-6 3 3 0 000 6z" />
                                                </svg>
                                            </a>

                                            <a href="{{ route('user-access-management.edit', $row->id) }}"
                                               title="Edit"
                                               class="group inline-flex items-center justify-center w-11 h-11 rounded-xl bg-amber-50 hover:bg-amber-100 border border-amber-100 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                     class="w-5 h-5 text-amber-600 group-hover:text-amber-700"
                                                     viewBox="0 0 24 24"
                                                     fill="none"
                                                     stroke="currentColor"
                                                     stroke-width="1.8">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M16.862 3.487a2.25 2.25 0 113.182 3.182L7.5 19.213 3 21l1.787-4.5L16.862 3.487z" />
                                                </svg>
                                            </a>

                                            <form method="POST"
                                                  action="{{ route('user-access-management.destroy', $row->id) }}"
                                                  onsubmit="return confirm('Hapus data?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        title="Hapus"
                                                        class="group inline-flex items-center justify-center w-11 h-11 rounded-xl bg-rose-50 hover:bg-rose-100 border border-rose-100 transition">
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         class="w-5 h-5 text-rose-600 group-hover:text-rose-700"
                                                         viewBox="0 0 24 24"
                                                         fill="none"
                                                         stroke="currentColor"
                                                         stroke-width="1.8">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M6 7.5h12M9.75 7.5V6a2.25 2.25 0 012.25-2.25h0A2.25 2.25 0 0114.25 6v1.5M18 7.5l-.867 12.142A2.25 2.25 0 0114.889 21H9.111a2.25 2.25 0 01-2.244-2.358L6 7.5" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center py-8 text-slate-400">
                                        Tidak ada data
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($rows->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $rows->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
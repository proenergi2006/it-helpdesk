<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Aset IT</h2>
            <div class="flex gap-2">
                <a href="{{ route('assets.audits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Opname</a>
                <a href="{{ route('assets.reconcile') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Cocokkan Accurate</a>
                <a href="{{ route('assets.import') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Import</a>
                <a href="{{ route('assets.create') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Tambah Aset</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif

            <form method="GET" class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-5 gap-3">
                <input name="q" value="{{ request('q') }}" placeholder="Cari kode, no. Accurate, nama, serial, pengguna..."
                       class="rounded-lg border-gray-300 md:col-span-2">
                <select name="type" class="rounded-lg border-gray-300">
                    <option value="">Semua jenis</option>
                    @foreach (\App\Models\Asset::TYPES as $t)
                        <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                    @endforeach
                </select>
                <select name="cabang" class="rounded-lg border-gray-300">
                    <option value="">Semua cabang</option>
                    @foreach (\App\Models\Asset::CABANG as $c)
                        <option value="{{ $c }}" @selected(request('cabang') === $c)>{{ $c }}</option>
                    @endforeach
                </select>
                <select name="status" class="rounded-lg border-gray-300">
                    <option value="">Semua status</option>
                    @foreach (\App\Models\Asset::STATUSES as $k => $v)
                        <option value="{{ $k }}" @selected(request('status') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <div class="md:col-span-5 flex gap-2">
                    <button class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-gray-800">Terapkan</button>
                    <a href="{{ route('assets.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 hover:bg-gray-200">Reset</a>
                </div>
            </form>

            <form method="GET" action="{{ route('assets.labels') }}" target="_blank">
                <div class="flex items-center justify-between mb-2">
                    <div class="text-sm text-gray-500">{{ $assets->total() }} aset</div>
                    <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 text-sm">Cetak Label QR (yang dipilih)</button>
                </div>

                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('.pick').forEach(c => c.checked = this.checked)"></th>
                                <th class="text-left px-4 py-3">Kode</th>
                                <th class="text-left px-4 py-3">Nama</th>
                                <th class="text-left px-4 py-3">Jenis</th>
                                <th class="text-left px-4 py-3">Cabang</th>
                                <th class="text-left px-4 py-3">Pengguna</th>
                                <th class="text-left px-4 py-3">Status</th>
                                <th class="text-left px-4 py-3">Garansi</th>
                                <th class="text-left px-4 py-3">Tiket</th>
                                <th class="text-left px-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($assets as $a)
                                <tr>
                                    <td class="px-4 py-3"><input type="checkbox" class="pick" name="ids[]" value="{{ $a->id }}"></td>
                                    <td class="px-4 py-3 font-semibold text-blue-700">{{ $a->code }}</td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($a->photo_url)
                                                <img src="{{ $a->photo_url }}" alt="" class="h-10 w-10 rounded object-cover border">
                                            @else
                                                <div class="h-10 w-10 rounded bg-gray-100 border"></div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $a->name }}</div>
                                                <div class="text-xs text-gray-500">{{ trim($a->brand.' '.$a->model) }}@if ($a->serial_number) · SN {{ $a->serial_number }}@endif</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">{{ $a->type }}</td>
                                    <td class="px-4 py-3">{{ $a->cabang ?? '-' }}</td>
                                    <td class="px-4 py-3">{{ $a->holder_name ?? '-' }}</td>
                                    <td class="px-4 py-3">
                                        <span class="inline-flex px-2 py-1 rounded text-xs {{ match ($a->status) {
                                            'aktif' => 'bg-green-50 text-green-700',
                                            'dipinjam' => 'bg-blue-50 text-blue-700',
                                            'rusak' => 'bg-red-50 text-red-700',
                                            'diperbaiki' => 'bg-amber-50 text-amber-700',
                                            default => 'bg-gray-100 text-gray-600',
                                        } }}">{{ $a->status_label }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($a->warranty_until)
                                            <span class="{{ $a->warranty_state === 'habis' ? 'text-red-600' : ($a->warranty_state === 'segera' ? 'text-amber-600' : 'text-gray-700') }}">
                                                {{ $a->warranty_until->format('d M Y') }}
                                            </span>
                                        @else - @endif
                                    </td>
                                    <td class="px-4 py-3">{{ $a->tickets_count }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('assets.show', $a) }}" class="text-blue-600 hover:underline">Detail</a>
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('assets.edit', $a) }}" class="text-gray-600 hover:underline">Edit</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="10" class="px-4 py-10 text-center text-gray-500">Belum ada aset. Tambahkan satu per satu atau gunakan Import.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div>{{ $assets->links() }}</div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Aset IT</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('assets.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Dashboard</a>
                <a href="{{ route('assets.audits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Opname</a>
                <a href="{{ route('assets.reconcile') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Cocokkan Accurate</a>
                <a href="{{ route('assets.import') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Import</a>
                <a href="{{ route('assets.export', request()->query()) }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm" title="Unduh semua aset sesuai filter ke Excel">Export Excel</a>
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

            <form method="GET" class="bg-white p-4 rounded-lg shadow grid grid-cols-1 md:grid-cols-4 xl:grid-cols-8 gap-3">
                @if (request('review')) <input type="hidden" name="review" value="{{ request('review') }}"> @endif
                <input name="q" value="{{ request('q') }}" placeholder="Cari kode, no. Accurate, nama, serial, pengguna..."
                       class="rounded-lg border-gray-300 md:col-span-2">
                <select name="type" class="rounded-lg border-gray-300">
                    <option value="">Semua jenis</option>
                    @foreach (\App\Models\Asset::typeOptions() as $group => $list)
                        <optgroup label="{{ $group }}">
                            @foreach ($list as $t)
                                <option value="{{ $t }}" @selected(request('type') === $t)>{{ $t }}</option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                <select name="sort" class="rounded-lg border-gray-300">
                    <option value="">Terbaru ditambahkan</option>
                    <option value="purchase_asc" @selected(request('sort') === 'purchase_asc')>Beli terlama dulu</option>
                    <option value="purchase_desc" @selected(request('sort') === 'purchase_desc')>Beli terbaru dulu</option>
                </select>
                <select name="department" class="rounded-lg border-gray-300 @if (\App\Models\Asset::restrictedDepartment()) hidden @endif">
                    <option value="">Semua dept. pemilik</option>
                    @foreach (\App\Models\Asset::DEPARTMENTS as $k => $v)
                        <option value="{{ $k }}" @selected(request('department') === $k)>{{ $v }}</option>
                    @endforeach
                </select>
                <select name="user_department" class="rounded-lg border-gray-300">
                    <option value="">Semua dept. pengguna</option>
                    @foreach ($userDepartments as $ud)
                        <option value="{{ $ud }}" @selected(request('user_department') === $ud)>{{ $ud }}</option>
                    @endforeach
                </select>
                <select name="asset_class" class="rounded-lg border-gray-300">
                    <option value="">Asset / Inventory</option>
                    @foreach (\App\Models\Asset::ASSET_CLASSES as $k => $v)
                        <option value="{{ $k }}" @selected(request('asset_class') === $k)>{{ $v }} ({{ \App\Models\Asset::CLASS_BUDGET[$k] }})</option>
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
                <div class="md:col-span-4 xl:col-span-8 flex gap-2">
                    <button class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-gray-800">Terapkan</button>
                    <a href="{{ route('assets.index') }}" class="rounded-lg bg-gray-100 px-4 py-2 hover:bg-gray-200">Reset</a>
                </div>
            </form>

            <form method="POST" action="{{ route('assets.labels') }}" id="bulk"
                  x-data="{ n: 0, panel: null, sync() { this.n = document.querySelectorAll('.pick:checked').length } }" @change="sync()">
                @csrf
                <div class="mb-2">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <div class="text-sm text-gray-500">{{ $assets->total() }} aset <span x-show="n > 0" x-cloak>· <strong class="text-gray-800" x-text="n + ' dipilih'"></strong></span></div>
                        <div class="flex flex-wrap items-center gap-2" :class="n === 0 ? 'opacity-50' : ''">
                            <button type="submit" formaction="{{ route('assets.labels') }}" formtarget="_blank" :disabled="n === 0" class="px-3 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 text-sm disabled:cursor-not-allowed">Cetak Label QR</button>
                            <button type="submit" formaction="{{ route('assets.export') }}" :disabled="n === 0" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm disabled:cursor-not-allowed">Export Terpilih</button>
                            <button type="button" @click="panel = panel === 'status' ? null : 'status'" :disabled="n === 0" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm disabled:cursor-not-allowed">Ubah Status</button>
                             <button type="button" @click="panel = panel === 'class' ? null : 'class'" :disabled="n === 0" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm disabled:cursor-not-allowed">Ubah Asset/Inventory</button>
                            <button type="button" @click="panel = panel === 'mutate' ? null : 'mutate'" :disabled="n === 0" class="px-3 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm disabled:cursor-not-allowed">Pindahkan (Mutasi)</button>
                            <button type="submit" formaction="{{ route('assets.bulk.delete') }}" :disabled="n === 0"
                                    onclick="return confirm('Hapus aset yang dipilih? Aset yang sudah punya tiket, perbaikan, atau opname tidak akan dihapus. Tindakan ini tidak bisa dibatalkan.')"
                                    class="px-3 py-2 rounded-lg bg-red-50 text-red-700 ring-1 ring-red-200 hover:bg-red-100 text-sm disabled:cursor-not-allowed">Hapus</button>
                        </div>
                    </div>

                    <div x-show="panel === 'status'" x-cloak class="mt-3 bg-white rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Status baru</label>
                            <select name="status" class="rounded-lg border-gray-300 text-sm">
                                @foreach (\App\Models\Asset::STATUSES as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                        <button type="submit" formaction="{{ route('assets.bulk.status') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Terapkan ke yang dipilih</button>
                    </div>

                    <div x-show="panel === 'class'" x-cloak class="mt-3 bg-white rounded-lg shadow p-4 flex flex-wrap items-end gap-3">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Klasifikasi baru</label>
                            <select name="asset_class" class="rounded-lg border-gray-300 text-sm">
                                @foreach (\App\Models\Asset::ASSET_CLASSES as $k => $v)<option value="{{ $k }}">{{ $v }} ({{ \App\Models\Asset::CLASS_BUDGET[$k] }})</option>@endforeach
                            </select>
                        </div>
                        <button type="submit" formaction="{{ route('assets.bulk.class') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Terapkan ke yang dipilih</button>
                        <p class="text-xs text-gray-500 w-full">Gunakan filter "Asset di bawah Rp 2 juta" di dashboard untuk menemukan yang keliru, lalu ubah sekaligus.</p>
                    </div>

                    <div x-show="panel === 'mutate'" x-cloak class="mt-3 bg-white rounded-lg shadow p-4 grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Cabang tujuan</label>
                            <select name="cabang" class="w-full rounded-lg border-gray-300 text-sm">
                                <option value="">(tidak diubah)</option>
                                @foreach (\App\Models\Asset::CABANG as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Lokasi / ruangan</label>
                            <input name="location" class="w-full rounded-lg border-gray-300 text-sm" placeholder="(tidak diubah)">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Departemen pengguna</label>
                            <input name="user_department" class="w-full rounded-lg border-gray-300 text-sm" placeholder="(tidak diubah)">
                        </div>
                        <div>
                            <label class="block text-xs text-gray-500 mb-1">Alasan mutasi</label>
                            <input name="reason" class="w-full rounded-lg border-gray-300 text-sm" placeholder="wajib diisi">
                        </div>
                        <button type="submit" formaction="{{ route('assets.bulk.mutate') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Pindahkan</button>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3"><input type="checkbox" onclick="document.querySelectorAll('.pick').forEach(c => c.checked = this.checked); this.form.dispatchEvent(new Event('change', {bubbles: true}))"></th>
                                <th class="text-left px-4 py-3">Kode</th>
                                <th class="text-left px-4 py-3">Nama</th>
                                <th class="text-left px-4 py-3">Jenis</th>
                                <th class="text-left px-4 py-3">Asset / Inv.</th>
                                <th class="text-left px-4 py-3">Tgl. Beli</th>
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
                                    <td class="px-4 py-3">
                                        @if ($a->asset_class)
                                            <span class="inline-flex px-2 py-1 rounded text-xs {{ $a->asset_class === 'asset' ? 'bg-blue-50 text-blue-700' : 'bg-amber-50 text-amber-700' }}">{{ \App\Models\Asset::ASSET_CLASSES[$a->asset_class] }}</span>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">{{ $a->purchase_date?->format('d M Y') ?? '-' }}</td>
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
                                        <span class="text-gray-300 mx-1">|</span>
                                        <a href="{{ route('assets.copy', $a) }}" class="text-indigo-600 hover:underline" title="Buat aset baru dengan spesifikasi yang sama">Salin</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="12" class="px-4 py-10 text-center text-gray-500">Belum ada aset. Tambahkan satu per satu atau gunakan Import.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <div>{{ $assets->links() }}</div>
        </div>
    </div>
</x-app-layout>

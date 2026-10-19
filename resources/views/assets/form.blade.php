@php $editing = $asset->exists; @endphp
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ $editing ? 'Edit Aset '.$asset->code : 'Tambah Aset' }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <form method="POST" action="{{ $editing ? route('assets.update', $asset) : route('assets.store') }}"
                  enctype="multipart/form-data"
                  class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                @if ($editing) @method('PUT') @endif

                @php
                    $field = fn ($n, $default = null) => old($n, $asset->{$n} instanceof \Carbon\Carbon ? $asset->{$n}->format('Y-m-d') : ($asset->{$n} ?? $default));
                    $input = 'w-full rounded-lg border-gray-300';
                @endphp

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perangkat</label>
                    <input name="name" value="{{ $field('name') }}" required class="{{ $input }}" placeholder="contoh: Laptop Finance 01">
                    @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                    <select name="type" required class="{{ $input }}">
                        <option value="">Pilih jenis</option>
                        @foreach (\App\Models\Asset::TYPES as $t)
                            <option value="{{ $t }}" @selected($field('type') === $t)>{{ $t }}</option>
                        @endforeach
                    </select>
                    @error('type') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select name="status" required class="{{ $input }}">
                        @foreach (\App\Models\Asset::STATUSES as $k => $v)
                            <option value="{{ $k }}" @selected($field('status', 'aktif') === $k)>{{ $v }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                    <input name="brand" value="{{ $field('brand') }}" class="{{ $input }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input name="model" value="{{ $field('model') }}" class="{{ $input }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Aset Accurate <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <input name="accurate_no" value="{{ $field('accurate_no') }}" class="{{ $input }}" placeholder="kosongkan untuk barang kecil / non-kapitalisasi">
                    @error('accurate_no') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                    <input name="serial_number" value="{{ $field('serial_number') }}" class="{{ $input }}">
                    @error('serial_number') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pengguna / Penanggung Jawab</label>
                    <select name="user_id" class="{{ $input }}">
                        <option value="">- Pilih user -</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}" @selected((string) $field('user_id') === (string) $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                    @error('user_id') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Atau pengguna tanpa akun <span class="text-xs text-gray-400">(opsional)</span></label>
                    <input name="user_name" value="{{ $field('user_name') }}" class="{{ $input }}" placeholder="contoh: Ruang Meeting, Pool Driver">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang</label>
                    <select name="cabang" class="{{ $input }}">
                        <option value="">Pilih cabang</option>
                        @foreach (\App\Models\Asset::CABANG as $c)
                            <option value="{{ $c }}" @selected($field('cabang') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Lokasi</label>
                    <input name="location" value="{{ $field('location') }}" class="{{ $input }}" placeholder="contoh: Lantai 2, ruang Finance">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                    <input type="date" name="purchase_date" value="{{ $field('purchase_date') }}" class="{{ $input }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Garansi Sampai</label>
                    <input type="date" name="warranty_until" value="{{ $field('warranty_until') }}" class="{{ $input }}">
                    @error('warranty_until') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Foto Aset <span class="text-xs text-gray-400">(JPG/PNG/WEBP, maks. 3 MB)</span></label>
                    @if ($asset->photo_url)
                        <div class="flex items-center gap-4 mb-2">
                            <img src="{{ $asset->photo_url }}" alt="Foto {{ $asset->name }}" class="h-24 w-24 rounded-lg object-cover border">
                            <label class="flex items-center gap-2 text-sm text-gray-600">
                                <input type="checkbox" name="remove_photo" value="1" class="rounded"> Hapus foto
                            </label>
                        </div>
                    @endif
                    <input type="file" name="photo" accept="image/*" class="text-sm">
                    @error('photo') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" rows="3" class="{{ $input }}">{{ $field('notes') }}</textarea>
                </div>

                <div class="md:col-span-2 flex justify-end gap-2">
                    <a href="{{ $editing ? route('assets.show', $asset) : route('assets.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Batal</a>
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

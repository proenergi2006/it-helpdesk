<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Import Aset</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('error'))
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif
            @error('file')
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ $message }}</div>
            @enderror

            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <ol class="list-decimal ml-5 text-sm text-gray-700 space-y-1">
                    <li>Unduh template, lalu isi satu perangkat per baris. Kolom <strong>nama</strong> dan <strong>jenis</strong> wajib.</li>
                    <li>Kode aset dibuat otomatis. Baris dengan serial number yang sudah terdaftar dilewati.</li>
                    <li>Kolom <strong>departemen</strong> berisi singkatan ({{ implode(', ', array_keys(\App\Models\Asset::DEPARTMENTS)) }}); kosong dianggap IT. Cabang dipakai untuk membentuk kode, contoh <code>AST-IT-JKT-0012</code>.</li>
                    <li>Kolom <strong>no_aset_accurate</strong> opsional. Jika nomor sudah ada di helpdesk, data aset itu diperbarui (tidak dibuat dobel).</li>
                    <li>Kolom status: aktif, dipinjam, rusak, diperbaiki, disposal. Tanggal format <code>2024-03-15</code>.</li>
                    <li>Format file: .xlsx, .xls, atau .csv (maksimal 5 MB).</li>
                </ol>

                <a href="{{ route('assets.import.template') }}" class="inline-block px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Unduh Template</a>

                <form method="POST" action="{{ route('assets.import.store') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="text-sm">
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Import</button>
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Batal</a>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<style>
    /* Lebarkan kolom STATUS */
    table th:nth-child(6),
    table td:nth-child(6) {
        min-width: 160px;
        /* ubah jadi 180px kalau masih sempit */
        text-align: center;
        white-space: normal;
        word-wrap: break-word;
    }

    /* Biar badge status tetap rapi di tengah */
    table td:nth-child(6) span {
        display: inline-block;
        min-width: 120px;
        padding: 6px 10px;
        border-radius: 9999px;
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        color: #fff;
    }

    /* Rapikan cell */
    table td,
    table th {
        vertical-align: middle;
    }
</style>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📊 Laporan Tiket
        </h2>
    </x-slot>


    <div class="py-8 bg-gray-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-xl shadow-lg">

                {{-- 🔍 FILTER SECTION --}}
                <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end mb-6">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $start }}"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $end }}"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>


                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Nama</label>
                        <input type="text" name="nama" value="{{ request('nama') }}" placeholder="Cari nama..."
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Kategori</label>
                        <select name="category"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Semua</option>
                            <option value="software" {{ request('category') == 'software' ? 'selected' : '' }}>Software
                            </option>
                            <option value="hardware" {{ request('category') == 'hardware' ? 'selected' : '' }}>Hardware
                            </option>
                            <option value="network" {{ request('category') == 'network' ? 'selected' : '' }}>Network
                            </option>
                            <option value="multimedia" {{ request('category') == 'multimedia' ? 'selected' : '' }}>
                                Multimedia</option>
                        </select>
                    </div>

                    <div class="flex flex-wrap gap-2 md:col-span-2">
                        <button
                            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-md flex items-center gap-2 transition-all">
                            🔎 Filter
                        </button>

                        <a href="{{ route('reports.export.excel', request()->query()) }}"
                            class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded-md flex items-center gap-2 transition-all">
                            📗 Excel
                        </a>

                        <a href="{{ route('reports.export.pdf', request()->query()) }}"
                            class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-md flex items-center gap-2 transition-all">
                            📕 PDF
                        </a>
                    </div>
                </form>

                {{-- 📋 TABLE SECTION --}}
                <div class="overflow-x-auto mt-4">
                    <table class="min-w-full text-sm border-collapse">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="p-3 text-left">#</th>
                                <th class="p-3 text-left">Nama</th>
                                <th class="p-3 text-left">Judul</th>
                                <th class="p-3 text-left">Cabang</th>
                                <th class="p-3 text-left">Kategori</th>
                                <th class="p-3 text-left w-[12rem] text-center">Status</th>
                                <th class="p-3 text-left">Dikerjakan Oleh</th>
                                <th class="p-3 text-left">Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tickets as $t)
                                <tr class="border-b hover:bg-blue-50 transition">
                                    <td class="p-3 font-semibold text-blue-700">#{{ $t->id }}</td>
                                    <td class="p-3">{{ $t->nama }}</td>
                                    <td class="p-3">{{ $t->title ?: '-' }}</td>
                                    <td class="p-3">{{ $t->cabang }}</td>
                                    <td class="p-3 capitalize">{{ $t->category }}</td>
                                    <td class="p-3">
                                        <span
                                            class="px-3 py-1 rounded-full text-white text-xs font-semibold
                                            {{ $t->status === 'open' ? 'bg-yellow-500' : ($t->status === 'in_progress' ? 'bg-blue-500' : 'bg-green-600') }}">
                                            {{ ucfirst($t->status) }}
                                        </span>
                                    </td>
                                    <td class="p-3">
                                        {{ $t->takenByUser?->name ?? '-' }}
                                    </td>
                                    <td class="p-3">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-gray-500">Tidak ada data</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

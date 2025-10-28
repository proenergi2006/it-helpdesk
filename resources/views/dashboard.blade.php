<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            🎧 Dashboard Tim IT
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Statistik --}}
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12 md:col-span-4 bg-blue-100 text-blue-800 p-5 rounded-xl text-center shadow">
                    <div class="text-3xl font-bold">{{ $openCount }}</div>
                    <p class="text-sm font-medium">Tiket Open</p>
                </div>
                <div class="col-span-12 md:col-span-4 bg-yellow-100 text-yellow-800 p-5 rounded-xl text-center shadow">
                    <div class="text-3xl font-bold">{{ $inProgressCount }}</div>
                    <p class="text-sm font-medium">Sedang Dikerjakan</p>
                </div>
                <div class="col-span-12 md:col-span-4 bg-green-100 text-green-800 p-5 rounded-xl text-center shadow">
                    <div class="text-3xl font-bold">{{ $resolvedCount }}</div>
                    <p class="text-sm font-medium">Selesai</p>
                </div>
            </div>

            {{-- Filter --}}
            <div class="flex flex-wrap gap-2 mb-4 mt-4">
                <select id="filterTanggal" class="border-gray-300 rounded-md text-sm px-2 py-1">
                    <option value="">Semua Tanggal</option>
                    <option value="today">Hari Ini</option>
                </select>

                <select id="filterCabang" class="border-gray-300 rounded-md text-sm px-2 py-1">
                    <option value="">Semua Cabang</option>
                    @foreach ($cabangs as $cabang)
                        <option value="{{ $cabang }}">{{ $cabang }}</option>
                    @endforeach
                </select>

                <select id="filterKategori" class="border-gray-300 rounded-md text-sm px-2 py-1">
                    <option value="">Semua Kategori</option>
                    <option value="software">Software</option>
                    <option value="hardware">Hardware</option>
                </select>

                <select id="filterStatus" class="border-gray-300 rounded-md text-sm px-2 py-1">
                    <option value="">Semua Status</option>
                    <option value="open">Open</option>
                    <option value="in_progress">Sedang Dikerjakan</option>
                    <option value="resolved">Selesai</option>
                </select>
            </div>

            {{-- Spinner --}}
            <div id="loadingSpinner" class="hidden text-center py-4">
                <div class="flex justify-center">
                    <svg class="animate-spin h-8 w-8 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                            stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                </div>
                <p class="text-sm text-gray-600 mt-2">Memuat data...</p>
            </div>

            {{-- Tabel Tiket --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
                    📋 Daftar Tiket Terbaru
                </h3>

                <div class="overflow-x-auto">
                    <table id="ticketTable" class="min-w-full text-sm text-gray-700 border-collapse">
                        <thead class="bg-blue-100 text-blue-800">
                            <tr>
                                <th class="p-2 text-left">#</th>
                                <th class="p-2 text-left">Nama</th>
                                <th class="p-2 text-left">Judul</th>
                                <th class="p-2 text-left">Cabang</th>
                                <th class="p-2 text-left">Kategori</th>
                                <th class="p-2 text-left">Status</th>
                                <th class="p-2 text-left">Dikerjakan Oleh</th>
                                <th class="p-2 text-left">Mulai</th>
                                <th class="p-2 text-left">Selesai</th>
                                <th class="p-2 text-left">Durasi</th>
                                <th class="p-2 text-left">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="ticketBody">
                            @foreach ($tickets as $ticket)
                                @php
                                    $durasi = '-';
                                    if ($ticket->started_at && $ticket->finished_at) {
                                        $diff = $ticket->started_at->diff($ticket->finished_at);
                                        $durasi =
                                            ($diff->h ? $diff->h . ' jam ' : '') .
                                            ($diff->i ? $diff->i . ' menit ' : '') .
                                            ($diff->s ? $diff->s . ' detik' : '');
                                    }
                                @endphp
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 font-semibold text-blue-700">
                                        {{ strtoupper(substr($ticket->category, 0, 1)) }}{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-2">{{ $ticket->nama }}</td>
                                    <td class="p-2">{{ $ticket->title }}</td>
                                    <td class="p-2">{{ $ticket->cabang }}</td>
                                    <td class="p-2 capitalize">{{ $ticket->category }}</td>
                                    <td class="p-2">
                                        <span
                                            class="px-3 py-1 rounded-full text-white text-xs font-semibold
                                            {{ $ticket->status === 'open'
                                                ? 'bg-yellow-500'
                                                : ($ticket->status === 'in_progress'
                                                    ? 'bg-blue-500'
                                                    : 'bg-green-600') }}">
                                            {{ ucfirst($ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="p-2">{{ $ticket->takenByUser?->name ?? '-' }}</td>
                                    <td class="p-2">{{ $ticket->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $ticket->finished_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $durasi }}</td>
                                    <td class="p-2">
                                        @if ($ticket->status === 'open')
                                            <form action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="in_progress">
                                                <button
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs">
                                                    Ambil
                                                </button>
                                            </form>
                                        @elseif ($ticket->status === 'in_progress')
                                            <form action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="resolved">
                                                <button
                                                    class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs">
                                                    Selesai
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Done</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    {{-- SCRIPT: Native Filter (tanpa DataTables) --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('#ticketBody tr');
            const spinner = document.getElementById('loadingSpinner');
            const filters = ['filterCabang', 'filterKategori', 'filterStatus', 'filterTanggal'];

            filters.forEach(id => document.getElementById(id).addEventListener('change', filterTickets));

            function filterTickets() {
                spinner.classList.remove('hidden');

                setTimeout(() => {
                    const cabang = document.getElementById('filterCabang').value.toLowerCase();
                    const kategori = document.getElementById('filterKategori').value.toLowerCase();
                    const status = document.getElementById('filterStatus').value.toLowerCase();
                    const tanggal = document.getElementById('filterTanggal').value;

                    rows.forEach(row => {
                        const td = row.querySelectorAll('td');
                        const matchCabang = !cabang || td[3].innerText.toLowerCase().includes(
                            cabang);
                        const matchKategori = !kategori || td[4].innerText.toLowerCase().includes(
                            kategori);
                        const matchStatus = !status || td[5].innerText.toLowerCase().includes(
                            status);

                        let matchTanggal = true;
                        if (tanggal === 'today') {
                            const today = new Date().toLocaleDateString('id-ID');
                            matchTanggal = td[7].innerText.startsWith(today);
                        }

                        if (matchCabang && matchKategori && matchStatus && matchTanggal)
                            row.style.display = '';
                        else
                            row.style.display = 'none';
                    });

                    spinner.classList.add('hidden');
                }, 400);
            }
        });
    </script>
</x-app-layout>

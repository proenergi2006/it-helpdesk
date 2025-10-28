<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>System Antrian Helpdesk IT</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">

    <style>
        .blink {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: .7;
            }
        }

        body {
            background: #f5f7fb;
        }

        @media (max-width:768px) {

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                text-align: left;
                margin-bottom: .5rem;
            }
        }
    </style>
</head>

<body class="font-sans" x-data="{ showModal: false }">
    <div class="max-w-7xl mx-auto py-6 px-3 sm:px-6">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-3">
            <div class="flex items-center gap-3">
                <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi Logo"
                    class="h-10 w-auto md:h-12 object-contain">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-blue-700 flex items-center gap-2">
                        Antrian Helpdesk IT
                    </h1>
                    <p class="text-gray-600 text-sm md:text-base">Pusat Bantuan IT - Pro Energi</p>
                </div>
            </div>

            <div class="flex items-center space-x-3 w-full md:w-auto justify-between">
                <button @click="showModal = true"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-semibold shadow text-sm md:text-base">
                    + Buat Ticket
                </button>
                <div class="text-right text-xs md:text-sm text-gray-500" id="clock"></div>
            </div>
        </div>

        {{-- Panel Antrian --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            {{-- SOFTWARE --}}
            <div class="bg-blue-600 text-white rounded-2xl shadow-lg p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-semibold mb-3 uppercase tracking-wider text-center">Software</h2>
                <div id="panelSoftware" class="grid grid-cols-1 gap-3">
                    @forelse ($softwareTickets as $ticket)
                        <div class="bg-blue-500 rounded-xl p-4 text-center shadow-md">
                            <div class="text-3xl md:text-4xl font-extrabold mb-1">
                                #S{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="text-base md:text-lg font-semibold truncate">{{ $ticket->title }}</div>
                            <div class="text-sm">Cabang: <span class="font-bold">{{ $ticket->cabang }}</span></div>
                            <div class="text-sm mt-1">Status: <span
                                    class="font-bold capitalize">{{ $ticket->status }}</span></div>
                        </div>
                    @empty
                        <div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>
                    @endforelse
                </div>
            </div>

            {{-- HARDWARE --}}
            <div class="bg-green-600 text-white rounded-2xl shadow-lg p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-semibold mb-3 uppercase tracking-wider text-center">Hardware</h2>
                <div id="panelHardware" class="grid grid-cols-1 gap-3">
                    @forelse ($hardwareTickets as $ticket)
                        <div class="bg-green-500 rounded-xl p-4 text-center shadow-md">
                            <div class="text-3xl md:text-4xl font-extrabold mb-1">
                                #H{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                            </div>
                            <div class="text-base md:text-lg font-semibold truncate">{{ $ticket->title }}</div>
                            <div class="text-sm">Cabang: <span class="font-bold">{{ $ticket->cabang }}</span></div>
                            <div class="text-sm mt-1">Status: <span
                                    class="font-bold capitalize">{{ $ticket->status }}</span></div>
                        </div>
                    @empty
                        <div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Statistik Ringkas --}}
        <div class="grid grid-cols-12 gap-4 mb-6">
            <div class="col-span-12 md:col-span-4 bg-blue-100 text-blue-800 p-4 rounded-xl text-center shadow-sm">
                <div class="text-3xl font-bold">{{ $totalToday }}</div>
                <p class="text-sm font-medium">Ticket Hari Ini</p>
            </div>
            <div class="col-span-12 md:col-span-4 bg-yellow-100 text-yellow-800 p-4 rounded-xl text-center shadow-sm">
                <div class="text-3xl font-bold">{{ $openCount }}</div>
                <p class="text-sm font-medium">Menunggu</p>
            </div>
            <div class="col-span-12 md:col-span-4 bg-green-100 text-green-800 p-4 rounded-xl text-center shadow-sm">
                <div class="text-3xl font-bold">{{ $resolvedCount }}</div>
                <p class="text-sm font-medium">Selesai</p>
            </div>
        </div>

        {{-- Tabel Ticket --}}
        <div class="bg-white rounded-2xl shadow p-4 md:p-6">
            <h2 class="text-xl md:text-2xl font-semibold text-blue-700 mb-4 flex items-center gap-2">
                📋 Daftar Ticket Antrian
            </h2>

            <div class="overflow-x-auto">
                <table id="ticketTable" class="min-w-full text-xs md:text-sm text-gray-700 border-collapse">
                    <thead class="bg-blue-100 text-blue-800">
                        <tr>
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">Nama</th>
                            <th class="p-2 text-left">Judul Ticket</th>
                            <th class="p-2 text-left">Cabang</th>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Status</th>
                            <th class="p-2 text-left">Dikerjakan Oleh</th>
                            <th class="p-2 text-left">Waktu</th>
                        </tr>
                    </thead>
                    <tbody id="ticketBody">
                        @forelse ($tickets as $ticket)
                            <tr class="border-b hover:bg-blue-50 transition">
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
                                                : ($ticket->status === 'resolved'
                                                    ? 'bg-green-600'
                                                    : 'bg-gray-400')) }}">
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                <td class="p-2">
                                    {{ $ticket->takenByUser?->name ?? '-' }}
                                </td>
                                <td class="p-2">{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-6 text-gray-500">Belum ada ticket</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="mt-10 text-center text-gray-500 text-sm py-6 border-t border-gray-200">
        <div class="flex flex-col md:flex-row justify-center items-center gap-2">
            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-6 w-auto opacity-70">
            <span>© {{ date('Y') }} <strong>PT Pro Energi</strong> — IT Helpdesk System</span>
        </div>
        <p class="mt-2 text-xs text-gray-400">
            Versi {{ config('app.version', '1.0.0') }} · Dikembangkan oleh <span
                class="font-semibold text-blue-600">Tim IT Pro Energi</span>
        </p>
    </footer>

    {{-- Toast --}}
    @if (session('success'))
        <script>
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2500,
                timerProgressBar: true
            });
        </script>
    @endif

    <script>
        const apiUrl = "{{ route('tickets.api') }}";
        setInterval(() => {
            document.getElementById('clock').innerText = new Date().toLocaleString('id-ID', {
                weekday: 'long',
                day: '2-digit',
                month: 'short',
                year: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        }, 1000);

        async function refreshData() {
            const res = await fetch(apiUrl);
            const data = await res.json();
            const sPanel = document.getElementById('panelSoftware');
            const hPanel = document.getElementById('panelHardware');
            const tbody = document.getElementById('ticketBody');

            const software = data.filter(t => t.category === 'software' && t.status === 'open').slice(0, 3);
            const hardware = data.filter(t => t.category === 'hardware' && t.status === 'open').slice(0, 2);

            sPanel.innerHTML = software.map(t => `
                <div class="bg-blue-500 rounded-xl p-4 text-center shadow-md">
                    <div class="text-3xl font-extrabold mb-1">#S${String(t.id).padStart(3,'0')}</div>
                    <div class="text-base font-semibold truncate">${t.title}</div>
                    <div class="text-sm">Cabang: <span class="font-bold">${t.cabang}</span></div>
                    <div class="text-sm mt-1">Status: <span class="font-bold capitalize">${t.status}</span></div>
                </div>`).join('') || `<div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>`;

            hPanel.innerHTML = hardware.map(t => `
                <div class="bg-green-500 rounded-xl p-4 text-center shadow-md">
                    <div class="text-3xl font-extrabold mb-1">#H${String(t.id).padStart(3,'0')}</div>
                    <div class="text-base font-semibold truncate">${t.title}</div>
                    <div class="text-sm">Cabang: <span class="font-bold">${t.cabang}</span></div>
                    <div class="text-sm mt-1">Status: <span class="font-bold capitalize">${t.status}</span></div>
                </div>`).join('') || `<div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>`;

            tbody.innerHTML = data.map(t => `
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="p-2 font-semibold text-blue-700">${t.category[0].toUpperCase()}${String(t.id).padStart(3,'0')}</td>
                    <td class="p-2">${t.nama ?? '-'}</td>
                    <td class="p-2">${t.title}</td>
                    <td class="p-2">${t.cabang}</td>
                    <td class="p-2 capitalize">${t.category}</td>
                    <td class="p-2">
                        <span class="px-3 py-1 rounded-full text-white text-xs font-semibold ${
                            t.status==='open'?'bg-yellow-500':t.status==='in_progress'?'bg-blue-500':t.status==='resolved'?'bg-green-600':'bg-gray-400'
                        }">${t.status}</span>
                    </td>
                    <td class="p-2">${t.taken_by_name ?? '-'}</td>
                    <td class="p-2">${new Date(t.created_at).toLocaleString('id-ID')}</td>
                </tr>`).join('');
        }
        refreshData();
        setInterval(refreshData, 10000);

        $(document).ready(() => $('#ticketTable').DataTable({
            pageLength: 5,
            order: [
                [7, 'desc']
            ],
            language: {
                search: "🔍 Cari:",
                lengthMenu: "Tampilkan _MENU_ tiket",
                info: "Menampilkan _START_-_END_ dari _TOTAL_ tiket",
                paginate: {
                    previous: "←",
                    next: "→"
                },
                emptyTable: "Belum ada ticket"
            }
        }));
    </script>
</body>

</html>

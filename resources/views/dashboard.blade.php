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
                    <option value="Hold - Third Party">Hold - Third Party</option>
                    <option value="Hold - Waiting User Response">Hold - Waiting User</option>
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
                                <th class="p-2 text-left">Klasifikasi</th>
                                <th class="p-2 text-left">Priority</th>
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

                                    $statusColor = match ($ticket->status) {
                                        'open' => 'bg-yellow-500',
                                        'in_progress' => 'bg-blue-500',
                                        'resolved' => 'bg-green-600',
                                        'Hold - Third Party' => 'bg-purple-600',
                                        'Hold - Waiting User Response' => 'bg-orange-500',
                                        default => 'bg-gray-400',
                                    };
                                @endphp
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 font-semibold text-blue-700">
                                        {{ strtoupper(substr($ticket->category, 0, 1)) }}{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-2">{{ $ticket->nama }}</td>
                                    <td class="p-2">{{ $ticket->title }}</td>
                                    <td class="p-2">{{ $ticket->cabang }}</td>
                                    <td class="p-2 capitalize">{{ $ticket->category }}</td>
                                    <td class="p-2 capitalize">{{ $ticket->klasifikasi }}</td>

                                    {{-- PRIORITY DROPDOWN --}}
                                    <td class="p-2">
                                        <select
                                            class="priority-dropdown text-sm border-gray-300 rounded-md focus:ring-1 focus:ring-blue-500"
                                            data-id="{{ $ticket->id }}" style="min-width:100px; padding:3px 5px;">
                                            <option value="Low" {{ $ticket->priority === 'Low' ? 'selected' : '' }}>
                                                🟢 Low</option>
                                            <option value="Medium"
                                                {{ $ticket->priority === 'Medium' ? 'selected' : '' }}>
                                                🟡 Medium</option>
                                            <option value="Critical"
                                                {{ $ticket->priority === 'Critical' ? 'selected' : '' }}>
                                                🔴 Critical</option>
                                        </select>
                                    </td>

                                    {{-- STATUS --}}
                                    <td class="p-2">
                                        <span
                                            class="px-3 py-1 rounded-full text-white text-xs font-semibold {{ $statusColor }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>

                                    <td class="p-2">{{ $ticket->takenByUser?->name ?? '-' }}</td>
                                    <td class="p-2">{{ $ticket->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $ticket->finished_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $durasi }}</td>

                                    {{-- AKSI --}}
                                    <td class="p-2">
                                        @if ($ticket->status === 'open')
                                            {{-- Tombol Ambil --}}
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <button @click="open = !open"
                                                    class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded-md text-xs font-semibold w-full flex justify-between items-center">
                                                    ⚙️ Aksi
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                                                        class="w-3 h-3 ml-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>

                                                <div x-show="open" @click.away="open = false"
                                                    class="absolute z-20 mt-1 w-32 bg-white border border-gray-200 rounded-md shadow-lg text-xs">
                                                    {{-- Tombol Ambil --}}
                                                    <form action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="hidden" name="status" value="in_progress">
                                                        <button type="submit"
                                                            class="block w-full text-left px-3 py-2 hover:bg-blue-50 text-blue-600 rounded-t-md">
                                                            🚀 Ambil
                                                        </button>
                                                    </form>

                                                    {{-- Tombol Hapus --}}
                                                    <button type="button" data-id="{{ $ticket->id }}"
                                                        class="btn-delete block w-full text-left px-3 py-2 hover:bg-red-50 text-red-600 rounded-b-md">
                                                        🗑️ Hapus
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif (in_array($ticket->status, ['in_progress', 'Hold - Third Party', 'Hold - Waiting User Response']))
                                            <div class="flex flex-col gap-2">
                                                {{-- Tombol Selesai pakai SweetAlert --}}
                                                <button type="button"
                                                    class="btn-finish bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-md text-xs font-semibold w-full"
                                                    data-id="{{ $ticket->id }}">
                                                    ✅ Selesai
                                                </button>

                                                {{-- Dropdown Hold --}}
                                                <div x-data="{ open: false }" class="relative">
                                                    <button type="button" @click="open = !open"
                                                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-md text-xs font-semibold w-full flex justify-between items-center">
                                                        ⏸️ Tahan Tiket
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                            viewBox="0 0 24 24" stroke-width="2"
                                                            stroke="currentColor" class="w-3 h-3 ml-1">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                d="M19 9l-7 7-7-7" />
                                                        </svg>
                                                    </button>

                                                    <div x-show="open" @click.away="open = false"
                                                        class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
                                                        <form
                                                            action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                            method="POST" class="block">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status"
                                                                value="Hold - Third Party">
                                                            <button type="submit"
                                                                class="block w-full text-left px-3 py-2 text-xs hover:bg-yellow-100 rounded-t-md">
                                                                🧑‍💻 Hold - Third Party
                                                            </button>
                                                        </form>

                                                        <form
                                                            action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                            method="POST" class="block">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status"
                                                                value="Hold - Waiting User Response">
                                                            <button type="submit"
                                                                class="block w-full text-left px-3 py-2 text-xs hover:bg-yellow-100 rounded-b-md">
                                                                💬 Hold - Waiting User
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-gray-400 text-xs italic">✅ Done</span>
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

    @push('scripts')
        {{-- Tambahkan jQuery dulu (wajib sebelum DataTables) --}}
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        {{-- Tambahkan SweetAlert2 dan DataTables --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">

        <script>
            $(document).ready(function() {

                // === INIT DATATABLES ===
                const table = $('#ticketTable').DataTable({
                    pageLength: 10,
                    lengthMenu: [5, 10, 25, 50, 100],
                    order: [
                        [9, 'desc']
                    ], // Kolom ke-10 = "Mulai"
                    language: {
                        search: "🔍 Cari:",
                        lengthMenu: "Tampilkan _MENU_ tiket per halaman",
                        info: "Menampilkan _START_ - _END_ dari _TOTAL_ tiket",
                        infoEmpty: "Tidak ada data tiket",
                        paginate: {
                            previous: "← Sebelumnya",
                            next: "Berikutnya →"
                        },
                        emptyTable: "Belum ada tiket tercatat"
                    },
                    responsive: true,
                    autoWidth: false,
                });

                // === FILTER DROPDOWN (masih jalan bareng DataTables) ===
                const spinner = $('#loadingSpinner');
                $('#filterCabang, #filterKategori, #filterStatus, #filterTanggal').on('change', function() {
                    spinner.removeClass('hidden');

                    setTimeout(() => {
                        const cabang = $('#filterCabang').val().toLowerCase();
                        const kategori = $('#filterKategori').val().toLowerCase();
                        const status = $('#filterStatus').val().toLowerCase();
                        const tanggal = $('#filterTanggal').val();

                        table.rows().every(function() {
                            const data = this.data();
                            const row = $(this.node());
                            const cabangCol = data[3]?.toLowerCase() ?? '';
                            const kategoriCol = data[4]?.toLowerCase() ?? '';
                            const statusCol = data[7]?.toLowerCase() ?? '';

                            let matchTanggal = true;
                            if (tanggal === 'today') {
                                const today = new Date().toLocaleDateString('id-ID');
                                matchTanggal = data[9]?.startsWith(today);
                            }

                            const match =
                                (!cabang || cabangCol.includes(cabang)) &&
                                (!kategori || kategoriCol.includes(kategori)) &&
                                (!status || statusCol.includes(status)) &&
                                matchTanggal;

                            if (match) row.show();
                            else row.hide();
                        });

                        spinner.addClass('hidden');
                    }, 300);
                });

                // === PRIORITY AJAX ===
                $('.priority-dropdown').on('change', async function() {
                    const id = $(this).data('id');
                    const value = $(this).val();

                    try {
                        const res = await fetch(`/tickets/${id}/priority`, {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                priority: value
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1200
                            });

                            $(this).css({
                                'background-color': value === 'Critical' ? '#dc2626' : value ===
                                    'Medium' ? '#facc15' : '#22c55e',
                                'color': 'white'
                            });
                        } else {
                            throw new Error('Gagal update priority');
                        }
                    } catch (e) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Terjadi kesalahan',
                            text: 'Gagal memperbarui priority.'
                        });
                    }
                });

                // === HAPUS TICKET ===
                $('.btn-delete').on('click', async function() {
                    const id = $(this).data('id');

                    const result = await Swal.fire({
                        title: 'Hapus Tiket?',
                        text: 'Apakah Anda yakin ingin menghapus tiket ini? Aksi ini tidak bisa dibatalkan.',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc2626',
                        cancelButtonColor: '#6b7280',
                        confirmButtonText: 'Ya, Hapus',
                        cancelButtonText: 'Batal'
                    });

                    if (result.isConfirmed) {
                        try {
                            const res = await fetch(`/tickets/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json'
                                }
                            });

                            const data = await res.json();

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: data.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });

                                table.row($(this).closest('tr')).remove().draw(false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal Menghapus',
                                    text: data.message
                                });
                            }
                        } catch (error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Terjadi kesalahan server saat menghapus tiket.'
                            });
                        }
                    }
                });

                // === SWEETALERT RESOLVED NOTE ===
                $('.btn-finish').on('click', async function() {
                    const id = $(this).data('id');

                    const {
                        value: note
                    } = await Swal.fire({
                        title: '📝 Catatan Penyelesaian',
                        input: 'textarea',
                        inputPlaceholder: 'Tuliskan bagaimana masalah ini diselesaikan...',
                        showCancelButton: true,
                        confirmButtonText: 'Simpan & Selesai',
                        cancelButtonText: 'Batal',
                        confirmButtonColor: '#16a34a',
                        cancelButtonColor: '#6b7280',
                        inputValidator: (value) => {
                            if (!value) return 'Catatan wajib diisi!';
                        }
                    });

                    if (note) {
                        try {
                            const res = await fetch(`/tickets/${id}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json' // ✅ tambahkan baris ini
                                },
                                body: JSON.stringify({
                                    status: 'resolved',
                                    resolution_note: note
                                })
                            });

                            const data = await res.json();

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tiket diselesaikan!',
                                    text: 'Catatan tersimpan sebagai dokumentasi internal.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                setTimeout(() => location.reload(), 1200);
                            } else {
                                throw new Error('Gagal update');
                            }
                        } catch (e) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menyimpan',
                                text: 'Terjadi kesalahan saat memperbarui tiket.'
                            });
                        }
                    }
                });

            });
        </script>

        <style>
            /* Styling tambahan agar pagination & search rapi */
            .dataTables_wrapper .dataTables_paginate {
                margin-top: 10px;
                text-align: center;
            }

            .dataTables_wrapper .dataTables_length,
            .dataTables_wrapper .dataTables_filter {
                margin-bottom: 0.75rem;
            }

            .dataTables_paginate .paginate_button {
                padding: 0.3em 0.8em;
                border-radius: 0.375rem;
                background-color: #f1f5f9;
                margin: 0 2px;
            }

            .dataTables_paginate .paginate_button.current {
                background-color: #3b82f6;
                color: white !important;
                border: none;
            }
        </style>
    @endpush


</x-app-layout>

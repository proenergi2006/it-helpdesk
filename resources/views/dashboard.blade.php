<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            👥 Dashboard Tim IT Departemen
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
                    <option value="Hold - Teknisi">Hold - Teknisi</option>
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
                                        'cancel' => 'bg-red-500',
                                        'in_progress' => 'bg-blue-500',
                                        'resolved' => 'bg-green-600',
                                        'Hold - Third Party' => 'bg-purple-600',
                                        'Hold - Waiting User Response' => 'bg-orange-500',
                                        'Hold - Teknisi' => 'bg-red-500',
                                        default => 'bg-gray-400',
                                    };
                                @endphp
                                <tr class="border-b hover:bg-gray-50 transition">
                                    <td class="p-2 font-semibold text-blue-700">
                                        {{ strtoupper(substr($ticket->category, 0, 1)) }}{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}
                                    </td>
                                    <td class="p-2">{{ $ticket->nama }}</td>
                                    <td class="p-2">{{ $ticket->title }}

                                    </td>
                                    <td class="p-2">{{ $ticket->cabang }}</td>
                                    <td class="p-2 capitalize">{{ $ticket->category }}</td>
                                    <td class="p-2 capitalize">{{ $ticket->klasifikasi }}
                                        @if ($ticket->description)
                                            <button type="button"
                                                class="text-blue-600 text-xs underline hover:text-blue-800 btn-view-desc"
                                                data-desc="{{ e($ticket->description) }}"
                                                data-id="{{ $ticket->id }}">
                                                📄 Deskripsi
                                            </button>
                                        @endif
                                    </td>

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
                                        <span class="status-badge {{ $statusColor }}">
                                            {{ $ticket->status }}
                                        </span>
                                    </td>

                                    <td class="p-2">{{ $ticket->takenByUser?->name ?? '-' }} <p>

                                            @if ($ticket->status === 'resolved' && $ticket->resolution_note)
                                                <button type="button"
                                                    class="mt-1 text-blue-600 text-xs underline hover:text-blue-800 btn-view-note"
                                                    data-note="{{ e($ticket->resolution_note) }}"
                                                    data-id="{{ $ticket->id }}">
                                                    📄Detail
                                                </button>
                                            @endif

                                    </td>
                                    <td class="p-2">{{ $ticket->started_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $ticket->finished_at?->format('d/m/Y H:i') ?? '-' }}</td>
                                    <td class="p-2">{{ $durasi }}</td>

                                    {{-- AKSI --}}
                                    <td class="p-2">
                                        <div class="flex flex-col gap-2">
                                            @if ($ticket->status !== 'resolved' && $ticket->status !== 'cancel')
                                                <button type="button"
                                                    class="bg-indigo-500 hover:bg-indigo-600 text-white px-3 py-1 rounded-md text-xs font-semibold w-full transfer-btn"
                                                    data-id="{{ $ticket->id }}">
                                                    🔄 Transfer
                                                </button>
                                            @else
                                                <span class="text-gray-400 text-xs italic">-</span>
                                            @endif
                                        </div>

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
                                                        🗑️ Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif (in_array($ticket->status, ['in_progress', 'Hold - Third Party', 'Hold - Waiting User Response', 'Hold - Teknisi']))
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

                                                        <form
                                                            action="{{ route('tickets.updateStatus', $ticket->id) }}"
                                                            method="POST" class="block">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status"
                                                                value="Hold - Teknisi">
                                                            <button type="submit"
                                                                class="block w-full text-left px-3 py-2 text-xs hover:bg-yellow-100 rounded-b-md">
                                                                🧰 Hold - Teknisi
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

    {{-- Modal Deskripsi --}}
    <div id="descModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-[90%] max-w-2xl p-5 overflow-y-auto max-h-[90vh]">
            <h2 class="text-lg font-semibold text-blue-700 mb-3">📝 Deskripsi Masalah</h2>

            <!-- Deskripsi -->
            <div id="descContent"
                class="text-gray-700 text-sm whitespace-pre-line border p-3 rounded-md bg-gray-50 mb-4">
            </div>

            <!-- Lampiran -->
            <div id="descAttachments" class="space-y-3"></div>

            <div class="text-right mt-4">
                <button id="closeDescModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm">Tutup</button>
            </div>
        </div>
    </div>


    <!-- Modal Catatan -->
    <div id="noteModal" class="hidden fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-96 p-4">
            <h2 class="text-lg font-semibold text-blue-700 mb-2">📝 Catatan Penyelesaian</h2>
            <div id="noteContent" class="text-gray-700 text-sm whitespace-pre-line border p-2 rounded-md bg-gray-50">
            </div>
            <div class="text-right mt-4">
                <button id="closeNoteModal"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-md text-sm">Tutup</button>
            </div>
        </div>
    </div>


    <style>
        #noteModal {
            animation: fadeIn 0.2s ease-in-out;
        }

        #ticketTable th:nth-child(8),
        #ticketTable td:nth-child(8) {
            min-width: 160px;
            /* kamu bisa ubah jadi 180 atau 200 sesuai kebutuhan */
            text-align: center;
        }

        /* Perbaiki tampilan badge status */
        .status-badge {
            display: inline-block;
            min-width: 130px;
            padding: 6px 10px;
            line-height: 1.2rem;
            white-space: normal;
            word-break: break-word;
            text-align: center;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            color: #fff;
        }

        /* Agar tabel tetap rapi di layar kecil */
        @media (max-width: 768px) {

            #ticketTable th:nth-child(8),
            #ticketTable td:nth-child(8) {
                min-width: 120px;
            }
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.98);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        select {
            appearance: none;
            /* Hilangkan arrow default */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg fill='none' stroke='%236b7280' stroke-width='2' viewBox='0 0 24 24'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.6rem center;
            background-size: 1rem;
            padding-right: 2rem;
            /* beri ruang untuk ikon panah */
        }

        /* Tampilan select lebih rapi seperti button */
        select.border-gray-300 {
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            background-color: white;
            font-size: 0.875rem;
            font-weight: 500;
            color: #111827;
            transition: all 0.2s;
        }

        select.border-gray-300:hover {
            border-color: #3b82f6;
        }

        select:focus {
            outline: none;
            ring: 2px solid #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
        }
    </style>

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
                    order: [], // ✅ kosongkan agar urutan dari backend dipakai
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

                // === LIHAT CATATAN PENYELESAIAN ===
                $(document).on('click', '.btn-view-note', function() {
                    const note = $(this).data('note');
                    $('#noteContent').text(note);
                    $('#noteModal').removeClass('hidden');
                });

                $('#closeNoteModal').on('click', function() {
                    $('#noteModal').addClass('hidden');
                });

                $(document).on('click', function(e) {
                    if ($(e.target).is('#noteModal')) {
                        $('#noteModal').addClass('hidden');
                    }
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

                $(document).on('click', '.btn-delete', async function() {
                    const id = $(this).data('id');
                    const row = $(this).closest('tr');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

                    const {
                        value: cancelNote
                    } = await Swal.fire({
                        title: "Batalkan Tiket?",
                        input: "textarea",
                        inputPlaceholder: "Tuliskan alasan pembatalan...",
                        showCancelButton: true,
                        confirmButtonColor: "#dc2626",
                        cancelButtonColor: "#6b7280",
                        confirmButtonText: "Ya, Batalkan",
                        cancelButtonText: "Batal",
                        inputValidator: value => {
                            if (!value) return "Catatan wajib diisi!";
                        }
                    });

                    if (!cancelNote) return;

                    try {
                        const res = await fetch(`/tickets/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": csrfToken,
                                "Accept": "application/json",
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                cancel_note: cancelNote
                            })
                        });

                        const data = await res.json();

                        if (data.success) {
                            Swal.fire({
                                icon: "success",
                                title: "Dibatalkan!",
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                            });

                            $('#ticketTable').DataTable().row(row).remove().draw(false);
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: "Gagal",
                                text: data.message
                            });
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: "error",
                            title: "Error",
                            text: "Terjadi kesalahan server."
                        });
                    }
                });



                // === SWEETALERT RESOLVED NOTE ===
                $(document).on('click', '.btn-finish', async function() {
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
                            const csrfToken = document
                                .querySelector('meta[name="csrf-token"]')
                                .getAttribute('content');

                            const res = await fetch(`/tickets/${id}/status`, {
                                method: 'PATCH',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json',
                                },
                                body: JSON.stringify({
                                    status: 'resolved',
                                    resolution_note: note,
                                }),
                            });

                            if (!res.ok) throw new Error('HTTP ' + res.status);
                            const data = await res.json();

                            if (data.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Tiket diselesaikan!',
                                    text: 'Catatan tersimpan sebagai dokumentasi internal.',
                                    timer: 1500,
                                    showConfirmButton: false,
                                });
                                setTimeout(() => location.reload(), 1200);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Gagal update',
                                    text: data.message ||
                                        'Terjadi kesalahan saat memperbarui tiket.',
                                });
                            }
                        } catch (e) {
                            console.error(e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal menyimpan',
                                text: 'Terjadi kesalahan saat memperbarui tiket.',
                            });
                        }
                    }
                });


                $('.transfer-btn').on('click', function() {
                    let ticketId = $(this).data('id');

                    Swal.fire({
                        title: "Alihkan Ticket ke Teknisi?",
                        input: "select",
                        inputOptions: {
                            @foreach ($technicians as $t)
                                "{{ $t->id }}": "{{ $t->name }}",
                            @endforeach
                        },
                        inputPlaceholder: "Pilih teknisi",
                        showCancelButton: true,
                        confirmButtonColor: '#2563eb',
                        cancelButtonColor: '#9ca3af',
                        confirmButtonText: "Transfer"
                    }).then((result) => {
                        if (result.isConfirmed) {

                            $.ajax({
                                url: `/tickets/${ticketId}/transfer`,
                                type: 'PUT',
                                data: {
                                    new_technician_id: result.value,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(res) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Berhasil!',
                                        text: res.message,
                                        timer: 2000,
                                        showConfirmButton: false
                                    });

                                    setTimeout(() => location.reload(), 1200);
                                },
                                error: function(err) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Gagal!',
                                        text: err.responseJSON?.message ??
                                            'Terjadi kesalahan.',
                                    });
                                }
                            });

                        }
                    });
                });


                // === LIHAT DESKRIPSI MASALAH ===
                $(document).on('click', '.btn-view-desc', function() {
                    const desc = $(this).data('desc');
                    const ticketId = $(this).data('id');

                    // tampilkan deskripsi teks
                    $('#descContent').text(desc || '-');

                    // kosongkan area lampiran
                    $('#descAttachments').html(
                        '<p class="text-gray-400 text-sm italic">Memuat lampiran...</p>');

                    // tampilkan modal dulu
                    $('#descModal').removeClass('hidden');

                    // ambil lampiran via AJAX
                    $.get(`/tickets/${ticketId}/detail`, function(data) {
                        const attachDiv = $('#descAttachments');
                        attachDiv.empty();

                        if (data.attachments && data.attachments.length > 0) {
                            data.attachments.forEach(file => {
                                const url = `/storage/${file.file_path}`;
                                const ext = file.file_name.split('.').pop().toLowerCase();

                                if (['jpg', 'jpeg', 'png'].includes(ext)) {
                                    attachDiv.append(`
                        <div class="border rounded-md p-2 bg-gray-50 mb-3">
                            <p class="text-sm text-gray-600 mb-1 font-semibold">📷 ${file.file_name}</p>
                            <img src="${url}" alt="${file.file_name}" class="max-h-64 rounded-lg shadow mx-auto">
                        </div>
                    `);
                                } else if (ext === 'pdf') {
                                    attachDiv.append(`
                        <div class="border rounded-md p-2 bg-gray-50 mb-3">
                            <p class="text-sm text-gray-600 mb-1 font-semibold">📄 ${file.file_name}</p>
                            <iframe src="${url}" class="w-full h-64 border rounded-lg"></iframe>
                        </div>
                    `);
                                } else {
                                    attachDiv.append(`
                        <div class="border rounded-md p-2 bg-gray-50 mb-3">
                            <p class="text-sm text-gray-600 mb-1 font-semibold">📎 ${file.file_name}</p>
                            <a href="${url}" target="_blank" class="text-blue-600 hover:underline text-sm">Lihat / Unduh File</a>
                        </div>
                    `);
                                }
                            });
                        } else {
                            attachDiv.html(
                                `<p class="text-gray-500 text-sm italic">Tidak ada lampiran.</p>`);
                        }
                    }).fail(() => {
                        $('#descAttachments').html(
                            `<p class="text-red-500 text-sm italic">Gagal memuat lampiran.</p>`);
                    });
                });

                // tombol tutup
                $('#closeDescModal').on('click', function() {
                    $('#descModal').addClass('hidden');
                });

                // klik luar modal menutup
                $(document).on('click', function(e) {
                    if ($(e.target).is('#descModal')) {
                        $('#descModal').addClass('hidden');
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

            #descModal {
                animation: fadeIn 0.2s ease-in-out;
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

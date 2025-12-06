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

        #chatbot {
            animation: slideUp 0.3s ease-out;
        }

        @keyframes slideUp {
            from {
                transform: translateY(20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Scroll bar halus */
        #chat-messages::-webkit-scrollbar {
            width: 6px;
        }

        #chat-messages::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }

        /* Lebarkan kolom STATUS di tabel antrian */
        #ticketTable th:nth-child(7),
        #ticketTable td:nth-child(7) {
            min-width: 150px;
            /* kamu bisa ubah jadi 180px kalau masih sempit */
            text-align: center;
            white-space: normal;
            /* biar teks bisa turun ke bawah kalau panjang */
            word-wrap: break-word;
        }

        /* Biar badge status tetap rapi di tengah */
        #ticketTable td:nth-child(7) span {
            display: inline-block;
            min-width: 110px;
            padding: 6px 10px;
            border-radius: 9999px;
            font-size: 11px;
            font-weight: 600;
            text-align: center;
            color: #fff;
        }

        /* Tambahan: sedikit merapikan cell tabel biar tidak padat */
        #ticketTable td,
        #ticketTable th {
            vertical-align: middle;
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

                            @php
                                $cat = strtoupper(substr($ticket->category, 0, 1)); // S / H
                                $klas = strtoupper(substr($ticket->klasifikasi, 0, 1)); // I / R
                                $code = $cat . $klas . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
                            @endphp

                            <div class="text-3xl md:text-4xl font-extrabold mb-1">
                                #{{ $code }}
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
                            @php
                                $cat = strtoupper(substr($ticket->category, 0, 1)); // H
                                $klas = strtoupper(substr($ticket->klasifikasi, 0, 1)); // I / R
                                $code = $cat . $klas . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
                            @endphp

                            <div class="text-3xl md:text-4xl font-extrabold mb-1">
                                #{{ $code }}
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
                            <th class="p-2 text-left">Klasifikasi</th>
                            <th class="p-2 text-left">Priority</th>
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
                                <td class="p-2 capitalize">{{ $ticket->klasifikasi ?? '-' }}</td>
                                <td class="p-2">
                                    <span
                                        class="px-3 py-1 rounded-full text-white text-xs font-semibold
        {{ $ticket->priority === 'Low'
            ? 'bg-green-600'
            : ($ticket->priority === 'Medium'
                ? 'bg-yellow-500'
                : 'bg-red-600') }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </td>
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

    {{-- 🌟 Modal Form Tambah Ticket --}}
    <div x-show="showModal" x-transition
        class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-lg w-full max-w-3xl p-8 relative mx-3 overflow-y-auto max-h-[90vh]">
            {{-- Header Modal --}}
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-xl font-semibold text-blue-700 flex items-center gap-2">
                    📝 Buat Ticket Baru
                </h2>
                <button @click="showModal = false" class="text-gray-400 hover:text-gray-600 text-2xl leading-none">
                    &times;
                </button>
            </div>

            {{-- Form Ticket --}}
            <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data"
                class="space-y-6">
                @csrf

                {{-- GRID WRAPPER --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Nama --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Nama</label>
                        <input name="nama" type="text"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Email</label>
                        <input name="email" type="email"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">
                            CC Email (Opsional)
                            <span class="text-xs text-gray-500">(pisahkan dengan koma)</span>
                        </label>

                        <input name="cc_emails" type="text"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="contoh: orang1@proenergi.co.id, orang2@proenergi.co.id">
                    </div>

                    {{-- Cabang --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Cabang</label>
                        <select name="cabang"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Cabang</option>
                            <option>Head Office</option>
                            <option>Jakarta</option>
                            <option>Surabaya</option>
                            <option>Samarinda</option>
                            <option>Palembang</option>
                            <option>Banjarmasin</option>
                            <option>Pontianak</option>
                            <option>Sulawesi</option>
                        </select>
                    </div>

                    {{-- Kategori --}}
                    <div>
                        <label class="block text-gray-700 font-medium mb-1">Kategori</label>
                        <select name="category"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Pilih Kategori</option>
                            <option value="software">Software</option>
                            <option value="hardware">Hardware</option>
                            <option value="network&multimedia">Network & Multimedia</option>
                        </select>
                    </div>

                    {{-- Klasifikasi --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Klasifikasi</label>
                        <select name="klasifikasi"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Klasifikasi</option>
                            <option value="Incident">Incident (Gangguan / Error)</option>
                            <option value="Request">Request (Permintaan Fitur / Akses)</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Priority</label>
                        <select name="priority"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Priority</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>


                    {{-- Judul Ticket (Full width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Judul Ticket</label>
                        <input name="title" type="text"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    {{-- Deskripsi (Full width) --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">Deskripsi Masalah</label>
                        <textarea name="description" rows="4"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"></textarea>
                    </div>

                    {{-- Attachment (opsional, max 3 file, 2MB, jpg/png/pdf) --}}
                    <div class="md:col-span-2">
                        <label class="block text-gray-700 font-medium mb-1">
                            Lampiran (Opsional) <span class="text-gray-500 text-xs">(maks. 3 file, 2MB,
                                PDF/JPG/PNG)</span>
                        </label>
                        <input type="file" name="attachments[]" id="attachments"
                            class="w-full border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500 p-1"
                            accept=".jpg,.jpeg,.png,.pdf" multiple>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="flex justify-end space-x-3 pt-6 border-t">
                    <button type="button" @click="showModal = false"
                        class="bg-gray-300 hover:bg-gray-400 px-4 py-2 rounded-lg font-medium transition-all">
                        Batal
                    </button>
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2 rounded-lg font-semibold transition-all">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="chat-toggle"
        class="fixed bottom-6 right-6 bg-blue-600 text-white w-14 h-14 flex items-center justify-center rounded-full shadow-lg cursor-pointer hover:bg-blue-700 transition-all">
        💬
    </div>

    <!-- Kotak chat (awalnya disembunyikan) -->
    <div id="chatbot"
        class="hidden fixed bottom-20 right-6 w-80 bg-white rounded-xl shadow-lg border border-gray-200">
        <div class="bg-blue-600 text-white p-3 rounded-t-xl font-semibold flex justify-between items-center">
            <span>💬 Bantuan Otomatis IT</span>
            <button id="chat-close" class="text-white hover:text-gray-200 text-sm">✖</button>
        </div>
        <div id="chat-messages" class="p-3 h-60 overflow-y-auto text-sm space-y-2">
            <div class="text-gray-500 italic">Ketik pertanyaan seputar masalah Anda...</div>
        </div>
        <div class="p-3 border-t flex">
            <input id="chat-input" type="text" class="flex-1 border-gray-300 rounded-l-md px-2 py-1 text-sm"
                placeholder="Tulis pertanyaan...">
            <button id="chat-send" class="bg-blue-600 text-white px-3 py-1 rounded-r-md text-sm">Kirim</button>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form[action="{{ route('tickets.store') }}"]');
            const requiredFields = [{
                    name: 'nama',
                    label: 'Nama'
                },
                {
                    name: 'email',
                    label: 'Email'
                },
                {
                    name: 'cabang',
                    label: 'Cabang'
                },
                {
                    name: 'category',
                    label: 'Kategori'
                },
                {
                    name: 'klasifikasi',
                    label: 'Klasifikasi'
                },
                {
                    name: 'priority',
                    label: 'Priority'
                },
                {
                    name: 'title',
                    label: 'Judul Ticket'
                },
            ];
            const fileInput = document.getElementById('attachments');

            form.addEventListener('submit', function(e) {
                // === VALIDASI FIELD WAJIB ===
                for (const field of requiredFields) {
                    const input = form.querySelector(`[name="${field.name}"]`);
                    if (!input || input.value.trim() === '') {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'warning',
                            title: 'Form belum lengkap!',
                            text: `Field "${field.label}" wajib diisi.`,
                            confirmButtonColor: '#3b82f6'
                        });
                        input.focus();
                        return false;
                    }
                }

                // === VALIDASI FILE UPLOAD ===
                const files = fileInput.files;
                if (files.length > 3) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'Terlalu banyak file!',
                        text: 'Maksimal hanya boleh mengunggah 3 file.',
                        confirmButtonColor: '#3b82f6'
                    });
                    return false;
                }

                for (let i = 0; i < files.length; i++) {
                    const file = files[i];
                    const allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];

                    if (!allowedTypes.includes(file.type)) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Format file tidak diizinkan!',
                            text: `File ${file.name} bukan PDF/JPG/PNG.`,
                            confirmButtonColor: '#ef4444'
                        });
                        return false;
                    }

                    if (file.size > 2 * 1024 * 1024) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Ukuran file terlalu besar!',
                            text: `File ${file.name} melebihi 2MB.`,
                            confirmButtonColor: '#ef4444'
                        });
                        return false;
                    }
                }
            });
        });
        document.addEventListener("DOMContentLoaded", function() {
            const chatToggle = document.getElementById("chat-toggle");
            const chatBox = document.getElementById("chatbot");
            const chatClose = document.getElementById("chat-close");
            const input = document.getElementById("chat-input");
            const sendBtn = document.getElementById("chat-send");
            const chatMessages = document.getElementById("chat-messages");

            let hasOpened = false;

            // === buka/tutup chat ===
            chatToggle.addEventListener("click", () => {
                chatBox.classList.toggle("hidden");
                chatToggle.classList.toggle("hidden");

                // jika pertama kali dibuka, kirim salam otomatis
                if (!hasOpened) {
                    hasOpened = true;
                    setTimeout(() => {
                        chatMessages.innerHTML += `
                    <div class="text-left bg-gray-100 rounded-md px-2 py-1">
                        🤖 Halo! Ada yang bisa saya bantu?
                    </div>
                `;
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    }, 300);
                }
            });

            chatClose.addEventListener("click", () => {
                chatBox.classList.add("hidden");
                chatToggle.classList.remove("hidden");
            });

            // === fungsi kirim pesan ===
            async function sendMessage() {
                const question = input.value.trim();
                if (!question) return;

                chatMessages.innerHTML +=
                    `<div class="text-right text-blue-600">🧑‍💻 ${question}</div>`;
                input.value = '';
                chatMessages.innerHTML +=
                    `<div id="loading" class="text-gray-400 italic">Mengetik...</div>`;
                chatMessages.scrollTop = chatMessages.scrollHeight;

                try {
                    const res = await fetch("{{ route('chat.ask') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}",
                            "Accept": "application/json"
                        },
                        body: JSON.stringify({
                            question
                        })
                    });

                    const data = await res.json();
                    document.getElementById("loading").remove();

                    chatMessages.innerHTML += `
                <div class="text-left bg-gray-100 rounded-md px-2 py-1">
                    🤖 ${data.answer}
                </div>
            `;
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                } catch (e) {
                    document.getElementById("loading").remove();
                    chatMessages.innerHTML +=
                        `<div class="text-red-500">❌ Terjadi kesalahan server.</div>`;
                }
            }

            sendBtn.addEventListener("click", sendMessage);
            input.addEventListener("keypress", e => {
                if (e.key === "Enter") sendMessage();
            });
        });
    </script>



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
                 <div class="text-3xl font-extrabold mb-1">
    #${t.category[0].toUpperCase()}${t.klasifikasi ? t.klasifikasi[0].toUpperCase() : ''}${String(t.id).padStart(3,'0')}
</div>

                    <div class="text-base font-semibold truncate">${t.title}</div>
                    <div class="text-sm">Cabang: <span class="font-bold">${t.cabang}</span></div>
                    <div class="text-sm mt-1">Status: <span class="font-bold capitalize">${t.status}</span></div>
                </div>`).join('') || `<div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>`;

            hPanel.innerHTML = hardware.map(t => `
                <div class="bg-green-500 rounded-xl p-4 text-center shadow-md">
                    <div class="text-3xl font-extrabold mb-1">#${t.category[0].toUpperCase()}${t.klasifikasi ? t.klasifikasi[0].toUpperCase() : ''}${String(t.id).padStart(3,'0')}
</div>
                    <div class="text-base font-semibold truncate">${t.title}</div>
                    <div class="text-sm">Cabang: <span class="font-bold">${t.cabang}</span></div>
                    <div class="text-sm mt-1">Status: <span class="font-bold capitalize">${t.status}</span></div>
                </div>`).join('') || `<div class="text-gray-200 text-center text-xl py-6">Belum ada ticket</div>`;

            tbody.innerHTML = data.map(t => `
                <tr class="border-b hover:bg-blue-50 transition">
                    <td class="p-2 font-semibold text-blue-700">${t.category[0].toUpperCase()}${t.klasifikasi ? t.klasifikasi[0].toUpperCase() : ''}${String(t.id).padStart(3,'0')}
</td>
                    <td class="p-2">${t.nama ?? '-'}</td>
                    <td class="p-2">${t.title}</td>
                    <td class="p-2">${t.cabang}</td>
                    <td class="p-2 capitalize">${t.category}</td>
                    <td class="p-2 capitalize">${t.klasifikasi ?? '-'}</td>
                 <td class="p-2">
    <span class="px-3 py-1 rounded-full text-white text-xs font-semibold ${
        t.priority === 'Low' ? 'bg-green-600' :
        t.priority === 'Medium' ? 'bg-yellow-500' :
        t.priority === 'Critical' ? 'bg-red-600' :
        'bg-gray-400'
    }">
        ${t.priority ?? '-'}
    </span>
</td>
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
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50, 100],
            order: [],
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

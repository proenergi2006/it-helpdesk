<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Ticket Saya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    @php
        function ticketCode($ticket) {
            $cat = strtoupper(substr($ticket->category, 0, 1));
            $klas = strtoupper(substr($ticket->klasifikasi, 0, 1));
            return $cat . $klas . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);
        }

        function statusBadgeClass($status) {
            return match($status) {
                'open' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
                'in_progress' => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',
                'resolved' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
                default => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
            };
        }

        function priorityBadgeClass($priority) {
            return match($priority) {
                'Low' => 'bg-emerald-500 text-white',
                'Medium' => 'bg-amber-500 text-white',
                'Critical' => 'bg-rose-500 text-white',
                default => 'bg-slate-400 text-white',
            };
        }

        function categoryLabel($category) {
            return match($category) {
                'software' => 'Software',
                'hardware' => 'Hardware',
                'network&multimedia' => 'Network & Multimedia',
                default => ucfirst($category),
            };
        }

        function categoryIcon($category) {
            return match($category) {
                'software' => '💻',
                'hardware' => '🖥️',
                'network&multimedia' => '📡',
                default => '📝',
            };
        }
    @endphp

    <div class="min-h-screen">
        {{-- Top Header --}}
        <div class="bg-gradient-to-r from-orange-500 via-orange-400 to-amber-400 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-white/90 backdrop-blur flex items-center justify-center shadow">
                            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Logo Pro Energi" class="h-10 w-auto object-contain">
                        </div>
                        <div class="text-white">
                            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                                Tracking Ticket Saya
                            </h1>
                            <p class="text-white/90 text-sm md:text-base">
                                Pantau seluruh ticket yang Anda buat secara real-time
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center gap-3">
                        <div class="flex items-center gap-3 bg-white/15 backdrop-blur rounded-2xl px-4 py-3 text-white">
                            <div class="h-10 w-10 rounded-full bg-white/20 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}
                            </div>
                            <div class="leading-tight">
                                <div class="font-semibold">{{ auth()->user()->name ?? 'User' }}</div>
                                <div class="text-xs text-white/85">{{ auth()->user()->email ?? '-' }}</div>
                            </div>
                        </div>

                        <a href="{{ route('welcome') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-700 hover:bg-slate-800 text-white px-5 py-3 font-semibold shadow transition">
                            Kembali
                        </a>

                        <a href="{{ route('welcome', ['open_ticket' => 1]) }}"
                            class="inline-flex items-center justify-center rounded-xl bg-white hover:bg-orange-50 text-orange-600 px-5 py-3 font-semibold shadow transition">
                            + Buat Ticket
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <div class="text-sm text-slate-500 mb-2">Total Ticket</div>
                    <div class="text-3xl font-extrabold text-slate-800">{{ $totalTickets }}</div>
                    <div class="mt-2 text-xs text-slate-400">Semua ticket yang pernah Anda buat</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-amber-200 p-5">
                    <div class="text-sm text-amber-600 mb-2">Open</div>
                    <div class="text-3xl font-extrabold text-amber-500">{{ $openTickets }}</div>
                    <div class="mt-2 text-xs text-slate-400">Menunggu penanganan</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-sky-200 p-5">
                    <div class="text-sm text-sky-600 mb-2">In Progress</div>
                    <div class="text-3xl font-extrabold text-sky-500">{{ $progressTickets }}</div>
                    <div class="mt-2 text-xs text-slate-400">Sedang dikerjakan tim IT</div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-emerald-200 p-5">
                    <div class="text-sm text-emerald-600 mb-2">Resolved</div>
                    <div class="text-3xl font-extrabold text-emerald-500">{{ $resolvedTickets }}</div>
                    <div class="mt-2 text-xs text-slate-400">Sudah selesai ditangani</div>
                </div>
            </div>

            {{-- Filter + Search --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-5 mb-6">
                <div class="flex flex-col gap-4">
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('tickets.my', array_merge(request()->except('page', 'status'), ['status' => ''])) }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $status === '' ? 'bg-orange-500 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua
                        </a>

                        <a href="{{ route('tickets.my', array_merge(request()->except('page'), ['status' => 'open'])) }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $status === 'open' ? 'bg-amber-500 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Open
                        </a>

                        <a href="{{ route('tickets.my', array_merge(request()->except('page'), ['status' => 'in_progress'])) }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $status === 'in_progress' ? 'bg-sky-500 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            In Progress
                        </a>

                        <a href="{{ route('tickets.my', array_merge(request()->except('page'), ['status' => 'resolved'])) }}"
                            class="px-4 py-2 rounded-xl text-sm font-semibold transition {{ $status === 'resolved' ? 'bg-emerald-500 text-white shadow' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Resolved
                        </a>
                    </div>

                    <form method="GET" action="{{ route('tickets.my') }}" class="grid grid-cols-1 md:grid-cols-12 gap-3">
                        <div class="md:col-span-6">
                            <input type="text" name="search" value="{{ $search }}"
                                placeholder="Cari kode ticket, judul, deskripsi, cabang..."
                                class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                        </div>

                        <div class="md:col-span-3">
                            <select name="status"
                                class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                <option value="">Semua Status</option>
                                <option value="open" {{ $status === 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="resolved" {{ $status === 'resolved' ? 'selected' : '' }}>Resolved</option>
                            </select>
                        </div>

                        <div class="md:col-span-3">
                            <select name="category"
                                class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                <option value="">Semua Kategori</option>
                                <option value="software" {{ $category === 'software' ? 'selected' : '' }}>Software</option>
                                <option value="hardware" {{ $category === 'hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="network&multimedia" {{ $category === 'network&multimedia' ? 'selected' : '' }}>Network & Multimedia</option>
                            </select>
                        </div>

                        <div class="md:col-span-12 flex flex-wrap gap-3">
                            <button type="submit"
                                class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 font-semibold shadow transition">
                                Cari / Filter
                            </button>

                            <a href="{{ route('tickets.my') }}"
                                class="inline-flex items-center justify-center rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 px-5 py-3 font-semibold transition">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Info Banner --}}
            <div class="mb-6 rounded-2xl bg-gradient-to-r from-sky-50 to-indigo-50 border border-sky-100 p-4 md:p-5">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h2 class="text-base md:text-lg font-bold text-slate-800">
                            Riwayat Ticket Anda
                        </h2>
                        <p class="text-sm text-slate-500 mt-1">
                            Klik tombol <span class="font-semibold text-blue-600">Detail</span> untuk melihat informasi lengkap ticket.
                        </p>
                    </div>
                    <div class="text-xs md:text-sm text-slate-500">
                        Menampilkan <span class="font-bold text-slate-700">{{ $tickets->count() }}</span> dari <span class="font-bold text-slate-700">{{ $tickets->total() }}</span> data
                    </div>
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden lg:block bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">Daftar Ticket</h3>
                        <p class="text-sm text-slate-500">Monitoring status ticket Anda</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-6 py-4 text-left font-bold">Kode</th>
                                <th class="px-6 py-4 text-left font-bold">Judul</th>
                                <th class="px-6 py-4 text-left font-bold">Cabang</th>
                                <th class="px-6 py-4 text-left font-bold">Kategori</th>
                                <th class="px-6 py-4 text-left font-bold">Priority</th>
                                <th class="px-6 py-4 text-left font-bold">Status</th>
                                <th class="px-6 py-4 text-left font-bold">Dikerjakan Oleh</th>
                                <th class="px-6 py-4 text-left font-bold">Tanggal</th>
                                <th class="px-6 py-4 text-center font-bold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tickets as $ticket)
                                @php $code = ticketCode($ticket); @endphp
                                <tr class="hover:bg-orange-50/40 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-blue-700">{{ $code }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-semibold text-slate-800">{{ $ticket->title }}</div>
                                        <div class="text-xs text-slate-400 mt-1">
                                            {{ \Illuminate\Support\Str::limit($ticket->description, 55) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">{{ $ticket->cabang }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span>{{ categoryIcon($ticket->category) }}</span>
                                            <span>{{ categoryLabel($ticket->category) }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ priorityBadgeClass($ticket->priority) }}">
                                            {{ $ticket->priority }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold capitalize {{ statusBadgeClass($ticket->status) }}">
                                            {{ str_replace('_', ' ', $ticket->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        {{ $ticket->takenByUser?->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $ticket->created_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('tickets.my.detail', $ticket->id) }}"
                                                class="inline-flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 font-semibold text-sm transition shadow-sm">
                                                Detail
                                            </a>
                                    
                                            @if ($ticket->status === 'open')
                                                <a href="{{ route('tickets.my.edit', $ticket->id) }}"
                                                    class="inline-flex items-center justify-center rounded-xl bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 font-semibold text-sm transition shadow-sm">
                                                    Edit
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-6 py-16 text-center">
                                        <div class="max-w-md mx-auto">
                                            <div class="text-5xl mb-3">📭</div>
                                            <h3 class="text-lg font-bold text-slate-700">Belum ada ticket</h3>
                                            <p class="text-sm text-slate-500 mt-1">
                                                Anda belum pernah membuat ticket atau tidak ada data yang cocok dengan filter.
                                            </p>
                                            <a href="{{ route('welcome', ['open_ticket' => 1]) }}"
                                                class="mt-5 inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 font-semibold transition shadow">
                                                + Buat Ticket Pertama
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="px-6 py-4 border-t border-slate-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>

            {{-- Mobile / Tablet Cards --}}
            <div class="lg:hidden space-y-4">
                @forelse ($tickets as $ticket)
                    @php $code = ticketCode($ticket); @endphp
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-extrabold text-blue-700">{{ $code }}</div>
                                    <h3 class="text-base font-bold text-slate-800 mt-1">{{ $ticket->title }}</h3>
                                    <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                                        <span>{{ categoryIcon($ticket->category) }}</span>
                                        <span>{{ $ticket->cabang }} • {{ categoryLabel($ticket->category) }}</span>
                                    </p>
                                </div>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold capitalize {{ statusBadgeClass($ticket->status) }}">
                                    {{ str_replace('_', ' ', $ticket->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Priority</span>
                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ priorityBadgeClass($ticket->priority) }}">
                                    {{ $ticket->priority }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Dikerjakan Oleh</span>
                                <span class="text-sm font-medium text-slate-700">{{ $ticket->takenByUser?->name ?? '-' }}</span>
                            </div>

                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Tanggal</span>
                                <span class="text-sm font-medium text-slate-700">{{ $ticket->created_at->format('d/m/Y H:i') }}</span>
                            </div>

                            <div class="pt-2">
                                <a href="{{ route('tickets.my.detail', $ticket->id) }}"
                                    class="w-full inline-flex items-center justify-center rounded-xl bg-blue-600 hover:bg-blue-700 text-white px-4 py-3 font-semibold transition">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 text-center">
                        <div class="text-5xl mb-3">📭</div>
                        <h3 class="text-lg font-bold text-slate-700">Belum ada ticket</h3>
                        <p class="text-sm text-slate-500 mt-1">
                            Anda belum pernah membuat ticket atau tidak ada data yang cocok dengan filter.
                        </p>
                        <a href="{{ route('welcome', ['open_ticket' => 1]) }}"
                            class="mt-5 inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 font-semibold transition shadow">
                            + Buat Ticket Pertama
                        </a>
                    </div>
                @endforelse

                @if ($tickets->hasPages())
                    <div class="pt-2">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

</body>
</html>
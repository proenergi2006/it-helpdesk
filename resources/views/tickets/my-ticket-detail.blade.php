<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Tracking Ticket</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    @php
        $cat = strtoupper(substr($ticket->category, 0, 1));
        $klas = strtoupper(substr($ticket->klasifikasi, 0, 1));
        $code = $cat . $klas . str_pad($ticket->id, 3, '0', STR_PAD_LEFT);

        $status = $ticket->status ?? 'open';

        $steps = [
            [
                'key' => 'created',
                'title' => 'Ticket Dibuat',
                'desc' => 'Ticket berhasil dibuat oleh user',
                'time' => $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            ],
            [
                'key' => 'open',
                'title' => 'Menunggu Penanganan',
                'desc' => 'Ticket sudah masuk ke antrian helpdesk',
                'time' => $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-',
            ],
            [
                'key' => 'in_progress',
                'title' => 'Sedang Dikerjakan',
                'desc' => 'Ticket sedang ditangani oleh tim IT',
                'time' => $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : '-',
            ],
            [
                'key' => 'resolved',
                'title' => 'Selesai',
                'desc' => 'Penanganan ticket telah selesai',
                'time' => $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : '-',
            ],
        ];

        $currentStepIndex = match ($status) {
            'open' => 1,
            'in_progress' => 2,
            'resolved' => 3,
            default => 1,
        };

        $priorityClass = match($ticket->priority) {
            'Low' => 'bg-emerald-500 text-white',
            'Medium' => 'bg-amber-500 text-white',
            'Critical' => 'bg-rose-500 text-white',
            default => 'bg-slate-400 text-white',
        };

        $statusClass = match($status) {
            'open' => 'bg-amber-100 text-amber-700 ring-1 ring-amber-200',
            'in_progress' => 'bg-sky-100 text-sky-700 ring-1 ring-sky-200',
            'resolved' => 'bg-emerald-100 text-emerald-700 ring-1 ring-emerald-200',
            default => 'bg-slate-100 text-slate-600 ring-1 ring-slate-200',
        };

        $categoryLabel = match($ticket->category) {
            'software' => 'Software',
            'hardware' => 'Hardware',
            'network&multimedia' => 'Network & Multimedia',
            default => ucfirst($ticket->category),
        };

        $categoryIcon = match($ticket->category) {
            'software' => '💻',
            'hardware' => '🖥️',
            'network&multimedia' => '📡',
            default => '📝',
        };

        $progressWidth = match($currentStepIndex) {
            0 => '8%',
            1 => '34%',
            2 => '67%',
            3 => '100%',
            default => '34%',
        };
    @endphp

    <div class="min-h-screen">
        {{-- Header --}}
        <div class="bg-gradient-to-r from-orange-500 via-orange-400 to-amber-400 shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-white/90 backdrop-blur flex items-center justify-center shadow">
                            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Logo Pro Energi" class="h-10 w-auto object-contain">
                        </div>
                        <div class="text-white">
                            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">
                                Detail Tracking Ticket
                            </h1>
                            <p class="text-white/90 text-sm md:text-base">
                                Pantau status dan progres ticket Anda secara detail
                            </p>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('tickets.my') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-700 hover:bg-slate-800 text-white px-5 py-3 font-semibold shadow transition">
                            ← Kembali
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

            {{-- Alert --}}
            @if (session('success'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-emerald-700">
                    <div class="font-semibold">Berhasil</div>
                    <div class="text-sm mt-1">{{ session('success') }}</div>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-rose-700">
                    <div class="font-semibold">Terjadi kesalahan</div>
                    <ul class="mt-2 text-sm list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Top Summary --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">
                <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                        <div>
                            <div class="text-sm text-slate-500 mb-2">Nomor Ticket</div>
                            <h2 class="text-3xl font-extrabold text-blue-700">{{ $code }}</h2>
                            <p class="text-slate-500 mt-2">{{ $ticket->title }}</p>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-bold {{ $priorityClass }}">
                                Priority: {{ $ticket->priority }}
                            </span>
                            <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-bold capitalize {{ $statusClass }}">
                                {{ str_replace('_', ' ', $ticket->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-500">Cabang</div>
                            <div class="font-bold text-slate-800 mt-1">{{ $ticket->cabang }}</div>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-500">Kategori</div>
                            <div class="font-bold text-slate-800 mt-1 flex items-center gap-2">
                                <span>{{ $categoryIcon }}</span>
                                <span>{{ $categoryLabel }}</span>
                            </div>
                        </div>

                        <div class="bg-slate-50 rounded-2xl p-4 border border-slate-100">
                            <div class="text-xs text-slate-500">Klasifikasi</div>
                            <div class="font-bold text-slate-800 mt-1">{{ $ticket->klasifikasi }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                    <div class="text-sm text-slate-500 mb-2">Dikerjakan Oleh</div>
                    <div class="text-xl font-extrabold text-slate-800">
                        {{ $ticket->takenByUser?->name ?? '-' }}
                    </div>

                    <div class="mt-6 space-y-4">
                        <div>
                            <div class="text-xs text-slate-500">Nama Pelapor</div>
                            <div class="font-semibold text-slate-800 mt-1">{{ $ticket->nama }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500">Email</div>
                            <div class="font-semibold text-slate-800 mt-1 break-all">{{ $ticket->email }}</div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500">Tanggal Dibuat</div>
                            <div class="font-semibold text-slate-800 mt-1">
                                {{ $ticket->created_at ? $ticket->created_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>

                        <div>
                            <div class="text-xs text-slate-500">Update Terakhir</div>
                            <div class="font-semibold text-slate-800 mt-1">
                                {{ $ticket->updated_at ? $ticket->updated_at->format('d/m/Y H:i') : '-' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Timeline Tracking --}}
            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-8 mb-8">
                <div class="mb-6">
                    <h3 class="text-xl font-bold text-slate-800">Tracking Progress</h3>
                    <p class="text-sm text-slate-500 mt-1">
                        Status perjalanan ticket Anda dari awal sampai selesai
                    </p>
                </div>

                {{-- Desktop timeline --}}
                <div class="hidden lg:block">
                    <div class="relative">
                        <div class="absolute top-7 left-0 right-0 h-1 bg-slate-200 rounded-full"></div>
                        <div class="absolute top-7 left-0 h-1 bg-emerald-500 rounded-full transition-all"
                            style="width: {{ $progressWidth }};">
                        </div>

                        <div class="relative grid grid-cols-4 gap-4">
                            @foreach ($steps as $index => $step)
                                @php
                                    $isDone = $index <= $currentStepIndex;
                                    $isCurrent = $index === $currentStepIndex;
                                @endphp

                                <div class="text-center">
                                    <div class="mx-auto relative z-10 h-14 w-14 rounded-full flex items-center justify-center border-4
                                        {{ $isDone ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-slate-300 text-slate-400' }}
                                        {{ $isCurrent ? 'ring-4 ring-emerald-100' : '' }}">
                                        @if($index < $currentStepIndex)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @elseif($index === 0)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z" />
                                            </svg>
                                        @elseif($index === 1)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M12 3C7.029 3 3 7.029 3 12s4.029 9 9 9 9-4.029 9-9-4.029-9-9-9z" />
                                            </svg>
                                        @elseif($index === 2)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.868v4.264a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @endif
                                    </div>

                                    <div class="mt-4">
                                        <div class="font-bold {{ $isDone ? 'text-emerald-600' : 'text-slate-500' }}">
                                            {{ $step['title'] }}
                                        </div>
                                        <div class="text-xs text-slate-500 mt-1">
                                            {{ $step['desc'] }}
                                        </div>
                                        <div class="text-xs text-slate-400 mt-2">
                                            {{ $index <= $currentStepIndex ? $step['time'] : '-' }}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Mobile timeline --}}
                <div class="lg:hidden">
                    <div class="space-y-5">
                        @foreach ($steps as $index => $step)
                            @php
                                $isDone = $index <= $currentStepIndex;
                                $isCurrent = $index === $currentStepIndex;
                            @endphp

                            <div class="flex gap-4">
                                <div class="flex flex-col items-center">
                                    <div class="h-12 w-12 rounded-full flex items-center justify-center border-4
                                        {{ $isDone ? 'bg-emerald-500 border-emerald-500 text-white' : 'bg-white border-slate-300 text-slate-400' }}
                                        {{ $isCurrent ? 'ring-4 ring-emerald-100' : '' }}">
                                        @if($index < $currentStepIndex)
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="font-bold">{{ $index + 1 }}</span>
                                        @endif
                                    </div>
                                    @if (!$loop->last)
                                        <div class="w-1 flex-1 mt-2 rounded-full {{ $isDone ? 'bg-emerald-500' : 'bg-slate-200' }}" style="min-height: 34px;"></div>
                                    @endif
                                </div>

                                <div class="pb-4">
                                    <div class="font-bold {{ $isDone ? 'text-emerald-600' : 'text-slate-500' }}">
                                        {{ $step['title'] }}
                                    </div>
                                    <div class="text-sm text-slate-500 mt-1">{{ $step['desc'] }}</div>
                                    <div class="text-xs text-slate-400 mt-2">
                                        {{ $index <= $currentStepIndex ? $step['time'] : '-' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Detail Information --}}
            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-5">Informasi Ticket</h3>

                        <div class="space-y-5">
                            <div>
                                <div class="text-sm text-slate-500 mb-1">Judul Ticket</div>
                                <div class="text-lg font-bold text-slate-800">{{ $ticket->title }}</div>
                            </div>

                            <div>
                                <div class="text-sm text-slate-500 mb-1">Deskripsi Masalah</div>
                                <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 whitespace-pre-line text-slate-700 leading-relaxed">
                                    {{ $ticket->description }}
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Feedback Form --}}
                    @if ($ticket->status === 'resolved' && !$ticket->feedback_at)
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-2">Berikan Penilaian</h3>
                            <p class="text-sm text-slate-500 mb-5">
                                Bantu kami meningkatkan layanan helpdesk dengan memberikan rating dan komentar.
                            </p>

                            <form method="POST" action="{{ route('tickets.my.feedback', $ticket->id) }}" class="space-y-5">
                                @csrf

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Rating</label>
                                    <select name="rating" class="w-full rounded-xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                        <option value="">Pilih Rating</option>
                                        <option value="5" {{ old('rating') == '5' ? 'selected' : '' }}>5 - Sangat Puas</option>
                                        <option value="4" {{ old('rating') == '4' ? 'selected' : '' }}>4 - Puas</option>
                                        <option value="3" {{ old('rating') == '3' ? 'selected' : '' }}>3 - Cukup</option>
                                        <option value="2" {{ old('rating') == '2' ? 'selected' : '' }}>2 - Tidak Puas</option>
                                        <option value="1" {{ old('rating') == '1' ? 'selected' : '' }}>1 - Sangat Tidak Puas</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Apakah masalah sudah selesai?</label>
                                    <select name="is_confirmed_resolved" class="w-full rounded-xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                        <option value="">Pilih Jawaban</option>
                                        <option value="1" {{ old('is_confirmed_resolved') === '1' ? 'selected' : '' }}>Ya, sudah selesai</option>
                                        <option value="0" {{ old('is_confirmed_resolved') === '0' ? 'selected' : '' }}>Belum, masih perlu tindak lanjut</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Komentar</label>
                                    <textarea name="feedback_comment" rows="4"
                                        class="w-full rounded-xl border-slate-300 focus:border-orange-400 focus:ring-orange-400"
                                        placeholder="Tuliskan pengalaman Anda atau catatan tambahan...">{{ old('feedback_comment') }}</textarea>
                                </div>

                                <div class="pt-2">
                                    <button type="submit"
                                        class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 font-semibold transition shadow">
                                        Kirim Feedback
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Feedback Result --}}
                    @if ($ticket->feedback_at)
                        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                            <h3 class="text-xl font-bold text-slate-800 mb-4">Feedback Anda</h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                <div>
                                    <div class="text-slate-500">Rating</div>
                                    <div class="font-bold text-slate-800 mt-1">
                                        {{ $ticket->rating }}/5
                                    </div>
                                </div>

                                <div>
                                    <div class="text-slate-500">Konfirmasi Penyelesaian</div>
                                    <div class="font-bold text-slate-800 mt-1">
                                        {{ $ticket->is_confirmed_resolved ? 'Sudah selesai' : 'Belum selesai' }}
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="text-slate-500">Komentar</div>
                                    <div class="mt-1 rounded-2xl bg-slate-50 border border-slate-100 p-4 whitespace-pre-line text-slate-700">
                                        {{ $ticket->feedback_comment ?: '-' }}
                                    </div>
                                </div>

                                <div class="md:col-span-2">
                                    <div class="text-slate-500">Dikirim pada</div>
                                    <div class="font-semibold text-slate-800 mt-1">
                                        {{ $ticket->feedback_at ? \Carbon\Carbon::parse($ticket->feedback_at)->format('d/m/Y H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-5">Ringkasan</h3>

                        <div class="space-y-4">
                            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                                <div class="text-xs text-slate-500">Status Saat Ini</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-bold capitalize {{ $statusClass }}">
                                        {{ str_replace('_', ' ', $ticket->status) }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                                <div class="text-xs text-slate-500">Priority</div>
                                <div class="mt-2">
                                    <span class="inline-flex items-center rounded-full px-4 py-2 text-sm font-bold {{ $priorityClass }}">
                                        {{ $ticket->priority }}
                                    </span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                                <div class="text-xs text-slate-500">Nomor Ticket</div>
                                <div class="mt-2 text-lg font-bold text-blue-700">{{ $code }}</div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-100 p-4">
                                <div class="text-xs text-slate-500">Kategori</div>
                                <div class="mt-2 text-lg font-bold text-slate-800 flex items-center gap-2">
                                    <span>{{ $categoryIcon }}</span>
                                    <span>{{ $categoryLabel }}</span>
                                </div>
                            </div>

                            <div class="rounded-2xl bg-orange-50 border border-orange-100 p-4">
                                <div class="text-sm font-semibold text-orange-700">Informasi</div>
                                <p class="text-sm text-orange-600 mt-2 leading-relaxed">
                                    Tracking ini menampilkan progres ticket Anda secara visual. Status akan berubah sesuai proses penanganan oleh tim IT.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Optional attachment placeholder --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-xl font-bold text-slate-800 mb-3">Catatan</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">
                            Jika ticket sudah selesai, Anda dapat memberikan rating dan komentar sebagai masukan untuk peningkatan layanan helpdesk.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </div>

</body>
</html>
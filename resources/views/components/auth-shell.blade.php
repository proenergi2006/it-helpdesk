@props(['title', 'heading', 'subtitle' => null, 'errorTitle' => null])
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }} - Service Management Pro Energi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
    <style>
        .login-bg { background-image: linear-gradient(105deg, rgba(8,15,32,.92) 0%, rgba(8,15,32,.72) 42%, rgba(8,15,32,.35) 100%), url('{{ asset('images/login-bg.jpg') }}'); background-size: cover; background-position: center; }
        .glow { background: radial-gradient(circle, rgba(249,115,22,.35), transparent 65%); }
        @keyframes rise { from { opacity: 0; transform: translateY(14px); } to { opacity: 1; transform: none; } }
        .rise { animation: rise .6s ease both; }
        .rise-2 { animation-delay: .12s; }
        .rise-3 { animation-delay: .24s; }
        @media (prefers-reduced-motion: reduce) { .rise { animation: none; } }
    </style>
</head>
<body class="min-h-screen text-slate-800 antialiased">

<div class="login-bg relative min-h-screen flex flex-col overflow-hidden">
    <div class="glow pointer-events-none absolute -left-40 top-1/3 h-[34rem] w-[34rem]"></div>

    <main class="relative z-10 flex-1 flex items-center px-4 sm:px-8 lg:px-16 py-10">
        <div class="mx-auto w-full max-w-7xl grid grid-cols-1 lg:grid-cols-[1.15fr_.85fr] gap-10 lg:gap-16 items-center">

            {{-- Branding --}}
            <section class="hidden lg:block text-white rise">
                <div class="flex items-center gap-4 mb-10">
                    <span class="h-px w-10 bg-orange-400"></span>
                    <div class="text-sm uppercase tracking-[.22em] text-orange-300 font-semibold">PT Pro Energi</div>
                </div>

                <h1 class="text-5xl xl:text-6xl font-extrabold leading-[1.05] tracking-tight">
                    Service<br><span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-amber-300">Management</span>
                </h1>
                <p class="mt-5 max-w-md text-lg text-slate-300 leading-relaxed">
                    Satu pintu untuk layanan IT, aset perusahaan, dan permintaan perubahan. Cepat dilaporkan, jelas progresnya.
                </p>

                <ul class="mt-10 space-y-4 max-w-md rise rise-2">
                    <li class="flex items-start gap-4 rounded-2xl bg-white/[.07] backdrop-blur-sm border border-white/10 p-4">
                        <span class="mt-0.5 h-10 w-10 shrink-0 rounded-xl bg-orange-500/90 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 0 1 0 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 0 1 0-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375Z"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Tiket layanan</div>
                            <div class="text-sm text-slate-300">Laporkan kendala, pantau statusnya dari open sampai selesai.</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 rounded-2xl bg-white/[.07] backdrop-blur-sm border border-white/10 p-4">
                        <span class="mt-0.5 h-10 w-10 shrink-0 rounded-xl bg-sky-500/90 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5ZM13.5 14.625v4.5M17.25 14.625v4.5M13.5 16.875h3.75"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Aset berbasis QR</div>
                            <div class="text-sm text-slate-300">Scan kode di perangkat untuk melihat info dan melapor masalah.</div>
                        </div>
                    </li>
                    <li class="flex items-start gap-4 rounded-2xl bg-white/[.07] backdrop-blur-sm border border-white/10 p-4">
                        <span class="mt-0.5 h-10 w-10 shrink-0 rounded-xl bg-emerald-500/90 flex items-center justify-center">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        </span>
                        <div>
                            <div class="font-semibold">Persetujuan terukur</div>
                            <div class="text-sm text-slate-300">Permintaan perubahan disetujui lewat web atau email, SLA berjalan jelas.</div>
                        </div>
                    </li>
                </ul>
            </section>

            {{-- Form --}}
            <section class="rise rise-3 w-full max-w-md mx-auto lg:mx-0 lg:ml-auto">
                <div class="lg:hidden text-center text-white mb-6">
                    <div class="text-3xl font-extrabold tracking-tight">Service <span class="text-orange-400">Management</span></div>
                    <div class="text-sm text-slate-300 mt-1">PT Pro Energi</div>
                </div>

                <div class="rounded-3xl bg-white shadow-2xl shadow-black/40 p-7 sm:p-9">
                    <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-12 w-auto mx-auto mb-6 object-contain">

                    <div class="mb-7">
                        <h2 class="text-2xl font-extrabold text-slate-900">{{ $heading }}</h2>
                        @if ($subtitle)<p class="text-sm text-slate-500 mt-1">{{ $subtitle }}</p>@endif
                    </div>

                    @if (session('status'))
                        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mb-5 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                            <div class="font-semibold mb-1">{{ $errorTitle ?: 'Terjadi kesalahan' }}</div>
                            <ul class="list-disc pl-5 space-y-0.5">
                                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                            </ul>
                        </div>
                    @endif

                    {{ $slot }}

                    @isset($footer)
                        <div class="mt-6 pt-5 border-t border-slate-200 text-center text-sm text-slate-500">{{ $footer }}</div>
                    @endisset
                </div>
            </section>
        </div>
    </main>

    <footer class="relative z-10 px-4 pb-6 text-center text-xs text-slate-400">
        © {{ date('Y') }} <span class="font-semibold text-slate-200">PT Pro Energi</span> · Service Management · Internal Support Platform
    </footer>
</div>

@vite('resources/js/app.js')
</body>
</html>

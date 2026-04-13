<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Helpdesk Pro Energi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-gradient-to-br from-slate-100 via-orange-50 to-amber-50 text-slate-800">

    <div class="min-h-screen flex flex-col">
        <div class="flex-1 flex items-center justify-center px-4 py-10">
            <div class="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-2 bg-white/90 backdrop-blur rounded-3xl shadow-2xl overflow-hidden border border-white/60">

                {{-- Left Branding Panel --}}
                <div class="hidden lg:flex relative bg-gradient-to-br from-orange-500 via-orange-400 to-amber-400 p-10 text-white">
                    <div class="absolute inset-0 opacity-10">
                        <div class="absolute top-10 left-10 w-40 h-40 bg-white rounded-full blur-3xl"></div>
                        <div class="absolute bottom-10 right-10 w-52 h-52 bg-white rounded-full blur-3xl"></div>
                    </div>

                    <div class="relative z-10 flex flex-col justify-between w-full">
                        <div>
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-16 w-16 rounded-2xl bg-white/90 flex items-center justify-center shadow-lg">
                                    <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-10 w-auto object-contain">
                                </div>
                                <div>
                                    <div class="text-3xl font-extrabold tracking-tight">Helpdesk Pro Energi</div>
                                    <div class="text-white/85 text-sm">Sistem layanan bantuan internal perusahaan</div>
                                </div>
                            </div>

                            <h1 class="text-4xl font-extrabold leading-tight mb-4">
                                Buat akun Anda
                            </h1>
                            <p class="text-white/90 text-base leading-relaxed max-w-md">
                                Daftarkan akun untuk mengirim ticket, memantau progres penanganan, dan melihat riwayat bantuan Anda kapan saja.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 gap-4 mt-10">
                            <div class="rounded-2xl bg-white/15 backdrop-blur p-4 border border-white/20">
                                <div class="font-semibold mb-1">Akses Personal</div>
                                <div class="text-sm text-white/85">Setiap user dapat memantau ticket miliknya sendiri dengan aman.</div>
                            </div>
                            <div class="rounded-2xl bg-white/15 backdrop-blur p-4 border border-white/20">
                                <div class="font-semibold mb-1">Tracking Progres</div>
                                <div class="text-sm text-white/85">Lihat status ticket dari awal sampai selesai.</div>
                            </div>
                            <div class="rounded-2xl bg-white/15 backdrop-blur p-4 border border-white/20">
                                <div class="font-semibold mb-1">Feedback Layanan</div>
                                <div class="text-sm text-white/85">Berikan penilaian setelah ticket terselesaikan.</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Form Panel --}}
                <div class="p-6 sm:p-8 lg:p-10 flex items-center">
                    <div class="w-full max-w-md mx-auto">
                        <div class="lg:hidden text-center mb-8">
                            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-14 mx-auto mb-4">
                            <h1 class="text-2xl font-extrabold text-slate-800">Daftar Akun Helpdesk</h1>
                            <p class="text-sm text-slate-500 mt-2">
                                Buat akun untuk mengirim dan memantau ticket.
                            </p>
                        </div>

                        <div class="hidden lg:block mb-8">
                            <h2 class="text-3xl font-extrabold text-slate-800">Daftar</h2>
                            <p class="text-slate-500 mt-2">
                                Lengkapi data di bawah ini untuk membuat akun baru.
                            </p>
                        </div>

                        @if ($errors->any())
                            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                <div class="font-semibold mb-1">Pendaftaran gagal</div>
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="name" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Nama Lengkap
                                </label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus
                                    class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3.5 text-slate-800 shadow-sm focus:border-orange-400 focus:ring-orange-400"
                                    placeholder="Masukkan nama lengkap">
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Email
                                </label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3.5 text-slate-800 shadow-sm focus:border-orange-400 focus:ring-orange-400"
                                    placeholder="contoh: nama@proenergi.co.id">
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Password
                                </label>
                                <input id="password" name="password" type="password" required
                                    class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3.5 text-slate-800 shadow-sm focus:border-orange-400 focus:ring-orange-400"
                                    placeholder="Minimal 8 karakter">
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-2">
                                    Konfirmasi Password
                                </label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required
                                    class="w-full rounded-2xl border-slate-300 bg-white px-4 py-3.5 text-slate-800 shadow-sm focus:border-orange-400 focus:ring-orange-400"
                                    placeholder="Ulangi password">
                            </div>

                            <button type="submit"
                                class="w-full inline-flex items-center justify-center rounded-2xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 py-3.5 font-bold text-base shadow-lg transition">
                                Daftar
                            </button>

                            <div class="text-center text-sm text-slate-500 pt-2">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700">
                                    Masuk
                                </a>
                            </div>
                        </form>

                        <div class="mt-8 pt-6 border-t border-slate-200">
                            <div class="flex items-start gap-3 rounded-2xl bg-slate-50 p-4 border border-slate-100">
                                <div class="h-10 w-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-lg">
                                    🔐
                                </div>
                                <div>
                                    <div class="font-semibold text-slate-800">Keamanan Akun</div>
                                    <div class="text-sm text-slate-500 mt-1">
                                        Gunakan email aktif dan password yang mudah Anda ingat namun tetap aman.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <footer class="px-4 pb-6">
            <div class="max-w-6xl mx-auto flex flex-col md:flex-row items-center justify-between gap-3 text-sm text-slate-500">
                <div>
                    © {{ date('Y') }} <span class="font-semibold text-slate-700">PT Pro Energi</span>. All rights reserved.
                </div>
                <div>
                    Helpdesk System · Internal Support Platform
                </div>
            </div>
        </footer>
    </div>

</body>
</html>
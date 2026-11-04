<x-auth-shell title="Masuk" heading="Selamat datang" subtitle="Masuk dengan akun Anda untuk melanjutkan." error-title="Login gagal">
    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ show: false }">
        @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg></span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full rounded-xl border-slate-300 bg-slate-50 pl-11 pr-4 py-3 text-slate-800 focus:bg-white focus:border-orange-400 focus:ring-orange-400" placeholder="nama@proenergi.co.id">
                    </div>
                </div>

        <div>
            <div class="flex items-center justify-between mb-1.5">
                <label for="password" class="block text-sm font-semibold text-slate-700">Password</label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Lupa password?</a>
                @endif
            </div>
            <div class="relative">
                <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z"/></svg></span>
                <input id="password" name="password" :type="show ? 'text' : 'password'" required autocomplete="current-password" class="w-full rounded-xl border-slate-300 bg-slate-50 pl-11 pr-16 py-3 text-slate-800 focus:bg-white focus:border-orange-400 focus:ring-orange-400" placeholder="Masukkan password">
                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-4 text-xs font-semibold text-slate-500 hover:text-slate-800" x-text="show ? 'Sembunyi' : 'Lihat'">Lihat</button>
            </div>
        </div>

        <label for="remember_me" class="inline-flex items-center gap-2 text-sm text-slate-600">
            <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-300 text-orange-500 focus:ring-orange-400">
            <span>Ingat saya</span>
        </label>

        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 py-3.5 font-bold shadow-lg shadow-orange-500/30 transition active:scale-[.99]">Masuk</button>
    </form>

    <x-slot name="footer">Belum punya akun? <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-700">Daftar sekarang</a></x-slot>
</x-auth-shell>

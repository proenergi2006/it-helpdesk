<x-auth-shell title="Lupa Password" heading="Lupa password?" subtitle="Masukkan email Anda. Kami kirimkan link untuk mengatur ulang password." error-title="Permintaan gagal">
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf

                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1.5">Email</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400"><svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg></span>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-xl border-slate-300 bg-slate-50 pl-11 pr-4 py-3 text-slate-800 focus:bg-white focus:border-orange-400 focus:ring-orange-400" placeholder="nama@proenergi.co.id">
                    </div>
                </div>

        <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white px-4 py-3.5 font-bold shadow-lg shadow-orange-500/30 transition active:scale-[.99]">Kirim Link Reset Password</button>
        <p class="text-xs text-slate-500 text-center">Belum menerima email? Periksa juga folder spam atau promosi.</p>
    </form>

    <x-slot name="footer">Sudah ingat password? <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-700">Kembali ke login</a></x-slot>
</x-auth-shell>

<x-guest-layout>
    <div class="min-h-screen bg-slate-950 text-slate-100">
        <!-- Background + grid -->
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -top-40 -left-40 h-96 w-96 rounded-full bg-cyan-500/20 blur-3xl"></div>
            <div class="absolute -bottom-40 -right-40 h-96 w-96 rounded-full bg-fuchsia-500/20 blur-3xl"></div>
            <div class="absolute inset-0 bg-[linear-gradient(to_right,rgba(255,255,255,0.06)_1px,transparent_1px),linear-gradient(to_bottom,rgba(255,255,255,0.06)_1px,transparent_1px)] bg-[size:48px_48px] opacity-20"></div>

            <!-- Scanline effect -->
            <div class="absolute inset-0 opacity-[0.08] mix-blend-screen scanline"></div>
        </div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-10">
            <div class="grid w-full grid-cols-1 gap-8 lg:grid-cols-2">
                <!-- LEFT: Branding + Widgets -->
                <div class="flex flex-col justify-center">
                    @php
                        // Ubah ini sesuai brand kamu
                        $appName = config('app.name', 'IT Helpdesk');
                        $orgName = 'Internal Support Center';
                        // Dummy numbers (biar futuristik). Kalau mau real, nanti bisa ambil dari DB / cache.
                        $open = 18;
                        $pending = 7;
                        $breach = 2;
                        $avgResponse = '11m';
                    @endphp

                    <div class="inline-flex items-center gap-3">
                        <div class="grid h-12 w-12 place-items-center rounded-2xl bg-white/5 ring-1 ring-white/10">
                            <svg class="h-6 w-6 text-cyan-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 2l9 5v10l-9 5-9-5V7l9-5Z" stroke="currentColor" stroke-width="1.5"/>
                                <path d="M7 9.5h10M7 14.5h6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-sm text-slate-400">{{ $orgName }}</div>
                            <div class="text-xl font-semibold tracking-tight">
                                Helpdesk <span class="text-cyan-300">Portal</span>
                            </div>
                        </div>
                    </div>

                    <h1 class="mt-6 text-3xl font-semibold leading-tight tracking-tight lg:text-4xl">
                        Kelola tiket, SLA, dan eskalasi IT dalam satu dashboard.
                    </h1>

                    <p class="mt-3 max-w-xl text-slate-400">
                        Login untuk membuat tiket, memonitor status, assignment teknisi, hingga laporan performa support.
                    </p>

                    <!-- System/Status widgets -->
                    <div class="mt-6 grid max-w-xl grid-cols-1 gap-3 sm:grid-cols-3">
                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-400">System</div>
                            <div class="mt-1 inline-flex items-center gap-2 text-sm font-medium">
                                <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                                Online
                            </div>
                            <div class="mt-1 text-xs text-slate-500">API: OK • Queue: OK</div>
                        </div>

                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-400">Security</div>
                            <div class="mt-1 text-sm font-medium text-slate-200">Audit Logging</div>
                            <div class="mt-1 text-xs text-slate-500">MFA ready • IP tracked</div>
                        </div>

                        <div class="rounded-2xl bg-white/5 p-4 ring-1 ring-white/10">
                            <div class="text-xs text-slate-400">SLA</div>
                            <div class="mt-1 text-sm font-medium text-slate-200">Avg response</div>
                            <div class="mt-1 text-xs text-slate-500">{{ $avgResponse }} (last 24h)</div>
                        </div>
                    </div>

                    <!-- Ticket overview panel -->
                    <div class="mt-6 max-w-xl rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs text-slate-400">Ticket Overview</div>
                                <div class="text-lg font-semibold tracking-tight">Live Queue Snapshot</div>
                            </div>
                            <div class="rounded-2xl bg-slate-950/40 px-3 py-1 text-xs text-slate-400 ring-1 ring-white/10">
                                Updated: now
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-3 gap-3">
                            <div class="rounded-2xl bg-slate-950/40 p-4 ring-1 ring-white/10">
                                <div class="text-xs text-slate-400">Open</div>
                                <div class="mt-1 text-2xl font-semibold text-cyan-300">{{ $open }}</div>
                                <div class="mt-1 text-xs text-slate-500">Assigned / in progress</div>
                            </div>
                            <div class="rounded-2xl bg-slate-950/40 p-4 ring-1 ring-white/10">
                                <div class="text-xs text-slate-400">Pending</div>
                                <div class="mt-1 text-2xl font-semibold text-slate-200">{{ $pending }}</div>
                                <div class="mt-1 text-xs text-slate-500">Waiting user/vendor</div>
                            </div>
                            <div class="rounded-2xl bg-slate-950/40 p-4 ring-1 ring-white/10">
                                <div class="text-xs text-slate-400">SLA Breach</div>
                                <div class="mt-1 text-2xl font-semibold text-fuchsia-300">{{ $breach }}</div>
                                <div class="mt-1 text-xs text-slate-500">Need escalation</div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2 text-xs text-slate-500">
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">INC</span>
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">SR</span>
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">Problem</span>
                            <span class="rounded-full bg-white/5 px-3 py-1 ring-1 ring-white/10">Change</span>
                        </div>
                    </div>

                    <div class="mt-6 text-xs text-slate-500">
                        Tip: gunakan email kantor. Aktivitas login terekam untuk audit & keamanan.
                    </div>
                </div>

                <!-- RIGHT: Login Card -->
                <div class="flex items-center justify-center lg:justify-end">
                    <div class="w-full max-w-md rounded-3xl bg-white/5 p-8 ring-1 ring-white/10 backdrop-blur">
                        <div class="mb-6">
                            <div class="text-sm text-slate-400">Authentication</div>
                            <div class="text-2xl font-semibold tracking-tight">Sign in</div>
                            <div class="mt-1 text-sm text-slate-400">
                                Masukkan kredensial untuk mengakses dashboard helpdesk.
                            </div>
                        </div>

                        <x-auth-session-status class="mb-4" :status="session('status')" />

                        <form method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <!-- Email -->
                            <div>
                                <x-input-label for="email" :value="__('Email')" class="text-slate-200" />
                                <x-text-input
                                    id="email"
                                    class="mt-1 block w-full bg-slate-950/40 border-white/10 text-slate-100 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                                    type="email"
                                    name="email"
                                    :value="old('email')"
                                    required
                                    autofocus
                                    autocomplete="username"
                                    placeholder="nama@perusahaan.com"
                                />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <!-- Password -->
                            <div>
                                <x-input-label for="password" :value="__('Password')" class="text-slate-200" />
                                <x-text-input
                                    id="password"
                                    class="mt-1 block w-full bg-slate-950/40 border-white/10 text-slate-100 placeholder:text-slate-500 focus:border-cyan-400 focus:ring-cyan-400"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="••••••••"
                                />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Remember -->
                            <div class="flex items-center justify-between">
                                <label for="remember_me" class="inline-flex items-center gap-2">
                                    <input
                                        id="remember_me"
                                        type="checkbox"
                                        class="rounded border-white/20 bg-slate-950/40 text-cyan-400 shadow-sm focus:ring-cyan-400"
                                        name="remember"
                                    >
                                    <span class="text-sm text-slate-300">{{ __('Remember me') }}</span>
                                </label>

                                {{--
                                <a class="text-sm text-cyan-300 hover:text-cyan-200" href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                                --}}
                            </div>

                            <div class="pt-2">
                                <x-primary-button class="w-full justify-center bg-gradient-to-r from-cyan-500 to-fuchsia-500 hover:from-cyan-400 hover:to-fuchsia-400 focus:ring-cyan-400">
                                    {{ __('Log in') }}
                                </x-primary-button>
                            </div>

                            <div class="mt-3 rounded-2xl bg-slate-950/40 p-3 text-xs text-slate-400 ring-1 ring-white/10">
                                <div class="flex items-center justify-between">
                                    <span>Security events</span>
                                    <span class="text-slate-500">Enabled</span>
                                </div>
                                <div class="mt-1 text-slate-500">
                                    Failed login, IP, dan device fingerprint akan dicatat.
                                </div>
                            </div>

                            <div class="text-center text-xs text-slate-500">
                                Dengan login, kamu menyetujui kebijakan akses & logging internal.
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Scanline CSS -->
        <style>
            .scanline {
                background: repeating-linear-gradient(
                    to bottom,
                    rgba(255,255,255,0.8) 0px,
                    rgba(255,255,255,0.8) 1px,
                    transparent 2px,
                    transparent 6px
                );
                animation: scan 8s linear infinite;
            }
            @keyframes scan {
                0% { transform: translateY(-20%); }
                100% { transform: translateY(20%); }
            }
        </style>
    </div>
</x-guest-layout>

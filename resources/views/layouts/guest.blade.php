<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    {{ $slot }}

    {{-- Footer --}}
    <footer class="mt-10">
        <div class="border-t border-white/10 bg-slate-950/70 backdrop-blur">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                    <div class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-cyan-500/20 to-fuchsia-500/20 ring-1 ring-white/10 flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-cyan-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.75h4.5m-4.5 0a2.25 2.25 0 00-2.25 2.25v1.5m9 0V6a2.25 2.25 0 00-2.25-2.25m-9 6.75h18m-18 0v8.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V10.5m-18 0l2.25-3h13.5L21 10.5" />
                            </svg>
                        </div>

                        <div>
                            <div class="font-semibold tracking-tight text-slate-100">
                                IT Department · Pro Energi
                            </div>
                            <div class="text-xs text-slate-400">
                                Internal System — Helpdesk · Documentation · Projects · MoM
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col md:items-end gap-1">
                        <div class="text-sm text-slate-300">
                            © 2026 <span class="font-medium text-slate-100">Pro Energi</span>. All rights reserved.
                        </div>
                        <div class="text-xs text-slate-500">
                            Powered by IT Department · {{ config('app.name', 'Helpdesk') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </footer>
</body>
</html>

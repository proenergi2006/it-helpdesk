<nav x-data="{ open: false }" class="sticky top-0 z-50">
    <!-- Futuristic top glow -->
    <div class="pointer-events-none absolute inset-x-0 -top-10 h-24 bg-gradient-to-r from-cyan-500/20 via-transparent to-fuchsia-500/20 blur-2xl"></div>

    <div class="bg-slate-950/80 backdrop-blur ring-1 ring-white/10 border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Left -->
                <div class="flex items-center gap-6">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <a href="{{ route('dashboard') }}" class="group inline-flex items-center gap-3">
                            <span class="grid h-10 w-10 place-items-center rounded-2xl bg-white/5 ring-1 ring-white/10 group-hover:ring-cyan-400/40 transition">
                                <img src="{{ asset('images/proenergi-logo.png') }}"
                                     alt="Pro Energi Logo"
                                     class="h-6 w-auto object-contain opacity-90 group-hover:opacity-100 transition">
                            </span>

                            <div class="hidden sm:block leading-tight">
                                <div class="text-xs text-slate-400">Internal Support</div reopening="1"/>
                                <div class="text-sm font-semibold text-slate-100">
                                    Helpdesk <span class="text-cyan-300">Portal</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links (Desktop) -->
                    <div class="hidden sm:flex items-center gap-2">
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')"
                            class="!border-0 !text-slate-300 hover:!text-white">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white/5 ring-1 ring-transparent hover:ring-white/10 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-cyan-300/90">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3v1.5M21 3v1.5M4.5 21h15M4.5 21V9a3 3 0 013-3h9a3 3 0 013 3v12M4.5 21h15" />
                                </svg>
                                <span>{{ __('Dashboard') }}</span>
                            </span>
                        </x-nav-link>

                        <x-nav-link :href="route('trend')" :active="request()->routeIs('trend')"
                            class="!border-0 !text-slate-300 hover:!text-white">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                         hover:bg-white/5 ring-1 ring-transparent
                                         hover:ring-fuchsia-400/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-fuchsia-300/90"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 17l6-6 4 4 8-8M21 21H3" />
                                </svg>
                                <span class="font-medium tracking-tight">Trend</span>
                            </span>
                        </x-nav-link>
                        
                        <x-nav-link :href="route('reports.index')" :active="request()->routeIs('reports.*')"
                            class="!border-0 !text-slate-300 hover:!text-white">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
                                         hover:bg-white/5 ring-1 ring-transparent
                                         hover:ring-cyan-400/30 transition">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    class="w-5 h-5 text-cyan-300/90"
                                    fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 3v18h18" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 17V9m4 8V5m4 12v-7" />
                                </svg>
                                <span class="font-medium tracking-tight">{{ __('Report') }}</span>
                            </span>
                        </x-nav-link>
                        
                        {{-- <x-nav-link :href="route('documents.index')" :active="request()->routeIs('documents.*')"
                            class="!border-0 !text-slate-300 hover:!text-white">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-white/5 ring-1 ring-transparent hover:ring-white/10 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-cyan-300/90"
                                    fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 14.25V6.75A2.25 2.25 0 0017.25 4.5H8.25A2.25 2.25 0 006 6.75v10.5A2.25 2.25 0 008.25 19.5h3.75" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 2.25v4.5a2.25 2.25 0 002.25 2.25h4.5" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16.5 19.5l3-3m0 0l-3-3m3 3H12" />
                                </svg>
                                <span>{{ __('Dokumentasi') }}</span>
                            </span>
                        </x-nav-link> --}}
                     {{-- Parent: Projects --}}
<div x-data="{ openProjects: false }" class="relative">
    <button
        type="button"
        @click="openProjects = !openProjects"
        @click.outside="openProjects = false"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
               !border-0 !text-slate-300 hover:!text-white
               hover:bg-white/5 ring-1 ring-transparent hover:ring-cyan-400/30 transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-cyan-300/90"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M7.5 14.25v2.25A2.25 2.25 0 009.75 18.75h4.5A2.25 2.25 0 0016.5 16.5v-2.25M7.5 9.75V7.5A2.25 2.25 0 019.75 5.25h4.5A2.25 2.25 0 0116.5 7.5v2.25M9 12h6"/>
        </svg>

        <span class="font-medium tracking-tight">Projects</span>

        <svg class="w-4 h-4 text-slate-400 transition"
             :class="openProjects ? 'rotate-180' : ''"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5"/>
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-cloak
        x-show="openProjects"
        x-transition
        class="absolute left-0 mt-2 w-56 rounded-xl
               bg-slate-950/90 backdrop-blur
               ring-1 ring-white/10 shadow-lg overflow-hidden z-50"
    >
        <a href="{{ route('projects.index') }}"
           class="flex items-center gap-2 px-4 py-3 text-sm text-slate-200 hover:bg-white/5">
            📁 <span>Project List</span>
        </a>

        <a href="{{ route('projects.board') }}"
           class="flex items-center gap-2 px-4 py-3 text-sm text-slate-200 hover:bg-white/5">
            📋 <span>Kanban Board</span>
        </a>
    </div>
</div>

                        {{-- Parent: Knowledge / Dokumentasi --}}
<div x-data="{ openDocs: false }" class="relative">
    <button
        type="button"
        @click="openDocs = !openDocs"
        @click.outside="openDocs = false"
        class="inline-flex items-center gap-2 px-3 py-2 rounded-xl
               !border-0 !text-slate-300 hover:!text-white
               hover:bg-white/5 ring-1 ring-transparent hover:ring-white/10 transition"
    >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-cyan-300/90"
            fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M19.5 14.25V6.75A2.25 2.25 0 0017.25 4.5H8.25A2.25 2.25 0 006 6.75v10.5A2.25 2.25 0 008.25 19.5h3.75" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 2.25v4.5a2.25 2.25 0 002.25 2.25h4.5" />
            <path stroke-linecap="round" stroke-linejoin="round"
                d="M16.5 19.5l3-3m0 0l-3-3m3 3H12" />
        </svg>

        <span class="font-medium tracking-tight">Knowledge</span>

        <svg class="w-4 h-4 text-slate-400 transition"
             :class="openDocs ? 'rotate-180' : ''"
             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>

    {{-- Dropdown --}}
    <div
        x-cloak
        x-show="openDocs"
        x-transition
        class="absolute left-0 mt-2 w-56 rounded-xl
               bg-slate-950/90 backdrop-blur
               ring-1 ring-white/10 shadow-lg overflow-hidden z-50"
    >
        <a href="{{ route('documents.index') }}"
           class="flex items-center gap-2 px-4 py-3 text-sm text-slate-200 hover:bg-white/5"
        >
            <span class="text-cyan-300">📚</span>
            <span>Dokumentasi</span>
        </a>

        <a href="{{ route('meetings.index') }}"
           class="flex items-center gap-2 px-4 py-3 text-sm text-slate-200 hover:bg-white/5"
        >
            <span class="text-cyan-300">📝</span>
            <span>Meeting MoM</span>
        </a>
    </div>
   
</div>

                        {{-- <x-nav-link :href="route('meetings.index')" :active="request()->routeIs('meetings.*')">
                            <span class="inline-flex items-center gap-2 px-3 py-2 rounded-xl hover:bg-gray-100 transition">
                                <span class="text-lg">📝</span>
                                <span>Meeting MoM</span>
                            </span>
                        </x-nav-link> --}}
                        
                        
                        
                    </div>
                </div>

                <!-- Right -->
                <div class="hidden sm:flex sm:items-center gap-3">
                    <!-- Small status pill (optional) -->
                    <div class="hidden md:inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1 text-xs text-slate-300 ring-1 ring-white/10">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        System Online
                    </div>

                    <!-- Settings Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center gap-2 rounded-2xl bg-white/5 px-3 py-2 text-sm font-medium text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 hover:bg-white/10 transition focus:outline-none">
                                <span class="grid h-8 w-8 place-items-center rounded-xl bg-slate-950/40 ring-1 ring-white/10">
                                    <svg class="h-4 w-4 text-cyan-300" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" stroke="currentColor" stroke-width="1.5"/>
                                        <path d="M20 21a8 8 0 1 0-16 0" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                    </svg>
                                </span>

                                <div class="text-left leading-tight">
                                    <div class="text-sm">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
                                </div>

                                <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <!-- NOTE: dropdown content bawaan Breeze cenderung putih. Kita override dengan wrapper -->
                            <div class="rounded-xl bg-slate-950 text-slate-200 ring-1 ring-white/10 overflow-hidden">
                                <div class="px-4 py-3 border-b border-white/10">
                                    <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                                    <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
                                </div>

                                <div class="py-1">
                                    <x-dropdown-link :href="route('profile.edit')"
                                        class="!text-slate-200 hover:!bg-white/5 hover:!text-white">
                                        {{ __('Profile') }}
                                    </x-dropdown-link>

                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <x-dropdown-link :href="route('logout')"
                                            class="!text-slate-200 hover:!bg-white/5 hover:!text-white"
                                            onclick="event.preventDefault(); this.closest('form').submit();">
                                            {{ __('Log Out') }}
                                        </x-dropdown-link>
                                    </form>
                                </div>
                            </div>
                        </x-slot>
                    </x-dropdown>
                </div>

                <!-- Hamburger (Mobile) -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-2xl bg-white/5 text-slate-200 ring-1 ring-white/10 hover:bg-white/10 hover:ring-cyan-400/30 transition focus:outline-none">
                        <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                            <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                            <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden"
                                stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

            </div>
        </div>

        <!-- Mobile menu -->
        <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
            <div class="px-4 pb-4 pt-2 space-y-2">
                <a href="{{ route('dashboard') }}"
                   class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                    {{ __('Dashboard') }}
                </a>
                <a href="{{ route('trend') }}"
                   class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                    Trend
                </a>
                <a href="{{ route('reports.index') }}"
                   class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                    📊 {{ __('Report') }}
                </a>

                <div class="mt-3 rounded-2xl bg-slate-950/50 p-4 ring-1 ring-white/10">
                    <div class="text-sm font-semibold">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>

                    <div class="mt-3 space-y-2">
                        <a href="{{ route('profile.edit') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            {{ __('Profile') }}
                        </a>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit"
                                class="w-full text-left rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                                {{ __('Log Out') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

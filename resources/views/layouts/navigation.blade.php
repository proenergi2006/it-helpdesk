<nav x-data="{ open: false }" class="sticky top-0 z-50">
    <!-- Futuristic top glow -->
    <div class="pointer-events-none absolute inset-x-0 -top-10 h-24 bg-gradient-to-r from-cyan-500/20 via-transparent to-fuchsia-500/20 blur-2xl"></div>

    <div class="bg-slate-950/80 backdrop-blur ring-1 ring-white/10 border-b border-white/10">
        <div class="w-full px-4 sm:px-6 lg:px-8">
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
                                <div class="text-xs text-slate-400">Internal Support</div>
                                <div class="text-sm font-semibold text-slate-100">
                                    Helpdesk <span class="text-cyan-300">Portal</span>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Navigation Links (Desktop) -->
                    <div class="hidden sm:flex items-center gap-1">
                        <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">Dashboard</a>

                        @if (Auth::check() && in_array(strtolower(Auth::user()->email), array_map('strtolower', config('helpdesk.approvers'))))
                            @php $pendingApprovals = \App\Models\Ticket::where('status', 'pending_approval')->count(); @endphp
                            <a href="{{ route('approvals.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('approvals.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}"><span class="inline-flex items-center">Approval @if ($pendingApprovals > 0)<span class="ml-1.5 inline-flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-bold leading-5 text-white">{{ $pendingApprovals }}</span>@endif</span></a>
                        @endif

                        @if (Auth::check() && Auth::user()->role === 'it')
                            <a href="{{ route('assets.dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('assets.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">Aset</a>
                        @endif

                        <a href="{{ route('trend') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('trend') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">Trend</a>

                        <div x-data="{ openReport: false }" class="relative">
                            <button type="button" @click="openReport = !openReport" @click.outside="openReport = false"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reports.*') || request()->routeIs('user-access-management.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                                <span>Report</span>
                                <svg class="w-4 h-4 text-slate-400 transition" :class="openReport ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-cloak x-show="openReport" x-transition
                                class="absolute left-0 mt-2 w-52 rounded-lg bg-slate-950 ring-1 ring-white/10 shadow-lg overflow-hidden z-50">
                                <a href="{{ route('reports.index') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Report Ticket</a>
                                <a href="{{ route('reports.feedback') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Report Feedback</a>
                                <a href="{{ route('reports.sla') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">SLA Report</a>
                                <a href="{{ route('user-access-management.index') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">User Management</a>
                            </div>
                        </div>

                        <div x-data="{ openProjects: false }" class="relative">
                            <button type="button" @click="openProjects = !openProjects" @click.outside="openProjects = false"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('projects.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                                <span>Projects</span>
                                <svg class="w-4 h-4 text-slate-400 transition" :class="openProjects ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-cloak x-show="openProjects" x-transition
                                class="absolute left-0 mt-2 w-52 rounded-lg bg-slate-950 ring-1 ring-white/10 shadow-lg overflow-hidden z-50">
                                <a href="{{ route('projects.index') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Project List</a>
                                <a href="{{ route('projects.board') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Kanban Board</a>
                            </div>
                        </div>

                        <div x-data="{ openDocs: false }" class="relative">
                            <button type="button" @click="openDocs = !openDocs" @click.outside="openDocs = false"
                                class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('documents.*') || request()->routeIs('meetings.*') ? 'bg-white/10 text-white' : 'text-slate-300 hover:text-white hover:bg-white/5' }}">
                                <span>Knowledge</span>
                                <svg class="w-4 h-4 text-slate-400 transition" :class="openDocs ? 'rotate-180' : ''"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>
                            <div x-cloak x-show="openDocs" x-transition
                                class="absolute left-0 mt-2 w-52 rounded-lg bg-slate-950 ring-1 ring-white/10 shadow-lg overflow-hidden z-50">
                                <a href="{{ route('documents.index') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Dokumentasi</a>
                                <a href="{{ route('meetings.index') }}"
                                   class="block px-4 py-2.5 text-sm text-slate-200 hover:bg-white/5">Meeting MoM</a>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Right -->
                <div class="hidden sm:flex sm:items-center gap-3">
                    <!-- Small status pill -->
                    <div class="hidden md:inline-flex items-center gap-2 rounded-full bg-white/5 px-3 py-1 text-xs text-slate-300 ring-1 ring-white/10">
                        <span class="h-2 w-2 rounded-full bg-emerald-400"></span>
                        System Online
                    </div>

                    @auth
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
                                        <div class="text-sm">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
                                    </div>

                                    <svg class="h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">
                                <div class="rounded-xl bg-slate-950 text-slate-200 ring-1 ring-white/10 overflow-hidden">
                                    <div class="px-4 py-3 border-b border-white/10">
                                        <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                                        <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>
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
                    @endauth

                    @guest
                        <a href="{{ route('login') }}"
                           class="inline-flex items-center gap-2 rounded-2xl bg-white/5 px-4 py-2 text-sm font-medium text-slate-200 ring-1 ring-white/10 hover:bg-white/10 hover:ring-cyan-400/30 transition">
                            Login
                        </a>
                    @endguest
                </div>

                <!-- Hamburger (Mobile) -->
                <div class="flex items-center sm:hidden">
                    <button @click="open = !open"
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
                    Dashboard
                </a>

                @if (Auth::check() && in_array(strtolower(Auth::user()->email), array_map('strtolower', config('helpdesk.approvers'))))
                            @php $pendingApprovals = \App\Models\Ticket::where('status', 'pending_approval')->count(); @endphp
                    <a href="{{ route('approvals.index') }}"
                       class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                        <span class="inline-flex items-center">Approval @if ($pendingApprovals > 0)<span class="ml-1.5 inline-flex min-w-5 items-center justify-center rounded-full bg-red-600 px-1.5 text-xs font-bold leading-5 text-white">{{ $pendingApprovals }}</span>@endif</span>
                    </a>
                @endif

                @if (Auth::check() && Auth::user()->role === 'it')
                    <a href="{{ route('assets.dashboard') }}"
                       class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                        Aset
                    </a>
                @endif

                <a href="{{ route('trend') }}"
                   class="block rounded-2xl bg-white/5 px-4 py-3 text-slate-200 ring-1 ring-white/10 hover:ring-cyan-400/30 transition">
                    Trend
                </a>

                {{-- Mobile Report Dropdown --}}
                <div x-data="{ openMobileReport: false }" class="rounded-2xl bg-white/5 ring-1 ring-white/10">
                    <button
                        type="button"
                        @click="openMobileReport = !openMobileReport"
                        class="w-full flex items-center justify-between px-4 py-3 text-slate-200"
                    >
                        <span class="flex items-center gap-2">
                            <span>Report</span>
                        </span>

                        <svg class="w-4 h-4 text-slate-400 transition"
                             :class="openMobileReport ? 'rotate-180' : ''"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="openMobileReport" x-transition class="px-3 pb-3 space-y-2">
                        <a href="{{ route('reports.index') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Report Ticket
                        </a>

                        <a href="{{ route('reports.feedback') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Report Feedback
                        </a>

                        <a href="{{ route('reports.sla') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            SLA Report
                        </a>

                        <a href="{{ route('user-access-management.index') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            User Management
                        </a>
                    </div>
                </div>

                {{-- Mobile Projects Dropdown --}}
                <div x-data="{ openMobileProjects: false }" class="rounded-2xl bg-white/5 ring-1 ring-white/10">
                    <button
                        type="button"
                        @click="openMobileProjects = !openMobileProjects"
                        class="w-full flex items-center justify-between px-4 py-3 text-slate-200"
                    >
                        <span class="flex items-center gap-2">
                            <span>Projects</span>
                        </span>

                        <svg class="w-4 h-4 text-slate-400 transition"
                             :class="openMobileProjects ? 'rotate-180' : ''"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="openMobileProjects" x-transition class="px-3 pb-3 space-y-2">
                        <a href="{{ route('projects.index') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Project List
                        </a>

                        <a href="{{ route('projects.board') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Kanban Board
                        </a>
                    </div>
                </div>

                {{-- Mobile Knowledge Dropdown --}}
                <div x-data="{ openMobileKnowledge: false }" class="rounded-2xl bg-white/5 ring-1 ring-white/10">
                    <button
                        type="button"
                        @click="openMobileKnowledge = !openMobileKnowledge"
                        class="w-full flex items-center justify-between px-4 py-3 text-slate-200"
                    >
                        <span class="flex items-center gap-2">
                            <span>Knowledge</span>
                        </span>

                        <svg class="w-4 h-4 text-slate-400 transition"
                             :class="openMobileKnowledge ? 'rotate-180' : ''"
                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9.75l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>

                    <div x-show="openMobileKnowledge" x-transition class="px-3 pb-3 space-y-2">
                        <a href="{{ route('documents.index') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Dokumentasi
                        </a>

                        <a href="{{ route('meetings.index') }}"
                           class="block rounded-xl bg-white/5 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/10 transition">
                            Meeting MoM
                        </a>
                    </div>
                </div>

                @auth
                    <div class="mt-3 rounded-2xl bg-slate-950/50 p-4 ring-1 ring-white/10">
                        <div class="text-sm font-semibold text-slate-200">{{ auth()->user()->name }}</div>
                        <div class="text-xs text-slate-400">{{ auth()->user()->email }}</div>

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
                @endauth

                @guest
                    <div class="mt-3 rounded-2xl bg-slate-950/50 p-4 ring-1 ring-white/10">
                        <a href="{{ route('login') }}"
                           class="block text-center rounded-xl bg-white/10 px-3 py-2 text-sm text-slate-200 ring-1 ring-white/10 hover:bg-white/15 transition">
                            Login
                        </a>
                    </div>
                @endguest

            </div>
        </div>
    </div>
</nav>
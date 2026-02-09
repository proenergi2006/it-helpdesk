{{-- resources/views/projects/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📝 Projects</h2>

            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500">
                + New Project
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Filter -->
            <div class="bg-white p-4 rounded-lg shadow">
                <form class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari nama project..."
                        class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 md:col-span-2"
                    />

                    <select name="status" class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All status</option>
                        @foreach(['backlog'=>'Backlog','todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'] as $k=>$v)
                            <option value="{{ $k }}" @selected(request('status')===$k)>{{ $v }}</option>
                        @endforeach
                    </select>

                    <select name="category" class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All type</option>
                        <option value="program" @selected(request('category')==='program')>Program</option>
                        <option value="infra" @selected(request('category')==='infra')>Infra</option>
                    </select>
                    

                    <select name="assigned_to" class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">All assignee</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}" @selected((string)request('assigned_to')===(string)$u->id)>{{ $u->name }}</option>
                        @endforeach
                    </select>

                   
                     

                    <div class="md:col-span-4 flex gap-2">
                        <button class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-gray-800">
                            Apply
                        </button>
                        <a href="{{ route('projects.index') }}"
                           class="rounded-lg bg-gray-100 px-4 py-2 hover:bg-gray-200">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3">Project</th>
                            <th class="text-left px-4 py-3">Assignee</th>
                            <th class="text-left px-4 py-3">Status</th>
                            <th class="text-left px-4 py-3">Progress</th>
                            <th class="text-left px-4 py-3">Start</th>
                            <th class="text-left px-4 py-3">Due</th>
                            <th class="text-left px-4 py-3">Done</th>
                            <th class="text-left px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($projects as $p)
                            @php
                                $statusLabel = [
                                    'backlog'=>'Backlog',
                                    'todo'=>'To Do',
                                    'in_progress'=>'In Progress',
                                    'review'=>'Review',
                                    'done'=>'Done'
                                ][$p->status] ?? $p->status;

                                $badgeClass = match($p->status){
                                    'backlog' => 'bg-gray-100 text-gray-700',
                                    'todo' => 'bg-blue-50 text-blue-700',
                                    'in_progress' => 'bg-amber-50 text-amber-700',
                                    'review' => 'bg-purple-50 text-purple-700',
                                    default => 'bg-green-50 text-green-700',
                                };

                                $isOverdue = $p->due_date && !$p->done_date && now()->toDateString() > $p->due_date->toDateString();
                                $nearDue = $p->due_date && !$p->done_date && now()->diffInDays($p->due_date, false) <= 3 && now()->diffInDays($p->due_date, false) >= 0;

                              
                                $progress = match($p->status){
                                    'backlog' => 0,
                                    'todo' => 35,
                                    'in_progress' => 50,
                                    'review' => 100,
                                    'done' => 100,
                                    default => 0,
                                };

                                $progressColor = match($p->status){
                                    'backlog' => 'bg-gray-400',
                                    'todo' => 'bg-blue-600',
                                    'in_progress' => 'bg-amber-500',
                                    'review' => 'bg-purple-600',
                                    'done' => 'bg-green-600',
                                    default => 'bg-gray-400',
                                };
                            @endphp

                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $p->name }}</div>
                                
                                    <div class="mt-1 flex items-center gap-2 text-xs text-gray-500">
                                        <span>#PRJ-{{ $p->id }}</span>
                                
                                        @php
                                            $catLabel = $p->category === 'infra' ? 'Infra' : 'Program';
                                            $catClass = $p->category === 'infra'
                                                ? 'bg-cyan-50 text-cyan-700 border-cyan-100'
                                                : 'bg-indigo-50 text-indigo-700 border-indigo-100';
                                        @endphp
                                
                                        <span class="inline-flex items-center px-2 py-0.5 rounded border {{ $catClass }}">
                                            {{ $catLabel }}
                                        </span>
                                    </div>
                                </td>
                                

                                <td class="px-4 py-3">
                                    @if($p->assignees->count())
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($p->assignees as $a)
                                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">
                                                    {{ $a->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                

                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded {{ $badgeClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div class="w-40 bg-gray-100 rounded-full h-2 overflow-hidden">
                                            <div
                                                class="h-2 rounded-full {{ $progressColor }}"
                                                style="width: {{ $progress }}%">
                                            </div>
                                        </div>
                                        <div class="text-xs text-gray-600 w-10 text-right">
                                            {{ $progress }}%
                                        </div>
                                    </div>
                                </td>
                                
                                

                                <td class="px-4 py-3">
                                    {{ $p->start_date?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $p->due_date?->format('d M Y') ?? '-' }}

                                    @if($isOverdue)
                                    <span class="ml-2 text-xs px-2 py-1 rounded bg-red-50 text-red-700">Overdue</span>
                                @elseif($nearDue)
                                    <span class="ml-2 text-xs px-2 py-1 rounded bg-amber-50 text-amber-700">H-3</span>
                                @endif
                                </td>

                                <td class="px-4 py-3">
                                    {{ $p->done_date?->format('d M Y') ?? '-' }}
                                </td>

                                {{-- <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route('projects.show', $p) }}"
                                        class="px-3 py-1 rounded bg-gray-800 text-white hover:bg-gray-700">
                                            Detail
                                        </a>

                                        <a href="{{ route('projects.edit', $p) }}"
                                           class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-500">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('projects.destroy', $p) }}" class="prj-delete">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td> --}}

                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                
                                        {{-- Detail --}}
                                        <a href="{{ route('projects.show', $p) }}"
                                           title="Detail"
                                           class="p-2 rounded-lg text-slate-700 hover:bg-slate-100 hover:text-slate-900 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M12 6.75c-5.25 0-9 5.25-9 5.25s3.75 5.25 9 5.25 9-5.25 9-5.25-3.75-5.25-9-5.25z" />
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M12 15.75a3.75 3.75 0 100-7.5 3.75 3.75 0 000 7.5z" />
                                            </svg>
                                        </a>
                                
                                        {{-- Edit --}}
                                        <a href="{{ route('projects.edit', $p) }}"
                                           title="Edit"
                                           class="p-2 rounded-lg text-blue-600 hover:bg-blue-50 hover:text-blue-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13l-2.685.896.896-2.685a4.5 4.5 0 011.13-1.897L16.862 4.487z" />
                                            </svg>
                                        </a>
                                
                                        {{-- Delete --}}
                                        <form method="POST"
                                              action="{{ route('projects.destroy', $p) }}"
                                              class="prj-delete">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    title="Delete"
                                                    class="p-2 rounded-lg text-rose-600 hover:bg-rose-50 hover:text-rose-700 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </form>
                                
                                    </div>
                                </td>
                                
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-gray-500">
                                    Belum ada project.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('form.prj-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                window.Swal.fire({
                    title: 'Hapus project?',
                    text: 'Data project akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((result) => {
                    if (result.isConfirmed) form.submit();
                });
            });
        });
    </script>
</x-app-layout>

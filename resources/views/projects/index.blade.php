{{-- resources/views/projects/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Projects</h2>

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
                <form class="grid grid-cols-1 md:grid-cols-4 gap-3">
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
                            @endphp

                            <tr>
                                <td class="px-4 py-3 font-medium">
                                    {{ $p->name }}
                                    <div class="text-xs text-gray-500">#PRJ-{{ $p->id }}</div>
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
                                    {{ $p->start_date?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $p->due_date?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    {{ $p->done_date?->format('d M Y') ?? '-' }}
                                </td>

                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
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

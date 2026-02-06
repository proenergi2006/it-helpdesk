<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">📝 Meeting of Memories</h2>
                <div class="text-xs text-gray-500 mt-1">Catatan meeting + action items  </div>
            </div>

            <a href="{{ route('meetings.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500">
                + New MoM
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

            <div class="bg-white p-4 rounded-lg shadow">
                <form class="flex flex-col md:flex-row gap-3">
                    <input name="q" value="{{ request('q') }}" placeholder="Cari judul / project / lokasi..."
                           class="w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                    <button class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-gray-800">
                        Search
                    </button>
                    <a href="{{ route('meetings.index') }}"
                       class="rounded-lg bg-gray-100 px-4 py-2 hover:bg-gray-200 text-center">
                        Reset
                    </a>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3">Meeting</th>
                            <th class="text-left px-4 py-3">Project</th>
                            <th class="text-left px-4 py-3">When</th>
                            <th class="text-left px-4 py-3">Attendees</th>
                            <th class="text-left px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($meetings as $m)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="font-medium text-gray-900">{{ $m->title }}</div>
                                    <div class="text-xs text-gray-500">{{ $m->location ?? '-' }}</div>
                                </td>
                                <td class="px-4 py-3">{{ $m->project_name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $m->meeting_at?->format('d M Y H:i') }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($m->attendees->take(4) as $a)
                                            <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $a->name }}</span>
                                        @endforeach
                                        @if($m->attendees->count() > 4)
                                            <span class="text-xs text-gray-500">+{{ $m->attendees->count()-4 }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex gap-2 flex-wrap">
                                        <a href="{{ route('meetings.show', $m) }}"
                                           class="px-3 py-1 rounded bg-gray-800 text-white hover:bg-gray-700">Detail</a>
                                        <a href="{{ route('meetings.edit', $m) }}"
                                           class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-500">Edit</a>
                                        <form method="POST" action="{{ route('meetings.destroy', $m) }}" class="mom-delete">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="px-4 py-10 text-center text-gray-500">Belum ada MoM.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>{{ $meetings->links() }}</div>
        </div>
    </div>

    <script>
        document.querySelectorAll('form.mom-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                window.Swal.fire({
                    title: 'Hapus MoM?',
                    text: 'Data MoM akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((r) => { if (r.isConfirmed) form.submit(); });
            });
        });
    </script>
</x-app-layout>

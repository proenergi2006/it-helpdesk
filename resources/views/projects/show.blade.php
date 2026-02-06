{{-- resources/views/projects/show.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="space-y-3">
            {{-- Row 1: Title --}}
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900">
                        {{ $project->name }}
                    </h2>
                    <div class="text-sm text-gray-500 mt-0.5">
                        Project ID · #PRJ-{{ $project->id }}
                    </div>
                </div>
    
                <div class="flex gap-2 shrink-0">
                    <a href="{{ route('projects.edit', $project) }}"
                       class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">
                        Edit
                    </a>
                    <a href="{{ route('projects.index') }}"
                       class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                        Back
                    </a>
                </div>
            </div>
    
            {{-- Row 2: Meta --}}
            @php
                $statusLabel = [
                    'backlog'=>'Backlog','todo'=>'To Do','in_progress'=>'In Progress','review'=>'Review','done'=>'Done'
                ][$project->status] ?? $project->status;
            @endphp
    
            <div class="flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-gray-600">
                <div class="flex items-center gap-2">
                    <span class="font-medium">Status</span>
                    <span class="px-2 py-1 rounded bg-gray-100 text-gray-700">
                        {{ $statusLabel }}
                    </span>
                </div>
    
                <div>
                    <span class="font-medium">Mulai:</span>
                    {{ $project->start_date?->format('d M Y') ?? '-' }} |
                </div>
    
                <div>
                    <span class="font-medium">Target:</span>
                    {{ $project->due_date?->format('d M Y') ?? '-' }} |
                </div>
    
                <div>
                    <span class="font-medium">Selesai:</span>
                    {{ $project->done_date?->format('d M Y') ?? '-' }}
                </div>
            </div>
        </div>
    </x-slot>
    
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            @if(session('success'))
                <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- CARD: Detail --}}
            <div class="bg-white rounded-xl shadow p-6">
              

              

              

                <div class="text-sm font-medium text-gray-700 mb-2">Deskripsi</div>

                {{-- render html dari trix --}}
                <div class="prose max-w-none">
                    {!! $project->description ?: '<span class="text-gray-500">-</span>' !!}
                </div>
            </div>

            {{-- CARD: Tambah progress/comment --}}
            <div class="bg-white rounded-xl shadow p-6">
                <div class="flex items-center justify-between">
                    <div class="font-semibold text-gray-800">Progress / Comment</div>
                    <div class="text-xs text-gray-500">Support image + PDF/Excel (max 5MB)</div>
                </div>

                <form method="POST" action="{{ route('projects.updates.store', $project) }}" class="mt-4 space-y-3">
                    @csrf

                    <input id="update_content" type="hidden" name="content" value="{{ old('content') }}">
                    <trix-editor input="update_content"
                        class="mt-1 bg-white rounded-lg border border-gray-300 min-h-[220px]"></trix-editor>

                    @error('content') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror

                    <div class="flex justify-end">
                        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">
                            Post Update
                        </button>
                    </div>
                </form>
            </div>

            {{-- CARD: List updates --}}
            <div class="bg-white rounded-xl shadow p-6">
                <div class="font-semibold text-gray-800 mb-4">Timeline</div>

                <div class="space-y-4">
                    @forelse($project->updates as $u)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $u->user?->name ?? 'User' }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ $u->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>

                                <form method="POST" action="{{ route('projects.updates.destroy', [$project, $u]) }}" class="upd-delete">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="px-3 py-1 rounded bg-red-600 text-white hover:bg-red-500 text-xs">
                                        Delete
                                    </button>
                                </form>
                            </div>

                            <div class="prose max-w-none mt-3">
                                {!! $u->content !!}
                            </div>
                        </div>
                    @empty
                        <div class="text-gray-500">Belum ada update.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="font-semibold text-gray-800 mb-4">Activity Log</div>
            
                <div class="space-y-3">
                    @forelse($project->activities as $a)
                        <div class="border rounded-lg p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <div class="text-sm font-medium text-gray-800">
                                        {{ $a->user?->name ?? 'System' }} · {{ $a->title }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $a->created_at->format('d M Y H:i') }}
                                    </div>
                                </div>
            
                                <div class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                    {{ $a->event }}
                                </div>
                            </div>
            
                            @if($a->meta)
                                <div class="text-sm text-gray-700 mt-3">
                                    <pre class="text-xs bg-gray-50 border rounded p-3 overflow-x-auto">{{ json_encode($a->meta, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-gray-500">Belum ada activity.</div>
                    @endforelse
                </div>
            </div>
            

        </div>
    </div>

    <script>
        // SweetAlert confirm delete update
        document.querySelectorAll('form.upd-delete').forEach(form => {
            form.addEventListener('submit', function(e){
                e.preventDefault();
                window.Swal.fire({
                    title: 'Hapus update?',
                    text: 'Update akan dihapus permanen.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ya, hapus',
                    cancelButtonText: 'Batal',
                }).then((r) => { if (r.isConfirmed) form.submit(); });
            });
        });

        // Trix attachment upload (image/pdf/excel max 5MB)
        document.addEventListener("trix-attachment-add", function (event) {
            const attachment = event.attachment;
            if (!attachment.file) return;

            const form = new FormData();
            form.append("file", attachment.file);

            fetch("{{ route('trix.upload') }}", {
                method: "POST",
                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" },
                body: form
            })
            .then(r => r.ok ? r.json() : r.json().then(e => Promise.reject(e)))
            .then(data => {
                attachment.setAttributes({
                    url: data.url,
                    href: data.href || data.url
                });
            })
            .catch(err => {
                alert(err?.message || 'Upload gagal (max 5MB, file harus image/pdf/excel).');
            });
        });
    </script>
</x-app-layout>

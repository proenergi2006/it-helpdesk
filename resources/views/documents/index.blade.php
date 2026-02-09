{{-- resources/views/documents/index.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">📚 Dokumentasi</h2>

            <a href="{{ route('documents.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-500">
                + Upload Dokumen
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
                <form class="grid grid-cols-1 md:grid-cols-3 gap-3">
                    <input
                        name="project"
                        value="{{ request('project') }}"
                        placeholder="Cari project..."
                        class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                    />

                    <select name="type" class="rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Semua jenis</option>
                    
                        @foreach([
                            'CR'  => 'Change Request',
                            'BRD' => 'Business Requirement',
                            'DEV' => 'Development',
                            'UAT' => 'UAT',
                            'IMP' => 'Implement Prod',
                            'UG'  => 'User Guide',
                            'DOC' => 'Dokumentasi'
                        ] as $k => $v)
                            <option value="{{ $k }}" @selected(request('type') === $k)>
                                {{ $v }}
                            </option>
                        @endforeach
                    </select>
                    

                    <button class="rounded-lg bg-gray-900 text-white px-4 py-2 hover:bg-gray-800">
                        Filter
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="text-left px-4 py-3">Project</th>
                            <th class="text-left px-4 py-3">Judul</th>
                            <th class="text-left px-4 py-3">Jenis</th>
                            <th class="text-left px-4 py-3">File</th>
                            <th class="text-left px-4 py-3">Created By</th>
                            <th class="text-left px-4 py-3">Updated By</th>
                            <th class="text-left px-4 py-3">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @forelse($documents as $doc)
                            <tr>
                                <td class="px-4 py-3 font-medium">{{ $doc->project_name }}</td>

                                <td class="px-4 py-3">
                                    <div class="font-medium">{{ $doc->title }}</div>
                                    <div class="text-xs text-gray-500">
                                        {{ $doc->created_at->format('d M Y H:i') }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded bg-indigo-50 text-indigo-700 border border-indigo-100">
                                        {{ $doc->type }}
                                    </span>
                                </td>

                                {{-- FILE: klik -> preview popup --}}
                                <td class="px-4 py-3">
                                    <button
                                        type="button"
                                        class="text-left hover:underline text-indigo-700 doc-preview"
                                        data-name="{{ e($doc->original_name) }}"
                                        data-mime="{{ e($doc->mime ?? '') }}"
                                        data-url="{{ route('documents.preview', $doc) }}"
                                        data-download="{{ route('documents.download', $doc) }}"
                                    >
                                        {{ $doc->original_name }}
                                    </button>

                                    <div class="text-xs text-gray-500">
                                        {{ number_format(($doc->size ?? 0)/1024, 0) }} KB
                                    </div>
                                </td>

                                <td class="px-4 py-3">{{ $doc->creator?->name ?? '-' }}</td>
                                <td class="px-4 py-3">{{ $doc->updater?->name ?? '-' }}</td>

                                {{-- <td class="px-4 py-3">
                                    <div class="flex flex-wrap gap-2">
                                        <a class="px-3 py-1 rounded bg-gray-900 text-white hover:bg-gray-800"
                                           href="{{ route('documents.download', $doc) }}">
                                            Download
                                        </a>

                                        <a class="px-3 py-1 rounded bg-blue-600 text-white hover:bg-blue-500"
                                           href="{{ route('documents.edit', $doc) }}">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('documents.destroy', $doc) }}" class="doc-delete">
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
                                        {{-- Download --}}
                                        <a href="{{ route('documents.download', $doc) }}"
                                           title="Download"
                                           class="p-2 rounded-lg text-emerald-600 hover:bg-emerald-50 hover:text-emerald-700 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                                                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M7.5 10.5L12 15m0 0l4.5-4.5M12 15V3" />
                                            </svg>
                                        </a>
                                
                                        {{-- Edit --}}
                                        <a href="{{ route('documents.edit', $doc) }}"
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
                                              action="{{ route('documents.destroy', $doc) }}"
                                              class="doc-delete">
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
                                    Belum ada dokumen.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $documents->links() }}
            </div>
        </div>
    </div>

    <script>
        // Preview modal (PDF/Image inline)
        document.querySelectorAll('.doc-preview').forEach(btn => {
            btn.addEventListener('click', () => {
                const name = btn.dataset.name || 'Preview';
                const mime = (btn.dataset.mime || '').toLowerCase();
                const url = btn.dataset.url;
                const download = btn.dataset.download;

                const isPdf = mime.includes('pdf');
                const isImg = mime.startsWith('image/');

                let html = '';

                if (isPdf) {
                    html = `
                        <div style="height:70vh">
                            <iframe src="${url}" style="width:100%;height:100%;border:0;border-radius:12px;"></iframe>
                        </div>
                        <div style="margin-top:10px;text-align:right">
                            <a href="${download}" class="swal2-confirm swal2-styled" style="text-decoration:none">
                                Download
                            </a>
                        </div>
                    `;
                } else if (isImg) {
                    html = `
                        <img src="${url}" alt="${name}" style="max-width:100%;border-radius:12px;" />
                        <div style="margin-top:10px;text-align:right">
                            <a href="${download}" class="swal2-confirm swal2-styled" style="text-decoration:none">
                                Download
                            </a>
                        </div>
                    `;
                } else {
                    html = `
                        <div style="text-align:left">
                            <div style="font-weight:600;margin-bottom:6px;">File tidak bisa dipreview</div>
                            <div style="font-size:13px;opacity:.85">Tipe: ${mime || '-'}</div>
                            <div style="margin-top:14px;text-align:right">
                                <a href="${download}" class="swal2-confirm swal2-styled" style="text-decoration:none">
                                    Download
                                </a>
                            </div>
                        </div>
                    `;
                }

                window.Swal.fire({
                    title: name,
                    html,
                    width: '900px',
                    showCloseButton: true,
                    showConfirmButton: false,
                });
            });
        });

        // Delete confirm
        document.querySelectorAll('form.doc-delete').forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();
                window.Swal.fire({
                    title: 'Hapus dokumen?',
                    text: 'File akan dihapus permanen.',
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

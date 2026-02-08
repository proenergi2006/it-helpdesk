{{-- resources/views/projects/edit.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Project</h2>
            <a href="{{ route('projects.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-xl shadow">
                <form method="POST" action="{{ route('projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    {{-- Nama --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nama Project</label>
                        <input
                            name="name"
                            value="{{ old('name', $project->name) }}"
                            required
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        />
                        @error('name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                     {{-- Tipe Project (Infra / Program) --}}
                     <div>
                        <label class="block text-sm font-medium text-gray-700">Tipe Project</label>
                        <select
                            name="category"
                            required
                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"
                        >
                            <option value="program" @selected(old('category', $project->category)==='program')>
                                Program / Aplikasi
                            </option>
                            <option value="infra" @selected(old('category', $project->category)==='infra')>
                                Infra / Network / Server
                            </option>
                        </select>
                        @error('category') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- Deskripsi full + Status/Assign bawah --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Deskripsi (Rich Text)</label>

                            <input id="description" type="hidden" name="description"
                                   value="{{ old('description', $project->description) }}">
                            <trix-editor input="description"
                                class="mt-1 bg-white rounded-lg border border-gray-300 min-h-[260px]">
                            </trix-editor>

                            @error('description') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                            <div class="text-xs text-gray-500 mt-1">Bisa bold/italic, list, dan sisipkan gambar.</div>
                        </div>

                        <div class="md:col-span-2">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Status</label>
                                    <select name="status" required
                                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($statuses as $s)
                                            @php
                                                $label = [
                                                    'backlog'=>'Backlog',
                                                    'todo'=>'To Do',
                                                    'in_progress'=>'In Progress',
                                                    'review'=>'Review',
                                                    'done'=>'Done'
                                                ][$s] ?? $s;
                                            @endphp
                                            <option value="{{ $s }}" @selected(old('status', $project->status)===$s)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                    @error('status') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Assign ke (bisa lebih dari 1)</label>

                                    @php
                                        $selected = collect(old('assignees', $project->assignees->pluck('id')->all()));
                                    @endphp

                                    <select id="assignees" name="assignees[]" multiple
                                            class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" @selected($selected->contains($u->id))>
                                                {{ $u->name }} ({{ $u->email }})
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('assignees') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                    @error('assignees.*') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dates --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mulai (opsional)</label>
                            <input type="date" name="start_date"
                                   value="{{ old('start_date', optional($project->start_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"/>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Target Selesai (opsional)</label>
                            <input type="date" name="due_date"
                                   value="{{ old('due_date', optional($project->due_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"/>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Selesai Real (opsional)</label>
                            <input type="date" name="done_date"
                                   value="{{ old('done_date', optional($project->done_date)->format('Y-m-d')) }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500"/>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 pt-2">
                        <button class="px-5 py-2.5 rounded-lg bg-blue-600 text-white hover:bg-blue-500">
                            Save Changes
                        </button>
                        <a href="{{ route('projects.index') }}"
                           class="px-5 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200">
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tom Select init
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('assignees');
            if (el && window.TomSelect) {
                new window.TomSelect(el, {
                    plugins: ['remove_button'],
                    placeholder: 'Pilih assignee...',
                    hidePlaceholder: true,
                    closeAfterSelect: false,
                    searchField: ['text'],
                    maxOptions: 5000,
                });
            }
        });

        // Trix image upload
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
            .then(r => r.json())
            .then(data => {
                if (!data.url) throw new Error('No url returned');
                attachment.setAttributes({ url: data.url, href: data.href || data.url });
            })
            .catch(() => alert("Upload gambar gagal"));
        });
    </script>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">+ New Meeting MoM</h2>
            <a href="{{ route('meetings.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-8 rounded-xl shadow">
                <form method="POST" action="{{ route('meetings.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Judul Meeting</label>
                            <input name="title" value="{{ old('title') }}" required
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                            @error('title') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Project (optional)</label>
                            <input name="project_name" value="{{ old('project_name') }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Waktu Meeting</label>
                            <input type="datetime-local" name="meeting_at" value="{{ old('meeting_at') }}" required
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                            @error('meeting_at') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Lokasi / Link (optional)</label>
                            <input name="location" value="{{ old('location') }}"
                                   class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Attendees</label>
                            <select id="attendees" name="attendees[]" multiple
                                    class="mt-1 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" @selected(collect(old('attendees',[]))->contains($u->id))>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700">Notes (MoM)</label>
                            <input id="notes" type="hidden" name="notes" value="{{ old('notes') }}">
                            <trix-editor input="notes" class="mt-1 bg-white rounded-lg border border-gray-300 min-h-[260px]"></trix-editor>
                        </div>
                    </div>

                    {{-- Action items (spreadsheet-like) --}}
                    <div class="border rounded-xl p-4">
                        <div class="flex items-center justify-between">
                            <div class="font-medium text-gray-800">Action Items</div>
                            <button type="button" id="addRow"
                                    class="px-3 py-1.5 rounded bg-gray-900 text-white hover:bg-gray-800 text-sm">
                                + Add Row
                            </button>
                        </div>

                        <div class="overflow-x-auto mt-3">
                            <table class="min-w-full text-sm" id="aiTable">
                                <thead class="bg-gray-50 border-b">
                                    <tr>
                                        <th class="text-left px-3 py-2">Task</th>
                                        <th class="text-left px-3 py-2">Owner</th>
                                        <th class="text-left px-3 py-2">Due</th>
                                        <th class="text-left px-3 py-2">Status</th>
                                        <th class="text-left px-3 py-2">#</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y" id="aiBody">
                                    {{-- default 1 row --}}
                                    <tr>
                                        <td class="px-3 py-2">
                                            <input name="tasks[]" class="w-full rounded-lg border-gray-300" placeholder="Contoh: UAT sign-off" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <select name="owners[]" class="w-full rounded-lg border-gray-300">
                                                <option value="">-</option>
                                                @foreach($users as $u)
                                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <input type="date" name="dues[]" class="w-full rounded-lg border-gray-300" />
                                        </td>
                                        <td class="px-3 py-2">
                                            <select name="statuses[]" class="w-full rounded-lg border-gray-300">
                                                <option value="todo">To Do</option>
                                                <option value="in_progress">In Progress</option>
                                                <option value="done">Done</option>
                                            </select>
                                        </td>
                                        <td class="px-3 py-2">
                                            <button type="button" class="rmRow px-2 py-1 rounded bg-red-600 text-white">X</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button class="px-5 py-2.5 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">Save</button>
                        <a href="{{ route('meetings.index') }}" class="px-5 py-2.5 rounded-lg bg-gray-100 hover:bg-gray-200">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // TomSelect for attendees (kalau kamu sudah pakai di project, ini otomatis jalan)
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('attendees');
            if (el && window.TomSelect) {
                new window.TomSelect(el, { plugins: ['remove_button'], placeholder: 'Pilih attendees...' });
            }
        });

        // Trix upload (pakai route yang sudah kamu punya)
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
            .then(data => attachment.setAttributes({ url: data.url, href: data.href || data.url }))
            .catch(err => alert(err?.message || 'Upload gagal (max 5MB, file harus image/pdf/excel).'));
        });

        // Action items add/remove row
        const aiBody = document.getElementById('aiBody');
        document.getElementById('addRow').addEventListener('click', () => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="px-3 py-2"><input name="tasks[]" class="w-full rounded-lg border-gray-300" placeholder="Task..." /></td>
                <td class="px-3 py-2">
                    <select name="owners[]" class="w-full rounded-lg border-gray-300">
                        <option value="">-</option>
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td class="px-3 py-2"><input type="date" name="dues[]" class="w-full rounded-lg border-gray-300" /></td>
                <td class="px-3 py-2">
                    <select name="statuses[]" class="w-full rounded-lg border-gray-300">
                        <option value="todo">To Do</option>
                        <option value="in_progress">In Progress</option>
                        <option value="done">Done</option>
                    </select>
                </td>
                <td class="px-3 py-2"><button type="button" class="rmRow px-2 py-1 rounded bg-red-600 text-white">X</button></td>
            `;
            aiBody.appendChild(tr);
        });

        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('rmRow')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
</x-app-layout>

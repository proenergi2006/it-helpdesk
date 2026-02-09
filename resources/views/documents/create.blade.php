<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Upload Dokumen</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 rounded-lg shadow">
                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium">Nama Project</label>
                        <input name="project_name" value="{{ old('project_name') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                        @error('project_name') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Judul Dokumen</label>
                        <input name="title" value="{{ old('title') }}" class="mt-1 w-full rounded-lg border-gray-300" required>
                        @error('title') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Jenis Dokumen</label>
                        <select name="type" class="mt-1 w-full rounded-lg border-gray-300" required>
                            @foreach([
                                'CR'  => 'Change Request',
                                'BRD' => 'Business Requirement',
                                'DEV' => 'Development',
                                'UAT' => 'UAT',
                                'IMP' => 'Implement Prod',
                                'DOC' => 'Dokumentasi',
                                'UG'  => 'User Guide',
                            ] as $k => $v)
                                <option value="{{ $k }}" @selected(old('type') === $k)>{{ $v }}</option>
                            @endforeach
                        </select>
                    
                        @error('type')
                            <div class="text-sm text-red-600 mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    

                    <div>
                        <label class="block text-sm font-medium">Catatan (opsional)</label>
                        <textarea name="notes" rows="3" class="mt-1 w-full rounded-lg border-gray-300">{{ old('notes') }}</textarea>
                        @error('notes') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium">File</label>
                        <input type="file" name="file" class="mt-1 w-full" required>
                        <div class="text-xs text-gray-500 mt-1">Maks 20MB. PDF/DOC/DOCX/XLS/XLSX/ZIP/PNG/JPG</div>
                        @error('file') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>

                    <div class="flex gap-2">
                        <button class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500">Upload</button>
                        <a href="{{ route('documents.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Back</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

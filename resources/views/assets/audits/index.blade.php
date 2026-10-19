<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Opname Aset</h2>
            <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('success'))
                <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif

            <form method="POST" action="{{ route('assets.audits.store') }}" class="bg-white rounded-xl shadow p-6 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                @csrf
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama opname</label>
                    <input name="name" required value="{{ old('name', 'Opname '.now()->format('F Y')) }}" class="w-full rounded-lg border-gray-300">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cabang <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <select name="cabang" class="w-full rounded-lg border-gray-300">
                        <option value="">Semua cabang</option>
                        @foreach (\App\Models\Asset::CABANG as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Mulai Opname</button>
            </form>

            <div class="bg-white rounded-xl shadow overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="text-left text-gray-500 border-b">
                        <tr><th class="p-3">Nama</th><th class="p-3">Cabang</th><th class="p-3">Terverifikasi</th><th class="p-3">Status</th><th class="p-3">Dibuat</th><th class="p-3"></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($audits as $a)
                            <tr class="border-b last:border-0">
                                <td class="p-3 font-medium">{{ $a->name }}</td>
                                <td class="p-3">{{ $a->cabang ?? 'Semua' }}</td>
                                <td class="p-3">{{ $a->items_count }}</td>
                                <td class="p-3">{{ $a->isOpen() ? 'Berjalan' : 'Selesai '.$a->closed_at->format('d M Y') }}</td>
                                <td class="p-3">{{ $a->created_at->format('d M Y') }} · {{ $a->creator?->name }}</td>
                                <td class="p-3 text-right"><a href="{{ route('assets.audits.show', $a) }}" class="text-blue-600 hover:underline">Buka</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="p-6 text-center text-gray-500">Belum ada opname.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

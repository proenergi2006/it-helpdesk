<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Approval Ticket</h2>
    </x-slot>

    <div class="py-10">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 p-3 rounded bg-green-100 text-green-800">{{ session('success') }}</div>
            @endif

            <div class="bg-white shadow rounded-lg overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-blue-100 text-blue-800">
                        <tr>
                            <th class="p-2 text-left">#</th>
                            <th class="p-2 text-left">Pemohon</th>
                            <th class="p-2 text-left">Judul</th>
                            <th class="p-2 text-left">Deskripsi</th>
                            <th class="p-2 text-left">Kategori</th>
                            <th class="p-2 text-left">Priority</th>
                            <th class="p-2 text-left">Dibuat</th>
                            <th class="p-2 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($tickets as $t)
                            <tr class="border-b align-top">
                                <td class="p-2 font-semibold text-blue-700">#{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td class="p-2">{{ $t->nama }}<br><span class="text-xs text-gray-500">{{ $t->cabang }}</span></td>
                                <td class="p-2">{{ $t->title }}</td>
                                <td class="p-2 whitespace-pre-line max-w-md">{{ $t->description }}</td>
                                <td class="p-2 capitalize">{{ $t->category }}</td>
                                <td class="p-2">{{ $t->priority }}</td>
                                <td class="p-2">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-2">
                                    <form method="POST" action="{{ route('approvals.approve', $t->id) }}" class="mb-2">
                                        @csrf
                                        <button class="px-3 py-1 rounded bg-green-600 text-white text-xs">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('approvals.reject', $t->id) }}">
                                        @csrf
                                        <input type="text" name="approval_note" required placeholder="Alasan penolakan"
                                            class="border rounded text-xs px-2 py-1 mb-1 w-full">
                                        <button class="px-3 py-1 rounded bg-red-600 text-white text-xs">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="text-center py-6 text-gray-500">Tidak ada ticket yang menunggu approval</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Cocokkan dengan Accurate</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
            @if (session('error'))
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif
            @error('file')
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ $message }}</div>
            @enderror

            <div class="bg-white rounded-xl shadow p-6 space-y-4">
                <ol class="list-decimal ml-5 text-sm text-gray-700 space-y-1">
                    <li>Minta finance mengekspor daftar aset dari Accurate (Excel/CSV).</li>
                    <li>Pastikan ada kolom nomor aset dengan header <code>No. Aset</code> atau <code>no_aset_accurate</code>. Kolom <code>Nama</code> opsional.</li>
                    <li>Hasil perbandingan hanya laporan, data aset di helpdesk tidak diubah.</li>
                </ol>

                <form method="POST" action="{{ route('assets.reconcile.run') }}" enctype="multipart/form-data" class="flex flex-wrap items-center gap-3">
                    @csrf
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required class="text-sm">
                    <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Bandingkan</button>
                    <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Kembali</a>
                </form>
            </div>

            @if ($result)
                <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                    @foreach ([
                        'Aset di Accurate' => $result['total_accurate'],
                        'Aset di Helpdesk (ber-nomor Accurate)' => $result['total_helpdesk'],
                        'Cocok' => $result['matched'],
                        'Perlu ditindaklanjuti' => $result['only_accurate']->count() + $result['only_helpdesk']->count() + $result['disposed_still']->count(),
                    ] as $label => $n)
                        <div class="bg-white rounded-xl shadow p-4">
                            <div class="text-2xl font-semibold">{{ $n }}</div>
                            <div class="text-xs text-gray-500">{{ $label }}</div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-semibold mb-1">Ada di Accurate, belum ada di Helpdesk ({{ $result['only_accurate']->count() }})</h3>
                    <p class="text-xs text-gray-500 mb-3">Tindakan: IT menambahkan ke helpdesk (bisa lewat Import dengan file yang sama).</p>
                    @forelse ($result['only_accurate'] as $r)
                        <div class="text-sm py-1 border-b last:border-0"><span class="font-mono">{{ $r['no'] }}</span> {{ $r['name'] }}</div>
                    @empty
                        <div class="text-sm text-gray-500">Tidak ada selisih.</div>
                    @endforelse
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-semibold mb-1">Ada di Helpdesk, tidak ada di Accurate ({{ $result['only_helpdesk']->count() }})</h3>
                    <p class="text-xs text-gray-500 mb-3">Mungkin sudah di-disposal di Accurate atau nomor salah ketik. Tindakan: cek bersama finance.</p>
                    @forelse ($result['only_helpdesk'] as $a)
                        <div class="text-sm py-1 border-b last:border-0">
                            <a href="{{ route('assets.show', $a) }}" class="text-blue-600 hover:underline">{{ $a->code }}</a>
                            <span class="font-mono">{{ $a->accurate_no }}</span> {{ $a->name }} · {{ $a->status_label }}
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Tidak ada selisih.</div>
                    @endforelse
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <h3 class="font-semibold mb-1">Status Disposal di Helpdesk, tapi masih ada di Accurate ({{ $result['disposed_still']->count() }})</h3>
                    <p class="text-xs text-gray-500 mb-3">Tindakan: finance memproses disposal di Accurate.</p>
                    @forelse ($result['disposed_still'] as $a)
                        <div class="text-sm py-1 border-b last:border-0">
                            <a href="{{ route('assets.show', $a) }}" class="text-blue-600 hover:underline">{{ $a->code }}</a>
                            <span class="font-mono">{{ $a->accurate_no }}</span> {{ $a->name }}
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Tidak ada selisih.</div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

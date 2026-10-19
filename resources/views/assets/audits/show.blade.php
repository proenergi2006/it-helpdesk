<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-3 sm:gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $audit->name }}</h2>
                <div class="text-sm text-gray-500 mt-0.5">{{ $audit->cabang ?? 'Semua cabang' }} · {{ $audit->isOpen() ? 'Berjalan' : 'Selesai '.$audit->closed_at->format('d M Y H:i') }}</div>
            </div>
            <div class="flex flex-wrap gap-2">
                @if ($audit->isOpen())
                    <form method="POST" action="{{ route('assets.audits.close', $audit) }}" onsubmit="return confirm('Tutup opname? {{ $pending->count() }} aset belum terverifikasi.')">
                        @csrf
                        <button class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-500 text-sm">Tutup Opname</button>
                    </form>
                @endif
                <a href="{{ route('assets.audits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Kembali</a>
            </div>
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

            @php $pct = $total ? round($scannedCount / $total * 100) : 0; @endphp
            <div class="bg-white rounded-xl shadow p-6 space-y-3">
                <div class="flex items-end justify-between gap-3">
                    <div class="text-sm text-gray-600">Terverifikasi <strong class="text-gray-900 text-lg">{{ $scannedCount }}</strong> dari {{ $total }} aset ({{ $pct }}%)</div>
                    <div class="text-sm text-gray-600">Belum: <strong>{{ $pending->count() }}</strong></div>
                </div>
                <div class="h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-green-500" style="width: {{ $pct }}%"></div></div>

                @if ($audit->isOpen())
                    <form method="POST" action="{{ route('assets.audits.scan', $audit) }}" class="flex flex-wrap gap-2 pt-2">
                        @csrf
                        <input name="code" autofocus required autocomplete="off" placeholder="Pindai QR dengan scanner, atau ketik kode (AST-0001)"
                               class="flex-1 min-w-[16rem] rounded-lg border-gray-300">
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Verifikasi</button>
                    </form>
                    <p class="text-xs text-gray-500">Dengan kamera HP: pindai QR di label, buka halaman aset, lalu tekan "Verifikasi di Opname".</p>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="font-semibold text-gray-800 mb-3">Belum terverifikasi ({{ $pending->count() }})</div>
                    @forelse ($pending as $a)
                        <div class="flex items-center justify-between gap-3 py-1.5 border-b last:border-0 text-sm">
                            <div class="min-w-0">
                                <a href="{{ route('assets.show', $a) }}" class="text-blue-600 hover:underline">{{ $a->code }}</a>
                                {{ $a->name }}
                                <span class="text-gray-500">· {{ $a->cabang ?? '-' }} · {{ $a->holder_name ?? '-' }}</span>
                            </div>
                            @if ($audit->isOpen())
                                <form method="POST" action="{{ route('assets.audits.verify', [$audit, $a]) }}">@csrf
                                    <button class="text-xs px-2 py-1 rounded bg-green-50 text-green-700 hover:bg-green-100">Verifikasi</button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Semua aset sudah terverifikasi.</div>
                    @endforelse
                </div>

                <div class="bg-white rounded-xl shadow p-6">
                    <div class="font-semibold text-gray-800 mb-3">Terverifikasi terakhir</div>
                    @forelse ($items as $i)
                        <div class="py-1.5 border-b last:border-0 text-sm">
                            <a href="{{ route('assets.show', $i->asset) }}" class="text-blue-600 hover:underline">{{ $i->asset->code }}</a>
                            {{ $i->asset->name }}
                            <span class="text-gray-500">· {{ $i->scanned_at->format('d M H:i') }} · {{ $i->scanner?->name }}</span>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Belum ada.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

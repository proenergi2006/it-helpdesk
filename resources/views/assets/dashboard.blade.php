@php
    $fmt = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
    $statusStyle = [
        'aktif'      => ['Aktif', 'bg-emerald-500', 'text-emerald-700'],
        'dipinjam'   => ['Dipinjam', 'bg-sky-500', 'text-sky-700'],
        'rusak'      => ['Rusak', 'bg-red-500', 'text-red-700'],
        'diperbaiki' => ['Diperbaiki', 'bg-amber-500', 'text-amber-700'],
        'disposal'   => ['Disposal', 'bg-gray-400', 'text-gray-600'],
    ];
    $attention = ($byStatus['rusak'] ?? 0) + ($byStatus['diperbaiki'] ?? 0);
    $auditPct = $auditTotal ? round($auditDone / $auditTotal * 100) : 0;
@endphp
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Aset</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Daftar Aset</a>
                <a href="{{ route('assets.create') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Tambah Aset</a>
                <a href="{{ route('assets.audits.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Opname</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- Ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">
                <a href="{{ route('assets.index') }}" class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4 hover:ring-blue-200">
                    <div class="text-xs text-gray-500">Total aset</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-1">{{ $total }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $live }} masih dipakai</div>
                </a>
                <a href="{{ route('assets.index', ['status' => 'rusak']) }}" class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4 hover:ring-red-200">
                    <div class="text-xs text-gray-500">Rusak dan diperbaiki</div>
                    <div class="text-3xl font-semibold mt-1 {{ $attention ? 'text-red-600' : 'text-gray-900' }}">{{ $attention }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $byStatus['rusak'] ?? 0 }} rusak, {{ $byStatus['diperbaiki'] ?? 0 }} diperbaiki</div>
                </a>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Tiket aset aktif</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-1">{{ $openTickets }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">open dan dikerjakan</div>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Perbaikan bulan ini</div>
                    <div class="text-3xl font-semibold text-gray-900 mt-1">{{ $repairsMonth }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $costMonth > 0 ? $fmt($costMonth) : 'belum ada biaya' }}</div>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4 col-span-2 lg:col-span-1">
                    <div class="text-xs text-gray-500">Biaya perbaikan tahun ini</div>
                    <div class="text-xl font-semibold text-gray-900 mt-2">{{ $fmt($costYear) }}</div>
                </div>
            </div>

            {{-- Status dan opname --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Kondisi aset</div>
                    @if ($total > 0)
                        <div class="flex h-3 rounded-full overflow-hidden bg-gray-100">
                            @foreach ($statusStyle as $key => [$label, $bar, $text])
                                @if (($byStatus[$key] ?? 0) > 0)
                                    <div class="{{ $bar }}" style="width: {{ $byStatus[$key] / $total * 100 }}%" title="{{ $label }}: {{ $byStatus[$key] }}"></div>
                                @endif
                            @endforeach
                        </div>
                        <div class="mt-3 flex flex-wrap gap-x-5 gap-y-1.5 text-sm">
                            @foreach ($statusStyle as $key => [$label, $bar, $text])
                                <a href="{{ route('assets.index', ['status' => $key]) }}" class="flex items-center gap-2 hover:underline">
                                    <span class="h-2.5 w-2.5 rounded-full {{ $bar }}"></span>
                                    <span class="text-gray-600">{{ $label }}</span>
                                    <strong class="text-gray-900">{{ $byStatus[$key] ?? 0 }}</strong>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-500">Belum ada aset. Tambahkan lewat tombol Tambah Aset atau Import.</p>
                    @endif
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Opname</div>
                    @if ($audit)
                        <div class="font-medium text-gray-900">{{ $audit->name }}</div>
                        <div class="text-sm text-gray-500 mt-0.5">{{ $auditDone }} dari {{ $auditTotal }} aset terverifikasi</div>
                        <div class="mt-3 h-2 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-emerald-500" style="width: {{ $auditPct }}%"></div></div>
                        <a href="{{ route('assets.audits.show', $audit) }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Lanjutkan opname</a>
                    @else
                        <p class="text-sm text-gray-500">Tidak ada opname yang berjalan.</p>
                        <a href="{{ route('assets.audits.index') }}" class="inline-block mt-3 text-sm text-blue-600 hover:underline">Mulai opname</a>
                    @endif
                </div>
            </div>

            {{-- Sebaran --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5">
                @foreach ([['Jenis', $byType, 'type'], ['Cabang', $byCabang, 'cabang'], ['Departemen', $byDepartment, 'department']] as [$title, $rows, $param])
                    @php $max = max(1, (int) $rows->max()); @endphp
                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Per {{ strtolower($title) }}</div>
                        <div class="space-y-2.5">
                            @forelse ($rows as $label => $n)
                                <a href="{{ route('assets.index', [$param => $label]) }}" class="block group">
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-700 group-hover:underline truncate pr-2">{{ $param === 'department' ? (\App\Models\Asset::DEPARTMENTS[$label] ?? $label) : $label }}</span>
                                        <span class="font-medium text-gray-900">{{ $n }}</span>
                                    </div>
                                    <div class="mt-1 h-1.5 bg-gray-100 rounded-full overflow-hidden"><div class="h-full bg-blue-500" style="width: {{ $n / $max * 100 }}%"></div></div>
                                </a>
                            @empty
                                <p class="text-sm text-gray-500">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Kelengkapan data</div>
                    <div class="space-y-2.5 text-sm">
                        @foreach (['Tanpa pemegang' => $noHolder, 'Tanpa foto' => $noPhoto, 'Tanpa departemen' => $noDepartment, 'Tanpa No. Aset Accurate' => $noAccurate, 'Dokumen belum lengkap (dari 4)' => $docIncomplete] as $label => $n)
                            <div class="flex justify-between">
                                <span class="text-gray-700">{{ $label }}</span>
                                <span class="font-medium {{ $n ? 'text-amber-600' : 'text-emerald-600' }}">{{ $n }}</span>
                            </div>
                        @endforeach
                    </div>
                    <p class="text-xs text-gray-400 mt-3">Dihitung dari aset yang belum disposal. Barang kecil memang boleh tanpa nomor Accurate.</p>
                </div>
            </div>

            {{-- Garansi dan sering diperbaiki --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="flex items-baseline justify-between mb-3">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Garansi segera habis (60 hari)</div>
                        @if ($warrantyExpired)<span class="text-xs text-red-600">{{ $warrantyExpired }} sudah habis</span>@endif
                    </div>
                    @forelse ($warrantySoon as $a)
                        <a href="{{ route('assets.show', $a) }}" class="flex items-center justify-between gap-3 py-2 border-b border-gray-100 last:border-0 text-sm hover:bg-gray-50">
                            <span class="min-w-0"><span class="font-mono text-xs text-gray-500">{{ $a->code }}</span> <span class="text-gray-800">{{ $a->name }}</span></span>
                            <span class="shrink-0 {{ now()->diffInDays($a->warranty_until) <= 30 ? 'text-red-600' : 'text-amber-600' }}">{{ $a->warranty_until->format('d M Y') }}</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Tidak ada garansi yang habis dalam 60 hari.</p>
                    @endforelse
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Paling sering diperbaiki</div>
                    @forelse ($topRepaired as $a)
                        <a href="{{ route('assets.show', $a) }}" class="flex items-center justify-between gap-3 py-2 border-b border-gray-100 last:border-0 text-sm hover:bg-gray-50">
                            <span class="min-w-0"><span class="font-mono text-xs text-gray-500">{{ $a->code }}</span> <span class="text-gray-800">{{ $a->name }}</span></span>
                            <span class="shrink-0 font-medium text-gray-900">{{ $a->repairs_count }}x</span>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada riwayat perbaikan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Aktivitas terbaru --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Perbaikan terbaru</div>
                    @forelse ($recentRepairs as $r)
                        <a href="{{ route('assets.show', $r->asset) }}" class="block py-2 border-b border-gray-100 last:border-0 text-sm hover:bg-gray-50">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-800 truncate">{{ $r->asset->name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $r->repaired_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 truncate">{{ $r->problem }}</div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada perbaikan.</p>
                    @endforelse
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Mutasi terbaru</div>
                    @forelse ($recentMutations as $m)
                        <a href="{{ route('assets.show', $m->asset) }}" class="block py-2 border-b border-gray-100 last:border-0 text-sm hover:bg-gray-50">
                            <div class="flex justify-between gap-3">
                                <span class="text-gray-800 truncate">{{ $m->asset->name }}</span>
                                <span class="text-xs text-gray-400 shrink-0">{{ $m->mutated_at->format('d M Y') }}</span>
                            </div>
                            <div class="text-xs text-gray-500 truncate">{{ $m->from_cabang ?? '-' }} &rarr; {{ $m->to_cabang ?? '-' }} · {{ $m->to_user ?? '-' }}</div>
                        </a>
                    @empty
                        <p class="text-sm text-gray-500">Belum ada mutasi.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

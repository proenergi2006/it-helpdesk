@php
    $fmt = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
    $input = 'w-full rounded-lg border-gray-300 text-sm';
    $repairTotal = $asset->repairs->sum(fn ($r) => $r->total_cost);
    $statusStyle = [
        'aktif'      => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'dipinjam'   => 'bg-sky-50 text-sky-700 ring-sky-200',
        'rusak'      => 'bg-red-50 text-red-700 ring-red-200',
        'diperbaiki' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'disposal'   => 'bg-gray-100 text-gray-600 ring-gray-200',
    ][$asset->status] ?? 'bg-gray-100 text-gray-600 ring-gray-200';
    $ticketStyle = fn ($s) => match ($s) {
        'open' => 'bg-yellow-50 text-yellow-700 ring-yellow-200',
        'in_progress' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'resolved' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'rejected', 'cancel' => 'bg-red-50 text-red-700 ring-red-200',
        default => 'bg-gray-100 text-gray-600 ring-gray-200',
    };
    $warranty = $asset->warranty_until;
    $warrantyText = $warranty
        ? ($warranty->isPast() ? 'Habis '.$warranty->format('d M Y') : 'Sisa '.(int) floor(now()->diffInDays($warranty)).' hari')
        : '-';
    $warrantyColor = match ($asset->warranty_state) {
        'habis' => 'text-red-600', 'segera' => 'text-amber-600', 'aktif' => 'text-emerald-600', default => 'text-gray-400',
    };
    $startTab = session('tab', $errors->hasAny(['problem', 'repaired_at', 'parts.*']) ? 'perbaikan' : ($errors->hasAny(['reason', 'mutated_at']) ? 'mutasi' : 'tiket'));
@endphp
<x-app-layout>
    <style>[x-cloak] { display: none !important; }</style>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-3">
            <nav class="flex items-center gap-2 text-sm text-gray-500 min-w-0">
                <a href="{{ route('assets.index') }}" class="hover:text-gray-800 shrink-0">Aset IT</a>
                <span>/</span>
                <span class="font-mono text-gray-800 truncate">{{ $asset->code }}</span>
            </nav>
            <a href="{{ route('assets.index') }}" class="px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm shrink-0">Kembali</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-5">
            @if (session('success'))
                <div class="p-3 rounded-lg bg-green-50 text-green-700 border border-green-200">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="p-3 rounded-lg bg-red-50 text-red-700 border border-red-200">{{ session('error') }}</div>
            @endif

            {{-- Hero --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 sm:p-6">
                <div class="flex flex-col sm:flex-row gap-5">
                    <div class="shrink-0">
                        @if ($asset->photo_url)
                            <a href="{{ $asset->photo_url }}" target="_blank" class="block">
                                <img src="{{ $asset->photo_url }}" alt="Foto {{ $asset->name }}" class="h-40 w-40 sm:h-36 sm:w-36 rounded-xl object-cover ring-1 ring-gray-200">
                            </a>
                        @else
                            <div class="h-40 w-40 sm:h-36 sm:w-36 rounded-xl bg-gray-50 ring-1 ring-gray-200 flex flex-col items-center justify-center text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25"/></svg>
                                <span class="text-xs mt-1">Belum ada foto</span>
                            </div>
                        @endif
                    </div>

                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ring-1 {{ $statusStyle }}">{{ $asset->status_label }}</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-gray-50 text-gray-600 ring-1 ring-gray-200">{{ $asset->type }}</span>
                            @if ($asset->department_name)
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium bg-indigo-50 text-indigo-700 ring-1 ring-indigo-200">{{ $asset->department_name }}</span>
                            @endif
                        </div>
                        <h1 class="mt-2 text-2xl font-semibold text-gray-900 break-words">{{ $asset->name }}</h1>
                        <div class="mt-1 text-sm text-gray-500">{{ trim($asset->brand.' '.$asset->model) ?: 'Merk dan model belum diisi' }}</div>

                        <div class="mt-3 flex flex-wrap items-center gap-2" x-data="{ copied: false }">
                            <span class="font-mono text-sm px-2.5 py-1 rounded-lg bg-gray-900 text-white tracking-wide">{{ $asset->code }}</span>
                            <button type="button" class="text-xs text-gray-500 hover:text-gray-800"
                                @click="navigator.clipboard.writeText(@js($asset->code)).then(() => { copied = true; setTimeout(() => copied = false, 1500) }).catch(() => {})"
                                x-text="copied ? 'Tersalin' : 'Salin kode'"></button>
                            @if ($asset->legacy_code)
                                <span class="text-xs text-gray-400">kode lama {{ $asset->legacy_code }}</span>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            @if ($openAudit)
                                <form method="POST" action="{{ route('assets.audits.verify', [$openAudit, $asset]) }}">@csrf
                                    <button class="px-4 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-400 text-sm font-medium">Verifikasi di Opname</button>
                                </form>
                            @endif
                            <a href="{{ route('welcome', ['open_ticket' => 1, 'asset' => $asset->code]) }}" class="px-4 py-2 rounded-lg bg-emerald-600 text-white hover:bg-emerald-500 text-sm font-medium">Buat Tiket</a>
                            <a href="{{ route('assets.labels', ['ids' => [$asset->id]]) }}" target="_blank" class="px-4 py-2 rounded-lg bg-white ring-1 ring-gray-300 hover:bg-gray-50 text-sm font-medium text-gray-700">Cetak Label QR</a>
                            <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm font-medium">Edit</a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ringkasan --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Tiket</div>
                    <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $asset->tickets->count() }}</div>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Perbaikan</div>
                    <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $asset->repairs->count() }}</div>
                    <div class="text-xs text-gray-400 mt-0.5">{{ $repairTotal > 0 ? $fmt($repairTotal) : 'Belum ada biaya' }}</div>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Mutasi</div>
                    <div class="text-2xl font-semibold text-gray-900 mt-1">{{ $asset->mutations->count() }}</div>
                </div>
                <div class="bg-white rounded-xl ring-1 ring-gray-100 shadow-sm p-4">
                    <div class="text-xs text-gray-500">Garansi</div>
                    <div class="text-lg font-semibold mt-1 {{ $warrantyColor }}">{{ $warrantyText }}</div>
                    @if ($warranty)<div class="text-xs text-gray-400 mt-0.5">sampai {{ $warranty->format('d M Y') }}</div>@endif
                </div>
            </div>

            {{-- Informasi + QR --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                <div class="lg:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-5">
                    @php
                        $groups = [
                            'Identitas' => [
                                'Jenis' => $asset->type,
                                'Merk' => $asset->brand,
                                'Model' => $asset->model,
                                'Serial Number' => $asset->serial_number,
                                'No. Aset Accurate' => $asset->accurate_no,
                            ],
                            'Penempatan' => [
                                'Departemen' => $asset->department_name,
                                'Cabang' => $asset->cabang,
                                'Lokasi' => $asset->location,
                                'Pengguna' => $asset->holder_name,
                            ],
                        ];
                    @endphp
                    @foreach ($groups as $title => $rows)
                        <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                            <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">{{ $title }}</div>
                            <dl class="space-y-2.5 text-sm">
                                @foreach ($rows as $label => $value)
                                    <div class="flex justify-between gap-4">
                                        <dt class="text-gray-500 shrink-0">{{ $label }}</dt>
                                        <dd class="text-gray-900 text-right break-words min-w-0 {{ $label === 'Serial Number' || $label === 'No. Aset Accurate' ? 'font-mono text-xs pt-0.5' : '' }}">{{ $value ?: '-' }}</dd>
                                    </div>
                                @endforeach
                            </dl>
                        </div>
                    @endforeach

                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Pembelian dan Garansi</div>
                        <dl class="space-y-2.5 text-sm">
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Tanggal beli</dt><dd class="text-gray-900">{{ $asset->purchase_date?->format('d M Y') ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Garansi sampai</dt><dd class="text-gray-900">{{ $warranty?->format('d M Y') ?? '-' }}</dd></div>
                            <div class="flex justify-between gap-4"><dt class="text-gray-500">Status garansi</dt><dd class="{{ $warrantyColor }} font-medium">{{ $warrantyText }}</dd></div>
                        </dl>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5">
                        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Catatan</div>
                        <p class="text-sm text-gray-700 whitespace-pre-line">{{ $asset->notes ?: 'Tidak ada catatan.' }}</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100 p-5 text-center self-start">
                    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">QR Aset</div>
                    <div id="qr" class="inline-block p-3 bg-white rounded-xl ring-1 ring-gray-200"></div>
                    <div class="mt-3 font-mono text-sm font-semibold text-gray-900">{{ $asset->code }}</div>
                    <div class="text-xs text-gray-400 break-all mt-1">{{ route('assets.scan', $asset->code) }}</div>
                </div>
            </div>

            {{-- Riwayat --}}
            <div class="bg-white rounded-2xl shadow-sm ring-1 ring-gray-100" x-data="{ tab: @js($startTab) }">
                <div class="border-b border-gray-100 px-3 sm:px-5 flex gap-1 overflow-x-auto">
                    @foreach (['tiket' => ['Tiket', $asset->tickets->count()], 'perbaikan' => ['Perbaikan', $asset->repairs->count()], 'mutasi' => ['Mutasi', $asset->mutations->count()]] as $key => [$label, $count])
                        <button type="button" @click="tab = '{{ $key }}'"
                            :class="tab === '{{ $key }}' ? 'border-blue-600 text-blue-700' : 'border-transparent text-gray-500 hover:text-gray-800'"
                            class="px-3 py-3 text-sm font-medium border-b-2 whitespace-nowrap">
                            {{ $label }} <span class="ml-1 text-xs rounded-full bg-gray-100 text-gray-600 px-2 py-0.5">{{ $count }}</span>
                        </button>
                    @endforeach
                </div>

                {{-- Tab Tiket --}}
                <div x-show="tab === 'tiket'" class="p-5">
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs uppercase tracking-wide text-gray-500">
                                    <th class="py-2 pr-4 font-medium">#</th>
                                    <th class="py-2 pr-4 font-medium">Judul</th>
                                    <th class="py-2 pr-4 font-medium">Pemohon</th>
                                    <th class="py-2 pr-4 font-medium">Status</th>
                                    <th class="py-2 pr-4 font-medium">Dikerjakan oleh</th>
                                    <th class="py-2 font-medium">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($asset->tickets as $t)
                                    <tr>
                                        <td class="py-2.5 pr-4 font-semibold text-blue-700 whitespace-nowrap">#{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}</td>
                                        <td class="py-2.5 pr-4 min-w-[12rem]">{{ $t->title }}</td>
                                        <td class="py-2.5 pr-4 whitespace-nowrap">{{ $t->nama }}</td>
                                        <td class="py-2.5 pr-4"><span class="inline-flex rounded-full px-2 py-0.5 text-xs font-medium ring-1 whitespace-nowrap {{ $ticketStyle($t->status) }}">{{ $t->status_label }}</span></td>
                                        <td class="py-2.5 pr-4 whitespace-nowrap">{{ $t->takenByUser?->name ?? '-' }}</td>
                                        <td class="py-2.5 text-gray-500 whitespace-nowrap">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="6" class="py-8 text-center text-gray-500">Belum ada tiket untuk perangkat ini.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab Perbaikan --}}
                <div x-show="tab === 'perbaikan'" x-cloak class="p-5" x-data="{ open: {{ $errors->hasAny(['problem', 'repaired_at', 'parts.*']) ? 'true' : 'false' }}, parts: [{ name: '', detail: '', qty: 1, cost: '' }] }">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="text-sm text-gray-500">
                            @if ($repairTotal > 0) Total biaya perbaikan <strong class="text-gray-900">{{ $fmt($repairTotal) }}</strong> @else Belum ada biaya tercatat @endif
                        </div>
                        <button type="button" @click="open = !open" class="px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-500 text-sm font-medium shrink-0" x-text="open ? 'Tutup' : 'Catat Perbaikan'"></button>
                    </div>

                <form x-show="open" x-cloak method="POST" action="{{ route('assets.repairs.store', $asset) }}"
                      class="border rounded-lg p-4 mb-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal perbaikan</label>
                        <input type="date" name="repaired_at" value="{{ old('repaired_at', now()->toDateString()) }}" required class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Terkait tiket (opsional)</label>
                        <select name="ticket_id" class="{{ $input }}">
                            <option value="">- Tidak ada -</option>
                            @foreach ($asset->tickets as $t)
                                <option value="{{ $t->id }}" @selected((string) old('ticket_id') === (string) $t->id)>#{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }} - {{ \Illuminate\Support\Str::limit($t->title, 50) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Masalah / kerusakan</label>
                        <textarea name="problem" rows="2" required class="{{ $input }}">{{ old('problem') }}</textarea>
                        @error('problem') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tindakan yang dilakukan</label>
                        <textarea name="action" rows="2" class="{{ $input }}">{{ old('action') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Vendor / servis luar (opsional)</label>
                        <input name="vendor" value="{{ old('vendor') }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Status aset setelah perbaikan</label>
                        <select name="status_after" class="{{ $input }}">
                            <option value="">Tidak diubah ({{ $asset->status_label }})</option>
                            @foreach (\App\Models\Asset::STATUSES as $k => $v)
                                <option value="{{ $k }}">{{ $v }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <div class="text-xs font-medium text-gray-600 mb-1">Part yang diganti / ditambah</div>
                        <template x-for="(p, i) in parts" :key="i">
                            <div class="grid grid-cols-12 gap-2 mb-2">
                                <input :name="`parts[${i}][name]`" x-model="p.name" placeholder="Nama part (mis. RAM, SSD, Adaptor)" class="{{ $input }} col-span-12 md:col-span-4">
                                <input :name="`parts[${i}][detail]`" x-model="p.detail" placeholder="Detail (mis. 8GB ke 16GB, SN baru)" class="{{ $input }} col-span-12 md:col-span-4">
                                <input type="number" min="1" :name="`parts[${i}][qty]`" x-model="p.qty" placeholder="Qty" class="{{ $input }} col-span-4 md:col-span-1">
                                <input type="number" min="0" step="1000" :name="`parts[${i}][cost]`" x-model="p.cost" placeholder="Biaya satuan (Rp)" class="{{ $input }} col-span-6 md:col-span-2">
                                <button type="button" @click="parts.splice(i, 1)" x-show="parts.length > 1" class="col-span-2 md:col-span-1 text-red-600 text-sm hover:underline">Hapus</button>
                            </div>
                        </template>
                        <button type="button" @click="parts.push({ name: '', detail: '', qty: 1, cost: '' })" class="text-sm text-blue-600 hover:underline">+ Tambah part</button>
                    </div>

                    <div class="md:col-span-2 flex justify-end">
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Simpan Perbaikan</button>
                    </div>
                </form>


                    <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                        @forelse ($asset->repairs as $r)
                            <li class="ml-5">
                                <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-amber-500 ring-4 ring-white"></span>
                                <div class="rounded-xl ring-1 ring-gray-200 p-4 text-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="font-semibold text-gray-900">{{ $r->repaired_at->format('d M Y') }}
                                            @if ($r->ticket)<span class="ml-2 text-xs font-normal text-blue-700">tiket #{{ str_pad($r->ticket->id, 3, '0', STR_PAD_LEFT) }}</span>@endif
                                            @if ($r->vendor)<span class="ml-2 text-xs font-normal text-gray-500">vendor: {{ $r->vendor }}</span>@endif
                                        </div>
                                        <div class="text-xs text-gray-400">dicatat {{ $r->creator?->name ?? '-' }}</div>
                                    </div>
                                    <div class="mt-2 text-gray-700"><span class="text-gray-500">Masalah:</span> {{ $r->problem }}</div>
                                    @if ($r->action)<div class="mt-1 text-gray-700"><span class="text-gray-500">Tindakan:</span> {{ $r->action }}</div>@endif
                                    @if ($r->parts->isNotEmpty())
                                        <div class="mt-3 overflow-x-auto">
                                            <table class="w-full text-xs">
                                                <thead class="text-gray-500"><tr><th class="text-left py-1 font-medium">Part</th><th class="text-left py-1 font-medium">Detail</th><th class="text-right py-1 font-medium">Qty</th><th class="text-right py-1 font-medium">Biaya</th></tr></thead>
                                                <tbody>
                                                    @foreach ($r->parts as $p)
                                                        <tr class="border-t border-gray-100">
                                                            <td class="py-1.5">{{ $p->name }}</td>
                                                            <td class="py-1.5">{{ $p->detail ?? '-' }}</td>
                                                            <td class="py-1.5 text-right">{{ $p->qty }}</td>
                                                            <td class="py-1.5 text-right whitespace-nowrap">{{ $p->cost !== null ? $fmt($p->qty * $p->cost) : '-' }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                        @if ($r->total_cost > 0)
                                            <div class="mt-1 text-right text-xs font-semibold text-gray-900">Total {{ $fmt($r->total_cost) }}</div>
                                        @endif
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="ml-5 text-sm text-gray-500 py-4">Belum ada riwayat perbaikan.</li>
                        @endforelse
                    </ol>
                </div>

                {{-- Tab Mutasi --}}
                <div x-show="tab === 'mutasi'" x-cloak class="p-5" x-data="{ open: {{ $errors->hasAny(['reason', 'mutated_at']) ? 'true' : 'false' }} }">
                    <div class="flex items-center justify-between gap-3 mb-4">
                        <div class="text-sm text-gray-500">Saat ini: {{ $asset->cabang ?? '-' }} · {{ $asset->location ?? '-' }} · {{ $asset->holder_name ?? '-' }}</div>
                        <button type="button" @click="open = !open" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 text-sm font-medium shrink-0" x-text="open ? 'Tutup' : 'Mutasi Aset'"></button>
                    </div>

                <form x-show="open" x-cloak method="POST" action="{{ route('assets.mutations.store', $asset) }}"
                      class="border rounded-lg p-4 mb-4 grid grid-cols-1 md:grid-cols-2 gap-3 bg-gray-50">
                    @csrf
                    <div class="md:col-span-2 text-xs text-gray-500">
                        Saat ini: {{ $asset->cabang ?? '-' }} · {{ $asset->location ?? '-' }} · {{ $asset->holder_name ?? '-' }}. Ubah ke tujuan baru di bawah.
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal mutasi</label>
                        <input type="date" name="mutated_at" value="{{ old('mutated_at', now()->toDateString()) }}" required class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Cabang tujuan</label>
                        <select name="cabang" class="{{ $input }}">
                            <option value="">-</option>
                            @foreach (\App\Models\Asset::CABANG as $c)
                                <option value="{{ $c }}" @selected(old('cabang', $asset->cabang) === $c)>{{ $c }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Lokasi tujuan</label>
                        <input name="location" value="{{ old('location', $asset->location) }}" class="{{ $input }}">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Pengguna baru</label>
                        <select name="user_id" class="{{ $input }}">
                            <option value="">- Pilih user -</option>
                            @foreach ($users as $u)
                                <option value="{{ $u->id }}" @selected((string) old('user_id', $asset->user_id) === (string) $u->id)>{{ $u->name }} ({{ $u->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Atau pengguna tanpa akun</label>
                        <input name="user_name" value="{{ old('user_name', $asset->user_name) }}" class="{{ $input }}" placeholder="mis. Ruang Meeting">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Alasan mutasi</label>
                        <input name="reason" value="{{ old('reason') }}" required class="{{ $input }}" placeholder="mis. Karyawan pindah divisi">
                        @error('reason') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                    </div>
                    <div class="md:col-span-2 flex justify-end">
                        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Simpan Mutasi</button>
                    </div>
                </form>


                    <ol class="relative border-l border-gray-200 ml-2 space-y-5">
                        @forelse ($asset->mutations as $mu)
                            <li class="ml-5">
                                <span class="absolute -left-1.5 mt-1.5 h-3 w-3 rounded-full bg-indigo-500 ring-4 ring-white"></span>
                                <div class="rounded-xl ring-1 ring-gray-200 p-4 text-sm">
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <div class="font-semibold text-gray-900">{{ $mu->mutated_at->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-400">dicatat {{ $mu->creator?->name ?? '-' }}</div>
                                    </div>
                                    <div class="mt-2 grid grid-cols-1 sm:grid-cols-[1fr_auto_1fr] gap-2 sm:items-center">
                                        <div class="rounded-lg bg-gray-50 px-3 py-2">
                                            <div class="text-xs text-gray-500">Dari</div>
                                            <div class="text-gray-800">{{ $mu->from_cabang ?? '-' }} · {{ $mu->from_location ?? '-' }}</div>
                                            <div class="text-gray-600 text-xs">{{ $mu->from_user ?? '-' }}</div>
                                        </div>
                                        <div class="text-gray-400 text-center">&rarr;</div>
                                        <div class="rounded-lg bg-indigo-50 px-3 py-2">
                                            <div class="text-xs text-indigo-600">Ke</div>
                                            <div class="text-gray-800">{{ $mu->to_cabang ?? '-' }} · {{ $mu->to_location ?? '-' }}</div>
                                            <div class="text-gray-600 text-xs">{{ $mu->to_user ?? '-' }}</div>
                                        </div>
                                    </div>
                                    @if ($mu->reason)<div class="mt-2 text-gray-700"><span class="text-gray-500">Alasan:</span> {{ $mu->reason }}</div>@endif
                                </div>
                            </li>
                        @empty
                            <li class="ml-5 text-sm text-gray-500 py-4">Belum ada mutasi.</li>
                        @endforelse
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        new QRCode(document.getElementById('qr'), {
            text: @js(route('assets.scan', $asset->code)),
            width: 176,
            height: 176,
            correctLevel: QRCode.CorrectLevel.M
        });
    </script>
</x-app-layout>

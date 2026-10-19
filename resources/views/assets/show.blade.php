<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $asset->name }}</h2>
                <div class="text-sm text-gray-500 mt-0.5">{{ $asset->code }} · {{ $asset->type }}</div>
            </div>
            <div class="flex gap-2 shrink-0">
                @if ($openAudit)
                    <form method="POST" action="{{ route('assets.audits.verify', [$openAudit, $asset]) }}">@csrf
                        <button class="px-4 py-2 rounded-lg bg-amber-500 text-white hover:bg-amber-400 text-sm">Verifikasi di Opname</button>
                    </form>
                @endif
                <a href="{{ route('welcome', ['open_ticket' => 1, 'asset' => $asset->code]) }}" class="px-4 py-2 rounded-lg bg-green-600 text-white hover:bg-green-500 text-sm">Buat Tiket</a>
                <a href="{{ route('assets.labels', ['ids' => [$asset->id]]) }}" target="_blank" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 text-sm">Cetak Label QR</a>
                <a href="{{ route('assets.edit', $asset) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500 text-sm">Edit</a>
                <a href="{{ route('assets.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">Kembali</a>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
                    <div class="font-semibold text-gray-800 mb-3">Detail Perangkat</div>
                    @if ($asset->photo_url)
                        <a href="{{ $asset->photo_url }}" target="_blank">
                            <img src="{{ $asset->photo_url }}" alt="Foto {{ $asset->name }}" class="mb-4 max-h-64 rounded-lg border object-contain">
                        </a>
                    @endif
                    <dl class="grid grid-cols-3 gap-y-2 text-sm">
                        @php
                            $rows = [
                                'Status' => $asset->status_label,
                                'Merk / Model' => trim($asset->brand.' '.$asset->model) ?: '-',
                                'No. Aset Accurate' => $asset->accurate_no ?? '-',
                                'Serial Number' => $asset->serial_number ?? '-',
                                'Cabang' => $asset->cabang ?? '-',
                                'Lokasi' => $asset->location ?? '-',
                                'Pengguna' => $asset->holder_name ?? '-',
                                'Tanggal Beli' => $asset->purchase_date?->format('d M Y') ?? '-',
                                'Garansi Sampai' => $asset->warranty_until?->format('d M Y') ?? '-',
                            ];
                        @endphp
                        @foreach ($rows as $label => $value)
                            <dt class="font-medium text-gray-600">{{ $label }}</dt>
                            <dd class="col-span-2">
                                {{ $value }}
                                @if ($label === 'Garansi Sampai' && $asset->warranty_state === 'habis')
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded bg-red-50 text-red-700">Garansi habis</span>
                                @elseif ($label === 'Garansi Sampai' && $asset->warranty_state === 'segera')
                                    <span class="ml-2 text-xs px-2 py-0.5 rounded bg-amber-50 text-amber-700">Segera habis</span>
                                @endif
                            </dd>
                        @endforeach
                        @if ($asset->notes)
                            <dt class="font-medium text-gray-600">Catatan</dt>
                            <dd class="col-span-2 whitespace-pre-line">{{ $asset->notes }}</dd>
                        @endif
                    </dl>
                </div>

                <div class="bg-white rounded-xl shadow p-6 text-center">
                    <div class="font-semibold text-gray-800 mb-3">QR Aset</div>
                    <div id="qr" class="inline-block p-2 bg-white"></div>
                    <div class="mt-2 text-sm font-semibold">{{ $asset->code }}</div>
                    <div class="text-xs text-gray-500 break-all mt-1">{{ route('assets.scan', $asset->code) }}</div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="font-semibold text-gray-800 mb-3">Riwayat Tiket ({{ $asset->tickets->count() }})</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-3 py-2">#</th>
                                <th class="text-left px-3 py-2">Judul</th>
                                <th class="text-left px-3 py-2">Pemohon</th>
                                <th class="text-left px-3 py-2">Status</th>
                                <th class="text-left px-3 py-2">Dikerjakan Oleh</th>
                                <th class="text-left px-3 py-2">Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($asset->tickets as $t)
                                <tr>
                                    <td class="px-3 py-2 font-semibold text-blue-700">#{{ str_pad($t->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td class="px-3 py-2">{{ $t->title }}</td>
                                    <td class="px-3 py-2">{{ $t->nama }}</td>
                                    <td class="px-3 py-2">{{ $t->status_label }}</td>
                                    <td class="px-3 py-2">{{ $t->takenByUser?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $t->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-6 text-center text-gray-500">Belum ada tiket untuk perangkat ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Riwayat Perbaikan --}}
    <div class="pb-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-4">
            @php
                $repairTotal = $asset->repairs->sum(fn ($r) => $r->total_cost);
                $fmt = fn ($n) => 'Rp '.number_format($n, 0, ',', '.');
                $input = 'w-full rounded-lg border-gray-300 text-sm';
            @endphp

            <div class="bg-white rounded-xl shadow p-6" x-data="{ open: {{ $errors->hasAny(['problem', 'repaired_at', 'parts.*']) ? 'true' : 'false' }}, parts: [{ name: '', detail: '', qty: 1, cost: '' }] }">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="font-semibold text-gray-800">Riwayat Perbaikan ({{ $asset->repairs->count() }})</div>
                        @if ($repairTotal > 0)
                            <div class="text-xs text-gray-500 mt-0.5">Total biaya perbaikan: <strong>{{ $fmt($repairTotal) }}</strong></div>
                        @endif
                    </div>
                    <button type="button" @click="open = !open" class="px-4 py-2 rounded-lg bg-amber-600 text-white hover:bg-amber-500 text-sm" x-text="open ? 'Tutup' : 'Catat Perbaikan'"></button>
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

                <div class="space-y-3">
                    @forelse ($asset->repairs as $r)
                        <div class="border rounded-lg p-4 text-sm">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <div class="font-medium text-gray-800">{{ $r->repaired_at->format('d M Y') }}
                                    @if ($r->ticket)<span class="ml-2 text-xs text-blue-700">tiket #{{ str_pad($r->ticket->id, 3, '0', STR_PAD_LEFT) }}</span>@endif
                                    @if ($r->vendor)<span class="ml-2 text-xs text-gray-500">vendor: {{ $r->vendor }}</span>@endif
                                </div>
                                <div class="text-xs text-gray-500">dicatat {{ $r->creator?->name ?? '-' }}</div>
                            </div>
                            <div class="mt-2"><span class="text-gray-500">Masalah:</span> {{ $r->problem }}</div>
                            @if ($r->action)<div class="mt-1"><span class="text-gray-500">Tindakan:</span> {{ $r->action }}</div>@endif
                            @if ($r->parts->isNotEmpty())
                                <table class="mt-2 w-full text-xs">
                                    <thead class="text-gray-500"><tr><th class="text-left py-1">Part</th><th class="text-left py-1">Detail</th><th class="text-right py-1">Qty</th><th class="text-right py-1">Biaya</th></tr></thead>
                                    <tbody>
                                        @foreach ($r->parts as $p)
                                            <tr class="border-t">
                                                <td class="py-1">{{ $p->name }}</td>
                                                <td class="py-1">{{ $p->detail ?? '-' }}</td>
                                                <td class="py-1 text-right">{{ $p->qty }}</td>
                                                <td class="py-1 text-right">{{ $p->cost !== null ? $fmt($p->qty * $p->cost) : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @if ($r->total_cost > 0)
                                    <div class="mt-1 text-right text-xs font-semibold">Total: {{ $fmt($r->total_cost) }}</div>
                                @endif
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-sm text-gray-500 py-4">Belum ada riwayat perbaikan.</div>
                    @endforelse
                </div>
            </div>

            {{-- Mutasi Aset --}}
            <div class="bg-white rounded-xl shadow p-6" x-data="{ open: {{ $errors->hasAny(['reason', 'mutated_at']) ? 'true' : 'false' }} }">
                <div class="flex items-center justify-between mb-3">
                    <div class="font-semibold text-gray-800">Mutasi Aset ({{ $asset->mutations->count() }})</div>
                    <button type="button" @click="open = !open" class="px-4 py-2 rounded-lg bg-indigo-600 text-white hover:bg-indigo-500 text-sm" x-text="open ? 'Tutup' : 'Mutasi Aset'"></button>
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

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-3 py-2">Tanggal</th>
                                <th class="text-left px-3 py-2">Dari</th>
                                <th class="text-left px-3 py-2">Ke</th>
                                <th class="text-left px-3 py-2">Alasan</th>
                                <th class="text-left px-3 py-2">Dicatat oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($asset->mutations as $mu)
                                <tr>
                                    <td class="px-3 py-2 whitespace-nowrap">{{ $mu->mutated_at->format('d M Y') }}</td>
                                    <td class="px-3 py-2">{{ $mu->from_cabang ?? '-' }} · {{ $mu->from_location ?? '-' }} · {{ $mu->from_user ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $mu->to_cabang ?? '-' }} · {{ $mu->to_location ?? '-' }} · {{ $mu->to_user ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $mu->reason ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $mu->creator?->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada mutasi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
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

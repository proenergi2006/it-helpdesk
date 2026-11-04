<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cek Kondisi {{ $asset->code }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    @php
        $done = session('checked') ? $asset->checks->firstWhere('id', session('checked')) : null;
        $roomDefault = old('room', $asset->location);
        $cabangDefault = old('cabang', $asset->cabang);
    @endphp
    <div class="max-w-md mx-auto px-4 py-8 space-y-5">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-10 w-auto">
            <div>
                <div class="font-bold text-blue-700 leading-tight">Cek Kondisi Aset</div>
                <div class="text-xs text-gray-500">Service Management · Pro Energi</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-5">
            <div class="text-xs uppercase tracking-wide text-gray-500">Aset</div>
            <div class="mt-1 text-lg font-semibold text-gray-900">{{ $asset->name }}</div>
            <div class="font-mono text-sm text-gray-600">{{ $asset->code }}</div>
            <div class="mt-1 text-sm text-gray-500">Pemegang: {{ $asset->holder_name ?? '-' }}</div>
        </div>

        @if ($done)
            <div class="bg-white rounded-2xl shadow p-6 text-center space-y-3">
                <div class="mx-auto h-12 w-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center text-2xl">✓</div>
                <div class="font-semibold text-gray-900">Cek kondisi tersimpan</div>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium ring-1 {{ $done->condition_badge }}">{{ $done->condition_label }}</span>
                @if ($done->location_mismatch)
                    <p class="text-sm text-amber-700 bg-amber-50 rounded-lg p-3">Lokasi yang Anda isi berbeda dengan data aset. Perbedaan ini dicatat untuk ditindaklanjuti.</p>
                @endif
                @if (in_array($done->condition, ['poor', 'need_repair']))
                    <a href="{{ route('assets.scan.ticket', $asset->code) }}" class="block w-full bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-xl font-semibold">Buat Tiket Perbaikan</a>
                @endif
                <a href="{{ route('assets.scan.check', $asset->code) }}" class="block text-sm text-blue-600 hover:underline">Cek ulang</a>
            </div>
        @else
            <form method="POST" action="{{ route('assets.scan.check.store', $asset->code) }}" class="bg-white rounded-2xl shadow p-5 space-y-5" x-data="{ c: @js(old('condition')) }">
                @csrf

                <div>
                    <div class="font-semibold text-gray-900 mb-2">Bagaimana kondisinya?</div>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ([['good', 'Baik', 'green'], ['poor', 'Buruk', 'red'], ['need_repair', 'Perlu perbaikan', 'yellow']] as [$val, $label, $color])
                            @php $ring = ['green' => 'bg-green-600', 'red' => 'bg-red-600', 'yellow' => 'bg-yellow-400 text-gray-900'][$color]; @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="condition" value="{{ $val }}" x-model="c" class="sr-only" required>
                                <span class="block text-center rounded-xl px-2 py-4 text-sm font-semibold text-white {{ $ring }} ring-offset-2 transition"
                                      :class="c === '{{ $val }}' ? 'ring-4 ring-blue-500' : 'opacity-70'">{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('condition') <div class="text-sm text-red-600 mt-2">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-900 mb-1" for="cabang">Di mana lokasinya?</label>
                    <select id="cabang" name="cabang" required class="w-full rounded-xl border-gray-300">
                        <option value="">Pilih cabang</option>
                        @foreach (\App\Models\Asset::CABANG as $cab)
                            <option value="{{ $cab }}" @selected($cabangDefault === $cab)>{{ $cab }}</option>
                        @endforeach
                    </select>
                    @error('cabang') <div class="text-sm text-red-600 mt-1">{{ $message }}</div> @enderror
                </div>

                <div>
                    <label class="block font-semibold text-gray-900 mb-1" for="room">Di ruangan mana?</label>
                    <input id="room" name="room" value="{{ $roomDefault }}" class="w-full rounded-xl border-gray-300" placeholder="contoh: Working Station, Lantai 2">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1" for="note">Catatan <span class="text-gray-400 font-normal">(opsional)</span></label>
                    <textarea id="note" name="note" rows="2" class="w-full rounded-xl border-gray-300" placeholder="contoh: layar retak di pojok kiri">{{ old('note') }}</textarea>
                </div>

                <button type="submit" class="w-full bg-sky-500 hover:bg-sky-600 text-white px-4 py-3 rounded-xl font-semibold shadow">Submit</button>
                <p class="text-xs text-gray-500 text-center">Dicatat atas nama {{ auth()->user()->name }}.</p>
            </form>

            @if ($last)
                <p class="text-xs text-gray-500 text-center">
                    Terakhir dicek {{ $last->created_at->translatedFormat('d M Y H:i') }}: {{ $last->condition_label }}@if ($last->checker) oleh {{ $last->checker->name }}@endif.
                </p>
            @endif
        @endif

        <div class="text-center space-y-2">
            @if ($asset->isManageableBy(auth()->user()))
                <a href="{{ route('assets.show', $asset) }}" class="block w-full text-center bg-white ring-1 ring-gray-300 hover:bg-gray-50 text-gray-800 px-4 py-3 rounded-xl font-semibold">Lihat Detail Aset</a>
            @else
                <a href="{{ route('assets.scan', $asset->code) }}" class="text-sm text-blue-600 hover:underline">Kembali ke info aset</a>
            @endif
        </div>
    </div>
    @vite('resources/js/app.js')
</body>
</html>

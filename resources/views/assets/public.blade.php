<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Aset {{ $asset->code }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 min-h-screen font-sans">
    <div class="max-w-md mx-auto px-4 py-8 space-y-6">
        <div class="flex items-center gap-3">
            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Pro Energi" class="h-10 w-auto">
            <div>
                <div class="font-bold text-blue-700 leading-tight">Helpdesk IT</div>
                <div class="text-xs text-gray-500">Pro Energi</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow p-6 space-y-4">
            <div class="text-xs uppercase tracking-wide text-gray-500">Informasi Aset</div>

            @if ($asset->photo_url)
                <img src="{{ $asset->photo_url }}" alt="Foto {{ $asset->name }}" class="w-full max-h-72 rounded-xl border object-contain bg-gray-50">
            @endif

            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-gray-500">Kode Aset</dt>
                    <dd class="text-lg font-mono font-semibold text-gray-900">{{ $asset->code }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Nama Aset</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $asset->name }}</dd>
                </div>
                <div>
                    <dt class="text-gray-500">Pemegang</dt>
                    <dd class="text-base font-medium text-gray-900">{{ $asset->holder_name ?? '-' }}</dd>
                </div>
            </dl>

            <a href="{{ route('assets.scan.ticket', $asset->code) }}"
               class="block w-full text-center bg-green-600 hover:bg-green-700 text-white px-4 py-3 rounded-xl font-semibold">
                Laporkan Masalah Perangkat Ini
            </a>
            @guest
                <p class="text-xs text-gray-500 text-center">Anda akan diminta login terlebih dahulu.</p>
            @endguest
        </div>

        <div class="text-center">
            <a href="{{ route('welcome') }}" class="text-sm text-blue-600 hover:underline">Ke halaman utama Helpdesk</a>
        </div>
    </div>
</body>
</html>

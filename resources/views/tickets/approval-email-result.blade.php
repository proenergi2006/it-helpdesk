<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hasil Approval</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md bg-white rounded-xl shadow p-6 text-center">
        <div class="text-lg font-semibold {{ $ok ? 'text-green-700' : 'text-amber-700' }}">{{ $ok ? 'Berhasil' : 'Tidak diproses' }}</div>
        <p class="mt-2 text-sm text-gray-700">{{ $message }}</p>
    </div>
</body>
</html>

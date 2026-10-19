<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Approval Ticket</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 min-h-screen py-8 px-4">
<div class="mx-auto w-full max-w-2xl space-y-4">
    <div class="bg-white rounded-xl shadow p-6">
        <h1 class="text-xl font-semibold text-gray-900">Approval Ticket #{{ str_pad($ticket->id, 3, '0', STR_PAD_LEFT) }}</h1>
        <p class="text-sm text-gray-500 mt-1">Anda masuk sebagai approver: <strong>{{ $approver->name }}</strong></p>

        <dl class="mt-4 grid grid-cols-3 gap-y-2 text-sm">
            <dt class="font-medium text-gray-600">Pemohon</dt><dd class="col-span-2">{{ $ticket->nama }} ({{ $ticket->cabang }})</dd>
            <dt class="font-medium text-gray-600">Kategori</dt><dd class="col-span-2 capitalize">{{ $ticket->category }}</dd>
            <dt class="font-medium text-gray-600">Klasifikasi</dt><dd class="col-span-2">{{ $ticket->klasifikasi }}</dd>
            <dt class="font-medium text-gray-600">Priority</dt><dd class="col-span-2">{{ $ticket->priority }}</dd>
            @if ($ticket->asset)
                <dt class="font-medium text-gray-600">Aset</dt><dd class="col-span-2">{{ $ticket->asset->code }} · {{ $ticket->asset->name }}</dd>
            @endif
            <dt class="font-medium text-gray-600">Judul</dt><dd class="col-span-2">{{ $ticket->title }}</dd>
            <dt class="font-medium text-gray-600">Deskripsi</dt><dd class="col-span-2 whitespace-pre-line">{{ $ticket->description }}</dd>
        </dl>
    </div>

    @if ($ticket->status !== 'pending_approval')
        <div class="rounded-xl border border-amber-300 bg-amber-50 p-4 text-sm text-amber-800">
            Ticket ini sudah diproses (status: <strong>{{ $ticket->status_label }}</strong>).
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2">
            <form method="POST" action="{{ request()->fullUrl() }}" class="bg-white rounded-xl shadow p-5 space-y-3">
                @csrf
                <input type="hidden" name="decision" value="approve">
                <div class="font-semibold text-green-700">Setujui</div>
                <label class="flex items-start gap-2 text-sm text-gray-700">
                    <input type="checkbox" name="create_project" value="1" class="mt-0.5 rounded">
                    <span>Buat Project + SDLC <span class="text-gray-400">(aplikasi baru / perubahan mayor)</span></span>
                </label>
                <button class="w-full rounded-lg bg-green-600 px-4 py-2 text-white font-semibold hover:bg-green-500">Setujui Ticket</button>
            </form>

            <form method="POST" action="{{ request()->fullUrl() }}" class="bg-white rounded-xl shadow p-5 space-y-3">
                @csrf
                <input type="hidden" name="decision" value="reject">
                <div class="font-semibold text-red-700">Tolak</div>
                <input type="text" name="approval_note" required maxlength="500" placeholder="Alasan penolakan"
                       class="w-full rounded-lg border-gray-300 text-sm">
                <button class="w-full rounded-lg bg-red-600 px-4 py-2 text-white font-semibold hover:bg-red-500">Tolak Ticket</button>
            </form>
        </div>
    @endif
</div>
</body>
</html>

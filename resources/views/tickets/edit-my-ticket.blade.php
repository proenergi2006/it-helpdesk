<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Ticket Saya</title>
    <link rel="icon" type="image/png" href="{{ asset('images/proenergi-logo.png') }}">
    @vite('resources/css/app.css')
</head>
<body class="bg-slate-100 font-sans text-slate-800">

    <div class="min-h-screen">
        <div class="bg-gradient-to-r from-orange-500 via-orange-400 to-amber-400 shadow-lg">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6">
                <div class="flex items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <div class="h-14 w-14 rounded-2xl bg-white/90 flex items-center justify-center shadow">
                            <img src="{{ asset('images/proenergi-logo.png') }}" alt="Logo Pro Energi" class="h-10 w-auto object-contain">
                        </div>
                        <div class="text-white">
                            <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">Edit Ticket</h1>
                            <p class="text-white/90 text-sm md:text-base">Anda hanya dapat mengubah ticket yang masih berstatus open</p>
                        </div>
                    </div>

                    <a href="{{ route('tickets.my') }}"
                        class="inline-flex items-center justify-center rounded-xl bg-slate-700 hover:bg-slate-800 text-white px-5 py-3 font-semibold shadow transition">
                        Kembali
                    </a>
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-8">
            @if ($errors->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-4 text-rose-700">
                    <div class="font-semibold mb-2">Ada data yang belum sesuai:</div>
                    <ul class="list-disc pl-5 text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-8">
                <form method="POST" action="{{ route('tickets.my.update', $ticket->id) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Nama</label>
                            <input type="text" value="{{ auth()->user()->name }}"
                                class="w-full rounded-2xl border-slate-300 bg-slate-100" disabled>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}"
                                class="w-full rounded-2xl border-slate-300 bg-slate-100" disabled>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cabang</label>
                            <select name="cabang" class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                @foreach (['Head Office','Jakarta','Surabaya','Samarinda','Palembang','Banjarmasin','Pontianak','Sulawesi'] as $cabang)
                                    <option value="{{ $cabang }}" {{ old('cabang', $ticket->cabang) === $cabang ? 'selected' : '' }}>
                                        {{ $cabang }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Kategori</label>
                            <select name="category" class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                <option value="software" {{ old('category', $ticket->category) === 'software' ? 'selected' : '' }}>Software</option>
                                <option value="hardware" {{ old('category', $ticket->category) === 'hardware' ? 'selected' : '' }}>Hardware</option>
                                <option value="network&multimedia" {{ old('category', $ticket->category) === 'network&multimedia' ? 'selected' : '' }}>Network & Multimedia</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Klasifikasi</label>
                            <select name="klasifikasi" class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                <option value="Incident" {{ old('klasifikasi', $ticket->klasifikasi) === 'Incident' ? 'selected' : '' }}>Incident</option>
                                <option value="Request" {{ old('klasifikasi', $ticket->klasifikasi) === 'Request' ? 'selected' : '' }}>Request</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Priority</label>
                            <select name="priority" class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                                <option value="Low" {{ old('priority', $ticket->priority) === 'Low' ? 'selected' : '' }}>Low</option>
                                <option value="Medium" {{ old('priority', $ticket->priority) === 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Critical" {{ old('priority', $ticket->priority) === 'Critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Judul Ticket</label>
                            <input type="text" name="title" value="{{ old('title', $ticket->title) }}"
                                class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Deskripsi Masalah</label>
                            <textarea name="description" rows="6"
                                class="w-full rounded-2xl border-slate-300 focus:border-orange-400 focus:ring-orange-400">{{ old('description', $ticket->description) }}</textarea>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-4">
                        <button type="submit"
                            class="inline-flex items-center justify-center rounded-xl bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 font-semibold shadow transition">
                            Simpan Perubahan
                        </button>

                        <a href="{{ route('tickets.my') }}"
                            class="inline-flex items-center justify-center rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 px-6 py-3 font-semibold transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

</body>
</html>
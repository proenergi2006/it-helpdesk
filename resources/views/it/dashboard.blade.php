@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto py-6 px-4">
        <h1 class="text-2xl font-bold text-blue-700 mb-4">🎧 Dashboard Tim IT</h1>
        <p class="text-gray-600 mb-6">Selamat datang, {{ Auth::user()->name }} — role: {{ Auth::user()->role }}</p>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-blue-100 p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-blue-700">Ticket Open</h2>
                <p class="text-3xl font-bold text-blue-800">{{ $openCount }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-yellow-700">Dalam Proses</h2>
                <p class="text-3xl font-bold text-yellow-800">{{ $inProgressCount }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-green-700">Selesai</h2>
                <p class="text-3xl font-bold text-green-800">{{ $resolvedCount }}</p>
            </div>
        </div>

        <div class="mt-6">
            <a href="{{ route('tickets.manage') }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Kelola Ticket</a>
        </div>
    </div>
@endsection

@props(['title' => 'Judul Grafik'])

<div class="bg-white rounded-2xl shadow-xl p-6 transition-all duration-500 hover:shadow-2xl">
    <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
        {{ $title }}
    </h3>
    <div class="relative">
        {{ $slot }}
    </div>
</div>

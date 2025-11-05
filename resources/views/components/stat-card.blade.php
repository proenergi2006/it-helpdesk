@props(['color' => 'from-blue-600 to-blue-400', 'label' => 'Label', 'value' => 0])

<div
    class="col-span-12 sm:col-span-6 lg:col-span-3 bg-gradient-to-br {{ $color }} text-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
    <p class="text-sm opacity-80">{{ $label }}</p>
    <h3 class="text-4xl font-bold mt-2">{{ $value }}</h3>
</div>

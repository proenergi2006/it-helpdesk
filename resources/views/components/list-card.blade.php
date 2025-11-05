@props(['title' => 'Daftar', 'items' => [], 'color' => 'blue'])

<div class="bg-white rounded-2xl shadow-xl p-6 hover:shadow-2xl transition-all">
    <h3 class="text-lg font-semibold text-{{ $color }}-700 mb-4">{{ $title }}</h3>
    <ul class="divide-y divide-gray-200">
        @foreach ($items as $i)
            @php
                $name = $i['name'] ?? ($i->category ?? 'Tidak diketahui');
                $value = $i['total'] ?? ($i->avg_hours ?? 0);
            @endphp
            <li class="flex justify-between py-2">
                <span class="font-medium text-gray-700 capitalize">{{ $name }}</span>
                <span class="font-bold text-{{ $color }}-600">{{ $value }}</span>
            </li>
        @endforeach
    </ul>
</div>

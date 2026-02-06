<x-app-layout>
    <x-slot name="header">
        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900">{{ $meeting->title }}</h2>
                <div class="text-sm text-gray-500 mt-0.5">
                    {{ $meeting->meeting_at->format('d M Y H:i') }} · {{ $meeting->location ?? '-' }}
                </div>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('meetings.export.pdf', $meeting) }}"
   class="px-4 py-2 rounded-lg bg-gray-900 text-white hover:bg-gray-800">
   Export PDF
</a>

                <a href="{{ route('meetings.edit', $meeting) }}" class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-500">Edit</a>
                <a href="{{ route('meetings.index') }}" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200">Back</a>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">

            <div class="bg-white rounded-xl shadow p-6">
                <div class="text-sm font-medium text-gray-700 mb-2">Attendees</div>
                <div class="flex flex-wrap gap-1">
                    @forelse($meeting->attendees as $a)
                        <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">{{ $a->name }}</span>
                    @empty
                        <span class="text-gray-500">-</span>
                    @endforelse
                </div>

                <hr class="my-5">

                <div class="text-sm font-medium text-gray-700 mb-2">Notes (MoM)</div>
                <div class="prose max-w-none">{!! $meeting->notes ?: '<span class="text-gray-500">-</span>' !!}</div>
            </div>

            <div class="bg-white rounded-xl shadow p-6">
                <div class="font-semibold text-gray-800 mb-3">Action Items</div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="text-left px-3 py-2">Task</th>
                                <th class="text-left px-3 py-2">Owner</th>
                                <th class="text-left px-3 py-2">Due</th>
                                <th class="text-left px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($meeting->actionItems as $ai)
                                <tr>
                                    <td class="px-3 py-2">{{ $ai->task }}</td>
                                    <td class="px-3 py-2">{{ $ai->owner?->name ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $ai->due_date?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $ai->status }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada action item.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

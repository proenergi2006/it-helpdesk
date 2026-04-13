<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            ⏱ SLA Report
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FILTER --}}
            <div class="bg-white p-6 rounded-2xl shadow mb-6">
                <form method="GET" action="{{ route('reports.sla') }}"
                    class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                    <div class="md:col-span-3">
                        <label class="block text-sm text-slate-600 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" value="{{ $start }}"
                            class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                    </div>

                    <div class="md:col-span-3">
                        <label class="block text-sm text-slate-600 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" value="{{ $end }}"
                            class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm text-slate-600 mb-1">Kategori</label>
                        <select name="category"
                            class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua</option>
                            <option value="software" {{ $category == 'software' ? 'selected' : '' }}>Software</option>
                            <option value="hardware" {{ $category == 'hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="network&multimedia" {{ $category == 'network&multimedia' ? 'selected' : '' }}>
                                Network & Multimedia
                            </option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm text-slate-600 mb-1">Status</label>
                        <select name="status"
                            class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua</option>
                            <option value="open" {{ $status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $status == 'in_progress' ? 'selected' : '' }}>In Progress
                            </option>
                            <option value="resolved" {{ $status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                            <option value="cancel" {{ $status == 'cancel' ? 'selected' : '' }}>Cancel</option>
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <button
                            class="w-full bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-xl font-semibold shadow">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- SUMMARY CARD --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Total Ticket</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalTickets }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Response On Time</div>
                    <div class="text-3xl font-extrabold text-emerald-500 mt-2">{{ $responseOnTime }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Response Late</div>
                    <div class="text-3xl font-extrabold text-rose-500 mt-2">{{ $responseLate }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Resolution On Time</div>
                    <div class="text-3xl font-extrabold text-sky-500 mt-2">{{ $resolutionOnTime }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Resolution Late</div>
                    <div class="text-3xl font-extrabold text-amber-500 mt-2">{{ $resolutionLate }}</div>
                </div>
            </div>

            {{-- TABLE DETAIL --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Detail SLA Ticket</h3>
                    <p class="text-sm text-slate-500">Monitoring kecepatan response dan penyelesaian ticket</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-4 py-4 text-left font-bold">Ticket</th>
                                <th class="px-4 py-4 text-left font-bold">Kategori</th>
                                <th class="px-4 py-4 text-left font-bold">Priority</th>
                                <th class="px-4 py-4 text-left font-bold">Dikerjakan Oleh</th>
                                <th class="px-4 py-4 text-left font-bold">Created</th>
                                <th class="px-4 py-4 text-left font-bold">Taken</th>
                                <th class="px-4 py-4 text-left font-bold">Resolved</th>
                                <th class="px-4 py-4 text-center font-bold">SLA Response</th>
                                <th class="px-4 py-4 text-center font-bold">SLA Resolution</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse ($tickets as $row)
                                @php
                                    $responseSeconds =
                                        $row->taken_at && $row->created_at
                                            ? $row->created_at->diffInSeconds($row->taken_at)
                                            : null;

                                    $resolutionSeconds =
                                        $row->resolved_at && $row->created_at
                                            ? $row->created_at->diffInSeconds($row->resolved_at)
                                            : null;

                                    $responseOnTimeStatus =
                                        $responseSeconds !== null && $row->sla_response_minutes
                                            ? $responseSeconds <= $row->sla_response_minutes * 60
                                            : null;

                                    $resolutionOnTimeStatus =
                                        $resolutionSeconds !== null && $row->sla_resolution_minutes
                                            ? $resolutionSeconds <= $row->sla_resolution_minutes * 60
                                            : null;

                                    $formatDuration = function ($seconds) {
                                        if ($seconds === null) {
                                            return '-';
                                        }

                                        $hours = floor($seconds / 3600);
                                        $minutes = floor(($seconds % 3600) / 60);
                                        $secs = $seconds % 60;

                                        if ($hours > 0) {
                                            return trim($hours . ' jam ' . $minutes . ' menit');
                                        }

                                        if ($minutes > 0) {
                                            return trim($minutes . ' menit ' . ($secs > 0 ? $secs . ' detik' : ''));
                                        }

                                        return $secs . ' detik';
                                    };

                                    $formatTarget = function ($minutes) {
                                        if (!$minutes) {
                                            return '-';
                                        }

                                        if ($minutes >= 60) {
                                            $hours = floor($minutes / 60);
                                            $mins = $minutes % 60;

                                            return trim($hours . ' jam ' . ($mins > 0 ? $mins . ' menit' : ''));
                                        }

                                        return $minutes . ' menit';
                                    };

                                    $priorityClass = match ($row->priority) {
                                        'Low' => 'bg-emerald-100 text-emerald-700',
                                        'Medium' => 'bg-amber-100 text-amber-700',
                                        'Critical' => 'bg-rose-100 text-rose-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp

                                <tr class="hover:bg-slate-50 align-top">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">#{{ $row->id }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->title }}</div>
                                    </td>

                                    <td class="px-4 py-4 capitalize">{{ $row->category }}</td>

                                    <td class="px-4 py-4">
                                        <span
                                            class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $priorityClass }}">
                                            {{ $row->priority }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ $row->takenByUser?->name ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        {{ optional($row->created_at)->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        {{ optional($row->taken_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap">
                                        {{ optional($row->resolved_at)->format('d/m/Y H:i') ?? '-' }}
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        @if ($responseSeconds !== null)
                                            <div class="text-xs text-slate-500 mb-2">
                                                {{ $formatDuration($responseSeconds) }}
                                                <span class="text-slate-400">/ target {{ $formatTarget($row->sla_response_minutes) }}</span>
                                            </div>

                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $responseOnTimeStatus ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $responseOnTimeStatus ? 'On Time' : 'Late' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        @if ($resolutionSeconds !== null)
                                            <div class="text-xs text-slate-500 mb-2">
                                                {{ $formatDuration($resolutionSeconds) }}
                                                <span class="text-slate-400">/ target {{ $formatTarget($row->sla_resolution_minutes) }}</span>
                                            </div>

                                            <span
                                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold {{ $resolutionOnTimeStatus ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                                {{ $resolutionOnTimeStatus ? 'On Time' : 'Late' }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-slate-500">
                                        Belum ada data SLA.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($tickets->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $tickets->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
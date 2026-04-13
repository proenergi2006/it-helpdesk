<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight flex items-center gap-2">
            ⭐ Report Feedback User
        </h2>
    </x-slot>

    <div class="py-8 bg-slate-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FILTER --}}
            <div class="bg-white p-6 rounded-2xl shadow mb-6">
                <form method="GET" action="{{ route('reports.feedback') }}"
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

                    <div class="md:col-span-3">
                        <label class="block text-sm text-slate-600 mb-1">Kategori</label>
                        <select name="category"
                                class="w-full border-slate-300 rounded-xl focus:ring-2 focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua</option>
                            <option value="software" {{ $category == 'software' ? 'selected' : '' }}>Software</option>
                            <option value="hardware" {{ $category == 'hardware' ? 'selected' : '' }}>Hardware</option>
                            <option value="network&multimedia" {{ $category == 'network&multimedia' ? 'selected' : '' }}>Network & Multimedia</option>
                        </select>
                    </div>

                    <div class="md:col-span-3 flex gap-2">
                        <button class="bg-orange-500 hover:bg-orange-600 text-white px-5 py-3 rounded-xl font-semibold shadow w-full">
                            Filter
                        </button>
                    </div>
                </form>
            </div>

            {{-- SUMMARY --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Total Feedback</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-2">{{ $totalFeedback }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Rata-rata Rating</div>
                    <div class="text-3xl font-extrabold text-amber-500 mt-2">{{ $avgRating }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Puas (4-5)</div>
                    <div class="text-3xl font-extrabold text-emerald-500 mt-2">{{ $puasCount }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Netral (3)</div>
                    <div class="text-3xl font-extrabold text-sky-500 mt-2">{{ $netralCount }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Tidak Puas (1-2)</div>
                    <div class="text-3xl font-extrabold text-rose-500 mt-2">{{ $tidakPuasCount }}</div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Masalah Dianggap Selesai</div>
                    <div class="text-2xl font-bold text-emerald-600 mt-2">{{ $resolvedConfirmedCount }}</div>
                </div>

                <div class="bg-white rounded-2xl shadow p-5">
                    <div class="text-sm text-slate-500">Masih Perlu Tindak Lanjut</div>
                    <div class="text-2xl font-bold text-rose-600 mt-2">{{ $notResolvedCount }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Summary Feedback per Staff IT</h3>
                    <p class="text-sm text-slate-500">Ringkasan rating berdasarkan staff yang mengerjakan ticket</p>
                </div>
            
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-4 py-4 text-left font-bold">Staff IT</th>
                                <th class="px-4 py-4 text-center font-bold">Total Feedback</th>
                                <th class="px-4 py-4 text-center font-bold">Rata-rata Rating</th>
                                <th class="px-4 py-4 text-center font-bold">Puas</th>
                                <th class="px-4 py-4 text-center font-bold">Netral</th>
                                <th class="px-4 py-4 text-center font-bold">Tidak Puas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($staffSummary as $staff)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4 font-semibold text-slate-800">
                                        {{ $staff['staff_name'] }}
                                    </td>
            
                                    <td class="px-4 py-4 text-center">
                                        {{ $staff['total_feedback'] }}
                                    </td>
            
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 24 24"
                                                         fill="{{ $i <= round($staff['avg_rating']) ? 'currentColor' : 'none' }}"
                                                         stroke="currentColor"
                                                         stroke-width="1.8"
                                                         class="w-4 h-4 {{ $i <= round($staff['avg_rating']) ? 'text-amber-400' : 'text-slate-300' }}">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.386a.562.562 0 01-.84.61L12.48 17.77a.563.563 0 00-.58 0l-4.757 2.87a.562.562 0 01-.84-.61l1.285-5.386a.563.563 0 00-.182-.557L3.202 10.385a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345l2.125-5.111z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{ $staff['avg_rating'] }}/5
                                            </span>
                                        </div>
                                    </td>
            
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-emerald-100 text-emerald-700">
                                            {{ $staff['puas'] }}
                                        </span>
                                    </td>
            
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-sky-100 text-sky-700">
                                            {{ $staff['netral'] }}
                                        </span>
                                    </td>
            
                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold bg-rose-100 text-rose-700">
                                            {{ $staff['tidak_puas'] }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-8 text-center text-slate-500">
                                        Belum ada summary feedback per staff.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- TABLE --}}
            <div class="bg-white rounded-2xl shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h3 class="text-lg font-bold text-slate-800">Daftar Feedback</h3>
                    <p class="text-sm text-slate-500">Komentar dan rating dari user</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th class="px-4 py-4 text-left font-bold">Ticket</th>
                                <th class="px-4 py-4 text-left font-bold">User</th>
                                <th class="px-4 py-4 text-left font-bold">Kategori</th>
                                <th class="px-4 py-4 text-left font-bold">Dikerjakan Oleh</th>
                                <th class="px-4 py-4 text-center font-bold">Rating</th>
                                <th class="px-4 py-4 text-center font-bold">Status User</th>
                                <th class="px-4 py-4 text-left font-bold">Komentar</th>
                                <th class="px-4 py-4 text-left font-bold">Tanggal Feedback</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse ($feedbacks as $row)
                                <tr class="hover:bg-slate-50">
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">#{{ $row->id }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->title }}</div>
                                    </td>

                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">{{ $row->nama }}</div>
                                        <div class="text-xs text-slate-400">{{ $row->email }}</div>
                                    </td>

                                    <td class="px-4 py-4 capitalize">{{ $row->category }}</td>

                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-800">
                                            {{ $row->takenByUser?->name ?? '-' }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <div class="flex flex-col items-center gap-1">
                                            <div class="flex items-center gap-1">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <svg xmlns="http://www.w3.org/2000/svg"
                                                         viewBox="0 0 24 24"
                                                         fill="{{ $i <= (int) $row->rating ? 'currentColor' : 'none' }}"
                                                         stroke="currentColor"
                                                         stroke-width="1.8"
                                                         class="w-5 h-5 {{ $i <= (int) $row->rating ? 'text-amber-400' : 'text-slate-300' }}">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.386a.562.562 0 01-.84.61L12.48 17.77a.563.563 0 00-.58 0l-4.757 2.87a.562.562 0 01-.84-.61l1.285-5.386a.563.563 0 00-.182-.557L3.202 10.385a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345l2.125-5.111z" />
                                                    </svg>
                                                @endfor
                                            </div>
                                    
                                            <span class="text-xs font-semibold text-slate-500">
                                                {{
                                                    $row->rating == 5 ? 'Sangat Puas' :
                                                    ($row->rating == 4 ? 'Puas' :
                                                    ($row->rating == 3 ? 'Cukup' :
                                                    'Tidak Puas'))
                                                }}
                                            </span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-bold
                                            {{ $row->is_confirmed_resolved ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                                            {{ $row->is_confirmed_resolved ? 'Selesai' : 'Belum Selesai' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ $row->feedback_comment ?: '-' }}
                                    </td>

                                    <td class="px-4 py-4">
                                        {{ optional($row->feedback_at)->format('d/m/Y H:i') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-10 text-center text-slate-500">
                                        Belum ada data feedback.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($feedbacks->hasPages())
                    <div class="p-4 border-t border-slate-100">
                        {{ $feedbacks->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📈 <span>Trend & Statistik Helpdesk IT</span>
        </h2>
    </x-slot>

    <div class="py-10 min-h-screen bg-gradient-to-br from-blue-100 via-sky-50 to-blue-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- Statistik Cards --}}
            <div class="grid grid-cols-12 gap-6">
                <div
                    class="col-span-12 sm:col-span-6 lg:col-span-3 bg-gradient-to-br from-blue-600 to-blue-400 text-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <p class="text-sm opacity-80">Total Semua Tiket</p>
                    <h3 class="text-5xl font-bold mt-2">{{ $totalAll }}</h3>
                </div>

                <div
                    class="col-span-12 sm:col-span-6 lg:col-span-3 bg-gradient-to-br from-indigo-600 to-indigo-400 text-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <p class="text-sm opacity-80">Tiket Minggu Ini</p>
                    <h3 class="text-5xl font-bold mt-2">{{ $totalWeek }}</h3>
                </div>

                <div
                    class="col-span-12 sm:col-span-6 lg:col-span-3 bg-gradient-to-br from-green-600 to-green-400 text-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <p class="text-sm opacity-80">Rata-rata Penyelesaian</p>
                    <h3 class="text-4xl font-bold mt-2">{{ $avgFormatted }}</h3>
                </div>

                <div
                    class="col-span-12 sm:col-span-6 lg:col-span-3 bg-gradient-to-br from-red-600 to-orange-400 text-white rounded-2xl shadow-lg p-6 transform hover:scale-105 transition-all duration-300">
                    <p class="text-sm opacity-80">Overdue > 24 Jam</p>
                    <h3 class="text-5xl font-bold mt-2">{{ $overdueCount }}</h3>
                </div>
            </div>

            {{-- Grafik Tren Mingguan --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 transition-all duration-500 hover:shadow-2xl">
                <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
                    📊 Tren Tiket Mingguan
                </h3>
                <div class="relative h-[350px]">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            {{-- Grafik Distribusi Teknisi --}}
            <div class="bg-white rounded-2xl shadow-xl p-6 transition-all duration-500 hover:shadow-2xl">
                <h3 class="text-lg font-semibold text-blue-700 mb-4 flex items-center gap-2">
                    👨‍💻 Distribusi Tiket per Teknisi
                </h3>

                <div class="flex flex-col lg:flex-row items-center gap-6">
                    <div class="w-full lg:w-1/2">
                        <div class="relative h-[300px]">
                            <canvas id="technicianChart"></canvas>
                        </div>
                    </div>
                    <div class="w-full lg:w-1/2">
                        <ul class="divide-y divide-gray-200">
                            @foreach ($technicianData as $t)
                                <li class="flex justify-between py-2">
                                    <span class="font-medium text-gray-700">{{ $t['name'] }}</span>
                                    <span class="font-bold text-blue-600">{{ $t['total'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="text-center text-gray-500 text-xs mt-10">
                © {{ date('Y') }} PT Pro Energi — Helpdesk IT Dashboard v{{ config('app.version', '1.0') }}
            </div>
        </div>
    </div>

    {{-- Chart.js Script --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {
                // ============================
                // 1️⃣ Trend Chart
                // ============================
                const trendCanvas = document.getElementById("trendChart");
                if (trendCanvas) {
                    const ctx = trendCanvas.getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(37,99,235,0.4)');
                    gradient.addColorStop(1, 'rgba(37,99,235,0.05)');

                    new Chart(trendCanvas, {
                        type: "line",
                        data: {
                            labels: @json($weekLabels),
                            datasets: [{
                                label: "Jumlah Tiket",
                                data: @json($trendData),
                                borderColor: "#2563eb",
                                backgroundColor: gradient,
                                borderWidth: 3,
                                fill: true,
                                tension: 0.4,
                                pointBackgroundColor: "#1d4ed8",
                                pointRadius: 5,
                                pointHoverRadius: 8,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            animation: {
                                duration: 1500,
                                easing: 'easeOutQuart'
                            },
                            plugins: {
                                legend: {
                                    display: false
                                },
                                tooltip: {
                                    backgroundColor: "rgba(17,24,39,0.9)",
                                    titleColor: "#fff",
                                    bodyColor: "#e5e7eb",
                                    padding: 10,
                                    cornerRadius: 8,
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        color: "#6b7280",
                                        stepSize: 1
                                    },
                                    grid: {
                                        color: "rgba(209,213,219,0.3)"
                                    },
                                },
                                x: {
                                    ticks: {
                                        color: "#6b7280"
                                    },
                                    grid: {
                                        display: false
                                    },
                                }
                            }
                        },
                    });
                }

                // ============================
                // 2️⃣ Technician Chart
                // ============================
                const techCanvas = document.getElementById("technicianChart");
                if (techCanvas) {
                    new Chart(techCanvas, {
                        type: "doughnut",
                        data: {
                            labels: @json($technicianData->pluck('name')),
                            datasets: [{
                                data: @json($technicianData->pluck('total')),
                                backgroundColor: [
                                    "#2563eb", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6", "#06b6d4"
                                ],
                                borderColor: "#fff",
                                borderWidth: 3,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: "bottom",
                                    labels: {
                                        color: "#374151",
                                        boxWidth: 14,
                                        font: {
                                            size: 12
                                        }
                                    },
                                },
                                tooltip: {
                                    callbacks: {
                                        label: function(context) {
                                            const total = context.chart._metasets[0].total;
                                            const value = context.raw;
                                            const percent = ((value / total) * 100).toFixed(1);
                                            return `${context.label}: ${value} tiket (${percent}%)`;
                                        }
                                    }
                                }
                            },
                            animation: {
                                animateScale: true,
                                animateRotate: true
                            },
                        },
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>

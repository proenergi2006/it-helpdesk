<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight flex items-center gap-2">
            📈 <span>Dashboard Trend & Analisis Helpdesk IT</span>
        </h2>
    </x-slot>

    <div class="py-10 min-h-screen bg-gradient-to-br from-blue-100 via-sky-50 to-blue-200">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-10">

            {{-- 🔹 Statistik Cards --}}
            <div class="grid grid-cols-12 gap-6">
                <x-stat-card color="from-blue-600 to-blue-400" label="Total Semua Tiket" :value="$totalAll" />
                <x-stat-card color="from-indigo-600 to-indigo-400" label="Tiket Minggu Ini" :value="$totalWeek" />
                <x-stat-card color="from-green-600 to-green-400" label="Rata-rata Penyelesaian" :value="$avgFormatted" />
                <x-stat-card color="from-red-600 to-orange-400" label="Overdue > 24 Jam" :value="$overdueCount" />
            </div>

            {{-- 📊 Tren Tiket Mingguan --}}
            <x-chart-card title="📊 Tren Tiket Mingguan">
                <canvas id="trendChart" class="h-[350px]"></canvas>
            </x-chart-card>

            {{-- 🧩 Rasio Status Tiket --}}
            <x-chart-card title="🧩 Rasio Status Tiket (Open / In Progress / Resolved)">
                <canvas id="statusChart" class="h-[300px]"></canvas>
            </x-chart-card>

            {{-- 👨‍💻 Distribusi Tiket per Teknisi --}}
            <x-chart-card title="👨‍💻 Distribusi Tiket per Teknisi">
                <div class="flex flex-col lg:flex-row gap-6">
                    <div class="w-full lg:w-1/2 h-[300px]">
                        <canvas id="technicianChart"></canvas>
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
            </x-chart-card>

            {{-- 🏅 Teknisi Paling Aktif & Kategori Paling Sering --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-list-card title="🏅 Teknisi Paling Aktif" :items="$topTechnicians" color="blue" />
                <x-list-card title="🧩 Kategori Paling Sering" :items="$topCategories" color="green" />
            </div>

            {{-- ⏱️ Rata-rata Durasi per Teknisi --}}
            <x-chart-card title="⏱️ Rata-rata Waktu Penyelesaian per Teknisi (jam)">
                <canvas id="avgTechChart" class="h-[350px]"></canvas>
            </x-chart-card>

            {{-- 🏢 Top Cabang --}}
            <x-chart-card title="🏢 Cabang dengan Tiket Terbanyak">
                <canvas id="branchChart" class="h-[350px]"></canvas>
            </x-chart-card>

            {{-- ✅ SLA Compliance --}}
            <x-chart-card title="✅ Kepatuhan SLA (Tepat Waktu vs Terlambat)">
                <canvas id="slaChart" class="h-[300px]"></canvas>
            </x-chart-card>

            {{-- Footer --}}
            <div class="text-center text-gray-500 text-xs mt-10">
                © {{ date('Y') }} PT Pro Energi — Helpdesk IT Dashboard v{{ config('app.version', '1.0') }}
            </div>
        </div>
    </div>

    {{-- Chart.js Scripts --}}
    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", () => {

                // === 1️⃣ Tren Mingguan ===
                new Chart(document.getElementById("trendChart"), {
                    type: "line",
                    data: {
                        labels: @json($weekLabels),
                        datasets: [{
                            label: "Jumlah Tiket",
                            data: @json($trendData),
                            borderColor: "#2563eb",
                            backgroundColor: "rgba(37,99,235,0.2)",
                            fill: true,
                            tension: 0.4,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // === 2️⃣ Rasio Status ===
                new Chart(document.getElementById("statusChart"), {
                    type: "doughnut",
                    data: {
                        labels: @json($statusLabels),
                        datasets: [{
                            data: @json($statusData),
                            backgroundColor: ["#facc15", "#3b82f6", "#22c55e"],
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // === 3️⃣ Distribusi Teknisi ===
                new Chart(document.getElementById("technicianChart"), {
                    type: "doughnut",
                    data: {
                        labels: @json($technicianData->pluck('name')),
                        datasets: [{
                            data: @json($technicianData->pluck('total')),
                            backgroundColor: ["#2563eb", "#10b981", "#f59e0b", "#ef4444", "#8b5cf6"],
                            borderWidth: 2,
                        }],
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // === 4️⃣ Rata-rata Durasi per Teknisi ===
                new Chart(document.getElementById("avgTechChart"), {
                    type: "bar",
                    data: {
                        labels: @json($avgPerTechnician->pluck('name')),
                        datasets: [{
                            label: "Jam",
                            data: @json($avgPerTechnician->pluck('avg_hours')),
                            backgroundColor: "#06b6d4",
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });

                // === 5️⃣ Top Kategori ===
                new Chart(document.getElementById("categoryChart"), {
                    type: "bar",
                    data: {
                        labels: @json($topCategories->pluck('category')),
                        datasets: [{
                            label: "Jumlah Tiket",
                            data: @json($topCategories->pluck('total')),
                            backgroundColor: "#3b82f6",
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // === 6️⃣ Top Cabang ===
                new Chart(document.getElementById("branchChart"), {
                    type: "bar",
                    data: {
                        labels: @json($branchData->pluck('cabang')),
                        datasets: [{
                            label: "Jumlah Tiket",
                            data: @json($branchData->pluck('total')),
                            backgroundColor: "#f97316",
                            borderRadius: 8,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });

                // === 7️⃣ SLA Compliance ===
                new Chart(document.getElementById("slaChart"), {
                    type: "pie",
                    data: {
                        labels: ["Tepat Waktu", "Terlambat"],
                        datasets: [{
                            data: @json($slaData),
                            backgroundColor: ["#22c55e", "#ef4444"],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>

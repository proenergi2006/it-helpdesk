<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendController extends Controller
{
    public function index()
    {
        // 📅 Periode Mingguan
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd   = Carbon::now()->endOfWeek();

        // ==========================
        // 🔹 Statistik Utama
        // ==========================
        $totalAll   = Ticket::count();
        $totalWeek  = Ticket::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        $avgDuration = Ticket::whereNotNull('finished_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minute'))
            ->value('avg_minute');
        $avgFormatted = $avgDuration
            ? round($avgDuration / 60, 2) . ' jam'
            : '-';

        $overdueCount = Ticket::where('status', '!=', 'resolved')
            ->where('started_at', '<', Carbon::now()->subHours(24))
            ->count();

        // ==========================
        // 👨‍💻 Data Distribusi Teknisi
        // ==========================
        $technicianData = Ticket::select('taken_by', DB::raw('COUNT(*) as total'))
            ->whereNotNull('taken_by')
            ->groupBy('taken_by')
            ->with('takenByUser:id,name')
            ->get()
            ->map(fn($item) => [
                'name' => $item->takenByUser->name ?? 'Tidak diketahui',
                'total' => $item->total,
            ]);

        // ==========================
        // 📊 Tren Mingguan (per hari)
        // ==========================
        $weekDays = collect(range(0, 6))->map(fn($i) => Carbon::now()->startOfWeek()->addDays($i)->format('Y-m-d'));
        $trendData = $weekDays->map(fn($day) => Ticket::whereDate('created_at', $day)->count());
        $weekLabels = $weekDays->map(fn($d) => Carbon::parse($d)->translatedFormat('D'))->toArray();

        // ==========================
        // 🏅 Top Teknisi
        // ==========================
        $topTechnicians = Ticket::select('taken_by', DB::raw('COUNT(*) as total'))
            ->whereNotNull('taken_by')
            ->groupBy('taken_by')
            ->with('takenByUser:id,name')
            ->orderByDesc('total')
            ->take(5)
            ->get()
            ->map(fn($t) => [
                'name' => $t->takenByUser->name ?? 'Tidak diketahui',
                'total' => $t->total,
            ]);

        // ==========================
        // 🧩 Top Kategori Tiket
        // ==========================
        $topCategories = Ticket::select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')
            ->orderByDesc('total')
            ->take(5)
            ->get();

        // ==========================
        // ⏱️ Rata-rata Durasi per Teknisi
        // ==========================
        $avgPerTechnician = Ticket::whereNotNull('finished_at')
            ->select('taken_by', DB::raw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minute'))
            ->groupBy('taken_by')
            ->with('takenByUser:id,name')
            ->get()
            ->map(fn($t) => [
                'name' => $t->takenByUser->name ?? 'Tidak diketahui',
                'avg_hours' => round($t->avg_minute / 60, 2),
            ]);

        // ==========================
        // 🏢 Top Cabang
        // ==========================
        $branchData = Ticket::select('cabang', DB::raw('COUNT(*) as total'))
            ->whereNotNull('cabang')
            ->groupBy('cabang')
            ->orderByDesc('total')
            ->take(7)
            ->get();

        // ==========================
        // ✅ SLA Compliance (Tepat waktu vs Terlambat)
        // SLA = selesai <= 24 jam dari started_at
        // ==========================
        $slaTepat = Ticket::whereNotNull('finished_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, started_at, finished_at) <= 24')
            ->count();
        $slaTerlambat = Ticket::whereNotNull('finished_at')
            ->whereRaw('TIMESTAMPDIFF(HOUR, started_at, finished_at) > 24')
            ->count();
        $slaData = [$slaTepat, $slaTerlambat];

        // ==========================
        // 🧠 Rasio Status Tiket
        // ==========================
        $openCount        = Ticket::where('status', 'open')->count();
        $inProgressCount  = Ticket::where('status', 'in_progress')->count();
        $resolvedCount    = Ticket::where('status', 'resolved')->count();
        $statusLabels     = ['Open', 'In Progress', 'Resolved'];
        $statusData       = [$openCount, $inProgressCount, $resolvedCount];

        // ==========================
        // 🔄 Return ke View
        // ==========================
        return view('trend', [
            // Statistik Umum
            'totalAll'       => $totalAll,
            'totalWeek'      => $totalWeek,
            'avgFormatted'   => $avgFormatted,
            'overdueCount'   => $overdueCount,

            // Grafik & Data
            'technicianData' => $technicianData,
            'weekLabels'     => $weekLabels,
            'trendData'      => $trendData,

            'topTechnicians' => $topTechnicians,
            'topCategories'  => $topCategories,
            'avgPerTechnician' => $avgPerTechnician,
            'branchData'     => $branchData,

            'slaData'        => $slaData,
            'statusLabels'   => $statusLabels,
            'statusData'     => $statusData,
        ]);
    }
}

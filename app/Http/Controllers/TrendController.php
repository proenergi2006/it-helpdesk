<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrendController extends Controller
{
    public function index()
    {
        // Total dan statistik umum
        $totalAll = Ticket::count();

        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();
        $totalWeek = Ticket::whereBetween('created_at', [$weekStart, $weekEnd])->count();

        $avgDuration = Ticket::whereNotNull('finished_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, started_at, finished_at)) as avg_minute'))
            ->value('avg_minute');
        $avgFormatted = $avgDuration
            ? round($avgDuration / 60, 2) . ' jam'
            : '-';

        $overdueCount = Ticket::where('status', '!=', 'resolved')
            ->where('started_at', '<', Carbon::now()->subHours(24))
            ->count();

        // Data untuk chart per teknisi
        $technicianData = Ticket::select('taken_by', DB::raw('COUNT(*) as total'))
            ->whereNotNull('taken_by')
            ->groupBy('taken_by')
            ->with('takenByUser:id,name')
            ->get()
            ->map(fn($item) => [
                'name' => $item->takenByUser->name ?? 'Tidak diketahui',
                'total' => $item->total,
            ]);

        // Data tren mingguan (tiket per hari)
        $weekDays = collect(range(0, 6))->map(fn($i) => Carbon::now()->startOfWeek()->addDays($i)->format('Y-m-d'));
        $trendData = $weekDays->map(function ($day) {
            return Ticket::whereDate('created_at', $day)->count();
        });

        return view('trend', [
            'totalAll' => $totalAll,
            'totalWeek' => $totalWeek,
            'avgFormatted' => $avgFormatted,
            'overdueCount' => $overdueCount,
            'technicianData' => $technicianData,
            'weekLabels' => $weekDays->map(fn($d) => Carbon::parse($d)->translatedFormat('D'))->toArray(),
            'trendData' => $trendData,
        ]);
    }
}

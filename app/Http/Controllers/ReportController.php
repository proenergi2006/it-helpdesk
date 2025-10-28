<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\TicketsExport;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $start = $request->input('start_date');
        $end   = $request->input('end_date');

        $query = Ticket::query();

        if ($start && $end) {
            $query->whereBetween('created_at', [$start, $end]);
        }

        $tickets = $query->orderBy('created_at', 'desc')->get();

        return view('reports.index', compact('tickets', 'start', 'end'));
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new TicketsExport($request->start_date, $request->end_date), 'report_tickets.xlsx');
    }

    public function exportPDF(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;

        $query = Ticket::with('takenByUser')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->orderBy('created_at', 'desc');

        $tickets = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('tickets', 'start', 'end'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan_tiket_' . now()->format('Ymd_His') . '.pdf');
    }
}

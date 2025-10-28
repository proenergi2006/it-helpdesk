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
        $tickets = Ticket::query()
            ->when(
                $request->start_date && $request->end_date,
                fn($q) =>
                $q->whereBetween('created_at', [$request->start_date, $request->end_date])
            )->get();

        $pdf = Pdf::loadView('reports.pdf', compact('tickets'));
        return $pdf->download('report_tickets.pdf');
    }
}

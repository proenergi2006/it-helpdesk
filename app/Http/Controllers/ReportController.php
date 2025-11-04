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
        $nama  = $request->input('nama');
        $kategori = $request->input('category');

        $query = Ticket::with('takenByUser')->orderBy('created_at', 'desc');

        if ($start) {
            $query->whereDate('created_at', '>=', $start);
        }
        if ($end) {
            $query->whereDate('created_at', '<=', $end);
        }
        if ($nama) {
            $query->where('nama', 'like', "%{$nama}%");
        }
        if ($kategori) {
            $query->where('category', $kategori);
        }

        $tickets = $query->get();

        return view('reports.index', compact('tickets', 'start', 'end'));
    }


    public function exportExcel(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $nama = $request->nama;
        $kategori = $request->category;

        return Excel::download(
            new TicketsExport($start, $end, $nama, $kategori),
            'report_tickets_' . now()->format('Ymd_His') . '.xlsx'
        );
    }

    public function exportPDF(Request $request)
    {
        $start = $request->start_date;
        $end = $request->end_date;
        $nama = $request->nama;
        $kategori = $request->category;

        $query = Ticket::with('takenByUser')
            ->when($start, fn($q) => $q->whereDate('created_at', '>=', $start))
            ->when($end, fn($q) => $q->whereDate('created_at', '<=', $end))
            ->when($nama, fn($q) => $q->where('nama', 'like', "%{$nama}%"))
            ->when($kategori, fn($q) => $q->where('category', $kategori))
            ->orderBy('created_at', 'desc');

        $tickets = $query->get();

        $pdf = Pdf::loadView('reports.pdf', compact('tickets', 'start', 'end'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan_tiket_' . now()->format('Ymd_His') . '.pdf');
    }
}

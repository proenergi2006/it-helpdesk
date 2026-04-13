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

    public function feedbackReport(Request $request)
    {
        $start = $request->get('start_date');
        $end = $request->get('end_date');
        $category = $request->get('category');
    
        $query = \App\Models\Ticket::with('takenByUser')
            ->whereNotNull('feedback_at')
            ->whereNotNull('rating');
    
        if ($start) {
            $query->whereDate('feedback_at', '>=', $start);
        }
    
        if ($end) {
            $query->whereDate('feedback_at', '<=', $end);
        }
    
        if ($category) {
            $query->where('category', $category);
        }
    
        $feedbacks = (clone $query)
            ->latest('feedback_at')
            ->paginate(10)
            ->withQueryString();
    
        $summaryData = (clone $query)->get();
    
        $totalFeedback = $summaryData->count();
        $avgRating = $totalFeedback > 0 ? round($summaryData->avg('rating'), 2) : 0;
    
        $puasCount = $summaryData->whereIn('rating', [4, 5])->count();
        $netralCount = $summaryData->where('rating', 3)->count();
        $tidakPuasCount = $summaryData->whereIn('rating', [1, 2])->count();
    
        $resolvedConfirmedCount = $summaryData->where('is_confirmed_resolved', true)->count();
        $notResolvedCount = $summaryData->where('is_confirmed_resolved', false)->count();
    
        // Summary per staff IT
        $staffSummary = $summaryData
            ->groupBy(function ($item) {
                return $item->takenByUser?->name ?? 'Belum Di-assign';
            })
            ->map(function ($items, $staffName) {
                $count = $items->count();
                $avg = $count > 0 ? round($items->avg('rating'), 2) : 0;
    
                return [
                    'staff_name' => $staffName,
                    'total_feedback' => $count,
                    'avg_rating' => $avg,
                    'puas' => $items->whereIn('rating', [4, 5])->count(),
                    'netral' => $items->where('rating', 3)->count(),
                    'tidak_puas' => $items->whereIn('rating', [1, 2])->count(),
                ];
            })
            ->sortByDesc('avg_rating')
            ->values();
    
        return view('reports.feedback', compact(
            'feedbacks',
            'start',
            'end',
            'category',
            'totalFeedback',
            'avgRating',
            'puasCount',
            'netralCount',
            'tidakPuasCount',
            'resolvedConfirmedCount',
            'notResolvedCount',
            'staffSummary'
        ));
    }

    public function slaReport(Request $request)
{
    $start = $request->get('start_date');
    $end = $request->get('end_date');
    $category = $request->get('category');
    $status = $request->get('status');

    $query = \App\Models\Ticket::with('takenByUser');

    if ($start) {
        $query->whereDate('created_at', '>=', $start);
    }

    if ($end) {
        $query->whereDate('created_at', '<=', $end);
    }

    if ($category) {
        $query->where('category', $category);
    }

    if ($status) {
        $query->where('status', $status);
    }

    $tickets = (clone $query)
        ->latest()
        ->paginate(10)
        ->withQueryString();

    $allTickets = (clone $query)->get();

    $totalTickets = $allTickets->count();

    $responseOnTime = 0;
    $responseLate = 0;
    $resolutionOnTime = 0;
    $resolutionLate = 0;

    foreach ($allTickets as $ticket) {
        if ($ticket->taken_at && $ticket->sla_response_minutes) {
            $responseMinutes = $ticket->created_at->diffInMinutes($ticket->taken_at);
            if ($responseMinutes <= $ticket->sla_response_minutes) {
                $responseOnTime++;
            } else {
                $responseLate++;
            }
        }

        if ($ticket->resolved_at && $ticket->sla_resolution_minutes) {
            $resolutionMinutes = $ticket->created_at->diffInMinutes($ticket->resolved_at);
            if ($resolutionMinutes <= $ticket->sla_resolution_minutes) {
                $resolutionOnTime++;
            } else {
                $resolutionLate++;
            }
        }
    }

    return view('reports.sla', compact(
        'tickets',
        'start',
        'end',
        'category',
        'status',
        'totalTickets',
        'responseOnTime',
        'responseLate',
        'resolutionOnTime',
        'resolutionLate'
    ));
}
}

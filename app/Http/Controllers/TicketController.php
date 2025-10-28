<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * Tampilkan halaman antrian utama (software & hardware)
     */
    public function index()
    {
        $tickets = Ticket::orderBy('created_at', 'desc')->get();

        $totalToday = Ticket::whereDate('created_at', now())->count();
        $openCount = Ticket::where('status', 'open')->count();
        $resolvedCount = Ticket::where('status', 'resolved')->count();

        $softwareTickets = Ticket::where('category', 'software')
            ->where('status', 'open')
            ->orderBy('id')
            ->limit(3)
            ->get();

        $hardwareTickets = Ticket::where('category', 'hardware')
            ->where('status', 'open')
            ->orderBy('id')
            ->limit(2)
            ->get();

        return view('welcome', compact(
            'tickets',
            'softwareTickets',
            'hardwareTickets',
            'totalToday',
            'openCount',
            'resolvedCount'
        ));
    }



    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama'        => 'required|string|max:100',
            'email'       => 'required|email|max:150',
            'cabang'      => 'required|string|max:50',
            'title'       => 'required|string|max:255',
            'category'    => 'required|in:software,hardware',
            'description' => 'required|string',
        ]);

        Ticket::create($validated);

        return redirect()->route('welcome')->with('success', 'Ticket berhasil dikirim!');
    }
    /**
     * API kecil (optional) untuk auto-refresh antrian realtime
     */
    public function apiList()
    {
        return response()->json(Ticket::orderBy('created_at', 'desc')->take(10)->get());
    }

    public function dashboard()
    {
        $tickets = Ticket::with('takenByUser')->latest()->get();
        $cabangs = Ticket::select('cabang')->distinct()->pluck('cabang');

        return view('dashboard', [
            'tickets' => $tickets,
            'cabangs' => $cabangs,
            'openCount' => Ticket::where('status', 'open')->count(),
            'inProgressCount' => Ticket::where('status', 'in_progress')->count(),
            'resolvedCount' => Ticket::where('status', 'resolved')->count(),
        ]);
    }





    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $status = $request->status;
        $user = Auth::user(); // ✅ sekarang tidak error

        if ($status === 'in_progress') {
            $ticket->taken_by = $user->id;
            $ticket->started_at = now();
        } elseif ($status === 'resolved') {
            $ticket->finished_at = now();
        }

        $ticket->status = $status;
        $ticket->save();

        return back()->with('success', 'Status tiket diperbarui oleh ' . $user->name);
    }
}

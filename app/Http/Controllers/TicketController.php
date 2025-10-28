<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    /**
     * 🏠 Halaman utama antrian (untuk user publik)
     */
    public function index()
    {
        $tickets = Ticket::with('takenByUser')->orderBy('created_at', 'desc')->get();

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

    /**
     * 📨 Simpan ticket baru dari form publik
     */
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
     * 🔄 API data antrian realtime (dipakai oleh JS untuk auto-refresh)
     */
    public function apiList()
    {
        $tickets = Ticket::with('takenByUser')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($t) {
                return [
                    'id'             => $t->id,
                    'nama'           => $t->nama,
                    'title'          => $t->title,
                    'cabang'         => $t->cabang,
                    'category'       => $t->category,
                    'status'         => $t->status,
                    'created_at'     => $t->created_at,
                    'taken_by_name'  => $t->takenByUser->name ?? null,
                ];
            });

        return response()->json($tickets);
    }

    /**
     * 📊 Dashboard untuk tim IT (hanya bisa diakses setelah login)
     */
    public function dashboard()
    {
        $tickets = Ticket::with('takenByUser')->latest()->get();
        $cabangs = Ticket::select('cabang')->distinct()->pluck('cabang');

        return view('dashboard', [
            'tickets'          => $tickets,
            'cabangs'          => $cabangs,
            'openCount'        => Ticket::where('status', 'open')->count(),
            'inProgressCount'  => Ticket::where('status', 'in_progress')->count(),
            'resolvedCount'    => Ticket::where('status', 'resolved')->count(),
        ]);
    }

    /**
     * 🔧 Update status tiket oleh tim IT
     */
    public function updateStatus(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);
        $status = $request->status;
        $user   = Auth::user();

        if (!$user) {
            return back()->with('error', 'Anda belum login!');
        }

        if ($status === 'in_progress') {
            $ticket->taken_by  = $user->id;
            $ticket->started_at = now();
        } elseif ($status === 'resolved') {
            $ticket->finished_at = now();
        }

        $ticket->status = $status;
        $ticket->save();

        return back()->with('success', 'Status tiket diperbarui oleh ' . $user->name);
    }
}

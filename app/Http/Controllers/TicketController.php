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
            'category' => 'required|in:software,hardware,network&multimedia',

            'klasifikasi' => 'required|in:Incident,Request',
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
                    'klasifikasi'    => $t->klasifikasi,
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
        $tickets = Ticket::with('takenByUser')
            ->orderBy('created_at', 'desc') // tiket terbaru paling atas
            ->get();

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
        $user = Auth::user();

        $allowedStatuses = [
            'open',
            'in_progress',
            'resolved',
            'Hold - Third Party',
            'Hold - Waiting User Response'
        ];

        if (!in_array($status, $allowedStatuses)) {
            return response()->json(['success' => false, 'message' => 'Status tidak valid.'], 400);
        }

        // Waktu otomatis
        if ($status === 'in_progress' && !$ticket->started_at) {
            $ticket->started_at = now();
            $ticket->taken_by = $user?->id;
        }

        // Catatan penyelesaian
        if ($status === 'resolved') {
            $ticket->finished_at = now();
            $ticket->resolution_note = $request->resolution_note ?? '-';
        }

        $ticket->status = $status;
        $ticket->save();

        // ✅ Selalu kembalikan JSON untuk AJAX
        if ($request->isJson() || $request->ajax() || $request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diperbarui!',
                'updated_by' => $user?->name ?? 'System',
                'status' => $status
            ], 200);
        }

        // Fallback (kalau bukan fetch)
        return back()->with('success', 'Status tiket diperbarui oleh ' . ($user?->name ?? 'System'));
    }



    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:Low,Medium,Critical'
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->priority = $request->priority;
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Priority tiket berhasil diperbarui!',
            'priority' => $ticket->priority
        ]);
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);

        // Hanya bisa hapus jika status open
        if ($ticket->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket hanya bisa dihapus jika status masih OPEN.'
            ], 400);
        }

        $ticket->delete();

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dihapus!'
        ]);
    }


    public function chatAsk(Request $request)
    {
        $question = strtolower(trim($request->input('question')));

        if (!$question) {
            return response()->json(['answer' => 'Silakan ketik pertanyaan Anda terlebih dahulu.']);
        }

        // Ambil semua tiket resolved yang punya catatan penyelesaian
        $tickets = \App\Models\Ticket::where('status', 'resolved')
            ->whereNotNull('resolution_note')
            ->get(['title', 'description', 'resolution_note']);

        $bestMatch = null;
        $bestScore = 0;

        // Tokenisasi pertanyaan user
        $questionWords = preg_split('/\s+/', $question);

        foreach ($tickets as $t) {
            // Gabungkan semua teks tiket jadi satu string dan ubah ke lowercase
            $text = strtolower($t->title . ' ' . $t->description . ' ' . $t->resolution_note);
            $textWords = preg_split('/\s+/', $text);

            // Hitung jumlah kata yang sama antara pertanyaan dan teks
            $commonWords = count(array_intersect($questionWords, $textWords));
            $totalWords = count($questionWords);

            // Hitung skor relevansi (% dari kata yang cocok)
            $score = ($totalWords > 0) ? ($commonWords / $totalWords) * 100 : 0;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $t;
            }
        }

        if ($bestMatch && $bestScore >= 25) { // ambil jika cocok minimal 25%
            return response()->json([
                'answer' => $bestMatch->resolution_note,
                'confidence' => round($bestScore, 1)
            ]);
        }

        return response()->json([
            'answer' => 'Maaf, saya belum menemukan solusi yang sesuai. Tim IT akan segera membantu Anda.',
            'confidence' => 0
        ]);
    }
}

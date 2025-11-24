<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TicketCreatedUser;
use App\Mail\TicketCreatedTeam;
use App\Mail\TicketResolvedUser;

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
        // 1️⃣ Validasi field utama dan attachment
        $validated = $request->validate([
            'nama'         => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'cabang'       => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:software,hardware,network&multimedia',
            'klasifikasi'  => 'required|in:Incident,Request',
            'description'  => 'required|string',

            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048', // 2MB per file
        ]);

        // 2️⃣ Simpan data tiket
        $ticket = Ticket::create($validated);

        // 3️⃣ Simpan attachment jika ada
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public'); // simpan ke storage/app/public/attachments
                DB::table('ticket_attachments')->insert([
                    'ticket_id' => $ticket->id,
                    'file_name' => $file->getClientOriginalName(),
                    'file_path' => $path,
                    'file_size' => $file->getSize(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Mail::to($ticket->email)->send(new TicketCreatedUser($ticket));

        Mail::to([
            'iwan.hermawan@proenergi.co.id',
            'fadli.fathur@proenergi.co.id',
            'gary.salsabilla@proenergi.co.id',
            'sultony.saddam@proenergi.co.id'
        ])->send(new TicketCreatedTeam($ticket));

        return redirect()->route('welcome')->with('success', 'Ticket berhasil dikirim!');
    }

    public function detail($id)
    {
        $ticket = Ticket::with('attachments')->findOrFail($id);

        return response()->json([
            'attachments' => $ticket->attachments->map(fn($a) => [
                'file_name' => $a->file_name,
                'file_path' => $a->file_path,
            ])
        ]);
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
            ->orderByRaw("
            CASE
                WHEN status = 'open' THEN 1
                WHEN status = 'in_progress' THEN 2
                WHEN status = 'Hold - Third Party' THEN 3
                WHEN status = 'Hold - Waiting User Response' THEN 4
                WHEN status = 'resolved' THEN 5
                ELSE 6
            END
        ")
            ->orderBy('created_at', 'desc') // urutkan baru berdasarkan waktu
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
            'Hold - Waiting User Response',
            'Hold - Teknisi'
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
            Mail::to($ticket->email)->send(new TicketResolvedUser($ticket));
        }

        $ticket->status = $status;
        $ticket->save();

        // Logging untuk debug
        Log::info('Status tiket diperbarui', [
            'id' => $id,
            'status' => $status,
            'by' => $user?->name ?? 'System'
        ]);

        // ✅ Selalu kembalikan JSON untuk fetch() dan Ajax
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Status tiket berhasil diperbarui!',
                'updated_by' => $user?->name ?? 'System',
                'status' => $status
            ]);
        }

        // fallback jika bukan AJAX
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

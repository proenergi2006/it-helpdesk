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

        $networkMultimediaTickets = Ticket::where('category', 'network&multimedia')
            ->where('status', 'open')
            ->orderBy('id')
            ->limit(2)
            ->get();

        return view('welcome', compact(
            'tickets',
            'softwareTickets',
            'hardwareTickets',
            'networkMultimediaTickets',
            'totalToday',
            'openCount',
            'resolvedCount'
        ));
    }

    public function store(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login.user')
                ->with('error', 'Silakan login terlebih dahulu untuk membuat ticket.');
        }

        $user = Auth::user();

        $validated = $request->validate([
            'nama'         => 'required|string|max:100',
            'email'        => 'required|email|max:150',
            'cc_emails'    => 'nullable|string',
            'cabang'       => 'required|string|max:50',
            'title'        => 'required|string|max:255',
            'category'     => 'required|in:software,hardware,network&multimedia',
            'priority'     => 'required|in:Low,Medium,Critical',
            'klasifikasi'  => 'required|in:Incident,Request',
            'description'  => 'required|string',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // SLA default berdasarkan priority
        $slaResponseMinutes = 240;
        $slaResolutionMinutes = 1440;

        if ($validated['priority'] === 'Medium') {
            $slaResponseMinutes = 120;
            $slaResolutionMinutes = 480;
        }

        if ($validated['priority'] === 'Critical') {
            $slaResponseMinutes = 30;
            $slaResolutionMinutes = 240;
        }

        $ticket = Ticket::create([
            'user_id'                => $user->id,
            'nama'                   => $user->name,
            'email'                  => $user->email,
            'cabang'                 => $validated['cabang'],
            'title'                  => $validated['title'],
            'category'               => $validated['category'],
            'priority'               => $validated['priority'],
            'klasifikasi'            => $validated['klasifikasi'],
            'description'            => $validated['description'],
            'status'                 => 'open',
            'cc_emails'              => $request->cc_emails,
            'sla_response_minutes'   => $slaResponseMinutes,
            'sla_resolution_minutes' => $slaResolutionMinutes,
            'taken_at'               => null,
            'resolved_at'            => null,
        ]);

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('attachments', 'public');

                DB::table('ticket_attachments')->insert([
                    'ticket_id'  => $ticket->id,
                    'file_name'  => $file->getClientOriginalName(),
                    'file_path'  => $path,
                    'file_size'  => $file->getSize(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $ccList = [];
        if (!empty($request->cc_emails)) {
            $ccList = array_filter(array_map('trim', explode(',', $request->cc_emails)));
            $ccList = array_filter($ccList, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            });
        }

        Mail::to($ticket->email)
            ->cc($ccList)
            ->send(new TicketCreatedUser($ticket));

        Mail::to([
            'iwan.hermawan@proenergi.co.id',
            'fadli.fathur@proenergi.co.id',
            'gary.salsabilla@proenergi.co.id',
            'sultony.saddam@proenergi.co.id',
            'ferry.indrawan@plazatoyota.co.id'
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

    public function apiList()
    {
        $tickets = Ticket::with('takenByUser')
            ->orderBy('created_at', 'desc')
            ->take(20)
            ->get()
            ->map(function ($t) {
                return [
                    'id'            => $t->id,
                    'nama'          => $t->nama,
                    'title'         => $t->title,
                    'description'   => $t->description,
                    'cabang'        => $t->cabang,
                    'category'      => $t->category,
                    'status'        => $t->status,
                    'klasifikasi'   => $t->klasifikasi,
                    'priority'      => $t->priority,
                    'created_at'    => $t->created_at,
                    'taken_by_name' => $t->takenByUser->name ?? null,
                ];
            });

        return response()->json($tickets);
    }

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
            ->orderBy('created_at', 'desc')
            ->get();

        $cabangs = Ticket::select('cabang')->distinct()->pluck('cabang');
        $technicians = \App\Models\User::whereIn('role', ['it', 'admin_it', 'support'])->get();

        return view('dashboard', [
            'tickets'         => $tickets,
            'cabangs'         => $cabangs,
            'openCount'       => Ticket::where('status', 'open')->count(),
            'inProgressCount' => Ticket::where('status', 'in_progress')->count(),
            'resolvedCount'   => Ticket::where('status', 'resolved')->count(),
            'technicians'     => $technicians,
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
            return response()->json([
                'success' => false,
                'message' => 'Status tidak valid.'
            ], 400);
        }

        if ($status === 'in_progress') {
            if (!$ticket->taken_at) {
                $ticket->taken_at = now();
            }

            if (!$ticket->taken_by) {
                $ticket->taken_by = $user?->id;
            }

            // kompatibilitas field lama
            if (!$ticket->started_at) {
                $ticket->started_at = now();
            }
        }

        if ($status === 'resolved') {
            if (!$ticket->resolved_at) {
                $ticket->resolved_at = now();
            }

            // kompatibilitas field lama
            if (!$ticket->finished_at) {
                $ticket->finished_at = now();
            }

            $ticket->resolution_note = $request->resolution_note ?? '-';

            $ccList = [];
            if (!empty($ticket->cc_emails)) {
                $ccList = array_filter(array_map('trim', explode(',', $ticket->cc_emails)));
                $ccList = array_filter($ccList, function ($email) {
                    return filter_var($email, FILTER_VALIDATE_EMAIL);
                });
            }

            Mail::to($ticket->email)
                ->cc($ccList)
                ->send(new TicketResolvedUser($ticket));
        }

        $ticket->status = $status;
        $ticket->save();

        Log::info('Status tiket diperbarui', [
            'id' => $id,
            'status' => $status,
            'by' => $user?->name ?? 'System'
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'    => true,
                'message'    => 'Status tiket berhasil diperbarui!',
                'updated_by' => $user?->name ?? 'System',
                'status'     => $status
            ]);
        }

        return back()->with('success', 'Status tiket diperbarui oleh ' . ($user?->name ?? 'System'));
    }

    public function updatePriority(Request $request, $id)
    {
        $request->validate([
            'priority' => 'required|in:Low,Medium,Critical'
        ]);

        $ticket = Ticket::findOrFail($id);
        $ticket->priority = $request->priority;

        // update target SLA juga saat priority berubah
        if ($request->priority === 'Low') {
            $ticket->sla_response_minutes = 240;
            $ticket->sla_resolution_minutes = 1440;
        } elseif ($request->priority === 'Medium') {
            $ticket->sla_response_minutes = 120;
            $ticket->sla_resolution_minutes = 480;
        } elseif ($request->priority === 'Critical') {
            $ticket->sla_response_minutes = 30;
            $ticket->sla_resolution_minutes = 240;
        }

        $ticket->save();

        return response()->json([
            'success'  => true,
            'message'  => 'Priority tiket berhasil diperbarui!',
            'priority' => $ticket->priority
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $ticket = Ticket::findOrFail($id);

        if ($ticket->status !== 'open') {
            return response()->json([
                'success' => false,
                'message' => 'Tiket hanya bisa dibatalkan jika status masih OPEN.'
            ], 400);
        }

        $cancelNote = $request->cancel_note ?? null;

        if (!$cancelNote) {
            return response()->json([
                'success' => false,
                'message' => 'Catatan pembatalan wajib diisi.'
            ], 400);
        }

        $ticket->status = 'cancel';
        $ticket->cancel_note = $cancelNote;
        $ticket->finished_at = now();
        $ticket->resolved_at = now();
        $ticket->save();

        $ccList = [];
        if (!empty($ticket->cc_emails)) {
            $ccList = array_filter(array_map('trim', explode(',', $ticket->cc_emails)));
            $ccList = array_filter($ccList, fn($email) => filter_var($email, FILTER_VALIDATE_EMAIL));
        }

        Mail::to($ticket->email)
            ->cc($ccList)
            ->send(new \App\Mail\TicketCancelledUser($ticket));

        return response()->json([
            'success' => true,
            'message' => 'Tiket berhasil dibatalkan dan notifikasi dikirim!'
        ]);
    }

    public function transfer(Request $request, $id)
    {
        $request->validate([
            'new_technician_id' => 'required|exists:users,id'
        ]);

        $ticket = Ticket::findOrFail($id);
        $oldTechnician = $ticket->taken_by;
        $newTechnician = $request->new_technician_id;

        if ($ticket->status === 'open') {
            $ticket->status = 'in_progress';

            if (!$ticket->taken_at) {
                $ticket->taken_at = now();
            }

            if (!$ticket->started_at) {
                $ticket->started_at = now();
            }
        }

        $ticket->taken_by = $newTechnician;
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Ticket berhasil dialihkan!',
            'old_technician' => $oldTechnician,
            'new_technician' => $newTechnician
        ]);
    }

    public function chatAsk(Request $request)
    {
        $question = strtolower(trim($request->input('question')));

        if (!$question) {
            return response()->json(['answer' => 'Silakan ketik pertanyaan Anda terlebih dahulu.']);
        }

        $tickets = \App\Models\Ticket::where('status', 'resolved')
            ->whereNotNull('resolution_note')
            ->get(['title', 'description', 'resolution_note']);

        $bestMatch = null;
        $bestScore = 0;

        $questionWords = preg_split('/\s+/', $question);

        foreach ($tickets as $t) {
            $text = strtolower($t->title . ' ' . $t->description . ' ' . $t->resolution_note);
            $textWords = preg_split('/\s+/', $text);

            $commonWords = count(array_intersect($questionWords, $textWords));
            $totalWords = count($questionWords);

            $score = ($totalWords > 0) ? ($commonWords / $totalWords) * 100 : 0;

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestMatch = $t;
            }
        }

        if ($bestMatch && $bestScore >= 25) {
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

    public function myTickets(Request $request)
    {
        $search = trim($request->get('search', ''));
        $status = $request->get('status', '');
        $category = $request->get('category', '');

        $query = Ticket::with('takenByUser')
            ->where('user_id', auth()->id());

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('cabang', 'like', "%{$search}%")
                    ->orWhere('nama', 'like', "%{$search}%")
                    ->orWhereRaw(
                        "CONCAT(UPPER(LEFT(category,1)), UPPER(LEFT(klasifikasi,1)), LPAD(id, 3, '0')) LIKE ?",
                        ["%" . strtoupper($search) . "%"]
                    );
            });
        }

        if ($status !== '' && in_array($status, ['open', 'in_progress', 'resolved'])) {
            $query->where('status', $status);
        }

        if ($category !== '' && in_array($category, ['software', 'hardware', 'network&multimedia'])) {
            $query->where('category', $category);
        }

        $tickets = $query->latest()->paginate(10)->withQueryString();

        $allTickets = Ticket::where('user_id', auth()->id())->get();

        $totalTickets = $allTickets->count();
        $openTickets = $allTickets->where('status', 'open')->count();
        $progressTickets = $allTickets->where('status', 'in_progress')->count();
        $resolvedTickets = $allTickets->where('status', 'resolved')->count();

        return view('tickets.my-tickets', compact(
            'tickets',
            'totalTickets',
            'openTickets',
            'progressTickets',
            'resolvedTickets',
            'search',
            'status',
            'category'
        ));
    }

    public function myTicketDetail($id)
    {
        $ticket = Ticket::where('user_id', auth()->id())
            ->with('takenByUser')
            ->findOrFail($id);

        return view('tickets.my-ticket-detail', compact('ticket'));
    }

    public function submitFeedback(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', auth()->id())
            ->where('status', 'resolved')
            ->findOrFail($id);

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'feedback_comment' => 'nullable|string|max:1000',
            'is_confirmed_resolved' => 'required|boolean',
        ]);

        $ticket->update([
            'rating' => $validated['rating'],
            'feedback_comment' => $validated['feedback_comment'] ?? null,
            'feedback_at' => now(),
            'is_confirmed_resolved' => $validated['is_confirmed_resolved'],
        ]);

        if ((int) $validated['is_confirmed_resolved'] === 0) {
            $ticket->update([
                'status' => 'in_progress',
                'resolved_at' => null,
                'finished_at' => null,
            ]);
        }

        return redirect()
            ->route('tickets.my.detail', $ticket->id)
            ->with('success', 'Feedback berhasil dikirim.');
    }
}
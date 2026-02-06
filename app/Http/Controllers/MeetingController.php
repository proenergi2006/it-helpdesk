<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\User;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class MeetingController extends Controller
{
    public function index(Request $request)
    {
        $q = Meeting::query()->with(['attendees','creator'])->latest('meeting_at');

        if ($request->filled('q')) {
            $term = $request->q;
            $q->where(function($qq) use ($term){
                $qq->where('title','like',"%$term%")
                   ->orWhere('project_name','like',"%$term%")
                   ->orWhere('location','like',"%$term%");
            });
        }

        $meetings = $q->paginate(10)->withQueryString();
        return view('meetings.index', compact('meetings'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('meetings.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => ['required','string','max:255'],
            'project_name' => ['nullable','string','max:255'],
            'meeting_at'   => ['required','date'],
            'location'     => ['nullable','string','max:255'],
            'notes'        => ['nullable','string'],

            'attendees'    => ['nullable','array'],
            'attendees.*'  => ['integer','exists:users,id'],

            // action items arrays
            'tasks'        => ['nullable','array'],
            'tasks.*'      => ['nullable','string','max:255'],
            'owners'       => ['nullable','array'],
            'owners.*'     => ['nullable','integer','exists:users,id'],
            'dues'         => ['nullable','array'],
            'dues.*'       => ['nullable','date'],
            'statuses'     => ['nullable','array'],
            'statuses.*'   => ['nullable','in:todo,in_progress,done'],
        ]);

        $meeting = Meeting::create([
            'title'        => $validated['title'],
            'project_name' => $validated['project_name'] ?? null,
            'meeting_at'   => $validated['meeting_at'],
            'location'     => $validated['location'] ?? null,
            'notes'        => $validated['notes'] ?? null,
            'created_by'   => auth()->id(),
            'updated_by'   => auth()->id(),
        ]);

        $meeting->attendees()->sync($validated['attendees'] ?? []);

        // action items
        $tasks = $validated['tasks'] ?? [];
        foreach ($tasks as $i => $task) {
            $task = trim((string)$task);
            if ($task === '') continue;

            $meeting->actionItems()->create([
                'task'     => $task,
                'owner_id' => $validated['owners'][$i] ?? null,
                'due_date' => $validated['dues'][$i] ?? null,
                'status'   => $validated['statuses'][$i] ?? 'todo',
            ]);
        }

        return redirect()->route('meetings.index')->with('success','Meeting MoM berhasil dibuat.');
    }

    public function show(Meeting $meeting)
    {
        $meeting->load(['attendees','actionItems.owner','creator','updater']);
        return view('meetings.show', compact('meeting'));
    }

    public function edit(Meeting $meeting)
    {
        $meeting->load(['attendees','actionItems']);
        $users = User::orderBy('name')->get();
        return view('meetings.edit', compact('meeting','users'));
    }

    public function update(Request $request, Meeting $meeting)
    {
        $validated = $request->validate([
            'title'        => ['required','string','max:255'],
            'project_name' => ['nullable','string','max:255'],
            'meeting_at'   => ['required','date'],
            'location'     => ['nullable','string','max:255'],
            'notes'        => ['nullable','string'],

            'attendees'    => ['nullable','array'],
            'attendees.*'  => ['integer','exists:users,id'],

            'tasks'        => ['nullable','array'],
            'tasks.*'      => ['nullable','string','max:255'],
            'owners'       => ['nullable','array'],
            'owners.*'     => ['nullable','integer','exists:users,id'],
            'dues'         => ['nullable','array'],
            'dues.*'       => ['nullable','date'],
            'statuses'     => ['nullable','array'],
            'statuses.*'   => ['nullable','in:todo,in_progress,done'],
        ]);

        $meeting->update([
            'title'        => $validated['title'],
            'project_name' => $validated['project_name'] ?? null,
            'meeting_at'   => $validated['meeting_at'],
            'location'     => $validated['location'] ?? null,
            'notes'        => $validated['notes'] ?? null,
            'updated_by'   => auth()->id(),
        ]);

        $meeting->attendees()->sync($validated['attendees'] ?? []);

        // replace action items (simple approach)
        $meeting->actionItems()->delete();

        $tasks = $validated['tasks'] ?? [];
        foreach ($tasks as $i => $task) {
            $task = trim((string)$task);
            if ($task === '') continue;

            $meeting->actionItems()->create([
                'task'     => $task,
                'owner_id' => $validated['owners'][$i] ?? null,
                'due_date' => $validated['dues'][$i] ?? null,
                'status'   => $validated['statuses'][$i] ?? 'todo',
            ]);
        }

        return redirect()->route('meetings.show', $meeting)->with('success','Meeting MoM berhasil diupdate.');
    }

    public function destroy(Meeting $meeting)
    {
        $meeting->delete();
        return redirect()->route('meetings.index')->with('success','Meeting MoM dihapus.');
    }


   

public function exportPdf(Meeting $meeting)
{
    $meeting->load(['attendees','actionItems.owner','creator','updater']);

    $pdf = Pdf::loadView('meetings.pdf', compact('meeting'))
        ->setPaper('a4', 'portrait');

    $filename = 'MoM_' . preg_replace('/[^A-Za-z0-9_\-]/', '_', $meeting->title) . '_' . $meeting->meeting_at->format('Ymd_Hi') . '.pdf';

    return $pdf->download($filename);
}

}

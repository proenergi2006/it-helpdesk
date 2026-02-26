<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    private const STATUSES = ['backlog','todo','in_progress','review','done'];

    public function index(Request $request)
    {
        $q = Project::query()->with(['assignees','creator','updater'])->latest();

        if ($request->filled('q')) {
            $q->where('name', 'like', '%'.$request->q.'%');
        }
        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $q->where('category', $request->category);
        }
        if ($request->filled('assigned_to')) {
            $assigneeId = (int) $request->assigned_to;
            $q->whereHas('assignees', function ($qq) use ($assigneeId) {
                $qq->where('users.id', $assigneeId);
            });
        }
        
        if (request('mine') === '1') {
            $q->whereHas('assignees', fn($qq) => $qq->where('users.id', auth()->id()));
        }

        $projects = $q->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get(['id','name','email']);

        return view('projects.index', compact('projects','users'));
    }

    public function create()
    {
        $this->ensureProjectAdmin();
        $users = User::orderBy('name')->get(['id','name','email']);
        $statuses = self::STATUSES;

        return view('projects.create', compact('users','statuses'));
    }

    public function store(Request $request)
    {
        $this->ensureProjectAdmin();
        $validated = $request->validate([
            'name'        => ['required','string','max:200'],
            'category' => 'required|in:infra,program',
            'description' => ['nullable','string'], // HTML dari editor
            'status'      => ['required','in:backlog,todo,in_progress,review,done'],
            'start_date'  => ['nullable','date'],
            'due_date'    => ['nullable','date','after_or_equal:start_date'],
            'done_date'   => ['nullable','date','after_or_equal:start_date'],
        
            // MULTI ASSIGNEE
            'assignees'   => ['nullable','array'],
            'assignees.*' => ['integer','exists:users,id'],
        ]);
        
        $project = Project::create([
            'name'        => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'start_date'  => $validated['start_date'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'done_date'   => $validated['done_date'] ?? null,
            'created_by'  => auth()->id(),
            'updated_by'  => auth()->id(),
        ]);
        
        $project->assignees()->sync($validated['assignees'] ?? []);
        

        return redirect()->route('projects.index')->with('success', 'Project berhasil dibuat.');
    }

    public function edit(Project $project)
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        $statuses = self::STATUSES;

        return view('projects.edit', compact('project','users','statuses'));
    }

    public function update(Request $request, Project $project)
    {
        $validated = $request->validate([
            'name'        => ['required','string','max:200'],
            'category' => 'required|in:infra,program',
            'description' => ['nullable','string'],
            'status'      => ['required','in:backlog,todo,in_progress,review,done'],
            'start_date'  => ['nullable','date'],
            'due_date'    => ['nullable','date','after_or_equal:start_date'],
            'done_date'   => ['nullable','date','after_or_equal:start_date'],
        
            'assignees'   => ['nullable','array'],
            'assignees.*' => ['integer','exists:users,id'],
        ]);

        $beforeStatus = $project->getOriginal('status');
        $beforeStart  = $project->getOriginal('start_date');
        $beforeDue    = $project->getOriginal('due_date');
        $beforeDone   = $project->getOriginal('done_date');
        
        $project->update([
            'name'        => $validated['name'],
            'category' => $validated['category'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'start_date'  => $validated['start_date'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'done_date'   => $validated['done_date'] ?? null,
            'updated_by'  => auth()->id(),
        ]);

       // assignees sync
        $beforeAssignees = $project->assignees()->pluck('users.id')->all();
        $project->assignees()->sync($validated['assignees'] ?? []);
        $afterAssignees  = $project->assignees()->pluck('users.id')->all();

        // 1) status
        if ($beforeStatus !== $project->status) {
            $project->logActivity(
                'status_changed',
                'Mengubah status',
                ['from' => $beforeStatus, 'to' => $project->status]
            );
        }

        // 2) dates
            if ($beforeStart !== $project->start_date || $beforeDue !== $project->due_date || $beforeDone !== $project->done_date) {
                $project->logActivity(
                    'dates_changed',
                    'Mengubah timeline',
                    [
                        'from' => ['start'=>$beforeStart,'due'=>$beforeDue,'done'=>$beforeDone],
                        'to'   => ['start'=>$project->start_date,'due'=>$project->due_date,'done'=>$project->done_date],
                    ]
                );
            }

            // 3) assignees
            sort($beforeAssignees); sort($afterAssignees);
            if ($beforeAssignees !== $afterAssignees) {
                $project->logActivity(
                    'assignees_changed',
                    'Mengubah assignee',
                    ['from' => $beforeAssignees, 'to' => $afterAssignees]
                );
            }
        
        $project->assignees()->sync($validated['assignees'] ?? []);
        

        return redirect()->route('projects.index')->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(Project $project)
    {
        $this->ensureProjectAdmin();
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }

    public function show(Project $project)
{
    $project->load(['assignees', 'updates.user','activities.user']);
    return view('projects.show', compact('project'));
}

private function ensureProjectAdmin(): void
{
    $allowed = [
        'iwan.hermawan@proenergi.co.id',
        'reno.oktavian@proenergi.com',
    ];

    abort_unless(in_array(auth()->user()->email, $allowed, true), 403);
}

}

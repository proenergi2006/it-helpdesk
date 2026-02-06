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
        if ($request->filled('assigned_to')) {
            $q->where('assigned_to', $request->assigned_to);
        }

        $projects = $q->paginate(10)->withQueryString();
        $users = User::orderBy('name')->get(['id','name','email']);

        return view('projects.index', compact('projects','users'));
    }

    public function create()
    {
        $users = User::orderBy('name')->get(['id','name','email']);
        $statuses = self::STATUSES;

        return view('projects.create', compact('users','statuses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required','string','max:200'],
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
            'description' => ['nullable','string'],
            'status'      => ['required','in:backlog,todo,in_progress,review,done'],
            'start_date'  => ['nullable','date'],
            'due_date'    => ['nullable','date','after_or_equal:start_date'],
            'done_date'   => ['nullable','date','after_or_equal:start_date'],
        
            'assignees'   => ['nullable','array'],
            'assignees.*' => ['integer','exists:users,id'],
        ]);
        
        $project->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status'      => $validated['status'],
            'start_date'  => $validated['start_date'] ?? null,
            'due_date'    => $validated['due_date'] ?? null,
            'done_date'   => $validated['done_date'] ?? null,
            'updated_by'  => auth()->id(),
        ]);
        
        $project->assignees()->sync($validated['assignees'] ?? []);
        

        return redirect()->route('projects.index')->with('success', 'Project berhasil diupdate.');
    }

    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Project berhasil dihapus.');
    }

    public function show(Project $project)
{
    $project->load(['assignees', 'updates.user']);
    return view('projects.show', compact('project'));
}

}

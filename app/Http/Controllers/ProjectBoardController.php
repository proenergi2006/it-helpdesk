<?php
// app/Http/Controllers/ProjectBoardController.php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectBoardController extends Controller
{
    public function index()
    {
        $statuses = ['backlog','todo','in_progress','review','done'];
    
        $projects = Project::whereIn('status', $statuses)
            ->with('assignees') // biar chips muncul
            ->ordered()
            ->get()
            ->groupBy('status');
    
        $users = User::orderBy('name')->get(['id','name']);
    
        return view('projects.board', compact('projects','statuses','users'));
    }

    public function move(Request $request)
    {
        $data = $request->validate([
            'id'       => 'required|exists:projects,id',
            'status'   => 'required|string',
            'position' => 'required|integer',
        ]);

        Project::where('id', $data['id'])->update([
            'status'   => $data['status'],
            'position' => $data['position'],
        ]);

        return response()->json(['success' => true]);
    }

    public function quickAdd(Request $request)
{
    $data = $request->validate([
        'name'        => 'required|string|max:255',
        'status'      => 'required|in:backlog,todo,in_progress,review,done',
        'due_date'    => 'nullable|date',
        'assignees'   => 'nullable|array',
        'assignees.*' => 'integer|exists:users,id',
    ]);

    $lastPos = (int) Project::where('status', $data['status'])->max('position');
    $nextPos = $lastPos + 1;

    $project = Project::create([
        'name'        => $data['name'],
        'status'      => $data['status'],
        'position'    => $nextPos,
        'due_date'    => $data['due_date'] ?? null,
        'created_by'  => Auth::id(), // kalau kolom ini ada
        'updated_by'  => Auth::id(), // kalau kolom ini ada
        'description' => null,
    ]);

    if (!empty($data['assignees'])) {
        $project->assignees()->sync($data['assignees']);
    }

    // return minimal data untuk append card di UI
    return response()->json([
        'success' => true,
        'project' => [
          'id' => $project->id,
          'name' => $project->name,
          'due_date' => optional($project->due_date)->format('d M Y'),
          'assignees' => $project->assignees()->pluck('name')->take(3)->values(),
          'assignees_more' => max(0, $project->assignees()->count() - 3),
        ],
      ]);
      
}
}

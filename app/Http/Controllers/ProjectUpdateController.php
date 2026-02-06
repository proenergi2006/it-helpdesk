<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectUpdate;
use Illuminate\Http\Request;

class ProjectUpdateController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $validated = $request->validate([
            'content' => ['required','string'], // HTML dari Trix
        ]);

        $project->updates()->create([
            'user_id' => auth()->id(),
            'content' => $validated['content'],
        ]);

        return back()->with('success', 'Progress update berhasil ditambahkan.');
    }

    public function destroy(Project $project, ProjectUpdate $update)
    {
        // pastikan update milik project tsb
        abort_unless($update->project_id === $project->id, 404);

        $update->delete();
        return back()->with('success', 'Progress update dihapus.');
    }
}

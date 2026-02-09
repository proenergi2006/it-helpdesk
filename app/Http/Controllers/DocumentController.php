<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        $q = Document::query()->with(['creator','updater'])->latest();

        if ($request->filled('project')) {
            $q->where('project_name', 'like', '%'.$request->project.'%');
        }
        if ($request->filled('type')) {
            $q->where('type', $request->type);
        }

        $documents = $q->paginate(10)->withQueryString();

        return view('documents.index', compact('documents'));
    }

    public function create()
    {
        return view('documents.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_name' => ['required','string','max:150'],
            'title'        => ['required','string','max:200'],
            'type'         => ['required','in:CR,BRD,DEV,UAT,IMP,DOC,UG'],
            'notes'        => ['nullable','string','max:2000'],
            'file'         => ['required','file','max:20480'], // 20MB
        ]);

        $file = $request->file('file');

        // optional: filter extension
        $allowedExt = ['pdf','doc','docx','xls','xlsx','zip','png','jpg','jpeg'];
        $ext = strtolower($file->getClientOriginalExtension());
        if (!in_array($ext, $allowedExt, true)) {
            return back()->withErrors(['file' => 'Tipe file tidak diizinkan.'])->withInput();
        }

        $path = $file->store('documents', 'public');

        Document::create([
            'project_name'   => $validated['project_name'],
            'title'          => $validated['title'],
            'type'           => $validated['type'],
            'notes'          => $validated['notes'] ?? null,
            'original_name'  => $file->getClientOriginalName(),
            'path'           => $path,
            'mime'           => $file->getClientMimeType(),
            'size'           => $file->getSize(),
            'created_by'     => auth()->id(),
            'updated_by'     => auth()->id(),
        ]);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diupload.');
    }

    public function edit(Document $document)
    {
        return view('documents.edit', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $validated = $request->validate([
            'project_name' => ['required','string','max:150'],
            'title'        => ['required','string','max:200'],
            'type'         => ['required','in:CR,BRD,DEV,UAT,IMP,DOC,UG'],
            'notes'        => ['nullable','string','max:2000'],
            'file'         => ['nullable','file','max:20480'],
        ]);

        $payload = [
            'project_name' => $validated['project_name'],
            'title'        => $validated['title'],
            'type'         => $validated['type'],
            'notes'        => $validated['notes'] ?? null,
            'updated_by'   => auth()->id(),
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');

            $allowedExt = ['pdf','doc','docx','xls','xlsx','zip','png','jpg','jpeg'];
            $ext = strtolower($file->getClientOriginalExtension());
            if (!in_array($ext, $allowedExt, true)) {
                return back()->withErrors(['file' => 'Tipe file tidak diizinkan.'])->withInput();
            }

            // delete old
            if ($document->path && Storage::disk('public')->exists($document->path)) {
                Storage::disk('public')->delete($document->path);
            }

            $path = $file->store('documents', 'public');

            $payload += [
                'original_name' => $file->getClientOriginalName(),
                'path'          => $path,
                'mime'          => $file->getClientMimeType(),
                'size'          => $file->getSize(),
            ];
        }

        $document->update($payload);

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil diupdate.');
    }

    public function destroy(Document $document)
    {
        if ($document->path && Storage::disk('public')->exists($document->path)) {
            Storage::disk('public')->delete($document->path);
        }

        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Dokumen berhasil dihapus.');
    }

    public function download(Document $document)
    {
        abort_unless(Storage::disk('public')->exists($document->path), 404);

        return Storage::disk('public')->download($document->path, $document->original_name);
    }
    public function preview(Document $document)
{
    abort_unless(Storage::disk('public')->exists($document->path), 404);

    return response()->file(
        Storage::disk('public')->path($document->path),
        [
            'Content-Type' => $document->mime ?? 'application/octet-stream',
            // inline supaya bisa dibuka di iframe
            'Content-Disposition' => 'inline; filename="'.$document->original_name.'"',
        ]
    );
}

}

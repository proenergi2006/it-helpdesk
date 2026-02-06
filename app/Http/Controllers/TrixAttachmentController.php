<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrixAttachmentController extends Controller
{
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'file' => ['required','image','max:5120'],
    //     ]);
    
    //     $path = $request->file('file')->store('trix', 'public');
    
    //     $url = asset('storage/' . $path);
    
    //     // format yang Trix suka
    //     return response()->json([
    //         'url'  => $url,
    //         'href' => $url,
    //     ]);
    // }

    public function store(Request $request)
{
    $request->validate([
        'file' => ['required', 'file', 'max:5120'], // 5MB
    ]);

    $file = $request->file('file');
    $ext  = strtolower($file->getClientOriginalExtension());

    // allow list
    $allowed = ['png','jpg','jpeg','gif','webp','pdf','xls','xlsx','csv'];
    if (!in_array($ext, $allowed, true)) {
        return response()->json(['message' => 'File type not allowed.'], 422);
    }

    $path = $file->store('trix', 'public');
    $url  = asset('storage/'.$path);

    return response()->json([
        'url'         => $url,
        'href'        => $url,
        'filename'    => $file->getClientOriginalName(),
        'filesize'    => $file->getSize(),
        'contentType' => $file->getMimeType(),
    ]);
}

    
}

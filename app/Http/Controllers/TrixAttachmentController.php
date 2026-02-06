<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TrixAttachmentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => ['required','image','max:5120'],
        ]);
    
        $path = $request->file('file')->store('trix', 'public');
    
        $url = asset('storage/' . $path);
    
        // format yang Trix suka
        return response()->json([
            'url'  => $url,
            'href' => $url,
        ]);
    }
    
}

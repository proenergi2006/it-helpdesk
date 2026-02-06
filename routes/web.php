<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TrendController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TrixAttachmentController;
use App\Http\Controllers\ProjectUpdateController;

// HALAMAN UTAMA diarahkan ke TicketController@index
Route::get('/', [TicketController::class, 'index'])->name('welcome');

// Simpan ticket baru
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

// Optional: API route untuk auto-refresh
Route::get('/api/tickets', [TicketController::class, 'apiList'])->name('tickets.api');

Route::post('/chat/ask', [TicketController::class, 'chatAsk'])->name('chat.ask');

// Route default bawaan Breeze (biarkan untuk dashboard login)

Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::get('/trend', [TrendController::class, 'index'])->name('trend');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.export.pdf');
    Route::patch('/tickets/{id}/priority', [TicketController::class, 'updatePriority'])
        ->name('tickets.updatePriority');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::get('/tickets/{id}/detail', [TicketController::class, 'detail']);
    Route::put('/tickets/{id}/transfer', [TicketController::class, 'transfer'])
        ->name('tickets.transfer');
        Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
        ->name('documents.download');

    Route::resource('documents', DocumentController::class)->except(['show']);
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
    ->name('documents.preview');
    Route::resource('projects', ProjectController::class)->except(['show']);
    Route::middleware('auth')->post('/trix/upload', [TrixAttachmentController::class, 'store'])
    ->name('trix.upload');

     // progress / comment updates
     Route::post('projects/{project}/updates', [ProjectUpdateController::class, 'store'])->name('projects.updates.store');
     Route::delete('projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])->name('projects.updates.destroy');
 
     // trix upload (image/pdf/excel)
     Route::post('/trix/upload', [TrixAttachmentController::class, 'store'])->name('trix.upload');
     Route::resource('projects', ProjectController::class);


});

require __DIR__ . '/auth.php';

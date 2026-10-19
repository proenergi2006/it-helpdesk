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
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\ProjectBoardController;
use App\Http\Controllers\UserAccessManagementController;


// Approve/reject ticket lewat link email (signed URL, tanpa login)
Route::middleware('signed')->group(function () {
    Route::get('/approval-email/{id}', [TicketController::class, 'emailApprovalShow'])->name('approvals.email.show');
    Route::post('/approval-email/{id}', [TicketController::class, 'emailApprovalAct'])->name('approvals.email.act');
});

// HALAMAN UTAMA diarahkan ke TicketController@index
Route::get('/', [TicketController::class, 'index'])->name('welcome');

// Simpan ticket baru
//Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

// Optional: API route untuk auto-refresh
Route::get('/api/tickets', [TicketController::class, 'apiList'])->name('tickets.api');

Route::post('/chat/ask', [TicketController::class, 'chatAsk'])->name('chat.ask');

// Route default bawaan Breeze (biarkan untuk dashboard login)

// Login pembeda tampilan
Route::get('/login/user', fn () => redirect()->route('login', ['type' => 'user']))->name('login.user');
Route::get('/login/it', fn () => redirect()->route('login', ['type' => 'it']))->name('login.it');

// Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
//     Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
//     Route::get('/trend', [TrendController::class, 'index'])->name('trend');
//     Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
//     Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
//     Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.export.pdf');
//     Route::patch('/tickets/{id}/priority', [TicketController::class, 'updatePriority'])
//         ->name('tickets.updatePriority');
//     Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
//     Route::get('/tickets/{id}/detail', [TicketController::class, 'detail']);
//     Route::put('/tickets/{id}/transfer', [TicketController::class, 'transfer'])
//         ->name('tickets.transfer');
//         Route::get('/documents/{document}/download', [DocumentController::class, 'download'])
//         ->name('documents.download');

//     Route::resource('documents', DocumentController::class)->except(['show']);
//     Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])
//     ->name('documents.preview');
   
//     Route::middleware('auth')->post('/trix/upload', [TrixAttachmentController::class, 'store'])
//     ->name('trix.upload');

//      // progress / comment updates
//      Route::post('projects/{project}/updates', [ProjectUpdateController::class, 'store'])->name('projects.updates.store');
//      Route::delete('projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])->name('projects.updates.destroy');
 
//      // trix upload (image/pdf/excel)
//      Route::post('/trix/upload', [TrixAttachmentController::class, 'store'])->name('trix.upload');

//      Route::get('/projects/board', [ProjectBoardController::class, 'index'])->name('projects.board');
//      Route::post('/projects/board/move', [ProjectBoardController::class, 'move'])->name('projects.board.move');
//      Route::post('/projects/board/quick-add', [ProjectBoardController::class, 'quickAdd'])
//     ->name('projects.board.quickAdd');

//      Route::resource('projects', ProjectController::class);

//      Route::resource('meetings', MeetingController::class);


//      Route::get('meetings/{meeting}/export-pdf', [MeetingController::class, 'exportPdf'])
//     ->name('meetings.export.pdf');

   


// });

// Semua user yang sudah login
Route::middleware('auth')->group(function () {
    Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])->name('tickets.my');
    Route::get('/my-tickets/{id}', [TicketController::class, 'myTicketDetail'])->name('tickets.my.detail');
    Route::post('/my-tickets/{id}/feedback', [TicketController::class, 'submitFeedback'])->name('tickets.my.feedback');

    Route::get('/my-tickets/{id}/edit', [TicketController::class, 'editMyTicket'])->name('tickets.my.edit');
Route::put('/my-tickets/{id}', [TicketController::class, 'updateMyTicket'])->name('tickets.my.update');
    Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Hasil scan QR aset: publik, hanya menampilkan kode, nama, dan pemegang aset
Route::get('/assets/scan/{code}', [\App\Http\Controllers\AssetController::class, 'scan'])->name('assets.scan');
Route::middleware('auth')->get('/assets/scan/{code}/ticket', [\App\Http\Controllers\AssetController::class, 'scanTicket'])->name('assets.scan.ticket');

// Khusus IT
Route::middleware(['auth', 'role:it'])->group(function () {
    Route::get('/assets/labels', [\App\Http\Controllers\AssetController::class, 'labels'])->name('assets.labels');
    Route::get('/assets/import', [\App\Http\Controllers\AssetController::class, 'importForm'])->name('assets.import');
    Route::post('/assets/import', [\App\Http\Controllers\AssetController::class, 'import'])->name('assets.import.store');
    Route::get('/assets/import/template', [\App\Http\Controllers\AssetController::class, 'importTemplate'])->name('assets.import.template');
    Route::get('/assets/audits', [\App\Http\Controllers\AssetAuditController::class, 'index'])->name('assets.audits.index');
    Route::post('/assets/audits', [\App\Http\Controllers\AssetAuditController::class, 'store'])->name('assets.audits.store');
    Route::get('/assets/audits/{audit}', [\App\Http\Controllers\AssetAuditController::class, 'show'])->name('assets.audits.show');
    Route::post('/assets/audits/{audit}/scan', [\App\Http\Controllers\AssetAuditController::class, 'scan'])->name('assets.audits.scan');
    Route::post('/assets/audits/{audit}/verify/{asset}', [\App\Http\Controllers\AssetAuditController::class, 'verify'])->name('assets.audits.verify');
    Route::post('/assets/audits/{audit}/close', [\App\Http\Controllers\AssetAuditController::class, 'close'])->name('assets.audits.close');
    Route::get('/assets/reconcile', [\App\Http\Controllers\AssetController::class, 'reconcileForm'])->name('assets.reconcile');
    Route::post('/assets/reconcile', [\App\Http\Controllers\AssetController::class, 'reconcile'])->name('assets.reconcile.run');
    Route::post('/assets/{asset}/repairs', [\App\Http\Controllers\AssetController::class, 'storeRepair'])->name('assets.repairs.store');
    Route::post('/assets/{asset}/mutations', [\App\Http\Controllers\AssetController::class, 'mutate'])->name('assets.mutations.store');
    Route::resource('assets', \App\Http\Controllers\AssetController::class);
    Route::get('/approvals', [TicketController::class, 'approvals'])->name('approvals.index');
    Route::post('/approvals/{id}/approve', [TicketController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [TicketController::class, 'reject'])->name('approvals.reject');
    Route::patch('/tickets/{id}/status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::patch('/tickets/{id}/priority', [TicketController::class, 'updatePriority'])->name('tickets.updatePriority');
    Route::delete('/tickets/{id}', [TicketController::class, 'destroy'])->name('tickets.destroy');
    Route::get('/tickets/{id}/detail', [TicketController::class, 'detail']);
    Route::put('/tickets/{id}/transfer', [TicketController::class, 'transfer'])->name('tickets.transfer');

    Route::get('/trend', [TrendController::class, 'index'])->name('trend');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [ReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [ReportController::class, 'exportPDF'])->name('reports.export.pdf');

    Route::get('/documents/{document}/download', [DocumentController::class, 'download'])->name('documents.download');
    Route::get('/documents/{document}/preview', [DocumentController::class, 'preview'])->name('documents.preview');
    Route::resource('documents', DocumentController::class)->except(['show']);

    Route::post('/trix/upload', [TrixAttachmentController::class, 'store'])->name('trix.upload');

    Route::post('projects/{project}/updates', [ProjectUpdateController::class, 'store'])->name('projects.updates.store');
    Route::delete('projects/{project}/updates/{update}', [ProjectUpdateController::class, 'destroy'])->name('projects.updates.destroy');

    Route::get('/projects/board', [ProjectBoardController::class, 'index'])->name('projects.board');
    Route::post('/projects/board/move', [ProjectBoardController::class, 'move'])->name('projects.board.move');
    Route::post('/projects/board/quick-add', [ProjectBoardController::class, 'quickAdd'])->name('projects.board.quickAdd');

    Route::post('projects/{project}/sdlc/{stage}/document', [ProjectController::class, 'uploadSdlcDocument'])->name('projects.sdlc.upload');
    Route::get('projects/{project}/sdlc/{stage}/document', [ProjectController::class, 'downloadSdlcDocument'])->name('projects.sdlc.download');
    Route::post('projects/{project}/sdlc/{stage}/toggle', [ProjectController::class, 'toggleSdlcStage'])->name('projects.sdlc.toggle');
    Route::resource('projects', ProjectController::class);
    Route::resource('meetings', MeetingController::class);
    Route::get('meetings/{meeting}/export-pdf', [MeetingController::class, 'exportPdf'])->name('meetings.export.pdf');

    Route::resource('user-access-management', UserAccessManagementController::class)
        ->only(['index', 'create', 'store', 'show', 'edit', 'update', 'destroy']);

        Route::get('/reports/feedback', [ReportController::class, 'feedbackReport'])->name('reports.feedback');
        Route::get('/reports/sla', [ReportController::class, 'slaReport'])->name('reports.sla');

        Route::get('/user-access-management/export/excel', [UserAccessManagementController::class, 'exportExcel'])
    ->name('user-access-management.export.excel');

Route::get('/user-access-management/export/pdf', [UserAccessManagementController::class, 'exportPdf'])
    ->name('user-access-management.export.pdf');
});

require __DIR__ . '/auth.php';

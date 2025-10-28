<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TrendController;

// HALAMAN UTAMA diarahkan ke TicketController@index
Route::get('/', [TicketController::class, 'index'])->name('welcome');

// Simpan ticket baru
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');

// Optional: API route untuk auto-refresh
Route::get('/api/tickets', [TicketController::class, 'apiList'])->name('tickets.api');

// Route default bawaan Breeze (biarkan untuk dashboard login)

Route::get('/dashboard', [TicketController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth', '')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/tickets/{id}/update-status', [TicketController::class, 'updateStatus'])->name('tickets.updateStatus');
    Route::get('/trend', [TrendController::class, 'index'])->name('trend');
});

require __DIR__ . '/auth.php';

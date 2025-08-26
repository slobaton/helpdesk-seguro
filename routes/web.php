<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TicketAttachmentController;
use App\Http\Controllers\TicketAssignmentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketResolutionController;
use App\Http\Controllers\TicketStatusController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::resource('tickets', TicketController::class);

    Route::post('tickets/{ticket}/assign', [TicketAssignmentController::class, 'store'])
        ->name('tickets.assign')
        ->middleware('can:assign,ticket');

    Route::patch('tickets/{ticket}/status', [TicketStatusController::class, 'update'])
        ->name('tickets.status')
        ->middleware(['throttle:20,1','can:changeStatus,ticket']);

    Route::post('tickets/{ticket}/attachments', [TicketAttachmentController::class, 'store'])
        ->name('tickets.attachments.store')
        ->middleware(['throttle:10,1','can:update,ticket']);

    Route::get('attachments/{attachment}', [TicketAttachmentController::class, 'show'])
        ->name('attachments.show')
        ->middleware('can:view,attachment');

    Route::post('tickets/{ticket}/resolution', [TicketResolutionController::class, 'store'])
        ->name('tickets.resolution.store')
        ->middleware('can:update,ticket');

    Route::view('/board', 'tickets.board')
        ->name('tickets.board')
        ->middleware('role:Admin|Technician');
});

require __DIR__.'/auth.php';

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\SnapshotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return view('home'); 
});

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (only for authenticated users)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/desks/map', [DeskController::class, 'map'])->name('desks.map');
    Route::get('/desks/snapshot', [DeskController::class, 'saveSnapshot'])->name('desks.snapshot');
    Route::resource('desks', DeskController::class);

    Route::get('/snapshots/list', [SnapshotController::class, 'list']);
    Route::post('/snapshots/load', [SnapshotController::class, 'load']);
    Route::post('/snapshots/reset', [SnapshotController::class, 'reset']);

    Route::resource('reservations', ReservationController::class);
    Route::resource('customers', CustomerController::class);

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', RoleMiddleware::class . ':Admin'])->group(function () {
    Route::resource('roles', \App\Http\Controllers\RoleController::class);
});

require __DIR__.'/auth.php';

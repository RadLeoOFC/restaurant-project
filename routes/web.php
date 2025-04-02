<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\SnapshotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return view('home'); 
});


Route::get('/switch-language/{locale}', function ($locale) {
    session(['app_locale' => $locale]);
    App::setLocale($locale);
    return redirect()->back();
})->name('switch.language');

// Authenticated routes
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (only for authenticated users)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::resource('translations', TranslationController::class);
    
    Route::get('/desks/map', [DeskController::class, 'map'])->name('desks.map');
    Route::get('/desks/snapshot', [DeskController::class, 'saveSnapshot'])->name('desks.snapshot');
    Route::resource('desks', DeskController::class);

    Route::get('/snapshots/list', [SnapshotController::class, 'list']);
    Route::post('/snapshots/load', [SnapshotController::class, 'load']);
    Route::post('/snapshots/reset', [SnapshotController::class, 'reset']);

    Route::resource('reservations', ReservationController::class);
    Route::resource('customers', CustomerController::class);

    Route::resource('notification-templates', \App\Http\Controllers\NotificationTemplateController::class);

    Route::resource('languages', LanguageController::class);   

    Route::get('/test-locale', function () {
        return view('test-locale');
    });
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

<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\DeskController;
use App\Http\Controllers\SnapshotController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\TranslationController;
use App\Http\Controllers\ExternalDeskController;
use App\Http\Controllers\ReportTemplateController;
use App\Http\Controllers\ReportController;
use App\Console\Commands\UpdateDeskStatuses;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;
use App\Http\Middleware\RoleMiddleware;

Route::get('/', function () {
    return view('home'); 
});

Route::get('/reservations/check-conflict', [\App\Http\Controllers\ReservationController::class, 'checkConflict'])
    ->name('reservations.checkConflict');

Route::get('/run-desk-statuses', function () {
    Artisan::call('desks:update-statuses');
    return 'Desk statuses updated.';
});

Route::get('/desks/future-statuses', [ReservationController::class, 'getFutureStatuses']);

Route::post('/desks/select', [DeskController::class, 'selectTemporary']);


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
    
    Route::get('/desks/map', [DeskController::class, 'map'])->name('desks.map');
    Route::get('/desks/snapshot', [DeskController::class, 'saveSnapshot'])->name('desks.snapshot');
    Route::post('/desks/save-all', [DeskController::class, 'saveAll']);
    Route::resource('desks', DeskController::class);
    Route::post('/external-desks/save-all', [ExternalDeskController::class, 'saveAll'])->middleware('auth');
    Route::resource('external-desks', ExternalDeskController::class);

    Route::get('/snapshots/list', [SnapshotController::class, 'list']);
    Route::post('/snapshots/load', [SnapshotController::class, 'load']);
    Route::post('/snapshots/reset', [SnapshotController::class, 'reset']);

    Route::get('/reservations/{reservation}/modal', [ReservationController::class, 'showModal']);
    Route::get('/reservations/{reservation}/edit-modal', [ReservationController::class, 'editModal']);
    Route::resource('reservations', ReservationController::class);

    Route::resource('customers', CustomerController::class);

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
    Route::resource('notification-templates', \App\Http\Controllers\NotificationTemplateController::class);

    Route::resource('languages', LanguageController::class);
    
    Route::put('/translations/bulk-update', [TranslationController::class, 'bulkUpdate'])->name('translations.bulkUpdate');
    Route::delete('/translations/delete-key/{key}', [TranslationController::class, 'destroyKey'])->name('translations.destroyKey');
    Route::resource('translations', TranslationController::class);

    Route::resource('report-templates', ReportTemplateController::class)->middleware('auth');
    Route::get('/reports/{type}', [ReportController::class, 'generate'])->name('reports.generate');
    Route::get('/api/chart-data', [ReportController::class, 'chartData'])->name('reports.chart.data');

});

require __DIR__.'/auth.php';

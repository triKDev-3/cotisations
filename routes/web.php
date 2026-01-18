<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CotisationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [CotisationController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth', 'collecteur'])->prefix('collecteur')->name('collecteur.')->group(function () {
    Route::get('/dashboard', [CotisationController::class, 'collecteurDashboard'])->name('dashboard');
    Route::post('/cotisations', [CotisationController::class, 'store'])->name('cotisations.store');
    Route::get('/jeunes/create', [CotisationController::class, 'createJeune'])->name('jeunes.create');
    Route::post('/jeunes', [CotisationController::class, 'storeJeune'])->name('jeunes.store');
    Route::get('/jeunes/{user}/edit', [CotisationController::class, 'editJeune'])->name('jeunes.edit');
    Route::put('/jeunes/{user}', [CotisationController::class, 'updateJeune'])->name('jeunes.update');
    Route::delete('/jeunes/{user}', [CotisationController::class, 'destroyJeune'])->name('jeunes.destroy');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

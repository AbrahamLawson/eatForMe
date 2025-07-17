<?php

// Contrôleurs de disponibilités supprimés
use App\Http\Controllers\Match\ShowActiveSearchController;
use App\Http\Controllers\Match\PerformActiveSearchController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Routes pour les disponibilités - supprimées
    
    // Routes pour la recherche active
    Route::get('/active-search', ShowActiveSearchController::class)->name('active-search');
    Route::post('/active-search/search', PerformActiveSearchController::class)->name('active-search.search');
    Route::patch('/active-search/preferences', \App\Http\Controllers\Match\UpdateActiveSearchPreferencesController::class)->name('active-search.preferences.update');
    
    // Routes pour le test de matching
    Route::get('/match/test', [\App\Http\Controllers\Match\TestMatchingController::class, 'index'])->name('match.test.index');
    Route::post('/match/test', [\App\Http\Controllers\Match\TestMatchingController::class, 'testMatching'])->name('match.test');
    Route::post('/match/test/availability', [\App\Http\Controllers\Match\TestMatchingController::class, 'updateAvailability'])->name('match.test.availability');
});

require __DIR__.'/auth.php';

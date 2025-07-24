<?php

declare(strict_types=1);

// Contrôleurs de disponibilités supprimés
use App\Http\Controllers\Match\FindCompatibleUsersController;
use App\Http\Controllers\Match\ShowActiveSearchController;
use App\Http\Controllers\Match\TestMatchingController;
use App\Http\Controllers\Match\UpdateActiveSearchPreferencesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function (Router $router) {
    // Groupe de routes pour le profil utilisateur
    $router->group([
        'prefix' => '/profile',
        'as' => 'profile.',
    ], static function (Router $router): void {
        $router->get('/', [ProfileController::class, 'edit'])->name('edit');
        $router->patch('/', [ProfileController::class, 'update'])->name('update');
        $router->delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Groupe de routes pour la recherche active
    $router->group([
        'prefix' => '/active-search',
        'as' => 'active-search.',
    ], static function (Router $router): void {
        $router->get('/', ShowActiveSearchController::class)->name('index');
        // $router->post('/search', PerformActiveSearchController::class)->name('search'); // Commenté car remplacé par /api/matches/search
        $router->patch('/preferences', UpdateActiveSearchPreferencesController::class)->name('preferences.update');
    });

    // Groupe de routes pour le test de matching
    $router->group([
        'prefix' => '/match/test',
        'as' => 'match.test.',
    ], static function (Router $router): void {
        $router->get('/', [TestMatchingController::class, 'index'])->name('index');
        $router->post('/', [TestMatchingController::class, 'testMatching'])->name('run');
        $router->post('/availability', [TestMatchingController::class, 'updateAvailability'])->name('availability');
    });

    // Route de recherche de matchs (approche Breeze standard)
    $router->post('/matches/search', FindCompatibleUsersController::class)->name('matches.search');
});

require __DIR__.'/auth.php';

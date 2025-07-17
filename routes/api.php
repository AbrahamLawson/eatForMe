<?php

// Contrôleur de disponibilité supprimé
use App\Http\Controllers\Match\CreateMatchController;
use App\Http\Controllers\Message\GetConversationController;
use App\Http\Controllers\Message\MarkMessageAsReadController;
use App\Http\Controllers\Message\SendMessageController;
use App\Http\Controllers\Profile\CreateProfileController;
use App\Http\Controllers\Profile\GetProfilesByPreferencesController;
use App\Http\Controllers\Profile\UpdateProfileController;
use App\Http\Controllers\Restaurant\FindRestaurantsByLocationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Routes publiques
// Commentées temporairement car AuthController n'existe pas
// Route::post('/login', [AuthController::class, 'login']);
// Route::post('/register', [AuthController::class, 'register']);

// Routes protégées
Route::middleware('auth:sanctum')->group(function () {
    // Profils
    Route::post('/profiles', CreateProfileController::class);
    Route::put('/profiles/{id}', UpdateProfileController::class);
    Route::get('/profiles/search', GetProfilesByPreferencesController::class);
    
    // Disponibilités - route supprimée
    
    // Matches
    Route::post('/matches', CreateMatchController::class);
    
    // Messages
    Route::post('/messages', SendMessageController::class);
    Route::get('/conversations/{match_id}', GetConversationController::class);
    Route::patch('/messages/{id}/read', MarkMessageAsReadController::class);
    
    // Restaurants
    Route::get('/restaurants/search', FindRestaurantsByLocationController::class);
});

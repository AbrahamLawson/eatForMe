<?php

declare(strict_types=1);

use App\Http\Controllers\Match\CreateMatchController;
use App\Http\Controllers\Match\FindCompatibleUsersController;
use App\Http\Controllers\Match\GetCompatibilityScoreController;
use App\Http\Controllers\Match\SubmitMatchFeedbackController;
use App\Http\Controllers\Message\GetConversationController;
use App\Http\Controllers\Message\MarkMessageAsReadController;
use App\Http\Controllers\Message\SendMessageController;
use App\Http\Controllers\Profile\CreateProfileController;
use App\Http\Controllers\Profile\GetProfilesByPreferencesController;
use App\Http\Controllers\Profile\UpdateProfileController;
use App\Http\Controllers\Restaurant\FindRestaurantsByLocationController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

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

// API routes can be added here if needed for external integrations
// For internal app functionality, use web routes with Breeze authentication

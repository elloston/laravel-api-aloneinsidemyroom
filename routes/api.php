<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\MessageReactionController;
use Illuminate\Http\Request;
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

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/user/{username}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'current']);
    Route::post('/signout', [AuthController::class, 'signout']);
});


Route::get('/messages', [MessageController::class, 'index']);
Route::get('/messages/{message}', [MessageController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages', [MessageController::class, 'store']);
    Route::put('/messages/{message}', [MessageController::class, 'update']);
    Route::delete('/messages/{message}', [MessageController::class, 'destroy']);
    // Reactions
    Route::post('/messages/{message}/like', [MessageReactionController::class, 'like']);
    Route::post('/messages/{message}/dislike', [MessageReactionController::class, 'dislike']);
    // Trashed
    Route::get('/messages', [MessageController::class, 'trashed']);
    Route::get('/messages/{message}', [MessageController::class, 'showTrashed']);
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReactionController;
use App\Http\Controllers\Api\ReplyController;
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

/**
 * Auth
 */
Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/user/{username}', [UserController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'current']);
    Route::post('/signout', [AuthController::class, 'signout']);
});

// Reaction
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/perform_reaction_to/{type}/{id}', [ReactionController::class, 'performReaction']);
});

/**
 * Post
 */
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post}', [PostController::class, 'show']);
Route::get('/posts/{postId}/comments', [CommentController::class, 'getCommentsForPost']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/posts', [PostController::class, 'store']);
    Route::put('/posts/{post}', [PostController::class, 'update']);
    Route::delete('/posts/{post}', [PostController::class, 'destroy']);
    // Trashed
    Route::get('/posts/trashed/', [PostController::class, 'trashed']);
    Route::get('/posts/trashed/{post}', [PostController::class, 'showTrashed']);
});

/**
 * Comment
 */
Route::get('/comments', [CommentController::class, 'index']);
Route::get('/comments/{comment}', [CommentController::class, 'show']);
Route::get('/comments/{commentId}/replies', [ReplyController::class, 'getRepliesForComment']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/comments', [CommentController::class, 'store']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    // Trashed
    Route::get('/comments/trashed/', [CommentController::class, 'trashed']);
    Route::get('/comments/trashed/{comment}', [CommentController::class, 'showTrashed']);
});

/**
 * Reply
 */
Route::get('/replies', [ReplyController::class, 'index']);
Route::get('/replies/{reply}', [ReplyController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/replies', [ReplyController::class, 'store']);
    Route::put('/replies/{reply}', [ReplyController::class, 'update']);
    Route::delete('/replies/{reply}', [ReplyController::class, 'destroy']);
    // Trashed
    Route::get('/replies/trashed/', [ReplyController::class, 'trashed']);
    Route::get('/replies/trashed/{reply}', [ReplyController::class, 'showTrashed']);
});

<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('signup', [AuthController::class, 'register']);
        Route::post('signin', [AuthController::class, 'login']);
        Route::post('signout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    });
    Route::get('admins', [UserController::class, 'indexAdmin'])->middleware(['auth:sanctum', 'is_admin']);
    Route::apiResource('users', UserController::class)->middleware(['auth:sanctum', 'is_admin']);

    Route::apiResource('games', GameController::class);
});

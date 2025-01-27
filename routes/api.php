<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\BookController;
use App\Http\Controllers\api\v1\LoansController;
use App\Http\Controllers\api\v1\StatisticController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::prefix('auth')->group(function() {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function() {
    Route::prefix('books')->group(function() {
        Route::get('', [BookController::class, 'getAllBook']);
        Route::post('', [BookController::class, 'create']);
        Route::put('{id}', [BookController::class, 'update']);
        Route::delete('{id}', [BookController::class, 'delete']);
    });

    Route::prefix('loans')->group(function() {
        Route::post('', [LoansController::class, 'create']);
        Route::post('{id}/return', [LoansController::class, 'bookReturn']);
    });

    Route::prefix('stats')->group(function() {
        Route::get('popular-books', [StatisticController::class, 'popular']);
    });
});

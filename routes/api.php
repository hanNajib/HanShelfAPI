<?php

use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\BookController;
use App\Http\Controllers\api\v1\LoansController;
use App\Http\Controllers\api\v1\ShelfController;
use App\Http\Controllers\api\v1\StatisticController;
use App\Http\Middleware\AdminAPI;
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
        Route::get('/search', [BookController::class, 'search']);
        Route::middleware(AdminAPI::class)->group(function() {
            Route::post('', [BookController::class, 'create']);
            Route::put('{id}', [BookController::class, 'update']);
            Route::delete('{id}', [BookController::class, 'delete']);
        });
    });

    Route::prefix('users')->group(function() {
        Route::get('{id}/loans', [LoansController::class, 'userHistory']);
        Route::get('loans', [LoansController::class, 'selfHistory']);
    });

    Route::prefix('loans')->group(function() {
        Route::post('', [LoansController::class, 'create']);
        Route::post('{id}/return', [LoansController::class, 'bookReturn']);
        Route::prefix('history')->group(function() {
            Route::get('all', [LoansController::class, 'history'])->middleware(AdminAPI::class);
        });
    });

    Route::prefix('stats')->group(function() {
        Route::get('popular-books', [StatisticController::class, 'popular']);
    });

    Route::prefix('shelf')->group(function() {
        Route::get('{id}/books', [ShelfController::class, 'getBook']);
        Route::get('', [ShelfController::class, 'get']);
        Route::middleware(AdminAPI::class)->group(function() {
            Route::post('', [ShelfController::class, 'create']);
            Route::put('{id}', [ShelfController::class, 'update']);
            Route::delete('{id}', [ShelfController::class, 'delete']);
        });
    });
});

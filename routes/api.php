<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ServiceController;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Authentication
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/auth/me', [AuthController::class, 'me']);
        Route::post('/auth/logout', [AuthController::class, 'logout']);

        // Services
        Route::get('/services', [ServiceController::class, 'index'])
            ->can('viewAny', Service::class);

        Route::get('/services/{service}', [ServiceController::class, 'show'])
            ->can('view', 'service');

        Route::post('/services', [ServiceController::class, 'store'])
            ->can('create', Service::class);

        Route::put('/services/{service}', [ServiceController::class, 'update'])
            ->can('update', 'service');

        Route::delete('/services/{service}', [ServiceController::class, 'destroy'])
            ->can('delete', 'service');
    });
});
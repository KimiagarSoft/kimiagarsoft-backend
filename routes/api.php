<?php

use App\Http\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('/services', [ServiceController::class, 'index']);
    Route::get('/services/{service}', [ServiceController::class, 'show']);
    Route::post('/services', [ServiceController::class, 'store']);
    Route::put('/services/{service}', [ServiceController::class, 'update']);
    Route::delete('/services/{service}', [ServiceController::class, 'destroy']);
});


<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DataSourceController;
use App\Http\Controllers\Api\AuthController;

// Public routes (authentication)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
   
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('reports', ReportController::class);
    Route::apiResource('data-sources', DataSourceController::class);
    Route::post('/reports/execute', [ReportController::class, 'execute']);
});

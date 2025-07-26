<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use Illuminate\Http\JsonResponse;

Route::get('/', function () {
    return view('welcome');
});




Route::get('/api/report', [ReportController::class, 'generateReport']);

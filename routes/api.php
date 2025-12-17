<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\ProjectApiController;
use App\Http\Controllers\Api\TaskApiController;
use Illuminate\Support\Facades\Route;

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/projects', [ProjectApiController::class, 'index']);
    Route::get('/projects/{project}', [ProjectApiController::class, 'show']);
    Route::get('/tasks', [TaskApiController::class, 'index']);
});




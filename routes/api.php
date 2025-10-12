<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppApiController;

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json(['message' => 'API is working!']);
});

// App API routes
Route::get('/apps', [AppApiController::class, 'index']);
Route::get('/apps/{id}', [AppApiController::class, 'show']);

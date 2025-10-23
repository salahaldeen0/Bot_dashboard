<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AppApiController;

// Test route to verify API is working
Route::get('/test', function () {
    return response()->json([
        'message' => 'API is working!',
        'base_url' => config('app.url'),
        'timestamp' => now()->toISOString(),
        'version' => '1.0.0'
    ]);
});

// API Information endpoint
Route::get('/info', function () {
    return response()->json([
        'api_name' => 'Bot Dashboard API',
        'version' => '1.0.0',
        'base_url' => config('app.url'),
        'endpoints' => [
            'GET /api/test' => 'Test API connectivity',
            'GET /api/info' => 'API information',
            'GET /api/apps' => 'Get all apps (with pagination and search)',
            'GET /api/apps/{id}' => 'Get specific app by ID',
            'GET /api/apps/stats' => 'Get apps statistics',
        ],
        'timestamp' => now()->toISOString()
    ]);
});

// App API routes
Route::prefix('apps')->group(function () {
    Route::get('/', [AppApiController::class, 'index']);
    Route::get('/stats', [AppApiController::class, 'stats']);
    Route::get('/{id}', [AppApiController::class, 'show']);
});

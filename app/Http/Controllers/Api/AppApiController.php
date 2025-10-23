<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class AppApiController extends Controller
{
    /**
     * Get all apps with pagination and filtering
     */
    public function index(Request $request)
    {
        try {
            $query = App::query();
            
            // Add search functionality
            if ($request->has('search')) {
                $search = $request->get('search');
                $query->where(function($q) use ($search) {
                    $q->where('app_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('database_type', 'like', "%{$search}%");
                });
            }
            
            // Add filtering by database type
            if ($request->has('database_type')) {
                $query->where('database_type', $request->get('database_type'));
            }
            
            // Get pagination parameters
            $perPage = $request->get('per_page', 15);
            $page = $request->get('page', 1);
            
            $apps = $query->paginate($perPage, ['*'], 'page', $page);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'apps' => $apps->items(),
                    'pagination' => [
                        'current_page' => $apps->currentPage(),
                        'last_page' => $apps->lastPage(),
                        'per_page' => $apps->perPage(),
                        'total' => $apps->total(),
                        'from' => $apps->firstItem(),
                        'to' => $apps->lastItem(),
                    ]
                ],
                'message' => 'Apps retrieved successfully',
                'timestamp' => now()->toISOString(),
                'base_url' => config('app.url')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving apps: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }

    /**
     * Get a single app by ID
     */
    public function show($id)
    {
        try {
            $app = App::findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $app,
                'message' => 'App retrieved successfully',
                'timestamp' => now()->toISOString(),
                'base_url' => config('app.url')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'App not found',
                'timestamp' => now()->toISOString()
            ], 404);
        }
    }
    
    /**
     * Get apps statistics
     */
    public function stats()
    {
        try {
            $totalApps = App::count();
            $databaseTypes = App::select('database_type')
                               ->selectRaw('count(*) as count')
                               ->groupBy('database_type')
                               ->get();
            
            $recentApps = App::latest()->take(5)->get(['id', 'app_name', 'created_at']);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_apps' => $totalApps,
                    'database_types' => $databaseTypes,
                    'recent_apps' => $recentApps,
                ],
                'message' => 'App statistics retrieved successfully',
                'timestamp' => now()->toISOString(),
                'base_url' => config('app.url')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving app statistics: ' . $e->getMessage(),
                'timestamp' => now()->toISOString()
            ], 500);
        }
    }
}

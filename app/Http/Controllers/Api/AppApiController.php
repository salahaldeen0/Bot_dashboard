<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class AppApiController extends Controller
{
    /**
     * Get all apps
     */
    public function index()
    {
        try {
            $apps = App::all();
            return response()->json([
                'success' => true,
                'data' => $apps,
                'message' => 'Apps retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error retrieving apps: ' . $e->getMessage()
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
                'message' => 'App retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'App not found'
            ], 404);
        }
    }
}

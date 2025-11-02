<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RolePermissionController extends Controller
{
    /**
     * Get permissions for a specific role from external database
     */
    public function index($appId, $roleId)
    {
        try {
            $app = App::findOrFail($appId);
            
            if (!$app->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database not connected'
                ], 400);
            }
            
            $databaseService = app(DatabaseService::class);
            
            // Get role from external database
            $role = $databaseService->getRole($app, $roleId);
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }
            
            // Get permissions with tables from external database
            $tables = $databaseService->getPermissionsForRole($app, $roleId);
            
            return response()->json([
                'success' => true,
                'role' => [
                    'id' => $role['id'],
                    'role_name' => $role['role_name'],
                    'description' => $role['description'] ?? null,
                ],
                'tables' => $tables
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading permissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load permissions: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update permissions for a role in external database
     */
    public function update(Request $request, $appId, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.table_name' => 'required|string',
            'permissions.*.actions' => 'array',
        ]);

        try {
            $app = App::findOrFail($appId);
            
            if (!$app->is_connected) {
                return response()->json([
                    'success' => false,
                    'message' => 'Database not connected'
                ], 400);
            }
            
            $databaseService = app(DatabaseService::class);
            
            // Verify role exists
            $role = $databaseService->getRole($app, $roleId);
            
            if (!$role) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found'
                ], 404);
            }

            // Update permissions in external database
            $databaseService->updatePermissionsForRole($app, $roleId, $request->permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions updated successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Error updating permissions: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to update permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}

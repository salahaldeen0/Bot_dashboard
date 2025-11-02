<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppRole;
use App\Services\DatabaseService;
use Illuminate\Http\Request;

class AppRoleController extends Controller
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    public function index($appId)
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }

        try {
            // Get roles from connected database's ai_bot_roles table
            $roles = $this->databaseService->getRoles($app, $app->id);
            
            return response()->json([
                'success' => true,
                'data' => $roles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch roles: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request, $appId)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }

        try {
            // Insert role into connected database's ai_bot_roles table
            $roleId = $this->databaseService->insertRole(
                $app,
                $app->id,
                $request->role_name,
                $request->description
            );

            return response()->json([
                'success' => true,
                'message' => 'Role created successfully in connected database!',
                'data' => [
                    'id' => $roleId,
                    'app_id' => $app->id,
                    'role_name' => $request->role_name,
                    'description' => $request->description,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $appId, $roleId)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }

        try {
            // Update role in connected database's ai_bot_roles table
            $this->databaseService->updateRole(
                $app,
                $roleId,
                $request->role_name,
                $request->description
            );

            // Get updated role
            $role = $this->databaseService->getRole($app, $roleId);

            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully in connected database!',
                'data' => $role
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update role: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($appId, $roleId)
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }

        try {
            // Delete role from connected database's ai_bot_roles table
            $this->databaseService->deleteRole($app, $roleId);

            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully from connected database!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete role: ' . $e->getMessage()
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class BotDataController extends Controller
{
    private DatabaseService $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Get statistics about AI bot tables
     */
    public function getStats($appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        try {
            $stats = $this->databaseService->getBotTableStats($app);
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch stats: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Check if AI bot tables exist
     */
    public function checkTables($appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        try {
            $status = $this->databaseService->checkBotTablesExist($app);
            
            return response()->json([
                'success' => true,
                'tables' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to check tables: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new role
     */
    public function createRole(Request $request, $appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string'
        ]);

        try {
            $roleId = $this->databaseService->insertRole(
                $app,
                $app->id,
                $request->role_name,
                $request->description
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully.',
                'role_id' => $roleId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all roles
     */
    public function getRoles($appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        try {
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

    /**
     * Create a new user
     */
    public function createUser(Request $request, $appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'role_id' => 'nullable|integer'
        ]);

        try {
            $userId = $this->databaseService->insertUser(
                $app,
                $app->id,
                $request->name,
                $request->phone,
                $request->role_id
            );
            
            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'user_id' => $userId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get all users
     */
    public function getUsers($appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        try {
            $users = $this->databaseService->getUsers($app, $app->id);
            
            return response()->json([
                'success' => true,
                'data' => $users
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new permission
     */
    public function createPermission(Request $request, $appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        $request->validate([
            'role_id' => 'required|integer',
            'permission' => 'required|string|max:255',
            'actions' => 'nullable|string'
        ]);

        try {
            $permissionId = $this->databaseService->insertPermission(
                $app,
                $app->id,
                $request->role_id,
                $request->permission,
                $request->actions
            );
            
            return response()->json([
                'success' => true,
                'message' => 'Permission created successfully.',
                'permission_id' => $permissionId
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get permissions for a role
     */
    public function getPermissionsByRole(Request $request, $appId): JsonResponse
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected.'
            ], 400);
        }

        $request->validate([
            'role_id' => 'required|integer'
        ]);

        try {
            $permissions = $this->databaseService->getPermissionsByRole($app, $request->role_id);
            
            return response()->json([
                'success' => true,
                'data' => $permissions
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch permissions: ' . $e->getMessage()
            ], 500);
        }
    }
}

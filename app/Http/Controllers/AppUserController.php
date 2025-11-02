<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppUser;
use App\Services\DatabaseService;
use Illuminate\Http\Request;

class AppUserController extends Controller
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
            // Get users from connected database's ai_bot_users table
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

    public function store(Request $request, $appId)
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role_id' => 'nullable|integer',
        ]);

        try {
            // Insert user into connected database's ai_bot_users table
            $userId = $this->databaseService->insertUser(
                $app,
                $app->id,
                $validated['name'],
                $validated['phone'],
                $validated['role_id'] ?? null
            );

            // Increment users_count
            $app->increment('users_count');

            return response()->json([
                'success' => true,
                'message' => 'User created successfully in connected database!',
                'data' => [
                    'id' => $userId,
                    'app_id' => $app->id,
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'role_id' => $validated['role_id'] ?? null,
                ],
                'users_count' => $app->users_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $appId, $userId)
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role_id' => 'nullable|integer',
        ]);

        try {
            // Update user in connected database's ai_bot_users table
            $this->databaseService->updateUser(
                $app,
                $userId,
                $validated['name'],
                $validated['phone'],
                $validated['role_id'] ?? null
            );

            // Get updated user
            $user = $this->databaseService->getUser($app, $userId);

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully in connected database!',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($appId, $userId)
    {
        $app = App::findOrFail($appId);

        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database is not connected. Please connect to a database first.'
            ], 400);
        }

        try {
            // Delete user from connected database's ai_bot_users table
            $this->databaseService->deleteUser($app, $userId);
            
            // Decrement users_count
            $app->decrement('users_count');

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully from connected database!',
                'users_count' => $app->users_count
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }
}

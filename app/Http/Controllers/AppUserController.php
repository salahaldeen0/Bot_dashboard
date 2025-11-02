<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppUser;
use Illuminate\Http\Request;

class AppUserController extends Controller
{
    public function index($appId)
    {
        $app = App::findOrFail($appId);
        $users = AppUser::with('role')->where('app_id', $appId)->orderBy('created_at', 'desc')->get();
        
        // Format users with role name
        $formattedUsers = $users->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'phone' => $user->phone,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->role_name : 'No Role',
                'created_at' => $user->created_at,
            ];
        });
        
        return response()->json([
            'success' => true,
            'data' => $formattedUsers
        ]);
    }

    public function store(Request $request, $appId)
    {
        $app = App::findOrFail($appId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role_id' => 'nullable|exists:app_roles,id',
        ]);

        $user = AppUser::create([
            'app_id' => $appId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'role_id' => $validated['role_id'] ?? null,
        ]);

        // Increment users_count
        $app->increment('users_count');

        return response()->json([
            'success' => true,
            'message' => 'User created successfully!',
            'data' => $user,
            'users_count' => $app->users_count
        ]);
    }

    public function update(Request $request, $appId, $userId)
    {
        $app = App::findOrFail($appId);
        $user = AppUser::where('app_id', $appId)->findOrFail($userId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'role_id' => 'nullable|exists:app_roles,id',
        ]);

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully!',
            'data' => $user
        ]);
    }

    public function destroy($appId, $userId)
    {
        $app = App::findOrFail($appId);
        $user = AppUser::where('app_id', $appId)->findOrFail($userId);
        
        $user->delete();
        
        // Decrement users_count
        $app->decrement('users_count');

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully!',
            'users_count' => $app->users_count
        ]);
    }
}

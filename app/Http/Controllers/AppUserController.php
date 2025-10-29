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
        $users = AppUser::where('app_id', $appId)->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    public function store(Request $request, $appId)
    {
        $app = App::findOrFail($appId);
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $user = AppUser::create([
            'app_id' => $appId,
            'name' => $validated['name'],
            'phone' => $validated['phone'],
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

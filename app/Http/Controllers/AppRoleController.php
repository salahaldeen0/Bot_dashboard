<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppRole;
use Illuminate\Http\Request;

class AppRoleController extends Controller
{
    public function index($appId)
    {
        $app = App::findOrFail($appId);
        $roles = $app->roles()->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    public function store(Request $request, $appId)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);

        $role = AppRole::create([
            'app_id' => $app->id,
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ]);
    }

    public function update(Request $request, $appId, $roleId)
    {
        $request->validate([
            'role_name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);
        $role = AppRole::where('app_id', $app->id)->findOrFail($roleId);

        $role->update([
            'role_name' => $request->role_name,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    public function destroy($appId, $roleId)
    {
        $app = App::findOrFail($appId);
        $role = AppRole::where('app_id', $app->id)->findOrFail($roleId);
        
        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}

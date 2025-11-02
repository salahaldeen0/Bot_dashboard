<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\AppRole;
use App\Models\RolePermission;
use App\Models\SchemaTable;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    /**
     * Get permissions for a specific role
     */
    public function index($appId, $roleId)
    {
        $app = App::findOrFail($appId);
        $role = AppRole::where('app_id', $app->id)->findOrFail($roleId);
        
        // Get all available tables (permissions) from schema_tables
        $availableTables = SchemaTable::where('app_id', $app->id)
            ->where('active_flag', true)
            ->orderBy('table_name')
            ->get(['id', 'table_name']);
        
        // Get existing permissions for this role
        $permissions = RolePermission::where('role_id', $role->id)->get();
        
        // Create a map of permissions by table name
        $permissionsMap = [];
        foreach ($permissions as $permission) {
            $permissionsMap[$permission->permission] = $permission->actions;
        }
        
        // Build response with all tables and their permission status
        $tablesWithPermissions = $availableTables->map(function ($table) use ($permissionsMap) {
            return [
                'table_name' => $table->table_name,
                'actions' => $permissionsMap[$table->table_name] ?? [],
            ];
        });
        
        return response()->json([
            'success' => true,
            'role' => $role,
            'tables' => $tablesWithPermissions
        ]);
    }

    /**
     * Update permissions for a role
     */
    public function update(Request $request, $appId, $roleId)
    {
        $request->validate([
            'permissions' => 'required|array',
            'permissions.*.table_name' => 'required|string',
            'permissions.*.actions' => 'array',
        ]);

        $app = App::findOrFail($appId);
        $role = AppRole::where('app_id', $app->id)->findOrFail($roleId);

        foreach ($request->permissions as $permissionData) {
            $tableName = $permissionData['table_name'];
            $actions = $permissionData['actions'] ?? [];

            if (empty($actions)) {
                // If no actions, delete the permission record
                RolePermission::where('role_id', $role->id)
                    ->where('permission', $tableName)
                    ->delete();
            } else {
                // Update or create the permission record
                RolePermission::updateOrCreate(
                    [
                        'role_id' => $role->id,
                        'permission' => $tableName,
                    ],
                    [
                        'actions' => $actions,
                    ]
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Permissions updated successfully'
        ]);
    }
}

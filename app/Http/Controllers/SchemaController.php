<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Models\SchemaTable;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Exception;

class SchemaController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Get paginated tables for an app
     */
    public function getTables(Request $request, $appId)
    {
        $app = App::findOrFail($appId);
        
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '');

        $query = $app->schemaTables();

        if ($search) {
            $query->where('table_name', 'LIKE', "%{$search}%");
        }

        $tables = $query->orderBy('table_name')->paginate($perPage);

        return response()->json($tables);
    }

    /**
     * Sync tables from external database
     */
    public function syncTables($appId)
    {
        $app = App::findOrFail($appId);

        try {
            $this->databaseService->syncTables($app);
            
            // Mark schema as synced to unlock Users tab
            $app->update(['has_synced_schema' => true]);
            
            return response()->json([
                'success' => true,
                'message' => 'Tables synced successfully'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to sync tables: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update table keywords
     */
    public function updateKeywords(Request $request, $appId, $tableId)
    {
        $request->validate([
            'keywords' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);
        $schemaTable = SchemaTable::where('app_id', $appId)
            ->where('id', $tableId)
            ->firstOrFail();

        $schemaTable->update([
            'keywords' => $request->keywords,
        ]);

        // Update in external database
        try {
            $this->databaseService->updateExternalSchemaTable(
                $app,
                $schemaTable->table_name,
                $request->keywords,
                $schemaTable->active_flag
            );

            return response()->json([
                'success' => true,
                'message' => 'Keywords updated successfully',
                'data' => $schemaTable
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Keywords saved locally but failed to update external database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active flag
     */
    public function toggleActive($appId, $tableId)
    {
        $app = App::findOrFail($appId);
        $schemaTable = SchemaTable::where('app_id', $appId)
            ->where('id', $tableId)
            ->firstOrFail();

        $schemaTable->update([
            'active_flag' => !$schemaTable->active_flag,
        ]);

        // Update in external database
        try {
            $this->databaseService->updateExternalSchemaTable(
                $app,
                $schemaTable->table_name,
                $schemaTable->keywords,
                $schemaTable->active_flag
            );

            return response()->json([
                'success' => true,
                'message' => 'Active status updated successfully',
                'data' => $schemaTable
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Status saved locally but failed to update external database: ' . $e->getMessage()
            ], 500);
        }
    }
}

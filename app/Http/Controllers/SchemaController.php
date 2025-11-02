<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;

class SchemaController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Get paginated tables for an app from external database
     */
    public function getTables(Request $request, $appId)
    {
        $app = App::findOrFail($appId);
        
        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database not connected'
            ], 400);
        }
        
        $perPage = $request->get('per_page', 10);
        $search = $request->get('search', '') ?? '';
        $page = $request->get('page', 1);

        try {
            $result = $this->databaseService->getSchemaTables($app, $page, $perPage, $search);
            
            // Add success flag to the response
            $result['success'] = true;
            
            return response()->json($result);
        } catch (Exception $e) {
            Log::error('Failed to fetch schema tables for app ' . $appId . ': ' . $e->getMessage(), [
                'exception' => $e,
                'app_id' => $appId,
                'database_type' => $app->database_type
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch tables: ' . $e->getMessage()
            ], 500);
        }
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
     * Update table keywords in external database
     */
    public function updateKeywords(Request $request, $appId, $tableId)
    {
        $request->validate([
            'keywords' => 'nullable|string',
        ]);

        $app = App::findOrFail($appId);
        
        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database not connected'
            ], 400);
        }

        try {
            // Get the table info from external database
            $schemaTable = $this->databaseService->getSchemaTable($app, $tableId);
            
            if (!$schemaTable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }

            // Update in external database
            $this->databaseService->updateExternalSchemaTable(
                $app,
                $schemaTable['table_name'],
                $request->keywords,
                $schemaTable['active_flag']
            );

            // Get updated table
            $updatedTable = $this->databaseService->getSchemaTable($app, $tableId);

            return response()->json([
                'success' => true,
                'message' => 'Keywords updated successfully',
                'data' => $updatedTable
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update keywords: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Toggle active flag in external database
     */
    public function toggleActive($appId, $tableId)
    {
        $app = App::findOrFail($appId);
        
        if (!$app->is_connected) {
            return response()->json([
                'success' => false,
                'message' => 'Database not connected'
            ], 400);
        }

        try {
            // Get the table info from external database
            $schemaTable = $this->databaseService->getSchemaTable($app, $tableId);
            
            if (!$schemaTable) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found'
                ], 404);
            }

            // Toggle active flag
            $newActiveFlag = !$schemaTable['active_flag'];

            // Update in external database
            $this->databaseService->updateExternalSchemaTable(
                $app,
                $schemaTable['table_name'],
                $schemaTable['keywords'],
                $newActiveFlag
            );

            // Get updated table
            $updatedTable = $this->databaseService->getSchemaTable($app, $tableId);

            return response()->json([
                'success' => true,
                'message' => 'Active status updated successfully',
                'data' => $updatedTable
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update active status: ' . $e->getMessage()
            ], 500);
        }
    }
}

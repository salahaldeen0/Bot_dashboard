<?php

namespace App\Http\Controllers;

use App\Models\App;
use App\Services\DatabaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Exception;

class AppController extends Controller
{
    protected $databaseService;

    public function __construct(DatabaseService $databaseService)
    {
        $this->databaseService = $databaseService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $apps = App::all();
        return view('apps.index', compact('apps'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        return view('apps.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'app_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'database_type' => 'nullable|string|max:50',
            'database_name' => 'nullable|string|max:255',
            'port' => 'nullable|integer|min:1|max:65535',
            'host' => 'nullable|string|max:255',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
        // Only encrypt password if provided
        if ($request->filled('password')) {
            $data['password'] = Crypt::encryptString($request->password);
        }

        $app = App::create($data);

        return redirect()->route('apps.edit', $app->id)
            ->with('success', 'App created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $app = App::findOrFail($id);
        return view('apps.show', compact('app'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $app = App::findOrFail($id);
        return view('apps.edit', compact('app'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $app = App::findOrFail($id);
        
        $request->validate([
            'app_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'phone_number' => 'nullable|string|max:20',
            'database_type' => 'required|string|max:50',
            'database_name' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'host' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        
        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Crypt::encryptString($request->password);
        } else {
            unset($data['password']);
        }

        $app->update($data);

        return redirect()->route('apps.index')->with('success', 'App updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $app = App::findOrFail($id);
        $app->delete();

        return redirect()->route('apps.index')->with('success', 'App deleted successfully!');
    }

    /**
     * Connect to database and sync schema tables
     */
    public function connectDatabase(Request $request, string $id)
    {
        $app = App::findOrFail($id);

        // Validate database connection fields
        $request->validate([
            'database_type' => 'required|string|max:50',
            'database_name' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'host' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'nullable|string|max:255',
        ]);

        // Update app with database credentials
        $data = $request->only(['database_type', 'database_name', 'port', 'host', 'username']);
        
        // Only update password if provided
        if ($request->filled('password')) {
            $data['password'] = Crypt::encryptString($request->password);
        }
        
        $app->update($data);

        try {
            // Test the connection
            if ($this->databaseService->testConnection($app)) {
                // Create schema_table in the external database
                $this->databaseService->createSchemaTable($app);
                
                // Sync tables from external database to local schema_tables
                $this->databaseService->syncTables($app);
                
                // Mark as connected
                $app->update(['is_connected' => true]);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Database connected successfully! Schema tables have been synced.',
                    'table_count' => $app->schemaTables()->count()
                ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Database connection failed: ' . $e->getMessage()
            ], 422);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to establish database connection.'
        ], 422);
    }
}
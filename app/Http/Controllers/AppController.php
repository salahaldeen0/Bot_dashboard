<?php

namespace App\Http\Controllers;

use App\Models\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AppController extends Controller
{
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
            'database_type' => 'required|string|max:50',
            'database_name' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'host' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($request->password);

        App::create($data);

        return redirect()->route('apps.index')->with('success', 'App created successfully!');
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
            $data['password'] = Hash::make($request->password);
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
}

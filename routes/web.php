<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\SessionsController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\SchemaController;
use App\Http\Controllers\AppUserController;

// Redirect root to sign-in
Route::get('/', function () {return redirect('sign-in');})->middleware('guest');

// Authentication Routes
Route::get('sign-up', [RegisterController::class, 'create'])->middleware('guest')->name('register');
Route::post('sign-up', [RegisterController::class, 'store'])->middleware('guest');
Route::get('sign-in', [SessionsController::class, 'create'])->middleware('guest')->name('login');
Route::post('sign-in', [SessionsController::class, 'store'])->middleware('guest')->name('login.store');
Route::post('verify', [SessionsController::class, 'show'])->middleware('guest');
Route::post('reset-password', [SessionsController::class, 'update'])->middleware('guest')->name('password.update');
Route::get('verify', function () {
	return view('sessions.password.verify');
})->middleware('guest')->name('verify'); 
Route::get('/reset-password/{token}', function ($token) {
	return view('sessions.password.reset', ['token' => $token]);
})->middleware('guest')->name('password.reset');

Route::post('sign-out', [SessionsController::class, 'destroy'])->middleware('auth')->name('logout');

// Protected Routes
Route::group(['middleware' => 'auth'], function () {
	// Dashboard
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
	
	// User Management
	Route::get('user-management', function () {
		return view('pages.laravel-examples.user-management');
	})->name('user-management');
	
	// App Management Routes
	Route::resource('apps', AppController::class);
	Route::post('apps/{app}/connect-database', [AppController::class, 'connectDatabase'])->name('apps.connect');
	
	// Schema Management Routes
	Route::get('apps/{app}/schema/tables', [SchemaController::class, 'getTables'])->name('schema.tables');
	Route::post('apps/{app}/schema/sync', [SchemaController::class, 'syncTables'])->name('schema.sync');
	Route::put('apps/{app}/schema/tables/{table}/keywords', [SchemaController::class, 'updateKeywords'])->name('schema.updateKeywords');
	Route::post('apps/{app}/schema/tables/{table}/toggle', [SchemaController::class, 'toggleActive'])->name('schema.toggleActive');
	
	// App Users Management Routes
	Route::get('apps/{app}/users', [AppUserController::class, 'index'])->name('app-users.index');
	Route::post('apps/{app}/users', [AppUserController::class, 'store'])->name('app-users.store');
	Route::put('apps/{app}/users/{user}', [AppUserController::class, 'update'])->name('app-users.update');
	Route::delete('apps/{app}/users/{user}', [AppUserController::class, 'destroy'])->name('app-users.destroy');
});
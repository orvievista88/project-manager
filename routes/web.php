<?php

// routes/web.php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;

//authentications
Route::get('/', function () {
    return redirect()->route('login'); // Redirect to the login page if the user is not authenticated
});

// Public Routes (Redirect to dashboard if already logged in)
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);
});

// Protected Routes (Only for logged-in users)
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', function () {
        return view('pages.dashboard');
    })->name('dashboard');

    // Projects Routes
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Logout
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
//end of authentication


// Profile Route (Make sure to create the controller or view for profile)
Route::get('/profile', function () {
    return view('profile'); // You should create this view (e.g., resources/views/profile.blade.php)
})->middleware('auth')->name('profile');

// Projects Route (Make sure to create the controller or view for projects)

// Define routes for the projects

Route::get('/projects', [ProjectController::class, 'index'])->middleware('auth')->name('projects.index');
Route::get('/projects/create', [ProjectController::class, 'create'])->middleware('auth')->name('projects.create');
Route::post('/projects', [ProjectController::class, 'store'])->middleware('auth')->name('projects.store');
Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->middleware('auth')->name('projects.edit');
Route::put('/projects/{project}', [ProjectController::class, 'update'])->middleware('auth')->name('projects.update');
Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->middleware('auth')->name('projects.destroy');
// end of project routes


//define routes for users
Route::middleware(['auth'])->group(function () {
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    
    // Add this line below:
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// end of user routes


// Tasks Route (Make sure to create the controller or view for tasks)
Route::get('/tasks', function () {
    return view('tasks'); // You should create this view (e.g., resources/views/tasks.blade.php)
})->middleware('auth')->name('tasks');

// Logout Route
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

// Include default Breeze routes (Registration, Password Reset, etc.)
// Check if you have this file in your routes folder!
if (file_exists(__DIR__ . '/auth.php')) {
    require __DIR__ . '/auth.php';
}

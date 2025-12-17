<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
	// List all users (Admin only)
	public function index()
	{
		$users = User::with('role')->get();
		$roles = Role::all(); // For the dropdown in your Modal
		return view('pages.users', compact('users', 'roles'));
	}

	// AJAX Store
	public function store(Request $request)
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
			'password' => ['required', 'confirmed', Rules\Password::defaults()],
			'role_id' => ['required', 'exists:roles,id'],
		]);

		$user = User::create([
			'name' => $request->name,
			'email' => $request->email,
			'password' => Hash::make($request->password),
			'role_id' => $request->role_id,
		]);

		return response()->json(['success' => 'User created successfully!', 'user' => $user]);
	}

	public function edit(User $user)
	{
		// Simply return the user as JSON so the AJAX can fill the form
		return response()->json($user);
	}

	// AJAX Update
	public function update(Request $request, User $user)
	{
		$request->validate([
			'name' => ['required', 'string', 'max:255'],
			'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
			'role_id' => ['required', 'exists:roles,id'],
		]);

		$user->update([
			'name' => $request->name,
			'email' => $request->email,
			'role_id' => $request->role_id,
		]);

		// Only update password if provided
		if ($request->filled('password')) {
			$user->update(['password' => Hash::make($request->password)]);
		}

		return response()->json(['success' => 'User updated successfully!']);
	}

	// AJAX Delete
	public function destroy(User $user)
	{
		// Prevent admin from deleting themselves
		if (auth()->id() === $user->id) {
			return response()->json(['error' => 'You cannot delete yourself!'], 403);
		}

		$user->delete();
		return response()->json(['success' => 'User deleted successfully.']);
	}
}

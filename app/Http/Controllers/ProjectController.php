<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index()
    {
        $user = auth()->user();

        // Admin (role_id 1) sees all. Users see only theirs.
        if ($user->role_id == '1') {
            $projects = Project::with(['user'])->withCount('tasks')->get();
            $users = User::all(); // For the "Assign to" dropdown
        } else {
            $projects = Project::where('user_id', $user->id)->withCount('tasks')->get();
            $users = null;
        }

        return view('pages.projects', compact('projects', 'users'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date',
            'user_id'     => 'nullable|exists:users,id', // Add validation for optional user_id
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Logic: Use provided user_id if Admin, otherwise default to self
        $ownerId = (Auth::user()->role_id == '1' && $request->filled('user_id')) 
                    ? $request->user_id 
                    : Auth::id();

        $project = Project::create([
            'title'       => $request->title,
            'description' => $request->description,
            'deadline'    => $request->deadline,
            'user_id'     => $ownerId,
        ]);

        return response()->json([
            'success' => 'Project created successfully!',
            'project' => $project
        ]);
    }

    /**
     * Show the form for editing the specified project via AJAX.
     */
    public function edit(Project $project)
    {
        // Security check using role_id
        if (Auth::user()->role_id != '1' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id'          => $project->id,
            'title'       => $project->title,
            'description' => $project->description,
            'user_id'     => $project->user_id, // Return user_id so modal dropdown populates
            'deadline'    => $project->deadline instanceof \Carbon\Carbon
                ? $project->deadline->format('Y-m-d')
                : date('Y-m-d', strtotime($project->deadline))
        ]);
    }

    /**
     * Update the specified project in storage via AJAX.
     */
    public function update(Request $request, Project $project)
    {
        if (Auth::user()->role_id != '1' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date',
            'user_id'     => 'nullable|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = [
            'title'       => $request->title,
            'description' => $request->description,
            'deadline'    => $request->deadline,
        ];

        // Only allow changing the owner if the current user is an Admin
        if (Auth::user()->role_id == '1' && $request->filled('user_id')) {
            $data['user_id'] = $request->user_id;
        }

        $project->update($data);

        return response()->json([
            'success' => 'Project updated successfully!',
            'project' => $project
        ]);
    }

    /**
     * Remove the specified project from storage via AJAX.
     */
    public function destroy(Project $project)
    {
        if (Auth::user()->role_id != '1' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $project->delete();

        return response()->json(['success' => 'Project deleted successfully.']);
    }
}
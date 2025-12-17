<?php

namespace App\Http\Controllers;

use App\Models\Project;
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

        // Admins see all projects; Users see only theirs.
        // Eager load 'user' to avoid N+1 query issues in the blade table.
        if ($user->role?->name === 'Admin') {
            $projects = Project::with('user')->get();
        } else {
            $projects = Project::where('user_id', $user->id)->get();
        }

        return view('pages.projects', compact('projects'));
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
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project = Project::create([
            'title'       => $request->title,
            'description' => $request->description,
            'deadline'    => $request->deadline,
            'user_id'     => Auth::id(),
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
        // Admin can edit anything; Users can only edit their own.
        if (Auth::user()->role?->name !== 'Admin' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        return response()->json([
            'id'          => $project->id,
            'title'       => $project->title,
            'description' => $project->description,
            // Check if deadline is cast as a date/Carbon object in Model
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
        // Security: Check if the user is allowed to update this.
        if (Auth::user()->role?->name !== 'Admin' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validator = Validator::make($request->all(), [
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'deadline'    => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $project->update([
            'title'       => $request->title,
            'description' => $request->description,
            'deadline'    => $request->deadline,
        ]);

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
        // Security: Admin can delete any; Users can only delete theirs.
        if (Auth::user()->role?->name !== 'Admin' && $project->user_id !== Auth::id()) {
            return response()->json(['error' => 'You do not have permission to delete this project.'], 403);
        }

        $project->delete();

        return response()->json(['success' => 'Project deleted successfully.']);
    }
}

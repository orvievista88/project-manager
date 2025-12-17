<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;

class ProjectApiController extends Controller
{
    public function index()
    {
        // Now that the relationship is defined, this will work!
        $projects = auth()->user()->projects()->get();

        return ProjectResource::collection($projects);
    }
    
    public function show(Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return new ProjectResource($project->load('tasks'));
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Http\Resources\TaskResource;

class TaskApiController extends Controller
{
    public function index()
    {
        // Fetch all tasks with their parent project to avoid extra queries
        $tasks = Task::with('project')->get();
        
        return TaskResource::collection($tasks);
    }
}
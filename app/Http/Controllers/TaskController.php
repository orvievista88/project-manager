<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index()
    {
        // Eager load projects to satisfy Requirement 5 (Relationships)
        $tasks = Task::with('project')->get();
        $projects = Project::all();

        return view('pages.tasks', compact('tasks', 'projects'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title'      => 'required|string|max:255',
            'status'     => 'required|in:todo,in_progress,done',
            'progress'   => 'required|integer|min:0|max:100', // Added for the progress bar
            'due_date'   => 'required|date',
        ]);

        Task::create($validated);
        return response()->json(['success' => 'Task created successfully!']);
    }

    public function edit(Task $task)
    {
        // Ensure due_date is treated as a Carbon instance to format it for the HTML5 date input
        $task->formatted_due_date = \Carbon\Carbon::parse($task->due_date)->format('Y-m-d');

        return response()->json($task);
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'project_id' => 'required|exists:projects,id',
            'title'      => 'required|string|max:255',
            'status'     => 'required|in:todo,in_progress,done',
            'progress'   => 'required|integer|min:0|max:100', // Added for the progress bar
            'due_date'   => 'required|date',
        ]);

        $task->update($validated);

        return response()->json(['success' => 'Task updated successfully!']);
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return response()->json(['success' => 'Task deleted!']);
    }
}
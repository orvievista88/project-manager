<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Project;
use Illuminate\Http\Request;

class TaskController extends Controller
{
	public function index()
	{
		$user = auth()->user();
		$projects = Project::all();
		// Only fetch users list if current user is admin
		$users = ($user->role_id == '1') ? \App\Models\User::all() : null;

		if ($user->role_id == '1') {
			$tasks = Task::with(['project', 'user'])->get();
		} else {
			$tasks = Task::where('user_id', $user->id)->with('project')->get();
		}

		return view('pages.tasks', compact('tasks', 'projects', 'users'));
	}

	public function store(Request $request)
	{
		$validated = $request->validate([
			'project_id' => 'required|exists:projects,id',
			'title'      => 'required|string|max:255',
			'status'     => 'required|in:todo,in_progress,done',
			'progress'   => 'required|integer|min:0|max:100',
			'due_date'   => 'required|date',
			'user_id'    => 'nullable|exists:users,id', // Admin can send this
		]);

		// Logic: If admin didn't pick a user or if the user is not admin, default to self
		if (auth()->user()->role !== 'admin' || empty($validated['user_id'])) {
			$validated['user_id'] = auth()->id();
		}

		Task::create($validated);
		return response()->json(['success' => 'Task assigned successfully!']);
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
			'progress'   => 'required|integer|min:0|max:100', 
			'due_date'   => 'required|date',
			'user_id'    => 'nullable|exists:users,id',
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

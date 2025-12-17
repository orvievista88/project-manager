@extends('layouts.app')

@section('content')
<div class="container">
	<div class="d-flex justify-content-between align-items-center mb-4">
		<div>
			<h1 class="fw-bold">Tasks</h1>
			<p class="text-muted">Manage your project milestones and tracking.</p>
		</div>
		<button class="btn btn-primary px-4" id="createTaskBtn">
			<i class="fas fa-plus me-2"></i>Create New Task
		</button>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-body p-0">
			<table class="table table-hover align-middle mb-0" id="tasksTable">
				<thead class="bg-light">
					<tr>
						<th class="ps-4">Task Title</th>
						<th>Project</th>
						<th>Status</th>
						<th>Progress</th>
						<th>Due Date</th>
						<th class="text-end pe-4">Actions</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tasks as $task)
					<tr id="task-{{ $task->id }}">
						<td class="ps-4"><strong>{{ $task->title }}</strong></td>
						<td><span class="text-muted">{{ $task->project->title ?? 'N/A' }}</span></td>
						<td>
							@php
							$badgeClass = [
							'todo' => 'bg-secondary',
							'in_progress' => 'bg-warning text-dark',
							'done' => 'bg-success'
							][$task->status] ?? 'bg-light text-dark';

							$statusLabel = str_replace('_', ' ', ucfirst($task->status));
							@endphp
							<span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
						</td>
						<td style="min-width: 150px;">
							<div class="d-flex align-items-center">
								<div class="progress flex-grow-1" style="height: 8px;">
									<div class="progress-bar bg-info" role="progressbar"
										style="width: {{ $task->progress }}%"
										aria-valuenow="{{ $task->progress }}" aria-valuemin="0" aria-valuemax="100">
									</div>
								</div>
								<span class="ms-2 small fw-bold">{{ $task->progress }}%</span>
							</div>
						</td>
						<td>{{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}</td>
						<td class="text-end pe-4">
							<button class="btn btn-sm btn-outline-warning editTaskBtn" data-id="{{ $task->id }}">
								<i class="fas fa-edit"></i> Edit
							</button>
							<button class="btn btn-sm btn-outline-danger deleteTaskBtn" data-id="{{ $task->id }}">
								<i class="fas fa-trash"></i> Delete
							</button>
						</td>
					</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
</div>

<div class="modal fade" id="taskModal" tabindex="-1" aria-hidden="true">
	<div class="modal-dialog">
		<div class="modal-content">
			<form id="taskForm">
				@csrf
				<div class="modal-header">
					<h5 class="modal-title" id="taskModalLabel">Task Details</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				</div>
				<div class="modal-body">
					<input type="hidden" id="taskId">

					<div class="mb-3">
						<label class="form-label fw-bold">Project</label>
						<select class="form-select" name="project_id" id="project_id" required>
							<option value="" disabled selected>Select a Project</option>
							@foreach($projects as $project)
							<option value="{{ $project->id }}">{{ $project->title }}</option>
							@endforeach
						</select>
					</div>

					<div class="mb-3">
						<label class="form-label fw-bold">Task Title</label>
						<input type="text" class="form-control" name="title" id="title" placeholder="What needs to be done?" required>
					</div>

					<div class="row">
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold">Status</label>
							<select class="form-select" name="status" id="status" required>
								<option value="todo">Todo</option>
								<option value="in_progress">In Progress</option>
								<option value="done">Done</option>
							</select>
						</div>
						<div class="col-md-6 mb-3">
							<label class="form-label fw-bold">Progress (%)</label>
							<input type="number" class="form-control" name="progress" id="progress" min="0" max="100" value="0" required>
						</div>
					</div>

					<div class="mb-3">
						<label class="form-label fw-bold">Due Date</label>
						<input type="date" class="form-control" name="due_date" id="due_date" required>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary" id="saveBtn">Save Changes</button>
				</div>
			</form>
		</div>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// OPEN CREATE MODAL
	$('#createTaskBtn').click(function() {
		$('#taskForm')[0].reset();
		$('#taskId').val('');
		$('#taskModalLabel').text('Create New Task');
		$('#taskModal').modal('show');
	});

	// OPEN EDIT MODAL
	$(document).on('click', '.editTaskBtn', function() {
		let id = $(this).data('id');
		$.get(`/tasks/${id}/edit`, function(data) {
			$('#taskId').val(data.id);
			$('#project_id').val(data.project_id);
			$('#title').val(data.title);
			$('#status').val(data.status);
			$('#progress').val(data.progress);

			// Fixed: Use the formatted date from controller (YYYY-MM-DD)
			$('#due_date').val(data.formatted_due_date || data.due_date.split('T')[0]);

			$('#taskModalLabel').text('Edit Task');
			$('#taskModal').modal('show');
		});
	});

	// AUTO-SYNC STATUS WITH PROGRESS
	$('#progress').on('input', function() {
		let val = $(this).val();
		if (val >= 100) {
			$('#status').val('done');
		} else if (val > 0) {
			$('#status').val('in_progress');
		} else {
			$('#status').val('todo');
		}
	});

	// SAVE TASK (STORE OR UPDATE)
	$('#taskForm').submit(function(e) {
		e.preventDefault();
		let id = $('#taskId').val();
		let url = id ? `/tasks/${id}` : '/tasks';
		let method = id ? 'PUT' : 'POST';

		$.ajax({
			url: url,
			method: method,
			data: $(this).serialize(),
			success: function(response) {
				location.reload();
			},
			error: function(xhr) {
				alert("Error: " + xhr.responseJSON.message);
			}
		});
	});

	// DELETE TASK
	$(document).on('click', '.deleteTaskBtn', function() {
		if (confirm('Are you sure you want to delete this task?')) {
			let id = $(this).data('id');
			$.ajax({
				url: `/tasks/${id}`,
				method: 'DELETE',
				success: function() {
					$(`#task-${id}`).fadeOut(300, function() {
						$(this).remove();
					});
				}
			});
		}
	});
</script>
@endsection
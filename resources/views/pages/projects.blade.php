@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="fw-bold">Projects</h1>
            <p class="text-muted">Overview of all active workstreams.</p>
        </div>
        <button class="btn btn-primary px-4" id="createProjectBtn">
            <i class="fas fa-plus me-2"></i>Create New Project
        </button>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <table class="table table-hover align-middle mb-0" id="projectsTable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">Title</th>
                        <th>Owner</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr id="project-{{ $project->id }}">
                        <td class="ps-4"><strong>{{ $project->title }}</strong></td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="fas fa-user-circle me-1"></i>
                                {{ $project->user->name ?? 'System' }}
                            </span>
                        </td>
                        <td class="text-muted small">{{ Str::limit($project->description, 50) }}</td>
                        <td>
                            <span class="text-{{ \Carbon\Carbon::parse($project->deadline)->isPast() ? 'danger' : 'dark' }}">
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'No Deadline' }}
                            </span>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary editProjectBtn" data-id="{{ $project->id }}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger deleteProjectBtn" data-id="{{ $project->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalLabel">Project Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    @csrf
                    <input type="hidden" id="projectId" name="projectId">
                    
                    <div class="mb-3">
                        <label class="form-label fw-bold">Project Title</label>
                        <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    @if(auth()->user()->role_id == '1')
                    <div class="mb-3">
                        <label class="form-label fw-bold text-primary">Assign Owner (Admin Only)</label>
                        <select class="form-select border-primary" name="user_id" id="user_id">
                            <option value="">-- Assign to self --</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" required>
                    </div>
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // CREATE
    $('#createProjectBtn').click(function() {
        $('#projectForm')[0].reset();
        $('#projectId').val('');
        $('#projectModalLabel').text('Create New Project');
        $('#projectModal').modal('show');
    });

    // EDIT
    $(document).on('click', '.editProjectBtn', function() {
        let id = $(this).data('id');
        $.get(`/projects/${id}/edit`, function(data) {
            $('#projectId').val(data.id);
            $('#title').val(data.title);
            $('#description').val(data.description);
            $('#deadline').val(data.deadline);

            // Populate user dropdown if it exists (Admins)
            if ($('#user_id').length) {
                $('#user_id').val(data.user_id);
            }

            $('#projectModalLabel').text('Edit Project');
            $('#projectModal').modal('show');
        });
    });

    // SUBMIT (Save/Update)
    $('#projectForm').submit(function(e) {
        e.preventDefault();
        let id = $('#projectId').val();
        let url = id ? `/projects/${id}` : '/projects';
        let method = id ? 'PUT' : 'POST';

        $.ajax({
            url: url,
            method: method,
            data: $(this).serialize(),
            success: function() {
                location.reload();
            },
            error: function(xhr) {
                alert("Error: " + (xhr.responseJSON?.message || "Check your input"));
            }
        });
    });

    // DELETE
    $(document).on('click', '.deleteProjectBtn', function() {
        if (confirm('Delete this project and all its tasks?')) {
            let id = $(this).data('id');
            $.ajax({
                url: `/projects/${id}`,
                method: 'DELETE',
                success: function() {
                    $(`#project-${id}`).fadeOut();
                }
            });
        }
    });
</script>
@endsection
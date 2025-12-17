@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Projects</h1>
        <button class="btn btn-primary" id="createProjectBtn">Create New Project</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="projectsTable">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Deadline</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($projects as $project)
                    <tr id="project-{{ $project->id }}">
                        <td><strong>{{ $project->title }}</strong></td>
                        <td class="text-muted">{{ Str::limit($project->description, 50) }}</td>
                        <td>
                            {{-- Formatting date for display --}}
                            {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'No Deadline' }}
                        </td>
                        <td class="text-end px-4">
                            <button class="btn btn-sm btn-outline-warning editProjectBtn" data-id="{{ $project->id }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger deleteProjectBtn" data-id="{{ $project->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalLabel">Project Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="projectForm">
                    @csrf
                    <div class="mb-3">
                        <label for="title" class="form-label">Project Title</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter project title" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="Describe the project..." required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="deadline" class="form-label">Deadline</label>
                        <input type="date" class="form-control" id="deadline" name="deadline" required>
                    </div>
                    <input type="hidden" id="projectId">
                    
                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save Project</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // 1. Global AJAX Setup for CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // 2. Open modal for CREATE
    $('#createProjectBtn').click(function() {
        $('#projectForm')[0].reset();
        $('#projectId').val('');
        $('#projectModalLabel').text('Create New Project');
        $('#projectModal').modal('show');
    });

    // 3. Open modal for EDIT
    $(document).on('click', '.editProjectBtn', function() {
        let projectId = $(this).data('id');
        $('#saveBtn').text('Updating...').prop('disabled', true);

        $.get(`/projects/${projectId}/edit`, function(data) {
            $('#title').val(data.title);
            $('#description').val(data.description);
            
            // Populate date (ensure format is YYYY-MM-DD)
            if(data.deadline) {
                let dateOnly = data.deadline.split(' ')[0];
                $('#deadline').val(dateOnly);
            }

            $('#projectId').val(data.id);
            $('#projectModalLabel').text('Edit Project');
            $('#saveBtn').text('Save Changes').prop('disabled', false);
            $('#projectModal').modal('show');
        }).fail(function() {
            alert("Could not fetch project data.");
            $('#saveBtn').prop('disabled', false);
        });
    });

    // 4. Submit Create/Update via AJAX
    $('#projectForm').submit(function(event) {
        event.preventDefault();
        
        let projectId = $('#projectId').val();
        let url = projectId ? `/projects/${projectId}` : '/projects';
        let method = projectId ? 'PUT' : 'POST';
        let formData = $(this).serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#projectModal').modal('hide');
                // Use a toast or location reload
                location.reload(); 
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    alert(Object.values(errors).flat().join('\n'));
                } else {
                    alert('An error occurred. Please try again.');
                }
            }
        });
    });

    // 5. Soft DELETE project
    $(document).on('click', '.deleteProjectBtn', function() {
        let projectId = $(this).data('id');

        if (confirm('Are you sure you want to delete this project?')) {
            $.ajax({
                url: `/projects/${projectId}`,
                method: 'DELETE',
                success: function(response) {
                    $(`#project-${projectId}`).fadeOut(300, function() {
                        $(this).remove();
                    });
                },
                error: function(xhr) {
                    alert('Error: ' + xhr.statusText);
                }
            });
        }
    });
</script>
@endsection
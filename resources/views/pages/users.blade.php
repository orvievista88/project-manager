@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Users</h1>
        <button class="btn btn-primary" id="createUserBtn">Add New User</button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-hover mb-0" id="usersTable">
                <thead class="table-light">
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="text-end px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                    <tr id="user-{{ $user->id }}">
                        <td><strong>{{ $user->name }}</strong></td>
                        <td class="text-muted">{{ $user->email }}</td>
                        <td>
                            {{-- Accessing the role name via relationship --}}
                            <span class="badge bg-info text-dark">
                                {{ $user->role->name ?? 'No Role Assigned' }}
                            </span>
                        </td>
                        <td class="text-end px-4">
                            <button class="btn btn-sm btn-outline-warning editUserBtn" data-id="{{ $user->id }}">Edit</button>
                            <button class="btn btn-sm btn-outline-danger deleteUserBtn" data-id="{{ $user->id }}">Delete</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">User Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    @csrf
                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                        <small class="text-muted" id="pwHint"></small>
                    </div>

                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                    </div>

                    <div class="mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="" selected disabled>-- Select a Role --</option>
                            @isset($roles)
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            @endisset
                        </select>
                    </div>

                    <input type="hidden" id="userId">

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="saveBtn">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
    $.ajaxSetup({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
    });

    // OPEN CREATE MODAL
    $('#createUserBtn').click(function() {
        $('#userForm')[0].reset();
        $('#userId').val('');
        $('#userModalLabel').text('Add New User');
        $('#pwHint').text('Password is required for new users.');
        $('#password').attr('required', true);
        $('#password_confirmation').attr('required', true);
        $('#userModal').modal('show');
    });

    // OPEN EDIT MODAL
    $(document).on('click', '.editUserBtn', function() {
        let userId = $(this).data('id');
        $('#saveBtn').text('Loading...').prop('disabled', true);

        $.get(`/users/${userId}/edit`, function(data) {
            $('#name').val(data.name);
            $('#email').val(data.email);
            $('#role_id').val(data.role_id); 
            $('#userId').val(data.id);
            
            // Clean UI for edit
            $('#password').val('').attr('required', false);
            $('#password_confirmation').val('').attr('required', false);
            $('#pwHint').text('Leave blank to keep current password.');
            
            $('#userModalLabel').text('Edit User');
            $('#saveBtn').text('Save Changes').prop('disabled', false);
            $('#userModal').modal('show');
        }).fail(function() {
            alert("Error: Could not retrieve user data.");
            $('#saveBtn').text('Save Changes').prop('disabled', false);
        });
    });

    // SUBMIT AJAX
    $('#userForm').submit(function(event) {
        event.preventDefault();

        let userId = $('#userId').val();
        let url = userId ? `/users/${userId}` : '/users';
        let method = userId ? 'PUT' : 'POST';
        let formData = $(this).serialize();

        $.ajax({
            url: url,
            method: method,
            data: formData,
            success: function(response) {
                $('#userModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                let errors = xhr.responseJSON?.errors;
                if (errors) {
                    alert(Object.values(errors).flat().join('\n'));
                } else {
                    alert('An error occurred. Check if the Role ID exists in your database.');
                }
            }
        });
    });

    // DELETE AJAX
    $(document).on('click', '.deleteUserBtn', function() {
        let userId = $(this).data('id');
        if (confirm('Are you sure you want to remove this user?')) {
            $.ajax({
                url: `/users/${userId}`,
                method: 'DELETE',
                success: function() {
                    $(`#user-${userId}`).fadeOut(300, function() { $(this).remove(); });
                },
                error: function(xhr) { alert('Error: ' + xhr.statusText); }
            });
        }
    });
</script>
@endsection
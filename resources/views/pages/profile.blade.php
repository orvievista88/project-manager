@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="fw-bold">My Profile</h1>
    <p class="text-muted">Update your account settings here.</p>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">New Password (Leave blank to stay the same)</label>
                    <input type="password" name="password" class="form-control">
                </div>

                <button type="submit" class="btn btn-primary">Update Profile</button>
            </form>
        </div>
    </div>
</div>
@endsection
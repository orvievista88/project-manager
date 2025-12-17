<!-- resources/views/auth/login.blade.php -->

@extends('layouts.app')

@section('content')
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card">
            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <div class="card-header text-center">
                <h4>Login</h4>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Email Address -->
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required>
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember_me" name="remember">
                        <label class="form-check-label" for="remember_me">Remember Me</label>
                    </div>

                    <div class="text-center">
                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                        <a class="text-decoration-none" href="{{ route('password.request') }}">Forgot your password?</a>
                        @endif

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary">Log in</button>
                    </div>
                </form>
                <hr>
                <div class="text-center">
                    <p class="mb-0">Don't have an account?</p>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-sm mt-2">Create New Account</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
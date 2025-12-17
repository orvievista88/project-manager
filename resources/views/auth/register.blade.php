<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Register - Project Manager</title>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
	<div class="container">
		<div class="row justify-content-center mt-5">
			<div class="col-md-5">
				<div class="card shadow">
					<div class="card-header bg-primary text-white text-center">
						<h4>Create Account</h4>
					</div>
					<div class="card-body p-4">
						@if(session('success'))
						<div class="alert alert-success">{{ session('success') }}</div>
						@endif

						<form method="POST" action="{{ route('register.post') }}">
							@csrf

							<div class="mb-3">
								<label class="form-label">Full Name</label>
								<input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
								@error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>

							<div class="mb-3">
								<label class="form-label">Email Address</label>
								<input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
								@error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>

							<div class="mb-3">
								<label class="form-label">Password</label>
								<input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
								@error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
							</div>

							<div class="mb-3">
								<label class="form-label">Confirm Password</label>
								<input type="password" name="password_confirmation" class="form-control" required>
							</div>

							<div class="d-grid">
								<button type="submit" class="btn btn-primary">Register</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</body>

</html>
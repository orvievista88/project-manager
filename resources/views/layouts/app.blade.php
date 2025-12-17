<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Project Management - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            background-color: #f4f7f6;
        }

        /* Top bar styling */
        .topbar {
            background-color: #343a40;
            color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1030;
            /* Higher than sidebar */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Sidebar styles */
        .sidebar {
            width: 250px;
            height: 100vh;
            background-color: #ffffff;
            position: fixed;
            top: 60px;
            /* Start below topbar */
            left: 0;
            z-index: 1020;
            border-right: 1px solid #dee2e6;
            transition: all 0.3s;
        }

        .sidebar a {
            color: #333;
            padding: 12px 20px;
            text-decoration: none;
            display: block;
            border-bottom: 1px solid #f8f9fa;
        }

        .sidebar a:hover {
            background-color: #e9ecef;
            color: #0d6efd;
        }

        /* Main content styling */
        .main-content {
            margin-left: 250px;
            margin-top: 60px;
            /* Offset for fixed topbar */
            padding: 30px;
            flex-grow: 1;
            min-height: calc(100vh - 60px);
        }

        /* Mobile Adjustments */
        @media (max-width: 767px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.show {
                left: 0;
            }

            .main-content {
                margin-left: 0;
            }

            #toggleSidebar {
                display: block !important;
            }
        }

        #toggleSidebar {
            display: none;
        }
    </style>
</head>

<body>
    @auth
    <nav class="topbar">
        <div class="container-fluid d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <button class="btn btn-outline-light me-3 d-md-none" id="toggleSidebar">☰</button>
                <span class="h4 mb-0">Project Management</span>
            </div>

            <div class="user-info d-flex align-items-center">
                <span class="me-3">Welcome, <strong>{{ Auth::user()->name }}</strong> ({{ Auth::user()->role->name ?? 'User' }})</span>
            </div>
        </div>
    </nav>

    <aside class="sidebar" id="sidebar">
        <div class="py-3">
            <h5 class="px-4 text-muted text-uppercase small">Menu</h5>
            <nav class="nav flex-column">
                <a href="{{ route('dashboard') }}">Home</a>
                <a href="{{ route('profile') }}">Profile</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('projects.index') }}">Projects</a>
                <a href="{{ route('tasks.index') }}">Tasks</a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2 px-3">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm w-100">Logout</button>
                </form>
            </nav>
        </div>
    </aside>

    <main class="main-content">
        <div class="container-fluid">
            @yield('content')
        </div>
    </main>

    @else
    <div class="container mt-5">
        @yield('content')
    </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
    <script>
        // Toggle sidebar logic
        const toggleBtn = document.getElementById('toggleSidebar');
        const sidebar = document.getElementById('sidebar');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                sidebar.classList.toggle('show');
            });
        }

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(e) {
            if (window.innerWidth < 768 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && e.target !== toggleBtn) {
                    sidebar.classList.remove('show');
                }
            }
        });
    </script>
</body>

</html>
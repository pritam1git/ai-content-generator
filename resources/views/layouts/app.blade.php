<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'AI Content Generator')</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar {
            min-height: 100vh;
            width: 250px;
            background: #1e1e2d;
            position: fixed;
            top: 0;
            left: 0;
            transition: all 0.3s;
            z-index: 1000;
        }

        .sidebar .brand {
            padding: 1.2rem 1.5rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar .brand i {
            color: #6f6cff;
        }

        .sidebar .nav-link {
            color: #b3b3c6;
            padding: 0.75rem 1.5rem;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.6rem;
            border-radius: 0;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(111, 108, 255, 0.15);
            color: #fff;
            border-left: 3px solid #6f6cff;
        }

        /* Main content */
        .main-content {
            margin-left: 250px;
            transition: all 0.3s;
        }

        .topbar {
            background: #fff;
            padding: 0.8rem 1.5rem;
            box-shadow: 0 1px 4px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .content-wrapper {
            padding: 1.8rem;
        }

        /* Mobile responsive */
        @media (max-width: 991px) {
            .sidebar {
                left: -250px;
            }
            .sidebar.show {
                left: 0;
            }
            .main-content {
                margin-left: 0;
            }
        }

        .card {
            border: none;
            border-radius: 12px;
        }

        .btn-primary {
            background-color: #6f6cff;
            border-color: #6f6cff;
        }

        .btn-primary:hover {
            background-color: #5b58e0;
            border-color: #5b58e0;
        }
    </style>

    @stack('styles')
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div class="brand">
            <i class="bi bi-stars"></i> AI Content Gen
        </div>
        <nav class="nav flex-column mt-2">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-grid-1x2-fill"></i> Dashboard
            </a>
            <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <i class="bi bi-chat-dots-fill"></i> AI Chat Assistant
            </a>
            <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text-fill"></i> Blog Generator
            </a>
            <a href="{{ route('product.index') }}" class="nav-link {{ request()->routeIs('product.*') ? 'active' : '' }}">
                <i class="bi bi-bag-check-fill"></i> Product Description
            </a>
            <a href="{{ route('history.index') }}" class="nav-link {{ request()->routeIs('history.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Prompt History
            </a>
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-fill"></i> Profile
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">

        <!-- Topbar -->
        <div class="topbar d-flex align-items-center justify-content-between">
            <button class="btn btn-sm btn-light d-lg-none" id="sidebarToggle">
                <i class="bi bi-list fs-5"></i>
            </button>

            <h5 class="mb-0 d-none d-md-block">@yield('page-title', 'Dashboard')</h5>

            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span>{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i>Profile Settings</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Page Content -->
        <div class="content-wrapper">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });
    </script>

    @stack('scripts')
</body>
</html>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <title>Rector Cup - @yield('title')</title>
    <style>
        .sidebar {
            min-height: 100vh;
            background: #f8f9fa;
            border-right: 1px solid #dee2e6;
        }

        .nav-link.active {
            font-weight: bold;
            background-color: #007bff20;
        }

        .live-indicator {
            width: 10px;
            height: 10px;
            background: red;
            border-radius: 50%;
            display: inline-block;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0% {
                opacity: 1;
            }

            50% {
                opacity: 0;
            }

            100% {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row bg-primary py-2 shadow-sm">
            <div class="col d-flex justify-content-between align-items-center">
                <h4 class="text-white mb-0 ml-3 font-weight-bold">RECTOR CUP</h4>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle border-white" type="button" data-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> {{ Auth::user()->name ?? "Guest" }}
                    </button>
                    <div class="dropdown-menu dropdown-menu-right">
                        @auth
                            <a class="dropdown-item" href="{{ route('admin.index') }}">Admin Dashboard</a>
                            <div class="dropdown-divider"></div>
                            <form action="{{ route('logout') }}" method="POST">@csrf <button
                                    class="dropdown-item text-danger">Logout</button></form>
                        @else
                            <a class="dropdown-item" href="/login">Login Panitia</a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-2 px-0 sidebar d-none d-lg-block">
                <div class="nav flex-column nav-pills p-3">
                    <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="/"><i
                            class="bi bi-house-door mr-2"></i> Dashboard</a>
                    @auth
                        <a class="nav-link {{ request()->is('admin*') ? 'active' : '' }}" href="/admin"><i
                                class="bi bi-gear mr-2"></i> Kelola Jadwal</a>
                    @endauth
                </div>
            </div>

            <div class="col-lg-10 py-4">
                @yield('content')
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
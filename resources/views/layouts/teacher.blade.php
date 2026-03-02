<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal Docente - {{ config('app.name') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #4f46e5;
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: #f1f5f9;
        }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(180deg, #4f46e5 0%, #3730a3 100%);
            color: white;
            z-index: 1000;
            overflow-y: auto;
        }
        .sidebar-header {
            padding: 1.5rem;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-nav {
            padding: 1rem 0;
        }
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 0.75rem 1.5rem;
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            transition: all 0.2s;
        }
        .sidebar-nav a:hover, .sidebar-nav a.active {
            background: rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar-nav a i {
            margin-right: 0.75rem;
            font-size: 1.25rem;
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 1.5rem;
            min-height: 100vh;
        }
        .topbar {
            background: white;
            padding: 1rem 1.5rem;
            margin: -1.5rem -1.5rem 1.5rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-menu {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
    </style>
    @livewireStyles
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <h5 class="mb-0"><i class="ri ri-graduation-cap-line me-2"></i> Portal Docente</h5>
            <small class="opacity-75">{{ Auth::user()->name ?? 'Profesor' }}</small>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('teacher.dashboard') }}" class="{{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
                <i class="ri ri-dashboard-line"></i> Dashboard
            </a>
            <a href="{{ route('teacher.my-subjects') }}" class="{{ request()->routeIs('teacher.my-subjects') ? 'active' : '' }}">
                <i class="ri ri-book-open-line"></i> Mis Materias
            </a>
            <a href="{{ route('teacher.my-evaluations') }}" class="{{ request()->routeIs('teacher.my-evaluations') ? 'active' : '' }}">
                <i class="ri ri-file-list-3-line"></i> Mis Evaluaciones
            </a>
            <a href="{{ route('teacher.grade-entry') }}" class="{{ request()->routeIs('teacher.grade-entry') ? 'active' : '' }}">
                <i class="ri ri-edit-box-line"></i> Registrar Notas
            </a>
            <a href="{{ route('teacher.attendance-entry') }}" class="{{ request()->routeIs('teacher.attendance-entry') ? 'active' : '' }}">
                <i class="ri ri-calendar-check-line"></i> Registrar Asistencia
            </a>
            <hr class="my-3 opacity-25">
            <a href="{{ route('admin.dashboard') }}">
                <i class="ri ri-arrow-left-line"></i> Volver al Sistema
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
                @csrf
                <a href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                    <i class="ri ri-logout-box-line"></i> Cerrar Sesión
                </a>
            </form>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="topbar">
            <div>
                <h5 class="mb-0">{{ $title ?? 'Portal Docente' }}</h5>
            </div>
            <div class="user-menu">
                <span class="text-muted">{{ now()->format('d/m/Y') }}</span>
                <div class="avatar">
                    {{ substr(Auth::user()->name ?? 'P', 0, 1) }}
                </div>
            </div>
        </div>

        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>

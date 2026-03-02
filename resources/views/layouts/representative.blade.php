<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Portal de Representantes - {{ config('app.name') }}</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #059669;
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
            background: linear-gradient(180deg, #059669 0%, #047857 100%);
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
        .student-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
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
            <h5 class="mb-0"><i class="ri ri-parent-line me-2"></i> Portal Representante</h5>
            <small class="opacity-75">{{ Auth::user()->name ?? 'Representante' }}</small>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('representative.dashboard') }}" class="{{ request()->routeIs('representative.dashboard') ? 'active' : '' }}">
                <i class="ri ri-dashboard-line"></i> Inicio
            </a>
            <a href="{{ route('representative.grades') }}" class="{{ request()->routeIs('representative.grades') ? 'active' : '' }}">
                <i class="ri ri-file-list-3-line"></i> Calificaciones
            </a>
            <a href="{{ route('representative.boletines') }}" class="{{ request()->routeIs('representative.boletines') ? 'active' : '' }}">
                <i class="ri ri-file-text-line"></i> Boletines
            </a>
            <a href="{{ route('representative.attendance') }}" class="{{ request()->routeIs('representative.attendance') ? 'active' : '' }}">
                <i class="ri ri-calendar-check-line"></i> Asistencia
            </a>
            <a href="{{ route('representative.payments') }}" class="{{ request()->routeIs('representative.payments') ? 'active' : '' }}">
                <i class="ri ri-money-dollar-circle-line"></i> Pagos
            </a>
            <hr class="my-3 opacity-25">
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
                <h5 class="mb-0">{{ $title ?? 'Portal de Representantes' }}</h5>
            </div>
            <div class="user-menu d-flex align-items-center gap-3">
                <span class="text-muted">{{ now()->format('d/m/Y') }}</span>
                <div class="avatar">
                    {{ substr(Auth::user()->name ?? 'R', 0, 1) }}
                </div>
            </div>
        </div>

        {{ $slot }}
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @livewireScripts
</body>
</html>

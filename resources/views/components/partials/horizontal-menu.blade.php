<div class="app-brand demo me-4">
  <a href="{{ url('/') }}" class="app-brand-link">
    <span class="app-brand-logo demo">
      <img src="{{ asset('logo/logo.png') }}" alt="logo" style="height: 70px;" />
    <span class="app-brand-text demo menu-text fw-semibold ms-2">{{ 'MIJVE' }}</span>
  </a>
</div>

<ul class="menu-inner py-1">
  <!-- Dashboard -->
  <li class="menu-item {{ request()->routeIs('admin/dashboard') || request()->routeIs('superadmin/dashboard') ? 'active' : '' }}">
    <a href="{{ url('/') }}" class="menu-link">
      <i class="menu-icon tf-icons ri ri-home-4-line"></i>
      <div>Dashboard</div>
    </a>
  </li>
      @canany(['access conceptos pago', 'access cajas'])
    <!-- Pagos y Finanzas -->
    <li class="menu-item {{ request()->routeIs('admin.pagos.*') || request()->routeIs('admin.conceptos-pago.*') || request()->routeIs('admin.cajas.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ri ri-money-dollar-circle-line"></i>
        <div>Pagos y Finanzas</div>
      </a>
      <ul class="menu-sub">
       
     
        @can('access conceptos pago')
        <li class="menu-item {{ request()->routeIs('admin.conceptos-pago.index') ? 'active' : '' }}">
          <a href="{{ route('admin.conceptos-pago.index') }}" class="menu-link">
            <div>Conceptos de Pago</div>
          </a>
        </li>
        @endcan
        @can('access cajas')
        <li class="menu-item {{ request()->routeIs('admin.cajas.index') ? 'active' : '' }}">
          <a href="{{ route('admin.cajas.index') }}" class="menu-link">
            <div>Caja Chica</div>
          </a>
        </li>
        @endcan
      </ul>
    </li>
    @endcan

   


    @canany(['access empresas', 'access sucursales', 'access school periods', 'access niveles educativos', 'access turnos'])
    <!-- Configuración Institucional -->
    <li class="menu-item {{ request()->routeIs('admin.empresas.*') || request()->routeIs('admin.sucursales.*') || request()->routeIs('admin.school-periods.*') || request()->routeIs('admin.niveles-educativos.*') || request()->routeIs('admin.turnos.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ri ri-building-4-line"></i>
        <div>Configuración</div>
      </a>
      <ul class="menu-sub">
        @can('access empresas')
        <li class="menu-item {{ request()->routeIs('admin.empresas.index') ? 'active' : '' }}">
          <a href="{{ route('admin.empresas.index') }}" class="menu-link">
            <div>Empresas</div>
          </a>
        </li>
        @endcan
        @can('access paises')
        <li class="menu-item {{ request()->routeIs('admin.paises.index') ? 'active' : '' }}">
          <a href="{{ route('admin.paises.index') }}" class="menu-link">
            <div>Países</div>
          </a>
        </li>
        @endcan
        @can('access sucursales')
        <li class="menu-item {{ request()->routeIs('admin.sucursales.index') ? 'active' : '' }}">
          <a href="{{ route('admin.sucursales.index') }}" class="menu-link">
            <div>Sucursales</div>
          </a>
        </li>
        @endcan
       
      </ul>
    </li>
    @endcan

    @can('access series')
    <!-- Series de Documentos -->
    <li class="menu-item {{ request()->routeIs('admin.series.*') ? 'active' : '' }}">
      <a href="{{ route('admin.series.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-file-list-3-line"></i>
        <div>Series</div>
      </a>
    </li>
    @endcan

    @can('view exchange-rates')
    <!-- Tasas de Cambio -->
    <li class="menu-item {{ request()->routeIs('admin.exchange-rates') ? 'active' : '' }}">
      <a href="{{ route('admin.exchange-rates') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-exchange-dollar-line"></i>
        <div>Tasas BCV</div>
      </a>
    </li>
    @endcan

    @can('access actividades')
    <!-- Actividades -->
    <li class="menu-item {{ request()->routeIs('admin.actividades.*') ? 'active' : '' }}">
      <a href="{{ route('admin.actividades.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-calendar-todo-line"></i>
        <div>Actividades</div>
      </a>
    </li>
    @endcan

    @can('access participantes')
    <!-- Participantes -->
    <li class="menu-item {{ request()->routeIs('admin.participantes.*') ? 'active' : '' }}">
      <a href="{{ route('admin.participantes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-group-line"></i>
        <div>Participantes</div>
      </a>
    </li>
    @endcan

    @can('access metodos_pago')
    <!-- Métodos de Pago -->
    <li class="menu-item {{ request()->routeIs('admin.metodos-pago.*') ? 'active' : '' }}">
      <a href="{{ route('admin.metodos-pago.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-bank-card-line"></i>
        <div>Métodos de Pago</div>
      </a>
    </li>
    @endcan

    @can('access pagos')
    <!-- Pagos -->
    <li class="menu-item {{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}">
      <a href="{{ route('admin.pagos.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-money-dollar-circle-line"></i>
        <div>Pagos</div>
      </a>
    </li>
    @endcan

    <!-- Personalización de Plantilla -->
    <li class="menu-item {{ request()->routeIs('admin.template-customization') ? 'active' : '' }}">
      <a href="{{ route('admin.template-customization') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-palette-line"></i>
        <div>Personalización</div>
      </a>
    </li>

       <!-- WhatsApp -->
    @can('access whatsapp')
        <li class="menu-item {{ request()->routeIs('admin.whatsapp.index') ? 'active' : '' }}">
          <a href="{{ route('admin.whatsapp.index') }}" class="menu-link">
               <i class="menu-icon tf-icons ri ri-whatsapp-line"></i>
            <div>Whatsapp</div>
          </a>
        </li>
    @endcan


   @canany(['access users', 'access roles', 'access permissions'])
    <!-- Usuarios y Permisos -->
    <li class="menu-item {{ request()->routeIs('admin.users.*') || request()->routeIs('admin.roles.*') || request()->routeIs('admin.permissions.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ri ri-group-line"></i>
        <div>Administración</div>
      </a>
      <ul class="menu-sub">
        @can('access users')
        <li class="menu-item {{ request()->routeIs('admin.users.index') ? 'active' : '' }}">
          <a href="{{ route('admin.users.index') }}" class="menu-link">
            <div>Usuarios</div>
          </a>
        </li>
        @endcan
        @can('access roles')
        <li class="menu-item {{ request()->routeIs('admin.roles.index') ? 'active' : '' }}">
          <a href="{{ route('admin.roles.index') }}" class="menu-link">
            <div>Roles</div>
          </a>
        </li>
        @endcan
        @can('access permissions')
        <li class="menu-item {{ request()->routeIs('admin.permissions.index') ? 'active' : '' }}">
          <a href="{{ route('admin.permissions.index') }}" class="menu-link">
            <div>Permisos</div>
          </a>
        </li>
        @endcan
      </ul>
    </li>
    @endcan

    @canany(['view active sessions', 'access activity log', 'access monitoreo'])
    <!-- Sistema y Monitoreo -->
    <li class="menu-item {{ request()->routeIs('admin.active-sessions*') || request()->is('admin/activity-log*') || request()->routeIs('admin.monitoreo.*') ? 'active open' : '' }}">
      <a href="javascript:void(0);" class="menu-link menu-toggle">
        <i class="menu-icon tf-icons ri ri-line-chart-line"></i>
        <div>Monitoreo</div>
      </a>
      <ul class="menu-sub">
        @can('view active sessions')
        <li class="menu-item {{ request()->routeIs('admin.active-sessions*') ? 'active' : '' }}">
          <a href="{{ route('admin.active-sessions.index') }}" class="menu-link">
            <div>Sesiones</div>
          </a>
        </li>
        @endcan
        @can('access activity log')
        <li class="menu-item {{ request()->is('admin/activity-log*') ? 'active' : '' }}">
          <a href="{{ route('admin.activity-log') }}" class="menu-link">
            <div>Actividad</div>
          </a>
        </li>
        @endcan
        @can('access database export')
        <li class="menu-item {{ request()->routeIs('admin.database-export') ? 'active' : '' }}">
          <a href="{{ route('admin.database-export') }}" class="menu-link">
            <div>Exportar Base de Datos</div>
          </a>
        </li>
        @endcan

      </ul>
    </li>
    @endcan
</ul>
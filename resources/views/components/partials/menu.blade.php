<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
  <div class="app-brand demo">
    <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo">
         <img src="{{ asset('logo/logo.png') }}" alt="logo" style="height: 60px;" />
      <span class="app-brand-text demo menu-text fw-semibold ms-2">{{'MIJVE' }}</span>
    </a>

    <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
        <path
          d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
          fill-opacity="0.9" />
        <path
          d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
          fill-opacity="0.4" />
      </svg>
    </a>
  </div>

  <div class="menu-inner-shadow"></div>

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
        <div data-i18n="Actividades">Actividades</div>
      </a>
    </li>
    @endcan

    @can('access participantes')
    <!-- Participantes -->
    <li class="menu-item {{ request()->routeIs('admin.participantes.*') ? 'active' : '' }}">
      <a href="{{ route('admin.participantes.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-group-line"></i>
        <div data-i18n="Participantes">Participantes</div>
      </a>
    </li>
    @endcan

    @can('access metodos_pago')
    <!-- Métodos de Pago -->
    <li class="menu-item {{ request()->routeIs('admin.metodos-pago.*') ? 'active' : '' }}">
      <a href="{{ route('admin.metodos-pago.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-bank-card-line"></i>
        <div data-i18n="Métodos de Pago">Métodos de Pago</div>
      </a>
    </li>
    @endcan

    @can('access extensiones')
    <!-- Extensiones -->
    <li class="menu-item {{ request()->routeIs('admin.extensiones.*') ? 'active' : '' }}">
      <a href="{{ route('admin.extensiones.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-building-2-line"></i>
        <div data-i18n="Extensiones">Extensiones</div>
      </a>
    </li>
    @endcan

    @can('access pagos')
    <!-- Pagos -->
    <li class="menu-item {{ request()->routeIs('admin.pagos.*') ? 'active' : '' }}">
      <a href="{{ route('admin.pagos.index') }}" class="menu-link">
        <i class="menu-icon tf-icons ri ri-money-dollar-circle-line"></i>
        <div data-i18n="Pagos">Pagos</div>
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
        <li class="menu-item {{ request()->routeIs('admin.whatsapp.connection') ? 'active' : '' }}">
          <a href="{{ route('admin.whatsapp.connection') }}" class="menu-link">
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
</aside>
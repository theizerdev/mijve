@extends('components.layouts.admin')

@section('content')
<div class="row">
  <div class="col-lg-12 mb-4 order-0">
    <div class="card">
      <div class="d-flex align-items-end row">
        <div class="col-sm-7">
          <div class="card-body">
            <h5 class="card-title text-primary">Bienvenido al sistema {{ Auth::user()->name }}! 🎉</h5>
            <p class="mb-4">
              Sistema de gestión escolar para el control de estudiantes, personal y recursos.
            </p>
          </div>
        </div>
        <div class="col-sm-5 text-center text-sm-left">
          <div class="card-body pb-0 px-0 px-md-4">
            <img
              src="{{ asset('materialize/assets/img/illustrations/man-with-laptop-light.png') }}"
              height="140"
              alt="View Badge User"
              data-app-dark-img="illustrations/man-with-laptop-dark.png"
              data-app-light-img="illustrations/man-with-laptop-light.png">
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Statistics Cards -->
  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('materialize/assets/img/icons/unicons/chart-success.png') }}" alt="chart success" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">Estudiantes</span>
        <h3 class="card-title mb-2">1,234</h3>
        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +12.5%</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('materialize/assets/img/icons/unicons/wallet-info.png') }}" alt="Credit Card" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">Personal</span>
        <h3 class="card-title mb-2">56</h3>
        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +8.2%</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('materialize/assets/img/icons/unicons/paypal.png') }}" alt="Paypal" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">Cursos</span>
        <h3 class="card-title mb-2">24</h3>
        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +5.3%</small>
      </div>
    </div>
  </div>

  <div class="col-lg-3 col-md-6 col-sm-6 mb-4">
    <div class="card">
      <div class="card-body">
        <div class="card-title d-flex align-items-start justify-content-between">
          <div class="avatar flex-shrink-0">
            <img src="{{ asset('materialize/assets/img/icons/unicons/cc-primary.png') }}" alt="Credit Card" class="rounded">
          </div>
        </div>
        <span class="fw-semibold d-block mb-1">Asistencia Hoy</span>
        <h3 class="card-title mb-2">87%</h3>
        <small class="text-success fw-medium"><i class="bx bx-up-arrow-alt"></i> +2.1%</small>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <!-- Order Statistics -->
  <div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between pb-0">
        <div class="card-title mb-0">
          <h5 class="m-0 me-2">Estadísticas de Registro</h5>
          <small class="text-muted">42.82k Total de Registros</small>
        </div>
      </div>
      <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex flex-column align-items-center gap-1">
            <h2 class="mb-2">8,258</h2>
            <span>Total de Registros</span>
          </div>
        </div>
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-primary"
                ><i class="bx bx-user"></i
              ></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Estudiantes</h6>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">8,258</small>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-success"><i class="bx bx-user-check"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Personal</h6>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">1,234</small>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <span class="avatar-initial rounded bg-label-info"><i class="bx bx-user-voice"></i></span>
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <h6 class="mb-0">Visitantes</h6>
              </div>
              <div class="user-progress">
                <small class="fw-semibold">215</small>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Expense Overview -->
  <div class="col-md-6 col-lg-4 order-1 mb-4">
    <div class="card h-100">
      <div class="card-header">
        <ul class="nav nav-pills" role="tablist">
          <li class="nav-item">
            <button
              type="button"
              class="nav-link active"
              role="tab"
              data-bs-toggle="tab"
              data-bs-target="#navs-tabs-line-card-income"
              aria-controls="navs-tabs-line-card-income"
              aria-selected="true"
            >
              Ingresos
            </button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Gastos</button>
          </li>
          <li class="nav-item">
            <button type="button" class="nav-link" role="tab">Balance</button>
          </li>
        </ul>
      </div>
      <div class="card-body px-0">
        <div class="tab-content p-0">
          <div class="tab-pane fade show active" id="navs-tabs-line-card-income" role="tabpanel">
            <div class="d-flex p-4 pt-3">
              <div class="avatar flex-shrink-0 me-3">
                <img src="{{ asset('materialize/assets/img/icons/unicons/wallet.png') }}" alt="User">
              </div>
              <div>
                <small class="text-muted d-block">Total de Ingresos</small>
                <div class="d-flex align-items-center">
                  <h6 class="mb-0 me-1">$459.10</h6>
                </div>
              </div>
            </div>
            <div id="incomeChart"></div>
            <div class="d-flex justify-content-center pt-4 gap-2">
              <div class="flex-shrink-0">
                <div id="expensesOfWeek"></div>
              </div>
              <div>
                <p class="mb-n1 mt-1">Gastos esta semana</p>
                <small class="text-muted">$390</small>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Transactions -->
  <div class="col-md-6 col-lg-4 order-2 mb-4">
    <div class="card h-100">
      <div class="card-header d-flex align-items-center justify-content-between">
        <h5 class="card-title m-0 me-2">Últimos Registros</h5>
        <div class="dropdown">
          <button
            class="btn p-0"
            type="button"
            id="transactionID"
            data-bs-toggle="dropdown"
            aria-haspopup="true"
            aria-expanded="false"
          >
            <i class="bx bx-dots-vertical-rounded"></i>
          </button>
          <div class="dropdown-menu dropdown-menu-end" aria-labelledby="transactionID">
            <a class="dropdown-item" href="javascript:void(0);">Actualizar</a>
            <a class="dropdown-item" href="javascript:void(0);">Ver más</a>
          </div>
        </div>
      </div>
      <div class="card-body">
        <ul class="p-0 m-0">
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/paypal.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Paypal</small>
                <h6 class="mb-0">Envío de materiales</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">+82.6</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Wallet</small>
                <h6 class="mb-0">Pago de colegiatura</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">+270.69</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/chart.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Transferencia</small>
                <h6 class="mb-0">Pago de servicios</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">+1,450.80</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/cc-success.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Tarjeta de crédito</small>
                <h6 class="mb-0">Reposición de útiles</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">-145.75</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
          <li class="d-flex mb-4 pb-1">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/wallet.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Wallet</small>
                <h6 class="mb-0">Pago de actividades</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">+1,042.99</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
          <li class="d-flex">
            <div class="avatar flex-shrink-0 me-3">
              <img src="{{ asset('materialize/assets/img/icons/unicons/cc-warning.png') }}" alt="User" class="rounded">
            </div>
            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
              <div class="me-2">
                <small class="d-block mb-1">Tarjeta de master</small>
                <h6 class="mb-0">Pago de uniformes</h6>
              </div>
              <div class="user-progress d-flex align-items-center gap-1">
                <h6 class="mb-0">-42.88</h6>
                <span class="text-muted">USD</span>
              </div>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</div>
@endsection

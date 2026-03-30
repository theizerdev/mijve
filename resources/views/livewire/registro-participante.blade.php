<div class="authentication-wrapper authentication-cover">
  {{-- Logo --}}
  <a href="{{ url('/') }}" class="auth-cover-brand d-flex align-items-center gap-2">
    <img src="{{ asset('logo/favicon.png') }}" alt="logo" style="height: 70px;" />
  </a>

  <div class="authentication-inner row m-0">

    {{-- ===== SIDEBAR IZQUIERDO ===== --}}
    <div class="d-none d-lg-flex col-lg-3 align-items-center justify-content-center p-12 mt-12 mt-xxl-0 purple darken-4 text-white">
      <div class="w-100 text-center px-3">
        @if($registrationClosed)
          {{-- Mensaje de inscripciones cerradas --}}
          <div class="text-center py-4">
            <div class="success-icon-wrapper mb-4">
              <div class="closed-circle mx-auto d-flex align-items-center justify-content-center">
                <i class="ri ri-lock-line ri-24px text-white"></i>
              </div>
            </div>
            <h3 class="text-white mb-2">Inscripciones Finalizadas</h3>
            <p class="text-light">
              El proceso de inscripciones ha concluido.<br>
              Gracias por tu participación.
            </p>
          </div>
        @else
          {{-- Indicador visual del paso actual --}}
          <div class="wizard-sidebar-indicator mb-5">
            @php
              $steps = [
                ['icon' => 'ri-file-list-3-line',    'label' => 'Términos y Condiciones',   'sub' => 'Condiciones del evento'],
                ['icon' => 'ri-map-pin-line',         'label' => 'Ubicación',  'sub' => 'Extensión y Actividad'],
                ['icon' => 'ri-user-3-line',          'label' => 'Personal',   'sub' => 'Datos del participante'],
                ['icon' => 'ri-phone-line',           'label' => 'Contacto',   'sub' => 'Teléfono y dirección'],
                ['icon' => 'ri-shield-check-line',    'label' => 'Confirmar',  'sub' => 'Revisar y enviar'],
              ];
            @endphp

            @foreach($steps as $i => $step)
              @php $num = $i + 1; @endphp
              <div class="d-flex align-items-center mb-4 sidebar-step {{ $currentStep === $num ? 'step-current' : ($currentStep > $num ? 'step-done' : 'step-pending') }}">
                <span class="sidebar-step-circle me-3 flex-shrink-0">
                  @if($currentStep > $num)
                    <i class="ri ri-check-line"></i>
                  @else
                    <i class="ri {{ $step['icon'] }}"></i>
                  @endif
                </span>
                <div class="text-start">
                  <span class="d-block fw-semibold sidebar-step-title text-white">{{ $step['label'] }}</span>
                  <small class="sidebar-step-sub">{{ $step['sub'] }}</small>
                </div>
              </div>
            @endforeach
          </div>

          {{-- Progreso --}}
          <div class="mt-4 px-2">
            <div class="d-flex justify-content-between mb-1">
              <small class="fw-medium text-heading">Progreso</small>
              <small class="fw-medium text-primary">{{ min(round(($currentStep / $totalSteps) * 100), 100) }}%</small>
            </div>
            <div class="progress" style="height: 6px;">
              <div class="progress-bar" role="progressbar"
                style="width: {{ min(round(($currentStep / $totalSteps) * 100), 100) }}%; transition: width .4s ease;"
                aria-valuenow="{{ $currentStep }}" aria-valuemin="0" aria-valuemax="{{ $totalSteps }}"></div>
            </div>
          </div>
        @endif
      </div>
    </div>

    {{-- ===== CONTENIDO PRINCIPAL ===== --}}
    <div class="d-flex col-lg-9 align-items-center justify-content-center authentication-bg p-4 p-sm-5 ">
      <div class="w-px-800 mt-12 mt-lg-0 pt-lg-0 pt-4">

        @if($registrationClosed)
          {{-- ===== VISTA DE INSCRIPCIONES CERRADAS ===== --}}
          <div class="text-center py-4 wizard-closed-wrapper" x-data x-init="$el.classList.add('animate-in')">
            <div class="closed-icon-wrapper mb-4">
              <div class="closed-circle mx-auto d-flex align-items-center justify-content-center">
                <i class="ri ri-lock-line ri-24px text-white"></i>
              </div>
            </div>
            <h2 class="mb-2">Proceso de Inscripciones Finalizado</h2>
            <p class="text-muted mb-5">
              La hora límite para realizar inscripciones fue a la <strong>1:00 PM</strong> hora de Venezuela.
            </p>

            {{-- Información adicional --}}
            <div class="card border shadow-none mx-auto mb-5" style="max-width: 580px;">
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-borderless mb-0">
                    <tbody>
                      <tr>
                        <td class="py-3 ps-4">
                          <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-3">
                              <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-time-line ri-16px"></i>
                              </span>
                            </span>
                            <span class="text-muted">Hora de cierre</span>
                          </div>
                        </td>
                        <td class="py-3 pe-4 text-end fw-medium">1:00 PM (Venezuela)</td>
                      </tr>
                      <tr>
                        <td class="py-3 ps-4">
                          <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-3">
                              <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-calendar-event-line ri-16px"></i>
                              </span>
                            </span>
                            <span class="text-muted">Fecha</span>
                          </div>
                        </td>
                        <td class="py-3 pe-4 text-end fw-medium">{{ now()->timezone('America/Caracas')->translatedFormat('d M, Y') }}</td>
                      </tr>
                      <tr>
                        <td class="py-3 ps-4">
                          <div class="d-flex align-items-center">
                            <span class="avatar avatar-sm me-3">
                              <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-information-line ri-16px"></i>
                              </span>
                            </span>
                            <span class="text-muted">Estado</span>
                          </div>
                        </td>
                        <td class="py-3 pe-4 text-end fw-medium">
                          <span class="badge bg-label-danger">Cerrado</span>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

            <p class="text-muted">
              Para cualquier información adicional, por favor contacta con tu líder correspondiente.
            </p>
          </div>
        @elseif($registroExitoso)
        {{-- ===== PANTALLA DE ÉXITO ===== --}}
        <div class="text-center py-4 wizard-success-wrapper" x-data x-init="$el.classList.add('animate-in')">
          <div class="success-icon-wrapper mb-4">
            <div class="success-circle">
              <i class="ri ri-check-line"></i>
            </div>
          </div>
          <h3 class="mb-2">¡Registro Exitoso!</h3>
          <p class="text-muted mb-5">
            <strong class="text-heading">{{ $participanteCreado->nombres }} {{ $participanteCreado->apellidos }}</strong>
            ha sido registrado correctamente en el sistema.
          </p>

          {{-- Tarjeta resumen --}}
          <div class="card border shadow-none mx-auto mb-5" style="max-width: 480px;">
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-borderless mb-0">
                  <tbody>
                    <tr>
                      <td class="py-3 ps-4">
                        <div class="d-flex align-items-center">
                          <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-primary"><i class="ri ri-calendar-todo-line ri-16px"></i></span></span>
                          <span class="text-muted">Actividad</span>
                        </div>
                      </td>
                      <td class="py-3 pe-4 text-end fw-medium">{{ $participanteCreado->actividad->nombre ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="py-3 ps-4">
                        <div class="d-flex align-items-center">
                          <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-info"><i class="ri ri-building-2-line ri-16px"></i></span></span>
                          <span class="text-muted">Extensión</span>
                        </div>
                      </td>
                      <td class="py-3 pe-4 text-end fw-medium">{{ $participanteCreado->extension->nombre ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                      <td class="py-3 ps-4">
                        <div class="d-flex align-items-center">
                          <span class="avatar avatar-sm me-3"><span class="avatar-initial rounded bg-label-success"><i class="ri ri-calendar-check-line ri-16px"></i></span></span>
                          <span class="text-muted">Fecha</span>
                        </div>
                      </td>
                      <td class="py-3 pe-4 text-end fw-medium">{{ now()->translatedFormat('d M, Y') }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <button wire:click="nuevoRegistro" class="btn btn-primary btn-lg px-5">
            <i class="ri ri-add-line me-2"></i>Registrar otro participante
          </button>
        </div>

        @else
        {{-- ===== WIZARD ===== --}}

        {{-- Stepper header mobile (visible solo en < lg) --}} <br><br>
        <div class="d-lg-none mb-4 ">
          <div class="d-flex align-items-center justify-content-between px-1">
            <span class="text-muted small">Paso {{ $currentStep }} de {{ $totalSteps }}</span>
            <span class="badge bg-primary">{{ round(($currentStep / $totalSteps) * 100) }}%</span>
          </div>
          <div class="progress mt-2" style="height: 5px;">
            <div class="progress-bar" style="width: {{ round(($currentStep / $totalSteps) * 100) }}%; transition: width .3s ease;"></div>
          </div>
        </div>

        {{-- STEP 1: Términos y Condiciones --}}
        <div class="wizard-step {{ $currentStep === 1 ? '' : 'd-none' }}" id="step-1">
          <div class="content-header mb-4">
            <h4 class="mb-1">Términos y Condiciones</h4>
            <span class="text-muted">Lee y acepta las condiciones para participar en el campamento.</span>
          </div>

          <div class="card border mb-4">
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
              <div class="terms-content">
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-primary"><i class="ri ri-heart-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Requisito de Fe</h6>
                    <p class="mb-0 text-muted">Para participar del campamento debe ser cristiano, ya que el evento está 100% enfocado y diseñado para jóvenes del Movimiento Misionero Mundial.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-success"><i class="ri ri-money-dollar-circle-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Condiciones de pago</h6>
                    <p class="mb-0 text-muted">Para formalizar la inscripción, el participante debe coordinar el pago correspondiente con su Líder de Jóvenes.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-warning"><i class="ri ri-refund-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Política de No Reembolso</h6>
                    <p class="mb-0 text-muted">Si por razones de su interés no puede asistir al campamento, no se reembolsará el dinero ya que será invertido en logística y gastos propios que no podrán ser reasignados.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-info"><i class="ri ri-calendar-check-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Rango de Edad</h6>
                    <p class="mb-0 text-muted">Este campamento está diseñado para personas cuyas edades están entre 14 y 35 años.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-secondary"><i class="ri ri-suitcase-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Artículos Personales</h6>
                    <p class="mb-0 text-muted">Cada participante deberá traer cobija, toalla y demás artículos personales, ya que en el evento solo se proveerá del espacio.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start mb-3">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-primary"><i class="ri ri-shirt-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Código de Vestimenta</h6>
                    <p class="mb-0 text-muted"><strong>Vestir con pudor y modestia:</strong> No pantalones muy ceñidos o con agujeros, faldas cortas, blusas con escotes u otro tipo de vestimenta que represente obscenidad (en caso de recién convertida). <strong>En el caso masculino:</strong> No debe usar franelillas, ni shorts, ni otro tipo de vestimenta que represente indecoro.</p>
                  </div>
                </div>
                <hr>
                <div class="d-flex align-items-start">
                  <span class="avatar avatar-xs me-3 mt-1 flex-shrink-0"><span class="avatar-initial rounded bg-label-danger"><i class="ri ri-shield-star-line ri-14px"></i></span></span>
                  <div>
                    <h6 class="mb-1">Normas de Convivencia</h6>
                    <p class="mb-0 text-muted">Debe estar dispuesto a acatar todas las normas de convivencia y disciplina que regirán en el campamento, a fin de garantizar el orden, bienestar y buen desarrollo de las actividades.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="form-check mb-4">
            <input class="form-check-input @error('acepta_terminos') is-invalid @enderror" type="checkbox" id="wz-terminos" wire:model="acepta_terminos" {{ $registrationClosed ? 'disabled' : '' }}>
            <label class="form-check-label" for="wz-terminos">
              He leído y acepto todos los términos y condiciones del campamento <span class="text-danger">*</span>
            </label>
            @error('acepta_terminos') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
          </div>

          <div class="col-12 d-flex justify-content-between">
            <button class="btn btn-outline-secondary" disabled {{ $registrationClosed ? 'disabled' : '' }}>
              <i class="icon-base ri ri-arrow-left-line icon-16px me-sm-1_5 me-0"></i>
              <span class="align-middle d-sm-inline-block d-none">Anterior</span>
            </button>
            <button class="btn btn-primary" wire:click="nextStep" wire:loading.attr="disabled" {{ $registrationClosed ? 'disabled' : '' }}>
              <span wire:loading.remove wire:target="nextStep">
                <span class="align-middle d-sm-inline-block d-none me-sm-1_5 me-0">Acepto, Continuar</span>
                <i class="icon-base ri ri-arrow-right-line icon-16px"></i>
              </span>

            </button>
          </div>
        </div>

        {{-- STEP 2: Ubicación y Actividad --}}
        <div class="wizard-step {{ $currentStep === 2 ? '' : 'd-none' }}" id="step-2">
          <div class="content-header mb-5">
            <h4 class="mb-1">Ubicación y Actividad</h4>
            <span class="text-muted">Selecciona la extensión y la actividad a la que deseas inscribirte.</span>
          </div>
          <div class="row gx-5">
            @if($empresas->count() > 1)
            <div class="col-sm-6 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <select class="form-select @error('empresa_id') is-invalid @enderror" id="wz-empresa" wire:model.live="empresa_id" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione...</option>
                  @foreach($empresas as $empresa)
                    <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                  @endforeach
                </select>
                <label for="wz-empresa">Empresa <span class="text-danger">*</span></label>
              </div>
              @error('empresa_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>
            @endif

            <div class="col-sm-{{ $empresas->count() > 1 ? '6' : '12' }} mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <select class="form-select @error('extension_id') is-invalid @enderror" id="wz-extension" wire:model.live="extension_id" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione una extensión...</option>
                  @foreach($extensiones as $ext)
                    <option value="{{ $ext->id }}">{{ $ext->nombre }}</option>
                  @endforeach
                </select>
                <label for="wz-extension">Extensión <span class="text-danger">*</span></label>
              </div>
              @error('extension_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <select class="form-select @error('actividad_id') is-invalid @enderror" id="wz-actividad" wire:model.live="actividad_id" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione una actividad...</option>
                  @foreach($actividades as $actividad)
                    <option value="{{ $actividad->id }}">{{ $actividad->nombre }} ({{ $actividad->edad_desde }} – {{ $actividad->edad_hasta }} años)</option>
                  @endforeach
                </select>
                <label for="wz-actividad">Actividad <span class="text-danger">*</span></label>
              </div>
              @error('actividad_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            @if($zona || $distrito)
            <div class="col-sm-6 mb-5">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" value="{{ $zona }}" readonly id="wz-zona">
                <label for="wz-zona"><i class="ri ri-map-2-line me-1"></i>Zona</label>
              </div>
            </div>
            <div class="col-sm-6 mb-5">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control" value="{{ $distrito }}" readonly id="wz-distrito">
                <label for="wz-distrito"><i class="ri ri-map-pin-2-line me-1"></i>Distrito</label>
              </div>
            </div>
            @endif

            <div class="col-12 d-flex justify-content-between">
              <button class="btn btn-outline-secondary" wire:click="prevStep" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="icon-base ri ri-arrow-left-line icon-16px me-sm-1_5 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Anterior</span>
              </button>
              <button class="btn btn-primary" wire:click="nextStep" wire:loading.attr="disabled" {{ $registrationClosed ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="nextStep">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1_5 me-0">Siguiente</span>
                  <i class="icon-base ri ri-arrow-right-line icon-16px"></i>
                </span>
              </button>
            </div>
          </div>
        </div>

        {{-- STEP 3: Información Personal --}}
        <div class="wizard-step {{ $currentStep === 3 ? '' : 'd-none' }}" id="step-3">
          <div class="content-header mb-5">
            <h4 class="mb-1">Información Personal</h4>
            <span class="text-muted">Ingresa los datos personales del participante.</span>
          </div>
          <div class="row gx-5">
            <div class="col-sm-6 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control @error('nombres') is-invalid @enderror" id="wz-nombres"
                  wire:model="nombres" placeholder="Juan Carlos" {{ $registrationClosed ? 'readonly' : '' }}>
                <label for="wz-nombres">Nombres <span class="text-danger">*</span></label>
              </div>
              @error('nombres') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-6 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control @error('apellidos') is-invalid @enderror" id="wz-apellidos"
                  wire:model="apellidos" placeholder="Pérez García" {{ $registrationClosed ? 'readonly' : '' }}>
                <label for="wz-apellidos">Apellidos <span class="text-danger">*</span></label>
              </div>
              @error('apellidos') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <input type="text" class="form-control @error('cedula') is-invalid @enderror" id="wz-cedula"
                  wire:model="cedula" placeholder="V-12345678" {{ $registrationClosed ? 'readonly' : '' }}>
                <label for="wz-cedula">Cédula</label>
              </div>
              @error('cedula') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <input type="date" class="form-control @error('fecha_nacimiento') is-invalid @enderror" id="wz-fnac"
                  wire:model.live="fecha_nacimiento" max="{{ date('Y-m-d') }}" placeholder=" " {{ $registrationClosed ? 'readonly' : '' }}>
                <label for="wz-fnac">Fecha de Nacimiento <span class="text-danger">*</span></label>
              </div>
              @error('fecha_nacimiento') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <input type="number" class="form-control @error('edad') is-invalid @enderror" id="wz-edad"
                  wire:model="edad" readonly placeholder=" ">
                <label for="wz-edad">Edad (calculada)</label>
              </div>
              @error('edad') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <select class="form-select @error('genero') is-invalid @enderror" id="wz-genero" wire:model="genero" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione...</option>
                  <option value="Masculino">Masculino</option>
                  <option value="Femenino">Femenino</option>
                </select>
                <label for="wz-genero">Género <span class="text-danger">*</span></label>
              </div>
              @error('genero') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-4 mb-5">
              <div class="form-floating form-floating-outline">
                <select class="form-select" id="wz-ecivil" wire:model="estado_civil" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione...</option>
                  <option value="Soltero(a)">Soltero(a)</option>
                  <option value="Casado(a)">Casado(a)</option>
                  <option value="Divorciado(a)">Divorciado(a)</option>
                  <option value="Viudo(a)">Viudo(a)</option>
                  <option value="Unión Libre">Unión Libre</option>
                </select>
                <label for="wz-ecivil">Estado Civil</label>
              </div>
            </div>

            <div class="col-sm-4 mb-5 form-control-validation">
              <div class="form-floating form-floating-outline">
                <select class="form-select @error('tipo_miembro') is-invalid @enderror" id="wz-tipo-miembro" wire:model="tipo_miembro" {{ $registrationClosed ? 'disabled' : '' }}>
                  <option value="">Seleccione...</option>
                  <option value="Miembro Activo">Miembro Activo</option>
                  <option value="Probante">Probante</option>
                </select>
                <label for="wz-tipo-miembro">Tipo de Miembro <span class="text-danger">*</span></label>
              </div>
              @error('tipo_miembro') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex justify-content-between">
              <button class="btn btn-outline-secondary" wire:click="prevStep" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="icon-base ri ri-arrow-left-line icon-16px me-sm-1_5 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Anterior</span>
              </button>
              <button class="btn btn-primary" wire:click="nextStep" wire:loading.attr="disabled" {{ $registrationClosed ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="nextStep">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1_5 me-0">Siguiente</span>
                  <i class="icon-base ri ri-arrow-right-line icon-16px"></i>
                </span>

              </button>
            </div>
          </div>
        </div>

        {{-- STEP 4: Contacto --}}
        <div class="wizard-step {{ $currentStep === 4 ? '' : 'd-none' }}" id="step-4">
          <div class="content-header mb-5">
            <h4 class="mb-1">Información de Contacto</h4>
            <span class="text-muted">Proporciona los datos de contacto para comunicaciones.</span>
          </div>
          <div class="row gx-5">
            <div class="col-sm-6 mb-5">
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ri ri-phone-line ri-20px"></i></span>
                <div class="form-floating form-floating-outline">
                  <input type="tel" class="form-control @error('telefono_principal') is-invalid @enderror" id="wz-tel1"
                    wire:model="telefono_principal" placeholder="0424-1234567" {{ $registrationClosed ? 'readonly' : '' }}>
                  <label for="wz-tel1">Teléfono Principal</label>
                </div>
              </div>
              @error('telefono_principal') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-sm-6 mb-5">
              <div class="input-group input-group-merge">
                <span class="input-group-text"><i class="ri ri-phone-line ri-20px"></i></span>
                <div class="form-floating form-floating-outline">
                  <input type="tel" class="form-control @error('telefono_alternativo') is-invalid @enderror" id="wz-tel2"
                    wire:model="telefono_alternativo" placeholder="0212-1234567" {{ $registrationClosed ? 'readonly' : '' }}>
                  <label for="wz-tel2">Teléfono Alternativo</label>
                </div>
              </div>
              @error('telefono_alternativo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 mb-5">
              <div class="form-floating form-floating-outline">
                <textarea class="form-control @error('direccion') is-invalid @enderror" id="wz-direccion"
                  wire:model="direccion" placeholder="Dirección completa" style="height: 100px;" {{ $registrationClosed ? 'readonly' : '' }}></textarea>
                <label for="wz-direccion">Dirección de Residencia</label>
              </div>
              @error('direccion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </div>

            <div class="col-12 d-flex justify-content-between">
              <button class="btn btn-outline-secondary" wire:click="prevStep" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="icon-base ri ri-arrow-left-line icon-16px me-sm-1_5 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">Anterior</span>
              </button>
              <button class="btn btn-primary" wire:click="nextStep" wire:loading.attr="disabled" {{ $registrationClosed ? 'disabled' : '' }}>
                <span wire:loading.remove wire:target="nextStep">
                  <span class="align-middle d-sm-inline-block d-none me-sm-1_5 me-0">Siguiente</span>
                  <i class="icon-base ri ri-arrow-right-line icon-16px"></i>
                </span>

              </button>
            </div>
          </div>
        </div>

        {{-- STEP 5: Confirmación --}}
        <div class="wizard-step {{ $currentStep === 5 ? '' : 'd-none' }}" id="step-5">
          <div class="content-header mb-5">
            <h4 class="mb-1">Confirmar Registro</h4>
            <span class="text-muted">Revisa toda la información antes de enviar. Puedes volver a cualquier paso para corregir.</span>
          </div>

          {{-- Tarjeta: Ubicación --}}
          <div class="card border mb-4 review-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-xs"><span class="avatar-initial rounded bg-label-primary"><i class="ri ri-map-pin-line ri-14px"></i></span></span>
                <h6 class="mb-0">Ubicación y Actividad</h6>
              </div>
              <button class="btn btn-sm btn-icon btn-text-primary" wire:click="goToStep(2)" title="Editar" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="ri ri-edit-line ri-18px"></i>
              </button>
            </div>
            <div class="card-body pb-3">
              <div class="row g-3">
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium ls-1" style="font-size:.7rem; letter-spacing: .08em;">Empresa</small>
                  <span>{{ $this->getEmpresaNombre() }}</span>
                </div>
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Extensión</small>
                  <span>{{ $this->getExtensionNombre() }}</span>
                </div>
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Actividad</small>
                  <span class="badge bg-label-primary">{{ $this->getActividadNombre() }}</span>
                </div>
                @if($zona || $distrito)
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Zona / Distrito</small>
                  <span>{{ $zona ?: '—' }} / {{ $distrito ?: '—' }}</span>
                </div>
                @endif
              </div>
            </div>
          </div>

          {{-- Tarjeta: Personal --}}
          <div class="card border mb-4 review-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-xs"><span class="avatar-initial rounded bg-label-info"><i class="ri ri-user-3-line ri-14px"></i></span></span>
                <h6 class="mb-0">Información Personal</h6>
              </div>
              <button class="btn btn-sm btn-icon btn-text-primary" wire:click="goToStep(3)" title="Editar" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="ri ri-edit-line ri-18px"></i>
              </button>
            </div>
            <div class="card-body pb-3">
              <div class="row g-3">
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Nombre Completo</small>
                  <span class="fw-medium">{{ $nombres }} {{ $apellidos }}</span>
                </div>
                <div class="col-sm-3">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Cédula</small>
                  <span>{{ $cedula ?: '—' }}</span>
                </div>
                <div class="col-sm-3">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Edad</small>
                  <span>{{ $edad }} años</span>
                </div>
                <div class="col-sm-4">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Fecha Nac.</small>
                  <span>{{ $fecha_nacimiento ? \Carbon\Carbon::parse($fecha_nacimiento)->format('d/m/Y') : '—' }}</span>
                </div>
                <div class="col-sm-4">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Género</small>
                  <span>{{ $genero }}</span>
                </div>
                <div class="col-sm-4">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Estado Civil</small>
                  <span>{{ $estado_civil ?: '—' }}</span>
                </div>
                <div class="col-sm-4">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Tipo de Miembro</small>
                  <span class="badge bg-label-{{ $tipo_miembro === 'Miembro Activo' ? 'success' : 'warning' }}">{{ $tipo_miembro }}</span>
                </div>
              </div>
            </div>
          </div>

          {{-- Tarjeta: Contacto --}}
          <div class="card border mb-5 review-card">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-xs"><span class="avatar-initial rounded bg-label-warning"><i class="ri ri-phone-line ri-14px"></i></span></span>
                <h6 class="mb-0">Contacto</h6>
              </div>
              <button class="btn btn-sm btn-icon btn-text-primary" wire:click="goToStep(4)" title="Editar" {{ $registrationClosed ? 'disabled' : '' }}>
                <i class="ri ri-edit-line ri-18px"></i>
              </button>
            </div>
            <div class="card-body pb-3">
              <div class="row g-3">
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Teléfono Principal</small>
                  <span>{{ $telefono_principal ?: '—' }}</span>
                </div>
                <div class="col-sm-6">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Teléfono Alternativo</small>
                  <span>{{ $telefono_alternativo ?: '—' }}</span>
                </div>
                <div class="col-sm-12">
                  <small class="text-uppercase text-muted d-block mb-1 fw-medium" style="font-size:.7rem; letter-spacing: .08em;">Dirección</small>
                  <span>{{ $direccion ?: '—' }}</span>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-between">
            <button class="btn btn-outline-secondary" wire:click="prevStep" {{ $registrationClosed ? 'disabled' : '' }}>
              <i class="icon-base ri ri-arrow-left-line icon-16px me-sm-1_5 me-0"></i>
              <span class="align-middle d-sm-inline-block d-none">Anterior</span>
            </button>
            <button class="btn btn-success btn-lg" wire:click="save" wire:loading.attr="disabled" {{ $registrationClosed ? 'disabled' : '' }}>
              <span wire:loading.remove wire:target="save">
                Confirmar Registro <i class="icon-base ri ri-check-line icon-16px ms-1_5"></i>
              </span>

            </button>
          </div>
        </div>

        @endif
      </div>
    </div>
  </div>
</div>

@push('styles')
<style>
  /* ===== Sidebar Steps ===== */
  .sidebar-step-circle {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.1rem;
    transition: all .3s ease;
  }
  .step-pending .sidebar-step-circle {
    background: rgba(var(--bs-secondary-rgb, 108,117,125), .1);
    color: #a0a4a8;
  }
  .step-pending .sidebar-step-title { color: #a0a4a8; }
  .step-pending .sidebar-step-sub   { color: #bfc3c7; }

  .step-current .sidebar-step-circle {
    background: var(--bs-primary);
    color: #fff;
    box-shadow: 0 4px 14px rgba(var(--bs-primary-rgb, 115,103,240), .4);
  }
  .step-current .sidebar-step-title { color: var(--bs-primary); font-weight: 600; }
  .step-current .sidebar-step-sub   { color: var(--bs-body-color); }

  .step-done .sidebar-step-circle {
    background: rgba(40, 199, 111, .15);
    color: #28C76F;
  }
  .step-done .sidebar-step-title { color: var(--bs-heading-color); }
  .step-done .sidebar-step-sub   { color: #28C76F; }

  /* Connector lines between sidebar steps */
  .sidebar-step:not(:last-child) {
    position: relative;
  }
  .sidebar-step:not(:last-child)::after {
    content: '';
    position: absolute;
    left: 20px;
    top: 46px;
    width: 2px;
    height: calc(100% - 14px);
    border-left: 2px dashed #e0e0e0;
  }
  .step-done:not(:last-child)::after {
    border-left-color: #28C76F;
    border-left-style: solid;
  }
  .step-current:not(:last-child)::after {
    border-left-color: var(--bs-primary);
    border-left-style: solid;
  }

  /* ===== Review cards hover ===== */
  .review-card {
    transition: border-color .2s, box-shadow .2s;
  }
  .review-card:hover {
    border-color: var(--bs-primary) !important;
    box-shadow: 0 2px 12px rgba(var(--bs-primary-rgb, 115,103,240), .12);
  }

  /* ===== Success animation ===== */
  .success-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #28C76F, #48DA89);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: successPulse 1.5s ease-in-out infinite;
  }
  .success-circle i {
    font-size: 2.5rem;
    color: #fff;
  }
  @keyframes successPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(40, 199, 111, .35); }
    50%      { box-shadow: 0 0 0 18px rgba(40, 199, 111, 0); }
  }
  .wizard-success-wrapper {
    opacity: 0;
    transform: translateY(20px);
  }
  .wizard-success-wrapper.animate-in {
    animation: fadeSlideUp .5s ease forwards;
  }
  @keyframes fadeSlideUp {
    to { opacity: 1; transform: translateY(0); }
  }

  /* ===== Registration closed styles ===== */
  .closed-circle {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    background: linear-gradient(135deg, #ff4750, #ff6b7a);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    animation: pulse 1.5s ease-in-out infinite;
  }
  .closed-circle i {
    font-size: 2.5rem;
    color: #fff;
  }
  @keyframes pulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(255, 71, 80, .35); }
    50%      { box-shadow: 0 0 0 18px rgba(255, 71, 80, 0); }
  }
  .wizard-closed-wrapper {
    opacity: 0;
    transform: translateY(20px);
  }
  .wizard-closed-wrapper.animate-in {
    animation: fadeSlideUp .5s ease forwards;
  }
  @keyframes fadeSlideUp {
    to { opacity: 1; transform: translateY(0); }
  }

  /* ===== Wizard step transitions ===== */
  .wizard-step {
    animation: stepFadeIn .35s ease;
  }
  @keyframes stepFadeIn {
    from { opacity: 0; transform: translateX(12px); }
    to   { opacity: 1; transform: translateX(0); }
  }

  /* ===== Mobile progress bar color ===== */
  .progress-bar {
    background: var(--bs-primary);
  }

  /* ===== Terms content ===== */
  .terms-content hr {
    opacity: .1;
  }
</style>
@endpush

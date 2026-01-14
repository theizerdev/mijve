<div>
    <!-- Alertas -->
    @if($error)
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="ri ri-error-warning-line me-2 ri-20px"></i>
                <div>{{ $error }}</div>
            </div>
            <button type="button" class="btn-close" wire:click="clearMessages"></button>
        </div>
    @endif

    @if($success)
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="ri ri-checkbox-circle-line me-2 ri-20px"></i>
                <div>{{ $success }}</div>
            </div>
            <button type="button" class="btn-close" wire:click="clearMessages"></button>
        </div>
    @endif

    <!-- Selector de Modo -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <span class="fw-medium">Tipo de envío:</span>
                <div class="btn-group" role="group">
                    <input type="radio" class="btn-check" id="modeIndividual" value="individual" wire:model.live="sendMode">
                    <label class="btn btn-outline-primary" for="modeIndividual">
                        <i class="ri ri-user-line me-1"></i>Individual
                    </label>
                    
                    <input type="radio" class="btn-check" id="modeGrupal" value="grupal" wire:model.live="sendMode">
                    <label class="btn btn-outline-primary" for="modeGrupal">
                        <i class="ri ri-group-line me-1"></i>Grupal
                    </label>
                </div>

                @if($sendMode === 'grupal')
                    <div class="vr d-none d-md-block"></div>
                    <span class="fw-medium">Enviar a:</span>
                    <div class="btn-group" role="group">
                        <input type="radio" class="btn-check" id="targetMayores" value="mayores" wire:model.live="targetGroup">
                        <label class="btn btn-outline-success" for="targetMayores">
                            <i class="ri ri-user-follow-line me-1"></i>Estudiantes Mayores
                        </label>
                        
                        <input type="radio" class="btn-check" id="targetMenores" value="menores" wire:model.live="targetGroup">
                        <label class="btn btn-outline-info" for="targetMenores">
                            <i class="ri ri-parent-line me-1"></i>Representantes (Menores)
                        </label>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Panel Principal -->
        <div class="{{ $sendMode === 'grupal' ? 'col-lg-7' : 'col-lg-8' }}">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        @if($sendMode === 'individual')
                            <i class="ri ri-send-plane-line me-2"></i>Enviar Mensaje Individual
                        @else
                            <i class="ri ri-mail-send-line me-2"></i>Enviar Mensaje Grupal
                            @if($targetGroup === 'mayores')
                                <span class="badge bg-success ms-2">Estudiantes +18</span>
                            @else
                                <span class="badge bg-info ms-2">Representantes</span>
                            @endif
                        @endif
                    </h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="sendMessage">
                        @if($sendMode === 'individual')
                            <!-- Número de Teléfono (Solo Individual) -->
                            <div class="mb-4">
                                <label for="to" class="form-label fw-medium">
                                    <i class="ri ri-phone-line me-1"></i>Número de Teléfono
                                </label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light">
                                        <i class="ri ri-whatsapp-line text-success"></i>
                                    </span>
                                    <input type="text" 
                                           id="to"
                                           class="form-control @error('to') is-invalid @enderror" 
                                           wire:model.live="to" 
                                           placeholder="584121234567"
                                           maxlength="15"
                                           inputmode="numeric">
                                    @error('to')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="form-text">
                                    <i class="ri ri-information-line me-1"></i>
                                    Formato: código de país + número sin espacios (ej: 584121234567)
                                </div>
                            </div>
                        @else
                            <!-- Filtros para Grupal -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Nivel Educativo</label>
                                    <select class="form-select" wire:model.live="filterNivel">
                                        <option value="">Todos los niveles</option>
                                        @foreach($niveles as $nivel)
                                            <option value="{{ $nivel->id }}">{{ $nivel->nombre }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Grado</label>
                                    <select class="form-select" wire:model.live="filterGrado">
                                        <option value="">Todos los grados</option>
                                        @foreach($grados as $grado)
                                            <option value="{{ $grado }}">{{ $grado }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-medium">Sección</label>
                                    <select class="form-select" wire:model.live="filterSeccion">
                                        <option value="">Todas las secciones</option>
                                        @foreach($secciones as $seccion)
                                            <option value="{{ $seccion }}">{{ $seccion }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Lista de Destinatarios -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-medium mb-0">
                                        <i class="ri ri-contacts-book-line me-1"></i>Seleccionar Destinatarios
                                    </label>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-primary">{{ $selectedCount }} seleccionados</span>
                                        @if($selectedCount !== $selectedWithPhoneCount)
                                            <span class="badge bg-warning" title="Algunos sin teléfono registrado">
                                                {{ $selectedWithPhoneCount }} con teléfono
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                
                                @error('selectedStudents')
                                    <div class="alert alert-danger py-2 small mb-2">{{ $message }}</div>
                                @enderror

                                <div class="border rounded" style="max-height: 250px; overflow-y: auto;">
                                    @if(count($students) > 0)
                                        <div class="list-group list-group-flush">
                                            <!-- Seleccionar Todos -->
                                            <label class="list-group-item list-group-item-action bg-light sticky-top">
                                                <div class="d-flex align-items-center">
                                                    <input type="checkbox" class="form-check-input me-2" 
                                                           wire:model.live="selectAll">
                                                    <strong>Seleccionar todos ({{ count($students) }})</strong>
                                                </div>
                                            </label>

                                            @foreach($students as $student)
                                                <label class="list-group-item list-group-item-action {{ !$student['tiene_telefono'] ? 'bg-light text-muted' : '' }}">
                                                    <div class="d-flex align-items-center">
                                                        <input type="checkbox" 
                                                               class="form-check-input me-2" 
                                                               wire:model.live="selectedStudents"
                                                               value="{{ $student['id'] }}"
                                                               {{ !$student['tiene_telefono'] ? 'disabled' : '' }}>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between">
                                                                <span class="fw-medium">{{ $student['nombre'] }}</span>
                                                                <small class="text-muted">{{ $student['grado'] }}</small>
                                                            </div>
                                                            @if($targetGroup === 'menores')
                                                                <small class="{{ $student['tiene_telefono'] ? 'text-muted' : 'text-danger' }}">
                                                                    <i class="ri ri-parent-line me-1"></i>
                                                                    {{ $student['representante'] ?: 'Sin representante' }}
                                                                    @if($student['tiene_telefono'])
                                                                        - {{ $student['telefono'] }}
                                                                    @else
                                                                        <span class="badge bg-danger ms-1">Sin teléfono</span>
                                                                    @endif
                                                                </small>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="text-center py-4 text-muted">
                                            <i class="ri ri-user-search-line ri-24px mb-2"></i>
                                            <p class="mb-0">No se encontraron estudiantes con los filtros seleccionados</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Plantillas Rápidas -->
                        <div class="mb-4">
                            <label class="form-label fw-medium">
                                <i class="ri ri-file-list-3-line me-1"></i>Plantillas Rápidas
                            </label>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($templates as $template)
                                    <button type="button" 
                                            class="btn btn-sm {{ $selectedTemplate === $template['id'] ? 'btn-primary' : 'btn-label-primary' }}"
                                            wire:click="useTemplate('{{ $template['id'] }}')">
                                        {{ $template['name'] }}
                                    </button>
                                @endforeach
                            </div>
                        </div>

                        <!-- Mensaje -->
                        <div class="mb-4">
                            <label for="message" class="form-label fw-medium">
                                <i class="ri ri-message-3-line me-1"></i>Mensaje
                            </label>
                            <textarea id="message"
                                      class="form-control @error('message') is-invalid @enderror" 
                                      wire:model.live="message" 
                                      rows="5" 
                                      placeholder="Escribe tu mensaje aquí..."
                                      maxlength="1000"
                                      style="resize: none;"></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    @if($sendMode === 'grupal')
                                        <i class="ri ri-magic-line me-1"></i>
                                        Variables: <code>{nombre}</code>, <code>{estudiante}</code>, <code>{grado}</code>
                                    @else
                                        <i class="ri ri-information-line me-1"></i>
                                        Puedes usar emojis y saltos de línea
                                    @endif
                                </small>
                                <small class="{{ $charCount > 900 ? 'text-danger' : 'text-muted' }}">
                                    <span class="fw-medium">{{ $charCount }}</span>/1000
                                </small>
                            </div>
                        </div>

                        <!-- Barra de Progreso (Envío Grupal) -->
                        @if($isSendingBulk)
                            <div class="mb-4">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small fw-medium">Enviando mensajes...</span>
                                    <span class="small">{{ $sendProgress }} / {{ $sendTotal }}</span>
                                </div>
                                <div class="progress" style="height: 10px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" 
                                         style="width: {{ $sendTotal > 0 ? ($sendProgress / $sendTotal) * 100 : 0 }}%"></div>
                                </div>
                                <div class="d-flex gap-3 mt-2 small">
                                    <span class="text-success"><i class="ri ri-check-line"></i> {{ $sendResults['success'] ?? 0 }} enviados</span>
                                    <span class="text-danger"><i class="ri ri-close-line"></i> {{ $sendResults['failed'] ?? 0 }} fallidos</span>
                                    @if(($sendResults['skipped'] ?? 0) > 0)
                                        <span class="text-warning"><i class="ri ri-skip-forward-line"></i> {{ $sendResults['skipped'] }} omitidos</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Botones -->
                        <div class="d-flex flex-wrap gap-2 justify-content-between">
                            <button type="button" 
                                    class="btn btn-label-secondary"
                                    wire:click="clearForm"
                                    @if($isSendingBulk) disabled @endif>
                                <i class="ri ri-eraser-line me-1"></i>Limpiar
                            </button>
                            
                            <button type="submit" 
                                    class="btn btn-success btn-lg"
                                    wire:loading.attr="disabled"
                                    wire:target="sendMessage"
                                    @if($sending || $isSendingBulk) disabled @endif>
                                <span wire:loading.remove wire:target="sendMessage">
                                    @if($sendMode === 'individual')
                                        <i class="ri ri-send-plane-fill me-1"></i>Enviar Mensaje
                                    @else
                                        <i class="ri ri-mail-send-fill me-1"></i>Enviar a {{ $selectedWithPhoneCount }} destinatarios
                                    @endif
                                </span>
                                <span wire:loading wire:target="sendMessage">
                                    <span class="spinner-border spinner-border-sm me-1"></span>Enviando...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Lateral -->
        <div class="{{ $sendMode === 'grupal' ? 'col-lg-5' : 'col-lg-4' }}">
            <!-- Vista Previa -->
            <div class="card mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title mb-0">
                        <i class="ri ri-smartphone-line me-2"></i>Vista Previa
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="whatsapp-preview">
                        <div class="whatsapp-header bg-success text-white p-2">
                            <div class="d-flex align-items-center">
                                <div class="avatar avatar-sm me-2">
                                    <span class="avatar-initial rounded-circle bg-white text-success">
                                        <i class="ri ri-user-line"></i>
                                    </span>
                                </div>
                                <div>
                                    <strong class="small">
                                        @if($sendMode === 'individual')
                                            {{ $to ?: 'Número' }}
                                        @else
                                            {{ $selectedCount }} destinatarios
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        </div>
                        <div class="whatsapp-body p-2" style="background-color: #e5ddd5; min-height: 120px;">
                            @if($message)
                                <div class="message-bubble bg-white p-2 rounded shadow-sm" style="max-width: 90%; margin-left: auto;">
                                    <p class="mb-0 small" style="white-space: pre-wrap;">{{ $message }}</p>
                                    <div class="text-end">
                                        <small class="text-muted" style="font-size: 0.7rem;">
                                            {{ now()->format('H:i') }}
                                            <i class="ri ri-check-double-line text-primary ms-1"></i>
                                        </small>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4 text-muted">
                                    <i class="ri ri-message-3-line ri-24px mb-2"></i>
                                    <p class="small mb-0">Escribe un mensaje</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info de Variables (Solo Grupal) -->
            @if($sendMode === 'grupal')
                <div class="card mb-4">
                    <div class="card-header py-3">
                        <h6 class="card-title mb-0">
                            <i class="ri ri-magic-line me-2"></i>Variables Disponibles
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 small">
                            <li class="mb-2">
                                <code class="bg-light px-2 py-1 rounded">{nombre}</code>
                                <span class="text-muted ms-2">→ Nombre del destinatario</span>
                            </li>
                            <li class="mb-2">
                                <code class="bg-light px-2 py-1 rounded">{estudiante}</code>
                                <span class="text-muted ms-2">→ Nombre del estudiante</span>
                            </li>
                            <li>
                                <code class="bg-light px-2 py-1 rounded">{grado}</code>
                                <span class="text-muted ms-2">→ Grado y sección</span>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Mensajes Recientes -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center py-3">
                    <h6 class="card-title mb-0">
                        <i class="ri ri-history-line me-2"></i>Recientes
                    </h6>
                    <button class="btn btn-sm btn-label-primary" wire:click="loadRecentMessages">
                        <i class="ri ri-refresh-line"></i>
                    </button>
                </div>
                <div class="card-body p-0">
                    @if(count($recentMessages) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($recentMessages as $msg)
                                <li class="list-group-item list-group-item-action cursor-pointer py-2"
                                    wire:click="resend('{{ $msg['to'] ?? '' }}', '{{ addslashes($msg['message'] ?? $msg['body'] ?? '') }}')"
                                    title="Click para reutilizar">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1 me-2">
                                            <p class="mb-0 fw-medium small text-truncate" style="max-width: 150px;">
                                                {{ $msg['to'] ?? 'N/A' }}
                                            </p>
                                            <p class="text-muted mb-0 small text-truncate" style="max-width: 150px;">
                                                {{ Str::limit($msg['message'] ?? $msg['body'] ?? '', 30) }}
                                            </p>
                                        </div>
                                        <span class="badge bg-success"><i class="ri ri-check-line"></i></span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="ri ri-inbox-line ri-20px"></i>
                            <p class="small mb-0">Sin mensajes</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tips -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card bg-light border-0">
                <div class="card-body py-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ri ri-shield-user-line text-success me-2 ri-20px"></i>
                                <div>
                                    <strong class="small">Mayores de edad</strong>
                                    <p class="text-muted mb-0 small">Mensaje directo al estudiante</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ri ri-parent-line text-info me-2 ri-20px"></i>
                                <div>
                                    <strong class="small">Menores de edad</strong>
                                    <p class="text-muted mb-0 small">Mensaje al representante registrado</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex align-items-center">
                                <i class="ri ri-time-line text-warning me-2 ri-20px"></i>
                                <div>
                                    <strong class="small">Envío secuencial</strong>
                                    <p class="text-muted mb-0 small">0.5s entre mensajes para evitar bloqueos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    .cursor-pointer:hover { background-color: rgba(var(--bs-primary-rgb), 0.05) !important; }
    .whatsapp-preview { border-radius: 8px; overflow: hidden; }
    .message-bubble { border-radius: 8px; border-top-right-radius: 0; }
    .sticky-top { z-index: 1; }
</style>
@endpush

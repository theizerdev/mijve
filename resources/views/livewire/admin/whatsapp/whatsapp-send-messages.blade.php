<div>
    <div class="container-fluid">
        <!-- Header Section -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="mb-1">Enviar Mensajes WhatsApp</h4>
                        <p class="text-muted mb-0">Envía mensajes individuales, masivos o usando plantillas</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button wire:click="toggleBulkMode" class="btn btn-outline-info">
                            <i class="ri ri-group-line me-2"></i>
                            {{ $bulkMode ? 'Modo Individual' : 'Modo Masivo' }}
                        </button>
                        <a href="{{ route('admin.whatsapp.dashboard') }}" class="btn btn-secondary">
                            <i class="ri ri-arrow-left-line me-2"></i>
                            Volver
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Connection Status -->
        @if($status !== 'connected')
            <div class="alert alert-warning">
                <i class="ri ri-alert-line me-2"></i>
                WhatsApp no está conectado. 
                <a href="{{ route('admin.whatsapp.connection') }}" class="btn btn-warning btn-sm ms-2">
                    Conectar WhatsApp
                </a>
            </div>
        @endif

        <div class="row">
            <!-- Main Form -->
            <div class="col-lg-8">
                @if(!$bulkMode)
                    <!-- Individual Message Form -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <ul class="nav nav-tabs card-header-tabs">
                                <li class="nav-item">
                                    <button class="nav-link {{ $activeTab === 'manual' ? 'active' : '' }}" 
                                            wire:click="$set('activeTab', 'manual')">
                                        <i class="ri ri-edit-line me-2"></i>Mensaje Manual
                                    </button>
                                </li>
                                <li class="nav-item">
                                    <button class="nav-link {{ $activeTab === 'template' ? 'active' : '' }}" 
                                            wire:click="$set('activeTab', 'template')">
                                        <i class="ri ri-file-text-line me-2"></i>Plantilla
                                    </button>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="sendMessage">
                                <!-- Recipient -->
                                <div class="mb-3">
                                    <label class="form-label">
                                        <i class="ri ri-phone-line me-1"></i>Número de Teléfono
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">+58</span>
                                        <input type="tel" class="form-control @error('recipient') is-invalid @enderror" 
                                               wire:model="recipient" placeholder="4121234567"
                                               {{ $status !== 'connected' ? 'disabled' : '' }}>
                                    </div>
                                    @error('recipient')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <!-- Manual Message -->
                                @if($activeTab === 'manual')
                                    <div class="mb-3">
                                        <label class="form-label">
                                            <i class="ri ri-message-3-line me-1"></i>Mensaje
                                        </label>
                                        <textarea class="form-control @error('message') is-invalid @enderror" 
                                                  wire:model="message" rows="4" 
                                                  placeholder="Escribe tu mensaje aquí..."
                                                  {{ $status !== 'connected' ? 'disabled' : '' }}></textarea>
                                        @error('message')<div class="text-danger small">{{ $message }}</div>@enderror
                                        <small class="text-muted">Caracteres: {{ strlen($message) }}/1000</small>
                                    </div>
                                @endif

                                <!-- Template -->
                                @if($activeTab === 'template')
                                    <div class="mb-3">
                                        <label class="form-label">Plantilla</label>
                                        <select class="form-select @error('selectedTemplate') is-invalid @enderror" 
                                                wire:model="selectedTemplate" {{ $status !== 'connected' ? 'disabled' : '' }}>
                                            <option value="">Selecciona una plantilla...</option>
                                            @foreach($templates as $template)
                                                <option value="{{ $template['id'] }}">
                                                    {{ $template['name'] }} - {{ ucfirst($template['category']) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('selectedTemplate')<div class="text-danger small">{{ $message }}</div>@enderror
                                    </div>

                                    @if($selectedTemplate && !empty($templateVariables))
                                        <div class="mb-3">
                                            <label class="form-label">Variables</label>
                                            @foreach($templateVariables as $variable => $value)
                                                <div class="mb-2">
                                                    <input type="text" class="form-control form-control-sm" 
                                                           wire:model="templateVariables.{{ $variable }}"
                                                           placeholder="{{ ucfirst(str_replace('_', ' ', $variable)) }}"
                                                           {{ $status !== 'connected' ? 'disabled' : '' }}>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($selectedTemplate)
                                        <div class="mb-3">
                                            <label class="form-label">Vista Previa</label>
                                            <div class="border rounded p-3 bg-light">
                                                @php
                                                    $template = collect($templates)->firstWhere('id', $selectedTemplate);
                                                    $preview = $template['content'] ?? '';
                                                    foreach($templateVariables as $key => $value) {
                                                        $preview = str_replace('{{' . $key . '}}', $value ?: '[' . $key . ']', $preview);
                                                    }
                                                @endphp
                                                {!! nl2br(e($preview)) !!}
                                            </div>
                                        </div>
                                    @endif
                                @endif

                                <div class="d-flex justify-content-between">
                                    <button type="button" wire:click="resetForm" class="btn btn-outline-secondary"
                                            {{ $status !== 'connected' ? 'disabled' : '' }}>
                                        <i class="ri ri-refresh-line me-2"></i>Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary"
                                            {{ $status !== 'connected' || $isSending ? 'disabled' : '' }}>
                                        @if($isSending)
                                            <span class="spinner-border spinner-border-sm me-2"></span>Enviando...
                                        @else
                                            <i class="ri ri-send-plane-line me-2"></i>Enviar Mensaje
                                        @endif
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <!-- Bulk Message Form -->
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h5 class="mb-0">
                                <i class="ri ri-group-line me-2"></i>Envío Masivo
                            </h5>
                        </div>
                        <div class="card-body">
                            <form wire:submit.prevent="sendMessage">
                                <div class="mb-3">
                                    <label class="form-label">Mensaje para Envío Masivo</label>
                                    <textarea class="form-control @error('bulkMessage') is-invalid @enderror" 
                                              wire:model="bulkMessage" rows="4" 
                                              placeholder="Mensaje que se enviará a todos los estudiantes seleccionados..."
                                              {{ $status !== 'connected' ? 'disabled' : '' }}></textarea>
                                    @error('bulkMessage')<div class="text-danger small">{{ $message }}</div>@enderror
                                    <small class="text-muted">Caracteres: {{ strlen($bulkMessage) }}/1000</small>
                                </div>

                                <div class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">Estudiantes Seleccionados ({{ count($selectedStudents) }})</label>
                                        <div>
                                            <button type="button" wire:click="selectAllStudents" class="btn btn-sm btn-outline-primary me-2">
                                                Seleccionar Todos
                                            </button>
                                            <button type="button" wire:click="clearSelection" class="btn btn-sm btn-outline-secondary">
                                                Limpiar
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                                        @if(count($students) > 0)
                                            @foreach($students as $student)
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           wire:click="toggleStudentSelection({{ $student['id'] }})"
                                                           {{ in_array($student['id'], $selectedStudents) ? 'checked' : '' }}
                                                           id="student_{{ $student['id'] }}">
                                                    <label class="form-check-label" for="student_{{ $student['id'] }}">
                                                        {{ $student['name'] }} - {{ $student['phone'] }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        @else
                                            <p class="text-muted text-center">No hay estudiantes con teléfono disponibles</p>
                                        @endif
                                    </div>
                                    @error('selectedStudents')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <button type="button" wire:click="resetForm" class="btn btn-outline-secondary">
                                        <i class="ri ri-refresh-line me-2"></i>Limpiar
                                    </button>
                                    <button type="submit" class="btn btn-primary"
                                            {{ $status !== 'connected' || $isSending || count($selectedStudents) === 0 ? 'disabled' : '' }}>
                                        @if($isSending)
                                            <span class="spinner-border spinner-border-sm me-2"></span>Enviando...
                                        @else
                                            <i class="ri ri-send-plane-line me-2"></i>Enviar a {{ count($selectedStudents) }} estudiantes
                                        @endif
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Recent Contacts -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <h6 class="mb-0">
                            <i class="ri ri-history-line me-2"></i>Contactos Recientes
                        </h6>
                    </div>
                    <div class="card-body">
                        @if(count($recentContacts) > 0)
                            @foreach($recentContacts as $contact)
                                <button type="button" class="btn btn-outline-secondary btn-sm w-100 mb-2 text-start"
                                        wire:click="selectRecentContact('{{ $contact->recipient }}')"
                                        {{ $status !== 'connected' ? 'disabled' : '' }}>
                                    <i class="ri ri-user-line me-2"></i>+58{{ $contact->recipient }}
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($contact->last_message)->diffForHumans() }}</small>
                                </button>
                            @endforeach
                        @else
                            <p class="text-muted text-center small">No hay contactos recientes</p>
                        @endif
                    </div>
                </div>

                <!-- Message History -->
                @if(count($messageHistory) > 0)
                    <div class="card shadow-sm">
                        <div class="card-header">
                            <h6 class="mb-0">
                                <i class="ri ri-chat-history-line me-2"></i>Historial Reciente
                            </h6>
                        </div>
                        <div class="card-body">
                            @foreach(array_slice($messageHistory, 0, 5) as $msg)
                                <div class="border-bottom pb-2 mb-2">
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">+58{{ $msg->recipient }}</small>
                                        <span class="badge bg-{{ $msg->status === 'sent' ? 'success' : 'secondary' }}">
                                            {{ $msg->status }}
                                        </span>
                                    </div>
                                    <p class="small mb-1">{{ Str::limit($msg->message, 50) }}</p>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($sendSuccess)
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri ri-check-line me-2"></i>Mensaje enviado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="clearMessages"></button>
            </div>
        </div>
    @endif

    @if($sendError)
        <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri ri-error-warning-line me-2"></i>{{ $sendError }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" wire:click="clearMessages"></button>
            </div>
        </div>
    @endif

    <!-- Session Flash Messages -->
    @if(session()->has('success'))
        <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1050">
            <div class="alert alert-success alert-dismissible fade show">
                <i class="ri ri-check-line me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session()->has('error'))
        <div class="position-fixed bottom-0 start-0 p-3" style="z-index: 1050">
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
</div>
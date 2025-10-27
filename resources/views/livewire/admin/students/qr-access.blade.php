<div>
    <div class="row g-4">
        <!-- Estadísticas -->
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-success rounded-3">
                                <i class="ri ri-login-box-line ri-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h5 class="mb-1">{{ $stats['entries'] }}</h5>
                        <p class="mb-0">Entradas Hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-danger rounded-3">
                                <i class="ri ri-logout-box-line ri-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h5 class="mb-1">{{ $stats['exits'] }}</h5>
                        <p class="mb-0">Salidas Hoy</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-primary rounded-3">
                                <i class="ri ri-bar-chart-line ri-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h5 class="mb-1">{{ $stats['total'] }}</h5>
                        <p class="mb-0">Total Accesos</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="avatar">
                            <div class="avatar-initial bg-label-info rounded-3">
                                <i class="ri ri-user-line ri-24px"></i>
                            </div>
                        </div>
                    </div>
                    <div class="card-info mt-4">
                        <h5 class="mb-1">{{ $stats['activeStudents'] }}</h5>
                        <p class="mb-0">Estudiantes Activos</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Escáner QR -->
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Escáner de Acceso</h5>
                    <div class="d-flex gap-2">
                        <button wire:click="toggleSound" class="btn btn-sm btn-icon {{ $soundEnabled ? 'btn-primary' : 'btn-secondary' }}">
                            <i class="ri ri-volume-{{ $soundEnabled ? 'up' : 'mute' }}-line"></i>
                        </button>
                        <div class="btn-group">
                            <button wire:click="$set('scanMode', 'camera')" class="btn btn-sm {{ $scanMode === 'camera' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-camera-line me-1"></i> Cámara
                            </button>
                            <button wire:click="$set('scanMode', 'manual')" class="btn btn-sm {{ $scanMode === 'manual' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-keyboard-line me-1"></i> Manual
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($scanMode === 'camera')
                        <div id="qr-reader" class="mb-3" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                    @else
                        <div class="mb-3">
                            <label class="form-label">Código del Estudiante</label>
                            <div class="input-group">
                                <input type="text" wire:model="manualCode" class="form-control" placeholder="Ingrese el código" wire:keydown.enter="searchByManualCode">
                                <button wire:click="searchByManualCode" class="btn btn-primary">
                                    <i class="ri ri-search-line"></i> Buscar
                                </button>
                            </div>
                        </div>
                    @endif

                    @if($showStudentInfo && $selectedStudent)
                        <div class="alert alert-primary d-flex align-items-center" role="alert">
                            <i class="ri ri-information-line ri-22px me-2"></i>
                            <div>Estudiante encontrado. Registre el acceso a continuación.</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Información del Estudiante -->
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Información del Estudiante</h5>
                </div>
                <div class="card-body">
                    @if($selectedStudent)
                        <div class="text-center mb-3">
                            @if($selectedStudent->foto)
                                <img src="{{ asset('storage/' . $selectedStudent->foto) }}" alt="Foto" class="rounded" style="width: 120px; height: 120px; object-fit: cover;">
                            @else
                                <div class="avatar avatar-xl">
                                    <span class="avatar-initial rounded bg-label-primary">
                                        {{ substr($selectedStudent->nombres, 0, 1) }}{{ substr($selectedStudent->apellidos, 0, 1) }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>Nombre:</strong> {{ $selectedStudent->nombres }} {{ $selectedStudent->apellidos }}
                        </div>
                        <div class="mb-2">
                            <strong>Código:</strong> {{ $selectedStudent->codigo }}
                        </div>
                        <div class="mb-2">
                            <strong>Documento:</strong> {{ $selectedStudent->documento_identidad }}
                        </div>
                        <div class="mb-2">
                            <strong>Grado:</strong> {{ $selectedStudent->grado }} - {{ $selectedStudent->seccion }}
                        </div>
                        @if($selectedStudent->nivelEducativo)
                            <div class="mb-2">
                                <strong>Nivel:</strong> {{ $selectedStudent->nivelEducativo->nombre }}
                            </div>
                        @endif
                        @if($selectedStudent->turno)
                            <div class="mb-2">
                                <strong>Turno:</strong> {{ $selectedStudent->turno->nombre }}
                            </div>
                        @endif
                        @if($selectedStudent->es_menor_de_edad)
                            <div class="badge bg-label-warning">Menor de Edad</div>
                        @endif

                        <hr class="my-3">

                        <div class="mb-3">
                            <label class="form-label">Tipo de Acceso</label>
                            <div class="btn-group w-100">
                                <button wire:click="$set('accessType', 'entrada')" class="btn {{ $accessType === 'entrada' ? 'btn-success' : 'btn-outline-success' }}">
                                    <i class="ri ri-login-box-line me-1"></i> Entrada
                                </button>
                                <button wire:click="$set('accessType', 'salida')" class="btn {{ $accessType === 'salida' ? 'btn-danger' : 'btn-outline-danger' }}">
                                    <i class="ri ri-logout-box-line me-1"></i> Salida
                                </button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Notas (Opcional)</label>
                            <textarea wire:model="notes" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
                        </div>

                        <button wire:click="registerAccess" class="btn btn-primary w-100">
                            <i class="ri ri-save-line me-1"></i> Registrar {{ ucfirst($accessType) }}
                        </button>
                        <button wire:click="resetForm" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="ri ri-close-line me-1"></i> Cancelar
                        </button>
                    @else
                        <div class="text-center text-muted py-5">
                            <i class="ri ri-qr-scan-2-line ri-48px mb-3"></i>
                            <p>Escanee un código QR o ingrese un código manualmente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Historial de Accesos -->
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Registros de Hoy</h5>
                    <button wire:click="loadTodayLogs" class="btn btn-sm btn-outline-primary">
                        <i class="ri ri-refresh-line"></i> Actualizar
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Hora</th>
                                    <th>Estudiante</th>
                                    <th>Código</th>
                                    <th>Tipo</th>
                                    <th>Registrado Por</th>
                                    <th>Notas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayLogs as $log)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($log['access_time'])->format('H:i:s') }}</td>
                                        <td>
                                            @if(isset($log['student']))
                                                {{ $log['student']['nombres'] }} {{ $log['student']['apellidos'] }}
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($log['student']))
                                                {{ $log['student']['codigo'] }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($log['type'] === 'entrada')
                                                <span class="badge bg-label-success">
                                                    <i class="ri ri-login-box-line me-1"></i> Entrada
                                                </span>
                                            @else
                                                <span class="badge bg-label-danger">
                                                    <i class="ri ri-logout-box-line me-1"></i> Salida
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($log['registered_by_user']))
                                                {{ $log['registered_by_user']['name'] }}
                                            @endif
                                        </td>
                                        <td>{{ $log['notes'] ?? '-' }}</td>
                                        <td>
                                            @if(auth()->user()->hasRole('Admin'))
                                                <button wire:click="deleteLog({{ $log['id'] }})" class="btn btn-sm btn-icon btn-text-danger" onclick="return confirm('¿Eliminar este registro?')">
                                                    <i class="ri ri-delete-bin-line"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No hay registros para hoy
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @assets
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endassets

    @script
    <script>
        let html5QrCode = null;
        let scannerActive = false;

        function initQrScanner() {
            const readerElement = document.getElementById('qr-reader');
            if (!readerElement || $wire.scanMode !== 'camera') return;

            if (scannerActive && html5QrCode) {
                html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
            } else {
                startScanner();
            }
        }

        function startScanner() {
            const readerElement = document.getElementById('qr-reader');
            if (!readerElement) return;

            html5QrCode = new Html5Qrcode('qr-reader');
            
            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    html5QrCode.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            $wire.call('processQrScan', decodedText);
                        }
                    ).then(() => {
                        scannerActive = true;
                    }).catch((err) => {
                        console.error('Error al iniciar escáner:', err);
                        showNotification('No se pudo acceder a la cámara. Use el modo manual.', 'error');
                    });
                } else {
                    showNotification('No se encontró ninguna cámara. Use el modo manual.', 'error');
                }
            }).catch(() => {
                showNotification('Error al detectar cámaras. Use el modo manual.', 'error');
            });
        }

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
            const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';
            
            const toast = `
                <div class="bs-toast toast toast-placement-ex m-2 ${bgColor} top-0 end-0 fade show" role="alert">
                    <div class="toast-header">
                        <i class="${icon} me-2"></i>
                        <div class="me-auto fw-medium">${type === 'success' ? 'Éxito' : 'Error'}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', toast);
            setTimeout(() => document.querySelector('.toast')?.remove(), 3000);
        }

        function playSound(type) {
            new Audio(`/sounds/${type}.mp3`).play().catch(() => {});
        }

        $wire.on('show-success', (event) => showNotification(event[0], 'success'));
        $wire.on('show-error', (event) => showNotification(event[0], 'error'));
        $wire.on('play-sound', (event) => playSound(event[0]));

        Livewire.hook('morph.updated', () => {
            if ($wire.scanMode === 'camera') {
                setTimeout(initQrScanner, 100);
            } else if (html5QrCode && scannerActive) {
                html5QrCode.stop().then(() => scannerActive = false).catch(() => {});
            }
        });

        setTimeout(initQrScanner, 500);
    </script>
    @endscript
</div>

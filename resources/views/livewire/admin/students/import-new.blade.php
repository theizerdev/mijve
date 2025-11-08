<div>
    <div>
    <div>
        @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="mdi mdi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="mdi mdi-alert-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(!empty($errors) && count($errors) > 0)
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="mdi mdi-alert-circle"></i>
            <ul class="mb-0">
                @foreach($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-1">Importar Estudiantes</h5>
            <p class="mb-0 text-muted">Importación masiva desde Excel o CSV</p>
        </div>
        <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
            <i class="ri ri-arrow-left-line"></i> Volver
        </a>
    </div>

    <!-- Progress Steps -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between position-relative">
                <div class="text-center flex-fill">
                    <div class="step-circle {{ $step >= 1 ? 'active' : '' }}">1</div>
                    <small class="d-block mt-2">Cargar</small>
                </div>
                <div class="step-line {{ $step >= 2 ? 'active' : '' }}"></div>
                <div class="text-center flex-fill">
                    <div class="step-circle {{ $step >= 2 ? 'active' : '' }}">2</div>
                    <small class="d-block mt-2">Preview</small>
                </div>
                <div class="step-line {{ $step >= 3 ? 'active' : '' }}"></div>
                <div class="text-center flex-fill">
                    <div class="step-circle {{ $step >= 3 ? 'active' : '' }}">3</div>
                    <small class="d-block mt-2">Mapeo</small>
                </div>
                <div class="step-line {{ $step >= 4 ? 'active' : '' }}"></div>
                <div class="text-center flex-fill">
                    <div class="step-circle {{ $step >= 4 ? 'active' : '' }}">4</div>
                    <small class="d-block mt-2">Importar</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Step 1: Upload -->
    @if($step === 1)
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="ri ri-upload-cloud-line" style="font-size: 64px; color: #6c757d;"></i>
                <h5 class="mt-3">Selecciona un archivo</h5>
                <p class="text-muted">Excel (.xlsx, .xls) o CSV (.csv)</p>
                
                <input type="file" wire:model="file" class="d-none" id="fileInput" accept=".xlsx,.xls,.csv">
                <label for="fileInput" class="btn btn-primary btn-lg mt-3">
                    <i class="ri ri-file-upload-line"></i> Seleccionar Archivo
                </label>

                @if($file)
                    <div class="alert alert-info mt-3 d-inline-block">
                        <i class="ri ri-file-line"></i> {{ $file->getClientOriginalName() }}
                    </div>
                @endif

                <div wire:loading wire:target="file" class="mt-3">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2">Procesando archivo...</p>
                </div>

                @if(!empty($errors) && count($errors) > 0)
                    <div class="alert alert-danger mt-3">
                        <i class="mdi mdi-alert-circle"></i>
                        @foreach(array_slice($errors, 0, 3) as $error)
                            <div>{{ $error }}</div>
                        @endforeach
                        @if(count($errors) > 3)
                            <div>... y {{ count($errors) - 3 }} errores más</div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Step 2: Preview -->
    @if($step === 2)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Vista Previa - {{ number_format($totalRows) }} filas</h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="updateExisting" id="updateExisting">
                            <label class="form-check-label" for="updateExisting">Actualizar existentes</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="fillMissingWithNA" id="fillMissing">
                            <label class="form-check-label" for="fillMissing">Llenar vacíos con 'n/a'</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Seleccionar todas ({{ count($selectedRows) }}/{{ count($previewData) }})
                            </label>
                        </div>
                    </div>
                </div>

                <div class="table-responsive" style="max-height: 500px;">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light sticky-top">
                            <tr>
                                <th width="50">#</th>
                                <th width="50"><input type="checkbox" wire:model="selectAll" wire:change="toggleSelectAll" class="form-check-input"></th>
                                @foreach($headers as $header)
                                    <th>{{ $header }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($previewData as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td><input type="checkbox" wire:model="selectedRows" value="{{ $index }}" class="form-check-input"></td>
                                    @foreach($row as $cell)
                                        <td>{{ Str::limit($cell, 30) }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 3: Mapping -->
    @if($step === 3)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Mapeo de Columnas</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Datos del Estudiante</h6>
                        @foreach(['nombres' => 'Nombres *', 'apellidos' => 'Apellidos *', 'documento_identidad' => 'Documento *', 'fecha_nacimiento' => 'Fecha Nacimiento', 'grado' => 'Grado', 'seccion' => 'Sección', 'correo_electronico' => 'Correo'] as $field => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <select class="form-select form-select-sm" wire:model="columnMapping.{{ $field }}">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($headers as $index => $header)
                                        <option value="{{ $index }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-md-6">
                        <h6>Datos del Representante</h6>
                        @foreach(['representante_nombres' => 'Nombres', 'representante_apellidos' => 'Apellidos', 'representante_documento_identidad' => 'Documento', 'representante_telefonos' => 'Teléfonos', 'representante_correo' => 'Correo'] as $field => $label)
                            <div class="mb-3">
                                <label class="form-label">{{ $label }}</label>
                                <select class="form-select form-select-sm" wire:model="columnMapping.{{ $field }}">
                                    <option value="">-- Seleccionar --</option>
                                    @foreach($headers as $index => $header)
                                        <option value="{{ $index }}">{{ $header }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Step 4: Import -->
    @if($step === 4)
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Progreso de Importación</h6>
            </div>
            <div class="card-body">
                <div class="progress mb-4" style="height: 30px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: {{ $progress }}%">{{ $progress }}%</div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="card text-center bg-success text-white">
                            <div class="card-body"><h2>{{ $importedCount }}</h2><p class="mb-0">Creados</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center bg-info text-white">
                            <div class="card-body"><h2>{{ $updatedCount }}</h2><p class="mb-0">Actualizados</p></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center bg-danger text-white">
                            <div class="card-body"><h2>{{ $failedCount }}</h2><p class="mb-0">Fallidos</p></div>
                        </div>
                    </div>
                </div>

                @if(count($errors) > 0)
                    <div class="card border-danger mt-3">
                        <div class="card-header bg-danger text-white">Errores ({{ count($errors) }})</div>
                        <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                            @foreach($errors as $error)
                                <div class="alert alert-danger alert-sm mb-2">{{ $error }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($progress >= 100)
                    <div class="text-center mt-4">
                        <div class="alert alert-success"><i class="ri ri-check-circle-line"></i> ¡Importación completada!</div>
                        <a href="{{ route('admin.students.index') }}" class="btn btn-primary">Ver Estudiantes</a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Navigation -->
    <div class="d-flex justify-content-between mt-4">
        <div>
            @if($step > 1 && $step < 4)
                <button type="button" class="btn btn-secondary" wire:click="previousStep">
                    <i class="ri ri-arrow-left-line"></i> Anterior
                </button>
            @endif
        </div>
        <div>
            @if($step === 2)
                <button type="button" class="btn btn-primary" wire:click="nextStep" @if(empty($selectedRows)) disabled @endif>
                    Siguiente <i class="ri ri-arrow-right-line"></i>
                </button>
            @elseif($step === 3)
                <button type="button" class="btn btn-success" wire:click="nextStep">
                    <i class="ri ri-upload-line"></i> Iniciar Importación
                </button>
            @elseif($step === 4 && $progress >= 100)
                <button type="button" class="btn btn-primary" wire:click="resetImport">
                    <i class="ri ri-refresh-line"></i> Nueva Importación
                </button>
            @endif
        </div>
    </div>
    </div>
</div>

<style>
.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #e9ecef;
    color: #6c757d;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
}
.step-circle.active {
    background: #0d6efd;
    color: white;
}
.step-line {
    flex: 1;
    height: 2px;
    background: #e9ecef;
    margin: 0 10px;
    align-self: center;
}
.step-line.active {
    background: #0d6efd;
}
</style>

</div>
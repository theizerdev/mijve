<div>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-0">Importar Estudiantes</h4>
            <p class="text-muted mb-0">Importar estudiantes masivamente desde un archivo CSV o Excel</p>
        </div>
        <div>
            <a href="{{ route('admin.students.index') }}" class="btn btn-secondary">
                <i class="ri ri-arrow-left-line me-1"></i> Volver
            </a>
        </div>
    </div>

    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Seleccionar Archivo</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Archivo CSV o Excel</label>
                        <input type="file" wire:model="file" class="form-control @error('file') is-invalid @enderror" id="file" accept=".csv,.xlsx,.xls">
                        @error('file') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        <div class="form-text">Formatos permitidos: CSV, XLSX, XLS. Tamaño máximo: 2MB.</div>
                    </div>

                    @if($file && empty($preview))
                        <div class="alert alert-info">
                            <i class="ri ri-loader-line me-2"></i> Procesando archivo...
                        </div>
                    @endif

                    @if(!empty($preview))
                        <div class="mt-4">
                            <h6>Vista previa de datos:</h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            @foreach($preview['headers'] as $index => $header)
                                                <th>{{ $header }}</th>
                                            @endforeach
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($preview['rows'] as $row)
                                            <tr>
                                                @foreach($row as $cell)
                                                    <td>{{ $cell }}</td>
                                                @endforeach
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="alert alert-info">
                                Total de registros a importar: <strong>{{ $totalRows }}</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            @if(!empty($preview))
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Mapeo de Columnas</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Asigne las columnas de su archivo a los campos del sistema:</p>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombres *</label>
                                    <select class="form-select" wire:model="columnMapping.nombres">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Apellidos *</label>
                                    <select class="form-select" wire:model="columnMapping.apellidos">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Fecha de Nacimiento</label>
                                    <select class="form-select" wire:model="columnMapping.fecha_nacimiento">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Documento de Identidad</label>
                                    <select class="form-select" wire:model="columnMapping.documento_identidad">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Grado</label>
                                    <select class="form-select" wire:model="columnMapping.grado">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Sección</label>
                                    <select class="form-select" wire:model="columnMapping.seccion">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nivel Educativo</label>
                                    <select class="form-select" wire:model="columnMapping.nivel_educativo">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Turno</label>
                                    <select class="form-select" wire:model="columnMapping.turno">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Período Escolar</label>
                                    <select class="form-select" wire:model="columnMapping.school_period">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Correo Electrónico</label>
                                    <select class="form-select" wire:model="columnMapping.correo_electronico">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Representante - Nombres</label>
                                    <select class="form-select" wire:model="columnMapping.representante_nombres">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Representante - Apellidos</label>
                                    <select class="form-select" wire:model="columnMapping.representante_apellidos">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Representante - Documento</label>
                                    <select class="form-select" wire:model="columnMapping.representante_documento_identidad">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Representante - Teléfonos</label>
                                    <select class="form-select" wire:model="columnMapping.representante_telefonos">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Representante - Correo</label>
                                    <select class="form-select" wire:model="columnMapping.representante_correo">
                                        <option value="">Seleccionar columna</option>
                                        @foreach($preview['headers'] as $index => $header)
                                            <option value="{{ $index }}">{{ $header }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-secondary" wire:click="resetImport">
                                <i class="ri ri-refresh-line me-1"></i> Reiniciar
                            </button>
                            
                            <button type="button" class="btn btn-primary" wire:click="import" @if($importing) disabled @endif>
                                @if($importing)
                                    <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                    Importando...
                                @else
                                    <i class="ri ri-upload-line me-1"></i> Importar Estudiantes
                                @endif
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Instrucciones</h5>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Seleccione un archivo CSV o Excel con los datos de los estudiantes</li>
                        <li>Revise la vista previa de los datos</li>
                        <li>Mapee las columnas del archivo a los campos del sistema</li>
                        <li>Haga clic en "Importar Estudiantes"</li>
                    </ol>
                    
                    <h6>Formato recomendado del archivo:</h6>
                    <ul>
                        <li><strong>Nombres</strong> (requerido)</li>
                        <li><strong>Apellidos</strong> (requerido)</li>
                        <li>Fecha de Nacimiento (formato: YYYY-MM-DD)</li>
                        <li>Documento de Identidad</li>
                        <li>Grado</li>
                        <li>Sección</li>
                        <li>Nivel Educativo (nombre exacto)</li>
                        <li>Turno (nombre exacto)</li>
                        <li>Período Escolar (nombre exacto)</li>
                        <li>Correo Electrónico</li>
                        <li>Representante - Nombres</li>
                        <li>Representante - Apellidos</li>
                        <li>Representante - Documento</li>
                        <li>Representante - Teléfonos (separados por comas)</li>
                        <li>Representante - Correo</li>
                    </ul>
                </div>
            </div>
            
            @if($importing || $imported)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Progreso de Importación</h5>
                    </div>
                    <div class="card-body">
                        @if($importing)
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $totalRows > 0 ? ($importedRows / $totalRows * 100) : 0 }}%"></div>
                            </div>
                        @endif
                        
                        <div class="mt-3">
                            <p>Importados: <strong>{{ $importedRows }}</strong></p>
                            <p>Errores: <strong>{{ $failedRows }}</strong></p>
                            
                            @if(!empty($errorsList))
                                <div class="mt-3">
                                    <h6>Errores:</h6>
                                    <ul class="text-danger">
                                        @foreach($errorsList as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
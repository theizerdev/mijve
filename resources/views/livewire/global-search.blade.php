<div class="position-relative" x-data="{ open: @entangle('showResults') }">
    <input type="text" 
           wire:model.live.debounce.300ms="search" 
           class="form-control" 
           placeholder="Buscar estudiantes, usuarios, empresas..."
           @click.away="$wire.closeResults()">
    
    @if($showResults && count(array_filter($results)) > 0)
    <div class="position-absolute bg-white shadow-lg rounded mt-1 w-100" style="z-index: 1050; max-height: 400px; overflow-y: auto;">
        @if(count($results['students']) > 0)
        <div class="p-2 border-bottom">
            <small class="text-muted fw-bold">ESTUDIANTES</small>
            @foreach($results['students'] as $student)
            <a href="{{ route('admin.students.show', $student) }}" class="d-block p-2 text-decoration-none text-dark hover-bg-light">
                <i class="mdi mdi-account-school"></i> {{ $student->nombres }} {{ $student->apellidos }} - {{ $student->codigo }}
            </a>
            @endforeach
        </div>
        @endif

        @if(count($results['users']) > 0)
        <div class="p-2 border-bottom">
            <small class="text-muted fw-bold">USUARIOS</small>
            @foreach($results['users'] as $user)
            <a href="{{ route('admin.users.edit', $user) }}" class="d-block p-2 text-decoration-none text-dark hover-bg-light">
                <i class="mdi mdi-account"></i> {{ $user->name }} - {{ $user->email }}
            </a>
            @endforeach
        </div>
        @endif

        @if(count($results['empresas']) > 0)
        <div class="p-2 border-bottom">
            <small class="text-muted fw-bold">EMPRESAS</small>
            @foreach($results['empresas'] as $empresa)
            <a href="{{ route('admin.empresas.edit', $empresa) }}" class="d-block p-2 text-decoration-none text-dark hover-bg-light">
                <i class="mdi mdi-office-building"></i> {{ $empresa->razon_social }}
            </a>
            @endforeach
        </div>
        @endif

        @if(count($results['sucursales']) > 0)
        <div class="p-2">
            <small class="text-muted fw-bold">SUCURSALES</small>
            @foreach($results['sucursales'] as $sucursal)
            <a href="{{ route('admin.sucursales.edit', $sucursal) }}" class="d-block p-2 text-decoration-none text-dark hover-bg-light">
                <i class="mdi mdi-store"></i> {{ $sucursal->nombre }}
            </a>
            @endforeach
        </div>
        @endif
    </div>
    @endif
</div>

<style>
.hover-bg-light:hover {
    background-color: #f8f9fa;
}
</style>

# Guía de Uso de Multitenancy

## Trait Multitenantable

El trait `Multitenantable` se aplica automáticamente a todos los modelos que lo usen, EXCEPTO para Super Administradores.

### Modelos que usan Multitenantable:
- Student
- StudentAccessLog
- SchoolPeriod
- EducationalLevel (NivelEducativo)
- Turno

### Modelos con scope personalizado:
- Empresa (usa `scopeForUser`)
- Sucursal (usa `scopeForUser`)
- User (usa `scopeForUser`)

## Uso en Componentes Livewire

### Para modelos con Multitenantable (automático):
```php
// En Index
public function render()
{
    $students = Student::query()
        ->with(['nivelEducativo', 'turno'])
        ->when($this->search, function($query) {
            $query->where('nombres', 'like', '%' . $this->search . '%');
        })
        ->paginate(10);
    
    return view('livewire.admin.students.index', compact('students'));
}

// En Create
public function save()
{
    $this->validate();
    
    Student::create([
        // NO es necesario asignar empresa_id y sucursal_id
        // El trait lo hace automáticamente
        'nombres' => $this->nombres,
        'apellidos' => $this->apellidos,
        // ... otros campos
    ]);
}
```

### Para Empresas:
```php
// En Index
public function render()
{
    $empresas = Empresa::query()
        ->forUser() // Aplicar filtro de usuario
        ->when($this->search, function($query) {
            $query->where('razon_social', 'like', '%' . $this->search . '%');
        })
        ->paginate(10);
    
    return view('livewire.admin.empresas.index', compact('empresas'));
}
```

### Para Sucursales:
```php
// En Index
public function render()
{
    $sucursales = Sucursal::query()
        ->forUser() // Aplicar filtro de usuario
        ->with('empresa')
        ->when($this->search, function($query) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        })
        ->paginate(10);
    
    return view('livewire.admin.sucursales.index', compact('sucursales'));
}

// En Create
public function mount()
{
    // Cargar solo empresas del usuario
    $this->empresas = Empresa::forUser()->where('status', true)->get();
}
```

### Para Usuarios:
```php
// En Index
public function render()
{
    $users = User::query()
        ->forUser() // Aplicar filtro de usuario
        ->with(['empresa', 'sucursal'])
        ->when($this->search, function($query) {
            $query->where('name', 'like', '%' . $this->search . '%');
        })
        ->paginate(10);
    
    return view('livewire.admin.users.index', compact('users'));
}

// En Create
public function mount()
{
    $this->empresas = Empresa::forUser()->where('status', true)->get();
    $this->sucursales = Sucursal::forUser()->where('status', true)->get();
}

public function save()
{
    $this->validate();
    
    User::create([
        'empresa_id' => $this->empresa_id,
        'sucursal_id' => $this->sucursal_id,
        'name' => $this->name,
        'email' => $this->email,
        'password' => Hash::make($this->password),
    ]);
}
```

## Comportamiento por Rol

### Super Administrador:
- Ve TODAS las empresas, sucursales y usuarios
- Ve TODOS los registros de todos los módulos
- NO se aplican filtros automáticos

### Administrador/Recepcionista:
- Ve solo su empresa y sucursal
- Ve solo registros de su empresa/sucursal
- Filtros automáticos aplicados

## Verificación Manual

Si necesitas verificar manualmente el rol:
```php
if (auth()->user()->hasRole('Super Administrador')) {
    // Lógica para super admin
} else {
    // Lógica para usuarios normales
}
```

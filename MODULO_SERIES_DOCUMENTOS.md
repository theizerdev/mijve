# 📄 Módulo de Series de Documentos

## 🎯 Descripción

Módulo para gestionar la numeración de documentos (facturas, boletas, recibos, notas de crédito) de forma configurable por empresa y sucursal.

## 📊 Estructura de Datos

### Tabla: `series`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| tipo_documento | string | factura, boleta, nota_credito, recibo |
| serie | string(10) | Código de la serie (F001, B001, NC01, R001) |
| correlativo_actual | integer | Último número usado |
| longitud_correlativo | integer | Longitud del número (default: 8) |
| activo | boolean | Si la serie está activa |
| empresa_id | bigint | Empresa propietaria |
| sucursal_id | bigint | Sucursal propietaria |

**Índices:**
- Único: (serie, empresa_id, sucursal_id)
- Compuesto: (tipo_documento, empresa_id, sucursal_id, activo)

## ✨ Características

### 1. Gestión de Series
- ✅ Crear series personalizadas por tipo de documento
- ✅ Configurar longitud del correlativo (4-12 dígitos)
- ✅ Activar/desactivar series
- ✅ Múltiples series por tipo de documento
- ✅ Series independientes por empresa/sucursal

### 2. Numeración Automática
- ✅ Incremento automático del correlativo
- ✅ Formato: SERIE-CORRELATIVO (ej: F001-00000001)
- ✅ Padding automático según longitud configurada
- ✅ Thread-safe (usa increment de base de datos)

### 3. Tipos de Documento Soportados

| Tipo | Prefijo Sugerido | Ejemplo |
|------|------------------|---------|
| Factura | F | F001-00000001 |
| Boleta | B | B001-00000001 |
| Nota de Crédito | NC | NC01-00000001 |
| Recibo | R | R001-00000001 |

## 🚀 Uso

### Crear Serie Manualmente

```php
use App\Models\Serie;

Serie::create([
    'tipo_documento' => 'factura',
    'serie' => 'F001',
    'correlativo_actual' => 0,
    'longitud_correlativo' => 8,
    'activo' => true,
    'empresa_id' => 1,
    'sucursal_id' => 1
]);
```

### Obtener Siguiente Número

```php
$serie = Serie::where('tipo_documento', 'recibo')
    ->where('empresa_id', 1)
    ->where('sucursal_id', 1)
    ->where('activo', true)
    ->first();

$numero = $serie->obtenerSiguienteNumero();
// Retorna: "00000001" (incrementa automáticamente)
```

### Integración con Pagos

El modelo `Pago` usa automáticamente las series configuradas:

```php
use App\Services\PagoService;

$pagoService = new PagoService();

// Al crear un pago, busca la serie activa automáticamente
$pago = $pagoService->crearPago([
    'tipo_pago' => 'recibo', // Busca serie activa de tipo 'recibo'
    'fecha' => now(),
    'matricula_id' => 1,
    'empresa_id' => 1,
    'sucursal_id' => 1,
    'detalles' => [...]
]);

// El pago tendrá: serie = "R001", numero = "00000001"
```

## 🖥️ Componentes Livewire

### 1. Index (Listado)
**Ruta:** `/admin/series`

**Características:**
- Listado paginado de series
- Filtro por tipo de documento
- Búsqueda por serie
- Toggle activo/inactivo
- Eliminar serie

### 2. Create (Crear)
**Ruta:** `/admin/series/crear`

**Características:**
- Selección de tipo de documento
- Generación automática de serie sugerida
- Configuración de correlativo inicial
- Configuración de longitud
- Validación de series duplicadas

### 3. Edit (Editar)
**Ruta:** `/admin/series/{serie}/editar`

**Características:**
- Editar tipo de documento
- Cambiar código de serie
- Ajustar correlativo actual
- Modificar longitud
- Activar/desactivar

## 📋 Instalación

### Paso 1: Ejecutar Migraciones
```bash
php artisan migrate
```

### Paso 2: Poblar Series Iniciales
```bash
php artisan db:seed --class=SerieSeeder
```

Esto creará automáticamente 4 series por cada empresa/sucursal:
- F001 (Facturas)
- B001 (Boletas)
- NC01 (Notas de Crédito)
- R001 (Recibos)

## 🔒 Validaciones

1. **Serie única**: No puede haber series duplicadas en la misma empresa/sucursal
2. **Serie activa requerida**: Debe existir al menos una serie activa por tipo para crear pagos
3. **Correlativo positivo**: El correlativo debe ser >= 0
4. **Longitud válida**: Entre 4 y 12 dígitos

## 💡 Casos de Uso

### Caso 1: Múltiples Series por Tipo
Una empresa puede tener varias series del mismo tipo:
- F001 (Facturas normales)
- F002 (Facturas de exportación)
- F003 (Facturas especiales)

Solo una debe estar activa a la vez para uso automático.

### Caso 2: Cambio de Serie Anual
Al iniciar un nuevo año fiscal:
1. Desactivar serie actual (F001)
2. Crear nueva serie (F002) con correlativo en 0
3. Los nuevos pagos usarán F002 automáticamente

### Caso 3: Reiniciar Numeración
Si se necesita reiniciar la numeración:
1. Editar la serie
2. Cambiar `correlativo_actual` a 0
3. Guardar

### Caso 4: Series por Sucursal
Cada sucursal tiene sus propias series independientes:
- Sucursal Lima: F001-00000001
- Sucursal Arequipa: F001-00000001 (diferente numeración)

## 🎨 Interfaz de Usuario

### Vista de Listado
```
┌─────────────────────────────────────────────────────┐
│ Series de Documentos              [+ Nueva Serie]   │
├─────────────────────────────────────────────────────┤
│ Filtros:                                            │
│ [Buscar...] [Tipo: Todos ▼]                        │
├──────┬────────┬────────┬──────────┬────────┬────────┤
│ Tipo │ Serie  │ Actual │ Empresa  │ Estado │ Acción │
├──────┼────────┼────────┼──────────┼────────┼────────┤
│ 📄   │ F001   │ 00125  │ Empresa1 │ ✓      │ ⚙️ 🗑️  │
│ 📄   │ B001   │ 00089  │ Empresa1 │ ✓      │ ⚙️ 🗑️  │
│ 📄   │ NC01   │ 00003  │ Empresa1 │ ✓      │ ⚙️ 🗑️  │
│ 📄   │ R001   │ 01234  │ Empresa1 │ ✓      │ ⚙️ 🗑️  │
└──────┴────────┴────────┴──────────┴────────┴────────┘
```

### Formulario de Creación
```
┌─────────────────────────────────────────┐
│ Nueva Serie de Documentos               │
├─────────────────────────────────────────┤
│ Tipo de Documento: [Recibo ▼]          │
│ Serie: [R001]                           │
│ Correlativo Inicial: [0]                │
│ Longitud: [8]                           │
│ Empresa: [Empresa 1 ▼]                  │
│ Sucursal: [Sucursal Principal ▼]       │
│ ☑ Activo                                │
│                                         │
│ [Cancelar] [Guardar]                    │
└─────────────────────────────────────────┘
```

## 🔧 Métodos del Modelo

### Serie::obtenerSiguienteNumero()
Incrementa el correlativo y retorna el número formateado.

```php
$serie = Serie::find(1);
$numero = $serie->obtenerSiguienteNumero();
// Retorna: "00000001" y actualiza correlativo_actual a 1
```

### Serie::getNumeroCompletoAttribute()
Retorna el formato completo serie-número.

```php
$serie->numero_completo; // "F001-00000125"
```

### Serie::getTiposDocumento()
Retorna array de tipos disponibles.

```php
Serie::getTiposDocumento();
// ['factura' => 'Factura', 'boleta' => 'Boleta', ...]
```

## 📊 Reportes Sugeridos

1. **Uso de Series**: Cantidad de documentos por serie
2. **Series Activas**: Listado de series en uso
3. **Proyección**: Estimación de cuándo se agotará una serie
4. **Auditoría**: Historial de cambios en series

## ⚠️ Consideraciones

1. **No eliminar series con documentos**: Verificar que no existan pagos antes de eliminar
2. **Backup antes de cambios**: Respaldar antes de modificar correlativos
3. **Una serie activa por tipo**: Para evitar confusión en la numeración automática
4. **Longitud suficiente**: Calcular longitud según volumen esperado

## 🔗 Archivos Relacionados

- **Migración**: `database/migrations/2025_11_04_000000_create_series_table.php`
- **Modelo**: `app/Models/Serie.php`
- **Componentes**: `app/Livewire/Admin/Series/`
- **Seeder**: `database/seeders/SerieSeeder.php`
- **Rutas**: `routes/admin.php`

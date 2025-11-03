# Sistema de Pagos Mejorado

## 📋 Estructura de Datos

### Tabla: `pagos` (Cabecera)
Representa el documento de pago (factura, boleta, recibo, nota de crédito)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| serie | string(10) | Serie del documento (F001, B001, NC01, R001) |
| numero | string(20) | Número correlativo (00000001) |
| tipo_pago | string | factura, boleta, nota_credito, recibo |
| fecha | date | Fecha de emisión |
| matricula_id | bigint | Matrícula asociada |
| user_id | bigint | Usuario que registra |
| subtotal | decimal(10,2) | Suma de detalles |
| descuento | decimal(10,2) | Descuento aplicado |
| total | decimal(10,2) | Total a pagar (subtotal - descuento) |
| metodo_pago | string | efectivo, transferencia, tarjeta |
| referencia | string | Número de operación/transacción |
| estado | enum | pendiente, aprobado, cancelado |
| observaciones | text | Notas adicionales |
| empresa_id | bigint | Empresa |
| sucursal_id | bigint | Sucursal |

**Índices:**
- Único: (serie, numero, empresa_id, sucursal_id)
- Compuesto: (empresa_id, sucursal_id, fecha)
- Compuesto: (matricula_id, estado)

### Tabla: `pago_detalles` (Líneas de detalle)
Cada línea del pago con su concepto

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | bigint | ID único |
| pago_id | bigint | Pago al que pertenece |
| concepto_pago_id | bigint | Concepto (Mensualidad, Matrícula, etc.) |
| payment_schedule_id | bigint | Cuota del cronograma (opcional) |
| descripcion | string | Descripción del ítem |
| cantidad | decimal(10,2) | Cantidad (default: 1) |
| precio_unitario | decimal(10,2) | Precio por unidad |
| subtotal | decimal(10,2) | cantidad × precio_unitario (calculado) |

### Tabla: `payment_schedules` (Mejorada)
Cronograma de pagos con tracking

| Campo | Tipo | Descripción |
|-------|------|-------------|
| monto_pagado | decimal(10,2) | Monto acumulado pagado |
| fecha_pago | date | Fecha del último pago |

## 🔄 Flujo de Trabajo

### 1. Crear Pago Manual
```php
$pagoService = new PagoService();

$pago = $pagoService->crearPago([
    'tipo_pago' => 'recibo',
    'fecha' => now(),
    'matricula_id' => 1,
    'metodo_pago' => 'efectivo',
    'descuento' => 0,
    'empresa_id' => 1,
    'sucursal_id' => 1,
    'detalles' => [
        [
            'concepto_pago_id' => 1, // Mensualidad
            'descripcion' => 'Mensualidad Enero 2025',
            'cantidad' => 1,
            'precio_unitario' => 500.00
        ],
        [
            'concepto_pago_id' => 3, // Material
            'descripcion' => 'Libros',
            'cantidad' => 2,
            'precio_unitario' => 50.00
        ]
    ]
]);
```

### 2. Pagar Cuota del Cronograma
```php
$pago = $pagoService->pagarCuota($scheduleId, [
    'tipo_pago' => 'recibo',
    'fecha' => now(),
    'metodo_pago' => 'transferencia',
    'referencia' => 'OP-123456',
    'monto' => 500.00 // Opcional, usa saldo pendiente si no se especifica
]);
```

### 3. Aprobar/Cancelar Pago
```php
// Aprobar
$pagoService->aprobarPago($pago);

// Cancelar (revierte pagos en cronograma)
$pagoService->cancelarPago($pago, 'Error en el monto');
```

## 📊 Características

### ✅ Numeración Automática
- Serie según tipo: F001 (Factura), B001 (Boleta), NC01 (Nota Crédito), R001 (Recibo)
- Número correlativo por serie/empresa/sucursal
- Formato: F001-00000001

### ✅ Cálculo Automático
- Subtotal = Σ(cantidad × precio_unitario)
- Total = Subtotal - Descuento
- Se recalcula al guardar detalles

### ✅ Integración con Cronograma
- Los detalles pueden vincularse a cuotas (payment_schedules)
- Actualiza automáticamente monto_pagado en la cuota
- Marca cuota como "pagado" cuando monto_pagado >= monto

### ✅ Multitenancy
- Trait Multitenantable en Pago
- Numeración independiente por empresa/sucursal

### ✅ Auditoría
- SoftDeletes en pagos
- Tracking de usuario que registra
- Observaciones para notas

## 🎯 Conceptos de Pago Predefinidos

1. Mensualidad
2. Matrícula
3. Material Didáctico
4. Uniforme
5. Seguro Escolar
6. Actividades Extracurriculares
7. Transporte
8. Alimentación
9. Mora
10. Otros

## 🚀 Migración

### Ejecutar migración:
```bash
php artisan migrate
```

### Poblar conceptos:
```bash
php artisan db:seed --class=ConceptoPagoMejoradoSeeder
```

### Migrar datos antiguos (opcional):
Los datos antiguos quedan en `pagos_old` para referencia.
Crear script de migración según necesidad.

## 📝 Ejemplos de Uso

### Pago de Mensualidad desde Cronograma
```php
// En el componente Livewire
public function pagarCuota($scheduleId)
{
    $pagoService = new PagoService();
    
    $pago = $pagoService->pagarCuota($scheduleId, [
        'tipo_pago' => 'recibo',
        'metodo_pago' => $this->metodo_pago,
        'referencia' => $this->referencia
    ]);
    
    session()->flash('message', "Pago registrado: {$pago->numero_completo}");
}
```

### Pago Mixto (Varios Conceptos)
```php
$pago = $pagoService->crearPago([
    'tipo_pago' => 'factura',
    'fecha' => now(),
    'matricula_id' => 1,
    'metodo_pago' => 'tarjeta',
    'descuento' => 50,
    'empresa_id' => 1,
    'sucursal_id' => 1,
    'detalles' => [
        [
            'concepto_pago_id' => 1,
            'payment_schedule_id' => 5, // Vinculado a cuota
            'descripcion' => 'Mensualidad Marzo',
            'cantidad' => 1,
            'precio_unitario' => 500
        ],
        [
            'concepto_pago_id' => 3,
            'descripcion' => 'Libros de texto',
            'cantidad' => 3,
            'precio_unitario' => 80
        ]
    ]
]);
```

### Consultar Pagos
```php
// Pagos de una matrícula
$pagos = Pago::where('matricula_id', 1)
    ->with(['detalles.conceptoPago'])
    ->get();

// Pagos aprobados del mes
$pagos = Pago::where('estado', 'aprobado')
    ->whereMonth('fecha', now()->month)
    ->get();

// Total recaudado
$total = Pago::where('estado', 'aprobado')
    ->sum('total');
```

## 🔐 Validaciones

- Serie y número únicos por empresa/sucursal
- Total debe ser >= 0
- Detalles requeridos (mínimo 1)
- Concepto de pago debe estar activo
- Matrícula debe existir y pertenecer a la empresa/sucursal

## 📈 Reportes Sugeridos

1. **Ingresos por período**: Suma de pagos aprobados
2. **Pagos pendientes**: Estado = pendiente
3. **Pagos por concepto**: Group by concepto_pago_id
4. **Morosidad**: Cuotas vencidas sin pagar
5. **Métodos de pago**: Group by metodo_pago

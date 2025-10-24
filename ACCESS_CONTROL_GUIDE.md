# Sistema de Control de Acceso con QR - Guía de Uso

## Descripción
Este sistema permite el control de acceso de estudiantes mediante código QR, con soporte para escaneo por cámara y entrada manual de códigos.

## Características Principales

### 📱 Escáner QR
- **Modo Cámara**: Escaneo directo con la cámara del dispositivo
- **Modo Manual**: Entrada manual de códigos para casos especiales
- **Sonidos de Notificación**: Alertas audibles para éxito/error (se pueden desactivar)
- **Auto-registro**: Registro automático después de escanear

### 📊 Estadísticas en Tiempo Real
- Entradas del día
- Salidas del día
- Total de accesos
- Estudiantes activos

### 👤 Información del Estudiante
- Foto del estudiante
- Datos personales (nombres, documento)
- Información académica (grado, sección, nivel educativo, turno)
- Último acceso registrado
- Validación de edad (indicador si es menor de edad)

### 📝 Registro de Acceso
- **Entrada/Salida**: Botones intuitivos para el tipo de acceso
- **Notas**: Campo opcional para agregar observaciones
- **Validación**: No permite registros duplicados del mismo tipo en un día
- **Registro Rápido**: Botón para registro express con notas predefinidas

### 📋 Historial de Accesos
- Registros recientes del día
- Búsqueda y filtrado
- Información detallada de cada acceso
- Opción de eliminar registros (solo administradores)

## Cómo Usar el Sistema

### 1. Acceso al Sistema
```
URL: http://127.0.0.1:8000/admin/access/students
```

### 2. Escaneo de Códigos QR

#### Modo Cámara:
1. Hacer clic en "Iniciar Escáner"
2. Apuntar la cámara al código QR del estudiante
3. El sistema automáticamente:
   - Buscará al estudiante
   - Mostrará su información
   - Registrará el acceso (entrada/salida)

#### Modo Manual:
1. Seleccionar "Manual" en el toggle superior
2. Ingresar el código del estudiante
3. Presionar Enter o hacer clic en el botón de búsqueda

### 3. Registro de Acceso

#### Opción Rápida:
- Después de escanear, usar el botón "Registrar [Entrada/Salida]"
- El sistema automáticamente determina si debe registrar entrada o salida

#### Opción Detallada:
1. Seleccionar el tipo de acceso (Entrada/Salida)
2. Agregar notas opcionales
3. Hacer clic en "Registrar Acceso"

### 4. Gestión de Registros

#### Ver Historial:
- Los registros del día aparecen en la parte inferior
- Se actualizan automáticamente después de cada registro

#### Eliminar Registros:
- Solo usuarios con rol de administrador pueden eliminar
- Hacer clic en el botón de eliminar (🗑️) junto al registro
- Confirmar la acción

## Validaciones del Sistema

### Prevención de Duplicados
- No se permite registrar dos entradas o dos salidas el mismo día
- El sistema sugerirá automáticamente el tipo de acceso correcto

### Validación de Estudiantes
- Solo estudiantes activos pueden registrar accesos
- Se valida que el código QR corresponda a un estudiante existente

### Seguridad
- Solo usuarios autenticados pueden acceder al sistema
- Los registros incluyen información de quién los creó
- Auditoría completa de todos los accesos

## Solución de Problemas

### Cámara No Funciona
1. Verificar permisos del navegador para usar la cámara
2. Asegurarse de usar HTTPS (la cámara requiere conexión segura)
3. Probar con otro navegador
4. Usar el modo manual como alternativa

### Código QR No Se Lee
1. Verificar que el código QR esté bien impreso
2. Asegurar buena iluminación
3. Mantener el código estable frente a la cámara
4. Verificar que el código corresponda al formato esperado

### Estudiante No Encontrado
1. Verificar que el código esté correcto
2. Confirmar que el estudiante esté registrado en el sistema
3. Revisar que el estudiante esté activo

## Configuración Adicional

### Sonidos
- Se pueden activar/desactivar con el botón de sonido
- Los sonidos incluyen:
  - Beep de éxito al escanear
  - Alerta de error
  - Notificaciones de registro

### Modo de Visualización
- Interfaz responsive para tablets y móviles
- Optimizado para uso en tablets en modo quiosco
- Botones grandes para fácil interacción táctil

## Soporte Técnico

Para problemas técnicos o dudas sobre el funcionamiento:
1. Verificar la consola del navegador (F12) para errores
2. Asegurar que todos los archivos estén correctamente cargados
3. Verificar que el servidor tenga los permisos adecuados
4. Contactar al administrador del sistema

## Mejores Prácticas

### Para el Personal de Acceso:
1. Siempre verificar la identidad del estudiante
2. Usar el modo manual como respaldo si el QR falla
3. Agregar notas para situaciones especiales
4. Revisar el historial al final del turno

### Para Administradores:
1. Monitorear los registros diariamente
2. Verificar que los códigos QR estén en buen estado
3. Mantener actualizada la información de los estudiantes
4. Realizar respaldos regulares de la base de datos

# Configuración de Notificaciones Automáticas

## Descripción
El sistema de notificaciones automáticas permite generar alertas de forma programada sin intervención manual.

## Funcionalidades Automáticas

### 1. Verificación de Pagos Pendientes
- **Frecuencia**: Diaria
- **Condición**: Pagos con más de 7 días de antigüedad
- **Notificación**: Recordatorio de pago pendiente con monto y días transcurridos

### 2. Alertas de Deudas Altas
- **Frecuencia**: Diaria
- **Condición**: Deuda total mayor a $1,000
- **Notificación**: Alerta de deuda acumulada

### 3. Resumen Semanal
- **Frecuencia**: Domingos
- **Contenido**: Resumen de gastos de la semana
- **Notificación**: Reporte semanal de gastos

## Configuración del Cron

### Opción 1: Cron del Sistema (Recomendado)

1. **Acceder al crontab**:
   ```bash
   crontab -e
   ```

2. **Agregar la línea** (ajustar la ruta según tu servidor):
   ```bash
   # Ejecutar notificaciones automáticas todos los días a las 9:00 AM
   0 9 * * * php /ruta/completa/a/tu/proyecto/system/cron_notificaciones.php
   
   # Ejemplo para Windows con XAMPP:
   0 9 * * * "C:\xampp\php\php.exe" "C:\xampp\htdocs\gasto\system\cron_notificaciones.php"
   ```

### Opción 2: Cron Web (Alternativa)

Si no tienes acceso al crontab del servidor, puedes usar servicios web:

1. **Cron-job.org**:
   - URL: `https://tu-dominio.com/gasto/system/cron_notificaciones.php`
   - Frecuencia: Diaria a las 9:00 AM

2. **EasyCron**:
   - URL: `https://tu-dominio.com/gasto/system/cron_notificaciones.php`
   - Frecuencia: Diaria

### Opción 3: Manual desde la Interfaz

También puedes generar notificaciones manualmente usando el botón con ícono de engranaje (⚙️) en la interfaz principal.

## Verificación del Funcionamiento

### 1. Verificar Logs
El script crea un archivo de log: `system/notificaciones_cron.log`

### 2. Verificar Notificaciones
- Abrir el panel de notificaciones
- Verificar que aparezcan las notificaciones generadas
- Cada usuario solo verá sus propias notificaciones

### 3. Verificar Base de Datos
```sql
-- Ver notificaciones pendientes
SELECT * FROM correos_pendientes WHERE enviado = 0;

-- Ver notificaciones por usuario
SELECT * FROM correos_pendientes WHERE destinatario = 'email@usuario.com' AND enviado = 0;
```

## Personalización

### Cambiar Frecuencias
Editar `system/cron_notificaciones.php`:

```php
// Cambiar límite de días para pagos pendientes
AND DATEDIFF(CURDATE(), dg.fecha) >= 7  // Cambiar 7 por el número deseado

// Cambiar límite de deuda alta
function verificarDeudasAltas($conn, $limite = 1000)  // Cambiar 1000 por el monto deseado

// Cambiar día del resumen semanal
if (date('N') == 7)  // 7 = domingo, cambiar por el día deseado
```

### Agregar Nuevos Tipos de Notificaciones
1. Crear nueva función en `cron_notificaciones.php`
2. Llamar la función en la sección principal
3. Agregar al log de resultados

## Troubleshooting

### Problema: No se generan notificaciones
**Solución**:
1. Verificar permisos del archivo `cron_notificaciones.php`
2. Verificar que PHP tenga acceso a la base de datos
3. Revisar el archivo de log para errores

### Problema: Notificaciones duplicadas
**Solución**:
El sistema ya incluye verificación para evitar duplicados. Si persiste, revisar la lógica de verificación.

### Problema: Usuarios no ven sus notificaciones
**Solución**:
1. Verificar que el email del usuario esté correcto en la base de datos
2. Verificar que las notificaciones tengan `enviado = 0`
3. Verificar que el usuario esté logueado correctamente

## Seguridad

- El script `cron_notificaciones.php` incluye verificaciones de seguridad
- Solo genera notificaciones para usuarios existentes
- No expone información sensible en los logs
- Las notificaciones se filtran por usuario para mantener privacidad 
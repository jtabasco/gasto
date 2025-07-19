# Configuración Global del Sistema

## 🕐 Zona Horaria Centralizada

### Ubicación
La configuración de zona horaria está centralizada en el archivo `conexion.php`:

```php
// Configuración global de zona horaria
if(function_exists('date_default_timezone_set')) {
    date_default_timezone_set('America/Mexico_City');
}
```

### ¿Por qué centralizada?
- ✅ **Consistencia**: Todos los archivos usan la misma zona horaria
- ✅ **Mantenimiento**: Solo un lugar para cambiar la configuración
- ✅ **Rendimiento**: Evita configuraciones duplicadas
- ✅ **Claridad**: Código más limpio y organizado

### Zona Horaria Configurada
- **Zona**: `America/Mexico_City`
- **UTC**: UTC-6 (Horario Central)
- **Cambio de horario**: Sí (DST)

### Archivos Limpiados
Los siguientes archivos ya no necesitan configuración individual de zona horaria:

#### Sistema Principal
- ✅ `system/correos_enviados.php`
- ✅ `system/enviar_correos_pend.php`
- ✅ `system/enviar_correo_deudas_netas.php`
- ✅ `system/cron_gastos_recurrentes.php`
- ✅ `system/cron_notificaciones.php`
- ✅ `system/generar_notificaciones.php`
- ✅ `system/add_Gasto.php`
- ✅ `system/add_Prestamo.php`

#### Archivos AJAX
- ✅ `system/ajax/get_emails.php`
- ✅ `system/ajax/get_email_details.php`
- ✅ `system/ajax/get_email_stats.php`
- ✅ `ajax/load_familias.php`

### Cambiar Zona Horaria
Para cambiar la zona horaria en todo el sistema, solo edita `conexion.php`:

```php
// Cambiar esta línea en conexion.php
date_default_timezone_set('America/Mexico_City');
```

### Zonas Horarias Comunes
- `America/Mexico_City` - México (UTC-6)
- `America/New_York` - Este de EE.UU. (UTC-5)
- `America/Denver` - Montaña EE.UU. (UTC-7)
- `America/Los_Angeles` - Pacífico EE.UU. (UTC-8)
- `America/Guatemala` - Guatemala (UTC-6)

### Verificación
Para verificar que la configuración funciona:

```php
echo date('Y-m-d H:i:s T'); // Mostrará la fecha actual en la zona configurada
```

## 📋 Beneficios de esta Centralización

1. **Mantenimiento Simplificado**: Un solo lugar para cambios
2. **Consistencia Garantizada**: Todos los archivos usan la misma zona
3. **Código Más Limpio**: Menos líneas duplicadas
4. **Menor Riesgo de Errores**: No hay configuraciones inconsistentes
5. **Mejor Organización**: Configuración global en el archivo de conexión

## 🔧 Notas Técnicas

- La configuración se aplica automáticamente cuando se incluye `conexion.php`
- Funciona con `date()`, `time()`, `strtotime()`, etc.
- Compatible con todas las funciones de fecha/hora de PHP
- Incluye verificación de existencia de la función para compatibilidad 
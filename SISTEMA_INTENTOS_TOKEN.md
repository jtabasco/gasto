# Sistema de Intentos para Verificación de Tokens

## Descripción
Se ha implementado un sistema de seguridad que limita a 3 intentos la verificación de códigos de restablecimiento de contraseña antes de requerir un nuevo token.

## Funcionalidades Implementadas

### 1. Control de Intentos
- **Límite**: 3 intentos por token
- **Almacenamiento**: Se agrega una columna `intentos` a la tabla `restablecer`
- **Reset**: Los intentos se resetean a 0 cuando el código es correcto

### 2. Comportamiento del Sistema

#### Cuando el código es correcto:
- Se resetean los intentos a 0
- Se permite continuar al cambio de contraseña
- Se muestra notificación de éxito

#### Cuando el código es incorrecto:
- Se incrementa el contador de intentos
- Se muestra notificación con intentos restantes
- El usuario permanece en la misma pantalla
- Se limpia el campo del código para nuevo intento

#### Cuando se agotan los intentos:
- El formulario cambia dinámicamente mostrando "Intentos Superados"
- Se ocultan los elementos del formulario original
- Se muestran botones para "Solicitar Nuevo Código" o "Volver al Login"
- El usuario puede elegir su próxima acción sin cambiar de página

### 3. Archivos Modificados

#### `system/verificartoken.php`
- Agrega verificación automática de columna `intentos`
- Implementa lógica de control de intentos
- Maneja respuestas AJAX con información de intentos restantes
- Actualiza la interfaz según el estado de intentos

#### `system/confirm.php`
- Modifica el JavaScript para manejar respuestas AJAX
- Mantiene al usuario en la misma pantalla en caso de error
- Muestra notificaciones con iziToast
- Limpia el campo del código después de intento fallido
- **NUEVO**: Cambia dinámicamente el formulario cuando se agotan los intentos
- **NUEVO**: Muestra botones de acción para solicitar nuevo código o volver al login

#### `system/update_correos_table_intentos.sql`
- Script SQL para agregar la columna `intentos` a la tabla `restablecer`

### 4. Base de Datos

#### Tabla `restablecer`
```sql
ALTER TABLE `restablecer` 
ADD COLUMN `intentos` INT DEFAULT 0 AFTER `codigo`;
```

### 5. Flujo de Usuario

1. **Usuario solicita código** → `restablecer.php`
2. **Usuario ingresa código** → `confirm.php`
3. **Sistema verifica código** → `verificartoken.php`
   - Si correcto: Continúa a cambio de contraseña
   - Si incorrecto: Muestra intentos restantes
   - Si agotados: Solicita nuevo código
4. **Cambio de contraseña** → `cambiarpassword.php`

### 6. Notificaciones y Interfaz

El sistema utiliza iziToast para mostrar notificaciones:
- **Éxito**: "Código válido"
- **Error con intentos**: "Código inválido. Te quedan X intentos."
- **Error sin intentos**: "Has agotado tus 3 intentos. Debes solicitar un nuevo código."

**Interfaz de Intentos Superados:**
- Cambio dinámico del formulario con mensaje "Intentos Superados"
- Icono de advertencia en rojo
- Mensaje explicativo sobre el límite de intentos
- Botones de acción: "Solicitar Nuevo Código" y "Volver al Login"

### 7. Seguridad

- Los intentos se almacenan por email en la base de datos
- Se resetean automáticamente al usar un código correcto
- Previene ataques de fuerza bruta
- Mantiene la experiencia de usuario fluida

## Instalación

1. Ejecutar el script SQL para agregar la columna:
```sql
ALTER TABLE `restablecer` 
ADD COLUMN `intentos` INT DEFAULT 0 AFTER `codigo`;
```

2. Los archivos PHP ya incluyen la verificación automática de la columna.

## Compatibilidad

- Compatible con el sistema existente
- No afecta la funcionalidad actual
- Agrega capa de seguridad adicional
- Mantiene la experiencia de usuario existente 
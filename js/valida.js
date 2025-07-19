// Función para debugging que funciona incluso si console no se muestra
function debug(message, type = 'log') {
    // Intentar console.log
    try {
        if (type === 'log') console.log(message);
        else if (type === 'error') console.error(message);
        else if (type === 'warn') console.warn(message);
    } catch (e) {
        // Fallback: mostrar en la página si es posible
        if (typeof showInPage === 'function') {
            showInPage(message);
        }
    }
}

debug('=== VALIDA.JS CARGADO ===');

// Verificar si iziToast está disponible
if (typeof iziToast === 'undefined') {
    debug('ERROR: iziToast no está disponible', 'error');
    alert('Error: La librería de notificaciones no se cargó correctamente');
} else {
    debug('iziToast está disponible');
    
    // Configurar iziToast
    iziToast.settings({
        timeout: 5000,
        resetOnHover: true,
        transitionIn: 'fadeInDown',
        transitionOut: 'fadeOutUp',
        position: 'topRight',
        zindex: 9999,
        close: true,
        closeOnEscape: true,
        closeOnClick: true
    });
    
    debug('iziToast configurado');
}

// Función para mostrar notificaciones
function showNotification(type, title, message) {
    debug('Mostrando notificación: ' + type + ' - ' + title + ' - ' + message);
    
    try {
        if (typeof iziToast === 'undefined') {
            debug('iziToast no disponible, usando alert', 'warn');
            alert(title + ': ' + message);
            return;
        }
        
        if (type === 'success') {
            iziToast.success({
                title: title,
                message: message,
                position: 'topRight',
                zindex: 9999
            });
        } else if (type === 'error') {
            iziToast.error({
                title: title,
                message: message,
                position: 'topRight',
                zindex: 9999
            });
        }
        debug('Notificación enviada correctamente');
    } catch (error) {
        debug('Error al mostrar notificación: ' + error, 'error');
        // Fallback: alert
        alert(title + ': ' + message);
    }
}

debug('Función showNotification definida');

$(document).ready(function() {
    debug('Document ready ejecutado');
    
    $("#frmAcceso").on('submit', function(e) {
        debug('Formulario enviado - evento capturado');
        e.preventDefault();
        
        const logina = $("#username").val();
        const clavea = $("#password").val();

        debug('Formulario enviado: ' + logina + ' - ' + clavea);

        // Validación básica
        if (!logina || !clavea) {
            debug('Campos vacíos, mostrando error...');
            showNotification('error', 'Error', 'Por favor, completa todos los campos');
            return;
        }

        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Iniciando...');
        submitBtn.prop('disabled', true);

        // Usar el archivo real de login
        $.ajax({
            url: "ajax/login.php",
            type: "POST",
            data: {"logina": logina, "clavea": clavea},
            dataType: "text",
            success: function(data) {
                debug('=== LOGIN AJAX ===');
                debug('Respuesta del servidor: ' + data);
                debug('Longitud de la respuesta: ' + data.length);
                debug('Tipo de respuesta: ' + typeof data);
                
                // Restaurar botón
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
                
                // Limpiar la respuesta de espacios y caracteres extra
                const cleanData = data.trim();
                debug('Datos limpios: "' + cleanData + '"');
                
                if (cleanData === "¡Bienvenido!") {
                    debug('Login exitoso, mostrando notificación de éxito');
                    showNotification('success', 'Éxito', 'Iniciando sesión...');
                    
                    setTimeout(() => {
                        $(location).attr("href", "system/index.php");
                    }, 1000);
                } else {
                    debug('Login fallido, mostrando notificación de error');
                    debug('Datos a mostrar: "' + cleanData + '"');
                    
                    // Mostrar error - siempre mostrar notificación de error
                    showNotification('error', 'Error de Acceso', 'Usuario y/o contraseña incorrectos');
                    
                    // Limpiar campo de contraseña
                    $("#password").val('').focus();
                }
            },
            error: function(xhr, status, error) {
                debug('Error en la petición: ' + status + ' - ' + error, 'error');
                debug('Respuesta del servidor: ' + xhr.responseText, 'error');
                
                // Restaurar botón en caso de error de conexión
                submitBtn.html(originalText);
                submitBtn.prop('disabled', false);
                
                showNotification('error', 'Error de Conexión', 'Error de conexión. Verifica tu conexión a internet.');
            }
        });
    });
    
    debug('Evento submit configurado');
});



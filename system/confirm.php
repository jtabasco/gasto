<?php 
if(isset($_GET['email'])){
    $email = $_GET['email'];
} else {
    header('Location: ../index.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Código - GastoApp</title>
    <!-- Bootstrap 5 y Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/css/iziToast.min.css">
    <style>
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-color: #1f2937;
            --light-color: #f9fafb;
            --border-radius: 16px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="50" cy="50" r="1" fill="rgba(255,255,255,0.1)"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
            pointer-events: none;
        }

        .verify-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .verify-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: var(--border-radius);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.2);
            max-width: 450px;
            width: 100%;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        .verify-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color), var(--success-color));
        }

        .brand-section {
            text-align: center;
            margin-bottom: 2rem;
        }

        .brand-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 25px rgba(99, 102, 241, 0.3);
        }

        .brand-icon i {
            font-size: 2.5rem;
            color: white;
        }

        .brand-title {
            font-size: 1.75rem;
            font-weight: 800;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .brand-subtitle {
            color: #6b7280;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 1rem;
        }

        .info-text {
            color: #6b7280;
            font-size: 0.9rem;
            text-align: center;
            margin-bottom: 2rem;
            line-height: 1.5;
        }

        .email-display {
            background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
            border-radius: 12px;
            padding: 1rem;
            margin-bottom: 2rem;
            text-align: center;
            border: 1px solid #d1d5db;
        }

        .email-text {
            color: var(--dark-color);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            position: relative;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.75rem;
            display: block;
            font-size: 0.9rem;
        }

        .input-group {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .input-group:focus-within {
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
            transform: translateY(-2px);
        }

        .input-group-text {
            background: white;
            border: none;
            color: #9ca3af;
            font-size: 1.1rem;
            padding: 0.75rem 1rem;
            transition: color 0.3s ease;
        }

        .input-group:focus-within .input-group-text {
            color: var(--primary-color);
        }

        .form-control {
            border: none;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            background: white;
            transition: all 0.3s ease;
            text-align: center;
            font-weight: 600;
            letter-spacing: 2px;
        }

        .form-control:focus {
            box-shadow: none;
            background: white;
        }

        .btn-verify {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            width: 100%;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .back-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .back-link:hover {
            color: var(--secondary-color);
        }

        .resend-section {
            text-align: center;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid #e5e7eb;
        }

        .resend-text {
            color: #6b7280;
            font-size: 0.85rem;
            margin-bottom: 0.5rem;
        }

        .resend-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .resend-link:hover {
            color: var(--secondary-color);
        }

        /* Floating elements */
        .floating-element {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .floating-element:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .floating-element:nth-child(2) {
            top: 20%;
            right: 10%;
            animation-delay: 2s;
        }

        .floating-element:nth-child(3) {
            bottom: 20%;
            left: 15%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        /* Animaciones */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .verify-card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .verify-container {
                padding: 10px;
            }

            .verify-card {
                padding: 2rem 1.5rem;
            }

            .brand-icon {
                width: 60px;
                height: 60px;
            }

            .brand-icon i {
                font-size: 2rem;
            }

            .brand-title {
                font-size: 1.5rem;
            }

            .brand-subtitle {
                font-size: 0.9rem;
            }

            .info-text {
                font-size: 0.85rem;
            }

            .email-display {
                padding: 0.75rem;
            }

            .email-text {
                font-size: 0.8rem;
            }

            .form-control {
                font-size: 0.9rem;
                letter-spacing: 2px;
            }

            .btn-verify {
                font-size: 1rem;
                padding: 0.875rem;
            }
        }

        /* Loading states */
        .btn-loading {
            pointer-events: none;
            opacity: 0.8;
        }

        .spinner-border-sm {
            width: 1rem;
            height: 1rem;
        }

        /* Estilos para intentos superados */
        .btn-verify {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 12px;
            padding: 1rem;
            font-size: 1.1rem;
            font-weight: 700;
            color: white;
            width: 100%;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-verify:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
            text-decoration: none;
        }

        .btn-verify.btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
        }

        .btn-verify.btn-outline:hover {
            background: var(--primary-color);
            color: white;
        }

        .d-grid.gap-3 {
            display: grid;
            gap: 1rem;
        }
    </style>
</head>

<body>
    <!-- Floating elements -->
    <div class="floating-element">
        <i class="bi bi-shield-check" style="font-size: 3rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-envelope-check" style="font-size: 2.5rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-key-fill" style="font-size: 3rem; color: white;"></i>
    </div>

    <div class="verify-container">
        <div class="verify-card">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="bi bi-shield-check"></i>
                </div>
                <h1 class="brand-title">Verificar Código</h1>
                <p class="brand-subtitle">Confirma tu identidad</p>
            </div>

            <p class="info-text">
                Hemos enviado un código de verificación a tu correo electrónico. 
                Ingresa el código de 4 dígitos para continuar.
            </p>

            <div class="email-display">
                <span class="email-text">
                    <i class="bi bi-envelope me-2"></i>
                    <?php echo htmlspecialchars($email); ?>
                </span>
            </div>

            <form action="verificartoken.php" method="POST" id="verifyForm">
                <div class="form-group">
                    <label for="codigo" class="form-label">
                        <i class="bi bi-key me-2"></i>Código de Verificación
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-key"></i>
                        </span>
                        <input type="text" class="form-control" id="codigo" name="codigo" 
                            placeholder="0000" maxlength="4" pattern="[0-9]{4}" required>
                    </div>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                </div>

                <button type="submit" class="btn btn-verify" id="btnVerify">
                    <span class="texto">
                        <i class="bi bi-check-circle me-2"></i>Verificar Código
                    </span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

            <div class="resend-section">
                <p class="resend-text">¿No recibiste el código?</p>
                <a href="restablecer.php" class="resend-link">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Reenviar código
                </a>
            </div>

            <div class="text-center mt-3">
                <!-- Enlace eliminado para evitar duplicidad con el botón dinámico -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../js/jquery-3.7.0.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/izitoast/1.4.0/js/iziToast.min.js"></script>
    <script>
        // Configurar iziToast
        iziToast.settings({
            timeout: 5000,
            resetOnHover: true,
            transitionIn: 'fadeInDown',
            transitionOut: 'fadeOutUp',
            position: 'topRight'
        });

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('verifyForm');
            const btn = document.getElementById('btnVerify');
            const spinner = btn.querySelector('.spinner-border');
            const textoBtn = btn.querySelector('.texto');
            const codigoInput = document.getElementById('codigo');
            let intentosFallidos = 0;

            // Mostrar mensaje de éxito al cargar
            iziToast.success({
                title: 'Éxito',
                message: 'Código enviado correctamente. Revisa tu email.'
            });

            // Auto-formatear el código (solo números)
            codigoInput.addEventListener('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length > 4) {
                    this.value = this.value.slice(0, 4);
                }
            });

            const toggleLoading = (loading) => {
                spinner.classList.toggle('d-none', !loading);
                textoBtn.innerHTML = loading ? 
                    '<i class="bi bi-hourglass-split me-2"></i>Verificando...' : 
                    '<i class="bi bi-check-circle me-2"></i>Verificar Código';
                btn.disabled = loading;
                btn.classList.toggle('btn-loading', loading);
            };

            const mostrarFormularioIntentosSuperados = () => {
                const verifyCard = document.querySelector('.verify-card');
                const brandSection = document.querySelector('.brand-section');
                const infoText = document.querySelector('.info-text');
                const emailDisplay = document.querySelector('.email-display');
                const formElement = document.getElementById('verifyForm');
                const resendSection = document.querySelector('.resend-section');

                // Cambiar el contenido del card
                brandSection.innerHTML = `
                    <div class="brand-icon" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <h1 class="brand-title">Intentos Superados</h1>
                    <p class="brand-subtitle">Has agotado tus intentos de verificación</p>
                `;

                infoText.innerHTML = `
                    <div style="background: linear-gradient(135deg, #fee2e2, #fecaca); border: 1px solid #fca5a5; border-radius: 12px; padding: 1rem; color: #dc2626; text-align: center;">
                        <i class="bi bi-exclamation-circle me-2"></i>
                        Has superado el límite de 3 intentos fallidos. 
                        Debes solicitar un nuevo código de verificación.
                    </div>
                `;

                // Ocultar elementos del formulario
                emailDisplay.style.display = 'none';
                formElement.style.display = 'none';
                resendSection.style.display = 'none';

                // Agregar botones de acción
                const actionButtons = document.createElement('div');
                actionButtons.className = 'text-center mt-4';
                actionButtons.innerHTML = `
                    <div class="d-grid gap-3">
                        <a href="restablecer.php" class="btn btn-verify">
                            <i class="bi bi-arrow-clockwise me-2"></i>
                            Solicitar Nuevo Código
                        </a>
                        <a href="../index.html" class="btn btn-verify btn-outline">
                            <i class="bi bi-arrow-left me-2"></i>
                            Volver al Login
                        </a>
                    </div>
                `;

                verifyCard.appendChild(actionButtons);
            };

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const codigo = codigoInput.value;
                const email = document.querySelector('input[name="email"]').value;

                if (!codigo || codigo.length !== 4) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Por favor, ingresa un código de 4 dígitos válido'
                    });
                    codigoInput.focus();
                    return;
                }

                toggleLoading(true);

                try {
                    const formData = new FormData();
                    formData.append('codigo', codigo);
                    formData.append('email', email);

                    const response = await fetch('verificartoken.php', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        iziToast.success({
                            title: 'Éxito',
                            message: data.message
                        });
                        setTimeout(() => {
                            window.location.href = 'verificartoken.php?email=' + encodeURIComponent(email) + '&codigo=' + encodeURIComponent(codigo);
                        }, 1500);
                    } else {
                        intentosFallidos++;
                        
                        if (data.message.includes('Has agotado tus 3 intentos')) {
                            // Mostrar formulario de intentos superados
                            setTimeout(() => {
                                mostrarFormularioIntentosSuperados();
                            }, 1000);
                        } else {
                            iziToast.error({
                                title: 'Error',
                                message: data.message
                            });
                            codigoInput.value = '';
                            codigoInput.focus();
                        }
                        toggleLoading(false);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    iziToast.error({
                        title: 'Error',
                        message: 'Error de conexión. Verifica tu conexión a internet.'
                    });
                    toggleLoading(false);
                }
            });

            // Efecto de focus en los inputs
            document.querySelectorAll('.form-control').forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });

            // Auto-focus en el input del código
            codigoInput.focus();
        });
    </script>
</body>

</html>
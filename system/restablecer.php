<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña - GastoApp</title>
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

        .reset-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .reset-card {
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

        .reset-card::before {
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
        }

        .form-control:focus {
            box-shadow: none;
            background: white;
        }

        .btn-reset {
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

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
            color: white;
        }

        .btn-reset:active {
            transform: translateY(0);
        }

        .btn-back {
            background: transparent;
            border: 2px solid var(--primary-color);
            border-radius: 12px;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            font-weight: 600;
            color: var(--primary-color);
            width: 100%;
            transition: all 0.3s ease;
        }

        .btn-back:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
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

        .reset-card {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .reset-container {
                padding: 10px;
            }

            .reset-card {
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

            .form-control {
                font-size: 0.9rem;
            }

            .btn-reset {
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
    </style>
</head>

<body>
    <!-- Floating elements -->
    <div class="floating-element">
        <i class="bi bi-shield-lock" style="font-size: 3rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-envelope" style="font-size: 2.5rem; color: white;"></i>
    </div>
    <div class="floating-element">
        <i class="bi bi-key" style="font-size: 3rem; color: white;"></i>
    </div>

    <div class="reset-container">
        <div class="reset-card">
            <div class="brand-section">
                <div class="brand-icon">
                    <i class="bi bi-shield-lock"></i>
                </div>
                <h1 class="brand-title">Restablecer Contraseña</h1>
                <p class="brand-subtitle">Recupera el acceso a tu cuenta</p>
            </div>

            <p class="info-text">
                Ingresa tu dirección de email y te enviaremos un código de verificación para restablecer tu contraseña.
            </p>

            <form id="restablecerForm">
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="bi bi-envelope me-2"></i>Dirección de Email
                    </label>
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="bi bi-envelope"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" required
                            placeholder="ejemplo@correo.com">
                    </div>
                </div>

                <button type="submit" class="btn btn-reset" id="btnRestablecer">
                    <span class="texto">
                        <i class="bi bi-send me-2"></i>Enviar Código
                    </span>
                    <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="../index.html" class="back-link">
                    <i class="bi bi-arrow-left"></i>
                    Volver al Login
                </a>
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
            const form = document.getElementById('restablecerForm');
            const btn = document.getElementById('btnRestablecer');
            const spinner = btn.querySelector('.spinner-border');
            const textoBtn = btn.querySelector('.texto');

            const toggleLoading = (loading) => {
                spinner.classList.toggle('d-none', !loading);
                textoBtn.innerHTML = loading ? 
                    '<i class="bi bi-hourglass-split me-2"></i>Enviando...' : 
                    '<i class="bi bi-send me-2"></i>Enviar Código';
                btn.disabled = loading;
                btn.classList.toggle('btn-loading', loading);
            };

            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const email = document.getElementById('email').value;
                if (!email) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Por favor, ingresa un correo válido'
                    });
                    return;
                }

                // Validación básica de email
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    iziToast.error({
                        title: 'Error',
                        message: 'Por favor, ingresa un correo válido'
                    });
                    return;
                }

                toggleLoading(true);

                try {
                    const response = await fetch('rest.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `email=${encodeURIComponent(email)}`
                    });

                    const data = await response.text();

                    if (data === 'ok') {
                        iziToast.success({
                            title: 'Éxito',
                            message: 'Código enviado correctamente. Revisa tu email.'
                        });
                        setTimeout(() => {
                            window.location.href = `confirm.php?email=${encodeURIComponent(email)}`;
                        }, 1500);
                    } else if (data === 'noexiste') {
                        iziToast.error({
                            title: 'Error',
                            message: 'No existe ningún usuario con ese correo electrónico'
                        });
                        toggleLoading(false);
                    } else {
                        iziToast.error({
                            title: 'Error',
                            message: 'Error al procesar la solicitud. Intenta nuevamente.'
                        });
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
        });
    </script>
</body>

</html>
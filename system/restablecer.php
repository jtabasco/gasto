<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <?php include "../include/script.php"; ?>
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4">Restablecer Contraseña</h2>
                
                <form id="restablecerForm">
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" required>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnRestablecer">
                        <span class="texto">Restablecer</span>
                        <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('restablecerForm');
        const btn = document.getElementById('btnRestablecer');
        const spinner = btn.querySelector('.spinner-border');
        const textoBtn = btn.querySelector('.texto');

        const toggleLoading = (loading) => {
            spinner.classList.toggle('d-none', !loading);
            textoBtn.textContent = loading ? 'Enviando...' : 'Restablecer';
            btn.disabled = loading;
        };

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const email = document.getElementById('email').value;
            if (!email) {
                toastr.error('Escriba un correo válido');
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
                    window.location.href = `/gasto/system/confirm.php?email=${encodeURIComponent(email)}`;
                } else if (data === 'noexiste') {
                    toastr.error('Ningún usuario con ese correo');
                    toggleLoading(false);
                }
            } catch (error) {
                toastr.error('Error al procesar la solicitud');
                toggleLoading(false);
            }
        });

        // Configuración global de toastr
        toastr.options = {
            positionClass: "toast-bottom-right",
            preventDuplicates: true
        };
    });
    </script>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <!-- INICIO LAYOUT MAIN -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Digisalud') ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS propio -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <!-- DataTables 2 + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.0/datatables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
     <!-- FIN LAYOUT MAIN -->
    <?= $this->renderSection('css') ?>
</head>

<body>
 
    <?= $this->include('partials/header') ?>
    <?= $this->include('partials/sidebar') ?>

    <main class="container-fluid py-4">
        <?php if (session('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?= esc(session('error')) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?= $this->renderSection('content') ?>
    </main>
   <?= $this->include('partials/footer') ?>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tus scripts22 personalizados -->
    <?= $this->renderSection('scripts') ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const formPerfil = document.getElementById('modoEdicion');

            if (!formPerfil || !formPerfil.matches('form[action*="perfil/actualizar"]')) {
                return;
            }

            const emailInput = formPerfil.querySelector('input[name="email"]');
            const submitButtons = formPerfil.querySelectorAll('button[type="submit"]');

            if (!emailInput) {
                return;
            }

            let feedback = emailInput.parentElement.querySelector('.invalid-feedback[data-email-feedback="perfil"]');

            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'invalid-feedback';
                feedback.dataset.emailFeedback = 'perfil';
                emailInput.insertAdjacentElement('afterend', feedback);
            }

            let timer = null;
            let ultimoValorValidado = emailInput.value.trim().toLowerCase();
            let emailValido = true;

            function setEstadoInvalido(mensaje) {
                emailValido = false;
                emailInput.classList.add('is-invalid');
                emailInput.classList.remove('is-valid');
                feedback.textContent = mensaje || 'Correo electrónico no válido.';
                submitButtons.forEach(function (button) {
                    button.disabled = true;
                });
            }

            function setEstadoValido() {
                emailValido = true;
                emailInput.classList.remove('is-invalid');
                emailInput.classList.add('is-valid');
                feedback.textContent = '';
                submitButtons.forEach(function (button) {
                    button.disabled = false;
                });
            }

            function limpiarEstado() {
                emailValido = true;
                emailInput.classList.remove('is-invalid', 'is-valid');
                feedback.textContent = '';
                submitButtons.forEach(function (button) {
                    button.disabled = false;
                });
            }

            function validarEmail() {
                const email = emailInput.value.trim().toLowerCase();

                if (email === '') {
                    limpiarEstado();
                    return;
                }

                if (!emailInput.checkValidity()) {
                    setEstadoInvalido('Ingresa un correo electrónico válido.');
                    return;
                }

                fetch('<?= site_url('perfil/validar-email') ?>?email=' + encodeURIComponent(email), {
                    method: 'GET',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(function (response) {
                        if (!response.ok) {
                            throw new Error('Error al validar el correo.');
                        }

                        return response.json();
                    })
                    .then(function (data) {
                        ultimoValorValidado = email;

                        if (data.valid) {
                            setEstadoValido();
                            return;
                        }

                        setEstadoInvalido(data.message || 'Este correo electrónico ya está registrado.');
                    })
                    .catch(function () {
                        limpiarEstado();
                    });
            }

            emailInput.addEventListener('input', function () {
                clearTimeout(timer);
                limpiarEstado();

                timer = setTimeout(validarEmail, 450);
            });

            emailInput.addEventListener('blur', function () {
                clearTimeout(timer);
                validarEmail();
            });

            formPerfil.addEventListener('submit', function (event) {
                const emailActual = emailInput.value.trim().toLowerCase();

                if (!emailValido || emailInput.classList.contains('is-invalid')) {
                    event.preventDefault();
                    setEstadoInvalido(feedback.textContent || 'Corrige el correo antes de guardar.');
                    return;
                }

                if (emailActual !== ultimoValorValidado && emailInput.checkValidity()) {
                    event.preventDefault();
                    validarEmail();
                }
            });
        });
    </script>
</body>
</html>

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
            function configurarValidacionEmail(options) {
                const form = options.form;

                if (!form) {
                    return;
                }

                const emailInput = form.querySelector('input[name="email"]');
                const submitButtons = form.querySelectorAll('button[type="submit"]');

                if (!emailInput) {
                    return;
                }

                const feedbackHost = options.feedbackHost ? options.feedbackHost(emailInput) : emailInput.parentElement;
                let feedback = feedbackHost.querySelector('.invalid-feedback[data-email-feedback="' + options.scope + '"]');

                if (!feedback) {
                    feedback = document.createElement('div');
                    feedback.className = 'invalid-feedback';
                    feedback.dataset.emailFeedback = options.scope;
                    feedback.style.display = 'none';
                    feedbackHost.insertAdjacentElement('beforeend', feedback);
                }

                let timer = null;
                let ultimoValorValidado = emailInput.value.trim().toLowerCase();
                let emailValido = true;

                function setEstadoInvalido(mensaje) {
                    emailValido = false;
                    emailInput.classList.add('is-invalid');
                    emailInput.classList.remove('is-valid');
                    feedback.textContent = mensaje || 'Correo electrónico no válido.';
                    feedback.style.display = 'block';
                    submitButtons.forEach(function (button) {
                        button.disabled = true;
                    });
                }

                function setEstadoValido() {
                    emailValido = true;
                    emailInput.classList.remove('is-invalid');
                    emailInput.classList.add('is-valid');
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                    submitButtons.forEach(function (button) {
                        button.disabled = false;
                    });
                }

                function limpiarEstado() {
                    emailValido = true;
                    emailInput.classList.remove('is-invalid', 'is-valid');
                    feedback.textContent = '';
                    feedback.style.display = 'none';
                    submitButtons.forEach(function (button) {
                        button.disabled = false;
                    });
                }

                function validarEmail(callback) {
                    const email = emailInput.value.trim().toLowerCase();

                    if (email === '') {
                        limpiarEstado();
                        if (callback) callback(true);
                        return;
                    }

                    if (!emailInput.checkValidity()) {
                        setEstadoInvalido('Ingresa un correo electrónico válido.');
                        if (callback) callback(false);
                        return;
                    }

                    fetch(options.url + '?email=' + encodeURIComponent(email), {
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
                                if (callback) callback(true);
                                return;
                            }

                            setEstadoInvalido(data.message || 'Este correo electrónico ya está registrado.');
                            if (callback) callback(false);
                        })
                        .catch(function () {
                            limpiarEstado();
                            if (callback) callback(true);
                        });
                }

                emailInput.addEventListener('input', function () {
                    clearTimeout(timer);
                    limpiarEstado();

                    timer = setTimeout(function () {
                        validarEmail();
                    }, 450);
                });

                emailInput.addEventListener('blur', function () {
                    clearTimeout(timer);
                    validarEmail();
                });

                form.addEventListener('submit', function (event) {
                    const emailActual = emailInput.value.trim().toLowerCase();

                    if (!emailValido || emailInput.classList.contains('is-invalid')) {
                        event.preventDefault();
                        setEstadoInvalido(feedback.textContent || 'Corrige el correo antes de guardar.');
                        return;
                    }

                    if (emailActual !== ultimoValorValidado && emailInput.checkValidity()) {
                        event.preventDefault();
                        validarEmail(function (valid) {
                            if (valid) {
                                form.submit();
                            }
                        });
                    }
                });
            }

            const formPerfil = document.getElementById('modoEdicion');
            if (formPerfil && formPerfil.matches('form[action*="perfil/actualizar"]')) {
                configurarValidacionEmail({
                    form: formPerfil,
                    scope: 'perfil',
                    url: '<?= site_url('perfil/validar-email') ?>',
                    feedbackHost: function (input) {
                        return input.parentElement;
                    }
                });
            }

            const formOrganizacion = document.querySelector('form[action*="organizaciones/update/"]');
            if (formOrganizacion) {
                const matchOrganizacion = formOrganizacion.getAttribute('action').match(/organizaciones\/update\/(\d+)/);

                if (matchOrganizacion && matchOrganizacion[1]) {
                    configurarValidacionEmail({
                        form: formOrganizacion,
                        scope: 'organizacion',
                        url: '<?= site_url('organizaciones/validar-email') ?>/' + matchOrganizacion[1],
                        feedbackHost: function (input) {
                            return input.closest('.org_edit-form-group') || input.parentElement;
                        }
                    });
                }
            }
        });
    </script>
</body>
</html>

<?= view('templates/head', ['title' => 'Nueva contraseña | Digisalud']) ?>

<script>
    document.body.classList.add('page-login');
</script>

<main class="container-fluid p-0 login-wrapper">
    <div class="row g-0 min-vh-100">
        <section class="col-lg-7 d-none d-lg-flex login-side align-items-center justify-content-center p-5">
            <div class="text-center text-secondary login-side-content">
                <h1 class="display-4 fw-bold mb-3">Nueva contraseña</h1>
                <h2 class="fw-light mb-4">Crea una contraseña segura para continuar.</h2>
                <p class="opacity-75 mb-0">Usa al menos 8 caracteres. Puedes incluir letras, números y símbolos.</p>
            </div>
        </section>

        <section class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-4">
            <div class="login-card">
                <h2 class="fw-bold text-dark mb-2 text-center">Restablecer contraseña</h2>
                <p class="text-muted mb-4 text-center">Ingresa y confirma tu nueva contraseña.</p>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('reset-password') ?>" id="resetPasswordForm" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <input type="hidden" name="token" value="<?= esc($token) ?>">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Nueva contraseña</label>
                        <div class="input-group password-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                id="password"
                                minlength="8"
                                placeholder="Nueva contraseña"
                                required>
                            <span class="input-group-text">
                                <i class="far fa-eye password-toggle" data-toggle-password="password"></i>
                            </span>
                            <div class="invalid-feedback">La contraseña debe tener al menos 8 caracteres.</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-uppercase text-muted">Confirmar contraseña</label>
                        <div class="input-group password-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                class="form-control"
                                name="confirmar_password"
                                id="confirmar_password"
                                placeholder="Confirmar contraseña"
                                required>
                            <span class="input-group-text">
                                <i class="far fa-eye password-toggle" data-toggle-password="confirmar_password"></i>
                            </span>
                            <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-ds-primary">
                            Actualizar contraseña
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a class="ds-link" href="<?= site_url('login') ?>">Volver al inicio de sesión</a>
                </div>
            </div>
        </section>
    </div>
</main>

<footer>
    © Digisalud 2026. Derechos reservados. V2.0.1
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-toggle-password]').forEach(function (icon) {
            icon.addEventListener('click', function () {
                const input = document.getElementById(this.dataset.togglePassword);
                if (!input) return;

                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        });

        const form = document.getElementById('resetPasswordForm');
        const password = document.getElementById('password');
        const confirmar = document.getElementById('confirmar_password');

        form.addEventListener('submit', function (event) {
            if (password.value !== confirmar.value) {
                confirmar.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmar.setCustomValidity('');
            }

            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }

            form.classList.add('was-validated');
        });
    });
</script>
</body>
</html>

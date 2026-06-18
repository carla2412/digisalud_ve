<?= view('templates/head', ['title' => 'Recuperar contraseña | Digisalud']) ?>

<script>
    document.body.classList.add('page-login');
</script>

<main class="container-fluid p-0 login-wrapper">
    <div class="row g-0 min-vh-100">
        <section class="col-lg-7 d-none d-lg-flex login-side align-items-center justify-content-center p-5">
            <div class="text-center text-secondary login-side-content">
                <h1 class="display-4 fw-bold mb-3">Recupera tu acceso</h1>
                <h2 class="fw-light mb-4">Te enviaremos un enlace seguro para crear una nueva contraseña.</h2>
                <p class="opacity-75 mb-0">El enlace tendrá una validez limitada por seguridad.</p>
            </div>
        </section>

        <section class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-4">
            <div class="login-card">
                <h2 class="fw-bold text-dark mb-2 text-center">Recuperar contraseña</h2>
                <p class="text-muted mb-4 text-center">Ingresa el correo asociado a tu cuenta.</p>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success alert-dismissible fade show rounded-3 border-0" role="alert">
                        <?= esc(session()->getFlashdata('success')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0" role="alert">
                        <?= esc(session()->getFlashdata('error')) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="post" action="<?= site_url('recuperar-password') ?>" class="needs-validation" novalidate>
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">Correo electrónico</label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="far fa-envelope"></i>
                            </span>
                            <input
                                type="email"
                                class="form-control"
                                name="email"
                                value="<?= old('email') ?>"
                                placeholder="usuario@ejemplo.com"
                                required>
                            <div class="invalid-feedback">Ingresa un correo electrónico válido.</div>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-ds-primary">
                            Enviar enlace
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
    document.querySelector('form').addEventListener('submit', function (event) {
        if (!this.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }

        this.classList.add('was-validated');
    });
</script>
</body>
</html>

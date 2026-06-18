<?= view('templates/head', ['title' => 'Login | Digisalud']) ?>


<script>
    document.body.classList.add('page-login');
</script>

<main class="container-fluid p-0 login-wrapper">
    <div class="row g-0 min-vh-100">

        <section class="col-lg-7 d-none d-lg-flex login-side align-items-center justify-content-center p-5">
            <div class="text-center text-secondary login-side-content">

                <h1 class="display-4 fw-bold mb-3">Bienvenido!</h1>
                <h2 class="fw-light mb-4">Hacemos de tu experiencia un avance total.</h2>
                <p class="opacity-75 mb-0">
                    Optimiza la atención médica, el registro de beneficiarios y la gestión de jornadas y centros.
                </p>
            </div>
        </section>

        <section class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-4">
            <div class="login-card ">

                <div class="text-center d-lg-none mb-4">

                    <img src="<?= base_url('img/digisalud.png') ?>" alt="DIGISALUD" class="logo">


                </div>

                <h2 class="fw-bold text-dark mb-2 text-center ">Iniciar Sesión</h2>
                <p class="text-muted mb-4"> </p>

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

                <form method="post" action="<?= site_url('auth/attempt') ?>">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase text-muted">
                            Usuario o correo
                        </label>
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="far fa-envelope"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                name="identity"
                                placeholder="Usuario o correo"
                                required
                                autocomplete="off">
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold text-uppercase text-muted">
                                Contraseña
                            </label>
                            <a href="<?= site_url('recuperar-password') ?>" class="ds-link small">¿La olvidaste?</a>
                        </div>

                        <div class="input-group password-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password" class="form-control"  name="password" id="password" placeholder="Contraseña" required autocomplete="off">
                            <span class="input-group-text">
                                <i class="far fa-eye password-toggle" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-ds-primary">
                            Entrar
                            <i class="fas fa-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-5">
                    <p class="text-muted mb-2">Crea una cuenta</p>

                    <div class="d-flex justify-content-center gap-4 flex-wrap">
                        <a class="ds-link" href="<?= site_url('registro/individual') ?>">
                            Individual
                        </a>
                        <a class="ds-link" href="<?= site_url('registro/organizacion') ?>">
                            Organización
                        </a>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-center gap-3 flex-wrap small">
                    <a href="#" target="_blank" class="text-muted text-decoration-none">
                        Políticas de privacidad
                    </a>
                    <span class="text-muted">|</span>
                    <a href="#" target="_blank" class="text-muted text-decoration-none">
                        Condiciones
                    </a>
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
    document.addEventListener('DOMContentLoaded', function() {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }
    });
</script>

</body>

</html>
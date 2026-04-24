<?= view('templates/head', ['title' => 'Login | Digisalud']) ?>

<style>
    :root {
    --ds-primary: #3695f5;
    --ds-primary-dark: #1b7ae2;
    --ds-dark: #004085;
    --ds-light: #f8f9fa;
    --footer-height: 46px;
}

html,
body {
    height: 100%;
    margin: 0;
    overflow: hidden;
     font-family: "Roboto", sans-serif;
    background: #fff;
}

body {
    display: flex;
    flex-direction: column;
}

.login-wrapper {
    height: calc(100vh - var(--footer-height));
    min-height: 0;
    width: 100%;
}

.login-wrapper > .row {
    height: 100%;
    margin: 0;
}

.login-side {
    height: 100%;
    background:
        linear-gradient(rgba(218, 234, 253, 0.82), rgba(178, 212, 249, 0.9)),
        url('/img/uno.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    color: #fff;
}

.login-card {
    width: 100%;
    max-width: 430px;
}

.form-control {
    background-color: #f1f3f5;
    border: 2px solid transparent;
    padding: 12px 15px;
    border-radius: 12px;
}

.form-control:focus {
    background-color: #fff;
    border-color: var(--ds-primary);
    box-shadow: 0 0 0 0.25rem rgba(54, 149, 245, 0.12);
}

.input-group-text {
    background-color: #f1f3f5;
    border: 2px solid transparent;
    border-radius: 12px;
}

.input-group .input-group-text:first-child {
    border-radius: 12px 0 0 12px;
}

.input-group .form-control {
    border-radius: 0 12px 12px 0;
}

.input-group.password-group .form-control {
    border-radius: 0;
}

.input-group.password-group .input-group-text:last-child {
    border-radius: 0 12px 12px 0;
}

.btn-ds-primary {
    background-color: var(--ds-primary);
    color: white;
    border: none;
    padding: 14px;
    border-radius: 12px;
    font-weight: 700;
    transition: all .2s ease;
}

.btn-ds-primary:hover {
    background-color: var(--ds-primary-dark);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(54, 149, 245, 0.3);
}

.ds-link {
    color: var(--ds-primary);
    font-weight: 600;
    text-decoration: none;
}

.ds-link:hover {
    color: var(--ds-primary-dark);
    text-decoration: underline;
}

.password-toggle {
    cursor: pointer;
    color: #6c757d;
}

footer {
    height: var(--footer-height);
    width: 100%;
    text-align: center;
    padding: 0;
    font-size: .8rem;
    color: #fff;
      background: linear-gradient(90deg, rgba(16, 26, 97, 1) 0%, rgba(16, 16, 133, 1) 35%, rgba(0, 212, 255, 1) 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

@media (max-width: 991px) {
    html,
    body {
        overflow: auto;
    }

    .login-wrapper {
        min-height: calc(100vh - var(--footer-height));
        height: auto;
    }

    .login-wrapper > .row {
        min-height: calc(100vh - var(--footer-height));
        height: auto;
    }

    footer {
        height: auto;
        min-height: var(--footer-height);
        padding: 1rem 0;
    }
}
</style>

<main class="container-fluid p-0 login-wrapper">
    <div class="row g-0 min-vh-100">

        <section class="col-lg-7 d-none d-lg-flex login-side align-items-center justify-content-center p-5">
            <div class="text-center text-secondary" style="max-width: 540px;">
                 
                <h1 class="display-4 fw-bold mb-3">Bienvenido!</h1>
                <h2 class="fw-light mb-4">Hacemos de tu experiencia un avance total.</h2>
                <p class="opacity-75 mb-0">
                    Optimiza la atención médica, el registro de beneficiarios y la gestión de jornadas y centros.
                </p>
            </div>
        </section>

        <section class="col-lg-5 d-flex align-items-center justify-content-center bg-white p-4">
            <div class="login-card">

                <div class="text-center d-lg-none mb-4">
                    <i class="fas fa-hand-holding-medical fa-3x mb-3" style="color: var(--ds-primary);"></i>
                    <h2 class="fw-bold mb-0" style="color: var(--ds-primary);">DigiSalud</h2>
                </div>

                <h2 class="fw-bold text-dark mb-2">Iniciar Sesión</h2>
                <p class="text-muted mb-4">Ingresa tus credenciales para acceder al sistema.</p>

                <?php if(session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-3 border-0" role="alert">
                        <?= session()->getFlashdata('error') ?>
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
                                autocomplete="off"
                            >
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <label class="form-label small fw-bold text-uppercase text-muted">
                                Contraseña
                            </label>
                            <a href="#" class="ds-link small">¿La olvidaste?</a>
                        </div>

                        <div class="input-group password-group">
                            <span class="input-group-text">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input
                                type="password"
                                class="form-control"
                                name="password"
                                id="password"
                                placeholder="Contraseña"
                                required
                                autocomplete="off"
                            >
                            <span class="input-group-text">
                                <i class="far fa-eye password-toggle" id="togglePassword"></i>
                            </span>
                        </div>
                    </div>

                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-ds-primary">
                            Entrar al sistema
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
                            Institucional
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
    document.addEventListener('DOMContentLoaded', function () {
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        if (togglePassword && passwordInput) {
            togglePassword.addEventListener('click', function () {
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
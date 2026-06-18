<?= view('templates/head', ['title' => 'Crear Usuario | Digisalud']) ?>
<link rel="stylesheet" href="<?= base_url('css/login.css') ?>">


<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">

             
              <div class="registro-header">
            <a href="<?= base_url('jornadas') ?>" class="back-btn">←</a>

            <div class="header-content">
                <h2 class="fw-bold">Crear Cuenta Individual</h2>
                <p>Datos de Información Personal, Profesional y Acceso</p>
            </div>
        </div>
<br>

            <?php if (session()->getFlashdata('errors')): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php foreach (session()->getFlashdata('errors') as $err): ?>
                            <li><?= esc($err) ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('registro/individual') ?>" id="formUsuario" class="needs-validation" novalidate>
                <?= csrf_field() ?>

                <!-- DATOS PERSONALES -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-secondary">
                            <i class="fas fa-user-circle me-2"></i> Datos Personales
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" value="<?= old('nombres') ?>" placeholder="Ej. Juan" required>
                                <div class="invalid-feedback">Por favor ingresa tus nombres.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?= old('apellidos') ?>" placeholder="Ej. Pérez" required>
                                <div class="invalid-feedback">Por favor ingresa tus apellidos.</div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label d-block">Género *</label>

                                <div class="d-flex gap-3 pt-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="genero" id="generoF" value="F" <?= old('genero') === 'F' ? 'checked' : '' ?> required>
                                        <label class="form-check-label" for="generoF">Femenino</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="genero" id="generoM" value="M" <?= old('genero') === 'M' ? 'checked' : '' ?> required>
                                        <label class="form-check-label" for="generoM">Masculino</label>
                                    </div>
                                </div>

                                <div class="invalid-feedback">Selecciona un género.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="date" class="form-control" id="fechaNacimiento" name="fecha_nacimiento" value="<?= old('fecha_nacimiento') ?>" required>
                                    <div class="invalid-feedback">Selecciona una fecha de nacimiento.</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- INFORMACIÓN PROFESIONAL -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-secondary">
                            <i class="fas fa-briefcase me-2"></i> Información Profesional
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="profesion" class="form-label">Profesión *</label>
                                <select id="profesion" name="profesion" class="form-select" data-selected="<?= esc(old('profesion')) ?>" required></select>
                                <div class="invalid-feedback">Ingresa una profesión válida.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="telefono" class="form-label">Teléfono de Contacto *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" class="form-control" id="telefono" name="telefono" value="<?= old('telefono') ?>" pattern="[0-9+ ]{6,15}" placeholder="412 1234567" required>
                                    <div class="invalid-feedback">Ingresa un número de teléfono válido.</div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- DIRECCIÓN -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-secondary">
                            <i class="fas fa-map-marker-alt me-2"></i> Dirección
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-md-6">
                                <label for="pais" class="form-label">País</label>
                                <input type="text" class="form-control" id="pais" name="pais" value="Venezuela" readonly>
                            </div>

                            <div class="col-md-6">
                                <label for="estado" class="form-label">Estado *</label>
                                <select id="estado" name="estado" class="form-select" data-selected="<?= esc(old('estado')) ?>" required>
                                    <option value="">Selecciona un estado...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="municipio" class="form-label">Municipio *</label>
                                <select id="municipio" name="municipio" class="form-select" data-selected="<?= esc(old('municipio')) ?>" required>
                                    <option value="">Selecciona un municipio...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="parroquia" class="form-label">Parroquia *</label>
                                <select id="parroquia" name="parroquia" class="form-select" data-selected="<?= esc(old('parroquia')) ?>" required>
                                    <option value="">Selecciona una parroquia...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="ciudad" class="form-label">Ciudad o Localidad</label>
                                <input type="text" class="form-control" id="ciudad" name="ciudad" value="<?= old('ciudad') ?>" placeholder="Ej. Acarigua">
                            </div>

                            <div class="col-md-6">
                                <label for="detalle" class="form-label">Detalle opcional</label>
                                <input type="text" class="form-control" id="detalle" name="detalle" value="<?= old('detalle') ?>" placeholder="Calle, edificio, referencia...">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ACCESO -->
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0 text-secondary">
                            <i class="fas fa-lock me-2"></i> Datos de Acceso
                        </h5>
                    </div>

                    <div class="card-body">
                        <div class="row g-3">

                            <div class="col-12">
                                <label for="email" class="form-label">Correo Electrónico *</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-envelope"></i>
                                    </span>
                                    <input type="email" class="form-control" name="email" id="email" value="<?= old('email') ?>" placeholder="usuario@ejemplo.com" required>
                                </div>
                                <div class="invalid-feedback" id="email-feedback">Ingresa un email válido.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="contrasena" class="form-label">Contraseña *</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" minlength="6" required>
                                <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
                            </div>

                            <div class="col-md-6">
                                <label for="confirmarContrasena" class="form-label">Confirmar Contraseña *</label>
                                <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena" required>
                                <div class="invalid-feedback">Las contraseñas no coinciden.</div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- BOTONES -->
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <button type="reset" class="btn btn-outline-secondary px-4">
                        <i class="fas fa-eraser me-2"></i> Limpiar
                    </button>

                    <button type="submit" class="btn btn-2-primary">
                        Guardar <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<footer>
    © Digisalud 2025. Derechos reservados. V2.0.1
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?= base_url('js/profesiones.js') ?>"></script>
<script src="<?= base_url('js/venezuela.js') ?>"></script>

<script>
$(document).ready(function () {

    const $estado = $('#estado');
    const $municipio = $('#municipio');
    const $parroquia = $('#parroquia');
    const $profesion = $('#profesion');

    // Profesiones
    if (Array.isArray(profesiones)) {
        profesiones.forEach(function(p) {
            $profesion.append(new Option(p, p, false, p === $profesion.data('selected')));
        });
    }

    $profesion.select2({
        placeholder: "Escribe tu profesión...",
        allowClear: true,
        width: "100%"
    });

    // Estados
    Object.keys(ubicaciones).forEach(estado => {
        $estado.append(new Option(estado, estado, false, estado === $estado.data('selected')));
    });

    $estado.select2({ placeholder: 'Selecciona un estado', width: '100%' });
    $municipio.select2({ placeholder: 'Selecciona un municipio', width: '100%' });
    $parroquia.select2({ placeholder: 'Selecciona una parroquia', width: '100%' });

    function cargarMunicipios(estadoSeleccionado, municipioActual) {
        const municipios = Object.keys(ubicaciones[estadoSeleccionado] || {});

        $municipio.empty().append(new Option('', ''));
        $parroquia.empty().append(new Option('', ''));

        municipios.forEach(mun => {
            $municipio.append(new Option(mun, mun, false, mun === municipioActual));
        });

        $municipio.trigger('change.select2');
        $parroquia.trigger('change.select2');
    }

    function cargarParroquias(estadoSeleccionado, municipioSeleccionado, parroquiaActual) {
        const parroquias = ubicaciones[estadoSeleccionado]?.[municipioSeleccionado] || [];

        $parroquia.empty().append(new Option('', ''));

        parroquias.forEach(pq => {
            $parroquia.append(new Option(pq, pq, false, pq === parroquiaActual));
        });

        $parroquia.trigger('change.select2');
    }

    $estado.on('change', function() {
        cargarMunicipios(this.value, '');
    });

    $municipio.on('change', function() {
        cargarParroquias($estado.val(), this.value, '');
    });

    const estadoActual = $estado.data('selected');
    const municipioActual = $municipio.data('selected');
    const parroquiaActual = $parroquia.data('selected');

    if (estadoActual) {
        cargarMunicipios(estadoActual, municipioActual);
        if (municipioActual) {
            cargarParroquias(estadoActual, municipioActual, parroquiaActual);
        }
    }
});
</script>

<script>
const form = document.getElementById('formUsuario');
const emailInput = document.getElementById('email');
const emailFeedback = document.getElementById('email-feedback');
const submitButton = form.querySelector('button[type="submit"]');
let emailValido = true;
let ultimoEmailValidado = emailInput.value.trim().toLowerCase();
let emailTimer = null;

function setEmailInvalido(mensaje) {
    emailValido = false;
    emailInput.classList.add('is-invalid');
    emailInput.classList.remove('is-valid');
    emailFeedback.textContent = mensaje || 'Correo electrónico no válido.';
    emailFeedback.style.display = 'block';
    submitButton.disabled = true;
}

function setEmailValido() {
    emailValido = true;
    emailInput.classList.remove('is-invalid');
    emailInput.classList.add('is-valid');
    emailFeedback.textContent = '';
    emailFeedback.style.display = 'none';
    submitButton.disabled = false;
}

function limpiarEmail() {
    emailValido = true;
    emailInput.classList.remove('is-invalid', 'is-valid');
    emailFeedback.textContent = '';
    emailFeedback.style.display = 'none';
    submitButton.disabled = false;
}

function validarEmailRegistro(callback) {
    const email = emailInput.value.trim().toLowerCase();

    if (email === '') {
        limpiarEmail();
        if (callback) callback(true);
        return;
    }

    if (!emailInput.checkValidity()) {
        setEmailInvalido('Ingresa un correo electrónico válido.');
        if (callback) callback(false);
        return;
    }

    fetch('<?= site_url('registro/individual/validar-email') ?>?email=' + encodeURIComponent(email), {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(function(response) {
            if (!response.ok) {
                throw new Error('Error al validar el correo.');
            }
            return response.json();
        })
        .then(function(data) {
            ultimoEmailValidado = email;

            if (data.valid) {
                setEmailValido();
                if (callback) callback(true);
                return;
            }

            setEmailInvalido(data.message || 'Este correo electrónico ya está registrado.');
            if (callback) callback(false);
        })
        .catch(function() {
            limpiarEmail();
            if (callback) callback(true);
        });
}

emailInput.addEventListener('input', function() {
    clearTimeout(emailTimer);
    limpiarEmail();
    emailTimer = setTimeout(validarEmailRegistro, 450);
});

emailInput.addEventListener('blur', function() {
    clearTimeout(emailTimer);
    validarEmailRegistro();
});

form.addEventListener('submit', function(e) {
    const pass = document.getElementById('contrasena');
    const confirm = document.getElementById('confirmarContrasena');
    const emailActual = emailInput.value.trim().toLowerCase();

    if (pass.value !== confirm.value) {
        confirm.setCustomValidity("Las contraseñas no coinciden");
    } else {
        confirm.setCustomValidity("");
    }

    if (!emailValido || emailInput.classList.contains('is-invalid')) {
        e.preventDefault();
        e.stopPropagation();
        setEmailInvalido(emailFeedback.textContent || 'Corrige el correo antes de guardar.');
        form.classList.add('was-validated');
        return;
    }

    if (emailActual !== ultimoEmailValidado && emailInput.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
        validarEmailRegistro(function(valid) {
            if (valid && form.checkValidity()) {
                form.submit();
            } else {
                form.classList.add('was-validated');
            }
        });
        return;
    }

    if (!form.checkValidity()) {
        e.preventDefault();
        e.stopPropagation();
    }

    form.classList.add('was-validated');
});
</script>

<script>
document.getElementById("telefono").addEventListener("input", function(e) {
    let value = e.target.value.replace(/\D/g, "");

    if (value.startsWith("58")) {
        value = value.substring(2);
    }

    let formatted = "+58 ";

    if (value.length > 0) {
        formatted += value.substring(0, 3);
    }

    if (value.length > 3) {
        formatted += " " + value.substring(3, 10);
    }

    e.target.value = formatted;
});
</script>

<?php $flashSuccess = session()->getFlashdata('success'); ?>

<?php if (!empty($flashSuccess)): ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
window.addEventListener('DOMContentLoaded', function () {
    Swal.fire({
        title: "Registro exitoso",
        text: <?= json_encode($flashSuccess) ?>,
        icon: "success",
        confirmButtonText: "Continuar"
    }).then(() => {
        window.location.replace("<?= site_url('login') ?>");
    });
});
</script>
<?php endif; ?>

</body>
</html>

<?= view('templates/head', ['title' => 'Cuenta Institucional | Digisalud']) ?>

<style>
:root {
    --ds-primary: #0d6efd;
    --ds-dark: #11186f;
    --ds-bg: #f5f8fc;
    --ds-border: #e0e6ed;
}

body {
    background: var(--ds-bg);
}

.registro-wrapper {
    max-width: 980px;
    margin: auto;
}

.registro-header {
    text-align: center;
    margin-bottom: 28px;
}

.registro-header h2 {
    font-weight: 800;
    color: var(--ds-primary);
}

.registro-header p {
    color: #6c757d;
    margin-bottom: 12px;
}

.progress {
    height: 10px;
    border-radius: 20px;
}

.card {
    border: 0;
    border-radius: 18px;
    box-shadow: 0 8px 25px rgba(15, 23, 42, 0.08);
    margin-bottom: 24px;
    overflow: hidden;
}

.card-header {
    background: #fff;
    border-bottom: 1px solid #edf1f7;
    padding: 18px 22px;
}

.card-header h5 {
    margin: 0;
    color: #495057;
    font-weight: 700;
}

.card-body {
    padding: 24px;
}

.form-label {
    font-weight: 600;
    color: #344054;
    font-size: 0.92rem;
}

.form-control,
.form-select,
.input-group-text,
.select2-container--default .select2-selection--single {
    border-radius: 12px !important;
    border: 1px solid var(--ds-border) !important;
    min-height: 45px;
}

.form-control,
.form-select,
.input-group-text {
    padding: 0.65rem 1rem;
}

.form-control:focus,
.form-select:focus {
    border-color: var(--ds-primary);
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.10);
}

.input-group-text {
    background: #f8fafc;
    color: var(--ds-primary);
}

 

.btn-outline-secondary {
    border-radius: 14px;
    font-weight: 700;
    padding: 12px 28px;
}

.select2-container {
    width: 100% !important;
}

.select2-selection__rendered {
    line-height: 43px !important;
    padding-left: 1rem !important;
}

.select2-selection__arrow {
    height: 43px !important;
}

.section-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: rgba(13, 110, 253, 0.1);
    color: var(--ds-primary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}

footer {
    text-align: center;
    color: #6c757d;
    padding: 24px;
}
</style>

<div class="container py-5">
    <div class="registro-wrapper">

        <div class="registro-header">
            <h2>Cuenta Institucional</h2>
            <p>Paso 1 de 2: Datos de la organización, ubicación y persona de contacto</p>

            <div class="progress">
                <div class="progress-bar bg-primary" style="width: 50%;"></div>
            </div>
        </div>

        <form method="post" action="<?= site_url('registro/organizacion') ?>" id="formOrganizacion" class="needs-validation" novalidate>
            <?= csrf_field() ?>

            <!-- DATOS DE LA ORGANIZACIÓN -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <span class="section-icon"><i class="fa-solid fa-building"></i></span>
                        Datos de la Organización
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-8">
                            <label for="nombreOrg" class="form-label">Nombre de la Organización *</label>
                            <input type="text" class="form-control" id="nombreOrg" name="nombreOrg" required>
                            <div class="invalid-feedback">Ingresa el nombre de la organización.</div>
                        </div>

                        <div class="col-md-4">
                            <label for="emailOrg" class="form-label">email Institucional *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                               <input type="email" class="form-control" id="emailOrg" name="email" placeholder="usuario@organizacion.org" required>
                                <div class="invalid-feedback">Ingresa un email válido.</div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label for="tipoOrg" class="form-label">Tipo *</label>
                            <select class="form-select" id="tipoOrg" name="tipoOrg" required>
                                <option value="">Selecciona un tipo...</option>
                                <option>Escolar</option>
                                <option>Comedor</option>
                                <option>Empresa Privada</option>
                                <option>Casa hogar</option>
                                <option>ONG</option>
                                <option>Alcaldía</option>
                                <option>Gobernación</option>
                                <option>Mixto</option>
                                <option>Organismo Público</option>
                            </select>
                            <div class="invalid-feedback">Selecciona el tipo de organización.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="categoriaOrg" class="form-label">Categoría *</label>
                            <select class="form-select" id="categoriaOrg" name="categoriaOrg" required>
                                <option value="">Selecciona una categoría...</option>
                                <option>Pública</option>
                                <option>Privada</option>
                                <option>Social</option>
                                <option>Educativa</option>
                                <option>Salud</option>
                                <option>Comunitaria</option>
                                <option>Otra</option>
                            </select>
                            <div class="invalid-feedback">Selecciona una categoría.</div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <span class="section-icon"><i class="fa-solid fa-location-dot"></i></span>
                        Ubicación
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
                            <select id="estado" name="estado" class="form-select" required>
                                <option value="">Selecciona un estado...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="municipio" class="form-label">Municipio *</label>
                            <select id="municipio" name="municipio" class="form-select" required>
                                <option value="">Selecciona un municipio...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="parroquia" class="form-label">Parroquia *</label>
                            <select id="parroquia" name="parroquia" class="form-select" required>
                                <option value="">Selecciona una parroquia...</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="ciudad" class="form-label">Ciudad o Localidad</label>
                            <input type="text" class="form-control" id="ciudad" name="ciudad">
                        </div>

                        <div class="col-md-6">
                            <label for="detalle" class="form-label">Detalle opcional</label>
                            <input type="text" class="form-control" id="detalle" name="detalle" placeholder="Calle, edificio, referencia...">
                        </div>

                    </div>
                </div>
            </div>

            <!-- PERSONA DE CONTACTO -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <span class="section-icon"><i class="fa-solid fa-user-tie"></i></span>
                        Persona de Contacto
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="nombres" class="form-label">Nombres *</label>
                            <input type="text" class="form-control" id="nombres" name="nombres" required>
                            <div class="invalid-feedback">Ingresa los nombres.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="apellidos" class="form-label">Apellidos *</label>
                            <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            <div class="invalid-feedback">Ingresa los apellidos.</div>
                        </div>
                        <div class="col-md-6">
                            <label for="fecha_nacimiento" class="form-label">Fecha de nacimiento *</label>
                            <input 
                                type="date" 
                                class="form-control" 
                                id="fecha_nacimiento" 
                                name="fecha_nacimiento" 
                                required
                            >
                            <div class="invalid-feedback">Ingresa la fecha de nacimiento.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label d-block">Género *</label>

                            <div class="d-flex gap-3 pt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="genero" id="generoF" value="F" required>
                                    <label class="form-check-label" for="generoF">Femenino</label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="genero" id="generoM" value="M" required>
                                    <label class="form-check-label" for="generoM">Masculino</label>
                                </div>
                            </div>

                            <div class="invalid-feedback">Selecciona un género.</div>
                        </div>

                        <div class="col-md-6">
                            <label for="telefono" class="form-label">Número de Teléfono *</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input type="tel" class="form-control" id="telefono" name="telefono" pattern="[0-9+ ]{6,15}" placeholder="+58 412 1234567" required>
                                <div class="invalid-feedback">Ingresa un teléfono válido.</div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="profesion" class="form-label">Profesión *</label>
                            <select id="profesion" name="profesion" class="form-select" required></select>
                            <div class="invalid-feedback">Ingresa una profesión válida.</div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- ACCESO -->
            <div class="card">
                <div class="card-header">
                    <h5>
                        <span class="section-icon"><i class="fa-solid fa-lock"></i></span>
                        Datos de Acceso
                    </h5>
                </div>

                <div class="card-body">
                    <div class="row g-3">

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

            <div class="d-flex justify-content-between align-items-center mt-4">
                <button type="reset" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-eraser me-2"></i> Limpiar
                </button>

                <button type="submit" class="btn btn-primary shadow">
                    Guardar <i class="fa-solid fa-arrow-right ms-2"></i>
                </button>
            </div>

        </form>
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

    if (Array.isArray(profesiones)) {
        profesiones.forEach(function(p) {
            $profesion.append(new Option(p, p));
        });
    }

    $profesion.select2({
        placeholder: "Escribe tu profesión...",
        allowClear: true,
        width: "100%"
    });

    Object.keys(ubicaciones).forEach(estado => {
        $estado.append(new Option(estado, estado));
    });

    $estado.select2({ placeholder: 'Selecciona un estado', width: '100%' });
    $municipio.select2({ placeholder: 'Selecciona un municipio', width: '100%' });
    $parroquia.select2({ placeholder: 'Selecciona una parroquia', width: '100%' });

    $estado.on('change', function() {
        const estadoSeleccionado = this.value;
        const municipios = Object.keys(ubicaciones[estadoSeleccionado] || {});

        $municipio.empty().append(new Option('', ''));
        $parroquia.empty().append(new Option('', ''));

        municipios.forEach(mun => {
            $municipio.append(new Option(mun, mun));
        });

        $municipio.trigger('change.select2');
        $parroquia.trigger('change.select2');
    });

    $municipio.on('change', function() {
        const estadoSeleccionado = $estado.val();
        const municipioSeleccionado = this.value;
        const parroquias = ubicaciones[estadoSeleccionado]?.[municipioSeleccionado] || [];

        $parroquia.empty().append(new Option('', ''));

        parroquias.forEach(pq => {
            $parroquia.append(new Option(pq, pq));
        });

        $parroquia.trigger('change.select2');
    });

});
</script>

<script>
const form = document.getElementById('formOrganizacion');

form.addEventListener('submit', function(e) {
    const pass = document.getElementById('contrasena');
    const confirm = document.getElementById('confirmarContrasena');

    if (pass.value !== confirm.value) {
        confirm.setCustomValidity("Las contraseñas no coinciden");
    } else {
        confirm.setCustomValidity("");
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
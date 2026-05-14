<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$esEdicion = isset($organizacion) && !empty($organizacion);
$org = $organizacion ?? [];
?>
<style>
 
 
/* Tarjetas con estilo amigable */
.org-form-friendly-card {
    border: none;
    border-radius: var(--ds-card-border-radius);
    transition: box-shadow 0.2s ease-in-out;
}

.org-form-friendly-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
}

.org-form-friendly-card .card-header {
    border-bottom: none;
}

/* Inputs y elementos de formulario */
.form-control, .form-select, .input-group-text {
    border-radius: 8px;
    border-color: #e3e6f0;
}

.form-control:focus, .form-select:focus {
    border-color: #bac8f3;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
}

.input-group-text {
    border-right: none;
}

.input-group .form-control {
    border-left: none;
}

.form-label {
    font-size: 0.9rem;
}

/* Área de Dropzone para el logo */
.org-form-logo-dropzone {
    border-color: #d1d3e2 !important;
    cursor: pointer;
    transition: background-color 0.2s;
}

.org-form-logo-dropzone:hover {
    background-color: #eaecf4 !important;
}

/* Botones redondeados */
.btn.rounded-pill {
    padding-top: 0.6rem;
    padding-bottom: 0.6rem;
    font-weight: 600;
}

/* Utilidades adicionales */
.org-form-fs-7 {
    font-size: 0.8rem;
}

.org-form-cursor-pointer {
    cursor: pointer;
}
</style>
 
 <div class="container-fluid px-4 mt-4">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 flex-nowrap">
                <li class="breadcrumb-item"><a href="<?= base_url('/') ?>">Inicio</a></li>
                <li class="breadcrumb-item"><a href="<?= base_url('organizaciones') ?>">Organizaciones</a></li>
                <li class="breadcrumb-item active text-truncate" aria-current="page">
                    <?= $esEdicion ? 'Editar Organización' : 'Nueva Organización' ?>
                </li>
            </ol>
        </nav>
        <h2 class="h3 font-weight-bold text-gray-800">
            <?= $esEdicion ? 'Editar Organización' : 'Nueva Organización' ?>
        </h2>
    </div>

    <form action="<?= base_url('organizaciones/' . ($accion ?? 'store')) ?>" method="POST" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row">
            <div class="col-lg-8">
                
                <div class="card shadow-sm mb-4 org-form-friendly-card">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h6 class="card-title text-uppercase text-muted fw-bold mb-0">1. Datos Básicos</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="nombre_org" class="form-label fw-bold text-dark">Nombre de la organización <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-building"></i></span>
                                    <input type="text"
                                        class="form-control"
                                        id="nombre_org"
                                        name="nombre_org"
                                        value="<?= old('nombre_org', $org['nombre_org'] ?? '') ?>"
                                        placeholder="Ej: Fundación Salud Para Todos"
                                        required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 mb-3">
                                <label for="tipoOrg" class="form-label">Tipo</label>
                                <select class="form-select" id="tipoOrg" name="tipoOrg" required>
                                    <option value="">Selecciona un tipo...</option>
                                    <option value="Escolar" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Escolar' ? 'selected' : '' ?>>Escolar</option>
                                    <option value="Comedor" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Comedor' ? 'selected' : '' ?>>Comedor</option>
                                    <option value="Empresa Privada" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Empresa Privada' ? 'selected' : '' ?>>Empresa Privada</option>
                                    <option value="Casa hogar" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Casa hogar' ? 'selected' : '' ?>>Casa hogar</option>
                                    <option value="ONG" <?= old('tipoOrg', $org['tipo'] ?? '') === 'ONG' ? 'selected' : '' ?>>ONG</option>
                                    <option value="Alcaldía" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Alcaldía' ? 'selected' : '' ?>>Alcaldía</option>
                                    <option value="Gobernación" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Gobernación' ? 'selected' : '' ?>>Gobernación</option>
                                    <option value="Mixto" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Mixto' ? 'selected' : '' ?>>Mixto</option>
                                    <option value="Organismo Público" <?= old('tipoOrg', $org['tipo'] ?? '') === 'Organismo Público' ? 'selected' : '' ?>>Organismo Público</option>
                                </select>
                                <div class="invalid-feedback">Selecciona el tipo de organización.</div>
                            </div>
                        </div>
                        <div class="row">
                            <!-- Categoría -->
                            <div class="col-12 col-md-6 mb-3">
                                <label for="categoriaOrg" class="form-label">Categoría</label>
                                <select class="form-select" id="categoriaOrg" name="categoriaOrg" required>
                                    <option value="">Selecciona una categoría...</option>
                                    <option value="Alimentación" <?= old('categoriaOrg', $org['categoria'] ?? '') === 'Alimentación' ? 'selected' : '' ?>>Alimentación</option>
                                    <option value="Programa Nutricional" <?= old('categoriaOrg', $org['categoria'] ?? '') === 'Programa Nutricional' ? 'selected' : '' ?>>Programa Nutricional</option>
                                    <option value="Atención Médica" <?= old('categoriaOrg', $org['categoria'] ?? '') === 'Atención Médica' ? 'selected' : '' ?>>Atención Médica</option>
                                    <option value="Voluntariado" <?= old('categoriaOrg', $org['categoria'] ?? '') === 'Voluntariado' ? 'selected' : '' ?>>Voluntariado</option>
                                    <option value="Donante" <?= old('categoriaOrg', $org['categoria'] ?? '') === 'Donante' ? 'selected' : '' ?>>Donante</option>
                                </select>
                                <div class="invalid-feedback">Selecciona una categoría.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="nombre_responsable" class="form-label fw-bold text-dark">Nombre del responsable</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-user"></i></span>
                                    <input type="text"
                                        class="form-control"
                                        id="nombre_responsable"
                                        name="nombre_responsable"
                                        value="<?= old('nombre_responsable', $org['nombre_responsable'] ?? '') ?>"
                                        placeholder="Ej: Juan Pérez">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm mb-4 org-form-friendly-card">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h6 class="card-title text-uppercase text-muted fw-bold mb-0">2. Ubicación y Contacto</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label fw-bold text-dark">Teléfono <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-phone"></i></span>
                                    <input type="text"
                                        class="form-control"
                                        id="telefono"
                                        name="telefono"
                                        value="<?= old('telefono', $org['telefono'] ?? '') ?>"
                                        placeholder="+58 412 0000000"
                                        required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="correo" class="form-label fw-bold text-dark">Correo electrónico <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light text-muted"><i class="fas fa-envelope"></i></span>
                                    <input type="email"
                                        class="form-control"
                                        id="correo"
                                        name="correo"
                                        value="<?= old('correo', $org['correo'] ?? '') ?>"
                                        placeholder="ejemplo@organizacion.org"
                                        required>
                                </div>
                            </div>
                        </div>
                        <!-- ═══ DIRECCIÓN ═══ -->
<div class="org-form-toggle-bar" onclick="toggleSeccion(this,'secDireccion')">
    <i class="bi bi-geo-alt" style="font-size:1.1rem;color:#101a61;"></i>
    <div>
        <div class="org-form-toggle-label">Dirección de residencia</div>
        <div class="org-form-toggle-desc">Opcional — carga automática desde venezuela.js</div>
    </div>
</div>

<div class="org-form-toggle-content" id="secDireccion">
    <input type="hidden" name="direccion_activa" id="hDireccion" value="<?= old('direccion_activa', !empty($org['direccion_id']) ? '1' : '') ?>">

    <div class="row g-3 py-3">
        <div class="col-md-6">
            <label class="form-label">País</label>
            <input type="text" name="pais" class="form-control" value="<?= old('pais', 'Venezuela') ?>" readonly style="background:#f8f9fa;">
        </div>

        <div class="col-md-6">
            <label class="form-label">Estado</label>
            <select id="estado" name="estado" class="form-select">
                <option value="">Selecciona un estado...</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Municipio</label>
            <select id="municipio" name="municipio" class="form-select">
                <option value="">Selecciona un municipio...</option>
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Parroquia / Ciudad</label>
            <select id="parroquia" name="parroquia" class="form-select">
                <option value="">Selecciona...</option>
            </select>
        </div>

        <div class="col-12">
            <label class="form-label">Ciudad o localidad</label>
            <input type="text"
                name="ciudad"
                id="ciudad"
                class="form-control"
                value="<?= old('ciudad', $org['ciudad'] ?? '') ?>"
                placeholder="Se carga automático o escribe manualmente">
        </div>
    </div>
</div>
                    </div>
                </div>

            </div>

            <div class="col-lg-4">
                
                <div class="card shadow-sm mb-4 org-form-friendly-card">
                    <div class="card-header bg-white border-0 pt-4 pb-0">
                        <h6 class="card-title text-uppercase text-muted fw-bold mb-0">3. Identidad Visual</h6>
                    </div>
                    <div class="card-body text-center">
                        <label class="form-label fw-bold text-dark mb-3">Logo de la organización</label>
                        
                        <div class="org-form-logo-dropzone p-4 border border-2 border-dashed rounded-3 bg-light org-form-cursor-pointer" id="logo-dropzone">
                            <div class="dz-message needsclick">
                                <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Arrastra el logo aquí</h5>
                                <span class="text-muted org-form-fs-7">o haz clic para buscar</span>
                            </div>
                            <input type="file" name="logo" id="logo-input" accept=".png, .jpg, .jpeg, .svg" class="d-none">
                            <?php
$logoActual = !empty($org['logo_url']) ? base_url('uploads/logos/' . $org['logo_url']) : '#';
$tieneLogo = !empty($org['logo_url']);
?>

<img id="logo-preview"
     src="<?= $logoActual ?>"
     alt="Previsualización del logo"
     class="img-fluid rounded-circle mt-3 <?= $tieneLogo ? '' : 'd-none' ?>"
     style="width: 100px; height: 100px; object-fit: cover;">
                        </div>
                        
                        <div class="form-text text-muted org-form-fs-7 mt-3">
                            Formatos aceptados: PNG, JPG, JPEG, SVG.<br>
                            Tamaño máximo sugerido: 2MB (100x100px).
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2 d-md-flex justify-content-md-end mb-5">
                    <a href="<?= base_url('organizaciones') ?>" class="btn btn-outline-secondary px-4 me-md-2 rounded-pill">Cancelar</a>
                    <button type="submit" class="btn btn-primary px-5 rounded-pill shadow-sm"><i class="fas fa-save me-2"></i>Guardar</button>
                </div>

            </div>
        </div>

    </form>
</div>
 

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('js/venezuela.js') ?>"></script>
 <script>
    document.getElementById('logo-dropzone').addEventListener('click', function() {
        document.getElementById('logo-input').click();
    });

    document.getElementById('logo-input').addEventListener('change', function(event) {
        const input = event.target;
        const preview = document.getElementById('logo-preview');
        const dzMessage = document.querySelector('.org-form-logo-dropzone .dz-message');

        if (input.files && input.files[0]) {
            const reader = new FileReader();

            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
                dzMessage.classList.add('d-none'); // Oculta el mensaje original
            }

            reader.readAsDataURL(input.files[0]);
        }
    });
</script>
<script>
    const estadoActual = "<?= esc(old('estado', $org['estado'] ?? '')) ?>";
    const municipioActual = "<?= esc(old('municipio', $org['municipio'] ?? '')) ?>";
    const parroquiaActual = "<?= esc(old('parroquia', $org['parroquia'] ?? '')) ?>";
</script>

<script>
$(document).ready(function () {
    const $e = $('#estado'),
          $m = $('#municipio'),
          $p = $('#parroquia');

    if (typeof ubicaciones !== 'undefined') {
        Object.keys(ubicaciones).forEach(function (estado) {
            $e.append(new Option(estado, estado));
        });
    }

    $e.select2({ placeholder: 'Selecciona un estado', width: '100%' });
    $m.select2({ placeholder: 'Selecciona un municipio', width: '100%' });
    $p.select2({ placeholder: 'Selecciona...', width: '100%' });

    function cargarMunicipios(estadoSeleccionado, municipioSeleccionado = '') {
        const municipios = Object.keys(ubicaciones[estadoSeleccionado] || {});
        $m.empty().append(new Option('', ''));
        $p.empty().append(new Option('', ''));

        municipios.forEach(function (mun) {
            $m.append(new Option(mun, mun));
        });

        if (municipioSeleccionado) {
            $m.val(municipioSeleccionado).trigger('change.select2');
        } else {
            $m.trigger('change.select2');
        }
    }

    function cargarParroquias(estadoSeleccionado, municipioSeleccionado, parroquiaSeleccionada = '') {
        const parroquias = ubicaciones[estadoSeleccionado]?.[municipioSeleccionado] || [];
        $p.empty().append(new Option('', ''));

        parroquias.forEach(function (par) {
            $p.append(new Option(par, par));
        });

        if (parroquiaSeleccionada) {
            $p.val(parroquiaSeleccionada).trigger('change.select2');
        } else {
            $p.trigger('change.select2');
        }
    }

    $e.on('change', function () {
        const est = this.value;
        $('#ciudad').val('');
        cargarMunicipios(est);
    });

    $m.on('change', function () {
        const est = $e.val();
        const mun = this.value;
        cargarParroquias(est, mun);

        const parroquias = ubicaciones[est]?.[mun] || [];
        if (parroquias.length > 0) {
            $('#ciudad').val(parroquias[0]);
        } else {
            $('#ciudad').val('');
        }
    });

    $p.on('change', function () {
        if (this.value) {
            $('#ciudad').val(this.value);
        }
    });

    // Cargar valores en modo edición
    if (estadoActual) {
        $e.val(estadoActual).trigger('change.select2');
        cargarMunicipios(estadoActual, municipioActual);

        if (municipioActual) {
            cargarParroquias(estadoActual, municipioActual, parroquiaActual);
        }
    }
});

function toggleSeccion(el, id) {
    const sec = document.getElementById(id);
    sec.style.display = (sec.style.display === 'none' || sec.style.display === '') ? 'block' : 'none';
}
</script>
<?= $this->endSection() ?>
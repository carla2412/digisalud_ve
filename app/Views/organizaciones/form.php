<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- {{--
    Vista: app/Views/organizaciones/form.php
    Usada tanto para crear (organizacion === null) como para editar.
    
    Variables recibidas del controlador:
      $titulo       — string: 'Nueva Organización' | 'Editar Organización'
      $organizacion — array|null: datos actuales (null en creación)
      $accion       — string: 'store' | 'update/{id}'
    
    SUPUESTO DE LAYOUT: ajustar nombre al real del proyecto.
    CSRF: CI4 genera el token automáticamente con csrf_token() / form_open().
    Se usa form HTML manual con el helper csrf_field() para mantener
    enctype="multipart/form-data".
--}} -->

<div class="container-fluid px-4">

  <!--   {{-- Breadcrumb --}} -->
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('dashboard') ?>">Inicio</a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('organizaciones') ?>">Organizaciones</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                <?= esc($titulo) ?>
            </li>
        </ol>
    </nav>

    <h4 class="mb-3">
        <i class="fas fa-building me-2 text-primary"></i>
        <?= esc($titulo) ?>
    </h4>

   <!--  {{-- Errores de validación del servidor --}} -->
    <?php if (session()->getFlashdata('errors') || isset($errors)) : ?>
        <?php $errores = session()->getFlashdata('errors') ?? ($errors ?? []); ?>
        <?php if (! empty($errores)) : ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong><i class="fas fa-exclamation-triangle me-1"></i> Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-1">
                    <?php foreach ($errores as $campo => $mensaje) : ?>
                        <li><?= esc($mensaje) ?></li>
                    <?php endforeach; ?>
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">

         <!--    {{-- ================================================================
                 FORMULARIO — enctype multipart/form-data obligatorio para logo
                 CSRF: csrf_field() inyecta el token oculto de CI4
                 ================================================================ --}} -->
            <form method="POST"
                  action="<?= base_url('organizaciones/' . $accion) ?>"
                  enctype="multipart/form-data"
                  novalidate>

                <?= csrf_field() ?>

                <div class="row g-3">

                 <!--    {{-- Nombre de la organización --}} -->
                    <div class="col-md-8">
                        <label for="nombre_org" class="form-label fw-semibold">
                            Nombre de la organización <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="nombre_org"
                               name="nombre_org"
                               class="form-control <?= session()->getFlashdata('errors') && isset(session()->getFlashdata('errors')['nombre_org']) ? 'is-invalid' : '' ?>"
                               maxlength="120"
                               required
                               value="<?= esc(old('nombre_org', $organizacion['nombre_org'] ?? '')) ?>">
                        <?php $errNombre = session()->getFlashdata('errors')['nombre_org'] ?? null; ?>
                        <?php if ($errNombre) : ?>
                            <div class="invalid-feedback"><?= esc($errNombre) ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- {{-- Tipo --}} -->
                    <div class="col-md-4">
                        <label for="tipo" class="form-label fw-semibold">
                            Tipo <span class="text-danger">*</span>
                        </label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <?php
                            $tipos = ['ONG', 'Fundación', 'Asociación', 'Casa hogar', 'Institución pública', 'Otro'];
                            $selTipo = old('tipo', $organizacion['tipo'] ?? 'ONG');
                            foreach ($tipos as $t) :
                            ?>
                                <option value="<?= esc($t) ?>" <?= $selTipo === $t ? 'selected' : '' ?>>
                                    <?= esc($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                 <!--    {{-- Categoría --}} -->
                    <div class="col-md-6">
                        <label for="categoria" class="form-label fw-semibold">
                            Categoría <span class="text-danger">*</span>
                        </label>
                        <input type="text"
                               id="categoria"
                               name="categoria"
                               class="form-control"
                               maxlength="80"
                               required
                               placeholder="Ej: Alimentación, Salud, Educación..."
                               value="<?= esc(old('categoria', $organizacion['categoria'] ?? '')) ?>">
                    </div>

                <!--     {{-- Nombre del responsable --}} -->
                    <div class="col-md-6">
                        <label for="nombre_responsable" class="form-label fw-semibold">
                            Nombre del responsable
                        </label>
                        <input type="text"
                               id="nombre_responsable"
                               name="nombre_responsable"
                               class="form-control"
                               maxlength="120"
                               value="<?= esc(old('nombre_responsable', $organizacion['nombre_responsable'] ?? '')) ?>">
                    </div>

                    <!-- {{-- Teléfono --}} -->
                    <div class="col-md-4">
                        <label for="telefono" class="form-label fw-semibold">
                            Teléfono <span class="text-danger">*</span>
                        </label>
                        <input type="tel"
                               id="telefono"
                               name="telefono"
                               class="form-control"
                               maxlength="30"
                               required
                               placeholder="+58 412 0000000"
                               value="<?= esc(old('telefono', $organizacion['telefono'] ?? '')) ?>">
                    </div>

                    <!-- {{-- Correo --}} -->
                    <div class="col-md-8">
                        <label for="correo" class="form-label fw-semibold">
                            Correo electrónico <span class="text-danger">*</span>
                        </label>
                        <input type="email"
                               id="correo"
                               name="correo"
                               class="form-control"
                               maxlength="120"
                               required
                               value="<?= esc(old('correo', $organizacion['correo'] ?? '')) ?>">
                    </div>

                    <!-- {{-- direccion_id — campo oculto o selector según implementación --}}
                    {{-- SUPUESTO: se pasa el ID de dirección como campo de texto simple.
                         Si el proyecto tiene un selector de direcciones, reemplazar aquí. --}} -->
                    <div class="col-md-4">
                        <label for="direccion_id" class="form-label fw-semibold">
                            ID de Dirección
                        </label>
                        <input type="number"
                               id="direccion_id"
                               name="direccion_id"
                               class="form-control"
                               min="1"
                               placeholder="Opcional"
                               value="<?= esc(old('direccion_id', $organizacion['direccion_id'] ?? '')) ?>">
                        <div class="form-text">Referencia a la tabla <code>direcciones</code>.</div>
                    </div>

                  <!--   {{-- ============================================================
                         Logo — con previsualización del actual en edición
                         ============================================================ --}} -->
                    <div class="col-md-8">
                        <label for="logo" class="form-label fw-semibold">
                            Logo de la organización
                        </label>

                        {{-- Previsualización del logo actual (solo en edición) --}}
                        <?php if (! empty($organizacion['logo_url'])) : ?>
                            <div class="mb-2" id="logo-preview-actual">
                                <p class="small text-muted mb-1">Logo actual:</p>
                                <img
                                    id="img-logo-actual"
                                    src="<?= base_url('organizaciones/logo/' . esc($organizacion['logo_url'])) ?>"
                                    alt="Logo actual"
                                    class="img-thumbnail"
                                    style="max-width: 150px; max-height: 150px; object-fit: contain;"
                                >
                            </div>
                        <?php endif; ?>

                        <!-- {{-- Preview dinámico antes de guardar --}} -->
                        <div class="mb-2" id="logo-preview-nuevo" style="display:none;">
                            <p class="small text-muted mb-1">Nuevo logo seleccionado:</p>
                            <img
                                id="img-logo-nuevo"
                                src="#"
                                alt="Preview nuevo logo"
                                class="img-thumbnail"
                                style="max-width: 150px; max-height: 150px; object-fit: contain;"
                            >
                        </div>

                        <input type="file"
                               id="logo"
                               name="logo"
                               class="form-control"
                               accept=".png,.jpg,.jpeg,image/png,image/jpeg">
                        <div class="form-text">
                            Formatos aceptados: PNG, JPG, JPEG.
                            <?php if (! empty($organizacion['logo_url'])) : ?>
                                Si no seleccionas un nuevo archivo, se conservará el logo actual.
                            <?php endif; ?>
                        </div>
                    </div>

                </div><!-- {{-- /row --}} -->

                <!-- {{-- Botones --}} -->
                <hr class="my-4">
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>
                        <?= $organizacion ? 'Actualizar' : 'Guardar' ?>
                    </button>
                    <a href="<?= base_url('organizaciones') ?>" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </a>
                </div>

            </form>
        </div>
    </div>

</div>

<!-- {{-- Script: preview dinámico del logo + auto-dismiss alerts --}} -->
<script>
    document.addEventListener('DOMContentLoaded', function () {

        // Preview dinámico del logo seleccionado
        const inputLogo    = document.getElementById('logo');
        const previewNuevo = document.getElementById('logo-preview-nuevo');
        const imgNuevo     = document.getElementById('img-logo-nuevo');

        if (inputLogo) {
            inputLogo.addEventListener('change', function (e) {
                const file = e.target.files[0];

                if (!file) {
                    previewNuevo.style.display = 'none';
                    return;
                }

                // Validar tipo en cliente (segunda barrera visual)
                const tiposPermitidos = ['image/png', 'image/jpeg'];
                if (!tiposPermitidos.includes(file.type)) {
                    alert('Solo se permiten imágenes PNG o JPG.');
                    inputLogo.value = '';
                    previewNuevo.style.display = 'none';
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (ev) {
                    imgNuevo.src       = ev.target.result;
                    previewNuevo.style.display = 'block';
                };
                reader.readAsDataURL(file);
            });
        }

        // Auto-dismiss alerts en 5 segundos
        const alerts = document.querySelectorAll('.alert.auto-dismiss');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>

<?= $this->endSection() ?>
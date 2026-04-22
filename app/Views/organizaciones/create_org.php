<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>
<style>
.create-container { max-width: 900px; margin: 0 auto; }
.breadcrumb-digi { font-size: .82rem; color: #6c757d; margin-bottom: 1rem; }
.breadcrumb-digi a { color: #6c757d; text-decoration: none; }
.breadcrumb-digi a:hover { color: #101a61; }
.breadcrumb-digi .active { font-weight: 600; color: #0b1b3f; }
.create-title { font-size: 1.2rem; font-weight: 600; color: #0b1b3f; margin-bottom: 4px; }
.create-sub { font-size: .82rem; color: #6c757d; margin-bottom: 1.5rem; }
.section-header { display: flex; align-items: center; gap: 8px; font-size: .9rem; font-weight: 600; color: #101a61; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2px solid #00D4FF; }
.toggle-bar { display: flex; align-items: center; gap: 12px; padding: 12px 16px; background: #f8f9fa; border-radius: 10px; cursor: pointer; margin-bottom: 8px; border: 1px solid #e9ecef; user-select: none; transition: border-color .2s; }
.toggle-bar:hover { border-color: #101a61; }
.toggle-bar.active { border-color: #00D4FF; background: #f0f9ff; }
.toggle-bar .toggle-label { font-size: .85rem; font-weight: 600; color: #0b1b3f; }
.toggle-bar .toggle-desc { font-size: .72rem; color: #888; }
.toggle-content { max-height: 0; overflow: hidden; transition: max-height .3s ease; padding: 0 4px; }
.toggle-content.open { max-height: 800px; }
.btn-guardar { background: #101a61; color: #fff; border: none; padding: 12px 28px; border-radius: 10px; font-size: .9rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; }
.btn-guardar:hover { background: #1a2a8a; color: #fff; }
.btn-cancelar { background: transparent; color: #6c757d; border: 1px solid #dee2e6; padding: 12px 24px; border-radius: 10px; font-size: .9rem; text-decoration: none; }
.btn-cancelar:hover { background: #f8f9fa; color: #333; }

.logo-dropzone { border: 2px dashed #d1d3e2; cursor: pointer; transition: background-color .2s, border-color .2s; border-radius: 12px; }
.logo-dropzone:hover { background-color: #eaecf4; border-color: #101a61; }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="create-container px-3 mt-3">

    <!-- Breadcrumb -->
    <nav class="breadcrumb-digi" aria-label="breadcrumb">
        <a href="<?= base_url('/') ?>">Inicio</a> /
        <a href="<?= base_url('organizaciones') ?>">Organizaciones</a> /
        <span class="active">Nueva Organización</span>
    </nav>

    <div class="create-title">Nueva Organización</div>
    <div class="create-sub">Completa los datos para registrar una nueva organización en el sistema.</div>

    <!-- Errores de validación -->
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

    <form action="<?= base_url('organizaciones/guardar') ?>" method="POST" enctype="multipart/form-data" novalidate>
        <?= csrf_field() ?>

        <!-- ═══ DATOS BÁSICOS ═══ -->
        <div class="section-header mt-3">
            <i class="bi bi-building" style="font-size:1.1rem;"></i> Datos Básicos
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-8">
                <label for="nombre_org" class="form-label">Nombre de la organización <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="nombre_org" name="nombre_org"
                       value="<?= old('nombre_org') ?>"
                       placeholder="Ej: Fundación Salud Para Todos" required>
            </div>

            <div class="col-md-4">
                <label for="tipo" class="form-label">Tipo <span class="text-danger">*</span></label>
                <select class="form-select" id="tipo" name="tipo" required>
                    <option value="">Selecciona un tipo...</option>
                    <?php
                    $tipos = ['Escolar','Comedor','Empresa Privada','Casa hogar','ONG','Alcaldía','Gobernación','Mixto','Organismo Público'];
                    foreach ($tipos as $t): ?>
                        <option value="<?= $t ?>" <?= old('tipo') === $t ? 'selected' : '' ?>><?= $t ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="categoria" class="form-label">Categoría <span class="text-danger">*</span></label>
                <select class="form-select" id="categoria" name="categoria" required>
                    <option value="">Selecciona una categoría...</option>
                    <?php
                    $categorias = ['Alimentación','Programa Nutricional','Atención Médica','Voluntariado','Donante'];
                    foreach ($categorias as $c): ?>
                        <option value="<?= $c ?>" <?= old('categoria') === $c ? 'selected' : '' ?>><?= $c ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-6">
                <label for="nombre_responsable" class="form-label">Nombre del responsable</label>
                <input type="text" class="form-control" id="nombre_responsable" name="nombre_responsable"
                       value="<?= old('nombre_responsable') ?>"
                       placeholder="Ej: Juan Pérez">
            </div>
        </div>

        <!-- ═══ CONTACTO ═══ -->
        <div class="section-header mt-4">
            <i class="bi bi-telephone" style="font-size:1.1rem;"></i> Contacto
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="telefono" name="telefono"
                       value="<?= old('telefono') ?>"
                       placeholder="+58 412 0000000" required>
            </div>

            <div class="col-md-6">
                <label for="correo" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="correo" name="correo"
                       value="<?= old('correo') ?>"
                       placeholder="ejemplo@organizacion.org" required>
            </div>
        </div>

        <!-- ═══ DIRECCIÓN ═══ -->
        <div class="toggle-bar mt-3" onclick="toggleSeccion(this,'secDireccion')">
            <i class="bi bi-geo-alt" style="font-size:1.1rem;color:#101a61;"></i>
            <div>
                <div class="toggle-label">Dirección</div>
                <div class="toggle-desc">Opcional — carga automática desde venezuela.js</div>
            </div>
        </div>

        <div class="toggle-content" id="secDireccion">
            <input type="hidden" name="direccion_activa" id="hDireccion" value="<?= old('direccion_activa') ?>">
            <div class="row g-3 py-3">
                <div class="col-md-6">
                    <label class="form-label">País</label>
                    <input type="text" name="pais" class="form-control" value="Venezuela" readonly style="background:#f8f9fa;">
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
                    <label class="form-label">Parroquia</label>
                    <select id="parroquia" name="parroquia" class="form-select">
                        <option value="">Selecciona...</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Ciudad o localidad</label>
                    <input type="text" name="ciudad" id="ciudad" class="form-control"
                           value="<?= old('ciudad') ?>"
                           placeholder="Se carga automático o escribe manualmente">
                </div>
            </div>
        </div>

        <!-- ═══ LOGO ═══ -->
        <div class="toggle-bar mt-3" onclick="toggleSeccion(this,'secLogo')">
            <i class="bi bi-image" style="font-size:1.1rem;color:#101a61;"></i>
            <div>
                <div class="toggle-label">Logo de la organización</div>
                <div class="toggle-desc">Opcional — PNG, JPG o JPEG (máx. 2MB)</div>
            </div>
        </div>

        <div class="toggle-content" id="secLogo">
            <div class="py-3 text-center">
                <div class="logo-dropzone p-4 bg-light mx-auto" id="logo-dropzone" style="max-width:320px;">
                    <div class="dz-message" id="dz-message">
                        <i class="bi bi-cloud-arrow-up" style="font-size:2.5rem; color:#aaa;"></i>
                        <p class="text-muted mb-0 mt-2" style="font-size:.85rem;">Arrastra el logo aquí o haz clic para buscar</p>
                    </div>
                    <input type="file" name="logo" id="logo-input" accept=".png,.jpg,.jpeg" class="d-none">
                    <img id="logo-preview" src="#" alt="Previsualización"
                         class="img-fluid rounded-circle mt-3 d-none"
                         style="width:100px; height:100px; object-fit:cover;">
                </div>
                <div class="text-muted mt-2" style="font-size:.75rem;">Formatos: PNG, JPG, JPEG — Tamaño sugerido: 100×100px</div>
            </div>
        </div>

        <!-- ═══ BOTONES ═══ -->
        <div class="d-flex justify-content-between align-items-center mt-4 mb-5">
            <a href="<?= base_url('organizaciones') ?>" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-guardar">
                <i class="bi bi-check-lg"></i> Guardar Organización
            </button>
        </div>

    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="<?= base_url('js/venezuela.js') ?>"></script>
<script>
// ═══ Logo dropzone ═══
document.getElementById('logo-dropzone').addEventListener('click', function () {
    document.getElementById('logo-input').click();
});

document.getElementById('logo-input').addEventListener('change', function (e) {
    const file = e.target.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = function (ev) {
        const preview = document.getElementById('logo-preview');
        preview.src = ev.target.result;
        preview.classList.remove('d-none');
        document.getElementById('dz-message').classList.add('d-none');
    };
    reader.readAsDataURL(file);
});

// ═══ Toggle secciones ═══
function toggleSeccion(bar, id) {
    const sec = document.getElementById(id);
    const isOpen = sec.classList.contains('open');
    sec.classList.toggle('open');
    bar.classList.toggle('active');

    // Marcar dirección como activa si se abre
    const hidden = sec.querySelector('input[type="hidden"]');
    if (hidden) {
        hidden.value = isOpen ? '' : '1';
    }
}

// ═══ Ubicaciones (Venezuela) ═══
const estadoActual = "<?= esc(old('estado', '')) ?>";
const municipioActual = "<?= esc(old('municipio', '')) ?>";
const parroquiaActual = "<?= esc(old('parroquia', '')) ?>";

document.addEventListener('DOMContentLoaded', function () {
    const $e = document.getElementById('estado');
    const $m = document.getElementById('municipio');
    const $p = document.getElementById('parroquia');

    if (typeof ubicaciones !== 'undefined') {
        Object.keys(ubicaciones).forEach(function (estado) {
            const opt = document.createElement('option');
            opt.value = estado;
            opt.textContent = estado;
            $e.appendChild(opt);
        });
    }

    function cargarMunicipios(est, munSel) {
        $m.innerHTML = '<option value="">Selecciona un municipio...</option>';
        $p.innerHTML = '<option value="">Selecciona...</option>';
        if (!est || !ubicaciones[est]) return;
        Object.keys(ubicaciones[est]).forEach(function (mun) {
            const opt = document.createElement('option');
            opt.value = mun;
            opt.textContent = mun;
            if (mun === munSel) opt.selected = true;
            $m.appendChild(opt);
        });
    }

    function cargarParroquias(est, mun, parSel) {
        $p.innerHTML = '<option value="">Selecciona...</option>';
        if (!est || !mun || !ubicaciones[est] || !ubicaciones[est][mun]) return;
        ubicaciones[est][mun].forEach(function (par) {
            const opt = document.createElement('option');
            opt.value = par;
            opt.textContent = par;
            if (par === parSel) opt.selected = true;
            $p.appendChild(opt);
        });
    }

    $e.addEventListener('change', function () {
        document.getElementById('ciudad').value = '';
        cargarMunicipios(this.value, '');
    });

    $m.addEventListener('change', function () {
        const est = $e.value;
        const mun = this.value;
        cargarParroquias(est, mun, '');
        const parroquias = ubicaciones[est]?.[mun] || [];
        document.getElementById('ciudad').value = parroquias.length > 0 ? parroquias[0] : '';
    });

    $p.addEventListener('change', function () {
        if (this.value) document.getElementById('ciudad').value = this.value;
    });

    // Restaurar valores en caso de validación fallida
    if (estadoActual) {
        $e.value = estadoActual;
        cargarMunicipios(estadoActual, municipioActual);
        if (municipioActual) {
            cargarParroquias(estadoActual, municipioActual, parroquiaActual);
        }
        // Abrir sección automáticamente
        const bar = document.querySelector('[onclick*="secDireccion"]');
        if (bar) toggleSeccion(bar, 'secDireccion');
    }
});
</script>
<?= $this->endSection() ?>

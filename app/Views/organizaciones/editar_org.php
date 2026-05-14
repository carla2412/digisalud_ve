<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
 

    .org_edit-page-wrapper {
        max-width: 1180px;
        margin: 0 auto;
        padding: 24px 16px 40px;
    }

    .org_edit-breadcrumb {
        font-size: .82rem;
        color: var(--ds-muted);
        margin-bottom: 18px;
    }

    .org_edit-breadcrumb a {
        color: var(--ds-muted);
        text-decoration: none;
    }

    .org_edit-breadcrumb strong {
        color: var(--ds-text);
    }

    .org_edit-page-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: center;
        margin-bottom: 20px;
    }

    .org_edit-page-header h1 {
        font-size: 1.45rem;
        font-weight: 700;
        color: var(--ds-text);
        margin: 0;
    }

    .org_edit-page-header p {
        color: var(--ds-muted);
        font-size: .9rem;
        margin: 4px 0 0;
    }

    .org_edit-info-box {
        display: flex;
        align-items: center;
        gap: 10px;
        background: #eef7ff;
        border: 1px solid var(--ds-border);
        padding: 12px 16px;
        border-radius: 14px;
        min-width: 260px;
    }

    .org_edit-info-box i {
        color: var(--ds-primary);
        font-size: 1.25rem;
    }

    .org_edit-info-box strong {
        display: block;
        font-size: .85rem;
    }

    .org_edit-info-box span {
        font-size: .75rem;
        color: var(--ds-muted);
    }

    .org_edit-card {
        background: #fff;
        border: 1px solid var(--ds-border);
        border-radius: 24px;
        box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .org_edit-layout {
        display: grid;
        grid-template-columns: 280px 1fr;
        min-height: 520px;
    }

    .org_edit-steps {
        background: linear-gradient(180deg, #f8fbff, #eef5ff);
        border-right: 1px solid var(--ds-border);
        padding: 24px;
    }

    .org_edit-step {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        padding: 14px;
        border-radius: 16px;
        color: var(--ds-muted);
        margin-bottom: 10px;
    }

    .org_edit-step.org_edit-active,
    .org_edit-step.org_edit-is-open {
        background: #fff;
        color: var(--ds-text);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .05);
    }

    .org_edit-step i {
        width: 36px;
        height: 36px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: #e8f3ff;
        color: var(--ds-primary);
    }

    .org_edit-step strong {
        display: block;
        font-size: .86rem;
    }

    .org_edit-step span {
        display: block;
        font-size: .72rem;
        color: var(--ds-muted);
    }

    .org_edit-form {
        padding: 28px;
    }

    .org_edit-section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 16px;
    }

    .org_edit-section-title.org_edit-mt {
        margin-top: 28px;
    }

    .org_edit-section-title i {
        color: var(--ds-primary);
        font-size: 1.2rem;
    }

    .org_edit-section-title h2 {
        font-size: 1rem;
        font-weight: 700;
        color: var(--ds-text);
        margin: 0;
    }

    .org_edit-form-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 18px;
    }

    .org_edit-form-group {
        display: block;
        width: 100%;
    }

    .org_edit-form-group label {
        display: block;
        font-size: .82rem;
        font-weight: 600;
        color: var(--ds-text);
        margin-bottom: 7px;
    }

    .org_edit-form-group label span {
        color: #dc3545;
    }

    .org_edit-form-group input,
    .org_edit-form-group select {
        width: 100%;
        border: 1px solid var(--ds-border);
        border-radius: 13px;
        padding: 11px 13px;
        font-size: .88rem;
        outline: none;
        background: #fff;
    }

    .org_edit-form-group input:focus,
    .org_edit-form-group select:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 4px rgba(54, 149, 245, .12);
    }

    .org_edit-input-icon {
        position: relative;
    }

    .org_edit-input-icon i {
        position: absolute;
        left: 13px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .org_edit-input-icon input {
        padding-left: 40px;
    }

    .org_edit-action-card {
        margin-top: 24px;
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 18px;
        background: #fafcff;
    }

    .org_edit-action-card-header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: center;
    }

    .org_edit-action-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .org_edit-action-info>i {
        width: 42px;
        height: 42px;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        border-radius: 14px;
        background: #e8f3ff;
        color: var(--ds-primary);
        font-size: 1.15rem;
    }

    .org_edit-action-info strong {
        display: block;
        color: var(--ds-text);
        font-size: .9rem;
    }

    .org_edit-action-info span {
        display: block;
        color: var(--ds-muted);
        font-size: .76rem;
    }

    .org_edit-btn-outline,
    .org_edit-btn-cancel,
    .org_edit-btn-save {
        border: none;
        text-decoration: none;
        cursor: pointer;
        border-radius: 13px;
        font-size: .86rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .org_edit-btn-outline {
        background: #fff;
        color: var(--ds-dark);
        border: 1px solid var(--ds-border);
        padding: 10px 16px;
    }

    .org_edit-btn-outline.org_edit-success {
        color: var(--ds-success);
    }

    .org_edit-toggle-content {
        max-height: 0;
        overflow: hidden;
        transition: max-height .3s ease;
    }

    .org_edit-toggle-content.org_edit-open {
        max-height: 900px;
    }

    .org_edit-address-fields {
        padding-top: 18px;
    }

    .org_edit-drop-zone {
        margin-top: 16px;
        border: 2px dashed var(--ds-border);
        background: #fff;
        border-radius: 16px;
        padding: 22px;
        text-align: center;
        color: var(--ds-muted);
        font-size: .82rem;
        cursor: pointer;
    }

    .org_edit-drop-zone:hover {
        border-color: var(--ds-primary);
        background: #f8fbff;
    }

    .org_edit-logo-preview {
        width: 96px;
        height: 96px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 14px;
        border: 4px solid var(--ds-border);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
    }

    .org_edit-form-actions {
        border-top: 1px solid var(--ds-border);
        padding: 18px 28px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #fbfdff;
    }

    .org_edit-btn-cancel {
        color: var(--ds-muted);
        border: 1px solid var(--ds-border);
        padding: 11px 20px;
        background: #fff;
    }

    .org_edit-btn-save {
        color: #fff;
        background: var(--ds-primary);
        padding: 12px 24px;
    }

    .org_edit-btn-save:hover {
        color: #fff;
        background: var(--ds-dark);
        padding: 12px 24px;
    }

    @media (max-width: 900px) {

        .org_edit-page-header,
        .org_edit-action-card-header {
            flex-direction: column;
            align-items: flex-start;
        }

        .org_edit-layout {
            grid-template-columns: 1fr;
        }

        .org_edit-steps {
            border-right: 0;
            border-bottom: 1px solid var(--ds-border);
        }

        .org_edit-form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>
<?php
$org = $organizacion ?? [];
$dir = $direccion ?? null;

$tieneLogo = !empty($org['logo_url']);
$logoSrc = $tieneLogo ? base_url('uploads/logos/' . $org['logo_url']) : '#';

$tieneDireccion = !empty($org['direccion_id']);
$partesResponsable = preg_split('/\s+/', trim($org['nombre_responsable'] ?? ''), 2);
$responsableNombres = $partesResponsable[0] ?? '';
$responsableApellidos = $partesResponsable[1] ?? '';
?>

<div class="org_edit-page-wrapper">

    <div class="org_edit-breadcrumb">
        <a href="<?= base_url('/') ?>">Inicio</a> /
        <a href="<?= base_url('organizaciones') ?>">Organizaciones</a> /
        <strong>Editar Organización</strong>
    </div>

    <div class="org_edit-page-header">
        <div>
            <h1>Editar Organización</h1>
            <p>Modifica los datos de <strong><?= esc($org['nombre_org'] ?? '') ?></strong>.</p>
        </div>

        <div class="org_edit-info-box">
            <i class="bi bi-info-circle"></i>
            <div>
                <strong>Información</strong>
                <span>Si no cambias el logo, se conserva el actual.</span>
            </div>
        </div>
    </div>

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

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="<?= base_url('organizaciones/update/' . $org['id_organizacion']) ?>"
        method="POST"
        enctype="multipart/form-data"
        class="org_edit-card"
        novalidate>

        <?= csrf_field() ?>

        <div class="org_edit-layout">

            <aside class="org_edit-steps">
                <div class="org_edit-step org_edit-active">
                    <i class="bi bi-building"></i>
                    <div>
                        <strong>Datos Básicos</strong>
                        <span>Información general</span>
                    </div>
                </div>

                <div class="org_edit-step">
                    <i class="bi bi-telephone"></i>
                    <div>
                        <strong>Contacto</strong>
                        <span>Datos de comunicación</span>
                    </div>
                </div>

                <div class="org_edit-step <?= $tieneDireccion ? 'org_edit-is-open' : '' ?>" id="stepDireccion">
                    <i class="bi bi-geo-alt"></i>
                    <div>
                        <strong>Dirección</strong>
                        <span><?= $tieneDireccion ? esc(($dir['estado'] ?? '') . ' — ' . ($dir['ciudad'] ?? '')) : 'Ubicación de la organización' ?></span>
                    </div>
                </div>

                <div class="org_edit-step <?= $tieneLogo ? 'org_edit-is-open' : '' ?>" id="stepLogo">
                    <i class="bi bi-image"></i>
                    <div>
                        <strong>Logo</strong>
                        <span><?= $tieneLogo ? 'Logo actual cargado' : 'Imagen de la organización' ?></span>
                    </div>
                </div>
            </aside>

            <section class="org_edit-form">

                <div class="org_edit-section-title">
                    <i class="bi bi-building"></i>
                    <h2>Datos Básicos</h2>
                </div>

                <div class=" ">
                    <div class="org_edit-form-group">
                        <label for="nombre_org">Nombre de la organización <span>*</span></label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-building"></i> 
                            <input type="text"
                                id="nombre_org"
                                name="nombre_org"
                                value="<?= old('nombre_org', $org['nombre_org'] ?? '') ?>"
                                placeholder="Ej: Fundación Salud Para Todos"
                                required>
                        </div>
                    </div>

                </div><br>
                <div class="org_edit-form-grid">
                    

                    <div class="org_edit-form-group">
                        <label for="tipo">Tipo <span>*</span></label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Selecciona un tipo...</option>
                            <?php
                            $tipos = ['Escolar', 'Comedor', 'Empresa Privada', 'Casa hogar', 'ONG', 'Alcaldía', 'Gobernación', 'Mixto', 'Organismo Público'];
                            foreach ($tipos as $t):
                            ?>
                                <option value="<?= esc($t) ?>" <?= old('tipo', $org['tipo'] ?? '') === $t ? 'selected' : '' ?>>
                                    <?= esc($t) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="categoria">Categoría <span>*</span></label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Selecciona una categoría...</option>
                            <?php
                            $categorias = ['Pública', 'Privada', 'Social', 'Educativa', 'Salud', 'Comunitaria'];
                            foreach ($categorias as $c):
                            ?>
                                <option value="<?= esc($c) ?>" <?= old('categoria', $org['categoria'] ?? '') === $c ? 'selected' : '' ?>>
                                    <?= esc($c) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="responsable_nombres">Nombre del responsable <span>*</span></label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-person"></i>
                            <input type="text"
                                id="responsable_nombres"
                                name="responsable_nombres"
                                value="<?= old('responsable_nombres', $responsableNombres) ?>"
                                placeholder="Ej: Juan"
                                required>
                        </div>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="responsable_apellidos">Apellido del responsable <span>*</span></label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-person"></i>
                            <input type="text"
                                id="responsable_apellidos"
                                name="responsable_apellidos"
                                value="<?= old('responsable_apellidos', $responsableApellidos) ?>"
                                placeholder="Ej: Pérez"
                                required>
                        </div>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="password">Nueva contraseña</label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-key"></i>
                            <input type="password"
                                id="password"
                                name="password"
                                minlength="6"
                                placeholder="Dejar vacío para no cambiar">
                        </div>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="confirmar_password">Confirmar nueva contraseña</label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-key-fill"></i>
                            <input type="password"
                                id="confirmar_password"
                                name="confirmar_password"
                                minlength="6"
                                placeholder="Repite solo si cambias contraseña">
                        </div>
                    </div>
                </div>
                <div class="org_edit-section-title org_edit-mt">
                    <i class="bi bi-telephone"></i>
                    <h2>Contacto Organización</h2>
                </div>

                <div class="org_edit-form-grid">
                    <div class="org_edit-form-group">
                        <label for="telefono">Teléfono Organización<span>*</span></label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-telephone"></i>
                            <input type="text"
                                id="telefono"
                                name="telefono"
                                value="<?= old('telefono', $org['telefono'] ?? '') ?>"
                                placeholder="+58 412 0000000"
                                required>
                        </div>
                    </div>

                    <div class="org_edit-form-group">
                        <label for="email">Correo electrónico Organización <span>*</span></label>
                        <div class="org_edit-input-icon">
                            <i class="bi bi-envelope"></i>
                            <input type="email"
                                id="email"
                                name="email"
                                value="<?= old('email', $org['email'] ?? '') ?>"
                                placeholder="ejemplo@organizacion.org"
                                required>
                        </div>
                    </div>
                </div>

                <div class="org_edit-action-card org_edit-address-card">
                    <div class="org_edit-action-card-header">
                        <div class="org_edit-action-info">
                            <i class="bi bi-geo-alt"></i>
                            <div>
                                <strong>Dirección</strong>
                                <span><?= $tieneDireccion ? esc(($dir['estado'] ?? '') . ' — ' . ($dir['ciudad'] ?? '')) : 'Opcional — carga automática desde venezuela.js' ?></span>
                            </div>
                        </div>

                        <button type="button" class="org_edit-btn-outline" onclick="toggleSeccion('secDireccion', 'stepDireccion')">
                            <i class="bi bi-map"></i>
                            <?= $tieneDireccion ? 'Editar dirección' : 'Buscar dirección' ?>
                        </button>
                    </div>

                    <div class="org_edit-toggle-content <?= $tieneDireccion ? 'org_edit-open' : '' ?>" id="secDireccion">
                        <input type="hidden"
                            name="direccion_activa"
                            id="hDireccion"
                            value="<?= old('direccion_activa', $tieneDireccion ? '1' : '') ?>">

                        <div class="org_edit-form-grid org_edit-address-fields">
                            <div class="org_edit-form-group">
                                <label for="pais">País</label>
                                <input type="text" id="pais" name="pais" value="Venezuela" readonly>
                            </div>

                            <div class="org_edit-form-group">
                                <label for="estado">Estado</label>
                                <select id="estado" name="estado">
                                    <option value="">Selecciona un estado...</option>
                                </select>
                            </div>

                            <div class="org_edit-form-group">
                                <label for="municipio">Municipio</label>
                                <select id="municipio" name="municipio">
                                    <option value="">Selecciona un municipio...</option>
                                </select>
                            </div>

                            <div class="org_edit-form-group">
                                <label for="parroquia">Parroquia</label>
                                <select id="parroquia" name="parroquia">
                                    <option value="">Selecciona...</option>
                                </select>
                            </div>

                            <div class="org_edit-form-group">
                                <label for="ciudad">Ciudad o localidad</label>
                                <input type="text"
                                    name="ciudad"
                                    id="ciudad"
                                    value="<?= old('ciudad', $dir['ciudad'] ?? '') ?>"
                                    placeholder="Se carga automático o escribe manualmente">
                            </div>

                            <div class="org_edit-form-group">
                                <label for="detalle">Detalle de dirección</label>
                                <input type="text"
                                    name="detalle"
                                    id="detalle"
                                    value="<?= old('detalle', $dir['detalle'] ?? '') ?>"
                                    placeholder="Ej: Calle, avenida, edificio, punto de referencia">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="org_edit-action-card org_edit-logo-card">
                    <div class="org_edit-action-card-header">
                        <div class="org_edit-action-info">
                            <i class="bi bi-image"></i>
                            <div>
                                <strong>Logo de la organización</strong>
                                <span><?= $tieneLogo ? 'Logo actual cargado — sube uno nuevo para reemplazar' : 'Opcional — PNG, JPG o JPEG máx. 2MB' ?></span>
                            </div>
                        </div>

                        <label class="org_edit-btn-outline org_edit-success">
                            <i class="bi bi-cloud-arrow-up"></i>
                            Subir logo
                            <input type="file"
                                name="logo"
                                id="logo-input"
                                accept=".png,.jpg,.jpeg,image/png,image/jpg,image/jpeg"
                                hidden>
                        </label>
                    </div>

                    <div class="org_edit-drop-zone" id="logo-dropzone">
                        <div id="dz-message" class="<?= $tieneLogo ? 'd-none' : '' ?>">
                            <i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i>
                            <div>Arrastra y suelta tu archivo aquí o haz clic para seleccionar</div>
                        </div>

                        <img id="org_edit-logo-preview"
                            src="<?= $logoSrc ?>"
                            alt="Previsualización"
                            class="org_edit-logo-preview <?= $tieneLogo ? '' : 'd-none' ?>">
                    </div>

                    <?php if ($tieneLogo): ?>
                        <div class="text-muted mt-2" style="font-size:.75rem;">
                            Si no subes un archivo nuevo, se conserva el logo actual.
                        </div>
                    <?php endif; ?>
                </div>

            </section>
        </div>

        <div class="org_edit-form-actions">
            <a href="<?= base_url('organizaciones') ?>" class="org_edit-btn-cancel">Cancelar</a>

            <button type="submit" class="org_edit-btn-save">
                <i class="bi bi-check-circle"></i>
                Actualizar Organización
            </button>
        </div>

    </form>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="<?= base_url('js/venezuela.js') ?>"></script>

<script>
    const estadoActual = "<?= esc(old('estado', $dir['estado'] ?? '')) ?>";
    const municipioActual = "<?= esc(old('municipio', $dir['municipio'] ?? '')) ?>";
    const parroquiaActual = "<?= esc(old('parroquia', $dir['parroquia'] ?? '')) ?>";

    const logoDropzone = document.getElementById('logo-dropzone');
    const logoInput = document.getElementById('logo-input');

    logoDropzone.addEventListener('click', function() {
        logoInput.click();
    });

    logoDropzone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#3695f5';
    });

    logoDropzone.addEventListener('dragleave', function() {
        this.style.borderColor = '#d1d5db';
    });

    logoDropzone.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.borderColor = '#d1d5db';

        if (e.dataTransfer.files.length > 0) {
            logoInput.files = e.dataTransfer.files;
            mostrarPreviewLogo(e.dataTransfer.files[0]);
        }
    });

    logoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        mostrarPreviewLogo(file);
    });

    function mostrarPreviewLogo(file) {
        const reader = new FileReader();

        reader.onload = function(ev) {
            const preview = document.getElementById('org_edit-logo-preview');
            preview.src = ev.target.result;
            preview.classList.remove('d-none');

            const dzMessage = document.getElementById('dz-message');
            if (dzMessage) dzMessage.classList.add('d-none');

            const stepLogo = document.getElementById('stepLogo');
            if (stepLogo) stepLogo.classList.add('org_edit-is-open');
        };

        reader.readAsDataURL(file);
    }

    function toggleSeccion(id, stepId) {
        const sec = document.getElementById(id);
        const step = document.getElementById(stepId);

        if (!sec) return;

        const isOpen = sec.classList.contains('org_edit-open');
        sec.classList.toggle('org_edit-open');

        if (step) {
            step.classList.toggle('org_edit-is-open');
        }

        const hidden = sec.querySelector('input[type="hidden"]');
        if (hidden) {
            hidden.value = isOpen ? '' : '1';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const $e = document.getElementById('estado');
        const $m = document.getElementById('municipio');
        const $p = document.getElementById('parroquia');
        const $ciudad = document.getElementById('ciudad');

        if (typeof ubicaciones !== 'undefined') {
            Object.keys(ubicaciones).forEach(function(estado) {
                const opt = document.createElement('option');
                opt.value = estado;
                opt.textContent = estado;
                $e.appendChild(opt);
            });
        }

        function cargarMunicipios(est, munSel) {
            $m.innerHTML = '<option value="">Selecciona un municipio...</option>';
            $p.innerHTML = '<option value="">Selecciona...</option>';

            if (!est || typeof ubicaciones === 'undefined' || !ubicaciones[est]) return;

            Object.keys(ubicaciones[est]).forEach(function(mun) {
                const opt = document.createElement('option');
                opt.value = mun;
                opt.textContent = mun;

                if (mun === munSel) {
                    opt.selected = true;
                }

                $m.appendChild(opt);
            });
        }

        function cargarParroquias(est, mun, parSel) {
            $p.innerHTML = '<option value="">Selecciona...</option>';

            if (!est || !mun || typeof ubicaciones === 'undefined' || !ubicaciones[est] || !ubicaciones[est][mun]) return;

            ubicaciones[est][mun].forEach(function(par) {
                const opt = document.createElement('option');
                opt.value = par;
                opt.textContent = par;

                if (par === parSel) {
                    opt.selected = true;
                }

                $p.appendChild(opt);
            });
        }

        $e.addEventListener('change', function() {
            $ciudad.value = '';
            cargarMunicipios(this.value, '');
        });

        $m.addEventListener('change', function() {
            const est = $e.value;
            const mun = this.value;

            cargarParroquias(est, mun, '');

            const parroquias = ubicaciones?.[est]?.[mun] || [];
            $ciudad.value = parroquias.length > 0 ? parroquias[0] : '';
        });

        $p.addEventListener('change', function() {
            if (this.value) {
                $ciudad.value = this.value;
            }
        });

        if (estadoActual) {
            $e.value = estadoActual;
            cargarMunicipios(estadoActual, municipioActual);

            if (municipioActual) {
                cargarParroquias(estadoActual, municipioActual, parroquiaActual);
            }
        }
    });
</script>
<?= $this->endSection() ?>
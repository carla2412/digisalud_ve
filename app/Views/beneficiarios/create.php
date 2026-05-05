<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    :root {
        --ds-primary: #3695f5;
        --ds-primary-dark: #1b7ae2;
        --ds-dark: #101a61;
        --ds-bg: #f5f8fc;
        --ds-light: #f8f9fa;
        --ds-border: #e0e6ed;
        --ds-muted: #38393a;
        --line: #e0e6ed;

        --white: #ffffff;
        --success: #29b35b;
        --danger: #e5484d;
        --warning: #f59e0b;
        --text-soft: #6b7280;
        --shadow-sm: 0 4px 16px rgba(16, 26, 97, 0.06);
        --shadow-md: 0 10px 30px rgba(16, 26, 97, 0.08);
        --radius-lg: 20px;
        --radius-md: 14px;
        --radius-sm: 10px;
    }

    body {
        background: linear-gradient(180deg, #f7faff 0%, #f5f8fc 100%);
    }

    .beneficiario-page {
        max-width: 1240px;
        margin: 0 auto;
        padding: 28px 12px 40px;
    }

    .breadcrumb-digi {
        font-size: 14px;
        color: var(--text-soft);
        margin-bottom: 22px;
    }

    .breadcrumb-digi a {
        color: var(--text-soft);
        text-decoration: none;
        font-weight: 500;
    }

    .breadcrumb-digi a:hover {
        color: var(--ds-primary);
    }

    .breadcrumb-digi .active {
        color: var(--ds-dark);
        font-weight: 700;
    }

    .beneficiario-shell {
        background: var(--white);
        border: 1px solid var(--ds-border);
        border-radius: 28px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
    }

    .shell-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        padding: 32px 32px 24px;
        border-bottom: 1px solid var(--line);
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .title-area h1 {
        margin: 0 0 10px;
        font-size: 28px;
        line-height: 1.2;
        color: var(--ds-dark);
        font-weight: 800;
    }

    .title-area p {
        margin: 0;
        color: var(--text-soft);
        font-size: 15px;
    }

    .id-card {
        min-width: 270px;
        background: var(--white);
        border: 1px solid var(--ds-border);
        border-radius: 16px;
        padding: 14px 16px;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .id-icon,
    .step-icon {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(54, 149, 245, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ds-primary);
        font-size: 18px;
        flex-shrink: 0;
    }

    .id-meta small {
        display: block;
        color: var(--text-soft);
        font-size: 12px;
        margin-bottom: 4px;
    }

    .id-meta strong {
        color: var(--ds-dark);
        font-size: 14px;
        letter-spacing: 0.2px;
        word-break: break-all;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 280px 1fr;
        gap: 0;
        min-height: 680px;
    }

    .side-steps {
        border-right: 1px solid var(--line);
        padding: 20px;
        background: #fcfdff;
    }

    .step-link {
        width: 100%;
        display: flex;
        gap: 14px;
        align-items: flex-start;
        text-decoration: none;
        padding: 16px;
        border-radius: 16px;
        color: var(--ds-dark);
        margin-bottom: 10px;
        border: 1px solid transparent;
        transition: 0.2s ease;
        background: transparent;
        cursor: pointer;
        text-align: left;
    }

    .step-link:hover {
        background: #f8fbff;
        border-color: #eef4fb;
    }

    .step-link.active {
        background: rgba(54, 149, 245, 0.08);
        border-color: rgba(54, 149, 245, 0.14);
    }

    .step-text strong {
        display: block;
        font-size: 15px;
        margin-bottom: 4px;
        color: var(--ds-dark);
    }

    .step-text span {
        display: block;
        color: var(--text-soft);
        font-size: 13px;
        line-height: 1.35;
    }

    .main-panel {
        padding: 24px;
        background: #fff;
    }

    .section-card {
        border: 1px solid var(--ds-border);
        border-radius: 22px;
        overflow: hidden;
        background: var(--white);
        margin-bottom: 18px;
    }

    .section-header-modern {
        display: flex;
        gap: 14px;
        align-items: center;
        padding: 22px 22px 12px;
    }

    .section-header-modern h2 {
        margin: 0 0 4px;
        font-size: 18px;
        color: var(--ds-dark);
        font-weight: 800;
    }

    .section-header-modern p {
        margin: 0;
        color: var(--text-soft);
        font-size: 14px;
    }

    .form-grid {
        padding: 0 22px 22px;
    }

    .form-label {
        font-size: 14px;
        font-weight: 700;
        color: var(--ds-dark);
        margin-bottom: 7px;
    }

    .req {
        color: var(--danger);
    }

    .form-control,
    .form-select {
        min-height: 48px;
        border: 1px solid var(--ds-border);
        border-radius: 12px;
        font-size: 15px;
        color: var(--ds-muted);
        background-color: #fff;
        box-shadow: none !important;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 4px rgba(54, 149, 245, 0.12) !important;
    }

    .helper {
        font-size: 12px;
        color: var(--text-soft);
        margin-top: 6px;
    }

    .toggle-bar {
        border: 1px solid var(--ds-border);
        border-radius: 16px;
        background: var(--white);
        padding: 18px 20px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        cursor: pointer;
        margin-bottom: 12px;
        transition: 0.2s ease;
    }

    .toggle-bar:hover {
        border-color: rgba(54, 149, 245, 0.28);
        box-shadow: var(--shadow-sm);
    }

    .toggle-bar.active {
        background: rgba(54, 149, 245, 0.06);
        border-color: rgba(54, 149, 245, 0.20);
    }

    .toggle-left {
        display: flex;
        gap: 14px;
        align-items: center;
        min-width: 0;
    }

    .toggle-label {
        font-size: 16px;
        font-weight: 800;
        color: var(--ds-dark);
        margin-bottom: 4px;
    }

    .toggle-desc {
        font-size: 13px;
        color: var(--text-soft);
        line-height: 1.4;
    }

    .toggle-chevron {
        color: var(--text-soft);
        font-size: 22px;
        flex-shrink: 0;
        transition: transform 0.2s ease;
    }

    .toggle-bar.active .toggle-chevron {
        transform: rotate(180deg);
    }

    .toggle-content {
        display: none;
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 18px;
        margin: -4px 0 14px;
        background: #fff;
    }

    .toggle-content.open {
        display: block;
    }

    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid var(--ds-border) !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        color: var(--ds-muted) !important;
        padding-left: 14px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }

    .chk-evaluar,
    .chk-lentes {
        border: 1px solid rgba(54, 149, 245, 0.18);
        background: rgba(54, 149, 245, 0.06);
        border-radius: 14px;
        padding: 14px 16px;
    }

    .rep-nuevo-form {
        border: 1px dashed rgba(54, 149, 245, 0.35);
        background: #f8fbff;
        border-radius: 16px;
        padding: 16px;
    }

    .rep-result {
        border: 1px solid var(--ds-border);
        border-radius: 12px;
        padding: 11px 13px;
        margin-bottom: 8px;
        cursor: pointer;
        background: #fff;
        transition: 0.2s ease;
        font-size: 14px;
    }

    .rep-result:hover {
        border-color: var(--ds-primary);
        background: rgba(54, 149, 245, 0.05);
    }

    .rep-selected {
        margin-top: 10px;
        border: 1px solid rgba(41, 179, 91, 0.25);
        background: rgba(41, 179, 91, 0.08);
        border-radius: 12px;
        padding: 11px 13px;
        font-size: 14px;
        color: var(--ds-dark);
    }

    .tag-container {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .tag-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(54, 149, 245, 0.08);
        color: var(--ds-dark);
        border: 1px solid rgba(54, 149, 245, 0.18);
        border-radius: 999px;
        padding: 7px 10px;
        font-size: 13px;
        font-weight: 600;
    }

    .tag-remove {
        cursor: pointer;
        color: var(--danger);
        font-weight: 900;
        font-size: 15px;
    }

    .shell-footer {
        border-top: 1px solid var(--line);
        background: #fff;
        padding: 18px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
    }

    .footer-note {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--text-soft);
        font-size: 13px;
    }

    .footer-note .shield {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: #f3f7fd;
        color: var(--ds-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
    }

    .footer-note strong {
        display: block;
        color: var(--ds-dark);
        margin-bottom: 2px;
        font-size: 14px;
    }

    .actions {
        display: flex;
        gap: 12px;
        align-items: center;
    }

    .btn-cancelar,
    .btn-guardar {
        min-height: 46px;
        border-radius: 12px;
        padding: 11px 20px;
        font-size: 15px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: 0.2s ease;
    }

    .btn-cancelar {
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-dark);
    }

    .btn-guardar {
        border: 0;
        background: linear-gradient(180deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 8px 20px rgba(54, 149, 245, 0.22);
        min-width: 220px;
    }

    .btn-cancelar:hover,
    .btn-guardar:hover {
        transform: translateY(-1px);
    }

    .required-note {
        padding: 0 22px 20px;
        font-size: 13px;
        color: var(--text-soft);
    }

    .alert-modern {
        border-radius: 16px;
        border: 1px solid rgba(229, 72, 77, 0.18);
        background: rgba(229, 72, 77, 0.06);
        color: #9f1239;
        padding: 14px 16px;
        margin-bottom: 18px;
    }

    .select2-hidden-accessible {
        border: 0 !important;
        clip: rect(0 0 0 0) !important;
        clip-path: inset(50%) !important;
        height: 1px !important;
        overflow: hidden !important;
        padding: 0 !important;
        position: absolute !important;
        width: 1px !important;
        white-space: nowrap !important;
    }

    .select2-container {
        width: 100% !important;
    }

    .select2-container--default .select2-selection--single {
        height: 48px !important;
        border: 1px solid var(--ds-border) !important;
        border-radius: 12px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding-left: 14px !important;
        color: var(--ds-muted) !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 46px !important;
    }

    @media (max-width: 1100px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .side-steps {
            border-right: 0;
            border-bottom: 1px solid var(--line);
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .step-link {
            margin-bottom: 0;
        }
    }

    @media (max-width: 860px) {
        .shell-header {
            flex-direction: column;
            align-items: stretch;
        }

        .id-card {
            min-width: 100%;
        }

        .actions {
            width: 100%;
            justify-content: flex-end;
        }
    }

    @media (max-width: 640px) {
        .beneficiario-page {
            padding: 16px 8px;
        }

        .shell-header,
        .main-panel,
        .shell-footer,
        .side-steps {
            padding-left: 16px;
            padding-right: 16px;
        }

        .side-steps {
            grid-template-columns: 1fr;
        }

        .title-area h1 {
            font-size: 24px;
        }

        .actions {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-cancelar,
        .btn-guardar {
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$errors = session('errors') ?? [];
$openDireccion    = old('direccion_activa') === '1';
$openEscolaridad  = old('escolaridad_activa') === '1';
$openFamiliar     = old('familiar_activo') === '1';
?>

<div class="beneficiario-page">

    <div class="breadcrumb-digi">
        <a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt;
        <a href="<?= base_url("jornadas/$jornada_id/beneficiarios") ?>">Beneficiarios</a> &gt;
        <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/buscar") ?>">Buscar</a> &gt;
        <span class="active">Nuevo registro</span>
    </div>

    <?php if (! empty($errors)): ?>
        <div class="alert-modern">
            <strong>Revisa la información ingresada.</strong>
            <ul class="mb-0 mt-2">
                <?php foreach ($errors as $error): ?>
                    <li><?= esc($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="post" action="<?= base_url("jornadas/$jornada_id/beneficiarios/store") ?>" id="formBeneficiario" novalidate>
        <?= csrf_field() ?>

        <div class="beneficiario-shell">

            <div class="shell-header">
                <div class="title-area">
                    <h1>Registrar nuevo beneficiario</h1>
                    <p>Completa los datos del beneficiario para asociarlo a una jornada.</p>
                </div>

                <div class="id-card">
                    <div class="id-icon">
                        <i class="bi bi-person-vcard"></i>
                    </div>
                    <div class="id-meta">
                        <small>ID Digisalud autogenerado</small>
                        <strong id="idPreview">__ · _ · ___ · ___ · ________</strong>
                    </div>
                </div>
            </div>

            <div class="content-grid">

                <aside class="side-steps">
                    <button type="button" class="step-link active" data-section="datosPersonales">
                        <div class="step-icon"><i class="bi bi-person"></i></div>
                        <div class="step-text">
                            <strong>Datos personales</strong>
                            <span>Información básica</span>
                        </div>
                    </button>

                    <button type="button" class="step-link <?= $openDireccion ? 'active' : '' ?>" onclick="abrirSeccionDesdeMenu('secDireccion')">
                        <div class="step-icon"><i class="bi bi-geo-alt"></i></div>
                        <div class="step-text">
                            <strong>Residencia</strong>
                            <span>Dirección de residencia</span>
                        </div>
                    </button>

                    <button type="button" class="step-link <?= $openEscolaridad ? 'active' : '' ?>" onclick="abrirSeccionDesdeMenu('secEscolaridad')">
                        <div class="step-icon"><i class="bi bi-mortarboard"></i></div>
                        <div class="step-text">
                            <strong>Escolaridad</strong>
                            <span>Información educativa</span>
                        </div>
                    </button>

                    <button type="button" class="step-link <?= $openFamiliar ? 'active' : '' ?>" onclick="abrirSeccionDesdeMenu('secFamiliar')">
                        <div class="step-icon"><i class="bi bi-people"></i></div>
                        <div class="step-text">
                            <strong>Familiar / Representante</strong>
                            <span>Datos del familiar</span>
                        </div>
                    </button>

                    <button type="button" class="step-link" onclick="abrirSeccionDesdeMenu('secAntecedentes')">
                        <div class="step-icon"><i class="bi bi-heart-pulse"></i></div>
                        <div class="step-text">
                            <strong>Antecedentes</strong>
                            <span>Clínicos y socioeconómicos</span>
                        </div>
                    </button>
                </aside>

                <main class="main-panel">

                    <!-- DATOS PERSONALES -->
                    <section class="section-card" id="datosPersonales">
                        <div class="section-header-modern">
                            <div class="step-icon"><i class="bi bi-person"></i></div>
                            <div>
                                <h2>Datos personales</h2>
                                <p>Información básica del beneficiario.</p>
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombres <span class="req">*</span></label>
                                    <input
                                        type="text"
                                        name="nombres"
                                        id="fNombres"
                                        class="form-control"
                                        required
                                        value="<?= old('nombres') ?>"
                                        placeholder="Ej: María José"
                                        oninput="actualizarIdPreview()">
                                    <div class="helper">Ingresa los nombres completos.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Apellidos <span class="req">*</span></label>
                                    <input
                                        type="text"
                                        name="apellidos"
                                        id="fApellidos"
                                        class="form-control"
                                        required
                                        value="<?= old('apellidos') ?>"
                                        placeholder="Ej: García López"
                                        oninput="actualizarIdPreview()">
                                    <div class="helper">Ingresa los apellidos completos.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Fecha de nacimiento <span class="req">*</span></label>
                                    <input
                                        type="date"
                                        name="fecha_nacimiento"
                                        id="fFecha"
                                        class="form-control"
                                        required
                                        value="<?= old('fecha_nacimiento') ?>"
                                        oninput="actualizarIdPreview()">
                                    <div class="helper">Formato: dd/mm/aaaa</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Sexo <span class="req">*</span></label>
                                    <select name="sexo" id="fSexo" class="form-select" required onchange="actualizarIdPreview()">
                                        <option value="">Seleccionar...</option>
                                        <option value="M" <?= old('sexo') === 'M' ? 'selected' : '' ?>>Masculino</option>
                                        <option value="F" <?= old('sexo') === 'F' ? 'selected' : '' ?>>Femenino</option>
                                    </select>
                                    <div class="helper">Selecciona el sexo.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">País de nacimiento <span class="req">*</span></label>
                                    <select name="pais_nacimiento" id="fPais" class="form-select" required onchange="actualizarIdPreview()">
                                        <?php
                                        $paises = ['Venezuela', 'Colombia', 'Ecuador', 'Brasil', 'El Salvador', 'Guatemala'];
                                        $paisOld = old('pais_nacimiento') ?: 'Venezuela';
                                        ?>
                                        <?php foreach ($paises as $pais): ?>
                                            <option value="<?= esc($pais) ?>" <?= $paisOld === $pais ? 'selected' : '' ?>>
                                                <?= $pais === 'Peru' ? 'Perú' : esc($pais) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="helper">Selecciona el país de nacimiento.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Teléfono</label>
                                    <input
                                        type="tel"
                                        name="telefono"
                                        class="form-control"
                                        value="<?= old('telefono') ?>"
                                        placeholder="+58 412 1234567">
                                    <div class="helper">Incluye código de país si aplica.</div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Correo</label>
                                    <input
                                        type="email"
                                        name="correo"
                                        class="form-control"
                                        value="<?= old('correo') ?>"
                                        placeholder="ejemplo@correo.com">
                                    <div class="helper">Correo electrónico de contacto.</div>
                                </div>
                            </div>
                        </div>

                        <div class="required-note">
                            <span class="req">*</span> Campos obligatorios
                        </div>
                    </section>

                    <!-- ═══ DIRECCIÓN ═══ -->
                    <div class="toggle-bar <?= $openDireccion ? 'active' : '' ?>" onclick="toggleSeccion(this,'secDireccion')">
                        <div class="toggle-left">
                            <div class="step-icon"><i class="bi bi-geo-alt"></i></div>
                            <div>
                                <div class="toggle-label">Dirección de residencia</div>
                                <div class="toggle-desc">País, estado, municipio, parroquia, ciudad y detalle</div>
                            </div>
                        </div>
                        <div class="toggle-chevron"><i class="bi bi-chevron-down"></i></div>
                    </div>

                    <div class="toggle-content <?= $openDireccion ? 'open' : '' ?>" id="secDireccion">
                        <input type="hidden" name="direccion_activa" id="hDireccion" value="<?= $openDireccion ? '1' : '' ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">País</label>
                                <input
                                    type="text"
                                    name="pais"
                                    class="form-control"
                                    value="<?= old('pais') ?: 'Venezuela' ?>"
                                    readonly
                                    style="background:#f8f9fa;">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado</label>
                                <select id="estado" name="estado" class="form-select" data-old="<?= old('estado') ?>">
                                    <option value="">Selecciona un estado...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Municipio</label>
                                <select id="municipio" name="municipio" class="form-select" data-old="<?= old('municipio') ?>">
                                    <option value="">Selecciona un municipio...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Parroquia</label>
                                <select id="parroquia" name="parroquia" class="form-select" data-old="<?= old('parroquia') ?>">
                                    <option value="">Selecciona una parroquia...</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ciudad o localidad</label>
                                <input
                                    type="text"
                                    name="ciudad"
                                    id="ciudad"
                                    class="form-control"
                                    value="<?= old('ciudad') ?>"
                                    placeholder="Ej: Araure, Acarigua, Guanare...">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Detalle</label>
                                <input
                                    type="text"
                                    name="detalle"
                                    id="detalle"
                                    class="form-control"
                                    value="<?= old('detalle') ?>"
                                    placeholder="Sector, calle, casa, punto de referencia...">
                            </div>
                        </div>
                    </div>

                    <!-- ESCOLARIDAD -->
                    <div class="toggle-bar <?= $openEscolaridad ? 'active' : '' ?>" onclick="toggleSeccion(this,'secEscolaridad')">
                        <div class="toggle-left">
                            <div class="step-icon"><i class="bi bi-mortarboard"></i></div>
                            <div>
                                <div class="toggle-label">Escolaridad</div>
                                <div class="toggle-desc">Opcional — se registra fecha y usuario para historial</div>
                            </div>
                        </div>
                        <div class="toggle-chevron"><i class="bi bi-chevron-down"></i></div>
                    </div>

                    <div class="toggle-content <?= $openEscolaridad ? 'open' : '' ?>" id="secEscolaridad">
                        <input type="hidden" name="escolaridad_activa" id="hEscolaridad" value="<?= $openEscolaridad ? '1' : '' ?>">

                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label">Nombre de la escuela</label>
                                <input
                                    type="text"
                                    name="nombre_escuela"
                                    class="form-control"
                                    value="<?= old('nombre_escuela') ?>"
                                    placeholder="Ej: U.E. Simón Bolívar">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Grado</label>
                                <?php
                                $grados = ['1er grado', '2do grado', '3er grado', '4to grado', '5to grado', '6to grado', '1er año', '2do año', '3er año', '4to año', '5to año'];
                                ?>
                                <select name="grado" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($grados as $grado): ?>
                                        <option value="<?= esc($grado) ?>" <?= old('grado') === $grado ? 'selected' : '' ?>>
                                            <?= esc($grado) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Sección</label>
                                <input
                                    type="text"
                                    name="seccion"
                                    class="form-control"
                                    value="<?= old('seccion') ?>"
                                    placeholder="A, B, C...">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Turno</label>
                                <?php $turnos = ['Mañana', 'Tarde', 'Integral']; ?>
                                <select name="turno" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($turnos as $turno): ?>
                                        <option value="<?= esc($turno) ?>" <?= old('turno') === $turno ? 'selected' : '' ?>>
                                            <?= esc($turno) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- FAMILIAR / REPRESENTANTE -->
                    <div class="toggle-bar <?= $openFamiliar ? 'active' : '' ?>" onclick="toggleSeccion(this,'secFamiliar')">
                        <div class="toggle-left">
                            <div class="step-icon"><i class="bi bi-people"></i></div>
                            <div>
                                <div class="toggle-label">Familiar / Representante</div>
                                <div class="toggle-desc">Opcional — busca existente o registra nuevo</div>
                            </div>
                        </div>
                        <div class="toggle-chevron"><i class="bi bi-chevron-down"></i></div>
                    </div>

                    <div class="toggle-content <?= $openFamiliar ? 'open' : '' ?>" id="secFamiliar">
                        <input type="hidden" name="familiar_activo" id="hFamiliar" value="<?= $openFamiliar ? '1' : '' ?>">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Relación</label>
                                <?php $relaciones = ['Madre', 'Padre', 'Abuelo/a', 'Tío/a', 'Hermano/a', 'Otro']; ?>
                                <select name="relacion" class="form-select">
                                    <option value="">Seleccionar...</option>
                                    <?php foreach ($relaciones as $relacion): ?>
                                        <option value="<?= esc($relacion) ?>" <?= old('relacion') === $relacion ? 'selected' : '' ?>>
                                            <?= esc($relacion) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono del representante</label>
                                <input
                                    type="tel"
                                    name="telefono_representante"
                                    class="form-control"
                                    value="<?= old('telefono_representante') ?>"
                                    placeholder="+58 412 1234567">
                            </div>

                            <div class="col-12">
                                <label class="form-label">Buscar representante existente</label>
                                <input
                                    type="text"
                                    id="buscarRep"
                                    class="form-control"
                                    placeholder="Buscar por nombre o ID Digisalud...">
                                <input type="hidden" name="representante_id" id="representanteId" value="<?= old('representante_id') ?>">
                                <div id="repResultados" class="mt-2"></div>
                                <div id="repSeleccionado" style="display:none;"></div>
                            </div>

                            <div class="col-12">
                                <div class="chk-evaluar">
                                    <div class="form-check">
                                        <input
                                            class="form-check-input"
                                            type="checkbox"
                                            name="evaluar_representante"
                                            value="1"
                                            id="chkEvaluarRep"
                                            <?= old('evaluar_representante') ? 'checked' : '' ?>>
                                        <label class="form-check-label" for="chkEvaluarRep">
                                            <strong>¿Evaluar al representante en esta jornada?</strong><br>
                                            <small class="text-muted">El representante también será agregado como beneficiario de la jornada.</small>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12" id="repNuevoBox" style="display:none;">
                                <div class="rep-nuevo-form">
                                    <p style="font-size:.82rem;font-weight:800;color:#101a61;margin-bottom:10px;">
                                        <i class="bi bi-person-plus me-1"></i> Registrar representante nuevo
                                    </p>

                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label">Nombres *</label>
                                            <input type="text" name="rep_nombres" class="form-control form-control-sm" value="<?= old('rep_nombres') ?>">
                                        </div>

                                        <div class="col-md-6">
                                            <label class="form-label">Apellidos *</label>
                                            <input type="text" name="rep_apellidos" class="form-control form-control-sm" value="<?= old('rep_apellidos') ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Fecha nac.</label>
                                            <input type="date" name="rep_fecha_nacimiento" class="form-control form-control-sm" value="<?= old('rep_fecha_nacimiento') ?>">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Sexo</label>
                                            <select name="rep_sexo" class="form-select form-select-sm">
                                                <option value="M" <?= old('rep_sexo') === 'M' ? 'selected' : '' ?>>M</option>
                                                <option value="F" <?= old('rep_sexo') === 'F' ? 'selected' : '' ?>>F</option>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Teléfono</label>
                                            <input type="tel" name="rep_telefono_nuevo" class="form-control form-control-sm" value="<?= old('rep_telefono_nuevo') ?>" placeholder="+58...">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ANTECEDENTES -->
                    <div class="toggle-bar" onclick="toggleSeccion(this,'secAntecedentes')">
                        <div class="toggle-left">
                            <div class="step-icon"><i class="bi bi-heart-pulse"></i></div>
                            <div>
                                <div class="toggle-label">Antecedentes clínicos y socioeconómicos</div>
                                <div class="toggle-desc">Opcional — busca en la base de datos</div>
                            </div>
                        </div>
                        <div class="toggle-chevron"><i class="bi bi-chevron-down"></i></div>
                    </div>

                    <div class="toggle-content" id="secAntecedentes">
                        <div class="chk-lentes mb-3">
                            <div class="form-check">
                                <input
                                    class="form-check-input"
                                    type="checkbox"
                                    name="usa_lentes"
                                    value="1"
                                    id="chkUsaLentes"
                                    <?= old('usa_lentes') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="chkUsaLentes">
                                    <strong><i class="bi bi-eyeglasses me-1"></i> ¿Usa lentes correctivos?</strong>
                                </label>
                            </div>
                        </div>

                        <hr class="my-3">

                        <label class="form-label" style="font-size:.86rem;color:#6c757d;">Buscar antecedente clínico:</label>
                        <input type="text" id="buscarAntecedente" class="form-control" placeholder="Ej: diabetes, asma, hipertensión...">
                        <div id="antResultados" class="mt-2" style="max-height:200px;overflow-y:auto;"></div>
                        <div class="tag-container" id="antSeleccionados"></div>

                        <hr class="my-3">

                        <label class="form-label" style="font-size:.86rem;color:#6c757d;">Buscar dato socioeconómico:</label>
                        <input type="text" id="buscarSocioeconomico" class="form-control" placeholder="Ej: aguas, techo, electricidad...">
                        <div id="socResultados" class="mt-2" style="max-height:200px;overflow-y:auto;"></div>
                        <div class="tag-container" id="socSeleccionados"></div>

                        <div class="mt-3">
                            <label class="form-label">Observaciones generales</label>
                            <textarea
                                name="observacion_antecedentes"
                                class="form-control"
                                rows="2"
                                placeholder="Notas adicionales..."><?= old('observacion_antecedentes') ?></textarea>
                        </div>
                    </div>

                </main>
            </div>

            <div class="shell-footer">
                <div class="footer-note">
                    <div class="shield"><i class="bi bi-shield-check"></i></div>
                    <div>
                        <strong>Tu información está segura</strong>
                        <div>El beneficiario quedará asociado a esta jornada.</div>
                    </div>
                </div>

                <div class="actions">
                    <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/buscar") ?>" class="btn-cancelar">Cancelar</a>
                    <button type="submit" class="btn-guardar">
                        <i class="bi bi-check-lg"></i> Guardar y asociar
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<script src="<?= base_url('js/venezuela.js') ?>"></script>

<script>
    (function() {
        'use strict';

        const URL_REPRESENTANTES = <?= json_encode(base_url('beneficiarios/buscar-ajax')) ?>;
        const URL_ANTECEDENTES = <?= json_encode(base_url('beneficiarios/antecedentes-ajax')) ?>;

        let repTimer = null;
        const antSet = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            initIdPreview();
            initDireccion();
            initRepresentante();
            initAntecedentes();
        });

        function byId(id) {
            return document.getElementById(id);
        }

        function escapeHtml(value) {
            return String(value ?? '')
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }

        function safeJson(response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return response.json();
        }

        window.toggleSeccion = function(bar, id) {
            const section = byId(id);
            if (!section) return;

            const isOpen = section.classList.contains('open');

            section.classList.toggle('open');
            bar.classList.toggle('active');

            const hiddenMap = {
                secDireccion: 'hDireccion',
                secEscolaridad: 'hEscolaridad',
                secFamiliar: 'hFamiliar'
            };

            if (hiddenMap[id] && byId(hiddenMap[id])) {
                byId(hiddenMap[id]).value = isOpen ? '' : '1';
            }
        };

        window.abrirSeccionDesdeMenu = function(id) {
            const section = byId(id);
            if (!section) return;

            const bar = section.previousElementSibling;

            if (!section.classList.contains('open') && bar) {
                window.toggleSeccion(bar, id);
            }

            section.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        };

        function initIdPreview() {
            actualizarIdPreview();

            ['fNombres', 'fApellidos', 'fFecha', 'fSexo', 'fPais'].forEach(function(id) {
                const el = byId(id);
                if (!el) return;

                el.addEventListener('input', actualizarIdPreview);
                el.addEventListener('change', actualizarIdPreview);
            });
        }

        window.actualizarIdPreview = function() {
            const preview = byId('idPreview');
            if (!preview) return;

            const nombres = (byId('fNombres')?.value || '').trim();
            const apellidos = (byId('fApellidos')?.value || '').trim();
            const fecha = byId('fFecha')?.value || '';
            const sexo = byId('fSexo')?.value || '_';
            const pais = (byId('fPais')?.value || 'VE').substring(0, 2).toUpperCase();

            const np = nombres.split(/\s+/).filter(Boolean);
            const ap = apellidos.split(/\s+/).filter(Boolean);

            const n1 = (np[0] || '___').substring(0, 3).toUpperCase();
            const n2 = np[1] ? np[1][0].toUpperCase() : '';
            const a1 = (ap[0] || '___').substring(0, 3).toUpperCase();
            const a2 = ap[1] ? ap[1][0].toUpperCase() : '';
            const fn = fecha ? fecha.replaceAll('-', '') : '________';

            preview.textContent = `${pais}${sexo}${n1}${n2}${a1}${a2}${fn}`;
        };

        function initDireccion() {
            const $estado = $('#estado');
            const $municipio = $('#municipio');
            const $parroquia = $('#parroquia');
            const $ciudad = $('#ciudad');

            if (!$estado.length || !$municipio.length || !$parroquia.length) {
                console.error('No existen los selects estado, municipio o parroquia en el DOM.');
                return;
            }

            const dataVenezuela =
                typeof ubicaciones !== 'undefined' ?
                ubicaciones :
                (window.ubicaciones || null);

            if (!dataVenezuela) {
                console.error('No se encontró la variable ubicaciones. Revisa public/js/venezuela.js');
                return;
            }

            const oldEstado = $estado.data('old') || '';
            const oldMunicipio = $municipio.data('old') || '';
            const oldParroquia = $parroquia.data('old') || '';
            const oldCiudad = $ciudad.val() || '';

            function resetSelect($select, placeholder) {
                $select.empty();
                $select.append(new Option(placeholder, '', true, false));
            }

            function refresh($select) {
                $select.trigger('change.select2');
            }

            function getMunicipios(estadoValue) {
                const estadoData = dataVenezuela[estadoValue];

                if (!estadoData) return [];

                // Caso 1: { "Anzoátegui": { "Anaco": ["San Joaquín", ...] } }
                if (!Array.isArray(estadoData) && typeof estadoData === 'object') {
                    return Object.keys(estadoData);
                }

                // Caso 2: { "Anzoátegui": [ { municipio: "Anaco", parroquias: [...] } ] }
                if (Array.isArray(estadoData)) {
                    return estadoData
                        .map(item => item.municipio || item.nombre || item.name || '')
                        .filter(Boolean);
                }

                return [];
            }

            function getParroquias(estadoValue, municipioValue) {
                const estadoData = dataVenezuela[estadoValue];

                if (!estadoData || !municipioValue) return [];

                // Caso 1: { "Anzoátegui": { "Anaco": ["San Joaquín", ...] } }
                if (!Array.isArray(estadoData) && typeof estadoData === 'object') {
                    return estadoData[municipioValue] || [];
                }

                // Caso 2: { "Anzoátegui": [ { municipio: "Anaco", parroquias: [...] } ] }
                if (Array.isArray(estadoData)) {
                    const item = estadoData.find(row =>
                        row.municipio === municipioValue ||
                        row.nombre === municipioValue ||
                        row.name === municipioValue
                    );

                    if (!item) return [];

                    return item.parroquias || item.parroquia || item.children || [];
                }

                return [];
            }

            function cargarEstados() {
                resetSelect($estado, 'Selecciona un estado...');

                Object.keys(dataVenezuela).forEach(nombreEstado => {
                    $estado.append(new Option(nombreEstado, nombreEstado, false, false));
                });

                refresh($estado);
            }

            function cargarMunicipios(estadoValue) {
                resetSelect($municipio, 'Selecciona un municipio...');
                resetSelect($parroquia, 'Selecciona una parroquia...');

                if ($ciudad.length) {
                    $ciudad.val('');
                }

                const municipios = getMunicipios(estadoValue);

                municipios.forEach(nombreMunicipio => {
                    $municipio.append(new Option(nombreMunicipio, nombreMunicipio, false, false));
                });

                refresh($municipio);
                refresh($parroquia);

                console.log('Estado seleccionado:', estadoValue);
                console.log('Municipios encontrados:', municipios);
            }

            function cargarParroquias(estadoValue, municipioValue) {
                resetSelect($parroquia, 'Selecciona una parroquia...');

                if ($ciudad.length) {
                    $ciudad.val('');
                }

                const parroquias = getParroquias(estadoValue, municipioValue);

                parroquias.forEach(nombreParroquia => {
                    $parroquia.append(new Option(nombreParroquia, nombreParroquia, false, false));
                });

                refresh($parroquia);

                console.log('Municipio seleccionado:', municipioValue);
                console.log('Parroquias encontradas:', parroquias);
            }

            if ($estado.hasClass('select2-hidden-accessible')) {
                $estado.select2('destroy');
            }

            if ($municipio.hasClass('select2-hidden-accessible')) {
                $municipio.select2('destroy');
            }

            if ($parroquia.hasClass('select2-hidden-accessible')) {
                $parroquia.select2('destroy');
            }

            $estado.select2({
                placeholder: 'Selecciona un estado',
                width: '100%',
                allowClear: true
            });

            $municipio.select2({
                placeholder: 'Selecciona un municipio',
                width: '100%',
                allowClear: true
            });

            $parroquia.select2({
                placeholder: 'Selecciona una parroquia',
                width: '100%',
                allowClear: true
            });

            cargarEstados();

            $estado.off('change.direccion').on('change.direccion', function() {
                cargarMunicipios($(this).val());
            });

            $municipio.off('change.direccion').on('change.direccion', function() {
                cargarParroquias($estado.val(), $(this).val());
            });

            $parroquia.off('change.direccion').on('change.direccion', function() {
                const parroquiaValue = $(this).val();

                if (parroquiaValue && $ciudad.length && $ciudad.val().trim() === '') {
                    $ciudad.val(parroquiaValue);
                }
            });

            if (oldEstado) {
                $estado.val(oldEstado).trigger('change.direccion');

                setTimeout(function() {
                    if (oldMunicipio) {
                        $municipio.val(oldMunicipio).trigger('change.direccion');

                        setTimeout(function() {
                            if (oldParroquia) {
                                $parroquia.val(oldParroquia).trigger('change.direccion');
                            }

                            if (oldCiudad) {
                                $ciudad.val(oldCiudad);
                            }
                        }, 80);
                    }
                }, 80);
            }
        }

        function resetSelect(select, placeholder) {
            select.innerHTML = '';
            select.appendChild(new Option(placeholder, ''));
        }

        function initSelect2(select, placeholder) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;

            jQuery(select).select2({
                placeholder: placeholder,
                width: '100%'
            });
        }

        function refreshSelect2(select) {
            if (typeof jQuery === 'undefined' || typeof jQuery.fn.select2 === 'undefined') return;

            jQuery(select).trigger('change.select2');
        }

        function initRepresentante() {
            const input = byId('buscarRep');
            if (!input) return;

            input.addEventListener('input', function() {
                clearTimeout(repTimer);

                const q = this.value.trim();
                const contenedor = byId('repResultados');
                const nuevoBox = byId('repNuevoBox');
                const representanteId = byId('representanteId');

                if (!contenedor || !nuevoBox) return;

                if (representanteId) representanteId.value = '';

                if (q.length < 2) {
                    contenedor.innerHTML = '';
                    nuevoBox.style.display = 'none';
                    return;
                }

                contenedor.innerHTML = '<div class="text-muted small mt-2">Buscando representante...</div>';

                repTimer = setTimeout(function() {
                    fetch(`${URL_REPRESENTANTES}?q=${encodeURIComponent(q)}`)
                        .then(safeJson)
                        .then(function(data) {
                            if (!Array.isArray(data) || data.length === 0) {
                                contenedor.innerHTML = `
                                <div class="alert alert-light border mt-2 mb-2">
                                    No se encontró representante con ese criterio.
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="habilitarRegistroRepresentante()">
                                    <i class="bi bi-person-plus"></i> Registrar representante nuevo
                                </button>
                            `;
                                nuevoBox.style.display = 'none';
                                return;
                            }

                            let html = '';

                            data.forEach(function(b) {
                                const id = b.id_beneficiario ?? '';
                                const nombres = b.nombres ?? '';
                                const apellidos = b.apellidos ?? '';
                                const nombreCompleto = `${nombres} ${apellidos}`.trim();
                                const idDigi = b.id_digisalud ?? '';

                                html += `
                                <div class="rep-result"
                                    data-id="${escapeHtml(id)}"
                                    data-nombre="${escapeHtml(nombreCompleto)}"
                                    data-digi="${escapeHtml(idDigi)}"
                                    onclick="seleccionarRepDesdeElemento(this)">
                                    <strong>${escapeHtml(String(apellidos).toUpperCase())}, ${escapeHtml(String(nombres).toUpperCase())}</strong>
                                    <span style="color:#888;font-size:.72rem;">— ${escapeHtml(idDigi)}</span>
                                </div>
                            `;
                            });

                            html += `
                            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="habilitarRegistroRepresentante()">
                                <i class="bi bi-person-plus"></i> No está en la lista, registrar nuevo
                            </button>
                        `;

                            contenedor.innerHTML = html;
                            nuevoBox.style.display = 'none';
                        })
                        .catch(function(error) {
                            console.error(error);
                            contenedor.innerHTML = `
                            <div class="alert alert-danger mt-2">
                                Error buscando representante. Revisa la ruta beneficiarios/buscar-ajax.
                            </div>
                        `;
                        });
                }, 300);
            });
        }

        window.seleccionarRepDesdeElemento = function(el) {
            seleccionarRep(
                el.dataset.id || '',
                el.dataset.nombre || '',
                el.dataset.digi || ''
            );
        };

        window.seleccionarRep = function(id, nombre, idDigi) {
            byId('representanteId').value = id;
            byId('repResultados').innerHTML = '';
            byId('buscarRep').value = nombre;
            byId('repNuevoBox').style.display = 'none';

            const box = byId('repSeleccionado');
            box.style.display = 'block';
            box.innerHTML = `
            <div class="rep-selected">
                <i class="bi bi-check-circle-fill text-success me-1"></i>
                <strong>${escapeHtml(nombre)}</strong>
                <span style="color:#888;font-size:.75rem;">(${escapeHtml(idDigi)})</span>
                <span style="cursor:pointer;float:right;color:#dc3545;" onclick="limpiarRep()">✕</span>
            </div>
        `;
        };

        window.habilitarRegistroRepresentante = function() {
            byId('representanteId').value = '';
            byId('repResultados').innerHTML = '';

            const seleccionado = byId('repSeleccionado');
            seleccionado.style.display = 'none';
            seleccionado.innerHTML = '';

            const nuevoBox = byId('repNuevoBox');
            nuevoBox.style.display = 'block';
            nuevoBox.scrollIntoView({
                behavior: 'smooth',
                block: 'nearest'
            });
        };

        window.limpiarRep = function() {
            byId('representanteId').value = '';
            byId('buscarRep').value = '';

            const seleccionado = byId('repSeleccionado');
            seleccionado.style.display = 'none';
            seleccionado.innerHTML = '';

            byId('repNuevoBox').style.display = 'none';
        };

        function initAntecedentes() {
            initBuscAnt('buscarAntecedente', 'antResultados', 'antSeleccionados', 'Antecedentes Clínicos');
            initBuscAnt('buscarSocioeconomico', 'socResultados', 'socSeleccionados', 'Datos Socioeconómicos');
        }

        function initBuscAnt(inputId, resId, contId, tipo) {
            const input = byId(inputId);
            const resultados = byId(resId);

            if (!input || !resultados) return;

            let timer = null;

            input.addEventListener('input', function() {
                clearTimeout(timer);

                const q = this.value.trim();

                if (q.length < 2) {
                    resultados.innerHTML = '';
                    return;
                }

                resultados.innerHTML = '<div class="text-muted small mt-2">Buscando...</div>';

                timer = setTimeout(function() {
                    fetch(`${URL_ANTECEDENTES}?q=${encodeURIComponent(q)}&tipo=${encodeURIComponent(tipo)}`)
                        .then(safeJson)
                        .then(function(data) {
                            if (!Array.isArray(data) || data.length === 0) {
                                resultados.innerHTML = '<p style="font-size:.82rem;color:#888;" class="mt-2">Sin resultados</p>';
                                return;
                            }

                            let html = '';

                            data.forEach(function(a) {
                                const id = String(a.id_antecedente ?? '');
                                const descripcion = a.descripcion || a.nombre || '';
                                const tipoAnt = a.tipo || tipo;

                                if (!id || antSet.has(id)) return;

                                html += `
                                <div class="rep-result"
                                    data-id="${escapeHtml(id)}"
                                    data-desc="${escapeHtml(descripcion)}"
                                    data-cont="${escapeHtml(contId)}"
                                    data-res="${escapeHtml(resId)}"
                                    data-input="${escapeHtml(inputId)}"
                                    onclick="addAntDesdeElemento(this)">
                                    ${escapeHtml(descripcion)}
                                    <span style="color:#888;font-size:.72rem;">(${escapeHtml(tipoAnt)})</span>
                                </div>
                            `;
                            });

                            resultados.innerHTML = html || '<p style="font-size:.82rem;color:#888;" class="mt-2">Ya seleccionaste todos los resultados encontrados.</p>';
                        })
                        .catch(function(error) {
                            console.error(error);
                            resultados.innerHTML = `
                            <div class="alert alert-danger mt-2">
                                Error cargando antecedentes. Revisa la ruta beneficiarios/antecedentes-ajax.
                            </div>
                        `;
                        });
                }, 300);
            });
        }

        window.addAntDesdeElemento = function(el) {
            addAnt(
                el.dataset.id || '',
                el.dataset.desc || '',
                el.dataset.cont || '',
                el.dataset.res || '',
                el.dataset.input || ''
            );
        };

        window.addAnt = function(id, desc, contId, resId, inputId) {
            id = String(id || '');

            if (!id || antSet.has(id)) return;

            antSet.add(id);

            const contenedor = byId(contId);
            if (!contenedor) return;

            const bubble = document.createElement('span');
            bubble.className = 'tag-item';

            bubble.innerHTML = `
            ${escapeHtml(desc)}
            <input type="hidden" name="antecedentes[]" value="${escapeHtml(id)}">
            <span class="tag-remove" onclick="rmAnt(this, '${escapeHtml(id)}')">×</span>
        `;

            contenedor.appendChild(bubble);

            if (byId(resId)) byId(resId).innerHTML = '';
            if (byId(inputId)) byId(inputId).value = '';
        };

        window.rmAnt = function(el, id) {
            antSet.delete(String(id || ''));

            if (el && el.parentElement) {
                el.parentElement.remove();
            }
        };

    })();
</script>

<?= $this->endSection() ?>
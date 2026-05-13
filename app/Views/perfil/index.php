<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    /* ═══ PERFIL MODULE ═══ */
    .perfil_user-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        max-width: 900px;
        margin: 0 auto;
    }

    .perfil_user-grid .perfil_user-span-2 {
        grid-column: 1 / -1;
    }

    .perfil_user-perfil-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid var(--ds-border);
        position: relative;
    }

    .perfil_user-perfil-card {
        background: #fff;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid var(--ds-border);
        position: relative;
    }

    .perfil_user-btn-custom {
        background: var(--ds-primary);
        color: white;
        padding: 15px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;

    }

    .perfil_user-btn-custom:hover {
        transform: translateY(-1px);
        background: var(--ds-primary-dark);
        color: #fff;
        text-decoration: none;

    }

    /* — FOTO CLICKEABLE — */
    .perfil_user-foto-upload-area {
        position: relative;
        width: 120px;
        height: 120px;
        border-radius: 14px;
        overflow: hidden;
        background: #e8eef5;
        cursor: pointer;
        flex-shrink: 0;
        transition: box-shadow .25s, transform .15s;
        /* SIN pointer-events:none en hijos */
    }

    .perfil_user-foto-upload-area:hover {
        box-shadow: 0 0 0 3px rgba(54, 149, 245, .4);
        transform: scale(1.02);
    }

    .perfil_user-foto-upload-area img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .perfil_user-foto-upload-area svg.perfil_user-avatar-svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .perfil_user-foto-upload-area .perfil_user-foto-hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(16, 26, 97, .4);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 4px;
        opacity: 0;
        transition: opacity .2s;
        pointer-events: none;
        /* el overlay NO intercepta clicks */
    }

    .perfil_user-foto-upload-area:hover .perfil_user-foto-hover-overlay {
        opacity: 1;
    }

    .perfil_user-foto-hover-overlay i {
        color: #fff;
        font-size: 1.5rem;
    }

    .perfil_user-foto-hover-overlay span {
        color: #fff;
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .3px;
        text-transform: uppercase;
    }

    .perfil_user-perfil-nombre {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0b1b3f;
        line-height: 1.2;
        margin: 0;
    }

    .perfil_user-perfil-rol-text {
        font-size: .88rem;
        color: #6c757d;
        margin: 0;
    }

    .perfil_user-badge-org {
        display: inline-block;
        background: var(--ds-primary-dark);
        color: #fff;
        font-weight: 600;
        font-size: .82rem;
        padding: 5px 16px;
        border-radius: 20px;
        letter-spacing: .3px;
    }

    /* — Tarjeta datos — */
    .perfil_user-datos-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }

    .perfil_user-datos-header h5 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0b1b3f;
        margin: 0;
    }

    .perfil_user-btn-edit-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff;
        border: 1.5px solid var(--ds-primary);
        color: var(--ds-primary);
        font-size: .8rem;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 20px;
        cursor: pointer;
        transition: all .2s;
    }

    .perfil_user-btn-edit-pill:hover {
        background: var(--ds-primary-dark);
        color: #fff;
    }

    .perfil_user-dato-row {
        margin-bottom: 14px;
    }

    .perfil_user-dato-label {
        font-size: .72rem;
        color: var(--ds-secondary);
        text-transform: uppercase;
        letter-spacing: .4px;
        margin-bottom: 1px;
        font-weight: 500;
    }

    .perfil_user-dato-value {
        font-size: .95rem;
        font-weight: 600;
        color: #0b1b3f;
    }

    .perfil_user-dato-icon-btn {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        border: 1.5px solid #e0e7ef;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--ds-primary);
        cursor: default;
        transition: all .2s;
    }

    .perfil_user-dato-icon-btn:hover {
        background: #eef5ff;
        border-color: var(--ds-primary-dark);
    }

    /* — Stats cards — */
    .perfil_user-stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid var(--ds-border);
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .perfil_user-stat-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.2rem;
    }

    .perfil_user-stat-icon.perfil_user-jornadas {
        background: #e8f5e9;
        color: #2e7d32;
    }

    .perfil_user-stat-icon.perfil_user-centros {
        background: var(--ds-light);
        color: var(--ds-danger);
    }

    .perfil_user-stat-info .perfil_user-stat-title {
        font-size: .78rem;
        color: #6c757d;
        font-weight: 500;
        margin: 0;
    }

    .perfil_user-stat-info .perfil_user-stat-number {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0b1b3f;
        line-height: 1;
        margin: 0;
    }

    .perfil_user-stat-mini-chart {
        margin-left: auto;
        opacity: .5;
    }

    /* — Historial cards — */
    .perfil_user-hist-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
        border: 1px solid var(--ds-border);
        min-height: 120px;
    }

    .perfil_user-hist-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }

    .perfil_user-hist-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .perfil_user-hist-icon.perfil_user-jornadas {
        background: #ede7f6;
        color: #5e35b1;
    }

    .perfil_user-hist-icon.perfil_user-centros {
        background: #fce4ec;
        color: var(--ds-danger);
    }

    .perfil_user-hist-header h6 {
        font-size: .9rem;
        font-weight: 700;
        color: #0b1b3f;
        margin: 0;
    }

    .perfil_user-hist-empty {
        font-size: .82rem;
        color: #adb5bd;
    }

    .perfil_user-hist-table {
        font-size: .82rem;
        margin: 0;
    }

    .perfil_user-hist-table td,
    .perfil_user-hist-table th {
        padding: 6px 8px;
        border-color: var(--ds-border);
        vertical-align: middle;
    }

    .perfil_user-hist-table thead th {
        background: #f8fafc !important;
        color: #6c757d !important;
        font-weight: 600;
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    /* — Botón actualizar — */
    .perfil_user-btn-actualizar {
        background: var(--ds-primary);
        color: #fff;
        border: none;
        padding: 12px 36px;
        border-radius: 30px;
        font-size: .9rem;
        font-weight: 600;
        cursor: pointer;
        transition: all .25s;
        box-shadow: 0 4px 14px rgba(54, 149, 245, .3);
    }

    .perfil_user-btn-actualizar:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(54, 149, 245, .4);
        color: #fff;
    }

    /* — Edit form fields — */
    .perfil_user-edit-field .form-control,
    .perfil_user-edit-field .form-select {
        border-radius: 10px;
        border: 1.5px solid #e0e7ef;
        font-size: .88rem;
        padding: 8px 14px;
    }

    .perfil_user-edit-field .form-control:focus,
    .perfil_user-edit-field .form-select:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 3px rgba(54, 149, 245, .1);
    }

    .perfil_user-edit-field label {
        font-size: .75rem;
        color: var(--ds-secondary);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }

    /* — Responsive — */
    @media (max-width: 768px) {
        .perfil_user-grid {
            grid-template-columns: 1fr;
        }

        .perfil_user-grid .perfil_user-span-2 {
            grid-column: 1;
        }
    }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?php
// Generar iniciales para el avatar SVG
$inicialN = mb_strtoupper(mb_substr($perfil['nombres'] ?? 'U', 0, 1));
$inicialA = mb_strtoupper(mb_substr($perfil['apellidos'] ?? '', 0, 1));
$iniciales = $inicialN . $inicialA;

// Determinar si hay foto real
$tieneFoto = !empty($perfil['foto_url']) && file_exists(FCPATH . $perfil['foto_url']);
$fotoUrl   = $tieneFoto ? base_url($perfil['foto_url']) . '?v=' . time() : '';
?>

<div class="container py-3">

    <!-- Mensaje de éxito -->
    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3" role="alert" style="max-width:900px;margin:0 auto 16px;">
            <i class="bi bi-check-circle-fill me-1"></i> <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ═══════════ GRID PRINCIPAL ═══════════ -->
    <div class="perfil_user-grid">

        <!-- ━━━ CARD IDENTIDAD ━━━ -->
        <div class="perfil_user-perfil-card perfil_user-perfil-identity">

            <!-- FOTO CLICKEABLE -->
            <div class="perfil_user-foto-upload-area" id="fotoArea" title="Clic para cambiar tu foto">

                <?php if ($tieneFoto): ?>
                    <img src="<?= $fotoUrl ?>" alt="Foto" id="fotoPerfil">
                <?php else: ?>
                    <!-- Avatar SVG con iniciales (no depende de API externa) -->
                    <svg class="perfil_user-avatar-svg" id="fotoPerfil" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg">
                        <rect width="120" height="120" fill=" var(--ds-primary) " />
                        <text x="60" y="60" text-anchor="middle" dominant-baseline="central"
                            font-family="Roboto, Arial, sans-serif" font-size="42" font-weight="700" fill="#fff">
                            <?= $iniciales ?>
                        </text>
                    </svg>
                <?php endif; ?>

                <div class="perfil_user-foto-hover-overlay">
                    <i class="bi bi-camera-fill"></i>
                    <span>Cambiar</span>
                </div>
            </div>

            <!-- Input file FUERA del wrapper para evitar conflictos -->
            <input type="file" id="inputFoto" accept="image/jpeg,image/png,image/webp" style="display:none;">

            <div>
                <h3 class="perfil_user-perfil-nombre"><?= esc($perfil['nombres'] . ' ' . $perfil['apellidos']) ?></h3>
                <p class="perfil_user-perfil-rol-text"><?= esc($perfil['descripcion_rol']) ?></p>
                <span class="perfil_user-badge-org"><?= esc($perfil['nombre_org']) ?></span>
            </div>
        </div>

        <!-- ━━━ CARD MIS DATOS PRINCIPALES ━━━ -->
        <div class="perfil_user-perfil-card" id="cardDatos">
            <div class="perfil_user-datos-header">
                <h5>Mis Datos Principales</h5>
                <button type="button" class="perfil_user-btn-edit-pill" id="btnEditar">
                    <i class="bi bi-pencil"></i> Edit
                </button>
            </div>

            <!-- MODO LECTURA -->
            <div id="modoLectura">
                <div class="d-flex justify-content-between align-items-start">
                    <div style="flex:1;">
                        <div class="perfil_user-dato-row">
                            <div class="perfil_user-dato-label">Nombre</div>
                            <div class="perfil_user-dato-value"><?= esc($perfil['nombres'] . ' ' . $perfil['apellidos']) ?></div>
                        </div>
                        <div class="perfil_user-dato-row">
                            <div class="perfil_user-dato-label">Email</div>
                            <div class="perfil_user-dato-value"><?= esc($perfil['email']) ?></div>
                        </div>
                        <div class="perfil_user-dato-row">
                            <div class="perfil_user-dato-label">Teléfono</div>
                            <div class="perfil_user-dato-value"><?= esc($perfil['telefono'] ?? '—') ?></div>
                        </div>
                        <div class="perfil_user-dato-row">
                            <div class="perfil_user-dato-label">Profesión</div>
                            <div class="perfil_user-dato-value">
                                <span style="background: var(--ds-bg) ;padding:3px 12px;border-radius:8px;font-size:.85rem;">
                                    <?= esc($perfil['profesion'] ?? '—') ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-column gap-2 ms-3">
                        <!-- <div class="perfil_user-dato-icon-btn"><i class="bi bi-envelope"></i></div>
                        <div class="perfil_user-dato-icon-btn"><i class="bi bi-clipboard"></i></div>
                        <div class="perfil_user-dato-icon-btn"><i class="bi bi-telephone"></i></div> -->
                    </div>
                </div>
            </div>

            <!-- MODO EDICIÓN (oculto) -->
            <form action="<?= site_url('perfil/actualizar') ?>" method="post" id="modoEdicion" style="display:none;">
                <div class="row g-2 perfil_user-edit-field">
                    <div class="col-6">
                        <label>Nombres</label>
                        <input type="text" name="nombres" class="form-control" value="<?= esc($perfil['nombres']) ?>" required>
                    </div>
                    <div class="col-6">
                        <label>Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" value="<?= esc($perfil['apellidos']) ?>" required>
                    </div>
                    <div class="col-6">
                        <label>Sexo</label>
                        <select name="genero" class="form-select">
                            <option value="M" <?= $perfil['genero'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $perfil['genero'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label>Fecha nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= esc($perfil['fecha_nacimiento']) ?>">
                    </div>
                    <div class="col-12">
                        <label>Profesión</label>
                        <select id="profesion" name="profesion" class="form-control">
                            <?php if (!empty($perfil['profesion'])): ?>
                                <option value="<?= esc($perfil['profesion']) ?>" selected><?= esc($perfil['profesion']) ?></option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label>Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= esc($perfil['email']) ?>" required>
                    </div>
                    <div class="col-12">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" id="telefono" class="form-control" value="<?= esc($perfil['telefono']) ?>">
                    </div>
                    <div class="col-12 d-flex gap-2 mt-2">
                        <button type="submit" class="perfil_user-btn-actualizar flex-grow-1">
                            <i class="bi bi-check-lg"></i> Guardar
                        </button>
                        <button type="button" class="btn btn-light px-3" id="btnCancelar" style="border-radius:30px;">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- ━━━ STATS JORNADAS + CENTROS (izquierda) ━━━ -->
        <div style="display:flex;flex-direction:column;gap:16px;">
            <div class="perfil_user-stat-card">
                <div class="perfil_user-stat-icon perfil_user-jornadas"><i class="bi bi-calendar2-check"></i></div>
                <div class="perfil_user-stat-info">
                    <p class="perfil_user-stat-title">Jornadas Participadas</p>
                    <p class="perfil_user-stat-number"><?= $estadisticas['jornadas'] ?></p>
                </div>
                <svg class="perfil_user-stat-mini-chart" width="60" height="30" viewBox="0 0 60 30">
                    <polyline fill="none" stroke="#a5d6a7" stroke-width="2" points="0,25 10,20 20,15 30,18 40,10 50,12 60,5" />
                    <polyline fill="none" stroke=" var(--ds-light) " stroke-width="2" points="0,20 10,22 20,18 30,22 40,16 50,20 60,14" />
                </svg>
            </div>
            <div class="perfil_user-stat-card">
                <div class="perfil_user-stat-icon perfil_user-centros"><i class="bi bi-house-door"></i></div>
                <div class="perfil_user-stat-info">
                    <p class="perfil_user-stat-title">Centros Asociados</p>
                    <p class="perfil_user-stat-number"><?= $estadisticas['centros'] ?></p>
                </div>
                <svg class="perfil_user-stat-mini-chart" width="60" height="30" viewBox="0 0 60 30">
                    <polyline fill="none" stroke="#ffcc80" stroke-width="2" points="0,22 10,18 20,20 30,12 40,15 50,8 60,10" />
                    <polyline fill="none" stroke="#f3e5f5" stroke-width="2" points="0,18 10,20 20,16 30,20 40,14 50,18 60,12" />
                </svg>
            </div>
        </div>

        <!-- ━━━ HISTORIAL CENTROS (derecha) ━━━ -->
        <div class="perfil_user-hist-card">
            <div class="perfil_user-hist-header">
                <div class="perfil_user-hist-icon perfil_user-centros"><i class="bi bi-building"></i></div>
                <h6>Historial de Centros</h6>
            </div>
            <?php if (!empty($estadisticas['detalle_centros'])): ?>
                <table class="table perfil_user-hist-table">
                    <thead>
                        <tr>
                            <th>Centro</th>
                            <th>Rol</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas['detalle_centros'] as $c): ?>
                            <tr>
                                <td><?= esc($c['nombre_centro']) ?></td>
                                <td><span class="badge" style="background: var(--ds-light) ;color: var(--ds-danger) ;font-weight:600;font-size:.72rem;"><?= esc($c['nombre_rol']) ?></span></td>
                                <td><?= date("d/m/Y", strtotime($c['fecha_asignacion'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="perfil_user-hist-empty mt-3 text-center">Sin actividad reciente</p>
            <?php endif; ?>
        </div>

        <!-- ━━━ HISTORIAL JORNADAS ━━━ -->
        <div class="perfil_user-hist-card">
            <div class="perfil_user-hist-header">
                <div class="perfil_user-hist-icon perfil_user-jornadas"><i class="bi bi-journal-check"></i></div>
                <h6>Historial de Jornadas</h6>
            </div>
            <?php if (!empty($estadisticas['detalle_jornadas'])): ?>
                <table class="table perfil_user-hist-table">
                    <thead>
                        <tr>
                            <th>Jornada</th>
                            <th>Rol</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas['detalle_jornadas'] as $j): ?>
                            <tr>
                                <td><?= esc($j['nombre_jornada']) ?></td>
                                <td><span class="badge" style="background: var(--ds-light) ;color:  var(--ds-dark) ;font-weight:600;font-size:.72rem;"><?= esc($j['nombre_rol']) ?></span></td>
                                <td><?= date("d/m/Y", strtotime($j['fecha_asignacion'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="perfil_user-hist-empty mt-3 text-center">Sin actividad reciente</p>
            <?php endif; ?>
        </div>

        <!-- ━━━ HISTORIAL CENTROS (segundo) ━━━ -->
        <div class="perfil_user-hist-card">
            <div class="perfil_user-hist-header">
                <div class="perfil_user-hist-icon perfil_user-centros"><i class="bi bi-clock-history"></i></div>
                <h6>Última conexión</h6>
            </div>
            <?php if (!empty($estadisticas['detalle_centros'])): ?>
                <table class="table perfil_user-hist-table">
                    <thead>
                        <tr>
                            <th>Centro</th>
                            <th>Rol</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($estadisticas['detalle_centros'] as $c): ?>
                            <tr>
                                <td><?= esc($c['nombre_centro']) ?></td>
                                <td><span class="badge" style="background: #fce4ec;color:  var(--ds-danger) ;font-weight:600;font-size:.72rem;"><?= esc($c['nombre_rol']) ?></span></td>
                                <td><?= date("d/m/Y", strtotime($c['fecha_asignacion'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="perfil_user-hist-empty mt-3 text-center">Sin actividad reciente</p>
            <?php endif; ?>
        </div>

        <!-- ━━━ BOTÓN ACTUALIZAR ━━━ -->
        <div class="perfil_user-span-2 text-end pb-2">
            <button type="button" class="btn perfil_user-btn-custom" id="btnActualizarBottom">
                Actualizar perfil
            </button>
        </div>

    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    $(document).ready(function() {

        // ═══════════════════════════════════════
        // 1. TOGGLE EDITAR / LECTURA
        // ═══════════════════════════════════════
        var $lectura = $('#modoLectura');
        var $edicion = $('#modoEdicion');
        var $btnEdit = $('#btnEditar');
        var $btnCancel = $('#btnCancelar');
        var $btnBottom = $('#btnActualizarBottom');
        var editando = false;

        function abrirEdicion() {
            $lectura.slideUp(200, function() {
                $edicion.slideDown(200);
            });
            $btnEdit.html('<i class="bi bi-x-lg"></i> Cerrar');
            editando = true;
        }

        function cerrarEdicion() {
            $edicion.slideUp(200, function() {
                $lectura.slideDown(200);
            });
            $btnEdit.html('<i class="bi bi-pencil"></i> Edit');
            editando = false;
        }

        $btnEdit.on('click', function() {
            if (editando) {
                cerrarEdicion();
            } else {
                abrirEdicion();
            }
        });
        $btnCancel.on('click', cerrarEdicion);

        $btnBottom.on('click', function() {
            abrirEdicion();
            $('html, body').animate({
                scrollTop: $('#cardDatos').offset().top - 80
            }, 400);
        });

        // ═══════════════════════════════════════
        // 2. FOTO CLICKEABLE — FUNCIONAL
        // ═══════════════════════════════════════
        var $fotoArea = $('#fotoArea');
        var $inputFoto = $('#inputFoto');

        // Click en el área de foto → disparar input file
        $fotoArea.on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            $inputFoto[0].click(); // Usar [0].click() nativo, más confiable
        });

        // Al seleccionar archivo
        $inputFoto.on('change', function() {
            var file = this.files[0];
            if (!file) return;

            // Validación en cliente
            var validTypes = ['image/jpeg', 'image/png', 'image/webp'];
            if (validTypes.indexOf(file.type) === -1) {
                Swal.fire('Error', 'Solo se permiten imágenes JPG, PNG o WEBP.', 'error');
                this.value = '';
                return;
            }
            if (file.size > 2 * 1024 * 1024) {
                Swal.fire('Error', 'La imagen no debe superar los 2 MB.', 'error');
                this.value = '';
                return;
            }

            // Preview inmediato — reemplazar SVG/img por img
            var reader = new FileReader();
            reader.onload = function(ev) {
                // Reemplazar todo el contenido del área de foto con una img
                var $existing = $fotoArea.find('img, svg.perfil_user-avatar-svg');
                if ($existing.is('svg')) {
                    // Reemplazar SVG por img
                    var newImg = $('<img>').attr({
                        src: ev.target.result,
                        alt: 'Foto',
                        id: 'fotoPerfil'
                    });
                    $existing.replaceWith(newImg);
                } else {
                    $existing.attr('src', ev.target.result);
                }
            };
            reader.readAsDataURL(file);

            // Subir al servidor vía AJAX
            var formData = new FormData();
            formData.append('foto', file);

            // Mostrar loading
            Swal.fire({
                title: 'Subiendo foto...',
                allowOutsideClick: false,
                didOpen: function() {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '<?= site_url("perfil/subir-foto") ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    Swal.close();
                    if (res.success) {
                        // Actualizar con la URL del servidor
                        $fotoArea.find('img').attr('src', res.foto_url);
                        Swal.fire({
                            icon: 'success',
                            title: 'Foto actualizada',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 2500
                        });
                    } else {
                        Swal.fire('Error', res.error || 'No se pudo subir la foto.', 'error');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    var msg = 'Error de conexión al subir la foto.';
                    if (xhr.status === 404) {
                        msg = 'Ruta no encontrada. Verifica que "perfil/subir-foto" esté en Routes.php';
                    }
                    Swal.fire('Error', msg, 'error');
                }
            });

            // Limpiar input para permitir subir el mismo archivo otra vez
            this.value = '';
        });

        // ═══════════════════════════════════════
        // 3. SELECT2 PROFESIONES
        // ═══════════════════════════════════════
        if (typeof profesiones !== 'undefined') {
            var dataProfesiones = profesiones.map(function(p) {
                return {
                    id: p,
                    text: p
                };
            });

            $('#profesion').select2({
                placeholder: 'Escribe tu profesión...',
                allowClear: true,
                width: '100%',
                minimumInputLength: 1,
                dropdownAutoWidth: true,
                data: dataProfesiones
            });

            var valorActual = '<?= esc($perfil['profesion'] ?? '') ?>';
            if (valorActual) {
                $('#profesion').val(valorActual).trigger('change');
            }
        }

        // ═══════════════════════════════════════
        // 4. FORMATEAR TELÉFONO (+58 XXX XXXXXXX)
        // ═══════════════════════════════════════
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

    });
</script>
<?= $this->endSection() ?>
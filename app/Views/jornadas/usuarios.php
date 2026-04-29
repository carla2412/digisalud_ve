<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
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
    --text-soft: #6b7280;
    --danger: #ef4444;
    --warning: #f4b400;
    --shadow-sm: 0 6px 18px rgba(16, 26, 97, 0.06);
    --shadow-md: 0 12px 28px rgba(16, 26, 97, 0.08);
}

.usuarios-page {
    max-width: 1440px;
    margin: 0 auto;
    padding: 26px 24px 40px;
}

.breadcrumb-digi {
    font-size: 14px;
    color: var(--text-soft);
    margin-bottom: 24px;
}

.breadcrumb-digi a {
    color: var(--text-soft);
    text-decoration: none;
}

.breadcrumb-digi a:hover {
    color: var(--ds-primary);
}

.breadcrumb-digi .active {
    color: var(--ds-dark);
    font-weight: 600;
}

.page-header {
    margin-bottom: 26px;
}

.page-header h1 {
    margin: 0 0 8px;
    font-size: 28px;
    line-height: 1.2;
    color: var(--ds-dark);
    font-weight: 600;
}

.page-header p {
    margin: 0;
    color: var(--ds-muted);
    font-size: 15px;
}

.usuarios-shell {
    background: var(--white);
    border: 1px solid var(--ds-border);
    border-radius: 26px;
    box-shadow: var(--shadow-md);
    padding: 18px;
}

.usuarios-grid {
    display: grid;
    grid-template-columns: minmax(0, 2.2fr) 390px;
    gap: 22px;
}

.panel {
    background: var(--white);
    border: 1px solid var(--ds-border);
    border-radius: 20px;
    box-shadow: var(--shadow-sm);
}

.panel-body {
    padding: 20px;
}

.section-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--ds-dark);
    margin: 0 0 14px;
    text-transform: uppercase;
    letter-spacing: .3px;
}

.search-row {
    display: grid;
    grid-template-columns: 1fr 174px;
    gap: 16px;
    align-items: center;
    margin-bottom: 22px;
}

.input-wrap {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-soft);
    font-size: 18px;
    pointer-events: none;
}

.search-input-jornada {
    width: 100%;
    height: 50px;
    padding: 0 16px 0 48px;
    border: 1px solid var(--ds-border);
    border-radius: 14px;
    outline: none;
    font-size: 15px;
    color: var(--ds-muted);
    background: #fff;
    transition: border-color .2s, box-shadow .2s;
}

.search-input-jornada:focus {
    border-color: var(--ds-primary);
    box-shadow: 0 0 0 4px rgba(54, 149, 245, .12);
}

.btn-buscar-jornada {
    height: 50px;
    border: 0;
    border-radius: 14px;
    padding: 0 20px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    transition: .2s ease;
    background: linear-gradient(180deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
    color: #fff;
    box-shadow: 0 10px 20px rgba(54, 149, 245, .22);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
}

.btn-buscar-jornada:hover {
    transform: translateY(-1px);
}

.empty-state {
    border: 1px dashed #d9e4f2;
    border-radius: 20px;
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    min-height: 230px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 32px 20px;
    margin-bottom: 28px;
}

.empty-icon {
    width: 94px;
    height: 94px;
    border-radius: 50%;
    background: rgba(54, 149, 245, .1);
    color: var(--ds-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    margin: 0 auto 18px;
}

.empty-state h3 {
    margin: 0 0 10px;
    font-size: 18px;
    color: #334155;
    font-weight: 600;
}

.empty-state p {
    margin: 0;
    color: var(--text-soft);
    font-size: 15px;
}

.resultados-busqueda {
    margin-bottom: 24px;
}

.usuario-resultado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 18px;
    border: 1px solid var(--ds-border);
    border-radius: 16px;
    margin-bottom: 10px;
    background: #fff;
    transition: .18s ease;
    cursor: pointer;
}

.usuario-resultado:hover {
    transform: translateY(-1px);
    border-color: rgba(54, 149, 245, .35);
    box-shadow: 0 10px 22px rgba(16, 26, 97, .06);
}

.usuario-resultado .nombre {
    font-size: 16px;
    font-weight: 600;
    color: var(--ds-dark);
}

.usuario-resultado .profesion {
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-top: 3px;
}

.usuario-resultado .organizacion {
    font-size: 13px;
    color: var(--text-soft);
    margin-top: 2px;
}

.usuario-resultado .accion-asignar {
    color: var(--ds-primary);
    font-size: 13px;
    font-weight: 600;
    white-space: nowrap;
}

.check-asignado {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    border: 2px solid #22c55e;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #22c55e;
    font-size: 15px;
    flex-shrink: 0;
}

.subsection-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 17px;
    font-weight: 600;
    margin: 0 0 16px;
    color: var(--ds-dark);
}

.tabla-asignados {
    margin-top: 10px;
}

.asignado-card {
    border: 1px solid var(--ds-border);
    border-radius: 18px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    background: #fff;
    margin-bottom: 12px;
}

.asignado-left {
    display: flex;
    align-items: center;
    gap: 16px;
    min-width: 0;
}

.avatar {
    width: 58px;
    height: 58px;
    border-radius: 50%;
    background: linear-gradient(180deg, #edf5ff 0%, #e6f0ff 100%);
    color: var(--ds-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 26px;
    flex-shrink: 0;
}

.asignado-info {
    min-width: 0;
}

.asignado-info .nombre {
    margin: 0 0 9px;
    font-size: 17px;
    font-weight: 600;
    color: var(--ds-dark);
}

.meta-row {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
}

.rol-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 600;
    letter-spacing: .2px;
    background: linear-gradient(180deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
    color: #fff;
}

.company {
    color: var(--ds-muted);
    font-size: 14px;
}

.btn-quitar {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 0;
    background: transparent;
    color: var(--danger);
    font-size: 15px;
    font-weight: 600;
    cursor: pointer;
    padding: 8px 10px;
    border-radius: 10px;
    white-space: nowrap;
}

.btn-quitar:hover {
    background: rgba(239, 68, 68, .06);
}

.side-panel {
    padding: 26px 20px;
    position: relative;
}

.close-panel {
    position: absolute;
    top: 16px;
    right: 18px;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #f1f5f9;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 22px;
    line-height: 1;
}

.close-panel:hover {
    color: #fff;
    background: var(--ds-primary);
}

.org-title {
    margin: 0 38px 6px 0;
    font-size: 22px;
    color: var(--ds-dark);
    font-weight: 600;
}

.org-subtitle {
    margin: 0 0 18px;
    font-size: 14px;
    color: var(--text-soft);
}

.pesquisas-row {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    margin-bottom: 18px;
}

.pesquisa-chip {
    width: 48px;
    height: 48px;
    border-radius: 16px;
    background: #f8fbff;
    border: 1px solid var(--ds-border);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 6px 14px rgba(16, 26, 97, .05);
    position: relative;
}

.pesquisa-chip img {
    width: 30px;
    height: 30px;
    object-fit: contain;
}

.pesquisa-chip:hover::after {
    content: attr(title);
    position: absolute;
    bottom: -34px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--ds-dark);
    color: #fff;
    font-size: 11px;
    padding: 5px 8px;
    border-radius: 8px;
    white-space: nowrap;
    z-index: 5;
}

.divider {
    height: 1px;
    background: var(--line);
    margin: 18px 0;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 14px;
}

.info-item {
    display: flex;
    gap: 12px;
    align-items: flex-start;
}

.info-item.full {
    grid-column: 1 / -1;
}

.info-icon {
    color: var(--ds-primary);
    font-size: 22px;
    line-height: 1;
    margin-top: 1px;
    flex-shrink: 0;
}

.info-copy small {
    display: block;
    font-size: 12px;
    color: var(--text-soft);
    margin-bottom: 5px;
}

.info-copy strong {
    font-size: 16px;
    color: var(--ds-dark);
}

.status-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 7px 11px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 600;
}

.status-activa {
    background: #dcfce7;
    color: #166534;
}

.status-finalizada {
    background: #fee2e2;
    color: #991b1b;
}

.status-inactiva {
    background: #f1f5f9;
    color: #475569;
}

.role-title {
    margin: 0 0 14px;
    font-size: 16px;
    color: var(--ds-dark);
    font-weight: 600;
}

.roles-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.role-option {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 16px;
    color: var(--ds-muted);
    cursor: pointer;
}

.role-option input[type="radio"] {
    appearance: none;
    width: 22px;
    height: 22px;
    border: 2px solid #b8c4d3;
    border-radius: 50%;
    position: relative;
    cursor: pointer;
    margin: 0;
    flex-shrink: 0;
    background: #fff;
}

.role-option input[type="radio"]:checked {
    border-color: var(--ds-primary);
}

.role-option input[type="radio"]:checked::after {
    content: "";
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: var(--ds-primary);
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
}

.sin-permiso-msg {
    font-size: 13px;
    color: #64748b;
    font-style: italic;
    margin-top: 18px;
    padding: 12px 14px;
    background: #f8fafc;
    border: 1px solid var(--ds-border);
    border-radius: 14px;
}

@media (max-width: 1120px) {
    .usuarios-grid {
        grid-template-columns: 1fr;
    }

    .side-panel {
        padding: 22px 20px;
    }
}

@media (max-width: 768px) {
    .usuarios-page {
        padding: 18px 16px 32px;
    }

    .search-row {
        grid-template-columns: 1fr;
    }

    .asignado-card {
        flex-direction: column;
        align-items: flex-start;
    }

    .info-grid {
        grid-template-columns: 1fr;
    }

    .org-title {
        font-size: 20px;
    }

    .page-header h1 {
        font-size: 24px;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $iconos_color = [
        '1' => ['img' => 'antropometria2.svg',     'gris' => 'antropometria-color.svg',     'nombre' => 'Antropometría'],
        '2' => ['img' => 'sanguinea2.svg',         'gris' => 'sanguinea-color.svg',         'nombre' => 'Laboratorio'],
        '3' => ['img' => 'visual2.svg',            'gris' => 'visual-color.svg',            'nombre' => 'Visual'],
        '4' => ['img' => 'signosVitales2.svg',     'gris' => 'signos-vitales-color.svg',    'nombre' => 'Signos vitales'],
        '5' => ['img' => 'medicinaGeneral2.svg',   'gris' => 'medicina-general-color.svg',  'nombre' => 'Medicina general'],
        '6' => ['img' => 'vacunacion2.svg',        'gris' => 'vacunacion-color.svg',        'nombre' => 'Vacunación'],
    ];

    $statusTexto = $jornada['status_jor'] == 1
        ? 'En Marcha'
        : ($jornada['status_jor'] == 2 ? 'Finalizada' : 'Inactiva');

    $statusClass = $jornada['status_jor'] == 1
        ? 'status-activa'
        : ($jornada['status_jor'] == 2 ? 'status-finalizada' : 'status-inactiva');

    $fechaInicio = !empty($jornada['fecha_inicio'])
        ? date('d-m-Y', strtotime($jornada['fecha_inicio']))
        : 'Sin fecha';

    $fechaFin = !empty($jornada['fecha_fin'])
        ? date('d-m-Y', strtotime($jornada['fecha_fin']))
        : $fechaInicio;
?>

<div class="usuarios-page">

    <div class="breadcrumb-digi">
        <a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt;
        <span class="active">Usuarios — <?= esc($jornada['nombre_jornada']) ?></span>
    </div>

    <div class="page-header">
        <h1>Busca tu usuario</h1>
        <p>Busca, asigna y administra los usuarios de la jornada.</p>
    </div>

    <section class="usuarios-shell">
        <div class="usuarios-grid">

            <!-- Columna izquierda -->
            <div class="panel">
                <div class="panel-body">

                    <h2 class="section-title">Filtro</h2>

                    <div class="search-row">
                        <div class="input-wrap">
                           
                            <input
                                type="text"
                                id="campoBusqueda"
                                class="search-input-jornada"
                                placeholder="Nombre o correo..."
                                autocomplete="off"
                            >
                        </div>

                        <button type="button" class="btn-buscar-jornada" onclick="buscarUsuarios()">
                            <i class="bi bi-search"></i>
                            <span>Buscar</span>
                        </button>
                    </div>

                    <!-- Resultados de búsqueda -->
                    <div id="resultadosBusqueda" class="resultados-busqueda"></div>

                    <div id="estadoInicial" class="empty-state">
                        <div>
                            <div class="empty-icon">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <h3>Escribe al menos 2 caracteres y presiona Buscar</h3>
                            <p>Usa el buscador para encontrar y asignar usuarios a esta jornada.</p>
                        </div>
                    </div>

                    <!-- Usuarios asignados -->
                    <div class="tabla-asignados" id="tablaAsignados">
                        <h3 class="subsection-title">
                            <i class="bi bi-people-fill"></i>
                            <span>Usuarios asignados (<?= count($usuariosAsignados) ?>)</span>
                        </h3>

                        <?php if (!empty($usuariosAsignados)): ?>
                            <?php foreach ($usuariosAsignados as $ua): ?>
                                <div class="asignado-card" id="asignado-<?= esc($ua['id_ruc']) ?>">
                                    <div class="asignado-left">
                                        <div class="avatar">
                                            <i class="bi bi-person-fill"></i>
                                        </div>

                                        <div class="asignado-info">
                                            <div class="nombre">
                                                <?= esc($ua['nombres'] . ' ' . $ua['apellidos']) ?>
                                            </div>

                                            <div class="meta-row">
                                                <span class="rol-badge"><?= esc($ua['nombre_rol']) ?></span>
                                                <span class="company"><?= esc($ua['nombre_org'] ?? '') ?></span>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (in_array($rolSesion, [1, 2, 3, 4])): ?>
                                        <button
                                            type="button"
                                            class="btn-quitar"
                                            onclick="eliminarUsuario(<?= (int) $ua['id_ruc'] ?>, '<?= esc($ua['nombres'] . ' ' . $ua['apellidos']) ?>')"
                                        >
                                            <i class="bi bi-trash3"></i>
                                            <span>Quitar</span>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted" style="font-size:.9rem;">No hay usuarios asignados aún.</p>
                        <?php endif; ?>
                    </div>

                </div>
            </div>

            <!-- Columna derecha -->
            <aside class="panel">
                <div class="side-panel">

                    <a href="<?= base_url('jornadas') ?>" class="close-panel">&times;</a>

                    <h2 class="org-title"><?= esc($jornada['nombre_jornada']) ?></h2>
                    <p class="org-subtitle"><?= esc($jornada['nombre_org'] ?? '') ?></p>

                    <!-- Pesquisas -->
                    <p class="org-subtitle mb-2">Pesquisas</p>

                    <div class="pesquisas-row">
                        <?php if (!empty($jornada['pesquisas'])): ?>
                            <?php foreach (explode(',', $jornada['pesquisas']) as $p): ?>
                                <?php
                                    $p = trim($p);
                                    if (!isset($iconos_color[$p])) {
                                        continue;
                                    }

                                    $icono = $iconos_color[$p];
                                ?>

                                <span class="pesquisa-chip" title="<?= esc($icono['nombre']) ?>">
                                    <img
                                        src="<?= base_url('img/' . $icono['img']) ?>"
                                        alt="<?= esc($icono['nombre']) ?>"
                                    >
                                </span>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <small class="text-muted">Sin pesquisas asociadas</small>
                        <?php endif; ?>
                    </div>

                    <div class="divider"></div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-geo-alt-fill"></i>
                            </div>
                            <div class="info-copy">
                                <small>Ubicación</small>
                                <strong><?= esc($jornada['ciudad'] ?? 'Sin ubicación') ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar-event-fill"></i>
                            </div>
                            <div class="info-copy">
                                <small>Fecha Inicio</small>
                                <strong><?= esc($fechaInicio) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-calendar-check-fill"></i>
                            </div>
                            <div class="info-copy">
                                <small>Fecha Fin</small>
                                <strong><?= esc($fechaFin) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">
                                <i class="bi bi-flag-fill"></i>
                            </div>
                            <div class="info-copy">
                                <small>Status</small>
                                <strong>
                                    <span class="status-pill <?= esc($statusClass) ?>">
                                        <?= esc($statusTexto) ?>
                                    </span>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="info-item full">
                        <div class="info-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="info-copy">
                            <small>N de Usuarios Asignados</small>
                            <strong id="conteoAsignados"><?= count($usuariosAsignados) ?></strong>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <h3 class="role-title">Asignar Rol</h3>

                    <div class="roles-list">
                        <label class="role-option">
                            <input type="radio" name="rol_asignar" value="4">
                            <span>Organizador</span>
                        </label>

                        <label class="role-option">
                            <input type="radio" name="rol_asignar" value="5" checked>
                            <span>Coordinador</span>
                        </label>

                        <label class="role-option">
                            <input type="radio" name="rol_asignar" value="6">
                            <span>Registrador</span>
                        </label>

                        <label class="role-option">
                            <input type="radio" name="rol_asignar" value="7">
                            <span>Data</span>
                        </label>
                    </div>

                    <?php if (!in_array($rolSesion, [1, 2, 3, 4])): ?>
                        <p class="sin-permiso-msg">
                            <i class="bi bi-lock-fill"></i>
                            No tienes permisos para asignar o eliminar usuarios.
                        </p>
                    <?php endif; ?>

                </div>
            </aside>

        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
const jornadaId    = <?= (int) $jornada_id ?>;
const rolSesion    = <?= (int) $rolSesion ?>;
const puedeAsignar = [1, 2, 3, 4].includes(rolSesion);
const csrfName     = '<?= csrf_token() ?>';
const csrfToken    = '<?= csrf_hash() ?>';

document.getElementById('campoBusqueda').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') {
        buscarUsuarios();
    }
});

function buscarUsuarios() {
    const q = document.getElementById('campoBusqueda').value.trim();

    if (q.length < 2) {
        Swal.fire({
            icon: 'info',
            title: 'Búsqueda muy corta',
            text: 'Escribe al menos 2 caracteres para buscar.',
            confirmButtonColor: '#3695f5'
        });
        return;
    }

    const estadoInicial = document.getElementById('estadoInicial');
    if (estadoInicial) {
        estadoInicial.style.display = 'none';
    }

    const contenedor = document.getElementById('resultadosBusqueda');

    contenedor.innerHTML = `
        <div class="text-center text-muted py-3">
            <i class="bi bi-search"></i> Buscando usuarios...
        </div>
    `;

    fetch(`<?= base_url('jornadas/usuarios/buscar-ajax') ?>?q=${encodeURIComponent(q)}&jornada_id=${jornadaId}`)
        .then(r => r.json())
        .then(data => {
            if (!Array.isArray(data) || data.length === 0) {
                contenedor.innerHTML = `
                    <p class="text-muted text-center mt-3" style="font-size:.9rem;">
                        No se encontraron usuarios.
                    </p>
                `;
                return;
            }

            let html = '';

            data.forEach(u => {
                const yaAsignado = u.ya_asignado;
                const profesion  = u.profesion || 'Sin profesión';
                const org        = u.nombre_org || 'Independiente';
                const nombre     = `${u.nombres ?? ''} ${u.apellidos ?? ''}`.trim();

                html += `
                    <div
                        class="usuario-resultado"
                        data-id="${u.id_usuario}"
                        onclick="${!yaAsignado && puedeAsignar ? 'seleccionarUsuario(' + u.id_usuario + ')' : ''}"
                    >
                        <div>
                            <div class="nombre">${escapeHtml(nombre)}</div>
                            <div class="profesion">${escapeHtml(profesion)}</div>
                            <div class="organizacion">${escapeHtml(org)}</div>
                        </div>

                        ${
                            yaAsignado
                                ? '<div class="check-asignado"><i class="bi bi-check-lg"></i></div>'
                                : (
                                    puedeAsignar
                                        ? '<span class="accion-asignar"><i class="bi bi-plus-circle"></i> Asignar</span>'
                                        : ''
                                )
                        }
                    </div>
                `;
            });

            contenedor.innerHTML = html;
        })
        .catch(() => {
            contenedor.innerHTML = `
                <p class="text-danger text-center mt-3" style="font-size:.9rem;">
                    Error al buscar usuarios.
                </p>
            `;
        });
}

function seleccionarUsuario(idUsuario) {
    if (!puedeAsignar) return;

    const rolSeleccionado = document.querySelector('input[name="rol_asignar"]:checked');

    if (!rolSeleccionado) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un rol',
            text: 'Primero selecciona un rol en el panel derecho antes de asignar un usuario.',
            confirmButtonColor: '#3695f5'
        });
        return;
    }

    const idRol = rolSeleccionado.value;

    Swal.fire({
        title: '¿Asignar usuario?',
        text: 'Se asignará con el rol seleccionado a esta jornada.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3695f5'
    }).then(result => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append(csrfName, csrfToken);
            formData.append('id_usuario', idUsuario);
            formData.append('id_rol', idRol);

            fetch(`<?= base_url('jornadas') ?>/${jornadaId}/usuarios/asignar`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario asignado',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.error || 'No se pudo asignar el usuario'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al asignar el usuario.'
                });
            });
        }
    });
}

function eliminarUsuario(idRuc, nombre) {
    if (!puedeAsignar) return;

    Swal.fire({
        title: '¿Quitar usuario?',
        text: `Se eliminará a ${nombre} de esta jornada.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(result => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append(csrfName, csrfToken);
            formData.append('id_ruc', idRuc);

            fetch(`<?= base_url('jornadas') ?>/${jornadaId}/usuarios/eliminar`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.error || 'No se pudo quitar el usuario'
                    });
                }
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Ocurrió un error al quitar el usuario.'
                });
            });
        }
    });
}

function escapeHtml(value) {
    return String(value)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}
</script>
<?= $this->endSection() ?>
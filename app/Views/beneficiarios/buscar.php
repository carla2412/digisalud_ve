<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    /**
     * Vista: Buscar / asociar beneficiarios a una jornada.
     * Mantiene la lógica existente de CI4:
     * - $jornada_id
     * - búsqueda AJAX en beneficiarios/buscar-ajax
     * - asociación POST a jornadas/{jornada_id}/asociar/{id_beneficiario}
     * - registro nuevo en jornadas/{jornada_id}/beneficiarios/create
     *
     * Si el controlador envía $jornada, se muestra el resumen lateral.
     * Si no lo envía, la vista no falla y muestra valores seguros.
     */

    $jornadaNombre = $jornada['nombre_jornada']
        ?? $jornada['nombre']
        ?? $jornada['nombre_org']
        ?? ('Jornada #' . (int) $jornada_id);

    $jornadaSubtitulo = $jornada['pesquisas']
        ?? $jornada['tipo_pesquisa']
        ?? 'Beneficiarios';

    $jornadaUbicacion = $jornada['ciudad']
        ?? $jornada['ubicacion']
        ?? 'No especificada';

    $fechaInicioRaw = $jornada['fecha_inicio']
        ?? $jornada['fecha_ini']
        ?? null;

    $fechaFinRaw = $jornada['fecha_fin']
        ?? $jornada['fecha_cierre']
        ?? $fechaInicioRaw;

    $fechaInicio = ! empty($fechaInicioRaw) ? date('d-m-Y', strtotime($fechaInicioRaw)) : '—';
    $fechaFin    = ! empty($fechaFinRaw) ? date('d-m-Y', strtotime($fechaFinRaw)) : '—';

    $statusRaw = (string) ($jornada['status_jor'] ?? $jornada['status'] ?? '');

    $statusTexto = match ($statusRaw) {
        '1' => 'Activa',
        '2' => 'Finalizada',
        '3' => 'Inactiva',
        default => $statusRaw !== '' ? ucfirst($statusRaw) : 'No especificado',
    };

    $cantidadAsignados = $totalBeneficiariosAsignados
        ?? $total_asignados
        ?? (isset($beneficiariosAsignados) && is_countable($beneficiariosAsignados) ? count($beneficiariosAsignados) : 0);
?>

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
        --success: #16a34a;
        --shadow-sm: 0 6px 18px rgba(16, 26, 97, 0.06);
        --shadow-md: 0 12px 28px rgba(16, 26, 97, 0.08);
    }

    body {
        background: linear-gradient(180deg, #f8fbff 0%, var(--ds-bg) 100%);
    }

    .beneficiarios-page {
        max-width: 1440px;
        margin: 0 auto;
        padding: 26px 24px 40px;
        color: var(--ds-dark);
    }

    .beneficiarios-breadcrumb {
        font-size: 14px;
        color: var(--text-soft);
        margin-bottom: 26px;
    }

    .beneficiarios-breadcrumb a {
        color: var(--text-soft);
        text-decoration: none;
    }

    .beneficiarios-breadcrumb a:hover {
        color: var(--ds-primary);
    }

    .beneficiarios-breadcrumb span {
        color: var(--ds-dark);
        font-weight: 600;
    }

    .beneficiarios-header {
        margin-bottom: 28px;
    }

    .beneficiarios-header h1 {
        margin: 0 0 8px;
        font-size: 28px;
        line-height: 1.2;
        color: var(--ds-dark);
        font-weight: 600;
    }

    .beneficiarios-header p {
        margin: 0;
        color: var(--ds-muted);
        font-size: 15px;
    }

    .beneficiarios-shell {
        background: var(--white);
        border: 1px solid var(--ds-border);
        border-radius: 26px;
        box-shadow: var(--shadow-md);
        padding: 18px;
    }

    .beneficiarios-grid {
        display: grid;
        grid-template-columns: minmax(0, 2.2fr) 380px;
        gap: 22px;
    }

    .beneficiarios-panel {
        background: var(--white);
        border: 1px solid var(--ds-border);
        border-radius: 20px;
        box-shadow: var(--shadow-sm);
        overflow: hidden;
    }

    .beneficiarios-panel-body {
        padding: 20px;
    }

    .section-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--ds-dark);
        margin: 0 0 14px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
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

    .search-input {
        width: 100%;
        height: 50px;
        padding: 0 16px 0 48px;
        border: 1px solid var(--ds-border);
        border-radius: 14px;
        outline: none;
        font-size: 15px;
        color: var(--ds-muted);
        background: #fff;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search-input:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 4px rgba(54, 149, 245, 0.12);
    }

    .ds-btn {
        min-height: 50px;
        border: 0;
        border-radius: 14px;
        padding: 0 20px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        white-space: nowrap;
    }

    .ds-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .ds-btn-primary {
        background: linear-gradient(180deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
        color: #fff;
        box-shadow: 0 10px 20px rgba(54, 149, 245, 0.22);
    }

    .ds-btn-primary:hover {
        color: #fff;
    }

    .ds-btn-outline {
        background: #fff;
        border: 1px solid var(--ds-primary);
        color: var(--ds-primary);
    }

    .ds-btn-outline:hover {
        background: rgba(54, 149, 245, 0.08);
        color: var(--ds-primary-dark);
    }

    .resultado-header {
        display: none;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 16px;
    }

    .resultado-count {
        color: var(--ds-dark);
        font-weight: 600;
        text-transform: uppercase;
        font-size: 14px;
    }

    .empty-state {
        border: 1px dashed #d9e4f2;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
        min-height: 260px;
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
        background: rgba(54, 149, 245, 0.1);
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

    #resultados {
        display: flex;
        flex-direction: column;
        gap: 14px;
        margin-bottom: 28px;
    }

    .resultado-card,
    .assigned-card {
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 16px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: #fff;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.04);
    }

    .resultado-card:hover,
    .assigned-card:hover {
        border-color: rgba(54, 149, 245, 0.45);
        box-shadow: 0 12px 24px rgba(16, 26, 97, 0.08);
    }

    .resultado-left,
    .assigned-left {
        display: flex;
        align-items: center;
        gap: 16px;
        min-width: 0;
    }

    .avatar {
        width: 66px;
        height: 66px;
        border-radius: 50%;
        background: linear-gradient(180deg, #edf5ff 0%, #e6f0ff 100%);
        color: var(--ds-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 30px;
        flex-shrink: 0;
    }

    .user-meta {
        min-width: 0;
    }

    .user-meta h4 {
        margin: 0 0 10px;
        font-size: 18px;
        color: var(--ds-dark);
        font-weight: 600;
        word-break: break-word;
    }

    .meta-row {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .badge-soft,
    .badge-primary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        letter-spacing: 0.2px;
    }

    .badge-primary {
        background: linear-gradient(180deg, var(--ds-primary) 0%, var(--ds-primary-dark) 100%);
        color: #fff;
    }

    .badge-soft {
        background: #eef6ff;
        color: var(--ds-primary-dark);
    }

    .company,
    .meta-text {
        color: var(--ds-muted);
        font-size: 14px;
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

    .side-panel {
        padding: 26px 20px;
    }

    .org-title {
        margin: 0 0 6px;
        font-size: 22px;
        color: var(--ds-dark);
        font-weight: 600;
    }

    .org-subtitle {
        margin: 0 0 18px;
        font-size: 14px;
        color: var(--text-soft);
    }

    .org-icon {
        width: 44px;
        height: 44px;
        border-radius: 50%;
        background: var(--warning);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 18px;
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
        font-weight: 600;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        background: #eef6ff;
        color: var(--ds-primary-dark);
    }

    .status-pill.status-finalizada {
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
    }

    .status-pill.status-activa {
        background: rgba(22, 163, 74, 0.1);
        color: var(--success);
    }

    .alert-search {
        border-radius: 16px;
        padding: 14px 16px;
        background: rgba(239, 68, 68, 0.08);
        color: #991b1b;
        border: 1px solid rgba(239, 68, 68, 0.2);
        margin-bottom: 16px;
    }
    .pesquisas-icons {
    display: flex;
    flex-direction: row;
    align-items: center;
    justify-content: flex-start;
    gap: 10px;
    flex-wrap: wrap;
    width: 100%;
    margin: 16px 0 18px;
}
    @media (max-width: 1120px) {
        .beneficiarios-grid {
            grid-template-columns: 1fr;
        }

        .side-panel {
            padding: 22px 20px;
        }
    }

    @media (max-width: 768px) {
        .beneficiarios-page {
            padding: 18px 16px 32px;
        }

        .search-row {
            grid-template-columns: 1fr;
        }

        .resultado-header,
        .resultado-card,
        .assigned-card {
            flex-direction: column;
            align-items: stretch;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .org-title {
            font-size: 20px;
        }

        .beneficiarios-header h1 {
            font-size: 24px;
        }

        .ds-btn {
            width: 100%;
        }
    }
</style>
<?php
$pesquisaMap = [
    '1' => [
        'nombre' => 'Antropometría',
        'emoji'  => 'antropometria2.svg',
        'clase'  => 'yellow'
    ],
    '2' => [
        'nombre' => 'Laboratorio',
        'emoji'  => 'sanguinea2.svg',
        'clase'  => 'red'
    ],
    '3' => [
        'nombre' => 'Visual',
        'emoji'  => 'visual2.svg',
        'clase'  => 'violet'
    ],
    '4' => [
        'nombre' => 'Signos vitales',
        'emoji'  => 'signosVitales2.svg',
        'clase'  => 'orange'
    ],
    '5' => [
        'nombre' => 'Medicina general',
        'emoji'  => 'medicinaGeneral2.svg',
        'clase'  => 'purple'
    ],
    '6' => [
        'nombre' => 'Vacunación',
        'emoji'  => 'vacunacion2.svg',
        'clase'  => 'blue'
    ],
];

$pesquisas_jornada = $pesquisas_jornada ?? [];

if (empty($pesquisas_jornada) && !empty($jornada['pesquisas'])) {
    $pesquisas_jornada = array_map('trim', explode(',', $jornada['pesquisas']));
}
?>
<div class="beneficiarios-page">
    <div class="beneficiarios-breadcrumb">
        <a href="<?= base_url('jornadas') ?>">Jornadas</a>
        &gt;
        <a href="<?= base_url("jornadas/$jornada_id/beneficiarios") ?>">Beneficiarios</a>
        &gt;
        <span>Buscar o registrar</span>
    </div>

    <div class="beneficiarios-header">
        <h1>Busca tu beneficiario</h1>
        <p>Busca, registra y asocia beneficiarios a esta jornada.</p>
    </div>

    <section class="beneficiarios-shell">
        <div class="beneficiarios-grid">
            <div class="beneficiarios-panel">
                <div class="beneficiarios-panel-body">
                  
                    <div class="search-row">
                        <div class="input-wrap">
                             
                            <input
                                type="text"
                                id="campoBusqueda"
                                class="search-input"
                                placeholder="Nombre, apellido o ID Digisalud..."
                                autocomplete="off"
                            >
                        </div>

                        <button type="button" class="ds-btn ds-btn-primary" onclick="ejecutarBusqueda()">
                            <span>🔎</span>
                            <span>Buscar</span>
                        </button>
                    </div>

                    <div class="resultado-header" id="resultadoHeader">
                        <span class="resultado-count" id="resultadoCount"></span>

                        <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/create") ?>" class="ds-btn ds-btn-outline">
                            + Registrar nuevo
                        </a>
                    </div>

                    <div id="resultados"></div>

                    <div id="sinResultados" style="display:none;">
                        <div class="empty-state">
                            <div>
                                <div class="empty-icon">👤</div>
                                <h3>No se encontró ningún beneficiario</h3>
                                <p class="mb-3">Puedes registrar uno nuevo y asociarlo a esta jornada.</p>

                                <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/create") ?>" class="ds-btn ds-btn-primary">
                                    + Registrar nuevo beneficiario
                                </a>
                            </div>
                        </div>
                    </div>

                    <div id="estadoInicial">
                        <div class="empty-state">
                            <div>
                                <div class="empty-icon">👥</div>
                                <h3>Escribe al menos 2 caracteres y presiona Buscar</h3>
                                <p>Usa el buscador para encontrar y asociar beneficiarios existentes a esta jornada.</p>
                            </div>
                        </div>
                    </div>

                    <?php if (! empty($beneficiariosAsignados) && is_iterable($beneficiariosAsignados)): ?>
                        <h3 class="subsection-title">
                            <span>👥</span>
                            <span>Beneficiarios asignados (<?= count($beneficiariosAsignados) ?>)</span>
                        </h3>

                        <?php foreach ($beneficiariosAsignados as $beneficiario): ?>
                            <div class="assigned-card mb-3">
                                <div class="assigned-left">
                                    <div class="avatar">👤</div>

                                    <div class="user-meta">
                                        <h4>
                                            <?= esc(trim(($beneficiario['apellidos'] ?? '') . ', ' . ($beneficiario['nombres'] ?? ''))) ?>
                                        </h4>
                                        <div class="meta-row">
                                            <span class="badge-primary">
                                                <?= esc($beneficiario['id_digisalud'] ?? 'SIN ID') ?>
                                            </span>
                                            <span class="company">
                                                <?= esc($beneficiario['parentesco'] ?? 'Sin representante') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <aside class="beneficiarios-panel">
                <div class="side-panel">
                    <h2 class="org-title"><?= esc($jornadaNombre) ?></h2>
                    

                    <div class="pesquisas-icons">
                        <?php if (!empty($pesquisas_jornada)): ?>
                            <?php foreach ($pesquisas_jornada as $idPesquisa): ?>
                                <?php if (isset($pesquisaMap[$idPesquisa])): ?>
                                    <?php $pesquisa = $pesquisaMap[$idPesquisa]; ?>

                                    <div class="pesquisa-icon <?= esc($pesquisa['clase']) ?>" title="<?= esc($pesquisa['nombre']) ?>">
                                        <img height="30"
                                            src="<?= base_url('img/' . $pesquisa['emoji']) ?>" 
                                            alt="<?= esc($pesquisa['nombre']) ?>"
                                        >
                                    </div>

                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="pesquisa-empty">
                                Sin pesquisas asignadas
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="divider"></div>

                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-icon">📍</div>
                            <div class="info-copy">
                                <small>Ubicación</small>
                                <strong><?= esc($jornadaUbicacion) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">📅</div>
                            <div class="info-copy">
                                <small>Fecha Inicio</small>
                                <strong><?= esc($fechaInicio) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">🗓️</div>
                            <div class="info-copy">
                                <small>Fecha Fin</small>
                                <strong><?= esc($fechaFin) ?></strong>
                            </div>
                        </div>

                        <div class="info-item">
                            <div class="info-icon">🚩</div>
                            <div class="info-copy">
                                <small>Status</small>
                                <strong>
                                    <span class="status-pill <?= $statusRaw === '1' ? 'status-activa' : ($statusRaw === '2' ? 'status-finalizada' : '') ?>">
                                        <?= esc($statusTexto) ?>
                                    </span>
                                </strong>
                            </div>
                        </div>
                    </div>

                    <div class="divider"></div>

                    <div class="info-item full">
                        <div class="info-icon">👥</div>
                        <div class="info-copy">
                            <small>N° de beneficiarios asignados</small>
                            <strong id="contadorAsignados"><?= (int) $cantidadAsignados ?></strong>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </section>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const jornadaId = <?= (int) $jornada_id ?>;
const buscarUrl = "<?= base_url('beneficiarios/buscar-ajax') ?>";
const asociarBaseUrl = "<?= base_url('jornadas') ?>";

const csrfName = "<?= csrf_token() ?>";
const csrfHash = "<?= csrf_hash() ?>";

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('campoBusqueda');

    if (!input) {
        return;
    }

    input.focus();

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            ejecutarBusqueda();
        }
    });
});

function escaparHtml(valor) {
    if (valor === null || valor === undefined) {
        return '';
    }

    return String(valor)
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

function resetearBusqueda() {
    document.getElementById('resultados').innerHTML = '';
    document.getElementById('sinResultados').style.display = 'none';
    document.getElementById('resultadoHeader').style.display = 'none';
    document.getElementById('estadoInicial').style.display = 'block';
}

function ejecutarBusqueda() {
    const q = document.getElementById('campoBusqueda').value.trim();

    const contenedor = document.getElementById('resultados');
    const sinResultados = document.getElementById('sinResultados');
    const header = document.getElementById('resultadoHeader');
    const contador = document.getElementById('resultadoCount');
    const estadoInicial = document.getElementById('estadoInicial');

    if (q.length < 2) {
        resetearBusqueda();
        return;
    }

    estadoInicial.style.display = 'none';
    sinResultados.style.display = 'none';
    header.style.display = 'none';

    contenedor.innerHTML = `
        <div class="empty-state">
            <div>
                <div class="empty-icon">🔎</div>
                <h3>Buscando beneficiarios...</h3>
                <p>Estamos consultando coincidencias por nombre, apellido o ID Digisalud.</p>
            </div>
        </div>
    `;

    fetch(`${buscarUrl}?q=${encodeURIComponent(q)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function (response) {
        if (!response.ok) {
            throw new Error('Error HTTP ' + response.status);
        }

        return response.json();
    })
    .then(function (data) {
        contenedor.innerHTML = '';

        if (!Array.isArray(data) || data.length === 0) {
            sinResultados.style.display = 'block';
            header.style.display = 'none';
            return;
        }

        header.style.display = 'flex';
        contador.textContent = `Se encontró ${data.length} beneficiario(s)`;

        let html = '';

        data.forEach(function (b) {
            const fecha = b.fecha_nacimiento
                ? b.fecha_nacimiento.split('-').reverse().join('/')
                : '—';

            const edad = b.edad ? `${escaparHtml(b.edad)} años` : 'Edad no disponible';
            const parentesco = b.parentesco ? escaparHtml(b.parentesco) : 'Sin representante';
            const nombreCompleto = `${(b.apellidos || '').toUpperCase()}, ${(b.nombres || '').toUpperCase()}`.trim();

            html += `
                <div class="resultado-card">
                    <div class="resultado-left">
                        <div class="avatar">👤</div>

                        <div class="user-meta">
                            <h4>${escaparHtml(nombreCompleto || 'Beneficiario sin nombre')}</h4>

                            <div class="meta-row">
                                <span class="badge-primary">${escaparHtml(b.id_digisalud || 'SIN ID')}</span>
                                <span class="badge-soft">FN: ${escaparHtml(fecha)}</span>
                                <span class="meta-text">${edad}</span>
                                <span class="meta-text">Representante: ${parentesco}</span>
                            </div>
                        </div>
                    </div>

                    <form method="post" action="${asociarBaseUrl}/${jornadaId}/asociar/${b.id_beneficiario}" style="margin:0;">
                        <input type="hidden" name="${csrfName}" value="${csrfHash}">
                        <button type="submit" class="ds-btn ds-btn-primary">
                            + Agregar
                        </button>
                    </form>
                </div>
            `;
        });

        contenedor.innerHTML = html;
    })
    .catch(function (error) {
        console.error(error);

        contenedor.innerHTML = `
            <div class="alert-search">
                Ocurrió un error al buscar beneficiarios. Revisa la consola del navegador o valida la ruta <strong>beneficiarios/buscar-ajax</strong>.
            </div>
        `;

        sinResultados.style.display = 'none';
        header.style.display = 'none';
    });
}
</script>
<?= $this->endSection() ?>

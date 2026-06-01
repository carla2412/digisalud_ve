<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
 

$pesquisasActivas = $pesquisasActivas ?? [];
$pesquisasActivas = array_values(array_unique(array_map('strval', $pesquisasActivas)));

$reportesDisponibles = $reportesDisponibles ?? [];

$jornadaId = $jornadaId ?? ($jornada['id_jornada'] ?? null);

$nombreJornada = $jornada['nombre_jornada'] 
    ?? $jornada['jornada'] 
    ?? $jornada['nombre'] 
    ?? $jornada['titulo'] 
    ?? $jornada['descripcion'] 
    ?? 'Jornada';

$estatus = $jornada['estado'] 
    ?? $jornada['estatus'] 
    ?? $jornada['status'] 
    ?? 'Sin estatus';

$fechaInicio = $jornada['fecha_inicio'] 
    ?? $jornada['fechaInicio'] 
    ?? null;

$fechaFin = $jornada['fecha_fin'] 
    ?? $jornada['fechaFin'] 
    ?? null;

$reportes = [];
 
 
/*
|--------------------------------------------------------------------------
| Antropometría
|--------------------------------------------------------------------------
*/
if (in_array('1', $pesquisasActivas, true)) {
    $itemsAntropometria = [];

    if (!empty($reportesDisponibles['antropometria']['menores_19'])) {
        $itemsAntropometria[] = [
            'titulo' => 'Menores de 19 años',
            'descripcion' => 'Niños, niñas y adolescentes evaluados.',
            'url' => site_url("jornadas/{$jornadaId}/reportes/antropometria/menores-19"),
        ];
    }

    if (!empty($reportesDisponibles['antropometria']['adultos'])) {
        $itemsAntropometria[] = [
            'titulo' => 'Adultos',
            'descripcion' => 'Personas mayores o iguales a 19 años evaluadas.',
            'url' => site_url("jornadas/{$jornadaId}/reportes/antropometria/adultos"),
        ];
    }

    if (!empty($reportesDisponibles['antropometria']['embarazadas'])) {
        $itemsAntropometria[] = [
            'titulo' => 'Embarazadas',
            'descripcion' => 'Evaluaciones antropométricas gestacionales registradas.',
            'url' => site_url("jornadas/{$jornadaId}/reportes/antropometria/embarazadas"),
        ];
    }

    if (!empty($itemsAntropometria)) {
        $reportes[] = [
            'id' => '1',
            'titulo' => 'Antropometría',
            'descripcion' => 'Estado nutricional y clasificación antropométrica.',
            'icono' => 'antropometria2.svg',
            'color' => 'yellow',
            'items' => $itemsAntropometria,
        ];
    }
}

/*
|--------------------------------------------------------------------------
| Laboratorio sanguíneo
|--------------------------------------------------------------------------
*/
if (in_array('2', $pesquisasActivas, true) && !empty($reportesDisponibles['sanguineo'])) {
    $reportes[] = [
        'id' => '2',
        'titulo' => 'Laboratorio sanguíneo',
        'descripcion' => 'Resultados de hemoglobina, anemia y hallazgos de laboratorio.',
        'icono' => 'sanguinea2.svg',
        'color' => 'red',
        'items' => [
            [
                'titulo' => 'Reporte sanguíneo detallado',
                'descripcion' => 'Listado por beneficiario con resultado e interpretación.',
                'url' => site_url("jornadas/{$jornadaId}/reportes/sanguineo"),
            ],
        ],
    ];
}

/*
|--------------------------------------------------------------------------
| Visual
|--------------------------------------------------------------------------
*/
if (in_array('3', $pesquisasActivas, true) && !empty($reportesDisponibles['visual'])) {
    $reportes[] = [
        'id' => '3',
        'titulo' => 'Visual',
        'descripcion' => 'Evaluaciones visuales realizadas en la jornada.',
        'icono' => 'visual2.svg',
        'color' => 'violet',
        'items' => [
            [
                'titulo' => 'Reporte visual detallado',
                'descripcion' => 'Resultados de pesquisa visual por beneficiario.',
                'url' => site_url("jornadas/{$jornadaId}/reportes/visual"),
            ],
        ],
    ];
}

/*
|--------------------------------------------------------------------------
| Signos vitales
|--------------------------------------------------------------------------
*/
if (in_array('4', $pesquisasActivas, true) && !empty($reportesDisponibles['signos_vitales'])) {
    $reportes[] = [
        'id' => '4',
        'titulo' => 'Signos vitales',
        'descripcion' => 'Presión arterial, frecuencia cardíaca, respiratoria y otros signos.',
        'icono' => 'signosVitales2.svg',
        'color' => 'orange',
        'items' => [
            [
                'titulo' => 'Reporte de signos vitales',
                'descripcion' => 'Detalle de mediciones registradas.',
                'url' => site_url("jornadas/{$jornadaId}/reportes/signos-vitales"),
            ],
        ],
    ];
}

/*
|--------------------------------------------------------------------------
| Medicina general
|--------------------------------------------------------------------------
*/
if (in_array('5', $pesquisasActivas, true) && !empty($reportesDisponibles['medicina_general'])) {
    $reportes[] = [
        'id' => '5',
        'titulo' => 'Medicina general',
        'descripcion' => 'Hallazgos clínicos, observaciones y remisiones médicas.',
        'icono' => 'medicinaGeneral2.svg',
        'color' => 'purple',
        'items' => [
            [
                'titulo' => 'Reporte de medicina general',
                'descripcion' => 'Evaluación médica detallada por beneficiario.',
                'url' => site_url("jornadas/{$jornadaId}/reportes/medicina-general"),
            ],
        ],
    ];
}

/*
|--------------------------------------------------------------------------
| Vacunación
|--------------------------------------------------------------------------
*/
if (in_array('6', $pesquisasActivas, true) && !empty($reportesDisponibles['vacunacion'])) {
    $reportes[] = [
        'id' => '6',
        'titulo' => 'Vacunación',
        'descripcion' => 'Vacunas registradas durante la jornada.',
        'icono' => 'vacunacion2.svg',
        'color' => 'blue',
        'items' => [
            [
                'titulo' => 'Reporte de vacunación',
                'descripcion' => 'Detalle de dosis y vacunas aplicadas.',
                'url' => site_url("jornadas/{$jornadaId}/reportes/vacunacion"),
            ],
        ],
    ];
}
?>


<style>
    :root {
        --rep-primary: #3695f5;
        --rep-primary-dark: #1b7ae2;
        --rep-dark: #101a61;
        --rep-bg: #f5f8fc;
        --rep-card: #ffffff;
        --rep-border: #e0e6ed;
        --rep-muted: #6b7280;
        --rep-text: #253041;
        --rep-success: #16a34a;
        --rep-disabled: #cbd5e1;
    }

    .rep-page {
        background: var(--rep-bg);
        min-height: calc(100vh - 72px);
        padding: 24px;
    }

    .rep-wrap {
        max-width: 1320px;
        margin: 0 auto;
    }

    .rep-header {

        border-radius: 24px;
        padding: 24px;

        box-shadow: 0 18px 40px rgba(16, 26, 97, .18);
        margin-bottom: 24px;
        position: relative;
        overflow: hidden;
    }

    .rep-header::after {
        content: "";
        position: absolute;
        width: 280px;
        height: 280px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .09);
        right: -90px;
        top: -120px;
    }

    .rep-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .rep-eyebrow {
        font-size: 13px;
        opacity: .85;
        margin-bottom: 6px;
        letter-spacing: .04em;
        text-transform: uppercase;
    }

    .rep-title {
        margin: 0;
        font-size: 30px;
        font-weight: 800;
    }

    .rep-subtitle {
        margin: 8px 0 0;
        color: rgba(100, 100, 100, 0.82);
        max-width: 760px;
    }

    .rep-meta {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: 18px;
        color: rgba(100, 100, 100, 0.82);
    }

    .rep-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .22);
       color: rgba(100, 100, 100, 0.82);
        padding: 8px 12px;
        font-size: 13px;
    }

    .rep-back {
        border: 0;
        background: #fff;
        color: var(--rep-dark);
        border-radius: 14px;
        padding: 10px 16px;
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, .14);
    }

    .rep-back:hover {
        color: var(--rep-primary-dark);
        text-decoration: none;
    }

    .rep-summary-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .rep-summary-card {
        background: var(--rep-card);
        border: 1px solid var(--rep-border);
        border-radius: 20px;
        padding: 18px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
    }

    .rep-summary-label {
        color: var(--rep-muted);
        font-size: 13px;
        margin-bottom: 6px;
    }

    .rep-summary-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--rep-dark);
        line-height: 1;
    }

    .rep-section-title {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        gap: 16px;
        margin-bottom: 16px;
    }

    .rep-section-title h2 {
        margin: 0;
        color: var(--rep-text);
        font-size: 24px;
        font-weight: 800;
    }

    .rep-section-title p {
        margin: 4px 0 0;
        color: var(--rep-muted);
    }

    .rep-search-box {
        min-width: 280px;
        max-width: 360px;
        width: 100%;
        position: relative;
    }

    .rep-search-box input {
        width: 100%;
        border: 1px solid var(--rep-border);
        border-radius: 14px;
        padding: 12px 14px;
        outline: none;
        background: #fff;
    }

    .rep-search-box input:focus {
        border-color: var(--rep-primary);
        box-shadow: 0 0 0 4px rgba(54, 149, 245, .13);
    }

    .rep-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 18px;
    }

    .rep-card {
        background: var(--rep-card);
        border: 1px solid var(--rep-border);
        border-radius: 24px;
        overflow: hidden;
        box-shadow: 0 12px 32px rgba(15, 23, 42, .07);
        display: flex;
        flex-direction: column;
        min-height: 100%;
    }

    .rep-card-disabled {
        opacity: .58;
    }

    .rep-card-top {
        padding: 18px;
        border-bottom: 1px solid var(--rep-border);
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .rep-icon {
        width: 52px;
        height: 52px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        flex: 0 0 auto;
    }

    .rep-icon img {
        width: 31px;
        height: 31px;
        object-fit: contain;
    }

    .rep-icon-yellow {
        background: #fff7d6;
    }

    .rep-icon-red {
        background: #ffe4e6;
    }

    .rep-icon-violet {
        background: #ede9fe;
    }

    .rep-icon-orange {
        background: #ffedd5;
    }

    .rep-icon-purple {
        background: #f3e8ff;
    }

    .rep-icon-blue {
        background: #dbeafe;
    }

    .rep-card h3 {
        margin: 0;
        color: var(--rep-text);
        font-size: 18px;
        font-weight: 800;
    }

    .rep-card-desc {
        margin: 6px 0 0;
        color: var(--rep-muted);
        font-size: 14px;
        line-height: 1.4;
    }

    .rep-status {
        margin-left: auto;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 800;
        white-space: nowrap;
    }

    .rep-status-active {
        background: #dcfce7;
        color: #166534;
    }

    .rep-status-disabled {
        background: #f1f5f9;
        color: #64748b;
    }

    .rep-card-body {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 10px;
        flex: 1;
    }

    .rep-link {
        border: 1px solid var(--rep-border);
        background: #fff;
        border-radius: 16px;
        padding: 14px;
        text-decoration: none;
        display: flex;
        gap: 12px;
        align-items: center;
        transition: .18s ease;
        color: inherit;
    }

    .rep-link:hover {
        border-color: var(--rep-primary);
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(54, 149, 245, .12);
        text-decoration: none;
    }

    .rep-link-disabled {
        pointer-events: none;
        background: #f8fafc;
    }

    .rep-link-icon {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        display: grid;
        place-items: center;
        background: var(--rep-bg);
        color: var(--rep-primary-dark);
        font-weight: 900;
        flex: 0 0 auto;
    }

    .rep-link-title {
        font-weight: 800;
        color: var(--rep-text);
        margin: 0;
        line-height: 1.2;
    }

    .rep-link-desc {
        margin: 3px 0 0;
        color: var(--rep-muted);
        font-size: 13px;
        line-height: 1.35;
    }

    .rep-link-arrow {
        margin-left: auto;
        color: var(--rep-primary-dark);
        font-weight: 900;
    }

    .rep-empty {
        display: none;
        background: #fff;
        border: 1px dashed var(--rep-border);
        border-radius: 20px;
        padding: 28px;
        color: var(--rep-muted);
        text-align: center;
    }

    @media (max-width: 1100px) {
        .rep-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .rep-summary-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .rep-page {
            padding: 16px;
        }

        .rep-grid,
        .rep-summary-grid {
            grid-template-columns: 1fr;
        }

        .rep-title {
            font-size: 24px;
        }

        .rep-section-title {
            align-items: stretch;
            flex-direction: column;
        }

        .rep-search-box {
            max-width: 100%;
        }

        .rep-card-top {
            flex-wrap: wrap;
        }

        .rep-status {
            margin-left: 0;
        }
    }
    .rep-empty-state {
    background: #fff;
    border: 1px dashed var(--rep-border);
    border-radius: 24px;
    padding: 36px;
    text-align: center;
    color: var(--rep-muted);
    box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
}

.rep-empty-state h3 {
    color: var(--rep-dark);
    font-size: 22px;
    font-weight: 800;
    margin-bottom: 8px;
}

.rep-empty-state p {
    max-width: 620px;
    margin: 0 auto 18px;
}

.rep-back-inline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: var(--rep-primary-dark);
    color: #fff;
    padding: 10px 16px;
    border-radius: 14px;
    font-weight: 700;
    text-decoration: none;
}

.rep-back-inline:hover {
    color: #fff;
    text-decoration: none;
    background: var(--rep-dark);
}
</style>
 
<div class="rep-page">
    <div class="rep-wrap">

        <section class="rep-header">
            <div class="rep-header-content">
                <div>
                    <div class="rep-eyebrow">Módulo de reportes</div>
                    <h1 class="rep-title">Reportes detallados</h1>
                    <p class="rep-subtitle">
                        Selecciona una pesquisa para consultar sus resultados detallados.
                       
                    </p>

                    <div class="rep-meta">
                        <span class="rep-pill">
                            Jornada: <?= esc($nombreJornada) ?>
                        </span>

                        <span class="rep-pill">
                            Estatus: <?= esc($estatus) ?>
                        </span>

                        <?php if ($fechaInicio): ?>
                            <span class="rep-pill">
                                Inicio: <?= esc(date('d-m-Y', strtotime($fechaInicio))) ?>
                            </span>
                        <?php endif; ?>

                        <?php if ($fechaFin): ?>
                            <span class="rep-pill">
                                Fin: <?= esc(date('d-m-Y', strtotime($fechaFin))) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <a href="<?= site_url('jornadas') ?>" class="rep-back">
                    ← Volver a jornadas
                </a>
            </div>
        </section>
   
<?php if (empty($reportes)): ?>

    <div class="rep-empty-state">
        <h3>No hay reportes disponibles todavía</h3>
        <p>
            Esta jornada tiene pesquisas asociadas, pero aún no existen evaluaciones registradas
            para generar reportes detallados.
        </p>

        <a href="<?= site_url('jornadas/' . $jornadaId . '/beneficiarios') ?>" class="rep-back-inline">
            Ir a beneficiarios
        </a>
    </div>

<?php else: ?>

    <section class="rep-summary-grid">
        <article class="rep-summary-card">
            <div class="rep-summary-label">Pesquisas asociadas</div>
            <div class="rep-summary-value"><?= count($pesquisasActivas) ?></div>
        </article>

        <article class="rep-summary-card">
            <div class="rep-summary-label">Reportes detallados</div>
            <div class="rep-summary-value">
                <?php
                    $totalReportes = 0;

                    foreach ($reportes as $reporte) {
                        $totalReportes += count($reporte['items']);
                    }

                    echo esc($totalReportes);
                ?>
            </div>
        </article>

        <article class="rep-summary-card">
            <div class="rep-summary-label">Pesquisas con datos</div>
            <div class="rep-summary-value"><?= count($reportes) ?></div>
        </article>
    </section>

    <section class="rep-section-title">
        <div>
            <h2>Reportes disponibles</h2>
            <p>
                Solo se muestran las pesquisas asociadas a esta jornada y que ya tienen evaluaciones registradas.
            </p>
        </div>

        <div class="rep-search-box">
            <input 
                type="search" 
                id="repSearch" 
                placeholder="Buscar reporte o pesquisa..."
                autocomplete="off"
            >
        </div>
    </section>

    <section class="rep-grid" id="repGrid">
        <?php foreach ($reportes as $reporte): ?>

            <article 
                class="rep-card" 
                data-report-card
                data-search="<?= esc(strtolower($reporte['titulo'] . ' ' . $reporte['descripcion'])) ?>"
            >
                <div class="rep-card-top">
                    <div class="rep-icon rep-icon-<?= esc($reporte['color']) ?>">
                        <img 
                            src="<?= base_url('img/' . $reporte['icono']) ?>" 
                            alt="<?= esc($reporte['titulo']) ?>"
                        >
                    </div>

                    <div>
                        <h3><?= esc($reporte['titulo']) ?></h3>
                        <p class="rep-card-desc"><?= esc($reporte['descripcion']) ?></p>
                    </div>

                    <span class="rep-status rep-status-active">
                        Disponible
                    </span>
                </div>

                <div class="rep-card-body">
                    <?php foreach ($reporte['items'] as $index => $item): ?>
                        <a 
                            href="<?= esc($item['url']) ?>" 
                            class="rep-link"
                            data-search="<?= esc(strtolower($item['titulo'] . ' ' . $item['descripcion'])) ?>"
                        >
                            <span class="rep-link-icon"><?= $index + 1 ?></span>

                            <span>
                                <p class="rep-link-title"><?= esc($item['titulo']) ?></p>
                                <p class="rep-link-desc"><?= esc($item['descripcion']) ?></p>
                            </span>

                            <span class="rep-link-arrow">›</span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </article>

        <?php endforeach; ?>
    </section>

    <div class="rep-empty" id="repEmpty">
        No se encontraron reportes con ese criterio de búsqueda.
    </div>

<?php endif; ?>



    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const input = document.getElementById('repSearch');
        const cards = Array.from(document.querySelectorAll('[data-report-card]'));
        const empty = document.getElementById('repEmpty');

        if (!input) {
            return;
        }

        input.addEventListener('input', function() {
            const term = this.value.trim().toLowerCase();
            let visible = 0;

            cards.forEach(function(card) {
                const cardText = card.dataset.search || '';
                const linksText = Array.from(card.querySelectorAll('[data-search]'))
                    .map(function(item) {
                        return item.dataset.search || '';
                    })
                    .join(' ');

                const match = !term || cardText.includes(term) || linksText.includes(term);

                card.style.display = match ? '' : 'none';

                if (match) {
                    visible++;
                }
            });

            empty.style.display = visible === 0 ? 'block' : 'none';
        });
    });
</script>

<?= $this->endSection() ?>
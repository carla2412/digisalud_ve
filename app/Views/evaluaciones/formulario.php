<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));
$nombrePesquisa = esc($tipoPesquisa['descripcion_view'] ?? $tipoPesquisa['nombre_tipo'] ?? 'Evaluación');
$esEdicion      = ! empty($evaluacionExistente);
$evalId         = $evaluacionExistente['id_evaluacion'] ?? '';
$obsExistente   = $evaluacionExistente['observaciones'] ?? '';

$urlRetorno = $jornadaId
    ? base_url("jornadas/{$jornadaId}/beneficiarios")
    : base_url("centros/{$centroId}/beneficiarios");

$seccionesExcluidas = ['observaciones_lab', 'seguimiento_visual', 'seguimiento_vitales'];
$itemsFormulario = [];
$itemRemitir = null;

foreach ($itemsAgrupados as $seccion => $items) {
    foreach ($items as $item) {
        if (strpos($item['codigo'], 'remitir_especialista') !== false || strpos($item['codigo'], 'especialista_') !== false) {
            $itemRemitir = $item;
            break 2;
        }
    }
}

foreach ($itemsAgrupados as $seccion => $items) {
    if (in_array($seccion, $seccionesExcluidas, true)) {
        continue;
    }

    $itemsVisibles = [];
    foreach ($items as $item) {
        if ($itemRemitir && $item['codigo'] === $itemRemitir['codigo']) {
            continue;
        }
        $itemsVisibles[] = $item;
    }

    if (! empty($itemsVisibles)) {
        $itemsFormulario[$seccion] = $itemsVisibles;
    }
}

$jsSections = [];
$jsRanges = [];
foreach ($itemsFormulario as $seccion => $items) {
    $fields = [];
    $required = [];
    foreach ($items as $item) {
        $fields[] = $item['codigo'];
        if (! empty($item['obligatorio'])) {
            $required[] = $item['codigo'];
        }
        if ($item['tipo_dato'] === 'number' && ($item['valor_min'] !== null || $item['valor_max'] !== null)) {
            $jsRanges[$item['codigo']] = [
                'min'   => $item['valor_min'] !== null ? (float) $item['valor_min'] : null,
                'max'   => $item['valor_max'] !== null ? (float) $item['valor_max'] : null,
                'label' => $item['nombre'],
            ];
        }
    }

    $jsSections[] = [
        'id'       => $seccion,
        'title'    => $nombresSecciones[$seccion] ?? ucfirst(str_replace('_', ' ', $seccion)),
        'required' => $required,
        'fields'   => $fields,
    ];
}

$observacionesSection = [
    'id'       => 'observaciones',
    'title'    => 'Observaciones y remisión',
    'required' => [],
    'fields'   => ['observaciones'],
];
if ($itemRemitir) {
    $observacionesSection['fields'][] = $itemRemitir['codigo'];
}
$jsSections[] = $observacionesSection;
?>

<style>
    :root {
        --lab-primary: #101a61;
        --lab-primary-soft: #e8edff;
        --lab-bg: #f4f7fb;
        --lab-card: #ffffff;
        --lab-text: #172033;
        --lab-muted: #64748b;
        --lab-border: #dbe3ef;
        --lab-danger: #dc2626;
        --lab-warning: #f59e0b;
        --lab-success: #16a34a;
        --lab-sidebar-w: 72px;
        --lab-actions-h: 72px;
    }

    .lab-page {
        display: grid;
        grid-template-columns: var(--lab-sidebar-w) minmax(0, 1fr);
        min-height: 100dvh;
        overflow: clip;
    }

    .sidebar {
        background: var(--lab-primary);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 14px 0;
    }

    .sidebar__logo,
    .sidebar__item {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        border: 0;
        display: grid;
        place-items: center;
        color: #fff;
        background: rgba(255, 255, 255, .1);
        text-decoration: none;
        position: relative;
        transition: .2s ease;
    }

    .sidebar__item img {
        width: 24px;
        height: 24px;
        filter: brightness(0) invert(1);
        opacity: .65;
    }

    .sidebar__item:hover,
    .sidebar__item.active {
        background: #fff;
    }

    .sidebar__item:hover img,
    .sidebar__item.active img {
        filter: none;
        opacity: 1;
    }

    .sidebar__item.evaluado::after {
        content: '';
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--lab-success);
        border: 2px solid var(--lab-primary);
    }

    .sidebar__item[title]::before {
        content: attr(title);
        position: absolute;
        left: 52px;
        top: 50%;
        transform: translateY(-50%);
        background: #111827;
        color: #fff;
        border-radius: 8px;
        padding: 6px 10px;
        font-size: .75rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        z-index: 30;
    }

    .sidebar__item:hover[title]::before {
        opacity: 1;
    }

    .lab-main {
        display: flex;
        flex-direction: column;
        min-width: 0;
        padding: 22px 26px 88px;
    }

    .lab-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 18px;
        padding-bottom: calc(var(--lab-actions-h) + 22px);
    }
    .lab-header h1 {
        margin: 0;
        color: var(--lab-primary);
        font-size: 1.35rem;
        font-weight: 900;
    }

    .lab-header p {
        margin: 2px 0 0;
        color: var(--lab-muted);
        font-size: .9rem;
    }
    .lab-title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .lab-icon {
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        background: #fff;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
    }

    .lab-icon img {
        width: 30px;
        height: 30px;
    }



    .lab-badge {
        display: inline-flex;
        margin-top: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
    }

    .lab-badge.new {
        background: #dbeafe;
        color: #1e40af;
    }

    .lab-badge.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .lab-progress {
        min-width: 280px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--lab-muted);
    }

    .progress-bar {
        width: 100%;
        height: 9px;
        margin-top: 8px;
        border-radius: 999px;
        background: #dbe3ef;
        overflow: hidden;
    }

    .progress-bar__fill {
        height: 100%;
        width: 0;
        border-radius: inherit;
        background: var(--lab-primary);
        transition: width .25s ease;
    }

    .btn-volver {
        color: var(--lab-muted);
        text-decoration: none;
        font-size: .85rem;
        font-weight: 700;
    }

    .btn-volver:hover {
        color: var(--lab-primary);
    }

    .stepper {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .step {
        border: 1px solid var(--lab-border);
        background: #fff;
        color: var(--lab-muted);
        border-radius: 999px;
        padding: 8px 12px 8px 8px;
        font-size: .78rem;
        font-weight: 800;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: .2s ease;
    }

    .step span {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #eef2f7;
        display: grid;
        place-items: center;
        color: var(--lab-muted);
    }

    .step.active,
    .step.completed {
        border-color: var(--lab-primary);
        color: var(--lab-primary);
    }

    .step.active span,
    .step.completed span {
        background: var(--lab-primary);
        color: #fff;
    }

    .lab-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 18px;
        align-items: start;
    }

    .lab-content {
        min-width: 0;
    }

    .form-section {
        display: none;
    }

    .form-section.active {
        display: block;
    }

    .section-card,
    .summary-card {
        background: var(--lab-card);
        border: 1px solid var(--lab-border);
        border-radius: 22px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .06);
    }

    .section-card {
        padding: 22px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .section-header h2,
    .summary-card h2,
    .summary-card h3 {
        margin: 0;
        color: var(--lab-primary);
        font-size: 1rem;
        font-weight: 900;
    }

    .section-header p {
        margin: 4px 0 0;
        color: var(--lab-muted);
        font-size: .84rem;
    }

    .section-status,
    .required-note {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        color: var(--lab-muted);
        font-size: .76rem;
        font-weight: 800;
    }

    .required-note {
        color: var(--lab-danger);
        margin-right: 8px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px;
    }

    .field {
        grid-column: span 6;
    }

    .field--full {
        grid-column: 1 / -1;
    }

    .field label {
        display: block;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .field input,
    .field select,
    .field textarea {
        width: 100%;
        border: 1.5px solid var(--lab-border);
        border-radius: 12px;
        background: #fff;
        color: var(--lab-text);
        font-size: .86rem;
        padding: 10px 12px;
        outline: none;
        transition: .15s ease;
    }

    .field input:focus,
    .field select:focus,
    .field textarea:focus {
        border-color: var(--lab-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .field small {
        display: block;
        margin-top: 5px;
        color: var(--lab-muted);
        font-size: .72rem;
    }

    .input-unit {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--lab-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: .15s ease;
    }

    .input-unit:focus-within {
        border-color: var(--lab-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .input-unit input {
        border: 0;
        border-radius: 0;
        box-shadow: none !important;
    }

    .input-unit span {
        padding: 0 12px;
        color: var(--lab-muted);
        font-size: .76rem;
        font-weight: 800;
        border-left: 1px solid var(--lab-border);
        white-space: nowrap;
    }

    .segmented {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .segmented label {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--lab-border);
        border-radius: 999px;
        padding: 8px 12px;
        cursor: pointer;
    }

    .field--error input,
    .field--error select,
    .field--error textarea,
    .field--error .input-unit,
    .is-invalid {
        border-color: var(--lab-danger) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .08) !important;
    }

    .field--warning input,
    .field--warning .input-unit {
        border-color: var(--lab-warning) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .1) !important;
    }

    .eval-campo-oculto {
        display: none !important;
    }

    .summary-panel {
        display: flex;
        flex-direction: column;
        gap: 14px;
        position: sticky;
        top: 16px;
    }

    .summary-card {
        padding: 18px;
    }

    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: .82rem;
    }

    .summary-row:last-child {
        border-bottom: 0;
    }

    .summary-row span {
        color: var(--lab-muted);
    }

    .summary-row strong {
        color: var(--lab-primary);
        text-align: right;
    }

    #summaryObservaciones {
        color: var(--lab-muted);
        font-size: .84rem;
        margin: 10px 0 0;
        word-break: break-word;
    }

    .actions-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 100%;
        min-height: var(--lab-actions-h);
        margin: 18px 0 0;
        padding: 14px 0 0;
        background: transparent;
        border-top: 1px solid var(--lab-border);
        box-sizing: border-box;
    }

    .btn {
        border: 0;
        border-radius: 12px;
        min-height: 42px;
        padding: 0 16px;
        font-weight: 900;
        font-size: .84rem;
        cursor: pointer;
        transition: .2s ease;
    }

    .btn--primary {
        background: var(--lab-primary);
        color: #fff;
    }

    .btn--secondary {
        background: var(--lab-primary-soft);
        color: var(--lab-primary);
    }

    .btn--ghost {
        background: transparent;
        color: var(--lab-muted);
    }

    .btn:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .eval-error-msg,
    .toast {
        border-radius: 12px;
        padding: 10px 12px;
        font-size: .82rem;
    }

    .eval-error-msg {
        display: none;
        margin-top: 12px;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .toast {
        position: fixed;
        right: 22px;
        bottom: 84px;
        z-index: 50;
        background: #111827;
        color: #fff;
        transform: translateY(12px);
        opacity: 0;
        pointer-events: none;
        transition: .2s ease;
    }

    .toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 1100px) {
        .lab-layout {
            grid-template-columns: 1fr;
        }

        .summary-panel {
            position: static;
        }
    }

    @media (max-width: 760px) {
        .lab-page {
            grid-template-columns: 1fr;
        }

        .sidebar {
            flex-direction: row;
            overflow-x: auto;
            justify-content: flex-start;
            padding: 10px 12px;
        }

        .lab-main {
            padding: 18px 14px 92px;
        }

        .lab-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .lab-progress {
            min-width: 0;
            width: 100%;
        }

        .field {
            grid-column: 1 / -1;
        }

        .actions-bar {
            margin: 0 -14px -18px;
            flex-wrap: wrap;
            padding: 12px 14px;
        }

        .btn {
            flex: 1 1 auto;
        }
    }
</style>

<div class="lab-page" data-page="evaluacion">
    <aside class="sidebar">
        

        <?php foreach ($pesquisasActividad as $pid): ?>
            <?php
            $info = $infoPesquisas[$pid] ?? null;
            if (! $info) continue;

            $esActiva    = ((int) $pid === (int) $tipoPesquisaId);
            $yaEvaluada  = in_array($pid, $pesquisasEvaluadas);
            $clases      = 'sidebar__item';
            if ($esActiva)   $clases .= ' active';
            if ($yaEvaluada) $clases .= ' evaluado';

            $urlPesquisa = base_url("evaluaciones/formulario/{$beneficiario['id_beneficiario']}/{$pid}")
                . ($jornadaId ? "?jornada_id={$jornadaId}" : "?centro_id={$centroId}");
            ?>
            <a href="<?= $urlPesquisa ?>"
                class="<?= $clases ?>"
                title="<?= esc($info['nombre']) ?>"
                aria-label="<?= esc($info['nombre']) ?>">
                <img src="<?= base_url('img/' . ($esActiva ? $info['img'] : $info['gris'])) ?>"
                    alt="<?= esc($info['nombre']) ?>">
            </a>
        <?php endforeach; ?>
    </aside>

    <main class="lab-main">
        <div class="lab-header">
            <div class="lab-title-row">
                <div class="lab-icon">
                    <img src="<?= base_url('img/' . ($infoPesquisas[$tipoPesquisaId]['img'] ?? 'sanguinea2.svg')) ?>"
                        alt="<?= esc($nombrePesquisa) ?>">
                </div>
                <div>
                    <h1><?= esc($nombrePesquisa) ?></h1>
                    <p><?= $nombreCompleto ?></p>
                    <span class="lab-badge <?= $esEdicion ? 'edit' : 'new' ?>">
                        <?= $esEdicion ? 'Editando' : 'Nueva evaluación' ?>
                    </span>
                </div>
            </div>

            <div class="lab-progress">
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                    <span id="progressText">Progreso: 0 de <?= count($jsSections) ?> secciones completadas</span>
                    <a href="<?= $urlRetorno ?>" class="btn-volver">
                        <i class="bi bi-arrow-left"></i> Volver
                    </a>
                </div>
                <div class="progress-bar">
                    <div id="progressFill" class="progress-bar__fill"></div>
                </div>
            </div>
        </div>

        <nav class="stepper" aria-label="Secciones del formulario">
            <?php foreach ($jsSections as $index => $section): ?>
                <button class="step <?= $index === 0 ? 'active' : '' ?>" type="button" data-step="<?= esc($section['id']) ?>">
                    <span><?= $index + 1 ?></span>
                    <?= esc($section['title']) ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <section class="lab-layout">
            <div class="lab-content">
                <form id="formEvaluacion" autocomplete="off" novalidate>
                    <input type="hidden" name="beneficiario_id" value="<?= (int) $beneficiario['id_beneficiario'] ?>">
                    <input type="hidden" name="tipo_pesquisa_id" value="<?= (int) $tipoPesquisaId ?>">
                    <input type="hidden" name="jornada_id" value="<?= (int) $jornadaId ?>">
                    <input type="hidden" name="centro_id" value="<?= (int) $centroId ?>">
                    <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">
<?php
$fechaEvaluacionRaw = $evaluacionExistente['fecha_evaluacion'] ?? date('Y-m-d');
$fechaEvaluacionInput = date('Y-m-d', strtotime($fechaEvaluacionRaw));
?>

<div class="field field--full" style="margin-bottom: 18px;">
    <label for="eval_fecha_hidden">
        Fecha evaluación *
    </label>
    <input
        type="date"
        name="fecha_evaluacion"
        id="eval_fecha_hidden"
        value="<?= esc($fechaEvaluacionInput) ?>"
        required
    >
</div><br>

                    <?php foreach ($itemsFormulario as $sectionIndex => $items): ?>
                        <?php $nombreSeccion = $nombresSecciones[$sectionIndex] ?? ucfirst(str_replace('_', ' ', $sectionIndex)); ?>
                        <section class="form-section <?= array_key_first($itemsFormulario) === $sectionIndex ? 'active' : '' ?>" data-section="<?= esc($sectionIndex) ?>">
                            <div class="section-card">
                                <div class="section-header">
                                    <div>
                                        <h2><?= esc($nombreSeccion) ?></h2>
                                        <p>Complete los campos disponibles para esta sección.</p>
                                    </div>
                                    <div>
                                        <?php if (array_filter($items, static fn($item) => ! empty($item['obligatorio']))): ?>
                                            <span class="required-note">* Campos obligatorios</span>
                                        <?php endif; ?>
                                        <span class="section-status" data-status="<?= esc($sectionIndex) ?>">0/<?= count($items) ?> completados</span>
                                    </div>
                                </div>

                                <div class="form-grid">
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                        $codigo     = $item['codigo'];
                                        $valorPrev  = $valoresExistentes[$codigo] ?? '';
                                        $oculto     = ! empty($item['depende_de']) ? 'eval-campo-oculto' : '';
                                        $dependeAttr = '';
                                        if (! empty($item['depende_de'])) {
                                            $dependeAttr = 'data-depende-de="' . esc($item['depende_de']) . '" data-depende-valor="' . esc($item['depende_valor']) . '"';
                                        }
                                        $unidad = trim((string) ($item['unidad'] ?? ''));
                                        $ancho  = (int) ($item['ancho_col'] ?? 6);
                                        $span   = $ancho >= 12 ? 'field--full' : '';
                                        ?>

                                        <div class="field <?= $span ?> <?= $oculto ?>"
                                            id="wrap_<?= esc($codigo) ?>"
                                            data-code="<?= esc($codigo) ?>"
                                            <?= $dependeAttr ?>>
                                            <label for="campo_<?= esc($codigo) ?>">
                                                <?= esc($item['nombre']) ?><?= ! empty($item['obligatorio']) ? ' *' : '' ?>
                                            </label>

                                            <?php if ($item['tipo_dato'] === 'number'): ?>
                                                <?php if ($unidad !== ''): ?>
                                                    <div class="input-unit">
                                                        <input type="number" step="any"
                                                            name="campos[<?= esc($codigo) ?>]"
                                                            id="campo_<?= esc($codigo) ?>"
                                                            value="<?= esc($valorPrev) ?>"
                                                            <?= $item['valor_min'] !== null ? 'min="' . esc($item['valor_min']) . '"' : '' ?>
                                                            <?= $item['valor_max'] !== null ? 'max="' . esc($item['valor_max']) . '"' : '' ?>
                                                            <?= $item['placeholder'] ? 'placeholder="' . esc($item['placeholder']) . '"' : '' ?>
                                                            data-codigo="<?= esc($codigo) ?>">
                                                        <span><?= esc($unidad) ?></span>
                                                    </div>
                                                <?php else: ?>
                                                    <input type="number" step="any"
                                                        name="campos[<?= esc($codigo) ?>]"
                                                        id="campo_<?= esc($codigo) ?>"
                                                        value="<?= esc($valorPrev) ?>"
                                                        <?= $item['valor_min'] !== null ? 'min="' . esc($item['valor_min']) . '"' : '' ?>
                                                        <?= $item['valor_max'] !== null ? 'max="' . esc($item['valor_max']) . '"' : '' ?>
                                                        <?= $item['placeholder'] ? 'placeholder="' . esc($item['placeholder']) . '"' : '' ?>
                                                        data-codigo="<?= esc($codigo) ?>">
                                                <?php endif; ?>

                                            <?php elseif ($item['tipo_dato'] === 'select'): ?>
                                                <select name="campos[<?= esc($codigo) ?>]"
                                                    id="campo_<?= esc($codigo) ?>"
                                                    data-codigo="<?= esc($codigo) ?>">
                                                    <option value="">Seleccione una opción</option>
                                                    <?php foreach (($item['opciones'] ?? []) as $opt): ?>
                                                        <option value="<?= esc($opt['valor']) ?>"
                                                            <?= ($valorPrev == $opt['valor']) ? 'selected' : '' ?>>
                                                            <?= esc($opt['texto']) ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>

                                            <?php elseif ($item['tipo_dato'] === 'textarea'): ?>
                                                <textarea name="campos[<?= esc($codigo) ?>]"
                                                    id="campo_<?= esc($codigo) ?>"
                                                    rows="4"
                                                    data-codigo="<?= esc($codigo) ?>"
                                                    <?= $item['placeholder'] ? 'placeholder="' . esc($item['placeholder']) . '"' : '' ?>><?= esc($valorPrev) ?></textarea>

                                            <?php elseif ($item['tipo_dato'] === 'date'): ?>
                                                <input type="date"
                                                    name="campos[<?= esc($codigo) ?>]"
                                                    id="campo_<?= esc($codigo) ?>"
                                                    value="<?= esc($valorPrev) ?>"
                                                    data-codigo="<?= esc($codigo) ?>">

                                            <?php else: ?>
                                                <input type="text"
                                                    name="campos[<?= esc($codigo) ?>]"
                                                    id="campo_<?= esc($codigo) ?>"
                                                    value="<?= esc($valorPrev) ?>"
                                                    <?= $item['placeholder'] ? 'placeholder="' . esc($item['placeholder']) . '"' : '' ?>
                                                    data-codigo="<?= esc($codigo) ?>">
                                            <?php endif; ?>

                                            <?php if ($item['tipo_dato'] === 'number' && ($item['valor_min'] !== null || $item['valor_max'] !== null)): ?>
                                                <small>Rango normal: <?= $item['valor_min'] !== null ? esc($item['valor_min']) : '—' ?> - <?= $item['valor_max'] !== null ? esc($item['valor_max']) : '—' ?><?= $unidad ? ' ' . esc($unidad) : '' ?></small>
                                            <?php elseif ($unidad !== '' && $item['tipo_dato'] !== 'number'): ?>
                                                <small>Unidad: <?= esc($unidad) ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </section>
                    <?php endforeach; ?>

                    <section class="form-section" data-section="observaciones">
                        <div class="section-card">
                            <div class="section-header">
                                <div>
                                    <h2>Observaciones y remisión</h2>
                                    <p>Agregue comentarios generales y defina si requiere remisión.</p>
                                </div>
                                <span class="section-status" data-status="observaciones">0/<?= $itemRemitir ? '2' : '1' ?> completados</span>
                            </div>

                            <div class="form-grid">
                                <div class="field field--full">
                                    <label for="eval_observaciones">Observaciones generales</label>
                                    <textarea id="eval_observaciones"
                                        name="observaciones"
                                        maxlength="500"
                                        rows="5"
                                        placeholder="Escriba observaciones generales..."> <?= esc($obsExistente) ?></textarea>
                                    <small><span id="observacionesCounter">0</span>/500</small>
                                </div>

                                <?php if ($itemRemitir): ?>
                                    <?php $valorRemitir = $valoresExistentes[$itemRemitir['codigo']] ?? ''; ?>
                                    <div class="field">
                                        <label for="campo_<?= esc($itemRemitir['codigo']) ?>">Remitir a especialista</label>
                                        <select name="campos[<?= esc($itemRemitir['codigo']) ?>]"
                                            id="campo_<?= esc($itemRemitir['codigo']) ?>"
                                            data-codigo="<?= esc($itemRemitir['codigo']) ?>">
                                            <option value="">No definida</option>
                                            <?php foreach (($itemRemitir['opciones'] ?? []) as $opt): ?>
                                                <option value="<?= esc($opt['valor']) ?>"
                                                    <?= ($valorRemitir == $opt['valor']) ? 'selected' : '' ?>>
                                                    <?= esc($opt['texto']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <aside class="summary-panel">
                <div class="summary-card">
                    <h2>Resumen de evaluación</h2>

                    <div class="summary-row">
                        <span>Fecha de evaluación</span>
                        <strong id="summaryFecha"> <?= date('d/m/Y', strtotime($fechaEvaluacionInput)) ?></strong>
                    </div>

                    <div class="summary-row">
                        <span>Secciones completadas</span>
                        <strong id="summarySecciones">0/<?= count($jsSections) ?></strong>
                    </div>

                    <div class="summary-row">
                        <span>Alertas</span>
                        <strong id="summaryAlertas">0</strong>
                    </div>

                    <div class="summary-row">
                        <span>Remisión a especialista</span>
                        <strong id="summaryRemision">No definida</strong>
                    </div>
                </div>

                <div class="summary-card">
                    <h3>Observaciones</h3>
                    <p id="summaryObservaciones">Sin observaciones registradas.</p>
                </div>

                <div class="eval-error-msg" id="evalErrorMsg">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <span id="evalErrorText"></span>
                </div>
            </aside>
        </section>

        <div class="actions-bar">
            <button id="btnCancelar" type="button" class="btn btn--ghost">Cancelar</button>

            <button id="btnAnterior" type="button" class="btn btn--secondary">Anterior</button>
            <button id="btnSiguiente" type="button" class="btn btn--primary">Siguiente</button>
            <button id="btnGuardarEval" type="button" class="btn btn--primary" hidden>Guardar evaluación</button>
        </div>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const LAB_SECTIONS = <?= json_encode($jsSections, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const LAB_RANGES = <?= json_encode($jsRanges, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
    const URL_GUARDAR = '<?= base_url("evaluaciones/guardar") ?>';
    const URL_RETORNO = '<?= $urlRetorno ?>';
    const CSRF_TOKEN = '<?= csrf_token() ?>';
    const CSRF_HASH = '<?= csrf_hash() ?>';
    const DRAFT_KEY = 'evaluacion_borrador_<?= (int) $beneficiario['id_beneficiario'] ?>_<?= (int) $tipoPesquisaId ?>';
    const REMITIR_CODE = <?= json_encode($itemRemitir['codigo'] ?? '', JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;

    let currentSectionIndex = 0;

    const form = document.getElementById('formEvaluacion');
    const sectionEls = Array.from(document.querySelectorAll('.form-section'));
    const stepEls = Array.from(document.querySelectorAll('.step'));

    const progressText = document.getElementById('progressText');
    const progressFill = document.getElementById('progressFill');
   
    const summarySecciones = document.getElementById('summarySecciones');
    const summaryAlertas = document.getElementById('summaryAlertas');
    const summaryRemision = document.getElementById('summaryRemision');
    const summaryObservaciones = document.getElementById('summaryObservaciones');
    const observacionesGenerales = document.getElementById('eval_observaciones');
    const observacionesCounter = document.getElementById('observacionesCounter');
    const errorDiv = document.getElementById('evalErrorMsg');
    const errorText = document.getElementById('evalErrorText');

    const btnAnterior = document.getElementById('btnAnterior');
    const btnSiguiente = document.getElementById('btnSiguiente');
    const btnGuardar = document.getElementById('btnGuardarEval');

    const btnCancelar = document.getElementById('btnCancelar');
     const fechaEvaluacionInput = document.getElementById('fecha_evaluacion');
    const summaryFecha = document.getElementById('summaryFecha');
    if (fechaEvaluacionInput && summaryFecha) {
  fechaEvaluacionInput.addEventListener('change', () => {
    if (!fechaEvaluacionInput.value) {
      summaryFecha.textContent = 'Pendiente';
      return;
    }

    const [y, m, d] = fechaEvaluacionInput.value.split('-');
    summaryFecha.textContent = `${d}/${m}/${y}`;
  });
}
    function getFieldElement(fieldName) {
        if (fieldName === 'observaciones') {
            return document.getElementById('eval_observaciones');
        }

        return document.getElementById('campo_' + fieldName) ||
            document.querySelector(`[name="campos[${CSS.escape(fieldName)}]"]`) ||
            document.querySelector(`[name="${CSS.escape(fieldName)}"]`);
    }

    function getFieldValue(fieldName) {
        const element = getFieldElement(fieldName);
        if (!element) return '';

        if (element.type === 'radio') {
            const checked = document.querySelector(`[name="${element.name}"]:checked`);
            return checked ? checked.value : '';
        }

        return String(element.value || '').trim();
    }

    function setFieldValue(fieldName, value) {
        const element = getFieldElement(fieldName);
        if (!element) return;

        if (element.type === 'radio') {
            const radio = document.querySelector(`[name="${element.name}"][value="${CSS.escape(value)}"]`);
            if (radio) radio.checked = true;
            return;
        }

        element.value = value ?? '';
    }

    function setSection(sectionId) {
        const nextIndex = LAB_SECTIONS.findIndex(section => section.id === sectionId);
        if (nextIndex < 0) return;

        currentSectionIndex = nextIndex;

        sectionEls.forEach(sectionEl => {
            sectionEl.classList.toggle('active', sectionEl.dataset.section === sectionId);
        });

        stepEls.forEach((stepEl, index) => {
            const isActive = stepEl.dataset.step === sectionId;
            const isCompleted = isSectionCompleted(LAB_SECTIONS[index]);

            stepEl.classList.toggle('active', isActive);
            stepEl.classList.toggle('completed', isCompleted);
        });

        updateButtons();
        updateSummary();
    }

    function updateButtons() {
        const isFirst = currentSectionIndex === 0;
        const isLast = currentSectionIndex === LAB_SECTIONS.length - 1;

        btnAnterior.hidden = isFirst;
        btnSiguiente.hidden = isLast;
        btnGuardar.hidden = !isLast;
    }

    function goNext() {
        const currentSection = LAB_SECTIONS[currentSectionIndex];

        if (!validateRequiredFields(currentSection)) {
            return;
        }

        if (currentSectionIndex < LAB_SECTIONS.length - 1) {
            setSection(LAB_SECTIONS[currentSectionIndex + 1].id);
        }
    }

    function goPrevious() {
        if (currentSectionIndex > 0) {
            setSection(LAB_SECTIONS[currentSectionIndex - 1].id);
        }
    }

    function validateRequiredFields(section) {
        let isValid = true;

        section.required.forEach(fieldName => {
            const field = getFieldElement(fieldName);
            if (!field) return;

            const fieldWrapper = field.closest('.field');
            const value = getFieldValue(fieldName);

            if (!value) {
                isValid = false;
                field.classList.add('is-invalid');
                fieldWrapper?.classList.add('field--error');
            } else {
                field.classList.remove('is-invalid');
                fieldWrapper?.classList.remove('field--error');
            }
        });

        if (!isValid) {
            showToast('Complete los campos obligatorios antes de continuar.');
        }

        return isValid;
    }

    function isSectionCompleted(section) {
        if (section.required.length) {
            return section.required.every(fieldName => Boolean(getFieldValue(fieldName)));
        }

        return section.fields.some(fieldName => Boolean(getFieldValue(fieldName)));
    }

    function getSectionCompletion(section) {
        const completed = section.fields.filter(fieldName => Boolean(getFieldValue(fieldName))).length;
        return {
            completed,
            total: section.fields.length
        };
    }

    function updateSectionStatuses() {
        LAB_SECTIONS.forEach(section => {
            const status = document.querySelector(`[data-status="${CSS.escape(section.id)}"]`);
            const {
                completed,
                total
            } = getSectionCompletion(section);

            if (status) {
                status.textContent = `${completed}/${total} completados`;
            }
        });
    }

    function updateProgress() {
        const completedSections = LAB_SECTIONS.filter(isSectionCompleted).length;
        const totalSections = LAB_SECTIONS.length;
        const percent = totalSections ? Math.round((completedSections / totalSections) * 100) : 0;

        progressText.textContent = `Progreso: ${completedSections} de ${totalSections} secciones completadas`;
        progressFill.style.width = `${percent}%`;
        summarySecciones.textContent = `${completedSections}/${totalSections}`;
    }

    function getAlertCount() {
        return Object.entries(LAB_RANGES).reduce((count, [fieldName, range]) => {
            const rawValue = getFieldValue(fieldName);
            if (!rawValue) return count;

            const value = Number(rawValue);
            if (Number.isNaN(value)) return count;

            const underMin = range.min !== null && value < Number(range.min);
            const overMax = range.max !== null && value > Number(range.max);

            return underMin || overMax ? count + 1 : count;
        }, 0);
    }

    function updateFieldRangeAlerts() {
        Object.entries(LAB_RANGES).forEach(([fieldName, range]) => {
            const field = getFieldElement(fieldName);
            if (!field) return;

            const fieldWrapper = field.closest('.field');
            const rawValue = getFieldValue(fieldName);
            const value = Number(rawValue);

            fieldWrapper?.classList.remove('field--warning');

            if (!rawValue || Number.isNaN(value)) return;

            const underMin = range.min !== null && value < Number(range.min);
            const overMax = range.max !== null && value > Number(range.max);

            if (underMin || overMax) {
                fieldWrapper?.classList.add('field--warning');
            }
        });
    }

    function updateSummary() {
        const fecha = document.getElementById('eval_fecha_hidden')?.value || '';
        const remision = REMITIR_CODE ? getFieldValue(REMITIR_CODE) : '';
        const observaciones = getFieldValue('observaciones');

        summaryFecha.textContent = fecha ? formatDate(fecha) : 'No definida';
        summaryRemision.textContent = remision || 'No definida';
        summaryObservaciones.textContent = observaciones || 'Sin observaciones registradas.';
        summaryAlertas.textContent = String(getAlertCount());

        if (observacionesCounter && observacionesGenerales) {
            observacionesCounter.textContent = String(observacionesGenerales.value.length);
        }

        updateSectionStatuses();
        updateProgress();
        updateFieldRangeAlerts();
    }

    function formatDate(value) {
        const [year, month, day] = value.split('-');
        if (!year || !month || !day) return value;
        return `${day}/${month}/${year}`;
    }

    function getFormSnapshot() {
        const data = {};

        LAB_SECTIONS.forEach(section => {
            section.fields.forEach(fieldName => {
                data[fieldName] = getFieldValue(fieldName);
            });
        });

        return data;
    }



    function cancelForm() {
        const shouldCancel = window.confirm('¿Desea cancelar la evaluación? Los cambios no guardados se perderán.');
        if (!shouldCancel) return;

        form.reset();
        localStorage.removeItem(DRAFT_KEY);
        window.location.href = URL_RETORNO;
    }

    function activarDependencias() {
        document.querySelectorAll('[data-depende-de]').forEach(wrap => {
            const codigoPadre = wrap.dataset.dependeDe;
            const valorRequerido = wrap.dataset.dependeValor;
            const padre = getFieldElement(codigoPadre);

            if (!padre) return;

            const verificar = () => {
                const debeMostrar = getFieldValue(codigoPadre) === valorRequerido;
                wrap.classList.toggle('eval-campo-oculto', !debeMostrar);

                if (!debeMostrar) {
                    const input = wrap.querySelector('input, select, textarea');
                    if (input) input.value = '';
                }

                updateSummary();
            };

            padre.addEventListener('change', verificar);
            verificar();
        });
    }

    async function guardarEvaluacion() {
        const invalidSection = LAB_SECTIONS.find(section => !validateRequiredFields(section));

        if (invalidSection) {
            setSection(invalidSection.id);
            return;
        }

        errorDiv.style.display = 'none';
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.field--error').forEach(el => el.classList.remove('field--error'));

        btnGuardar.disabled = true;
        btnGuardar.textContent = 'Guardando...';

        try {
            const formData = new FormData(form);
            formData.set('observaciones', getFieldValue('observaciones'));
            formData.append(CSRF_TOKEN, CSRF_HASH);

            const res = await fetch(URL_GUARDAR, {
                method: 'POST',
                body: formData,
            });

            const data = await res.json();

            if (!data.ok) {
                if (data.campo) {
                    const campoErr = getFieldElement(data.campo);
                    if (campoErr) {
                        campoErr.classList.add('is-invalid');
                        campoErr.closest('.field')?.classList.add('field--error');
                        campoErr.scrollIntoView({
                            behavior: 'smooth',
                            block: 'center'
                        });
                        campoErr.focus();
                    }
                }

                errorText.textContent = data.mensaje || 'Error al guardar.';
                errorDiv.style.display = 'block';
                return;
            }

            localStorage.removeItem(DRAFT_KEY);

            Swal.fire({
                icon: 'success',
                title: '¡Evaluación guardada!',
                text: data.mensaje || 'Evaluación guardada correctamente.',
                confirmButtonColor: '#101a61',
                timer: 1800,
                showConfirmButton: false,
            }).then(() => {
                window.location.href = data.url_retorno || URL_RETORNO;
            });
        } catch (err) {
            console.error('Error guardando:', err);
            errorText.textContent = 'Error de conexión al guardar.';
            errorDiv.style.display = 'block';
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.textContent = 'Guardar evaluación';
        }
    }

    function showToast(message) {
        let toast = document.querySelector('.toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'toast';
            toast.setAttribute('role', 'status');
            toast.setAttribute('aria-live', 'polite');
            document.body.appendChild(toast);
        }

        toast.textContent = message;
        toast.classList.add('show');

        window.clearTimeout(showToast.timeout);
        showToast.timeout = window.setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    stepEls.forEach(stepEl => {
        stepEl.addEventListener('click', () => {
            setSection(stepEl.dataset.step);
        });
    });

    form.addEventListener('input', updateSummary);
    form.addEventListener('change', updateSummary);
    btnSiguiente.addEventListener('click', goNext);
    btnAnterior.addEventListener('click', goPrevious);
    btnGuardar.addEventListener('click', guardarEvaluacion);

    btnCancelar.addEventListener('click', cancelForm);

    loadDraft();
    activarDependencias();
    setSection(LAB_SECTIONS[0]?.id || 'observaciones');
    updateSummary();
</script>
<?= $this->endSection() ?>
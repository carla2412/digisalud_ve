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

$seccionCondicionesEspeciales = null;
$itemsCondicionesEspeciales = [];

foreach ($itemsFormulario as $seccion => $items) {
    $nombreSeccionBusqueda = strtolower($nombresSecciones[$seccion] ?? $seccion);

    if (
        (strpos($nombreSeccionBusqueda, 'condiciones') !== false || strpos($nombreSeccionBusqueda, 'codiciones') !== false)
        && strpos($nombreSeccionBusqueda, 'especial') !== false
    ) {
        $seccionCondicionesEspeciales = $seccion;
        $itemsCondicionesEspeciales = $items;
        unset($itemsFormulario[$seccion]);
        break;
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

foreach ($itemsCondicionesEspeciales as $item) {
    $observacionesSection['fields'][] = $item['codigo'];

    if (! empty($item['obligatorio'])) {
        $observacionesSection['required'][] = $item['codigo'];
    }

    if ($item['tipo_dato'] === 'number' && ($item['valor_min'] !== null || $item['valor_max'] !== null)) {
        $jsRanges[$item['codigo']] = [
            'min'   => $item['valor_min'] !== null ? (float) $item['valor_min'] : null,
            'max'   => $item['valor_max'] !== null ? (float) $item['valor_max'] : null,
            'label' => $item['nombre'],
        ];
    }
}

if ($itemRemitir) {
    $observacionesSection['fields'][] = $itemRemitir['codigo'];
}
$jsSections[] = $observacionesSection;
?>

<style>
   .labo-page {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr);
    width: 100%;
    min-height: calc(100vh - var(--app-header-height, 0px));
    position: relative;
    z-index: 0;
}
.labo-sidebar {
    background: var(--ds-dark);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 18px 0;
    box-shadow: 8px 0 28px rgba(8, 20, 79, 0.14);
}
.labo-sidebar_item {
    width: 42px;
    height: 42px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    text-decoration: none;
    color: #fff;
    background: rgba(255, 255, 255, 0.1);
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.12);
    transition: 0.18s ease;
}

    .labo-sidebar_item img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        filter: brightness(0) invert(1);
        opacity: .65;
    }

    .labo-sidebar_item:hover {
    transform: translateY(-1px);
    background: rgba(255, 255, 255, 0.18);
}
.labo-sidebar_item.active {
    background: #ffffff;
    color: var(--ds-primary);
    box-shadow: 0 10px 22px rgba(0, 0, 0, 0.18);
}

    .labo-sidebar_item:hover img,
    .labo-sidebar_item.active img {
        filter: none;
        opacity: 1;
    }

.labo-sidebar_item.done::after {
    content: "";
    position: absolute;
    right: -2px;
    bottom: -2px;
    width: 13px;
    height: 13px;
    background: var(--ds-success);
    border: 2px solid var(--ds-primary);
    border-radius: 999px;
}
    .labo-sidebar_item.labo-evaluado::after {
        content: '';
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--ds-success);
        border: 2px solid var(--ds-primary);
    }

    .labo-sidebar_item[title]::before {
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

    .labo-sidebar_item:hover[title]::before {
        opacity: 1;
    }

    .labo-main {
        min-width: 0;
    width: 100%;
    max-width: 1720px;
    margin: 0 auto;
    padding: 24px 28px 96px;
    }

    .labo-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-bottom: 16px;
        padding-bottom: calc(22px + 12px);
    }

    .labo-header h1 {
        margin: 0;
        color: var(--ds-dark);
        font-size: 1.35rem;
        font-weight: 900;
    }

    .labo-header p {
        margin: 2px 0 0;
        color: var(--ds-muted);
        font-size: .9rem;
    }

    .labo-title-row {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .labo-icon {
        width: 48px;
        height: 48px;
        border-radius: 18px;
        display: grid;
        place-items: center;
        background: #fff;
        box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
    }

    .labo-icon img {
        width: 30px;
        height: 30px;
    }



    .labo-badge {
        display: inline-flex;
        margin-top: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
    }

    .labo-badge.labo-new {
        background: #dbeafe;
        color: #1b7ae2;
    }

    .labo-badge.labo-edit {
        background: #fef3c7;
        color: #92400e;
    }

    .labo-progress {
        min-width: 280px;
        font-size: .78rem;
        font-weight: 700;
        color: var(--ds-muted);
    }

    .labo-progress-bar {
        width: 100%;
        height: 9px;
        margin-top: 8px;
        border-radius: 999px;
        background: #dbe3ef;
        overflow: hidden;
    }

    .labo-progress-bar__fill {
        height: 100%;
        width: 0;
        border-radius: inherit;
        background: var(--ds-primary);
        transition: width .25s ease;
    }

    .labo-btn-volver {
        color: var(--ds-muted);
        text-decoration: none;
        font-size: .85rem;
        font-weight: 700;
    }

    .labo-btn-volver:hover {
        color: var(--ds-primary);
    }

    .labo-stepper {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 18px;
    }

    .labo-step {
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-muted);
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

    .labo-step span {
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #eef2f7;
        display: grid;
        place-items: center;
        color: var(--ds-muted);
    }

    .labo-step.active,
    .labo-step.labo-completed {
        border-color: var(--ds-primary);
        color: var(--ds-primary);
    }

    .labo-step.active span,
    .labo-step.labo-completed span {
        background: var(--ds-primary);
        color: #fff;
    }

    .labo-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 320px;
        gap: 18px;
        align-items: start;
    }

    .labo-content {
        min-width: 0;
    }

    .labo-form-section {
        display: none;
    }

    .labo-form-section.active {
        display: block;
    }

    .labo-section-card,
    .labo-summary-card {
        background: var(--ds-bg);
        border: 1px solid var(--lab-border);
        border-radius: 22px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .06);
    }

    .labo-section-card {
        padding: 22px;
    }

    .labo-section-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .labo-section-header h2,
    .labo-summary-card h2,
    .labo-summary-card h3 {
        margin: 0;
        color: var(--ds-dark);
        font-size: 1rem;
        font-weight: 900;
    }

    .labo-section-header p {
        margin: 4px 0 0;
        color: var(--ds-muted);
        font-size: .84rem;
    }

    .labo-section-status,
    .labo-required-note {
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
        color: var(--ds-muted);
        font-size: .76rem;
        font-weight: 800;
    }

    .labo-required-note {
        color: var(--ds-danger);
        margin-right: 8px;
    }

    .labo-form-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px;
    }

    .labo-field {
        grid-column: span 6;
    }

    .labo-field--full {
        grid-column: 1 / -1;
    }

    .labo-field label {
        display: block;
        color: var(--ds-dark);
        font-size: .78rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .labo-field input,
    .labo-field select,
    .labo-field textarea {
        width: 100%;
        border: 1.5px solid var(--ds-border);
        border-radius: 12px;
        background: #fff;
        color: var(--ds-text);
        font-size: .86rem;
        padding: 10px 12px;
        outline: none;
        transition: .15s ease;
    }

    .labo-field input:focus,
    .labo-field select:focus,
    .labo-field textarea:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .labo-field small {
        display: block;
        margin-top: 5px;
        color: var(--ds-muted);
        font-size: .72rem;
    }

    .labo-input-unit {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--ds-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: .15s ease;
    }

    .labo-input-unit:focus-within {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .labo-input-unit input {
        border: 0;
        border-radius: 0;
        box-shadow: none !important;
    }

    .labo-input-unit span {
        padding: 0 12px;
        color: var(--ds-muted);
        font-size: .76rem;
        font-weight: 800;
        border-left: 1px solid var(--ds-border);
        white-space: nowrap;
    }

    .labo-segmented {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .labo-segmented label {
        margin: 0;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid var(--ds-border);
        border-radius: 999px;
        padding: 8px 12px;
        cursor: pointer;
    }

    .labo-field--error input,
    .labo-field--error select,
    .labo-field--error textarea,
    .labo-field--error .labo-input-unit,
    .is-invalid {
        border-color: var(--ds-danger) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .08) !important;
    }

    .labo-field--warning input,
    .labo-field--warning .labo-input-unit {
        border-color: var(--ds-warning) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .1) !important;
    }

    .labo-eval-campo-oculto {
        display: none !important;
    }

    .labo-summary-panel {
        display: flex;
        flex-direction: column;
        gap: 14px;
        position: sticky;
        top: 16px;
    }

    .labo-summary-card {
        padding: 18px;
    }

    .labo-summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: .82rem;
    }

    .labo-summary-row:last-child {
        border-bottom: 0;
    }

    .labo-summary-row span {
        color: var(--ds-muted);
    }

    .labo-summary-row strong {
        color: var(--ds-primary);
        text-align: right;
    }

    #summaryObservaciones {
        color: var(--ds-muted);
        font-size: .84rem;
        margin: 10px 0 0;
        word-break: break-word;
    }

    .labo-actions-bar {
        position: sticky;
        bottom: 0;
        z-index: 20;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 10px;
        width: 100%;
        max-width: 100%;
        min-height: 22px;
        margin: 18px 0 0;
        padding: 14px 0 0;
        background: transparent;
        border-top: 1px solid var(--ds-border);
        box-sizing: border-box;
    }

    .labo-actions-bar .btn {
        border: 1px solid var(--ds-border);
        min-height: 44px;
        padding: 0 18px;
        border-radius: 12px;
        font-size: .86rem;
        font-weight: 900;
        cursor: pointer;
        transition: .2s ease;
    }

    .labo-actions-bar .btn:hover:not(:disabled) {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(15, 23, 42, .10);
    }

    .labo-btn--primary {
        background: var(--ds-primary);
        color: #fff;
    }

    .labo-btn--secondary {
        background: var(--ds-primary-light);
        color: var(--ds-primary);
    }

    .labo-btn--ghost {
        background: transparent;
        color: var(--ds-muted);
    }

    .btn:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    .labo-eval-error-msg,
    .labo-toast {
        border-radius: 12px;
        padding: 10px 12px;
        font-size: .82rem;
    }

    .labo-eval-error-msg {
        display: none;
        margin-top: 12px;
        background: #fef2f2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .labo-toast {
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

    .labo-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 1100px) {
        .labo-layout {
            grid-template-columns: 1fr;
        }

        .labo-summary-panel {
            position: static;
        }
    }

    @media (max-width: 760px) {
        .labo-page {
            grid-template-columns: 1fr;
        }

        .labo-sidebar {
            flex-direction: row;
            overflow-x: auto;
            justify-content: flex-start;
            padding: 10px 12px;
        }

        .labo-main {
            padding: 18px 14px 92px;
        }

        .labo-header {
            align-items: flex-start;
            flex-direction: column;
        }

        .labo-progress {
            min-width: 0;
            width: 100%;
        }

        .labo-field {
            grid-column: 1 / -1;
        }

        .labo-actions-bar {
            margin: 0 -14px -18px;
            flex-wrap: wrap;
            padding: 12px 14px;
        }

        .btn {
            flex: 1 1 auto;
        }
    }


    /* UX refresh: mantiene la lógica, estructura PHP e iconografía existente de CI4 */
    body {
        background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
    }

    .labo-page {
        background: transparent;
    }

    .labo-sidebar {
        background: linear-gradient(180deg, #102073 0%, #08144f 100%);
        box-shadow: 8px 0 28px rgba(8, 20, 79, .12);
    }


    .labo-sidebar_item:hover,
    .labo-sidebar_item.active {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(0, 0, 0, .18);
    }

    .labo-main {
        max-width: 1720px;
        width: 100%;
        margin: 0 auto;
    }

    .labo-header {
        background: rgba(255, 255, 255, .86);
        border: 1px solid var(--ds-border);
        border-radius: 26px;
        padding: 22px;
        margin-bottom: 18px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, .07);
        backdrop-filter: blur(10px);
    }

    .labo-icon {
        border: 1px solid var(--ds-border);
        box-shadow: 0 12px 26px rgba(16, 26, 97, .10);
    }

    .labo-badge {
        letter-spacing: .01em;
    }

    .labo-progress {
        background: #f8fbff;
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 14px;
    }

    .labo-progress-bar {
        height: 10px;
        background: #e5ecf7;
    }

    .labo-stepper {
        flex-wrap: nowrap;
        overflow-x: auto;
        padding: 0 2px 12px;
        scroll-snap-type: x proximity;
    }

    .labo-stepper::-webkit-scrollbar {
        height: 7px;
    }

    .labo-stepper::-webkit-scrollbar-thumb {
        background: #c8d4e8;
        border-radius: 999px;
    }

    .labo-step {
        scroll-snap-align: start;
        min-height: 42px;
        background: rgba(255, 255, 255, .92);
        box-shadow: 0 8px 18px rgba(15, 23, 42, .04);
    }

    .labo-step:hover {
        border-color: var(--ds-primary-dark);
        color: var(--ds-primary-dark);
        transform: translateY(-1px);
    }

    .labo-step.labo-completed span::after {
        content: '✓';
        font-size: .72rem;
        line-height: 1;
    }

    .labo-step.labo-completed span {
        font-size: 0;
        background: var(--ds-success);
    }

    .labo-layout {
        gap: 22px;
    }

    .labo-section-card,
    .labo-summary-card {
        border-radius: 24px;
        box-shadow: 0 18px 44px rgba(15, 23, 42, .07);
    }

    .labo-section-card {
        padding: 24px;
    }

    .labo-section-header {
        align-items: flex-start;
        padding-bottom: 16px;
        border-bottom: 1px solid #edf2f7;
    }

    .labo-section-status {
        background: var(--ds-primary-light);
        color: var(--ds-primary);
        border-radius: 999px;
        padding: 6px 10px;
    }

    .labo-field {
        background: #fbfdff;
        border: 1px solid #edf2f7;
        border-radius: 18px;
        padding: 14px;
        transition: .18s ease;
    }

    .labo-field:focus-within {
        background: #fff;
        border-color: rgba(16, 26, 97, .22);
        box-shadow: 0 12px 28px rgba(16, 26, 97, .08);
        transform: translateY(-1px);
    }
 
    .labo-field small {
        background: #f1f5fb;
        border-radius: 999px;
        display: inline-flex;
        padding: 4px 9px;
        margin-top: 8px;
    }

    .labo-field--warning small {
        background: #fff7ed;
        color: #9a3412;
    }

    .labo-summary-panel {
        top: 18px;
    }

    .labo-summary-card h2,
    .labo-summary-card h3 {
        margin-bottom: 6px;
    }

    .labo-summary-row strong {
        font-weight: 900;
    }

    .labo-actions-bar {
        position: sticky;
        bottom: 0;
        background: rgba(248, 251, 255, .92);
        backdrop-filter: blur(10px);
        border: 1px solid var(--ds-border);
        border-radius: 20px 20px 0 0;
        padding: 12px;
        box-shadow: 0 -16px 36px rgba(15, 23, 42, .08);
    }

    .labo-actions-help {
        margin-right: auto;
        color: var(--lab-muted);
        font-size: .78rem;
        font-weight: 800;
    }

    .labo-btn--primary {
        border-color: var(--ds-primary) !important;
        box-shadow: 0 10px 22px rgba(16, 26, 97, .18);
    }

    .labo-btn--secondary {
        border-color: transparent !important;
    }

    .labo-btn--ghost:hover {
        background: #fff;
        color: var(--ds-primary);
    }

    .labo-toast {
        box-shadow: 0 18px 36px rgba(15, 23, 42, .22);
    }

    @media (max-width: 760px) {
        .labo-header {
            border-radius: 20px;
            padding: 18px;
        }

        .labo-section-card {
            padding: 18px;
        }

        .labo-section-header {
            flex-direction: column;
        }

        .labo-actions-help {
            flex: 1 0 100%;
            text-align: center;
            order: -1;
            margin: 0 0 4px;
        }
    }
    

    /* Aside homologado con vacunación */
    .labo-sidebar {
        background: var(--ds-dark);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 18px 0;
        box-shadow: 8px 0 28px rgba(8, 20, 79, .12);
    }

    .labo-sidebar_item {
        width: 42px;
        height: 42px;
        border-radius: 16px;
        border: 0;
        display: grid;
        place-items: center;
        background: rgba(255, 255, 255, 0.1);
        text-decoration: none;
        position: relative;
        transition: .2s ease;
        filter: brightness(0) invert(1);
    }

    .labo-sidebar_item img {
        width: 26px;
        height: 26px;
        opacity: 1;
        filter: none;
    }

    .labo-sidebar_item.active {
        filter: brightness(1) invert(0);
        background: #fff;
    }

    .labo-sidebar_item:hover {
        filter: none;
        opacity: 1;
        background: #fff;
    }

    .labo-sidebar_item:hover img,
    .labo-sidebar_item.active img {
        filter: none;
        opacity: 1;
    }

    .labo-sidebar_item.done::after,
    .labo-sidebar_item.labo-evaluado::after {
        content: '';
        position: absolute;
        right: -2px;
        bottom: -2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: var(--ds-success);
        border: 2px solid var(--ds-primary);
    }

    @media (max-width: 1180px) {
        .labo-sidebar {
            flex-direction: row;
            overflow-x: auto;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            box-shadow: 0 8px 24px rgba(8, 20, 79, 0.14);
        }

        .labo-sidebar_item {
            flex: 0 0 48px;
        }
    }
</style>

<div class="labo-page" data-page="evaluacion">
      <aside class="labo-sidebar" aria-label="Menú de pesquisas">
        <?php foreach ($pesquisasActividad as $pid): ?>
            <?php
            $info = $infoPesquisas[$pid] ?? null;
            if (!$info) continue;

            $esActiva   = ((int) $pid === (int) $tipoPesquisaId);
            $yaEvaluada = in_array($pid, $pesquisasEvaluadas);

            $clases = 'labo-sidebar_item';
            if ($esActiva)   $clases .= ' active';
            if ($yaEvaluada) $clases .= ' done';

            $urlPesquisa = base_url("evaluaciones/formulario/{$beneficiario['id_beneficiario']}/{$pid}")
                . ($jornadaId ? "?jornada_id={$jornadaId}" : '')
                . ($centroId  ? (($jornadaId ? '&' : '?') . "centro_id={$centroId}") : '');

            $imgFile = $esActiva
                ? ($info['img'] ?? 'laboratorio.svg')
                : ($info['gris'] ?? 'laboratorio-gris.svg');
            ?>

            <a class="<?= esc($clases) ?>"
               href="<?= esc($urlPesquisa) ?>"
               title="<?= esc($info['nombre']) ?>"
               aria-label="<?= esc($info['nombre']) ?>">
                <img src="<?= base_url("img/{$imgFile}") ?>"
                     alt="<?= esc($info['nombre']) ?>">
            </a>
        <?php endforeach; ?>
    </aside>


    <main class="labo-main">
        <div class="labo-header">
            <div class="labo-title-row">
                <div class="labo-icon">
                    <img src="<?= base_url('img/' . ($infoPesquisas[$tipoPesquisaId]['img'] ?? 'sanguinea2.svg')) ?>"
                        alt="<?= esc($nombrePesquisa) ?>">
                </div>
                <div>
                    <h1><?= esc($nombrePesquisa) ?></h1>
                    <p><?= $nombreCompleto ?></p>
                    <span class="labo-badge <?= $esEdicion ? 'labo-edit' : 'labo-new' ?>">
                        <?= $esEdicion ? 'Editando' : 'Nueva evaluación' ?>
                    </span>
                </div>
            </div>

            <div class="labo-progress">
                <div style="display:flex; justify-content:space-between; gap:12px; align-items:center;">
                    <span id="progressText">Progreso: 0 de <?= count($jsSections) ?> secciones completadas</span>

                </div>
                <div class="labo-progress-bar">
                    <div id="progressFill" class="labo-progress-bar__fill"></div>
                </div>
            </div>
        </div>

        <nav class="labo-stepper" aria-label="Secciones del formulario">
            <?php foreach ($jsSections as $index => $section): ?>
                <button class="labo-step <?= $index === 0 ? 'active' : '' ?>" type="button" data-step="<?= esc($section['id']) ?>">
                    <span><?= $index + 1 ?></span>
                    <?= esc($section['title']) ?>
                </button>
            <?php endforeach; ?>
        </nav>

        <section class="labo-layout">
            <div class="labo-content">
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

                    <div class="labo-field labo-field--full" style="margin-bottom: 18px;">
                        <label for="eval_fecha_hidden">
                            Fecha evaluación *
                        </label>
                        <input
                            type="date"
                            name="fecha_evaluacion"
                            id="eval_fecha_hidden"
                            value="<?= esc($fechaEvaluacionInput) ?>"
                            required>
                    </div><br>

                    <?php foreach ($itemsFormulario as $sectionIndex => $items): ?>
                        <?php $nombreSeccion = $nombresSecciones[$sectionIndex] ?? ucfirst(str_replace('_', ' ', $sectionIndex)); ?>
                        <section class="labo-form-section <?= array_key_first($itemsFormulario) === $sectionIndex ? 'active' : '' ?>" data-section="<?= esc($sectionIndex) ?>">
                            <div class="labo-section-card">
                                <div class="labo-section-header">
                                    <div>
                                        <h2><?= esc($nombreSeccion) ?></h2>
                                        <p>Complete los campos disponibles para esta sección.</p>
                                    </div>
                                    <div>
                                        <?php if (array_filter($items, static fn($item) => ! empty($item['obligatorio']))): ?>
                                            <span class="labo-required-note">* Campos obligatorios</span>
                                        <?php endif; ?>
                                        <span class="labo-section-status" data-status="<?= esc($sectionIndex) ?>">0/<?= count($items) ?> completados</span>
                                    </div>
                                </div>

                                <div class="labo-form-grid">
                                    <?php foreach ($items as $item): ?>
                                        <?php
                                        $codigo     = $item['codigo'];
                                        $valorPrev  = $valoresExistentes[$codigo] ?? '';
                                        $oculto     = ! empty($item['depende_de']) ? 'labo-eval-campo-oculto' : '';
                                        $dependeAttr = '';
                                        if (! empty($item['depende_de'])) {
                                            $dependeAttr = 'data-depende-de="' . esc($item['depende_de']) . '" data-depende-valor="' . esc($item['depende_valor']) . '"';
                                        }
                                        $unidad = trim((string) ($item['unidad'] ?? ''));
                                        $ancho  = (int) ($item['ancho_col'] ?? 6);
                                        $span   = $ancho >= 12 ? 'labo-field--full' : '';
                                        ?>

                                        <div class="labo-field <?= $span ?> <?= $oculto ?>"
                                            id="wrap_<?= esc($codigo) ?>"
                                            data-code="<?= esc($codigo) ?>"
                                            <?= $dependeAttr ?>>
                                            <label for="campo_<?= esc($codigo) ?>">
                                                <?= esc($item['nombre']) ?><?= ! empty($item['obligatorio']) ? ' *' : '' ?>
                                            </label>

                                            <?php if ($item['tipo_dato'] === 'number'): ?>
                                                <?php if ($unidad !== ''): ?>
                                                    <div class="labo-input-unit">
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

                    <section class="labo-form-section" data-section="observaciones">
                        <div class="labo-section-card">
                            <div class="labo-section-header">
                                <div>
                                    <h2>Observaciones y remisión</h2>
                                    <p>Agregue comentarios generales y defina si requiere remisión.</p>
                                </div>
                                <span class="labo-section-status" data-status="observaciones">0/<?= 1 + count($itemsCondicionesEspeciales) + ($itemRemitir ? 1 : 0) ?> completados</span>
                            </div>

                            <div class="labo-form-grid">
                                <div class="labo-field labo-field--full">
                                    <label for="eval_observaciones">Observaciones generales</label>
                                    <textarea id="eval_observaciones"
                                        name="observaciones"
                                        maxlength="500"
                                        rows="5"
                                        placeholder="Escriba observaciones generales..."> <?= esc($obsExistente) ?></textarea>
                                    <small><span id="observacionesCounter">0</span>/500</small>
                                </div>


                                <?php foreach ($itemsCondicionesEspeciales as $item): ?>
                                    <?php
                                    $codigo     = $item['codigo'];
                                    $valorPrev  = $valoresExistentes[$codigo] ?? '';
                                    $oculto     = ! empty($item['depende_de']) ? 'labo-eval-campo-oculto' : '';
                                    $dependeAttr = '';
                                    if (! empty($item['depende_de'])) {
                                        $dependeAttr = 'data-depende-de="' . esc($item['depende_de']) . '" data-depende-valor="' . esc($item['depende_valor']) . '"';
                                    }
                                    $unidad = trim((string) ($item['unidad'] ?? ''));
                                    $ancho  = (int) ($item['ancho_col'] ?? 6);
                                    $span   = $ancho >= 12 ? 'labo-field--full' : '';
                                    ?>

                                    <div class="labo-field <?= $span ?> <?= $oculto ?>"
                                        id="wrap_<?= esc($codigo) ?>"
                                        data-code="<?= esc($codigo) ?>"
                                        <?= $dependeAttr ?>>
                                        <label for="campo_<?= esc($codigo) ?>">
                                            <?= esc($item['nombre']) ?><?= ! empty($item['obligatorio']) ? ' *' : '' ?>
                                        </label>

                                        <?php if ($item['tipo_dato'] === 'number'): ?>
                                            <?php if ($unidad !== ''): ?>
                                                <div class="labo-input-unit">
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

                                <?php if ($itemRemitir): ?>
                                    <?php $valorRemitir = $valoresExistentes[$itemRemitir['codigo']] ?? ''; ?>
                                    <div class="labo-field">
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

            <aside class="labo-summary-panel">
                <div class="labo-summary-card">
                    <h2>Resumen de evaluación</h2>

                    <div class="labo-summary-row">
                        <span>Fecha de evaluación</span>
                        <strong id="summaryFecha"> <?= date('d/m/Y', strtotime($fechaEvaluacionInput)) ?></strong>
                    </div>

                    <div class="labo-summary-row">
                        <span>Secciones completadas</span>
                        <strong id="summarySecciones">0/<?= count($jsSections) ?></strong>
                    </div>

                    <div class="labo-summary-row">
                        <span>Alertas</span>
                        <strong id="summaryAlertas">0</strong>
                    </div>

                    <div class="labo-summary-row">
                        <span>Remisión a especialista</span>
                        <strong id="summaryRemision">No definida</strong>
                    </div>
                </div>

                <div class="labo-summary-card">
                    <h3>Observaciones</h3>
                    <p id="summaryObservaciones">Sin observaciones registradas.</p>
                </div>
                <?php if ((int) $tipoPesquisaId === 2): ?>
                    <?= view('partials/lab_badges', [
                        'beneficiario' => $beneficiario,
                        'jornadaId'    => $jornadaId,
                        'centroId'     => $centroId,
                    ]) ?>
                <?php endif; ?>
                <div class="labo-eval-error-msg" id="evalErrorMsg">
                    <i class="bi bi-exclamation-triangle me-1"></i>
                    <span id="evalErrorText"></span>
                </div>
            </aside>
        </section>

        <div class="labo-actions-bar">
            <div class="labo-actions-help" aria-live="polite">Navegación rápida: usa ← Anterior y → Siguiente.</div>
            <button id="btnCancelar" type="button" class="btn labo-btn--ghost">Cancelar</button>

            <button id="btnAnterior" type="button" class="btn labo-btn--secondary">Anterior</button>
            <button id="btnSiguiente" type="button" class="btn labo-btn--primary">Siguiente</button>
            <button id="btnGuardarEval" type="button" class="btn labo-btn--primary" hidden>Guardar evaluación</button>
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
    const sectionEls = Array.from(document.querySelectorAll('.labo-form-section'));
    const stepEls = Array.from(document.querySelectorAll('.labo-step'));

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
    const fechaEvaluacionInput = document.getElementById('eval_fecha_hidden');
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
            stepEl.classList.toggle('labo-completed', isCompleted);
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

            const fieldWrapper = field.closest('.labo-field');
            const value = getFieldValue(fieldName);

            if (!value) {
                isValid = false;
                field.classList.add('is-invalid');
                fieldWrapper?.classList.add('labo-field--error');
            } else {
                field.classList.remove('is-invalid');
                fieldWrapper?.classList.remove('labo-field--error');
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

            const fieldWrapper = field.closest('.labo-field');
            const rawValue = getFieldValue(fieldName);
            const value = Number(rawValue);

            fieldWrapper?.classList.remove('labo-field--warning');

            if (!rawValue || Number.isNaN(value)) return;

            const underMin = range.min !== null && value < Number(range.min);
            const overMax = range.max !== null && value > Number(range.max);

            if (underMin || overMax) {
                fieldWrapper?.classList.add('labo-field--warning');
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



    function loadDraft() {
        // Se mantiene como punto seguro de extensión para borradores sin alterar el flujo actual.
        return null;
    }

    function cancelForm() {
        Swal.fire({
            title: '¿Desea cancelar la evaluación?',
            text: 'Los cambios no guardados se perderán.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, cancelar',
            cancelButtonText: 'No, continuar',
            reverseButtons: true,
            confirmButtonColor: '#3695f5',
            cancelButtonColor: '#999b9c'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = URL_RETORNO;
            }
        });
    }

    function activarDependencias() {
        document.querySelectorAll('[data-depende-de]').forEach(wrap => {
            const codigoPadre = wrap.dataset.dependeDe;
            const valorRequerido = wrap.dataset.dependeValor;
            const padre = getFieldElement(codigoPadre);

            if (!padre) return;

            const verificar = () => {
                const debeMostrar = getFieldValue(codigoPadre) === valorRequerido;
                wrap.classList.toggle('labo-eval-campo-oculto', !debeMostrar);

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
        form.querySelectorAll('.labo-field--error').forEach(el => el.classList.remove('labo-field--error'));

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
                        campoErr.closest('.labo-field')?.classList.add('labo-field--error');
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
        let toast = document.querySelector('.labo-toast');

        if (!toast) {
            toast = document.createElement('div');
            toast.className = 'labo-toast';
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

    document.addEventListener('keydown', event => {
        const tagName = event.target?.tagName?.toLowerCase();
        const isTyping = ['input', 'select', 'textarea'].includes(tagName);

        if (isTyping) return;

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            if (!btnSiguiente.hidden) goNext();
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            goPrevious();
        }
    });

    loadDraft();
    activarDependencias();
    setSection(LAB_SECTIONS[0]?.id || 'observaciones');
    updateSummary();
</script>
<?php if ((int) $tipoPesquisaId === 2): ?>
    <script src="<?= base_url('js/lab-interpretacion.js') ?>"></script>
<?php endif; ?>

<?= $this->endSection() ?>
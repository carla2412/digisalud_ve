<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>


<?php

/**
 * Vista: evaluaciones/vacunacion.php
 * Evaluación de Vacunación — tipo_pesquisa_id = 6
 *
 * Variables recibidas del controlador (EvaluacionesController::formulario):
 *   $beneficiario, $tipoPesquisa, $tipoPesquisaId, $jornadaId, $centroId,
 *   $itemsAgrupados, $nombresSecciones, $evaluacionExistente, $valoresExistentes,
 *   $pesquisasActividad, $pesquisasEvaluadas, $infoPesquisas
 */

// ── Helpers ──
$esEdicion     = !empty($evaluacionExistente);
$evaluacionId  = $esEdicion ? $evaluacionExistente['id_evaluacion'] : '';
$fechaHoy      = date('Y-m-d');
$fechaEval     = $esEdicion ? date('Y-m-d', strtotime($evaluacionExistente['fecha_evaluacion'])) : $fechaHoy;
$observaciones = $esEdicion ? ($evaluacionExistente['observaciones'] ?? '') : '';

// ── Definición de vacunas para renderizar ──
// codigo => [nombre visible, descripción corta]
$vacunasDefinidas = [
    'bcg'                              => ['B.C.G.', 'Vacuna contra tuberculosis'],
    'dt'                               => ['D.T.', 'Difteria y tétanos'],
    'dtp'                              => ['D.T.P.', 'Difteria, tétanos y tosferina'],
    'fiebre_amarilla'                  => ['Fiebre Amarilla', 'Vacuna antiamarílica'],
    'fiebre_tifoidea'                  => ['Fiebre Tifoidea', 'Vacuna antitifoidea'],
    'gammaglobulina'                   => ['Gammaglobulina', 'Inmunización pasiva'],
    'hepatitis_a'                      => ['Hepatitis A', 'Esquema hepatitis A'],
    'hepatitis_b'                      => ['Hepatitis B', 'Esquema hepatitis B'],
    'hib'                              => ['H.I.B.', 'Haemophilus influenzae tipo B'],
    'influenza_antigripal'             => ['Influenza (Antigripal)', 'Vacuna antigripal'],
    'meningococo'                      => ['Meningococo', 'Vacuna meningocócica'],
    'inmunoglobulina'                  => ['Inmunoglobulina', 'Inmunización pasiva específica'],
    'neumococo'                        => ['Neumococo', 'Vacuna antineumocócica'],
    'pentavalente'                     => ['Pentavalente', 'Vacuna pentavalente'],
    'poliomielitis'                    => ['Poliomielitis', 'Vacuna antipolio'],
    'ppd'                              => ['P.P.D.', 'Prueba de tuberculina'],
    'rabia'                            => ['Rabia', 'Vacuna antirrábica'],
    'rotavirus'                        => ['Rotavirus', 'Vacuna contra rotavirus'],
    'rubeola'                          => ['Rubéola', 'Vacuna contra rubéola'],
    'sarampion'                        => ['Sarampión', 'Vacuna contra sarampión'],
    'suero_antiescorpionico_antiofidico' => ['Suero Antiescorpiónico y Antiofídico', 'Antivenenos'],
    'tetravalente'                     => ['Tetravalente', 'Vacuna tetravalente'],
    'toxoide'                          => ['Toxoide', 'Toxoide tetánico/diftérico'],
    'trivalente_viral'                 => ['Trivalente Viral', 'Sarampión, rubéola y parotiditis'],
    'varicela'                         => ['Varicela', 'Vacuna contra varicela'],
    'vph'                              => ['V.P.H.', 'Virus del papiloma humano'],
    'covid19'                          => ['COVID-19', 'Vacuna contra SARS-CoV-2'],
];

// Campos de la sección "aplicacion" que se renderizan en el step 2
$camposAplicacion = [
    'responsable_aplicacion',
    'cargo_responsable',
    'lugar_aplicacion',
    'dosis',
    'sitio_aplicacion',
    'proxima_dosis',
];

// Campos de la sección "control" que se renderizan en el step 3
# REEMPLAZAR POR:
$camposAplicacion = [
    'responsable_aplicacion',
    'cargo_responsable',
    'lugar_aplicacion',
    'sitio_aplicacion',
];
// Helper: obtener valor existente de un campo
function evalVacValor($codigo, $valoresExistentes)
{
    return $valoresExistentes[$codigo] ?? '';
}

// Helper: normalizar dosis de vacunación guardadas por versiones anteriores
function evalVacNormalizarDosis($valor)
{
    $valor = trim((string) $valor);
    $mapa = [
        '1_dosis' => '1_dosis',
        '1ra' => '1_dosis',
        'primera' => '1_dosis',
        'primera_dosis' => '1_dosis',
        '2_dosis' => '2_dosis',
        '2da' => '2_dosis',
        'segunda' => '2_dosis',
        'segunda_dosis' => '2_dosis',
        '3_dosis' => '3_dosis',
        '3ra' => '3_dosis',
        'tercera' => '3_dosis',
        'tercera_dosis' => '3_dosis',
        'dosis_unica' => 'dosis_unica',
        'unica' => 'dosis_unica',
        'única' => 'dosis_unica',
        'refuerzo' => 'dosis_unica',
        'aplicada' => 'aplicada',
        'aplicado' => 'aplicada',
        'si' => 'aplicada',
        'sí' => 'aplicada',
        's' => 'aplicada',
        '1' => 'aplicada',
        'true' => 'aplicada',
    ];

    $normalizado = mb_strtolower($valor, 'UTF-8');
    $normalizado = str_replace([' ', '-'], '_', $normalizado);

    if (isset($mapa[$normalizado])) {
        return $mapa[$normalizado];
    }

    return '';
}
?>

<style>
    * {
        box-sizing: border-box;
        font-family: "Roboto", sans-serif;
    }

    .eval_vac_page {
        display: grid;
        grid-template-columns: 72px minmax(0, 1fr);
        width: 100%;
            min-height: 100dvh;
        overflow: clip;
        margin-left: -12px;
        margin-right: -12px;
        margin-top: -1.5rem;
        
    }

    .eval_vac_sidebar {
        background: var(--ds-dark);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        padding: 18px 0;
        box-shadow: 8px 0 28px rgba(8, 20, 79, .12);
    }

    .eval_vac_sidebar_item {
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
        
    }
  
.eval_vac_sidebar_item   {
 filter: brightness(0) invert(1); 
 

}

.eval_vac_sidebar_item_active {
 filter: brightness(1) invert(0); 
 
background: #fff;
}
 
.eval_vac_sidebar_item:hover    {
filter: none;
opacity: 1;
background: #fff;
}

    .eval_vac_sidebar_icon {
        width: 26px;
        height: 26px;
    }

    .eval_vac_main {
        min-width: 0;
        width: 100%;
        max-width: 1720px;
        margin: 0 auto;
        padding: 24px 28px 96px;
    }

    .eval_vac_header {
        display: flex;
        justify-content: space-between;
        gap: 24px;
        align-items: center;
        background: var(--ds-bg-light);
        border: 1px solid var(--ds-border);
        border-radius: 28px;
        padding: 22px;
        margin-bottom: 18px;
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.07);
        backdrop-filter: blur(10px);
    }

    .eval_vac_header_info {
        display: flex;
        gap: 14px;
        align-items: center;
        min-width: 0;
    }

    .eval_vac_header_icon {
        width: 58px;
        height: 58px;
        border-radius: 20px;
        background: var(--ds-bg);
        color: var(--ds-primary);
        display: grid;
        place-items: center;
        flex: 0 0 auto;
    }
 

    .eval_vac_title {
        margin: 0;
        color: var(--ds-dark);
        font-size: 1.55rem;
        font-weight: 600;
    }

    .eval_vac_subtitle {
        margin: 4px 0 0;
        color: var(--ds-muted);
        font-size: 0.92rem;
    }

    .eval_vac_badge {
        display: inline-flex;
        margin-top: 8px;
        padding: 4px 11px;
        border-radius: 999px;
        font-size: 0.74rem;
        font-weight: 600;
    }

    .eval_vac_badge_new {
        background: #dbeafe;
        color: var(--eval_vac_info);
    }

    .eval_vac_badge_edit {
        background: #fef3c7;
        color: var(--ds-orange);
    }

    .eval_vac_header_meta {
        display: grid;
        grid-template-columns: 220px 260px;
        gap: 16px;
        align-items: end;
    }

    .eval_vac_date_box,
    .eval_vac_progress_box {
        background: #f8fbff;
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 14px;
    }

    .eval_vac_label {
        display: block;
        color: #334155;
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 6px;
    }

    .eval_vac_input,
    .eval_vac_search_input,
    .eval_vac_textarea,
    .eval_vac_select {
        width: 100%;
        border: 1.5px solid var(--ds-border);
        border-radius: 12px;
        background: #fff;
        color: var(--eval_vac_text);
        font-size: 0.88rem;
        padding: 10px 12px;
        outline: none;
        transition: 0.15s ease;
    }

    .eval_vac_input:focus,
    .eval_vac_search_input:focus,
    .eval_vac_textarea:focus,
    .eval_vac_select:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, 0.08);
    }

    .eval_vac_textarea {
        resize: vertical;
        min-height: 120px;
    }

    .eval_vac_progress_text {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        color: var(--ds-muted);
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 9px;
    }

    .eval_vac_progress_text strong {
        color: var(--ds-primary);
    }

    .eval_vac_progress_bar {
        width: 100%;
        height: 10px;
        border-radius: 999px;
        overflow: hidden;
        background: #e5ecf7;
    }

    .eval_vac_progress_fill {
        height: 100%;
        width: 0;
        border-radius: inherit;
        background: var(--ds-primary);
        transition: width 0.2s ease;
    }

    .eval_vac_steps {
        display: flex;
        gap: 10px;
        overflow-x: auto;
        padding-bottom: 12px;
        margin-bottom: 10px;
    }

    .eval_vac_step {
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-muted);
        border-radius: 999px;
        padding: 8px 14px 8px 8px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
        transition: 0.18s ease;
    }

    .eval_vac_step span {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        background: #eef2f7;
        display: grid;
        place-items: center;
        color: var(--ds-muted);
    }

    .eval_vac_step:hover {
        border-color: rgba(16, 26, 97, 0.35);
        transform: translateY(-1px);
    }

    .eval_vac_step_active {
        background: var(--ds-primary);
        color: #fff;
        border-color: var(--ds-primary);
    }

    .eval_vac_step_active span {
        background: #fff;
        color: var(--ds-primary);
    }

    .eval_vac_step_panel {
        display: none;
    }

    .eval_vac_step_panel_active {
        display: block;
    }

    .eval_vac_layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 22px;
        align-items: start;
    }

    .eval_vac_card,
    .eval_vac_summary_card {
        background: var(--ds-bg-light);
        border: 1px solid var(--ds-border);
        border-radius: 24px;
        box-shadow: 0 18px 44px rgba(15, 23, 42, 0.07);
    }

    .eval_vac_card {
        padding: 24px;
    }

    .eval_vac_card_header {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        align-items: flex-start;
        padding-bottom: 16px;
        border-bottom: 1px solid #edf2f7;
        margin-bottom: 18px;
    }

    .eval_vac_card_title,
    .eval_vac_summary_title {
        margin: 0;
        color: var(--ds-dark);
        font-size: 1.05rem;
        font-weight: 600;
    }

    .eval_vac_card_description,
    .eval_vac_summary_text {
        margin: 5px 0 0;
        color: var(--ds-muted);
        font-size: 0.84rem;
    }

    .eval_vac_tools {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: center;
        margin-bottom: 18px;
    }

    .eval_vac_search_box {
        flex: 1;
        max-width: 360px;
    }

    .eval_vac_sr_only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    .eval_vac_filters {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .eval_vac_filter {
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-muted);
        border-radius: 999px;
        padding: 9px 13px;
        font-size: 0.78rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.15s ease;
    }

    .eval_vac_filter_active {
        background: var(--ds-primary);
        color: #fff;
        border-color: var(--ds-primary);
    }

    .eval_vac_table_header {
        display: grid;
        grid-template-columns: 1.2fr 1.2fr 1.3fr 0.8fr;
        gap: 14px;
        align-items: center;
        background: #f1f5fb;
        color: #334155;
        border-radius: 14px;
        padding: 13px 16px;
        font-size: 0.78rem;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .eval_vac_list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .eval_vac_row {
        display: grid;
        grid-template-columns: 1.2fr 1.2fr 1.3fr 0.8fr;
        gap: 14px;
        align-items: center;
        background: #fbfdff;
        border: 1px solid #edf2f7;
        border-radius: 18px;
        padding: 14px 16px;
        transition: 0.18s ease;
    }

    .eval_vac_row:hover {
        background: #fff;
        border-color: rgba(16, 26, 97, 0.2);
        box-shadow: 0 12px 28px rgba(16, 26, 97, 0.07);
    }

    .eval_vac_name strong {
        display: block;
        color: var(--ds-text);
        font-size: 0.92rem;
    }

    .eval_vac_name small {
        display: block;
        margin-top: 4px;
        color: var(--ds-muted);
        font-size: 0.74rem;
    }

    .eval_vac_extra_fields {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .eval_vac_extra_fields_hidden {
        opacity: 0.35;
        pointer-events: none;
    }

    .eval_vac_alert {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        min-height: 30px;
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 0.72rem;
        font-weight: 600;
        text-align: center;
    }

    .eval_vac_alert_success {
        background: #dcfce7;
        color: #166534;
    }

    .eval_vac_alert_warning {
        background: #fef3c7;
        color: #92400e;
    }

    .eval_vac_alert_danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .eval_vac_alert_neutral {
        background: #eef2f7;
        color: #475569;
    }

    .eval_vac_summary {
        display: flex;
        flex-direction: column;
        gap: 16px;
        position: sticky;
        top: 18px;
    }

    .eval_vac_summary_card {
        padding: 18px;
    }

    .eval_vac_summary_row {
        display: flex;
        justify-content: space-between;
        gap: 12px;
        align-items: center;
        padding: 13px 0;
        border-bottom: 1px solid #edf2f7;
        color: var(--ds-muted);
        font-size: 0.86rem;
    }

    .eval_vac_summary_row:last-child {
        border-bottom: 0;
    }

    .eval_vac_summary_row strong {
        color: var(--ds-dark);
        font-size: 1.15rem;
        font-weight: 600;
    }

    .eval_vac_field_group {
        margin-top: 14px;
    }

    .eval_vac_grid_2 {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .eval_vac_grid_3 {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
    }

    .eval_vac_panel_note {
        margin: 16px 0 0;
        padding: 14px;
        border-radius: 16px;
        background: var(--ds-primary-light);
        color: var(--ds-primary);
        font-size: 0.86rem;
        font-weight: 600;
    }

    .eval_vac_actions {
        position: sticky;
        bottom: 0;
        z-index: 20;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 10px;
        margin-top: 22px;
        padding: 12px;
        background: rgba(248, 251, 255, 0.94);
        backdrop-filter: blur(10px);
        border: 1px solid var(--ds-border);
        border-radius: 20px 20px 0 0;
        box-shadow: 0 -16px 36px rgba(15, 23, 42, 0.08);
    }

    .eval_vac_actions_help {
        margin: 0 auto 0 0;
        color: var(--ds-muted);
        font-size: 0.8rem;
        font-weight: 600;
    }

    .eval_vac_btn {
        min-height: 44px;
        border-radius: 12px;
        padding: 0 18px;
        border: 1px solid var(--ds-border);
        font-size: 0.86rem;
        font-weight: 600;
        cursor: pointer;
        transition: 0.18s ease;
    }

    .eval_vac_btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.1);
    }

    .eval_vac_btn_primary {
        background: var(--ds-primary);
        color: #fff;
        border-color: var(--ds-primary);
    }

    .eval_vac_btn_secondary {
        background: var(--ds-primary-light);
        color: var(--ds-primary);
        border-color: transparent;
    }

    .eval_vac_btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
        box-shadow: none;
    }

    .eval_vac_toast {
        position: fixed;
        right: 24px;
        bottom: 94px;
        z-index: 50;
        background: #111827;
        color: #fff;
        border-radius: 12px;
        padding: 12px 14px;
        font-size: 0.86rem;
        font-weight: 600;
        transform: translateY(12px);
        opacity: 0;
        pointer-events: none;
        transition: 0.2s ease;
        box-shadow: 0 18px 36px rgba(15, 23, 42, 0.22);
    }

    .eval_vac_toast_show {
        transform: translateY(0);
        opacity: 1;
    }

    @media (max-width: 1180px) {
        .eval_vac_page {
            grid-template-columns: 1fr;
        }

        .eval_vac_sidebar {
            flex-direction: row;
            overflow-x: auto;
            justify-content: flex-start;
            align-items: center;
            gap: 10px;
            padding: 10px 14px;
            box-shadow: 0 8px 24px rgba(8, 20, 79, 0.14);
        }

        .eval_vac_sidebar_item {
            flex: 0 0 48px;
        }

        .eval_vac_main {
            padding: 18px 14px 90px;
        }

        .eval_vac_layout {
            grid-template-columns: 1fr;
        }

        .eval_vac_summary {
            position: static;
        }
    }

    @media (max-width: 940px) {
        .eval_vac_header {
            align-items: flex-start;
            flex-direction: column;
        }

        .eval_vac_header_meta {
            width: 100%;
            grid-template-columns: 1fr;
        }

        .eval_vac_tools {
            align-items: stretch;
            flex-direction: column;
        }

        .eval_vac_search_box {
            max-width: 100%;
        }

        .eval_vac_table_header {
            display: none;
        }

        .eval_vac_row {
            grid-template-columns: 1fr;
        }

        .eval_vac_extra_fields,
        .eval_vac_grid_2,
        .eval_vac_grid_3 {
            grid-template-columns: 1fr;
        }

        .eval_vac_actions {
            flex-wrap: wrap;
        }

        .eval_vac_actions_help {
            flex: 1 0 100%;
            text-align: center;
            margin: 0 0 4px;
        }

        .eval_vac_btn {
            flex: 1 1 auto;
        }
    }

    @media (max-width: 600px) {

        .eval_vac_header,
        .eval_vac_card,
        .eval_vac_summary_card {
            border-radius: 20px;
        }

        .eval_vac_header_info {
            align-items: flex-start;
        }

        .eval_vac_header_icon {
            width: 48px;
            height: 48px;
        }

        .eval_vac_title {
            font-size: 1.2rem;
        }
    }
</style>

<div class="eval_vac_page" data-page="evaluacion">

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- SIDEBAR — Pesquisas de la jornada                      -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <aside class="eval_vac_sidebar" aria-label="Menú de pesquisas">
        <?php foreach ($pesquisasActividad as $pid): ?>
            <?php
            $info = $infoPesquisas[$pid] ?? null;
            if (!$info) continue;

            $esActiva    = ((int) $pid === (int) $tipoPesquisaId);
            $yaEvaluada  = in_array($pid, $pesquisasEvaluadas);

            $clasesSidebar = 'eval_vac_sidebar_item';
            if ($esActiva)   $clasesSidebar .= ' eval_vac_sidebar_item_active';
            if ($yaEvaluada) $clasesSidebar .= ' eval_vac_sidebar_item_done';

            $urlPesquisa = base_url("evaluaciones/formulario/{$beneficiario['id_beneficiario']}/{$pid}")
                . ($jornadaId ? "?jornada_id={$jornadaId}" : '')
                . ($centroId  ? (($jornadaId ? '&' : '?') . "centro_id={$centroId}") : '');

            // Imagen SVG para el ícono
            $imgFile = $esActiva
                ? ($info['img'] ?? 'vacunacion2.svg')
                : ($info['gris'] ?? 'vacunacion-color.svg');
            ?>
            <a class="<?= $clasesSidebar ?>"
                href="<?= esc($urlPesquisa) ?>"
                aria-label="<?= esc($info['nombre']) ?>"
                title="<?= esc($info['nombre']) ?>">
                <img class="eval_vac_sidebar_icon"
                    src="<?= base_url("img/{$imgFile}") ?>"
                    alt="<?= esc($info['nombre']) ?>">
            </a>
        <?php endforeach; ?>
    </aside>

    <!-- ═══════════════════════════════════════════════════════ -->
    <!-- MAIN CONTENT                                           -->
    <!-- ═══════════════════════════════════════════════════════ -->
    <main class="eval_vac_main">

        <!-- ── HEADER ── -->
        <div class="eval_vac_header">
            <div class="eval_vac_header_info">
                <div class="eval_vac_header_icon">
                    <img src="<?= base_url('img/vacunacion2.svg') ?>" alt="Vacunación" style="width:32px;height:32px;">
                </div>
                <div>
                    <h1 class="eval_vac_title">Vacunación</h1>
                    <p class="eval_vac_subtitle">
                        Beneficiario: <?= esc($beneficiario['nombres'] . ' ' . $beneficiario['apellidos']) ?>
                        <?php if ($jornadaId): ?>
                            · Jornada #<?= esc($jornadaId) ?>
                        <?php endif; ?>
                    </p>
                    <?php if ($esEdicion): ?>
                        <span class="eval_vac_badge eval_vac_badge_edit">Editando evaluación</span>
                    <?php else: ?>
                        <span class="eval_vac_badge eval_vac_badge_new">Nueva evaluación</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="eval_vac_header_meta">
                <div class="eval_vac_date_box">
                    <label class="eval_vac_label" for="eval_vac_fecha">Fecha de evaluación</label>
                    <input id="eval_vac_fecha"
                        name="fecha_evaluacion"
                        class="eval_vac_input"
                        type="date"
                        value="<?= esc($fechaEval) ?>">
                </div>
                <div class="eval_vac_progress_box">
                    <div class="eval_vac_progress_text">
                        <span>Progreso</span>
                        <strong id="eval_vac_progress_text">0 / <?= count($vacunasDefinidas) ?> vacunas</strong>
                    </div>
                    <div class="eval_vac_progress_bar">
                        <div id="eval_vac_progress_fill" class="eval_vac_progress_fill"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── STEPS NAV ── -->
        <nav class="eval_vac_steps" aria-label="Secciones de evaluación">
            <button type="button" class="eval_vac_step eval_vac_step_active" data-step="1">
                <span>1</span> Estado de vacunas
            </button>
            <button type="button" class="eval_vac_step" data-step="2">
                <span>2</span> Datos generales
            </button>
            <button type="button" class="eval_vac_step" data-step="3">
                <span>3</span> Control y observaciones
            </button>
        </nav>

        <!-- ═══════════════════════════════════════════════════════ -->
        <!-- FORM                                                   -->
        <!-- ═══════════════════════════════════════════════════════ -->
        <form class="eval_vac_form" id="eval_vac_form" autocomplete="off">
            <?= csrf_field() ?>
            <input type="hidden" name="beneficiario_id" value="<?= esc($beneficiario['id_beneficiario']) ?>">
            <input type="hidden" name="tipo_pesquisa_id" value="<?= esc($tipoPesquisaId) ?>">
            <input type="hidden" name="jornada_id" value="<?= esc($jornadaId) ?>">
            <input type="hidden" name="centro_id" value="<?= esc($centroId) ?>">
            <?php if ($esEdicion): ?>
                <input type="hidden" name="evaluacion_id" value="<?= esc($evaluacionId) ?>">
            <?php endif; ?>

            <!-- ══════════════════════════════════════════════ -->
            <!-- STEP 1: Estado de vacunas                     -->
            <!-- ══════════════════════════════════════════════ -->
            <section class="eval_vac_step_panel eval_vac_step_panel_active" data-step-panel="1">
                <div class="eval_vac_layout">

                    <section class="eval_vac_card">
                        <div class="eval_vac_card_header">
                            <div>
                                <h2 class="eval_vac_card_title">Estado de vacunas</h2>
                                <p class="eval_vac_card_description">
                                    Selecciona la dosis aplicada para cada vacuna. La fecha y el lote se habilitan cuando se selecciona una dosis.
                                </p>
                            </div>
                        </div>

                        <!-- Buscador y filtros -->
                        <div class="eval_vac_tools">
                            <div class="eval_vac_search_box">
                                <label class="eval_vac_sr_only" for="eval_vac_buscar">Buscar vacuna</label>
                                <input id="eval_vac_buscar"
                                    class="eval_vac_search_input"
                                    type="search"
                                    placeholder="Buscar vacuna...">
                            </div>
                            <div class="eval_vac_filters" aria-label="Filtros de vacunas">
                                <button type="button" class="eval_vac_filter eval_vac_filter_active" data-filter="todas">Todas</button>
                                <button type="button" class="eval_vac_filter" data-filter="1_dosis">1° Dosis</button>
                                <button type="button" class="eval_vac_filter" data-filter="2_dosis">2° Dosis</button>
                                <button type="button" class="eval_vac_filter" data-filter="3_dosis">3° Dosis</button>
                                <button type="button" class="eval_vac_filter" data-filter="dosis_unica">Dosis Única</button>
                                <button type="button" class="eval_vac_filter" data-filter="sin_seleccionar">Sin seleccionar</button>
                            </div>
                        </div>

                        <!-- Encabezado de tabla -->
                        <div class="eval_vac_table_header" aria-hidden="true">
                            <span>Vacuna</span>
                            <span>Dosis aplicada</span>
                            <span>Fecha vencimiento / Nro. de lote</span>
                            <span>Alerta</span>
                        </div>

                        <!-- Lista de vacunas -->
                        <div class="eval_vac_list">
                            <?php foreach ($vacunasDefinidas as $codigoVac => $infoVac): ?>
                                <?php
                                $nombreVac = $infoVac[0];
                                $descVac   = $infoVac[1];

                                // Valor del select de dosis (almacenado como campos[CODIGO_dosis])
                                $valorDosis = evalVacValor($codigoVac . '_dosis', $valoresExistentes);
                                $valorFecha = evalVacValor($codigoVac . '_fecha', $valoresExistentes);
                                $valorLote  = evalVacValor($codigoVac . '_lote_vacuna', $valoresExistentes);

                                // También checar el valor del select original de la BD (Aplicada/No aplicada)
                                // para backward compatibility
                                $valorOriginal = evalVacValor($codigoVac, $valoresExistentes);
                                $valorDosis = evalVacNormalizarDosis($valorDosis);
                                if ($valorDosis === '' && $valorOriginal !== '') {
                                    $valorDosis = evalVacNormalizarDosis($valorOriginal);
                                    $dosisGeneral = evalVacNormalizarDosis(evalVacValor('dosis', $valoresExistentes));
                                    if ($valorDosis === 'aplicada' && $dosisGeneral !== '' && $dosisGeneral !== 'aplicada') {
                                        $valorDosis = $dosisGeneral;
                                    }
                                }
                                $tieneSeleccion = !empty($valorDosis);
                                $extraClase     = $tieneSeleccion ? '' : 'eval_vac_extra_fields_hidden';
                                $alertaClase    = $tieneSeleccion ? 'eval_vac_alert_success' : 'eval_vac_alert_warning';
                                $alertaTexto    = $tieneSeleccion ? '' : 'Sin seleccionar';

                                if ($valorDosis === '1_dosis')      $alertaTexto = '1° Dosis';
                                elseif ($valorDosis === '2_dosis')   $alertaTexto = '2° Dosis';
                                elseif ($valorDosis === '3_dosis')   $alertaTexto = '3° Dosis';
                                elseif ($valorDosis === 'dosis_unica') $alertaTexto = 'Dosis Única';
                                elseif ($valorDosis === 'aplicada') $alertaTexto = 'Aplicada';
                                ?>
                                <article class="eval_vac_row" data-vaccine-row data-codigo="<?= esc($codigoVac) ?>">
                                    <div class="eval_vac_name">
                                        <strong><?= esc($nombreVac) ?></strong>
                                        <small><?= esc($descVac) ?></small>
                                    </div>

                                    <div>
                                        <select class="eval_vac_select eval_vac_dosis_select"
                                            name="campos[<?= esc($codigoVac) ?>_dosis]">
                                            <option value="">Seleccione dosis</option>
                                            <option value="1_dosis" <?= $valorDosis === '1_dosis'      ? 'selected' : '' ?>>1° Dosis</option>
                                            <option value="2_dosis" <?= $valorDosis === '2_dosis'      ? 'selected' : '' ?>>2° Dosis</option>
                                            <option value="3_dosis" <?= $valorDosis === '3_dosis'      ? 'selected' : '' ?>>3° Dosis</option>
                                            <option value="dosis_unica" <?= $valorDosis === 'dosis_unica'  ? 'selected' : '' ?>>Dosis Única</option>
                                            <option value="aplicada" <?= $valorDosis === 'aplicada' ? 'selected' : '' ?>>Aplicada</option>
                                        </select>
                                    </div>

                                    <div class="eval_vac_extra_fields <?= $extraClase ?>">
                                        <input class="eval_vac_input"
                                            type="date"
                                            name="campos[<?= esc($codigoVac) ?>_fecha]"
                                            value="<?= esc($valorFecha) ?>"
                                            placeholder="Fecha vencimiento">
                                        <input class="eval_vac_input"
                                            type="text"
                                            name="campos[<?= esc($codigoVac) ?>_lote_vacuna]"
                                            value="<?= esc($valorLote) ?>"
                                            placeholder="Nro. de lote">
                                    </div>

                                    <div class="eval_vac_alert <?= $alertaClase ?>">
                                        <?= esc($alertaTexto) ?>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <!-- ── SIDEBAR: Resumen + Observaciones rápidas ── -->
                    <aside class="eval_vac_summary">
                        <section class="eval_vac_summary_card">
                            <h2 class="eval_vac_summary_title">Resumen</h2>
                            <p class="eval_vac_summary_text">Vista rápida antes de guardar.</p>

                            <div class="eval_vac_summary_row">
                                <span>Registradas</span>
                                <strong id="eval_vac_count_registradas">0</strong>
                            </div>
                            <div class="eval_vac_summary_row">
                                <span>1° Dosis</span>
                                <strong id="eval_vac_count_primera">0</strong>
                            </div>
                            <div class="eval_vac_summary_row">
                                <span>2° Dosis</span>
                                <strong id="eval_vac_count_segunda">0</strong>
                            </div>
                            <div class="eval_vac_summary_row">
                                <span>3° Dosis</span>
                                <strong id="eval_vac_count_tercera">0</strong>
                            </div>
                            <div class="eval_vac_summary_row">
                                <span>Dosis Única</span>
                                <strong id="eval_vac_count_unica">0</strong>
                            </div>
                            <div class="eval_vac_summary_row">
                                <span>Sin seleccionar</span>
                                <strong id="eval_vac_count_sin_seleccionar">0</strong>
                            </div>
                        </section>

                        <section class="eval_vac_summary_card">
                            <h3 class="eval_vac_summary_title">Observaciones rápidas</h3>
                            <textarea name="campos[observaciones_rapidas]"
                                class="eval_vac_textarea"
                                rows="6"
                                placeholder="Agregar observaciones de vacunación..."><?= esc(evalVacValor('observaciones_rapidas', $valoresExistentes)) ?></textarea>
                        </section>
                    </aside>
                </div>
            </section>

            <!-- ══════════════════════════════════════════════ -->
            <!-- STEP 2: Aplicación actual                     -->
            <!-- ══════════════════════════════════════════════ -->
            <section class="eval_vac_step_panel" data-step-panel="2">
                <section class="eval_vac_card">
                    <div class="eval_vac_card_header">
                        <div>
                            <h2 class="eval_vac_card_title">Datos generales</h2>
                            <p class="eval_vac_card_description">
                                Datos del responsable de la aplicación, lugar y vacuna aplicada durante esta jornada.
                            </p>
                        </div>
                    </div>

                    <div class="eval_vac_grid_3">
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_responsable">Responsable de aplicación</label>
                            <input id="eval_vac_responsable"
                                class="eval_vac_input"
                                type="text"
                                name="campos[responsable_aplicacion]"
                                value="<?= esc(evalVacValor('responsable_aplicacion', $valoresExistentes)) ?>"
                                placeholder="Nombre del responsable">
                        </div>
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_cargo_responsable">Cargo</label>
                            <input id="eval_vac_cargo_responsable"
                                class="eval_vac_input"
                                type="text"
                                name="campos[cargo_responsable]"
                                value="<?= esc(evalVacValor('cargo_responsable', $valoresExistentes)) ?>"
                                placeholder="Ej: Enfermera, médico, vacunador">
                        </div>
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_lugar_aplicacion">Lugar de aplicación</label>
                            <input id="eval_vac_lugar_aplicacion"
                                class="eval_vac_input"
                                type="text"
                                name="campos[lugar_aplicacion]"
                                value="<?= esc(evalVacValor('lugar_aplicacion', $valoresExistentes)) ?>"
                                placeholder="Ej: Ambulatorio Central">
                        </div>
                    </div>

                    <div class="eval_vac_field_group" style="margin-top:14px;">
                        <label class="eval_vac_label" for="eval_vac_sitio_ap">Zona anatómica de aplicación</label>
                        <select id="eval_vac_sitio_ap"
                            class="eval_vac_select"
                            name="campos[sitio_aplicacion]">
                            <?php
                            $opcionesSitio = [
                                ''                  => 'Seleccione',
                                'deltoides_izq'     => 'Deltoides izquierdo (brazo)',
                                'deltoides_der'     => 'Deltoides derecho (brazo)',
                                'vasto_externo_izq' => 'Vasto externo izquierdo (muslo)',
                                'vasto_externo_der' => 'Vasto externo derecho (muslo)',
                                'gluteo'            => 'Glúteo',
                                'oral'              => 'Vía oral',
                                'intranasal'        => 'Vía intranasal',
                                'intradermica'      => 'Intradérmica (antebrazo)',
                            ];
                            $valorSitio = evalVacValor('sitio_aplicacion', $valoresExistentes);
                            foreach ($opcionesSitio as $val => $txt):
                            ?>
                                <option value="<?= esc($val) ?>" <?= $valorSitio === $val ? 'selected' : '' ?>><?= esc($txt) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <p class="eval_vac_panel_note">
                        La dosis y fecha de vencimiento se registran individualmente en el paso 1 (Estado de vacunas).
                    </p>
                </section>
            </section>

            <!-- ══════════════════════════════════════════════ -->
            <!-- STEP 3: Control y observaciones               -->
            <!-- ══════════════════════════════════════════════ -->
            <section class="eval_vac_step_panel" data-step-panel="3">
                <section class="eval_vac_card">
                    <div class="eval_vac_card_header">
                        <div>
                            <h2 class="eval_vac_card_title">Control y observaciones</h2>
                            <p class="eval_vac_card_description">
                                Registra recomendaciones, control posterior, reacción observada o comentarios finales.
                            </p>
                        </div>
                    </div>

                    <div class="eval_vac_grid_2">
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_proximo_control">Próximo control</label>
                            <input id="eval_vac_proximo_control"
                                class="eval_vac_input"
                                type="date"
                                name="campos[proximo_control]"
                                value="<?= esc(evalVacValor('proximo_control', $valoresExistentes)) ?>">
                        </div>
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_reaccion">Reacción observada</label>
                            <select id="eval_vac_reaccion"
                                class="eval_vac_select"
                                name="campos[reaccion_observada]">
                                <?php
                                $opcionesReaccion = [
                                    ''          => 'Seleccione',
                                    'ninguna'   => 'Ninguna',
                                    'leve'      => 'Leve',
                                    'moderada'  => 'Moderada',
                                    'severa'    => 'Severa',
                                ];
                                $valorReaccion = evalVacValor('reaccion_observada', $valoresExistentes);
                                foreach ($opcionesReaccion as $val => $txt):
                                ?>
                                    <option value="<?= esc($val) ?>" <?= $valorReaccion === $val ? 'selected' : '' ?>><?= esc($txt) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="eval_vac_field_group">
                        <label class="eval_vac_label" for="eval_vac_descripcion_reaccion">Descripción de reacción</label>
                        <textarea id="eval_vac_descripcion_reaccion"
                            class="eval_vac_textarea"
                            name="campos[descripcion_reaccion]"
                            placeholder="Describe cualquier reacción observada..."><?= esc(evalVacValor('descripcion_reaccion', $valoresExistentes)) ?></textarea>
                    </div>

                    <div class="eval_vac_field_group">
                        <label class="eval_vac_label" for="eval_vac_recomendaciones">Recomendaciones</label>
                        <textarea id="eval_vac_recomendaciones"
                            class="eval_vac_textarea"
                            name="campos[recomendaciones]"
                            placeholder="Agregar recomendaciones..."><?= esc(evalVacValor('recomendaciones', $valoresExistentes)) ?></textarea>
                    </div>

                    <div class="eval_vac_grid_2">
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_no_se_aplico">¿No se aplicó ninguna vacuna?</label>
                            <select id="eval_vac_no_se_aplico"
                                class="eval_vac_select"
                                name="campos[no_se_aplico]">
                                <?php
                                $valorNoAplico = evalVacValor('no_se_aplico', $valoresExistentes);
                                ?>
                                <option value="">Seleccione</option>
                                <option value="s" <?= $valorNoAplico === 's' ? 'selected' : '' ?>>Sí</option>
                                <option value="n" <?= $valorNoAplico === 'n' ? 'selected' : '' ?>>No</option>
                            </select>
                        </div>
                        <div class="eval_vac_field_group">
                            <label class="eval_vac_label" for="eval_vac_remision">Remisión</label>
                            <input id="eval_vac_remision"
                                class="eval_vac_input"
                                type="text"
                                name="campos[remision]"
                                value="<?= esc(evalVacValor('remision', $valoresExistentes)) ?>"
                                placeholder="Referencia o remisión">
                        </div>
                    </div>

                    <div class="eval_vac_field_group">
                        <label class="eval_vac_label" for="eval_vac_observaciones_finales">Observaciones finales</label>
                        <textarea id="eval_vac_observaciones_finales"
                            class="eval_vac_textarea"
                            name="campos[observaciones_finales]"
                            placeholder="Agregar observaciones finales..."><?= esc(evalVacValor('observaciones_finales', $valoresExistentes)) ?></textarea>
                    </div>

                    <!-- Campo observaciones de la tabla pesquisa_evaluaciones -->
                    <div class="eval_vac_field_group">
                        <label class="eval_vac_label" for="eval_vac_observaciones_eval">Observaciones generales de la evaluación</label>
                        <textarea id="eval_vac_observaciones_eval"
                            class="eval_vac_textarea"
                            name="observaciones"
                            placeholder="Observaciones generales..."><?= esc($observaciones) ?></textarea>
                    </div>

                </section>
            </section>

            <!-- ── ACTIONS BAR ── -->
            <div class="eval_vac_actions">
                <p class="eval_vac_actions_help" id="eval_vac_actions_help">
                    Estás en: Estado de vacunas
                </p>

                <?php
                $urlCancelar = '';
                if (!empty($jornadaId)) {
                    $urlCancelar = base_url("jornadas/{$jornadaId}/beneficiarios");
                } elseif (!empty($centroId)) {
                    $urlCancelar = base_url("centros/{$centroId}/beneficiarios");
                } else {
                    $urlCancelar = base_url('dashboard');
                }
                ?>
                <a href="<?= esc($urlCancelar) ?>" class="eval_vac_btn eval_vac_btn_secondary" style="text-decoration:none;display:inline-flex;align-items:center;">
                    Cancelar
                </a>

                <button type="button" id="eval_vac_btn_prev" class="eval_vac_btn eval_vac_btn_secondary" disabled>
                    Anterior
                </button>

                <button type="button" id="eval_vac_btn_next" class="eval_vac_btn eval_vac_btn_secondary">
                    Siguiente
                </button>

                <button type="submit" class="eval_vac_btn eval_vac_btn_primary" id="eval_vac_btn_guardar">
                    <?= $esEdicion ? 'Actualizar evaluación' : 'Guardar evaluación' ?>
                </button>
            </div>
        </form>

    </main>
</div>

<!-- Toast -->
<div id="eval_vac_toast" class="eval_vac_toast"></div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- ═══════════════════════════════════════════════════════════════ -->
<!-- JAVASCRIPT                                                    -->
<!-- ═══════════════════════════════════════════════════════════════ -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // ── Referencias DOM ──
        var evalVacRows = Array.from(document.querySelectorAll('[data-vaccine-row]'));
        var evalVacSearch = document.getElementById('eval_vac_buscar');
        var evalVacFilters = Array.from(document.querySelectorAll('.eval_vac_filter'));
        var evalVacForm = document.getElementById('eval_vac_form');
        var evalVacToast = document.getElementById('eval_vac_toast');

        var evalVacCountRegistradas = document.getElementById('eval_vac_count_registradas');
        var evalVacCountPrimera = document.getElementById('eval_vac_count_primera');
        var evalVacCountSegunda = document.getElementById('eval_vac_count_segunda');
        var evalVacCountTercera = document.getElementById('eval_vac_count_tercera');
        var evalVacCountUnica = document.getElementById('eval_vac_count_unica');
        var evalVacCountSinSeleccionar = document.getElementById('eval_vac_count_sin_seleccionar');
        var evalVacProgressText = document.getElementById('eval_vac_progress_text');
        var evalVacProgressFill = document.getElementById('eval_vac_progress_fill');

        var evalVacStepButtons = Array.from(document.querySelectorAll('.eval_vac_step'));
        var evalVacStepPanels = Array.from(document.querySelectorAll('.eval_vac_step_panel'));
        var evalVacBtnPrev = document.getElementById('eval_vac_btn_prev');
        var evalVacBtnNext = document.getElementById('eval_vac_btn_next');
        var evalVacActionsHelp = document.getElementById('eval_vac_actions_help');
        var evalVacBtnGuardar = document.getElementById('eval_vac_btn_guardar');

        var evalVacCurrentFilter = 'todas';
        var evalVacCurrentStep = 1;
        var totalSteps = 3;

        var evalVacStepTitles = {
            1: 'Estado de vacunas',
            2: 'Datos generales',
            3: 'Control y observaciones'
        };

        // ── Helpers ──
        function evalVacGetSelectedDosis(row) {
            var select = row.querySelector('.eval_vac_dosis_select');
            return select ? select.value : '';
        }

        function evalVacGetDosisLabel(value) {
            var labels = {
                '1_dosis': '1° Dosis',
                '2_dosis': '2° Dosis',
                '3_dosis': '3° Dosis',
                'dosis_unica': 'Dosis Única',
                'aplicada': 'Aplicada'
            };
            return labels[value] || '';
        }

        function evalVacSetAlert(row, dosis) {
            var alert = row.querySelector('.eval_vac_alert');
            var extraFields = row.querySelector('.eval_vac_extra_fields');
            if (!alert || !extraFields) return;

            alert.classList.remove(
                'eval_vac_alert_success', 'eval_vac_alert_warning',
                'eval_vac_alert_danger', 'eval_vac_alert_neutral'
            );

            if (dosis) {
                alert.textContent = evalVacGetDosisLabel(dosis);
                alert.classList.add('eval_vac_alert_success');
                extraFields.classList.remove('eval_vac_extra_fields_hidden');
            } else {
                alert.textContent = 'Sin seleccionar';
                alert.classList.add('eval_vac_alert_warning');
                extraFields.classList.add('eval_vac_extra_fields_hidden');
            }
        }

        // ── Resumen lateral ──
        function evalVacUpdateSummary() {
            var primeraDosis = 0,
                segundaDosis = 0,
                terceraDosis = 0,
                dosisUnica = 0,
                sinSeleccionar = 0;

            evalVacRows.forEach(function(row) {
                var dosis = evalVacGetSelectedDosis(row);
                evalVacSetAlert(row, dosis);

                if (dosis === '1_dosis') primeraDosis++;
                if (dosis === '2_dosis') segundaDosis++;
                if (dosis === '3_dosis') terceraDosis++;
                if (dosis === 'dosis_unica') dosisUnica++;
                if (!dosis) sinSeleccionar++;
            });

            var total = evalVacRows.length;
            var registradas = total - sinSeleccionar;
            var percent = total > 0 ? Math.round((registradas / total) * 100) : 0;

            if (evalVacCountRegistradas) evalVacCountRegistradas.textContent = registradas;
            if (evalVacCountPrimera) evalVacCountPrimera.textContent = primeraDosis;
            if (evalVacCountSegunda) evalVacCountSegunda.textContent = segundaDosis;
            if (evalVacCountTercera) evalVacCountTercera.textContent = terceraDosis;
            if (evalVacCountUnica) evalVacCountUnica.textContent = dosisUnica;
            if (evalVacCountSinSeleccionar) evalVacCountSinSeleccionar.textContent = sinSeleccionar;

            if (evalVacProgressText) {
                evalVacProgressText.textContent = registradas + ' / ' + total + ' vacunas';
            }
            if (evalVacProgressFill) {
                evalVacProgressFill.style.width = percent + '%';
            }
        }

        // ── Filtros ──
        function evalVacApplyFilters() {
            var searchValue = evalVacSearch ? evalVacSearch.value.trim().toLowerCase() : '';

            evalVacRows.forEach(function(row) {
                var rowText = row.textContent.toLowerCase();
                var dosis = evalVacGetSelectedDosis(row);

                var matchesSearch = rowText.includes(searchValue);
                var matchesFilter =
                    evalVacCurrentFilter === 'todas' ||
                    dosis === evalVacCurrentFilter ||
                    (evalVacCurrentFilter === 'sin_seleccionar' && !dosis);

                row.style.display = (matchesSearch && matchesFilter) ? '' : 'none';
            });
        }

        // ── Steps navigation ──
        function evalVacShowStep(step) {
            evalVacCurrentStep = Number(step);

            evalVacStepButtons.forEach(function(button) {
                var isActive = Number(button.dataset.step) === evalVacCurrentStep;
                button.classList.toggle('eval_vac_step_active', isActive);
            });

            evalVacStepPanels.forEach(function(panel) {
                var isActive = Number(panel.dataset.stepPanel) === evalVacCurrentStep;
                panel.classList.toggle('eval_vac_step_panel_active', isActive);
            });

            if (evalVacBtnPrev) evalVacBtnPrev.disabled = (evalVacCurrentStep === 1);
            if (evalVacBtnNext) evalVacBtnNext.disabled = (evalVacCurrentStep === totalSteps);

            if (evalVacActionsHelp) {
                evalVacActionsHelp.textContent = 'Estás en: ' + evalVacStepTitles[evalVacCurrentStep];
            }

            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }

        // ── Event listeners ──
        evalVacRows.forEach(function(row) {
            var select = row.querySelector('.eval_vac_dosis_select');
            if (select) {
                select.addEventListener('change', function() {
                    evalVacUpdateSummary();
                    evalVacApplyFilters();
                });
            }
        });

        if (evalVacSearch) {
            evalVacSearch.addEventListener('input', evalVacApplyFilters);
        }

        evalVacFilters.forEach(function(button) {
            button.addEventListener('click', function() {
                evalVacFilters.forEach(function(btn) {
                    btn.classList.remove('eval_vac_filter_active');
                });
                button.classList.add('eval_vac_filter_active');
                evalVacCurrentFilter = button.dataset.filter || 'todas';
                evalVacApplyFilters();
            });
        });

        evalVacStepButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                evalVacShowStep(button.dataset.step);
            });
        });

        if (evalVacBtnPrev) {
            evalVacBtnPrev.addEventListener('click', function() {
                if (evalVacCurrentStep > 1) evalVacShowStep(evalVacCurrentStep - 1);
            });
        }

        if (evalVacBtnNext) {
            evalVacBtnNext.addEventListener('click', function() {
                if (evalVacCurrentStep < totalSteps) evalVacShowStep(evalVacCurrentStep + 1);
            });
        }

        // ── Toast helper ──
        function evalVacShowToast(msg, tipo) {
            if (!evalVacToast) return;
            evalVacToast.textContent = msg;
            evalVacToast.style.background = (tipo === 'error') ? '#dc2626' : '#111827';
            evalVacToast.classList.add('eval_vac_toast_show');
            setTimeout(function() {
                evalVacToast.classList.remove('eval_vac_toast_show');
            }, 3000);
        }

        // ── SUBMIT: Envío AJAX ──
        if (evalVacForm) {
            evalVacForm.addEventListener('submit', function(event) {
                event.preventDefault();

                // Inyectar la fecha de evaluación en el form
                var fechaInput = document.getElementById('eval_vac_fecha');
                var fechaHidden = evalVacForm.querySelector('input[name="fecha_evaluacion"]');
                if (!fechaHidden) {
                    fechaHidden = document.createElement('input');
                    fechaHidden.type = 'hidden';
                    fechaHidden.name = 'fecha_evaluacion';
                    evalVacForm.appendChild(fechaHidden);
                }
                fechaHidden.value = fechaInput ? fechaInput.value : '';

                // Deshabilitar botón
                evalVacBtnGuardar.disabled = true;
                evalVacBtnGuardar.textContent = 'Guardando...';

                var formData = new FormData(evalVacForm);

                fetch('<?= base_url("evaluaciones/guardar") ?>', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.ok) {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'success',
                                    title: '¡Guardado!',
                                    text: data.mensaje || 'Evaluación guardada correctamente.',
                                    confirmButtonColor: '#101a61',
                                }).then(function() {
                                    if (data.url_retorno) {
                                        window.location.href = data.url_retorno;
                                    }
                                });
                            } else {
                                evalVacShowToast(data.mensaje || 'Evaluación guardada.', 'ok');
                                if (data.url_retorno) {
                                    setTimeout(function() {
                                        window.location.href = data.url_retorno;
                                    }, 1500);
                                }
                            }
                        } else {
                            if (typeof Swal !== 'undefined') {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: data.mensaje || 'No se pudo guardar la evaluación.',
                                    confirmButtonColor: '#101a61',
                                });
                            } else {
                                evalVacShowToast(data.mensaje || 'Error al guardar.', 'error');
                            }
                        }
                    })
                    .catch(function(err) {
                        console.error('Error:', err);
                        evalVacShowToast('Error de conexión.', 'error');
                    })
                    .finally(function() {
                        evalVacBtnGuardar.disabled = false;
                        evalVacBtnGuardar.textContent = '<?= $esEdicion ? "Actualizar evaluación" : "Guardar evaluación" ?>';
                    });
            });
        }

        // ── Init ──
        evalVacUpdateSummary();
        evalVacApplyFilters();
        evalVacShowStep(1);
    });
</script>

<?= $this->endSection() ?>
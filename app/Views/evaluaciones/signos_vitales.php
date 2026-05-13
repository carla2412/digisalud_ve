<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));

$infoPesquisaActual = $infoPesquisas[$tipoPesquisaId] ?? [];

$nombrePesquisa = $infoPesquisaActual['nombre']
  ?? ($tipoPesquisa['descripcion_view'] ?? $tipoPesquisa['nombre_tipo'] ?? 'Signos vitales');

$iconoPesquisa = $infoPesquisaActual['img'] ?? 'signosVitales2.svg';

$esEdicion    = ! empty($evaluacionExistente);
$evalId       = $evaluacionExistente['id_evaluacion'] ?? '';
$obsExistente = $evaluacionExistente['observaciones'] ?? '';


$fechaEvaluacionRaw = $evaluacionExistente['fecha_evaluacion'] ?? date('Y-m-d');
$fechaEvaluacionIso = ! empty($fechaEvaluacionRaw)
  ? date('Y-m-d', strtotime($fechaEvaluacionRaw))
  : date('Y-m-d');
$fechaEvaluacionVista = ! empty($fechaEvaluacionRaw)
  ? date('d/m/Y', strtotime($fechaEvaluacionRaw))
  : date('d/m/Y');

$urlRetorno = $jornadaId
  ? base_url("jornadas/{$jornadaId}/beneficiarios")
  : base_url("centros/{$centroId}/beneficiarios");

$valorCampo = static function (string $codigo, $default = '') use ($valoresExistentes) {
  return esc($valoresExistentes[$codigo] ?? $default);
};

$formatoNumeroVista = static function ($valor, $default = '') {
  if ($valor === '' || $valor === null) {
    return esc($default);
  }

  $numero = (float) str_replace(',', '.', (string) $valor);
  return esc(number_format($numero, 4, ',', ''));
};
?>

<style>
 

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
   
    background: var(--ds-bg);
    color: var(--ds-text);
  }


  .sig_vit-sidebar {
    background: var(--ds-dark);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 14px 0;
  }

  .sig_vit-sidebar__logo,
  .sig_vit-sidebar__item {
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

  .sig_vit-sidebar__item img {
    width: 24px;
    height: 24px;
    filter: brightness(0) invert(1);
    opacity: .65;
  }

  .sig_vit-sidebar__item:hover,
  .sig_vit-sidebar__item.active {
    background: #fff;
  }

  .sig_vit-sidebar__item:hover img,
  .sig_vit-sidebar__item.active img {
    filter: none;
    opacity: 1;
  }

  .sig_vit-sidebar__item.sig_vit-evaluado::after {
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

  .sig_vit-sidebar__item[title]::before {
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

  .sig_vit-sidebar__item:hover[title]::before {
    opacity: 1;
  }

  .nav-item {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    border: 1px solid rgba(255, 255, 255, 0.12);
    background: rgba(255, 255, 255, 0.08);
    color: #fff;
    display: grid;
    place-items: center;
    position: relative;
    font-size: 20px;
    text-decoration: none;
    overflow: visible;
  }

  .nav-item img {
    width: 25px;
    height: 25px;
    object-fit: contain;
    filter: brightness(0) invert(1);
    opacity: .72;
  }

  .nav-item.active {
    background: #ffffff;
    color: var(--ds-dark);
    outline: 4px solid #1fc7ff;
  }

  .nav-item.active img {
    filter: none;
    opacity: 1;
  }

  .nav-item.sig_vit-has-dot::after {
    content: "";
    width: 9px;
    height: 9px;
    background: #23d160;
    border-radius: 50%;
    position: absolute;
    right: -1px;
    bottom: 5px;
    border: 2px solid var(--ds-primary);
  }

  .sig_vit-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
  }

  .sig_vit-lab-main {
    display: flex;
    flex-direction: column;
    min-width: 0;
    padding: 22px 26px 88px;
  }

  .sig_vit-lab-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    margin-bottom: 18px;
  }

  .sig_vit-lab-title-row {
    display: flex;
    align-items: center;
    gap: 12px;
  }

  .sig_vit-lab-icon {
    width: 48px;
    height: 48px;
    border-radius: 18px;
    display: grid;
    place-items: center;
    background: #fff;
    box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
  }

  .sig_vit-lab-icon img {
    width: 30px;
    height: 30px;
  }

  .sig_vit-lab-header h1 {
    margin: 0;
    color: var(--ds-primary);
    font-size: 1.35rem;
    font-weight: 900;
  }

  .sig_vit-lab-header p {
    margin: 2px 0 0;
    color: var(--ds-muted);
    font-size: .9rem;
  }

  .sig_vit-lab-badge {
    display: inline-flex;
    margin-top: 6px;
    padding: 3px 10px;
    border-radius: 999px;
    font-size: .72rem;
    font-weight: 800;
  }

  .sig_vit-lab-badge.sig_vit-new {
    background: #dbeafe;
    color: #1e40af;
  }

  .sig_vit-lab-badge.sig_vit-edit {
    background: #fef3c7;
    color: #92400e;
  }

  .sig_vit-btn-volver {
    color: var(--ds-muted);
    text-decoration: none;
    font-size: .85rem;
    font-weight: 700;
  }

  .sig_vit-btn-volver:hover {
    color: var(--ds-primary);
  }

  @media (max-width: 760px) {
    .sig_vit-lab-main {
      padding: 18px 14px 92px;
    }

    .sig_vit-lab-header {
      align-items: flex-start;
      flex-direction: column;
    }
  }

  .sig_vit-title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .sig_vit-title-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: var(--ds-dark);
    color: #fff;
    display: grid;
    place-items: center;
    font-size: 24px;
  }

  h1 {
    margin: 0;
    font-size: 24px;
    color: var(--ds-primary);
    line-height: 1.1;
  }

  .sig_vit-patient-row {
    margin-top: 4px;
    display: flex;
    align-items: center;
    gap: 12px;
    color: #344054;
    font-size: 14px;
  }

  .badge {
    padding: 5px 12px;
    border-radius: 999px;
    background: #fff0c9;
    color: #b54708;
    font-weight: 700;
    font-size: 12px;
  }

  .badge.sig_vit-new {
    background: #dbeafe;
    color: #1e40af;
  }

  .sig_vit-back-link {
    color: #53627c;
    text-decoration: none;
    font-size: 14px;
  }

  .sig_vit-content {
    padding: 24px 28px;
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  .sig_vit-top-grid {
    display: grid;
    grid-template-columns: minmax(340px, 0.95fr) minmax(560px, 1.3fr);
    gap: 16px;
  }

  .sig_vit-tip-card,
  .sig_vit-summary-card,
  .sig_vit-form-card {
    background: var(--card);
    border: 1px solid #e6edf6;
    border-radius: 12px;
    box-shadow: var(--ds-secondary-light);
  }

  .sig_vit-tip-card {
    padding: 18px;
  }

  .sig_vit-tip-box {
    min-height: 80px;
    border-radius: 10px;
    border: 1px solid var(--ds-border);
    background: var(--ds-bg);
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    color: #075985;
    line-height: 1.45;
  }

  .sig_vit-tip-icon {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #208bee;
    color: #fff;
    display: grid;
    place-items: center;
    font-weight: 800;
    flex: 0 0 auto;
  }

  .sig_vit-summary-card {
    padding: 18px 22px;
  }

  .sig_vit-summary-card h2 {
    margin: 0 0 16px;
    font-size: 18px;
    color: #0f172a;
  }

  .sig_vit-summary-row {
    display: grid;
    grid-template-columns: 230px repeat(5, 1fr);
    align-items: center;
    gap: 16px;
  }

  .sig_vit-status-pill {
    background: var(--ds-bg);
    border: 1px solid #acdcb9;
    border-radius: 10px;
    padding: 12px 14px;
    color: var(--success);
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 13px;
    min-height: 54px;
  }

  .sig_vit-status-pill.sig_vit-warning {
    background: var(--ds-warning);
    border-color: #fedf89;
    color: var(--warning);
  }

  .sig_vit-status-pill.sig_vit-danger {
    background: var(--ds-danger);
    border-color: #fecdca;
    color: var(--danger);
  }

  .sig_vit-status-pill strong {
    display: block;
    color: inherit;
  }

  .sig_vit-metric {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    border-left: 1px solid var(--ds-border);
    padding-left: 16px;
  }

  .sig_vit-metric:first-of-type {
    border-left: 0;
  }

  .sig_vit-metric-icon {
    font-size: 27px;
    line-height: 1;
  }

  .sig_vit-metric-value {
    font-weight: 800;
    font-size: 17px;
    color: #0f172a;
    line-height: 1.1;
  }

  .sig_vit-metric-unit {
    color: var(--ds-muted);
    font-size: 12px;
    margin-top: 2px;
  }

  .sig_vit-cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    align-items: stretch;
  }

  .sig_vit-form-card {
    padding: 22px;
    min-height: 350px;
    max-height: 600px;
  }

  .sig_vit-card-title {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
  }

  .sig_vit-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 25px;
    display: grid;
    place-items: center;
    font-size: 24px;
  }

  .sig_vit-card-icon.sig_vit-purple {
    background: #ffe9fa;

  }

  .sig_vit-card-icon.sig_vit-purple>img {
    width: 3rem;
  }

  .sig_vit-card-icon.sig_vit-green {
    background: #e8f8ed;
    color: #12a150;
  }

  .sig_vit-card-icon.sig_vit-blue {
    background: #e9f4ff;
    color: #1f7ae0;
  }

  .sig_vit-card-title h3 {
    margin: 0;
    color: #11184f;
    font-size: 19px;
  }

  .sig_vit-field {
    margin-bottom: 18px;
  }

  label {
    display: block;
    font-weight: 600;
    color: #182033;
    margin-bottom: 8px;
    font-size: 15px;
  }

  .sig_vit-required {
    color: #f04438;
  }

  .sig_vit-input-wrap {
    height: 43px;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid var(--ds-border);
    border-radius: 10px;
    overflow: hidden;
  }

  input,
  select,
  textarea {
    width: 100%;
    border: 0;
    outline: 0;
    background: transparent;
    font: inherit;
    color: #172033;
  }

  input,
  select {
    height: 100%;
    padding: 0 14px;
  }

  select {
    appearance: none;
    background-image: linear-gradient(45deg, transparent 50%, #344054 50%),
      linear-gradient(135deg, #344054 50%, transparent 50%);
    background-position: calc(100% - 18px) 18px, calc(100% - 13px) 18px;
    background-size: 5px 5px, 5px 5px;
    background-repeat: no-repeat;
    padding-right: 36px;
  }

  .sig_vit-unit {
    min-width: 72px;
    height: 22px;
    display: grid;
    place-items: center;
    border-left: 1px solid var(--ds-border);
    color: #53627c;
    font-weight: 700;
    font-size: 13px;
    margin-right: 8px;
  }

  .sig_vit-calendar {
    width: 42px;
    display: grid;
    place-items: center;
    color: #111827;
    font-size: 17px;
  }

  .sig_vit-hint {
    color: #64748b;
    margin-top: 7px;
    font-size: 13px;
    line-height: 1.4;
  }

  textarea {
    min-height: 88px;
    resize: vertical;
    padding: 12px 14px;
    border: 1px solid var(--ds-border);
    border-radius: 10px;
    display: block;
  }

  .sig_vit-textarea-footer {
    text-align: right;
    color: #64748b;
    font-size: 12px;
    margin-top: 5px;
  }

  .sig_vit-actions {
    display: flex;
    justify-content: flex-end;
    gap: 14px;
    padding-top: 12px;
  }

  .btn {
    border: 1;

    min-height: 42px;
    padding: 0 16px;


    cursor: pointer;
    transition: .2s ease;
  }

  .btn.sig_vit-secondary {
    background: #fff;
    color: #344054;
    border: 1px solid #d8e0ed;
  }

  .btn.sig_vit-soft {
    background: #e9edff;
    color: var(--ds-primary);
  }

  .btn.sig_vit-primary {
    background: var(--ds-primary);
    color: #fff;
    min-width: 210px;
  }

  .btn:hover {
    filter: brightness(0.97);
  }

  .sig_vit-input-wrap.is-invalid,
  textarea.is-invalid,
  input.is-invalid,
  select.is-invalid {
    border-color: #f04438 !important;
    box-shadow: 0 0 0 3px rgba(240, 68, 56, .08);
  }

  .sig_vit-input-wrap.sig_vit-is-warning {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, .10);
  }

  .sig_vit-field-error {
    color: #b42318;
    margin-top: 7px;
    font-size: 12px;
    display: none;
  }

  .sig_vit-field-error.show {
    display: block;
  }

  @media (max-width: 1280px) {
    .sig_vit-top-grid {
      grid-template-columns: 1fr;
    }

    .sig_vit-summary-row {
      grid-template-columns: repeat(3, 1fr);
    }

    .sig_vit-status-pill {
      grid-column: 1 / -1;
    }

    .sig_vit-cards-grid {
      grid-template-columns: 1fr;
    }

    .sig_vit-form-card {
      min-height: auto;
    }
  }

  @media (max-width: 768px) {
    .sig_vit-app {
      grid-template-columns: 1fr;
    }

    .sig_vit-sidebar {
      flex-direction: row;
      overflow-x: auto;
      justify-content: flex-start;
      padding: 10px 12px;
    }


    .sig_vit-content {
      padding: 18px;
    }

    .sig_vit-summary-row {
      grid-template-columns: 1fr 1fr;
    }

    .sig_vit-actions {
      flex-direction: column;
    }

    .btn,
    .btn.sig_vit-primary {
      width: 100%;
    }
  }


  .sig_vit-lab-page {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr);
    min-height: 100dvh;
    overflow: clip;
  }

  @media (max-width: 768px) {
    .sig_vit-lab-page {
      grid-template-columns: 1fr;
    }

    .sig_vit-sidebar {
      flex-direction: row;
      overflow-x: auto;
      justify-content: flex-start;
      padding: 10px 12px;
    }
  }
</style>

<div class="sig_vit-lab-page" data-page="evaluacion">
  <aside class="sig_vit-sidebar">


    <?php foreach ($pesquisasActividad as $pid): ?>
      <?php
      $info = $infoPesquisas[$pid] ?? null;
      if (! $info) continue;

      $esActiva    = ((int) $pid === (int) $tipoPesquisaId);
      $yaEvaluada  = in_array($pid, $pesquisasEvaluadas);
      $clases      = 'sig_vit-sidebar__item';
      if ($esActiva)   $clases .= ' active';
      if ($yaEvaluada) $clases .= ' sig_vit-evaluado';

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

  <main class="sig_vit-lab-main">





    <section class="sig_vit-content">
      <div class="sig_vit-top-grid">
        <div class="sig_vit-tip-card">
          <div>

            <div class="sig_vit-lab-header">
              <div class="sig_vit-lab-title-row">
                <div class="sig_vit-lab-icon">
                  <img src="<?= base_url('img/' . $iconoPesquisa) ?>"
                    alt="<?= esc($nombrePesquisa) ?>">
                </div>

                <div>
                  <h1><?= esc($nombrePesquisa) ?></h1>
                  <p><?= $nombreCompleto ?></p>

                  <span class="sig_vit-lab-badge <?= $esEdicion ? 'sig_vit-edit' : 'sig_vit-new' ?>">
                    <?= $esEdicion ? 'Editando' : 'Nueva evaluación' ?>
                  </span>
                </div>
              </div>

            </div>
          </div>
        </div>

        <div class="sig_vit-summary-card">
          <h2>Resumen rápido</h2>

          <div class="sig_vit-summary-row">
            <div class="sig_vit-status-pill" id="estadoGeneralSignos">
              <span id="estadoGeneralIcon">✓</span>
              <div>
                <strong id="estadoGeneralTitle">Estado general:</strong>
                <span id="estadoGeneralText">sin alertas críticas detectadas.</span>
              </div>
            </div>

            <div class="sig_vit-metric">
              <div class="sig_vit-metric-icon"><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit_120_off.png") ?>" alt=""></div>
              <div>

                <div class="sig_vit-metric-value" id="summaryPressure">120/80</div>
                <div class="sig_vit-metric-unit">mmHg</div>
              </div>
            </div>

            <div class="sig_vit-metric">
              <div class="sig_vit-metric-icon"><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit.png") ?>" alt=""></div>
              <div>
                <div class="sig_vit-metric-value" id="summaryHeart">72</div>
                <div class="sig_vit-metric-unit">lpm</div>
              </div>
            </div>

            <div class="sig_vit-metric">
              <div class="sig_vit-metric-icon"><img style="width: 30px;" src="<?= base_url("img/icon/lungs_4981940.png") ?>" alt=""></div>
              <div>
                <div class="sig_vit-metric-value" id="summaryResp">16</div>
                <div class="sig_vit-metric-unit">rpm</div>
              </div>
            </div>

            <div class="sig_vit-metric">
              <div class="sig_vit-metric-icon"><img style="width: 30px;" src="<?= base_url("img/icon/thermometer.png") ?>" alt=""></div>
              <div>
                <div class="sig_vit-metric-value" id="summaryTemp">37</div>
                <div class="sig_vit-metric-unit">°C</div>
              </div>
            </div>

            <div class="sig_vit-metric">
              <div class="sig_vit-metric-icon" style="color:#ec4899;"><img style="width: 35px;" src="<?= base_url("img/icon/oximeter.png") ?>" alt=""></div>
              <div>
                <div class="sig_vit-metric-value" id="summaryOxygen">97</div>
                <div class="sig_vit-metric-unit">%</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <form id="formSignosVitales" novalidate>
        <input type="hidden" name="beneficiario_id" value="<?= (int) $beneficiario['id_beneficiario'] ?>">
        <input type="hidden" name="tipo_pesquisa_id" value="<?= (int) $tipoPesquisaId ?>">
        <input type="hidden" name="jornada_id" value="<?= (int) $jornadaId ?>">
        <input type="hidden" name="centro_id" value="<?= (int) $centroId ?>">
        <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">
        <input type="hidden" id="fechaEvaluacionIso" value="<?= esc($fechaEvaluacionIso) ?>">

        <div class="sig_vit-cards-grid">
          <section class="sig_vit-form-card">
            <div class="sig_vit-card-title">
              <div class="sig_vit-card-icon sig_vit-purple"><img src="<?= base_url('img/icon/icon_vit_120_off.png') ?>"></div>
              <h3>Presión arterial</h3>
            </div>

            <div class="sig_vit-field">
              <label for="evaluationDate">Fecha evaluación <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="evaluationDate" name="fecha_evaluacion" data-codigo="fecha_evaluacion" value="<?= esc($fechaEvaluacionVista) ?>" required />
                <span class="sig_vit-calendar">🗓️</span>
              </div>
              <div class="sig_vit-field-error">La fecha de evaluación es obligatoria.</div>
            </div>

            <div class="sig_vit-field">
              <label for="systolic">Tensión arterial sistólica <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="systolic" name="campos[tension_sistolica]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['tension_sistolica'] ?? '', '120,0000') ?>" class="sig_vit-campo-signo-vital" data-codigo="tension_sistolica" data-label="Tensión sistólica" data-unidad="mmHg" data-min="70" data-max="180" data-alerta-min="90" data-alerta-max="140" required />
                <span class="sig_vit-unit">mmHg</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 90 - 140 mmHg.</div>
              <div class="sig_vit-field-error"></div>
            </div>

            <div class="sig_vit-field">
              <label for="diastolic">Tensión arterial diastólica <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="diastolic" name="campos[tension_diastolica]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['tension_diastolica'] ?? '', '80,00') ?>" class="sig_vit-campo-signo-vital" data-codigo="tension_diastolica" data-label="Tensión diastólica" data-unidad="mmHg" data-min="40" data-max="120" data-alerta-min="60" data-alerta-max="90" required />
                <span class="sig_vit-unit">mmHg</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 60 - 90 mmHg.</div>
              <div class="sig_vit-field-error"></div>
            </div>
          </section>

          <section class="sig_vit-form-card">
            <div class="sig_vit-card-title">
              <div class="sig_vit-card-icon sig_vit-green"><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit.png") ?>" alt=""></div>
              <h3>Frecuencias</h3>
            </div>

            <div class="sig_vit-field">
              <label for="heartRate">Frecuencia cardíaca <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="heartRate" name="campos[frecuencia_cardiaca]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['frecuencia_cardiaca'] ?? '', '72,0000') ?>" class="sig_vit-campo-signo-vital" data-codigo="frecuencia_cardiaca" data-label="Frecuencia cardíaca" data-unidad="lpm" data-min="30" data-max="220" data-alerta-min="60" data-alerta-max="100" required />
                <span class="sig_vit-unit">lpm</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 60 - 100 lpm.</div>
              <div class="sig_vit-field-error"></div>
            </div>

            <div class="sig_vit-field">
              <label for="respiratoryRate">Frecuencia respiratoria <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="respiratoryRate" name="campos[frecuencia_respiratoria]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['frecuencia_respiratoria'] ?? '', '16,0000') ?>" class="sig_vit-campo-signo-vital" data-codigo="frecuencia_respiratoria" data-label="Frecuencia respiratoria" data-unidad="rpm" data-min="5" data-max="60" data-alerta-min="12" data-alerta-max="20" required />
                <span class="sig_vit-unit">rpm</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 12 - 20 rpm.</div>
              <div class="sig_vit-field-error"></div>
            </div>
          </section>

          <section class="sig_vit-form-card">
            <div class="sig_vit-card-title">
              <div class="sig_vit-card-icon sig_vit-blue"><img style="width: 35px;" src="<?= base_url("img/icon/thermometer.png") ?>" alt=""></div>
              <h3>Temperatura y seguimiento</h3>
            </div>

            <div class="sig_vit-field">
              <label for="temperature">Temperatura <span class="sig_vit-required">*</span></label>
              <div class="sig_vit-input-wrap">
                <input id="temperature" name="campos[temperatura]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['temperatura'] ?? '', '37,0000') ?>" class="sig_vit-campo-signo-vital" data-codigo="temperatura" data-label="Temperatura" data-unidad="°C" data-min="30" data-max="45" data-alerta-min="36" data-alerta-max="37.5" required />
                <span class="sig_vit-unit">°C</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 36.0 - 37.5 °C.</div>
              <div class="sig_vit-field-error"></div>
            </div>

            <div class="sig_vit-field">
              <label for="oxygen">Saturación de oxígeno</label>
              <div class="sig_vit-input-wrap">
                <input id="oxygen" name="campos[saturacion_o2]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['saturacion_o2'] ?? '', '97,0000') ?>" class="sig_vit-campo-signo-vital" data-codigo="saturacion_o2" data-label="Saturación de oxígeno" data-unidad="%" data-min="50" data-max="100" data-alerta-min="95" data-alerta-max="100" />
                <span class="sig_vit-unit">%</span>
              </div>
              <div class="sig_vit-hint">Rango esperado: 95 - 100%.</div>
              <div class="sig_vit-field-error"></div>
            </div>

            <div class="sig_vit-field">
              <label for="referral">¿Requiere remisión?</label>
              <div class="sig_vit-input-wrap">
                <select id="referral" name="campos[especialista_vitales]" class="sig_vit-campo-signo-vital" data-codigo="especialista_vitales" data-label="Remisión" data-unidad="">
                  <option value="n" <?= (($valoresExistentes['especialista_vitales'] ?? 'n') === 'n') ? 'selected' : '' ?>>No requiere remisión</option>
                  <option value="s" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 's') ? 'selected' : '' ?>>Requiere remisión</option>
                  <option value="seguimiento" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 'seguimiento') ? 'selected' : '' ?>>Seguimiento médico</option>
                </select>
              </div>
              <div class="sig_vit-hint">Usa esta opción si los signos vitales sugieren seguimiento médico.</div>
            </div>

            <div class="sig_vit-field">
              <label for="observations">Observaciones</label>
              <textarea id="observations" name="campos[observaciones_vitales]" class="sig_vit-campo-signo-vital" data-codigo="observaciones_vitales" data-label="Observaciones" data-unidad="" maxlength="200"><?= $valorCampo('observaciones_vitales', '') ?></textarea>
              <div class="sig_vit-textarea-footer">
                <span id="charCount">0</span> / 200
              </div>
            </div>
          </section>
        </div>

        <div class="sig_vit-actions">
          <button type="button" id="btnCancelarSignos" class="btn sig_vit-secondary">Cancelar</button>
          <button type="button" id="btnLimpiarSignos" class="btn sig_vit-soft">Limpiar</button>
          <button type="button" id="btnGuardarSignos" class="btn sig_vit-primary">Guardar</button>
        </div>
      </form>
    </section>
  </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  (function() {
    const $ = (selector) => document.querySelector(selector);

    const form = $('#formSignosVitales');
    const btnGuardar = $('#btnGuardarSignos');
    const btnLimpiar = $('#btnLimpiarSignos');
    const btnCancelar = $('#btnCancelarSignos');
    const URL_RETORNO = '<?= $urlRetorno ?>';

    const fields = {
      systolic: $('#systolic'),
      diastolic: $('#diastolic'),
      heartRate: $('#heartRate'),
      respiratoryRate: $('#respiratoryRate'),
      temperature: $('#temperature'),
      oxygen: $('#oxygen'),
      observations: $('#observations'),
      evaluationDate: $('#evaluationDate')
    };

    const summary = {
      pressure: $('#summaryPressure'),
      heart: $('#summaryHeart'),
      respiratory: $('#summaryResp'),
      temperature: $('#summaryTemp'),
      oxygen: $('#summaryOxygen'),
      status: $('#estadoGeneralSignos'),
      statusIcon: $('#estadoGeneralIcon'),
      statusTitle: $('#estadoGeneralTitle'),
      statusText: $('#estadoGeneralText')
    };

    const campos = Array.from(document.querySelectorAll('.sig_vit-campo-signo-vital'));

    function normalizeValue(value) {
      return String(value || '').replace(',', '.').trim();
    }

    function parseNumber(value) {
      const number = parseFloat(normalizeValue(value));
      return Number.isFinite(number) ? number : null;
    }

    function compactValue(value) {
      const number = parseNumber(value);
      return number !== null ? String(Math.round(number)) : '-';
    }

    function decimalValue(value) {
      const number = parseNumber(value);
      return number !== null ? String(number) : '';
    }

    function setFieldState(input, state, message) {
      const field = input.closest('.sig_vit-field');
      const wrap = input.closest('.sig_vit-input-wrap');
      const error = field ? field.querySelector('.sig_vit-field-error') : null;

      input.classList.remove('is-invalid');
      if (wrap) wrap.classList.remove('is-invalid', 'sig_vit-is-warning');
      if (error) {
        error.textContent = '';
        error.classList.remove('show');
      }

      if (state === 'invalid') {
        input.classList.add('is-invalid');
        if (wrap) wrap.classList.add('is-invalid');
        if (error) {
          error.textContent = message || 'Valor inválido.';
          error.classList.add('show');
        }
      }

      if (state === 'warning' && wrap) {
        wrap.classList.add('sig_vit-is-warning');
      }
    }

    function updateSummary() {
      summary.pressure.textContent = `${compactValue(fields.systolic.value)}/${compactValue(fields.diastolic.value)}`;
      summary.heart.textContent = compactValue(fields.heartRate.value);
      summary.respiratory.textContent = compactValue(fields.respiratoryRate.value);
      summary.temperature.textContent = compactValue(fields.temperature.value);
      summary.oxygen.textContent = compactValue(fields.oxygen.value);
      updateCharCount();
      updateStatus();
    }

    function updateCharCount() {
      $('#charCount').textContent = fields.observations.value.length;
    }

    function validarFecha() {
      const value = fields.evaluationDate.value.trim();
      const wrap = fields.evaluationDate.closest('.sig_vit-input-wrap');
      const error = fields.evaluationDate.closest('.sig_vit-field').querySelector('.sig_vit-field-error');
      wrap.classList.remove('is-invalid');
      error.classList.remove('show');

      if (!value) {
        wrap.classList.add('is-invalid');
        error.textContent = 'La fecha de evaluación es obligatoria.';
        error.classList.add('show');
        return false;
      }

      if (!toIsoDate(value)) {
        wrap.classList.add('is-invalid');
        error.textContent = 'Usa el formato dd/mm/aaaa.';
        error.classList.add('show');
        return false;
      }

      return true;
    }

    function validarCampo(campo) {
      const esObligatorio = campo.hasAttribute('required');
      const valorTexto = campo.value.trim();
      const min = campo.dataset.min !== undefined ? Number(campo.dataset.min) : null;
      const max = campo.dataset.max !== undefined ? Number(campo.dataset.max) : null;
      const alertaMin = campo.dataset.alertaMin !== undefined ? Number(campo.dataset.alertaMin) : null;
      const alertaMax = campo.dataset.alertaMax !== undefined ? Number(campo.dataset.alertaMax) : null;
      const valor = parseNumber(valorTexto);

      setFieldState(campo, 'clean');

      if (campo.tagName === 'SELECT' || campo.tagName === 'TEXTAREA') {
        return true;
      }

      if (esObligatorio && valorTexto === '') {
        setFieldState(campo, 'invalid', 'Este campo es obligatorio.');
        return false;
      }

      if (valorTexto !== '' && valor === null) {
        setFieldState(campo, 'invalid', 'Ingresa un número válido.');
        return false;
      }

      if (valor !== null) {
        if (min !== null && valor < min) {
          setFieldState(campo, 'invalid', `El valor mínimo permitido es ${min}.`);
          return false;
        }

        if (max !== null && valor > max) {
          setFieldState(campo, 'invalid', `El valor máximo permitido es ${max}.`);
          return false;
        }

        if ((alertaMin !== null && valor < alertaMin) || (alertaMax !== null && valor > alertaMax)) {
          setFieldState(campo, 'warning');
        }
      }

      return true;
    }

    function generarAlertas() {
      const listaAlertas = [];

      campos.forEach((campo) => {
        if (campo.tagName === 'SELECT' || campo.tagName === 'TEXTAREA') return;

        const valor = parseNumber(campo.value);
        if (valor === null) return;

        const alertaMin = Number(campo.dataset.alertaMin);
        const alertaMax = Number(campo.dataset.alertaMax);
        const label = campo.dataset.label;
        const unidad = campo.dataset.unidad || '';

        if (!Number.isNaN(alertaMin) && valor < alertaMin) {
          listaAlertas.push(`${label}: ${valor} ${unidad} está por debajo del rango esperado.`);
        }

        if (!Number.isNaN(alertaMax) && valor > alertaMax) {
          listaAlertas.push(`${label}: ${valor} ${unidad} está por encima del rango esperado.`);
        }
      });

      return listaAlertas;
    }

    function updateStatus() {
      const numericWithValue = campos.filter((campo) => {
        return campo.tagName !== 'SELECT' && campo.tagName !== 'TEXTAREA' && campo.value.trim() !== '';
      });
      const listaAlertas = generarAlertas();

      summary.status.className = 'sig_vit-status-pill';

      if (numericWithValue.length === 0) {
        summary.status.classList.add('sig_vit-warning');
        summary.statusIcon.textContent = 'i';
        summary.statusTitle.textContent = 'Pendiente:';
        summary.statusText.textContent = 'completa los signos vitales para ver el estado general.';
        return;
      }

      if (listaAlertas.length === 0) {
        summary.statusIcon.textContent = '✓';
        summary.statusTitle.textContent = 'Estado general:';
        summary.statusText.textContent = 'sin alertas críticas detectadas.';
        return;
      }

      summary.status.classList.add('sig_vit-warning');
      summary.statusIcon.textContent = '!';
      summary.statusTitle.textContent = 'Revisar:';
      summary.statusText.textContent = `se detectaron ${listaAlertas.length} valor(es) fuera del rango esperado.`;
    }

    function validarFormulario() {
      let valido = validarFecha();

      campos.forEach((campo) => {
        if (!validarCampo(campo)) valido = false;
      });

      updateSummary();
      return valido;
    }

    function toIsoDate(value) {
      const clean = String(value || '').trim();

      if (/^\d{4}-\d{2}-\d{2}$/.test(clean)) {
        return clean;
      }

      const match = clean.match(/^(\d{2})\/(\d{2})\/(\d{4})$/);
      if (!match) return '';

      const [, d, m, y] = match;
      return `${y}-${m}-${d}`;
    }

    function prepararFormData() {
      const formData = new FormData(form);

      formData.set('fecha_evaluacion', toIsoDate(fields.evaluationDate.value));
      formData.set('observaciones', fields.observations.value.trim());

      campos.forEach((campo) => {
        if (campo.tagName === 'SELECT' || campo.tagName === 'TEXTAREA') {
          formData.set(campo.name, campo.value.trim());
          return;
        }

        formData.set(campo.name, decimalValue(campo.value));
      });

      const csrfName = '<?= csrf_token() ?>';
      const csrfHash = '<?= csrf_hash() ?>';
      formData.append(csrfName, csrfHash);

      return formData;
    }

    async function guardarEvaluacion() {
      if (!validarFormulario()) {
        summary.status.className = 'sig_vit-status-pill sig_vit-danger';
        summary.statusIcon.textContent = '!';
        summary.statusTitle.textContent = 'Faltan datos:';
        summary.statusText.textContent = 'revisa los campos marcados antes de guardar.';
        return;
      }

      btnGuardar.disabled = true;
      btnGuardar.innerHTML = 'Guardando...';

      try {
        const resp = await fetch('<?= base_url("evaluaciones/guardar") ?>', {
          method: 'POST',
          body: prepararFormData(),
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          }
        });

        const data = await resp.json();

        if (data.ok) {
          summary.status.className = 'sig_vit-status-pill';
          summary.statusIcon.textContent = '✓';
          summary.statusTitle.textContent = 'Listo:';
          summary.statusText.textContent = data.mensaje || 'evaluación guardada correctamente.';

          if (typeof Swal !== 'undefined') {
            Swal.fire({
              icon: 'success',
              title: '¡Guardado!',
              text: data.mensaje || 'Evaluación de signos vitales guardada.',
              confirmButtonColor: '#111b69'
            }).then(() => {
              window.location.href = data.url_retorno || URL_RETORNO;
            });
          } else {
            alert(data.mensaje || 'Evaluación guardada.');
            window.location.href = data.url_retorno || URL_RETORNO;
          }

          return;
        }

        summary.status.className = 'sig_vit-status-pill sig_vit-danger';
        summary.statusIcon.textContent = '!';
        summary.statusTitle.textContent = 'Error:';
        summary.statusText.textContent = data.mensaje || 'no se pudo guardar.';

        if (data.campo) {
          const campoError = document.querySelector(`[data-codigo="${data.campo}"]`);
          if (campoError) {
            setFieldState(campoError, 'invalid', data.mensaje || 'Revisa este campo.');
            campoError.focus();
          }
        }

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.mensaje || 'No se pudo guardar la evaluación.',
            confirmButtonColor: '#111b69'
          });
        }
      } catch (err) {
        console.error('Error guardando evaluación:', err);
        summary.status.className = 'sig_vit-status-pill sig_vit-danger';
        summary.statusIcon.textContent = '!';
        summary.statusTitle.textContent = 'Error:';
        summary.statusText.textContent = 'no se pudo conectar con el servidor.';

        if (typeof Swal !== 'undefined') {
          Swal.fire({
            icon: 'error',
            title: 'Error de conexión',
            text: 'No se pudo conectar con el servidor. Intenta de nuevo.',
            confirmButtonColor: '#111b69'
          });
        }
      } finally {
        btnGuardar.disabled = false;
        btnGuardar.innerHTML = '▣ Guardar evaluación';
      }
    }

    function ejecutarLimpieza() {
      form.reset();
      fields.evaluationDate.value = $('#fechaEvaluacionIso').value.split('-').reverse().join('/');

      campos.forEach((campo) => {
        setFieldState(campo, 'clean');
      });

      const dateWrap = fields.evaluationDate.closest('.sig_vit-input-wrap');
      const dateError = fields.evaluationDate.closest('.sig_vit-field').querySelector('.sig_vit-field-error');
      dateWrap.classList.remove('is-invalid');
      dateError.classList.remove('show');

      updateSummary();
    }

    function limpiarFormulario() {
      if (typeof Swal !== 'undefined') {
        Swal.fire({
          title: '¿Limpiar formulario?',
          text: 'Se borrarán todos los valores ingresados.',
          icon: 'question',
          showCancelButton: true,
          confirmButtonColor: '#111b69',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Sí, limpiar',
          cancelButtonText: 'Cancelar'
        }).then((result) => {
          if (result.isConfirmed) ejecutarLimpieza();
        });
      } else if (confirm('¿Limpiar todos los campos?')) {
        ejecutarLimpieza();
      }
    }

    function cancelarSignos() {
      Swal.fire({
        title: '¿Desea cancelar la evaluación?',
        text: 'Los cambios no guardados se perderán.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'No, continuar',
        reverseButtons: true,
        confirmButtonColor: '#3695f5',
        cancelButtonColor: '#38393a'
      }).then((result) => {
        if (result.isConfirmed) {
          window.location.href = URL_RETORNO;
        }
      });
    }

    campos.forEach((campo) => {
      campo.addEventListener('input', () => {
        validarCampo(campo);
        updateSummary();
      });
      campo.addEventListener('change', () => {
        validarCampo(campo);
        updateSummary();
      });
    });

    fields.evaluationDate.addEventListener('input', validarFecha);
    fields.evaluationDate.addEventListener('change', validarFecha);
    btnGuardar.addEventListener('click', guardarEvaluacion);
    btnLimpiar.addEventListener('click', limpiarFormulario);
    btnCancelar.addEventListener('click', cancelarSignos);

    updateSummary();
  })();
</script>
<?= $this->endSection() ?>
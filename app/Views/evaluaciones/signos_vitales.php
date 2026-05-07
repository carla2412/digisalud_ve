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
  :root {
    --primary: #101a61;
    --primary-soft: #f5f8fc;
    --accent: #dc2626;
    --bg: #f8f9fa;
    --card: #ffffff;
    --text: #101828;
    --muted: #38393a;
    --border: #e0e6ed;
    --success-bg: #dff5e6;
    --success: #16a34a;
    --warning-bg: #fff4d6;
    --warning: #b54708;
    --danger-bg: #fee4e2;
    --danger: #b42318;
    --info-bg: #e8f8ff;
    --info-border: #83daf4;
    --shadow: 0 8px 24px rgba(16, 24, 40, 0.08);
    --radius: 16px;
    
 
    --lab-sidebar-w: 72px;
  }

  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    background: var(--bg);
    color: var(--text);
  }
 
 
    .sidebar {
        background: var(--primary);
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
        background: var(--success);
        border: 2px solid var(--primary);
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
    color: var(--accent);
    outline: 4px solid #1fc7ff;
  }

  .nav-item.active img {
    filter: none;
    opacity: 1;
  }

  .nav-item.has-dot::after {
    content: "";
    width: 9px;
    height: 9px;
    background: #23d160;
    border-radius: 50%;
    position: absolute;
    right: -1px;
    bottom: 5px;
    border: 2px solid var(--primary);
  }

  .main {
    display: flex;
    flex-direction: column;
    min-width: 0;
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

.btn-volver {
    color: var(--lab-muted);
    text-decoration: none;
    font-size: .85rem;
    font-weight: 700;
}

.btn-volver:hover {
    color: var(--lab-primary);
}

@media (max-width: 760px) {
    .lab-main {
        padding: 18px 14px 92px;
    }

    .lab-header {
        align-items: flex-start;
        flex-direction: column;
    }
}
  .title-wrap {
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .title-icon {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    background: var(--accent);
    color: #fff;
    display: grid;
    place-items: center;
    font-size: 24px;
  }

  h1 {
    margin: 0;
    font-size: 24px;
    color: var(--primary);
    line-height: 1.1;
  }

  .patient-row {
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

  .badge.new {
    background: #dbeafe;
    color: #1e40af;
  }

  .back-link {
    color: #53627c;
    text-decoration: none;
    font-size: 14px;
  }

  .content {
    padding: 24px 28px;
    display: flex;
    flex-direction: column;
    gap: 22px;
  }

  .top-grid {
    display: grid;
    grid-template-columns: minmax(340px, 0.95fr) minmax(560px, 1.3fr);
    gap: 16px;
  }

  .tip-card,
  .summary-card,
  .form-card {
    background: var(--card);
    border: 1px solid #e6edf6;
    border-radius: var(--radius);
    box-shadow: var(--shadow);
  }

  .tip-card {
    padding: 18px;
  }

  .tip-box {
    min-height: 80px;
    border-radius: 10px;
    border: 1px solid var(--info-border);
    background: var(--info-bg);
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 16px 18px;
    color: #075985;
    line-height: 1.45;
  }

  .tip-icon {
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

  .summary-card {
    padding: 18px 22px;
  }

  .summary-card h2 {
    margin: 0 0 16px;
    font-size: 18px;
    color: #0f172a;
  }

  .summary-row {
    display: grid;
    grid-template-columns: 230px repeat(5, 1fr);
    align-items: center;
    gap: 16px;
  }

  .status-pill {
    background: var(--success-bg);
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

  .status-pill.warning {
    background: var(--warning-bg);
    border-color: #fedf89;
    color: var(--warning);
  }

  .status-pill.danger {
    background: var(--danger-bg);
    border-color: #fecdca;
    color: var(--danger);
  }

  .status-pill strong {
    display: block;
    color: inherit;
  }

  .metric {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
    border-left: 1px solid var(--border);
    padding-left: 16px;
  }

  .metric:first-of-type {
    border-left: 0;
  }

  .metric-icon {
    font-size: 27px;
    line-height: 1;
  }

  .metric-value {
    font-weight: 800;
    font-size: 17px;
    color: #0f172a;
    line-height: 1.1;
  }

  .metric-unit {
    color: var(--muted);
    font-size: 12px;
    margin-top: 2px;
  }

  .cards-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    align-items: stretch;
  }

  .form-card {
    padding: 22px;
    min-height: 350px;
    max-height: 600px;
  }

  .card-title {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 22px;
  }

  .card-icon {
    width: 48px;
    height: 48px;
    border-radius: 25px;
    display: grid;
    place-items: center;
    font-size: 24px;
  }

  .card-icon.purple {
    background: #ffe9fa;
    
  }
    .card-icon.purple>img {
    width: 3rem;
  }

  .card-icon.green {
    background: #e8f8ed;
    color: #12a150;
  }

  .card-icon.blue {
    background: #e9f4ff;
    color: #1f7ae0;
  }

  .card-title h3 {
    margin: 0;
    color: #11184f;
    font-size: 19px;
  }

  .field {
    margin-bottom: 18px;
  }

  label {
    display: block;
    font-weight: 600;
    color: #182033;
    margin-bottom: 8px;
    font-size: 15px;
  }

  .required {
    color: #f04438;
  }

  .input-wrap {
    height: 43px;
    display: flex;
    align-items: center;
    background: #fff;
    border: 1px solid var(--border);
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

  .unit {
    min-width: 72px;
    height: 22px;
    display: grid;
    place-items: center;
    border-left: 1px solid var(--border);
    color: #53627c;
    font-weight: 700;
    font-size: 13px;
    margin-right: 8px;
  }

  .calendar {
    width: 42px;
    display: grid;
    place-items: center;
    color: #111827;
    font-size: 17px;
  }

  .hint {
    color: #64748b;
    margin-top: 7px;
    font-size: 13px;
    line-height: 1.4;
  }

  textarea {
    min-height: 88px;
    resize: vertical;
    padding: 12px 14px;
    border: 1px solid var(--border);
    border-radius: 10px;
    display: block;
  }

  .textarea-footer {
    text-align: right;
    color: #64748b;
    font-size: 12px;
    margin-top: 5px;
  }

  .actions {
    display: flex;
    justify-content: flex-end;
    gap: 14px;
    padding-top: 12px;
  }

  .btn {
 border: 1 ;
         
        min-height: 42px;
        padding: 0 16px;
        
       
        cursor: pointer;
        transition: .2s ease;
  }

  .btn.secondary {
    background: #fff;
    color: #344054;
    border: 1px solid #d8e0ed;
  }

  .btn.soft {
    background: #e9edff;
    color: var(--primary);
  }

  .btn.primary {
    background: var(--primary);
    color: #fff;
    min-width: 210px;
  }

  .btn:hover {
    filter: brightness(0.97);
  }

  .input-wrap.is-invalid,
  textarea.is-invalid,
  input.is-invalid,
  select.is-invalid {
    border-color: #f04438 !important;
    box-shadow: 0 0 0 3px rgba(240, 68, 56, .08);
  }

  .input-wrap.is-warning {
    border-color: #f59e0b !important;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, .10);
  }

  .field-error {
    color: #b42318;
    margin-top: 7px;
    font-size: 12px;
    display: none;
  }

  .field-error.show {
    display: block;
  }

  @media (max-width: 1280px) {
    .top-grid {
      grid-template-columns: 1fr;
    }

    .summary-row {
      grid-template-columns: repeat(3, 1fr);
    }

    .status-pill {
      grid-column: 1 / -1;
    }

    .cards-grid {
      grid-template-columns: 1fr;
    }

    .form-card {
      min-height: auto;
    }
  }

  @media (max-width: 768px) {
    .app {
      grid-template-columns: 1fr;
    }

    .sidebar {
            flex-direction: row;
            overflow-x: auto;
            justify-content: flex-start;
            padding: 10px 12px;
        }
   

    .content {
      padding: 18px;
    }

    .summary-row {
      grid-template-columns: 1fr 1fr;
    }

    .actions {
      flex-direction: column;
    }

    .btn,
    .btn.primary {
      width: 100%;
    }
  }

  
    .lab-page {
        display: grid;
        grid-template-columns: var(--lab-sidebar-w) minmax(0, 1fr);
        min-height: 100dvh;
        overflow: clip;
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

 

        

    <section class="content">
      <div class="top-grid">
        <div class="tip-card">
          <div  >
           
             <div class="lab-header">
        <div class="lab-title-row">
            <div class="lab-icon">
                <img src="<?= base_url('img/' . $iconoPesquisa) ?>"
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
 
    </div>
          </div>
        </div>

        <div class="summary-card">
          <h2>Resumen rápido</h2>

          <div class="summary-row">
            <div class="status-pill" id="estadoGeneralSignos">
              <span id="estadoGeneralIcon">✓</span>
              <div>
                <strong id="estadoGeneralTitle">Estado general:</strong>
                <span id="estadoGeneralText">sin alertas críticas detectadas.</span>
              </div>
            </div>

            <div class="metric">
              <div class="metric-icon"  ><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit_120_off.png") ?>" alt=""></div>
              <div>
                
                <div class="metric-value" id="summaryPressure">120/80</div>
                <div class="metric-unit">mmHg</div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-icon" ><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit.png") ?>" alt=""></div>
              <div>
                <div class="metric-value" id="summaryHeart">72</div>
                <div class="metric-unit">lpm</div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-icon" ><img style="width: 30px;" src="<?= base_url("img/icon/lungs_4981940.png") ?>" alt=""></div>
              <div>
                <div class="metric-value" id="summaryResp">16</div>
                <div class="metric-unit">rpm</div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-icon"  ><img style="width: 30px;" src="<?= base_url("img/icon/thermometer.png") ?>" alt=""></div>
              <div>
                <div class="metric-value" id="summaryTemp">37</div>
                <div class="metric-unit">°C</div>
              </div>
            </div>

            <div class="metric">
              <div class="metric-icon" style="color:#ec4899;"><img style="width: 35px;" src="<?= base_url("img/icon/oximeter.png") ?>" alt=""></div>
              <div>
                <div class="metric-value" id="summaryOxygen">97</div>
                <div class="metric-unit">%</div>
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

        <div class="cards-grid">
          <section class="form-card">
            <div class="card-title">
              <div class="card-icon purple"><img src="<?= base_url('img/icon/icon_vit_120_off.png') ?>"></div>
              <h3>Presión arterial</h3>
            </div>

            <div class="field">
              <label for="evaluationDate">Fecha evaluación <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="evaluationDate" name="fecha_evaluacion" data-codigo="fecha_evaluacion" value="<?= esc($fechaEvaluacionVista) ?>" required />
                <span class="calendar">🗓️</span>
              </div>
              <div class="field-error">La fecha de evaluación es obligatoria.</div>
            </div>

            <div class="field">
              <label for="systolic">Tensión arterial sistólica <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="systolic" name="campos[tension_sistolica]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['tension_sistolica'] ?? '', '120,0000') ?>" class="campo-signo-vital" data-codigo="tension_sistolica" data-label="Tensión sistólica" data-unidad="mmHg" data-min="70" data-max="180" data-alerta-min="90" data-alerta-max="140" required />
                <span class="unit">mmHg</span>
              </div>
              <div class="hint">Rango esperado: 90 - 140 mmHg.</div>
              <div class="field-error"></div>
            </div>

            <div class="field">
              <label for="diastolic">Tensión arterial diastólica <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="diastolic" name="campos[tension_diastolica]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['tension_diastolica'] ?? '', '80,00') ?>" class="campo-signo-vital" data-codigo="tension_diastolica" data-label="Tensión diastólica" data-unidad="mmHg" data-min="40" data-max="120" data-alerta-min="60" data-alerta-max="90" required />
                <span class="unit">mmHg</span>
              </div>
              <div class="hint">Rango esperado: 60 - 90 mmHg.</div>
              <div class="field-error"></div>
            </div>
          </section>

          <section class="form-card">
            <div class="card-title">
              <div class="card-icon green"><img style="width: 35px;" src="<?= base_url("img/icon/icon_vit.png") ?>" alt=""></div>
              <h3>Frecuencias</h3>
            </div>

            <div class="field">
              <label for="heartRate">Frecuencia cardíaca <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="heartRate" name="campos[frecuencia_cardiaca]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['frecuencia_cardiaca'] ?? '', '72,0000') ?>" class="campo-signo-vital" data-codigo="frecuencia_cardiaca" data-label="Frecuencia cardíaca" data-unidad="lpm" data-min="30" data-max="220" data-alerta-min="60" data-alerta-max="100" required />
                <span class="unit">lpm</span>
              </div>
              <div class="hint">Rango esperado: 60 - 100 lpm.</div>
              <div class="field-error"></div>
            </div>

            <div class="field">
              <label for="respiratoryRate">Frecuencia respiratoria <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="respiratoryRate" name="campos[frecuencia_respiratoria]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['frecuencia_respiratoria'] ?? '', '16,0000') ?>" class="campo-signo-vital" data-codigo="frecuencia_respiratoria" data-label="Frecuencia respiratoria" data-unidad="rpm" data-min="5" data-max="60" data-alerta-min="12" data-alerta-max="20" required />
                <span class="unit">rpm</span>
              </div>
              <div class="hint">Rango esperado: 12 - 20 rpm.</div>
              <div class="field-error"></div>
            </div>
          </section>

          <section class="form-card">
            <div class="card-title">
              <div class="card-icon blue"><img style="width: 35px;" src="<?= base_url("img/icon/thermometer.png") ?>" alt=""></div>
              <h3>Temperatura y seguimiento</h3>
            </div>

            <div class="field">
              <label for="temperature">Temperatura <span class="required">*</span></label>
              <div class="input-wrap">
                <input id="temperature" name="campos[temperatura]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['temperatura'] ?? '', '37,0000') ?>" class="campo-signo-vital" data-codigo="temperatura" data-label="Temperatura" data-unidad="°C" data-min="30" data-max="45" data-alerta-min="36" data-alerta-max="37.5" required />
                <span class="unit">°C</span>
              </div>
              <div class="hint">Rango esperado: 36.0 - 37.5 °C.</div>
              <div class="field-error"></div>
            </div>

            <div class="field">
              <label for="oxygen">Saturación de oxígeno</label>
              <div class="input-wrap">
                <input id="oxygen" name="campos[saturacion_o2]" type="text" value="<?= $formatoNumeroVista($valoresExistentes['saturacion_o2'] ?? '', '97,0000') ?>" class="campo-signo-vital" data-codigo="saturacion_o2" data-label="Saturación de oxígeno" data-unidad="%" data-min="50" data-max="100" data-alerta-min="95" data-alerta-max="100" />
                <span class="unit">%</span>
              </div>
              <div class="hint">Rango esperado: 95 - 100%.</div>
              <div class="field-error"></div>
            </div>

            <div class="field">
              <label for="referral">¿Requiere remisión?</label>
              <div class="input-wrap">
                <select id="referral" name="campos[especialista_vitales]" class="campo-signo-vital" data-codigo="especialista_vitales" data-label="Remisión" data-unidad="">
                  <option value="n" <?= (($valoresExistentes['especialista_vitales'] ?? 'n') === 'n') ? 'selected' : '' ?>>No requiere remisión</option>
                  <option value="s" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 's') ? 'selected' : '' ?>>Requiere remisión</option>
                  <option value="seguimiento" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 'seguimiento') ? 'selected' : '' ?>>Seguimiento médico</option>
                </select>
              </div>
              <div class="hint">Usa esta opción si los signos vitales sugieren seguimiento médico.</div>
            </div>

            <div class="field">
              <label for="observations">Observaciones</label>
              <textarea id="observations" name="campos[observaciones_vitales]" class="campo-signo-vital" data-codigo="observaciones_vitales" data-label="Observaciones" data-unidad="" maxlength="200"><?= $valorCampo('observaciones_vitales', '') ?></textarea>
              <div class="textarea-footer">
                <span id="charCount">0</span> / 200
              </div>
            </div>
          </section>
        </div>

        <div class="actions">
          <button type="button" id="btnCancelarSignos" class="btn secondary">Cancelar</button>
          <button type="button" id="btnLimpiarSignos" class="btn soft">Limpiar</button>
          <button type="button" id="btnGuardarSignos" class="btn primary">Guardar</button>
        </div>
      </form>
    </section>
  </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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

    const campos = Array.from(document.querySelectorAll('.campo-signo-vital'));

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
      const field = input.closest('.field');
      const wrap = input.closest('.input-wrap');
      const error = field ? field.querySelector('.field-error') : null;

      input.classList.remove('is-invalid');
      if (wrap) wrap.classList.remove('is-invalid', 'is-warning');
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
        wrap.classList.add('is-warning');
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
      const wrap = fields.evaluationDate.closest('.input-wrap');
      const error = fields.evaluationDate.closest('.field').querySelector('.field-error');
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

      summary.status.className = 'status-pill';

      if (numericWithValue.length === 0) {
        summary.status.classList.add('warning');
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

      summary.status.classList.add('warning');
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
        summary.status.className = 'status-pill danger';
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
          summary.status.className = 'status-pill';
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

        summary.status.className = 'status-pill danger';
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
        summary.status.className = 'status-pill danger';
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

      const dateWrap = fields.evaluationDate.closest('.input-wrap');
      const dateError = fields.evaluationDate.closest('.field').querySelector('.field-error');
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
      if (confirm('¿Desea cancelar la evaluación? Los cambios no guardados se perderán.')) {
        window.location.href = URL_RETORNO;
      }
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

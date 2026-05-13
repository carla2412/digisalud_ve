<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
 

// ── Helpers ──
$esEdicion     = !empty($evaluacionExistente);
$evalId        = $esEdicion ? $evaluacionExistente['id_evaluacion'] : '';
$fechaHoy      = date('Y-m-d');
$fechaEval     = $esEdicion ? date('Y-m-d', strtotime($evaluacionExistente['fecha_evaluacion'])) : $fechaHoy;
$fechaEvalVista = date('d/m/Y', strtotime($fechaEval));
$observacionesEval = $esEdicion ? ($evaluacionExistente['observaciones'] ?? '') : '';

// Nombre completo
$nombreCompleto = trim(($beneficiario['nombres'] ?? '') . ' ' . ($beneficiario['apellidos'] ?? ''));

// Edad
$edadTexto = '—';
if (!empty($beneficiario['fecha_nacimiento'])) {
    $nac  = new \DateTime($beneficiario['fecha_nacimiento']);
    $diff = (new \DateTime())->diff($nac);
    $edadTexto = $diff->y . ' año' . ($diff->y != 1 ? 's' : '');
}

// Jornada
$jornadaNombre = 'Evaluación actual';
if ($jornadaId) {
    $jornadaRow = db_connect()->table('jornadas')->select('nombre_jornada')->where('id_jornada', $jornadaId)->get()->getRowArray();
    $jornadaNombre = $jornadaRow['nombre_jornada'] ?? 'Evaluación actual';
}

// URL de retorno
$urlRetorno = $jornadaId
    ? base_url("jornadas/{$jornadaId}/beneficiarios/{$beneficiario['id_beneficiario']}/evaluar")
    : base_url("centros/{$centroId}/beneficiarios");

// Icono pesquisa
$iconoPesquisa = base_url('img/visual2.svg');
$nombrePesquisa = 'Visual';

// Helper para obtener valor existente
$valorCampo = function(string $codigo, $default = '') use ($valoresExistentes) {
    return $valoresExistentes[$codigo] ?? $default;
};

// Mapeo de pesquisas evaluadas / sidebar
$pesquisasEvaluadasStr = array_map('strval', $pesquisasEvaluadas);
?>

<style>
    

    .visual-page {
      min-height: 100vh;
      background: var(--visual-gris-fondo);
    }

    .visual-topbar {
      height: 84px;
      padding: 22px 44px 12px;
      background: #ffffff;
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 24px;
    }

    .visual-title h1 {
      margin: 0;
      font-size: 28px;
      line-height: 1.1;
      font-weight: 600;
      letter-spacing: 0.3px;
      color: var(ds-dark);
    }

    .visual-title p {
      margin: 4px 0 0;
      font-size: 13.5px;
      color: var(--ds-muted);
      letter-spacing: 0.3px;
    }

    .visual-top-actions {
      display: flex;
      gap: 16px;
      align-items: center;
    }

    .visual-btn {
      border: 0;
      min-height: 38px;
      padding: 0 20px;
      border-radius: 22px;
      background: var(ds-primary);
      color: #ffffff;
      font-size: 14px;
      font-weight: 600;
      cursor: pointer;
      letter-spacing: 0.2px;
    }

    .visual-btn:hover {
      filter: brightness(0.95);
    }

    .visual-btn-light {
      background: var(ds-primary-suave);
      color: var(ds-primary);
    }

    .visual-content {
      padding: 14px 38px 26px;
    }

    .visual-grid {
      display: grid;
      grid-template-columns: 296px minmax(520px, 1fr) 338px;
      gap: 28px;
      align-items: stretch;
    }

    .visual-card {
      background:#fff;
      border: 1px solid var(--visual-gris-borde);
      border-radius: 16px;
      box-shadow: var(--visual-sombra);
    }

    .visual-card-inner {
      padding: 26px;
    }

    .visual-card h2 {
      margin: 0 0 10px;
      font-size: 20px;
      line-height: 1.2;
      color: var(ds-dark);
      letter-spacing: 0.2px;
    }

    .visual-card-subtitle {
      margin: 0 0 20px;
      color: var(--ds-muted);
      font-size: 13.5px;
      line-height: 1.4;
    }

    .visual-label {
      display: block;
      margin-bottom: 7px;
      color: #637291;
      font-size: 13px;
      font-weight: 600;
      letter-spacing: 0.3px;
    }

    .visual-input,
    .visual-select,
    .visual-textarea {
      width: 100%;
      min-height: 43px;
      padding: 10px 16px;
      border: 1px solid #cbd7ed;
      border-radius: 12px;
      outline: none;
      background: #f8faff;
      color: #21345c;
      font-size: 15px;
    }

    .visual-input:focus,
    .visual-select:focus,
    .visual-textarea:focus {
      border-color: var(ds-primary);
      box-shadow: 0 0 0 4px rgba(49, 93, 244, 0.12);
      background: #ffffff;
    }

    .visual-input-date {
      font-size: 22px;
      color: #21345c;
      letter-spacing: 0.3px;
    }

    .visual-select {
      appearance: none;
      background-image:
        linear-gradient(45deg, transparent 50%, #6d7b99 50%),
        linear-gradient(135deg, #6d7b99 50%, transparent 50%);
      background-position:
        calc(100% - 18px) 18px,
        calc(100% - 13px) 18px;
      background-size: 5px 5px, 5px 5px;
      background-repeat: no-repeat;
    }

    .visual-textarea {
      min-height: 138px;
      resize: none;
      line-height: 1.55;
    }

    .visual-block {
      margin-bottom: 24px;
    }

    .visual-block:last-child {
      margin-bottom: 0;
    }

    .visual-radio-row {
      display: flex;
      align-items: center;
      gap: 28px;
      flex-wrap: wrap;
    }

    .visual-radio,
    .visual-check {
      position: relative;
      display: inline-flex;
      align-items: center;
    }

    .visual-radio input,
    .visual-check input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    .visual-radio label,
    .visual-check label {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      cursor: pointer;
      color: #334461;
      font-size: 14.5px;
      font-weight: 600;
      user-select: none;
    }

    .visual-radio-circle {
      width: 28px;
      height: 28px;
      border-radius: 50%;
      border: 1.8px solid #9baccb;
      background: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .visual-radio-circle::after {
      content: "";
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: var(--ds-primary);
      opacity: 0;
    }

    .visual-radio input:checked + label .visual-radio-circle {
      border-color: var(ds-primary);
      background: var(ds-primary);
      box-shadow: 0 0 0 4px rgba(49, 93, 244, 0.08);
    }

    .visual-radio input:checked + label .visual-radio-circle::after {
      opacity: 1;
    }

    .visual-check-square {
      width: 23px;
      height: 23px;
      border: 1.5px solid #b9c7df;
      border-radius: 6px;
      background: #ffffff;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
    }

    .visual-check-square::after {
      content: "";
      width: 10px;
      height: 6px;
      border-left: 3px solid var(ds-primary);
      border-bottom: 3px solid var(ds-primary);
      transform: rotate(-45deg);
      margin-top: -3px;
      opacity: 0;
    }

    .visual-check input:checked + label .visual-check-square {
      border-color: #b9c7df;
      background: var(--ds-primary);
    }

    .visual-check input:checked + label .visual-check-square::after {
      opacity: 1;
    }

    .visual-left-alert {
      margin-top: 32px;
      padding: 16px 17px;
      border: 1px solid var(--ds-warning);
      border-radius: 13px;
      background:  #f5e0bf;
      color: #925b00;
    }

    .visual-left-alert strong {
      display: block;
      margin-bottom: 8px;
      font-size: 15.5px;
    }

    .visual-left-alert span {
      display: block;
      font-size: 13.5px;
      line-height: 1.55;
    }

    .visual-eyes-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 42px;
      margin-bottom: 34px;
    }

    .visual-eye-card {
      min-height: 132px;
      padding: 22px 22px 18px;
      border: 1px solid var(--ds-borde);
      border-radius: 16px;
      background: #f8faff;
      display: grid;
      grid-template-columns: 46px 1fr;
      column-gap: 18px;
      align-items: start;
    }

    .visual-eye-icon {
      width: 50px;
      height: 50px;
      border-radius: 50%;
      border: 1px solid #aecaef;
      background: #edf3ff;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 0;
    }

    .visual-eye-icon svg {
      width: 28px;
      height: 28px;
      fill: none;
      stroke: var(ds-primary);
      stroke-width: 2.1;
    }

    .visual-eye-content h3 {
      margin: 0 0 8px;
      font-size: 18px;
      color: var(ds-dark);
    }

    .visual-eye-content label {
      display: block;
      margin-bottom: 8px;
      font-size: 12.5px;
      color: var(--ds-muted);
    }

    .visual-eye-content .visual-select {
      min-height: 39px;
      font-weight: 600;
      background-color: #ffffff;
    }

    .visual-middle-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 54px;
    }

    .visual-section-title {
      margin: 0 0 6px;
      color: var(ds-dark);
      font-size: 19px;
      font-weight: 600;
      letter-spacing: 0.2px;
    }

    .visual-section-help {
      margin: 0 0 20px;
      color: var(--ds-muted);
      font-size: 13px;
      line-height: 1.45;
    }

    .visual-option-list {
      display: grid;
      gap: 16px;
      margin-top: 18px;
    }

    .visual-follow {
      margin-top: 36px;
    }

    .visual-result-badge {
      min-height: 58px;
      padding: 12px 20px;
      border-radius: 16px;
      border: 1px solid transparent;
      background: #eef2f8;
      color: #526179;
      display: flex;
      flex-direction: column;
      align-items: flex-start;
      justify-content: center;
      line-height: 1.15;
      width: 100%;
    }

    .visual-result-badge strong {
      font-size: 20px;
      font-weight: 600;
      letter-spacing: 0.2px;
    }

    .visual-result-badge span {
      margin-top: 3px;
      font-size: 13px;
    }

    .visual-result-red {
      background: var(--ds-danger);
      border-color: #ffa79f;
      color: var(--visual-rojo);
    }

    .visual-result-orange {
      background: #fff4e9;
      border-color: #ffc58c;
      color: #c45a00;
    }

    .visual-result-yellow {
      background: #fff8df;
      border-color: #f5d577;
      color: #8a6500;
    }

    .visual-result-green {
      background: #eafaf2;
      border-color: #9bdcbf;
      color:var(--ds-success);
    }

    .visual-result-gray {
      background: #eef2f8;
      border-color: #d8e1f1;
      color: #526179;
    }

    .visual-side-space {
      margin-top: 31px;
    }

    .visual-side-actions {
      margin-top: 30px;
      display: grid;
      gap: 14px;
    }

    .visual-save-btn {
      width: 100%;
      min-height: 39px;
      border-radius: 12px;
      border: 0;
      background: var(--ds-primary);
      color: #ffffff;
      font-size: 15px;
      font-weight: 600;
      cursor: pointer;
      letter-spacing: 0.2px;
    }

    .visual-save-btn:hover {
       transform: translateY(-1px);
        box-shadow: 0 10px 22px rgba(15, 23, 42, 0.1);
          background: var(--ds-primary-dark);
    }
 
    .visual-footer-note {
      width: calc(100% - 350px);
      margin: 16px 22px 0 auto;
      padding: 11px 22px;
      border: 1px solid var(--ds-borde);
      border-radius: 15px;
      background: #ffffff;
      color: #3b4d6f;
      font-size: 13.5px;
      line-height: 1.45;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .visual-hidden {
      display: none;
    }

    .visual-error {
      display: none;
      margin-top: 18px;
      padding: 13px 15px;
      border-radius: 12px;
      border: 1px solid #ffc0ba;
      background: #fff2f0;
      color:var(--ds-danger);
      font-size: 13px;
      font-weight: 600;
    }

    .visual-error-show {
      display: block;
    }

    .visual-beneficiario-card {
      margin: 20px 20px 0;
      min-height: 100px;
      padding: 22px 34px;
      border: 1px solid var(--ds-borde);
      border-radius: 24px;
      background: #ffffff;
      box-shadow: 0 10px 28px rgba(28, 42, 76, 0.10);
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 18px;
    }

    .visual-beneficiario-info {
      display: flex;
      align-items: center;
      gap: 16px;
    }

    .visual-beneficiario-icon {
      width: 46px;
      height: 46px;
      border-radius: 18px;
      background: #f7faff;
      border: 1px solid var(--ds-borde);
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 8px 18px rgba(28, 42, 76, 0.08);
    }

    .visual-beneficiario-icon img {
      width: 42px;
      height: 42px;
    }

    .visual-beneficiario-texto h2 {
      margin: 0;
      color: var(ds-dark);
      font-size: 26px;
      font-weight: 600;
      line-height: 1.1;
    }

    .visual-beneficiario-texto p {
      margin: 5px 0 8px;
      color: var(--ds-muted);
      font-size: 14px;
      font-weight: 600;
    }

    .visual-beneficiario-badge {
      display: inline-flex;
      align-items: center;
      min-height: 22px;
      padding: 4px 13px;
      border-radius: 999px;
      background: #ddecff;
      color: var(ds-primary);
      font-size: 12px;
      font-weight: 600;
    }

    .visual-beneficiario-badge.visual-edit-badge {
      background: #fff3cd;
      color: #856404;
    }

    .visual-beneficiario-detalle {
      display: flex;
      align-items: center;
      justify-content: flex-end;
      gap: 18px;
      flex-wrap: wrap;
    }

    .visual-beneficiario-detalle div {
      min-width: 130px;
      padding: 12px 16px;
      border-radius: 16px;
      background: #f8faff;
      border: 1px solid #edf2fb;
    }

    .visual-beneficiario-detalle span {
      display: block;
      margin-bottom: 4px;
      color: var(--ds-muted);
      font-size: 11px;
      font-weight: 600;
      text-transform: uppercase;
      letter-spacing: 0.04em;
    }

    .visual-beneficiario-detalle strong {
      color: var(ds-dark);
      font-size: 14px;
      font-weight: 600;
    }

    @media (max-width: 900px) {
      .visual-beneficiario-card {
        flex-direction: column;
        align-items: flex-start;
        padding: 22px;
      }

      .visual-beneficiario-detalle {
        width: 100%;
        justify-content: flex-start;
      }

      .visual-beneficiario-detalle div {
        flex: 1 1 160px;
      }
    }

    @media (max-width: 1180px) {
      .visual-grid {
        grid-template-columns: 1fr;
      }

      .visual-footer-note {
        width: 100%;
        margin: 18px 0 0;
        white-space: normal;
      }
    }

    @media (max-width: 760px) {
      .visual-topbar {
        height: auto;
        padding: 22px;
        flex-direction: column;
      }

      .visual-content {
        padding: 16px;
      }

      .visual-eyes-grid,
      .visual-middle-grid {
        grid-template-columns: 1fr;
        gap: 22px;
      }

      .visual-card-inner {
        padding: 22px;
      }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="visual-page">

  <!-- ── Barra beneficiario ── -->
  <section class="visual-beneficiario-card">
    <div class="visual-beneficiario-info">
      <div class="visual-beneficiario-icon">
        <img src="<?= esc($iconoPesquisa) ?>" alt="<?= esc($nombrePesquisa) ?>">
      </div>

      <div class="visual-beneficiario-texto">
        <h2><?= esc($nombrePesquisa) ?></h2>
        <p><?= esc($nombreCompleto) ?></p>

        <span class="visual-beneficiario-badge <?= $esEdicion ? 'visual-edit-badge' : '' ?>">
          <?= $esEdicion ? 'Editando evaluación' : 'Nueva evaluación' ?>
        </span>
      </div>
    </div>

    <div class="visual-beneficiario-detalle">
      <div>
        <span>Edad</span>
        <strong><?= esc($edadTexto) ?></strong>
      </div>

      <div>
        <span>Jornada</span>
        <strong><?= esc($jornadaNombre) ?></strong>
      </div>
    </div>
  </section>

  <!-- ── Formulario ── -->
  <form id="visual-form" novalidate>
    <input type="hidden" name="beneficiario_id" value="<?= (int) $beneficiario['id_beneficiario'] ?>">
    <input type="hidden" name="tipo_pesquisa_id" value="<?= (int) $tipoPesquisaId ?>">
    <input type="hidden" name="jornada_id" value="<?= (int) $jornadaId ?>">
    <input type="hidden" name="centro_id" value="<?= (int) $centroId ?>">
    <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">

    <section class="visual-content">
      <div class="visual-grid">

        <!-- ═══ COLUMNA IZQUIERDA: Datos de evaluación ═══ -->
        <aside class="visual-card">
          <div class="visual-card-inner">
            <h2>Datos de evaluación</h2>

            <div class="visual-block">
              <label class="visual-label" for="visual-fecha-evaluacion">Fecha</label>
              <input
                type="date"
                id="visual-fecha-evaluacion"
                name="fecha_evaluacion"
                class="visual-input visual-input-date"
                value="<?= esc($fechaEvalVista) ?>"
              />
            </div>

            <div class="visual-block">
              <span class="visual-label">Paciente usa lentes</span>
              <div class="visual-radio-row">
                <div class="visual-radio">
                  <input type="radio" id="visual-lentes-si" name="campos[usa_lentes]" value="s"
                    <?= ($valorCampo('usa_lentes', '') === 's') ? 'checked' : '' ?> />
                  <label for="visual-lentes-si">
                    <span class="visual-radio-circle"></span>
                    Sí
                  </label>
                </div>
                <div class="visual-radio">
                  <input type="radio" id="visual-lentes-no" name="campos[usa_lentes]" value="n"
                    <?= ($valorCampo('usa_lentes', '') === 'n') ? 'checked' : '' ?> />
                  <label for="visual-lentes-no">
                    <span class="visual-radio-circle"></span>
                    No
                  </label>
                </div>
              </div>
            </div>

            <div class="visual-block">
              <label class="visual-label" for="distancia_snellen">Distancia del paciente</label>
              <select id="distancia_snellen" name="campos[distancia_examen]" class="visual-select">
                <option value="">Seleccionar</option>
                <option value="6" <?= ($valorCampo('distancia_examen', '') === '6') ? 'selected' : '' ?>>6 mts / 20 pies</option>
                <option value="3" <?= ($valorCampo('distancia_examen', '') === '3') ? 'selected' : '' ?>>3 mts / 10 pies</option>
              </select>
            </div>

           

            <div id="visual-alerta-validacion" class="visual-error">
              Debe completar los campos obligatorios antes de guardar.
            </div>
          </div>
        </aside>

        <!-- ═══ COLUMNA CENTRAL: Registro agudeza visual ═══ -->
        <section class="visual-card">
          <div class="visual-card-inner">
            <h2>Registro de agudeza visual</h2>
            <p class="visual-card-subtitle">
              Seleccione la línea reconocida por cada ojo. El sistema calcula la clasificación.
            </p>

            <div class="visual-eyes-grid">
              <!-- Ojo izquierdo -->
              <div class="visual-eye-card">
                <div class="visual-eye-icon" aria-hidden="true">
                  <svg viewBox="0 0 32 32">
                      <img src="<?= esc($iconoPesquisa) ?>"<?= esc($nombrePesquisa) ?>">
                  
                  </svg>
                </div>
                <div class="visual-eye-content">
                  <h3>Ojo izquierdo</h3>
                  <label for="linea_ojo_izq_snellen">Línea reconocida</label>
                  <select id="linea_ojo_izq_snellen" name="campos[linea_ojo_izquierdo]" class="visual-select">
                    <option value="">Seleccionar</option>
                    <option value="200" <?= ($valorCampo('linea_ojo_izquierdo', '') === '200') ? 'selected' : '' ?>>20/200</option>
                    <option value="100" <?= ($valorCampo('linea_ojo_izquierdo', '') === '100') ? 'selected' : '' ?>>20/100</option>
                    <option value="70" <?= ($valorCampo('linea_ojo_izquierdo', '') === '70') ? 'selected' : '' ?>>20/70</option>
                    <option value="50" <?= ($valorCampo('linea_ojo_izquierdo', '') === '50') ? 'selected' : '' ?>>20/50</option>
                    <option value="40" <?= ($valorCampo('linea_ojo_izquierdo', '') === '40') ? 'selected' : '' ?>>20/40</option>
                    <option value="30" <?= ($valorCampo('linea_ojo_izquierdo', '') === '30') ? 'selected' : '' ?>>20/30</option>
                    <option value="25" <?= ($valorCampo('linea_ojo_izquierdo', '') === '25') ? 'selected' : '' ?>>20/25</option>
                    <option value="20" <?= ($valorCampo('linea_ojo_izquierdo', '') === '20') ? 'selected' : '' ?>>20/20</option>
                    <option value="15" <?= ($valorCampo('linea_ojo_izquierdo', '') === '15') ? 'selected' : '' ?>>20/15</option>
                    <option value="13" <?= ($valorCampo('linea_ojo_izquierdo', '') === '13') ? 'selected' : '' ?>>20/13</option>
                    <option value="10" <?= ($valorCampo('linea_ojo_izquierdo', '') === '10') ? 'selected' : '' ?>>20/10</option>
                  </select>
                </div>
              </div>

              <!-- Ojo derecho -->
              <div class="visual-eye-card">
                <div class="visual-eye-icon" aria-hidden="true">
                  <svg viewBox="0 0 32 32">
                    <path d="M3 16s5-8 13-8 13 8 13 8-5 8-13 8S3 16 3 16Z">  <img src="<?= esc($iconoPesquisa) ?>"<?= esc($nombrePesquisa) ?>"></path>
                    <circle cx="16" cy="16" r="4"></circle>
                  </svg>
                </div>
                <div class="visual-eye-content">
                  <h3>Ojo derecho</h3>
                  <label for="linea_ojo_der_snellen">Línea reconocida</label>
                  <select id="linea_ojo_der_snellen" name="campos[linea_ojo_derecho]" class="visual-select">
                    <option value="">Seleccionar</option>
                    <option value="200" <?= ($valorCampo('linea_ojo_derecho', '') === '200') ? 'selected' : '' ?>>20/200</option>
                    <option value="100" <?= ($valorCampo('linea_ojo_derecho', '') === '100') ? 'selected' : '' ?>>20/100</option>
                    <option value="70" <?= ($valorCampo('linea_ojo_derecho', '') === '70') ? 'selected' : '' ?>>20/70</option>
                    <option value="50" <?= ($valorCampo('linea_ojo_derecho', '') === '50') ? 'selected' : '' ?>>20/50</option>
                    <option value="40" <?= ($valorCampo('linea_ojo_derecho', '') === '40') ? 'selected' : '' ?>>20/40</option>
                    <option value="30" <?= ($valorCampo('linea_ojo_derecho', '') === '30') ? 'selected' : '' ?>>20/30</option>
                    <option value="25" <?= ($valorCampo('linea_ojo_derecho', '') === '25') ? 'selected' : '' ?>>20/25</option>
                    <option value="20" <?= ($valorCampo('linea_ojo_derecho', '') === '20') ? 'selected' : '' ?>>20/20</option>
                    <option value="15" <?= ($valorCampo('linea_ojo_derecho', '') === '15') ? 'selected' : '' ?>>20/15</option>
                    <option value="13" <?= ($valorCampo('linea_ojo_derecho', '') === '13') ? 'selected' : '' ?>>20/13</option>
                    <option value="10" <?= ($valorCampo('linea_ojo_derecho', '') === '10') ? 'selected' : '' ?>>20/10</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="visual-middle-grid">
              <!-- Agujero estenopeico -->
              <div>
                <h3 class="visual-section-title">Agujero estenopeico</h3>
                <p class="visual-section-help">
                  Permite diferenciar posible error refractivo.
                </p>

                <div class="visual-radio-row">
                  <div class="visual-radio">
                    <input type="radio" id="visual-agujero-si" name="campos[utiliza_agujero_estenopeico]" value="s"
                      <?= ($valorCampo('utiliza_agujero_estenopeico', '') === 's') ? 'checked' : '' ?> />
                    <label for="visual-agujero-si">
                      <span class="visual-radio-circle"></span>
                      Se utilizó
                    </label>
                  </div>
                  <div class="visual-radio">
                    <input type="radio" id="visual-agujero-no" name="campos[utiliza_agujero_estenopeico]" value="n"
                      <?= ($valorCampo('utiliza_agujero_estenopeico', '') === 'n') ? 'checked' : '' ?> />
                    <label for="visual-agujero-no">
                      <span class="visual-radio-circle"></span>
                      No se utilizó
                    </label>
                  </div>
                </div>

                <div class="visual-option-list <?= ($valorCampo('utiliza_agujero_estenopeico', '') !== 's') ? 'visual-hidden' : '' ?>" id="visual-agujero-opciones">
                  <div class="visual-check">
                    <input type="radio" id="visual-mejora-si" name="campos[mejora_agujero]" value="s"
                      <?= ($valorCampo('mejora_agujero', '') === 's') ? 'checked' : '' ?> />
                    <label for="visual-mejora-si">
                      <span class="visual-check-square"></span>
                      Mejora con agujero estenopeico
                    </label>
                  </div>
                  <div class="visual-check">
                    <input type="radio" id="visual-mejora-no" name="campos[mejora_agujero]" value="n"
                      <?= ($valorCampo('mejora_agujero', '') === 'n') ? 'checked' : '' ?> />
                    <label for="visual-mejora-no">
                      <span class="visual-check-square"></span>
                      No mejora con agujero estenopeico
                    </label>
                  </div>
                </div>
              </div>

              <!-- Diagnósticos presuntivos + Seguimiento -->
              <div>
                <h3 class="visual-section-title">Diagnósticos presuntivos</h3>

                <div class="visual-option-list">
                  <div class="visual-check">
                    <input type="checkbox" id="visual-estrabismo" name="campos[estrabismo]" value="s"
                      <?= ($valorCampo('estrabismo', '') === 's') ? 'checked' : '' ?> />
                    <label for="visual-estrabismo">
                      <span class="visual-check-square"></span>
                      Estrabismo
                    </label>
                  </div>
                </div>

                <div class="visual-follow">
                  <h3 class="visual-section-title">¿Requiere seguimiento?</h3>
                  <div class="visual-radio-row">
                    <div class="visual-radio">
                      <input type="radio" id="visual-seguimiento-si" name="campos[remitir_especialista_visual]" value="s"
                        <?= ($valorCampo('remitir_especialista_visual', '') === 's') ? 'checked' : '' ?> />
                      <label for="visual-seguimiento-si">
                        <span class="visual-radio-circle"></span>
                        Sí
                      </label>
                    </div>
                    <div class="visual-radio">
                      <input type="radio" id="visual-seguimiento-no" name="campos[remitir_especialista_visual]" value="n"
                        <?= ($valorCampo('remitir_especialista_visual', 'n') === 'n') ? 'checked' : '' ?> />
                      <label for="visual-seguimiento-no">
                        <span class="visual-radio-circle"></span>
                        No
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </section>

        <!-- ═══ COLUMNA DERECHA: Resultado y acción ═══ -->
        <aside class="visual-card">
          <div class="visual-card-inner">
            <h2>Resultado y acción</h2>

            <div id="visual-resultado-badge" class="visual-result-badge visual-result-gray">
              <strong>Esperando valor</strong>
              <span>Complete los datos de agudeza visual</span>
            </div>

            <div class="visual-side-space">
              <span class="visual-label">Remitir a especialista</span>
            </div>

            <div class="visual-side-space">
              <label class="visual-label" for="visual-observaciones">Observaciones</label>
              <textarea
                id="visual-observaciones"
                name="campos[observaciones_visual]"
                class="visual-textarea"
                maxlength="500"
                placeholder="Ej.: dificultad para reconocer la primera línea en ambos ojos."
              ><?= esc($valorCampo('observaciones_visual', '')) ?></textarea>
            </div>

            <div class="visual-side-space">
              <span class="visual-label">Acciones sugeridas</span>

              <div class="visual-side-actions">
                <div class="visual-check">
                  <input type="checkbox" id="visual-remitir-especialista" name="visual_accion_remitir" value="s" />
                  <label for="visual-remitir-especialista">
                    <span class="visual-check-square"></span>
                    Generar remisión
                  </label>
                </div>

                <div class="visual-check">
                  <input type="checkbox" id="visual-programar-seguimiento" name="visual_accion_seguimiento" value="s" />
                  <label for="visual-programar-seguimiento">
                    <span class="visual-check-square"></span>
                    Programar seguimiento
                  </label>
                </div>

                <button type="button" class="visual-save-btn" id="visual-guardar">
                  <?= $esEdicion ? 'Actualizar evaluación' : 'Guardar evaluación' ?>
                </button>
              </div>
            </div>
          </div>
        </aside>

      </div>
    </section>
  </form>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
(function() {
  'use strict';

  var URL_GUARDAR  = '<?= base_url("evaluaciones/guardar") ?>';
  var URL_RETORNO  = '<?= esc($urlRetorno, 'js') ?>';
  var CSRF_TOKEN   = '<?= csrf_token() ?>';
  var CSRF_HASH    = '<?= csrf_hash() ?>';
  var ES_EDICION   = <?= $esEdicion ? 'true' : 'false' ?>;

  // ── Elementos ──
  var elDistancia     = document.getElementById('distancia_snellen');
  var elOjoIzq        = document.getElementById('linea_ojo_izq_snellen');
  var elOjoDer        = document.getElementById('linea_ojo_der_snellen');
  var elResultado     = document.getElementById('visual-resultado-badge');
  var elResumen       = document.getElementById('visual-resumen-automatico');
  var elAlerta        = document.getElementById('visual-alerta-validacion');
  var elAgujeroSi     = document.getElementById('visual-agujero-si');
  var elAgujeroNo     = document.getElementById('visual-agujero-no');
  var elAgujeroOpc    = document.getElementById('visual-agujero-opciones');
  var elRemitir       = document.getElementById('visual-remitir-especialista');
  var elSeguimiento   = document.getElementById('visual-programar-seguimiento');
  var btnGuardar      = document.getElementById('visual-guardar');

  // ── Eventos ──
  elDistancia.addEventListener('change', calcularAgudeza);
  elOjoIzq.addEventListener('change', calcularAgudeza);
  elOjoDer.addEventListener('change', calcularAgudeza);
  elAgujeroSi.addEventListener('change', toggleAgujero);
  elAgujeroNo.addEventListener('change', toggleAgujero);
  btnGuardar.addEventListener('click', guardarEvaluacion);

  // ── Calcular al cargar si hay valores existentes ──
  calcularAgudeza();

  // ═══ CÁLCULO AGUDEZA VISUAL SNELLEN ═══
  function calcularAgudeza() {
    var distanciaMap = { '6': 20, '3': 10 };
    var distanciaVal = elDistancia.value;
    var distanciaPies = distanciaMap[distanciaVal] || 0;

    var ojoIzqVal = parseFloat(elOjoIzq.value);
    var ojoDerVal = parseFloat(elOjoDer.value);

    if (!distanciaPies || !ojoIzqVal || !ojoDerVal) {
      actualizarResultado('Esperando valor', 'Complete los datos de agudeza visual', 'gray');
      actualizarResumen(false, 'Complete la distancia y línea reconocida.');
      return;
    }

    // Tomar el peor ojo (valor de línea más alto = peor visión)
    var peorOjo = Math.max(ojoIzqVal, ojoDerVal);
    var agudeza = distanciaPies / peorOjo;

    var nivel = '';
    var accion = '';
    var estado = 'gray';

    if (agudeza <= 2 && agudeza >= (20 / 15)) {
      nivel = 'Sobre Normal';
      accion = 'Sin remisión automática';
      estado = 'green';
    } else if (agudeza < (20 / 15) && agudeza >= 1) {
      nivel = 'Normal';
      accion = 'Sin remisión automática';
      estado = 'green';
    } else if (agudeza < 1 && agudeza >= (20 / 30)) {
      nivel = 'Deficiencia visual leve';
      accion = 'Seguimiento según criterio clínico';
      estado = 'yellow';
    } else if (agudeza < (20 / 30) && agudeza >= 0.4) {
      nivel = 'Deficiencia visual moderada';
      accion = 'Considerar remisión';
      estado = 'orange';
    } else if (agudeza < 0.4 && agudeza >= 0.1) {
      nivel = 'Deficiencia visual alta';
      accion = 'Remitir a especialista';
      estado = 'red';
    } else if (agudeza < 0.1) {
      nivel = 'Ceguera';
      accion = 'Remisión prioritaria';
      estado = 'red';
    }

    actualizarResultado(nivel, accion, estado);
    aplicarAccionesSugeridas(estado);
    actualizarResumen(
      estado === 'red' || estado === 'orange',
      accion
    );

    // Actualizar campo calculado agudeza_visual_nivel
    var nivelMap = {
      'green': 'normal',
      'yellow': 'leve',
      'orange': 'moderada',
      'red': 'severa'
    };
    var inputNivel = document.getElementById('visual-nivel-calculado');
    if (inputNivel) {
      inputNivel.value = nivelMap[estado] || '';
    }
  }

  function actualizarResultado(texto, accion, estado) {
    elResultado.className = 'visual-result-badge visual-result-' + estado;
    elResultado.innerHTML = '<strong>' + texto + '</strong><span>' + accion + '</span>';
  }

  function aplicarAccionesSugeridas(estado) {
    if (estado === 'red' || estado === 'orange') {
      elRemitir.checked = true;
      elSeguimiento.checked = true;
      return;
    }
    if (estado === 'yellow') {
      elRemitir.checked = false;
      elSeguimiento.checked = true;
      return;
    }
    elRemitir.checked = false;
    elSeguimiento.checked = false;
  }

  function actualizarResumen(mostrarAlerta, mensaje) {
    if (mostrarAlerta) {
      elResumen.innerHTML =
        '<strong>Alerta: baja agudeza visual</strong>' +
        '<span>' + mensaje + ' y seguimiento recomendado.</span>';
      return;
    }
    elResumen.innerHTML =
      '<strong>Resumen automático</strong>' +
      '<span>' + mensaje + '</span>';
  }

  function toggleAgujero() {
    var usaAgujero = document.querySelector('input[name="campos[utiliza_agujero_estenopeico]"]:checked');

    if (usaAgujero && usaAgujero.value === 's') {
      elAgujeroOpc.classList.remove('visual-hidden');
      return;
    }

    elAgujeroOpc.classList.add('visual-hidden');
    document.getElementById('visual-mejora-si').checked = false;
    document.getElementById('visual-mejora-no').checked = false;
  }

  // ═══ VALIDACIÓN ═══
  function validarFormulario() {
    var usaLentes   = document.querySelector('input[name="campos[usa_lentes]"]:checked');
    var distancia   = elDistancia.value;
    var ojoIzq      = elOjoIzq.value;
    var ojoDer      = elOjoDer.value;
    var usaAgujero  = document.querySelector('input[name="campos[utiliza_agujero_estenopeico]"]:checked');
    var mejoraAguj  = document.querySelector('input[name="campos[mejora_agujero]"]:checked');
    var seguimiento = document.querySelector('input[name="campos[remitir_especialista_visual]"]:checked');

    var valido = true;

    if (!usaLentes) valido = false;
    if (!distancia) valido = false;
    if (!ojoIzq) valido = false;
    if (!ojoDer) valido = false;
    if (!usaAgujero) valido = false;
    if (usaAgujero && usaAgujero.value === 's' && !mejoraAguj) valido = false;
    if (!seguimiento) valido = false;

    if (!valido) {
      elAlerta.classList.add('visual-error-show');
      return false;
    }

    elAlerta.classList.remove('visual-error-show');
    return true;
  }

  // ═══ GUARDAR ═══
  function guardarEvaluacion() {
    if (!validarFormulario()) return;

    btnGuardar.disabled = true;
    btnGuardar.textContent = 'Guardando...';

    var form = document.getElementById('visual-form');
    var formData = new FormData(form);
    formData.append(CSRF_TOKEN, CSRF_HASH);

    // Agregar campo calculado nivel
    var nivelInput = document.getElementById('visual-nivel-calculado');
    if (nivelInput && nivelInput.value) {
      formData.append('campos[agudeza_visual_nivel]', nivelInput.value);
    }

    // Método visual fijo (Snellen)
    formData.append('campos[metodo_visual]', 'snellen');

    // Checkbox estrabismo: enviar 'n' si no está marcado
    var estrabismoCheck = document.getElementById('visual-estrabismo');
    if (!estrabismoCheck.checked) {
      formData.set('campos[estrabismo]', 'n');
    }

    // Observaciones generales → campo observaciones de pesquisa_evaluaciones
    var obsVisual = document.getElementById('visual-observaciones');
    formData.append('observaciones', obsVisual ? obsVisual.value : '');

    fetch(URL_GUARDAR, {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: formData
    })
    .then(function(resp) { return resp.json(); })
    .then(function(data) {
      if (data.ok) {
        Swal.fire({
          icon: 'success',
          title: '¡Evaluación guardada!',
          text: data.mensaje || 'Evaluación visual guardada correctamente.',
          confirmButtonColor: '#315df4',
          timer: 1800,
          showConfirmButton: false
        }).then(function() {
          window.location.href = data.url_retorno || URL_RETORNO;
        });
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: data.mensaje || 'No se pudo guardar la evaluación.',
          confirmButtonColor: '#315df4'
        });
      }
    })
    .catch(function(err) {
      console.error('Error:', err);
      Swal.fire({
        icon: 'error',
        title: 'Error de conexión',
        text: 'No se pudo conectar con el servidor.',
        confirmButtonColor: '#315df4'
      });
    })
    .finally(function() {
      btnGuardar.disabled = false;
      btnGuardar.textContent = ES_EDICION ? 'Actualizar evaluación' : 'Guardar evaluación';
    });
  }

})();
</script>

<!-- Campo oculto para nivel calculado -->
<input type="hidden" id="visual-nivel-calculado" value="<?= esc($valorCampo('agudeza_visual_nivel', '')) ?>">

<?= $this->endSection() ?>
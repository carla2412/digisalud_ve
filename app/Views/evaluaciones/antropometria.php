<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));

$infoPesquisaActual = $infoPesquisas[$tipoPesquisaId] ?? [];
$nombrePesquisa = $infoPesquisaActual['nombre'] ?? ($tipoPesquisa['descripcion_view'] ?? $tipoPesquisa['nombre_tipo'] ?? 'Antropometría');
$iconoPesquisa = $infoPesquisaActual['img'] ?? 'antropometria2.svg';

$esEdicion = ! empty($evaluacionExistente);
$evalId = $evaluacionExistente['id_evaluacion'] ?? '';
$obsExistente = $evaluacionExistente['observaciones'] ?? '';

$fechaEvaluacionRaw = $evaluacionExistente['fecha_evaluacion'] ?? date('Y-m-d');
$fechaEvaluacionIso = ! empty($fechaEvaluacionRaw) ? date('Y-m-d', strtotime($fechaEvaluacionRaw)) : date('Y-m-d');

$urlRetorno = $jornadaId
  ? base_url("jornadas/{$jornadaId}/beneficiarios")
  : base_url("centros/{$centroId}/beneficiarios");

$valorCampo = static function (string $codigo, $default = '') use ($valoresExistentes) {
  return esc($valoresExistentes[$codigo] ?? $default);
};

$sexoRaw = strtoupper((string)($beneficiario['sexo'] ?? $beneficiario['genero'] ?? $beneficiario['sexo_biologico'] ?? ''));
$sexoAntro = str_starts_with($sexoRaw, 'F') || str_contains($sexoRaw, 'MUJ') ? 'F' : 'M';

$fechaNacimiento = $beneficiario['fecha_nacimiento'] ?? '';
$zscoreManifest = $zscoreManifest ?? [];

$fechaNacimientoBenef = $beneficiario['fecha_nacimiento'] ?? null;

$sexoVista = $sexoAntro === 'F' ? 'femenino' : 'masculino';

$calcularEdadTexto = static function (?string $fechaNacimiento, ?string $fechaReferencia = null): string {
  if (empty($fechaNacimiento)) {
    return 'Edad no registrada';
  }

  try {
    $nacimiento = new DateTimeImmutable($fechaNacimiento);
    $referencia = new DateTimeImmutable($fechaReferencia ?: date('Y-m-d'));

    if ($referencia < $nacimiento) {
      return 'Edad no válida';
    }

    $diff = $nacimiento->diff($referencia);

    return $diff->y . ' ' . ($diff->y === 1 ? 'año' : 'años')
      . ' | ' . $diff->m . ' ' . ($diff->m === 1 ? 'mes' : 'meses')
      . ' | ' . $diff->d . ' ' . ($diff->d === 1 ? 'día' : 'días');
  } catch (Throwable $e) {
    return 'Edad no válida';
  }
};

$edadTextoInicial = $calcularEdadTexto($fechaNacimientoBenef, $fechaEvaluacionIso ?? date('Y-m-d'));
?>

<style>
  :root {
    --antro-primary: #2f8df0;
    --antro-primary-dark: #0b63ce;
    --antro-dark: #071761;
    --antro-bg: #f4f8fd;
    --antro-card: #ffffff;
    --antro-soft: #eaf4ff;
    --antro-border: #cfe0fb;
    --antro-border-2: #d7e7ff;
    --antro-muted: #6b7280;
    --antro-text: #152238;
    --antro-warning-bg: #fff7ed;
    --antro-warning-border: #ffd4a8;
    --antro-warning-text: #8a3d00;
    --antro-danger: #ef4444;
    --antro-success: #16a34a;
    --antro-shadow: 0 16px 40px rgba(7, 23, 97, 0.08);
  }

  * {
    box-sizing: border-box;
  }



  .antro-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    background: var(--antro-card);
    border: 1px solid var(--antro-border);
    border-radius: 30px;
    padding: 26px 34px;
    box-shadow: var(--antro-shadow);
    margin-bottom: 18px;
  }

  .antro-header-left {
    display: flex;
    align-items: center;
    gap: 18px;
    min-width: 0;
  }

  .antro-icon {
    width: 52px;
    height: 52px;
    border-radius: 50%;
    background: #ffc107;
    display: flex;
    align-items: center;
    justify-content: center;
    flex: 0 0 auto;
  }

  .antro-icon img {
    width: 32px;
    height: 32px;
    object-fit: contain;
  }

  .antro-title {
    margin: 0;
    color: var(--antro-dark);
    font-size: 30px;
    line-height: 1.1;
    font-weight: 800;
  }

  .antro-subtitle {
    margin: 8px 0 10px;
    color: #374151;
    font-size: 16px;
  }

  .antro-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 26px;
    padding: 4px 14px;
    border-radius: 999px;
    background: var(--antro-soft);
    color: var(--antro-primary-dark);
    font-weight: 800;
    font-size: 13px;
  }

  .antro-chip-edit {
    background: #fef3c7;
    color: #92400e;
  }

  .antro-header-right {
    display: flex;
    gap: 18px;
    align-items: center;
    flex-wrap: wrap;
    justify-content: flex-end;
  }

  .antro-mini-card {
    min-width: 220px;
    background: #f9fbff;
    border: 1px solid var(--antro-border-2);
    border-radius: 20px;
    padding: 16px 18px;
  }

  .antro-mini-card label {
    display: block;
    color: var(--antro-dark);
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 10px;
  }

  .antro-date-input,
  .antro-input,
  .antro-input-wrap input,
  .antro-input-wrap select,
  .antro-textarea {
    width: 100%;
    border: 1px solid var(--antro-border);
    border-radius: 14px;
    background: #fff;
    color: #111827;
    font-size: 15px;
    outline: 0;
  }

  .antro-date-input,
  .antro-input {
    min-height: 44px;
    padding: 0 14px;
  }

  .antro-tags {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
  }

  .antro-tag {
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 800;
    background: var(--antro-soft);
    color: var(--antro-primary-dark);
  }

  .antro-tag-warning {
    background: #fff1e6;
    color: #c15b00;
  }




  .antro-layout {
    display: grid;
    grid-template-columns: 240px minmax(0, 1fr) 340px;
    gap: 24px;
    align-items: stretch;
  }

  .antro-form-card {
    min-height: 100%;
  }

  .antro-steps {
    display: flex;
    flex-direction: column;
    gap: 18px;
    position: sticky;
    top: 20px;
  }

  .antro-step {
    width: 100%;
    min-height: 58px;
    display: flex;
    align-items: center;
    gap: 14px;
    border: 1px solid var(--antro-border-2);
    border-radius: 999px;
    background: #fff;
    padding: 10px 16px;
    cursor: pointer;
    color: #334155;
    font-weight: 800;
    text-align: left;
    transition: .2s ease;
  }

  .antro-step:hover {
    border-color: var(--antro-primary);
    background: #f8fbff;
  }

  .antro-step-active {
    border: 2px solid var(--antro-primary);
    background: var(--antro-soft);
    color: var(--antro-primary-dark);
  }

  .antro-step-number {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: #eef3f9;
    color: var(--antro-muted);
    flex: 0 0 auto;
  }

  .antro-step-active .antro-step-number {
    background: var(--antro-primary);
    color: #fff;
  }

  .antro-card {
    background: var(--antro-card);
    border: 1px solid var(--antro-border);
    border-radius: 26px;
    box-shadow: var(--antro-shadow);
    padding: 30px;
  }

  .antro-section {
    display: none;
  }

  .antro-section-active {
    display: block;
  }

  .antro-section-title {
    margin: 0;
    color: var(--antro-dark);
    font-size: 22px;
    font-weight: 800;
  }

  .antro-section-help {
    margin: 8px 0 22px;
    color: #4b5563;
    font-size: 15px;
  }

  .antro-alert-box {
    border: 1px solid var(--antro-warning-border);
    background: var(--antro-warning-bg);
    color: var(--antro-warning-text);
    border-radius: 18px;
    padding: 18px 20px;
    margin-bottom: 24px;
    line-height: 1.35;
  }

  .antro-alert-box strong {
    display: block;
    margin-bottom: 6px;
    font-size: 16px;
  }

  .antro-alert-info {
    background: #f0f7ff;
    border-color: #b7d8ff;
    color: #123f80;
  }

  .antro-alert-danger {
    background: #fef2f2;
    border-color: #fecaca;
    color: #991b1b;
  }

  .antro-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 18px;
  }

  .antro-grid-2 {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 18px;
  }

  .antro-field {
    border: 1px solid var(--antro-border-2);
    background: #fdfefe;
    border-radius: 18px;
    padding: 16px;
  }

  .antro-field-full {
    grid-column: 1 / -1;
  }

  .antro-field label,
  .antro-radio-box-label {
    display: block;
    color: var(--antro-dark);
    font-size: 14px;
    font-weight: 800;
    margin-bottom: 10px;
  }

  .antro-required {
    color: var(--antro-danger);
    font-weight: 900;
  }

  .antro-input-wrap {
    display: flex;
    align-items: center;
    min-height: 48px;
    border: 1px solid var(--antro-border);
    border-radius: 14px;
    background: #fff;
    overflow: hidden;
  }

  .antro-input-wrap input,
  .antro-input-wrap select {
    width: 100%;
    min-height: 48px;
    border: 0;
    outline: 0;
    background: transparent;
    padding: 0 14px;
    font-size: 16px;
  }

  .antro-input-wrap input[readonly] {
    background: #fbfdff;
    color: var(--antro-primary-dark);
    font-weight: 800;
  }

  .antro-unit {
    min-width: 52px;
    padding: 0 12px;
    text-align: center;
    color: var(--antro-muted);
    font-size: 13px;
    font-weight: 800;
    border-left: 1px solid var(--antro-border);
  }

  .antro-radio-box {
    border: 1px solid var(--antro-border-2);
    background: #fdfefe;
    border-radius: 18px;
    padding: 16px;
  }

  .antro-radio-options {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px;
  }

  .antro-radio-pill {
    min-height: 42px;
    border: 1px solid #9fc7ff;
    border-radius: 999px;
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 0 12px;
    cursor: pointer;
    color: var(--antro-dark);
    font-size: 14px;
    font-weight: 800;
  }

  .antro-radio-pill input {
    accent-color: var(--antro-primary);
  }

  .antro-radio-pill:has(input:checked) {
    background: var(--antro-soft);
  }

  .antro-calculated-strip {
    background: #f8fbff;
    border: 1px solid var(--antro-border-2);
    border-radius: 20px;
    padding: 20px;
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 18px;
    margin: 20px 0;
  }

  .antro-kpi {
    border-right: 1px solid #dbe7f7;
    padding-right: 14px;
  }

  .antro-kpi:last-child {
    border-right: 0;
  }

  .antro-kpi span {
    display: block;
    color: #4b5563;
    font-size: 13px;
    font-weight: 800;
    margin-bottom: 8px;
  }

  .antro-kpi strong {
    display: block;
    color: var(--antro-primary);
    font-size: 22px;
    font-weight: 900;
  }

  .antro-actions-inline {
    display: flex;
    flex-wrap: wrap;
    gap: 14px;
    align-items: center;
    margin-top: 18px;
  }

  .antro-btn {
    min-height: 46px;
    border: 1px solid transparent;
    border-radius: 16px;
    padding: 0 22px;
    cursor: pointer;
    font-weight: 800;
    font-size: 15px;
    transition: .2s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
  }

  .antro-btn-primary {
    background: var(--antro-primary);
    color: #fff;
  }

  .antro-btn-primary:hover {
    background: var(--antro-primary-dark);
    color: #fff;
  }

  .antro-btn-soft {
    background: #eef6ff;
    color: var(--antro-primary-dark);
    border-color: #b7d8ff;
  }

  .antro-btn-muted {
    background: #f3f6fb;
    color: #6b7280;
    border-color: var(--antro-border-2);
  }

  .antro-btn-outline {
    background: #fff;
    color: #374151;
    border-color: var(--antro-border);
  }

  .antro-note {
    display: block;
    width: 100%;
    color: var(--antro-muted);
    font-size: 12px;
    margin-top: -4px;
  }

  .antro-summary {
    position: sticky;
    top: 20px;
    align-self: stretch;
    min-height: 100%;
  }

  .antro-summary h3 {
    margin: 0;
    color: var(--antro-dark);
    font-size: 22px;
  }

  .antro-summary p {
    margin: 8px 0 22px;
    color: #4b5563;
    font-size: 14px;
  }

  .antro-summary-row {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    padding: 15px 0;
    border-bottom: 1px solid #dbe7f7;
    color: var(--antro-muted);
    font-size: 14px;
    font-weight: 800;
  }

  .antro-summary-row strong {
    color: var(--antro-primary);
    text-align: right;
  }

  .antro-summary-row .antro-danger {
    color: var(--antro-danger);
  }

  .antro-next-action {
    margin-top: 24px;
    background: #f0f7ff;
    border: 1px solid #b7d8ff;
    border-radius: 18px;
    padding: 20px;
  }

  .antro-next-action strong {
    display: block;
    color: var(--antro-dark);
    margin-bottom: 10px;
  }

  .antro-next-action p {
    margin: 0 0 16px;
    color: #334155;
  }

  .antro-textarea {
    min-height: 130px;
    resize: vertical;
    padding: 14px;
    font-family: inherit;
  }

  .antro-hidden {
    display: none !important;
  }



  .antro-progress-title {
    color: var(--antro-dark);
    font-weight: 900;
    margin-bottom: 10px;
  }

  .antro-progress-wrap {
    display: grid;
    grid-template-columns: minmax(260px, 520px) auto;
    align-items: center;
    gap: 18px;
  }

  .antro-progress-track {
    width: 100%;
    height: 16px;
    background: var(--antro-soft);
    border-radius: 999px;
    overflow: hidden;
  }

  .antro-progress-bar {
    height: 100%;
    width: 0;
    background: var(--antro-primary);
    border-radius: 999px;
    transition: .2s ease;
  }

  .antro-progress-text {
    color: var(--antro-primary);
    font-size: 14px;
    font-weight: 800;
    white-space: nowrap;
  }

  .antro-footer-actions {
    display: flex;
    gap: 12px;
    flex-wrap: nowrap;
    justify-content: flex-end;
    align-items: center;
  }

  .antro-footer-actions .antro-btn {
    min-width: 118px;
    height: 50px;
    padding: 0 22px;
    white-space: nowrap;
  }

  .antro-footer-actions #btnGuardarAntro {
    min-width: 180px;
  }

  @media (max-width: 900px) {
    .antro-footer-actions {
      flex-wrap: wrap;
      justify-content: stretch;
    }

    .antro-footer-actions .antro-btn {
      flex: 1 1 calc(50% - 8px);
      min-width: 0;
    }

    .antro-footer-actions #btnGuardarAntro {
      flex-basis: 100%;
    }
  }

  .antro-modal-backdrop {
    position: fixed;
    inset: 0;
    background: rgba(7, 23, 97, .32);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 2000;
    padding: 20px;
  }

  .antro-modal-backdrop.antro-modal-open {
    display: flex;
  }

  .antro-modal {
    width: min(760px, 100%);
    max-height: 90vh;
    overflow: auto;
    background: #fff;
    border-radius: 24px;
    border: 1px solid var(--antro-border);
    box-shadow: 0 24px 80px rgba(7, 23, 97, .22);
    padding: 28px;
  }

  .antro-modal-wide {
    width: min(920px, 100%);
  }

  .antro-modal-header {
    display: flex;
    justify-content: space-between;
    gap: 16px;
    align-items: center;
    margin-bottom: 20px;
  }

  .antro-modal-title {
    margin: 0;
    color: var(--antro-dark);
    font-size: 22px;
    font-weight: 900;
  }

  .antro-modal-close {
    width: 38px;
    height: 38px;
    border: 0;
    border-radius: 50%;
    background: #eef3ff;
    color: var(--antro-dark);
    cursor: pointer;
    font-size: 22px;
    font-weight: 800;
  }

  .antro-table {
    width: 100%;
    border-collapse: collapse;
    overflow: hidden;
    border-radius: 16px;
    border: 1px solid var(--antro-border);
  }

  .antro-table th,
  .antro-table td {
    padding: 13px;
    border-bottom: 1px solid #e2ecfb;
    text-align: left;
    font-size: 14px;
  }

  .antro-table th {
    background: #f0f7ff;
    color: var(--antro-dark);
    font-weight: 900;
  }

  .antro-table td {
    color: #334155;
  }

  .antro-step-disabled {
    display: none !important;
  }

  .antro-section-disabled {
    display: none !important;
  }

  .antro-shell {
    display: grid;
    grid-template-columns: 72px minmax(0, 1fr);
    width: 100%;
    min-height: calc(100vh - var(--app-header-height, 0px));
    position: relative;
    z-index: 0;
    margin-left: -12px;
    margin-right: -12px;
    margin-top: -1.5rem;
    background: var(--antro-bg);
  }

  .antro-sidebar {
    background: var(--ds-dark, #101a61);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 18px 0;
    box-shadow: 8px 0 28px rgba(8, 20, 79, .12);
  }

  .antro-sidebar-item {
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

  .antro-sidebar-item img {
    width: 26px;
    height: 26px;
    opacity: 1;
    filter: none;
    object-fit: contain;
  }

  .antro-sidebar-item.active {
    filter: brightness(1) invert(0);
    background: #fff;
    box-shadow: 0 10px 22px rgba(0, 0, 0, .18);
  }

  .antro-sidebar-item:hover {
    filter: none;
    opacity: 1;
    background: #fff;
    transform: translateY(-1px);
  }

  .antro-sidebar-item:hover img,
  .antro-sidebar-item.active img {
    filter: none;
    opacity: 1;
  }

  .antro-sidebar-item[title]::before {
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

  .antro-sidebar-item:hover[title]::before {
    opacity: 1;
  }

  .antro-main {
    min-width: 0;
    width: 100%;
    padding: 24px 28px 110px;
    background: var(--antro-bg);
    overflow-x: hidden;
  }

  .antro-footer {
    position: fixed;
    left: calc(72px + 28px);
    right: 28px;
    bottom: 18px;
    z-index: 50;

    background: #fff;
    border: 1px solid var(--antro-border);
    border-radius: 22px;
    padding: 20px 24px;
    box-shadow: var(--antro-shadow);

    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
  }


  .eval-patient-head {
    margin-top: 8px;
    margin-bottom: 10px;
  }

  .eval-patient-name {
    color: #374151;
    font-size: 18px;
    font-weight: 800;
    line-height: 1.2;
  }

  .eval-patient-meta {
    display: flex;
    align-items: center;
    gap: 7px;
    flex-wrap: wrap;
    margin-top: 5px;
    color: #4b5563;
    font-size: 14px;
    font-weight: 700;
  }

  .eval-patient-journey {
    margin-top: 5px;
    color: #6b7280;
    font-size: 14px;
    font-weight: 700;
  }

  #filaMetodoEdema.solo-edema {
    grid-template-columns: 1fr;
  }

  #filaMetodoEdema.solo-edema #bloqueEdema {
    grid-column: 1 / -1;
  }

  @media (max-width: 1500px) {
    .antro-layout {
      grid-template-columns: 220px minmax(0, 1fr) 310px;
      gap: 18px;
    }

    .antro-card {
      padding: 24px;
    }
  }

  @media (max-width: 1180px) {
    .antro-shell {
      grid-template-columns: 1fr;
    }

    .antro-sidebar {
      flex-direction: row;
      overflow-x: auto;
      justify-content: flex-start;
      align-items: center;
      gap: 10px;
      padding: 10px 14px;
      box-shadow: 0 8px 24px rgba(8, 20, 79, 0.14);
    }

    .antro-sidebar-item {
      flex: 0 0 48px;
    }

    .antro-main {
      padding: 18px 14px 150px;
    }

    .antro-layout {
      grid-template-columns: 1fr;
    }

    .antro-steps,
    .antro-summary {
      position: static;
    }

    .antro-steps {
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .antro-footer {
      left: 14px;
      right: 14px;
      bottom: 14px;
      flex-direction: column;
      align-items: stretch;
    }

    .antro-progress-wrap {
      grid-template-columns: 1fr;
    }
  }

  @media (max-width: 900px) {
    .antro-header {
      flex-direction: column;
      align-items: stretch;
    }

    .antro-header-right,
    .antro-header-left {
      justify-content: flex-start;
    }

    .antro-grid-3,
    .antro-grid-2,
    .antro-calculated-strip {
      grid-template-columns: 1fr;
    }

    .antro-kpi {
      border-right: 0;
      border-bottom: 1px solid #dbe7f7;
      padding-bottom: 14px;
    }

    .antro-kpi:last-child {
      border-bottom: 0;
    }

    .antro-footer-actions {
      flex-wrap: wrap;
      justify-content: stretch;
    }

    .antro-footer-actions .antro-btn {
      flex: 1 1 calc(50% - 8px);
      min-width: 0;
    }

    .antro-footer-actions #btnGuardarAntro {
      flex-basis: 100%;
    }
  }

  @media (max-width: 560px) {
    .antro-steps {
      grid-template-columns: 1fr;
    }

    .antro-radio-options {
      grid-template-columns: 1fr;
    }

    .antro-mini-card {
      min-width: 100%;
    }
  }
</style>

<div class="antro-shell" data-page="antropometria">
  <aside class="antro-sidebar" aria-label="Menú de pesquisas">
    <?php

    foreach ($pesquisasActividad as $pid): ?>
      <?php
      $info = $infoPesquisas[$pid] ?? null;
      if (! $info) {
        continue;
      }

      $esActiva = ((int) $pid === (int) $tipoPesquisaId);

      $query = [];
      if ($jornadaId) {
        $query['jornada_id'] = $jornadaId;
      }

      if ($centroId) {
        $query['centro_id'] = $centroId;
      }

      $urlPesquisa = base_url("evaluaciones/formulario/{$beneficiario['id_beneficiario']}/{$pid}");
      $urlPesquisa .= ! empty($query) ? '?' . http_build_query($query) : '';

      $imgFile = $esActiva
        ? ($info['img'] ?? 'antropometria2.svg')
        : ($info['gris'] ?? 'antropometria-color.svg');
      ?>

      <a href="<?= esc($urlPesquisa) ?>"
        class="antro-sidebar-item <?= $esActiva ? 'active' : '' ?>"
        title="<?= esc($info['nombre']) ?>"
        aria-label="<?= esc($info['nombre']) ?>">
        <img src="<?= base_url('img/' . $imgFile) ?>" alt="<?= esc($info['nombre']) ?>">
      </a>
    <?php endforeach; ?>
  </aside>

  <main class="antro-main">
    <form id="formAntropometria" method="post" action="<?= base_url('evaluaciones/guardar') ?>">
      <?= csrf_field() ?>

      <input type="hidden" name="beneficiario_id" value="<?= esc($beneficiario['id_beneficiario']) ?>">
      <input type="hidden" name="tipo_pesquisa_id" value="<?= esc($tipoPesquisaId) ?>">
      <input type="hidden" name="jornada_id" value="<?= esc($jornadaId) ?>">
      <input type="hidden" name="centro_id" value="<?= esc($centroId) ?>">
      <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">
      <input type="hidden" id="fechaNacimientoBeneficiario" value="<?= esc($fechaNacimientoBenef ?? '') ?>">
      <input type="hidden" id="antroSexo" value="<?= esc($sexoAntro) ?>">
      <input type="hidden" id="antroFechaNacimiento" value="<?= esc($fechaNacimiento) ?>">
      <input type="hidden" id="antroJsonManifest" value='<?= esc(json_encode($zscoreManifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)) ?>'>

      <?php foreach (
        [
          'imc',
          'zpe',
          'zpe_percentil',
          'zte',
          'zte_percentil',
          'zpt',
          'zpt_percentil',
          'zimce',
          'zimce_percentil',
          'zcc',
          'zcc_percentil',
          'zcbi',
          'zcbi_percentil',
          'zptri',
          'zptri_percentil',
          'zpsub',
          'zpsub_percentil',
          'grupo_edad_reporte',
          'clasificacion_imc_talla',
          'estado_nutricional_agregado',
          'edad_dias_medicion',
          'edad_meses_medicion',
          'embarazo_semanas',
          'embarazo_imc_pregestacional',
          'embarazo_ganancia_kg'
        ] as $codigo
      ): ?>
        <input type="hidden" id="<?= $codigo ?>" name="campos[<?= $codigo ?>]" value="<?= $valorCampo($codigo) ?>">
      <?php endforeach; ?>

      <div class="antro-header">
        <div class="antro-header-left">
          <div class="antro-icon">
            <img src="<?= base_url('img/' . $iconoPesquisa) ?>" alt="<?= esc($nombrePesquisa) ?>">
          </div>

          <div>
            <h1 class="antro-title"><?= esc($nombrePesquisa) ?></h1>
            <div class="eval-patient-head">
              <div class="eval-patient-name">
                <?= $nombreCompleto ?>
              </div>

              <div class="eval-patient-meta">
                <span id="sexoBeneficiarioTexto"><?= esc(strtolower($sexoVista ?? ($sexoAntro === 'F' ? 'femenino' : 'masculino'))) ?></span>
                <span>/</span>
                <span id="edadBeneficiarioTexto"><?= esc($edadTextoInicial) ?></span>
              </div>

              <div class="eval-patient-journey">
                <?= $jornadaId ? 'Jornada ' . esc($jornadaId) : 'Sin jornada asociada' ?>
              </div>
            </div>

            <span class="antro-chip <?= $esEdicion ? 'antro-chip-edit' : '' ?>">
              <?= $esEdicion ? 'Editando evaluación' : 'Nueva evaluación' ?>
            </span>
          </div>
        </div>

        <div class="antro-header-right">
          <div class="antro-mini-card">
            <label for="fecha_evaluacion">Fecha de evaluación</label>
            <input class="antro-date-input" type="date" id="fecha_evaluacion" name="fecha_evaluacion" value="<?= esc($fechaEvaluacionIso) ?>" required>
          </div>

          <div class="antro-mini-card">
            <label>Estado</label>
            <div class="antro-tags">
              <span class="antro-tag" id="antroTagEdad">—</span>
              <span class="antro-tag antro-tag-warning" id="antroTagImc">IMC pendiente</span>
            </div>
          </div>
        </div>
      </div>



      <div class="antro-layout">
        <nav class="antro-steps" aria-label="Secciones de evaluación">
          <button class="antro-step antro-step-active" type="button" data-step="1">
            <span class="antro-step-number">1</span>
            <span>Mediciones</span>
          </button>

          <button class="antro-step" type="button" data-step="2" id="stepCondiciones">
            <span class="antro-step-number">2</span>
            <span>Condiciones</span>
          </button>

          <button class="antro-step" type="button" data-step="3">
            <span class="antro-step-number">3</span>
            <span>Observaciones</span>
          </button>
        </nav>

        <section class="antro-card antro-form-card">
          <div class="antro-section antro-section-active" data-section="1">
            <h2 class="antro-section-title">Mediciones básicas</h2>
            <p class="antro-section-help">
              Vista simplificada: primero los datos obligatorios, luego opciones clínicas.
            </p>

            <div class="antro-alert-box" id="antroInterpretacionBox">
              <strong>Interpretación combinada</strong>
              <span id="antroInterpretacionTexto">
                Completa peso y talla para calcular la interpretación combinada.
              </span>
            </div>

            <div class="antro-grid-3">
              <div class="antro-field">
                <label for="peso">Peso <span class="antro-required">*</span></label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="0.9" max="275" id="peso" name="campos[peso]" value="<?= $valorCampo('peso') ?>" required>
                  <span class="antro-unit">kg</span>
                </div>
              </div>

              <div class="antro-field">
                <label for="talla">Talla / longitud <span class="antro-required">*</span></label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="30" max="230" id="talla" name="campos[talla]" value="<?= $valorCampo('talla') ?>" required>
                  <span class="antro-unit">cm</span>
                </div>
              </div>

              <div class="antro-field" id="campoCintura">
                <label for="circ_cintura">Circunferencia cintura <span class="antro-required">*</span></label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="30" max="220" id="circ_cintura" name="campos[circ_cintura]" value="<?= $valorCampo('circ_cintura') ?>">
                  <span class="antro-unit">cm</span>
                </div>
              </div>
            </div>

            <div class="antro-grid-2" id="filaMetodoEdema">
              <div class="antro-radio-box" id="bloqueMetodoTalla">
                <span class="antro-radio-box-label">Método de medición de talla</span>
                <div class="antro-radio-options">
                  <label class="antro-radio-pill">
                    <input type="radio" name="campos[metodo_medicion_talla]" value="de_pie" <?= $valorCampo('metodo_medicion_talla') === 'de_pie' ? 'checked' : '' ?>>
                    De pie
                  </label>

                  <label class="antro-radio-pill">
                    <input type="radio" name="campos[metodo_medicion_talla]" value="acostado" <?= $valorCampo('metodo_medicion_talla', 'acostado') === 'acostado' ? 'checked' : '' ?>>
                    Acostado
                  </label>
                </div>
              </div>

              <div class="antro-radio-box" id="bloqueEdema">
                <span class="antro-radio-box-label">Edema</span>
                <div class="antro-radio-options">
                  <label class="antro-radio-pill">
                    <input type="radio" name="campos[edema]" value="0" <?= $valorCampo('edema', '0') === '0' ? 'checked' : '' ?>>
                    No
                  </label>

                  <label class="antro-radio-pill">
                    <input type="radio" name="campos[edema]" value="1" <?= $valorCampo('edema') === '1' ? 'checked' : '' ?>>
                    Sí
                  </label>
                </div>
              </div>
            </div>
            <div class="antro-grid-2" id="medicionesMenor5">
              <div class="antro-field" data-menor5>
                <label for="circ_cefalica">Circunferencia cefálica</label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="0" id="circ_cefalica" name="campos[circ_cefalica]" value="<?= $valorCampo('circ_cefalica') ?>">
                  <span class="antro-unit">cm</span>
                </div>
              </div>

              <div class="antro-field" data-menor5>
                <label for="circ_brazo_izq">Circunferencia brazo izquierdo</label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="0" id="circ_brazo_izq" name="campos[circ_brazo_izq]" value="<?= $valorCampo('circ_brazo_izq') ?>">
                  <span class="antro-unit">cm</span>
                </div>
              </div>

              <div class="antro-field" data-menor5>
                <label for="pliegue_tricipital">Pliegue tricipital</label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="0" id="pliegue_tricipital" name="campos[pliegue_tricipital]" value="<?= $valorCampo('pliegue_tricipital') ?>">
                  <span class="antro-unit">mm</span>
                </div>
              </div>

              <div class="antro-field" data-menor5>
                <label for="pliegue_subescapular">Pliegue subescapular</label>
                <div class="antro-input-wrap">
                  <input type="number" step="0.1" min="0" id="pliegue_subescapular" name="campos[pliegue_subescapular]" value="<?= $valorCampo('pliegue_subescapular') ?>">
                  <span class="antro-unit">mm</span>
                </div>
              </div>
            </div>
            <div class="antro-calculated-strip">
              <div class="antro-kpi">
                <span>IMC</span>
                <strong id="imcPreview">—</strong>
              </div>

              <div class="antro-kpi">
                <span>Grupo reporte</span>
                <strong id="grupoReportePreview">—</strong>
              </div>

              <div class="antro-kpi">
                <span>ZIMC/E</span>
                <strong id="zimcePreview">—</strong>
              </div>

              <div class="antro-kpi">
                <span>ZTE</span>
                <strong id="ztePreview">—</strong>
              </div>
            </div>

            <div class="antro-actions-inline">
              <button class="antro-btn antro-btn-soft" type="button" id="btnZscore">
                Ver Percentil / Z-Score
              </button>

              <button class="antro-btn antro-btn-muted antro-hidden" type="button" id="btnPesoDiferencia">
                Peso por diferencia
              </button>

              <small class="antro-note" id="notaPesoDiferencia">
                Peso por diferencia solo se muestra para menores de 2 años.
              </small>
            </div>
          </div>
          <div class="antro-section" data-section="2" id="sectionCondiciones">
            <h2 class="antro-section-title" id="condicionesTitulo">Condiciones especiales</h2>
            <p class="antro-section-help" id="condicionesHelp">
              Flujo para embarazo, lactancia y discapacidad según edad y sexo.
            </p>

            <div class="antro-alert-box antro-alert-info antro-hidden" id="condicionesNoAplican">
              <strong>No aplica</strong>
              <span>Embarazo y discapacidad no aplican para menores de 2 años.</span>
            </div>

            <div id="bloqueCondicionesEspeciales">
              <div class="antro-grid-2">
                <div class="antro-radio-box" id="bloqueLactante">
                  <span class="antro-radio-box-label">Mujer lactante</span>
                  <div class="antro-radio-options">
                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[lactante]" value="0" <?= $valorCampo('lactante', '0') === '0' ? 'checked' : '' ?>>
                      No
                    </label>

                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[lactante]" value="1" <?= $valorCampo('lactante') === '1' ? 'checked' : '' ?>>
                      Sí
                    </label>
                  </div>
                </div>

                <div class="antro-radio-box" id="bloqueEmbarazada">
                  <span class="antro-radio-box-label">Mujer embarazada</span>
                  <div class="antro-radio-options">
                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[embarazada]" value="0" <?= $valorCampo('embarazada', '0') === '0' ? 'checked' : '' ?>>
                      No
                    </label>

                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[embarazada]" value="1" <?= $valorCampo('embarazada') === '1' ? 'checked' : '' ?>>
                      Sí
                    </label>
                  </div>
                </div>
              </div>

              <div class="antro-grid-2 antro-hidden" id="embarazoCampos">
                <div class="antro-field">
                  <label for="fum">Fecha última menstruación</label>
                  <div class="antro-input-wrap">
                    <input type="date" id="fum" name="campos[embarazo_fum]" value="<?= $valorCampo('embarazo_fum') ?>">
                  </div>
                </div>

                <div class="antro-field">
                  <label for="fechaEco">Fecha último eco</label>
                  <div class="antro-input-wrap">
                    <input type="date" id="fechaEco" name="campos[embarazo_fecha_eco]" value="<?= $valorCampo('embarazo_fecha_eco') ?>">
                  </div>
                </div>

                <div class="antro-field">
                  <label for="semanasEco">Semanas por último eco</label>
                  <div class="antro-input-wrap">
                    <input type="number" step="0.1" min="0" id="semanasEco" name="campos[embarazo_semanas_eco]" value="<?= $valorCampo('embarazo_semanas_eco') ?>">
                    <span class="antro-unit">sem</span>
                  </div>
                </div>

                <div class="antro-field">
                  <label for="embarazo_imc_pregestacional_vista">IMC pregestacional</label>
                  <div class="antro-input-wrap">
                    <input type="number" step="0.01" min="0" id="embarazo_imc_pregestacional_vista" value="<?= $valorCampo('embarazo_imc_pregestacional') ?>">
                  </div>
                </div>
              </div>

              <div class="antro-grid-2">
                <div class="antro-radio-box">
                  <span class="antro-radio-box-label">Discapacidad</span>
                  <div class="antro-radio-options">
                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[discapacidad]" value="0" <?= $valorCampo('discapacidad', '0') === '0' ? 'checked' : '' ?>>
                      No
                    </label>

                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[discapacidad]" value="1" <?= $valorCampo('discapacidad') === '1' ? 'checked' : '' ?>>
                      Sí
                    </label>
                  </div>
                </div>

                <div class="antro-radio-box antro-hidden" id="bloqueErguido">
                  <span class="antro-radio-box-label">Se mantiene erguido</span>
                  <div class="antro-radio-options">
                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[se_mantiene_erguido]" value="1" <?= $valorCampo('se_mantiene_erguido', '1') === '1' ? 'checked' : '' ?>>
                      Sí
                    </label>

                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[se_mantiene_erguido]" value="0" <?= $valorCampo('se_mantiene_erguido') === '0' ? 'checked' : '' ?>>
                      No
                    </label>
                  </div>
                </div>

                <div class="antro-radio-box antro-hidden" id="bloqueAusencia">
                  <span class="antro-radio-box-label">Ausencia de extremidades</span>
                  <div class="antro-radio-options">
                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[ausencia_extremidades]" value="0" <?= $valorCampo('ausencia_extremidades', '0') === '0' ? 'checked' : '' ?>>
                      No
                    </label>

                    <label class="antro-radio-pill">
                      <input type="radio" name="campos[ausencia_extremidades]" value="1" <?= $valorCampo('ausencia_extremidades') === '1' ? 'checked' : '' ?>>
                      Sí
                    </label>
                  </div>
                </div>

                <div class="antro-field antro-hidden" id="campoTallaEstimada">
                  <label for="talla_estimada">Talla estimada</label>
                  <div class="antro-input-wrap">
                    <input type="number" step="0.1" min="0" id="talla_estimada" name="campos[talla_estimada]" value="<?= $valorCampo('talla_estimada') ?>">
                    <span class="antro-unit">cm</span>
                  </div>
                </div>

                <div class="antro-field antro-hidden" id="campoPesoAjustado">
                  <label for="peso_ajustado">Peso ajustado</label>
                  <div class="antro-input-wrap">
                    <input type="number" step="0.1" min="0" id="peso_ajustado" name="campos[peso_ajustado]" value="<?= $valorCampo('peso_ajustado') ?>">
                    <span class="antro-unit">kg</span>
                  </div>
                </div>
              </div>
            </div>
          </div>


          <div class="antro-section" data-section="3">
            <h2 class="antro-section-title">Observaciones y remisión</h2>
            <p class="antro-section-help">
              Registra remisión, observaciones clínicas y comentarios finales.
            </p>

            <div class="antro-grid-2">
              <div class="antro-field antro-field-full">
                <label for="remision">Remisión</label>
                <div class="antro-input-wrap">
                  <select id="remision" name="campos[remision]">
                    <option value="" <?= $valorCampo('remision') === '' ? 'selected' : '' ?>>Sin remisión</option>
                    <option value="nutricion" <?= $valorCampo('remision') === 'nutricion' ? 'selected' : '' ?>>Nutrición</option>
                    <option value="medicina_general" <?= $valorCampo('remision') === 'medicina_general' ? 'selected' : '' ?>>Medicina general</option>
                    <option value="pediatria" <?= $valorCampo('remision') === 'pediatria' ? 'selected' : '' ?>>Pediatría</option>
                    <option value="gineco_obstetricia" <?= $valorCampo('remision') === 'gineco_obstetricia' ? 'selected' : '' ?>>Gineco-obstetricia</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="antro-field">
              <label for="observaciones">Observaciones</label>
              <textarea class="antro-textarea" id="observaciones" name="observaciones"><?= esc($obsExistente) ?></textarea>
            </div>
          </div>
        </section>

        <aside class="antro-card antro-summary">
          <h3>Resumen clínico</h3>
          <p>Datos clave siempre visibles.</p>

          <div class="antro-summary-row">
            <span>Peso</span>
            <strong id="resPeso">—</strong>
          </div>

          <div class="antro-summary-row">
            <span>Talla</span>
            <strong id="resTalla">—</strong>
          </div>

          <div class="antro-summary-row">
            <span>IMC</span>
            <strong id="resImc">—</strong>
          </div>

          <div class="antro-summary-row" id="resCinturaRow">
            <span>Cintura</span>
            <strong id="resCintura" class="antro-danger">Pendiente</strong>
          </div>

          <div class="antro-summary-row">
            <span>Edema</span>
            <strong id="resEdema">No</strong>
          </div>

          <div class="antro-summary-row">
            <span>ZIMC/E</span>
            <strong id="resZimce">—</strong>
          </div>

          <div class="antro-summary-row">
            <span>ZTE</span>
            <strong id="resZte">—</strong>
          </div>

          <div class="antro-summary-row">
            <span>ZPT/P-L</span>
            <strong id="resZpt">—</strong>
          </div>

          <div class="antro-summary-row">
            <span>Estado</span>
            <strong id="resEstado">—</strong>
          </div>



          <div class="antro-alert-box antro-alert-danger antro-hidden" id="antroError"></div>
        </aside>
      </div>

      <footer class="antro-footer">
        <div>
          <div class="antro-progress-title">Progreso de evaluación</div>
          <div class="antro-progress-wrap">
            <div class="antro-progress-track">
              <div class="antro-progress-bar" id="antroProgressBar"></div>
            </div>
            <span class="antro-progress-text" id="antroProgressText">0 / 3 básicos completos</span>
          </div>
        </div>

        <div class="antro-footer-actions">
          <a class="antro-btn antro-btn-outline" href="<?= esc($urlRetorno) ?>">Volver</a>
          <button class="antro-btn antro-btn-soft" type="button" id="btnAnterior">Anterior</button>
          <button class="antro-btn antro-btn-soft" type="button" id="btnSiguiente">Siguiente</button>
          <button class="antro-btn antro-btn-primary" type="submit" id="btnGuardarAntro">Guardar evaluación</button>
        </div>
      </footer>
    </form>
  </main>
</div>

<div class="antro-modal-backdrop" id="modalPesoDiferencia">
  <div class="antro-modal">
    <div class="antro-modal-header">
      <h3 class="antro-modal-title">Peso por diferencia</h3>
      <button class="antro-modal-close" type="button" data-close-modal>&times;</button>
    </div>

    <div class="antro-grid-2">
      <div class="antro-field">
        <label for="pesoCargador">Peso del cargador</label>
        <div class="antro-input-wrap">
          <input type="number" step="0.1" min="0" id="pesoCargador">
          <span class="antro-unit">kg</span>
        </div>
      </div>

      <div class="antro-field">
        <label for="pesoAmbos">Peso de ambos</label>
        <div class="antro-input-wrap">
          <input type="number" step="0.1" min="0" id="pesoAmbos">
          <span class="antro-unit">kg</span>
        </div>
      </div>

      <div class="antro-field antro-field-full">
        <label for="pesoCalculado">Peso calculado</label>
        <div class="antro-input-wrap">
          <input type="number" step="0.1" id="pesoCalculado" readonly>
          <span class="antro-unit">kg</span>
        </div>
      </div>
    </div>

    <div class="antro-actions-inline">
      <button class="antro-btn antro-btn-primary" type="button" id="guardarPesoCalculado">
        Usar este peso
      </button>

      <button class="antro-btn antro-btn-outline" type="button" data-close-modal>
        Cancelar
      </button>
    </div>
  </div>
</div>

<div class="antro-modal-backdrop" id="modalZscore">
  <div class="antro-modal antro-modal-wide">
    <div class="antro-modal-header">
      <h3 class="antro-modal-title">Percentil / Z-Score</h3>
      <button class="antro-modal-close" type="button" data-close-modal>&times;</button>
    </div>

    <table class="antro-table">
      <thead>
        <tr>
          <th>Indicador</th>
          <th>Percentil</th>
          <th>Z-Score</th>
          <th>Gráfico</th>
        </tr>
      </thead>
      <tbody id="tablaZscore">
        <tr>
          <td>P/L</td>
          <td id="pct_pl">—</td>
          <td id="z_pl">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>P/E</td>
          <td id="pct_pe">—</td>
          <td id="z_pe">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>L/E</td>
          <td id="pct_le">—</td>
          <td id="z_le">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>IMC/E</td>
          <td id="pct_imce">—</td>
          <td id="z_imce">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>CC/E</td>
          <td id="pct_cce">—</td>
          <td id="z_cce">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>CBI/E</td>
          <td id="pct_cbie">—</td>
          <td id="z_cbie">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>PT/E</td>
          <td id="pct_pte">—</td>
          <td id="z_pte">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
        <tr>
          <td>PS/E</td>
          <td id="pct_pse">—</td>
          <td id="z_pse">—</td>
          <td><button class="antro-btn antro-btn-soft" type="button">Ver</button></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formAntropometria');

    const sexo = document.getElementById('antroSexo')?.value || '';
    const fechaNacimiento = document.getElementById('antroFechaNacimiento')?.value || '';
    const fechaEvaluacion = document.getElementById('fecha_evaluacion');

    const peso = document.getElementById('peso');
    const talla = document.getElementById('talla');
    const cintura = document.getElementById('circ_cintura');

    const hiddenImc = document.getElementById('imc');
    const hiddenGrupo = document.getElementById('grupo_edad_reporte');
    const hiddenEstado = document.getElementById('estado_nutricional_agregado');
    const hiddenClasificacion = document.getElementById('clasificacion_imc_talla');
    const hiddenEdadDias = document.getElementById('edad_dias_medicion');
    const hiddenEdadMeses = document.getElementById('edad_meses_medicion');

    const btnPesoDiferencia = document.getElementById('btnPesoDiferencia');
    const notaPesoDiferencia = document.getElementById('notaPesoDiferencia');
    const btnZscore = document.getElementById('btnZscore');
    const campoCintura = document.getElementById('campoCintura');
    const resCinturaRow = document.getElementById('resCinturaRow');

    const steps = document.querySelectorAll('.antro-step');
    const sections = document.querySelectorAll('.antro-section');

    let currentStep = 1;
    let edadDias = calcularEdadDias();
    let edadMeses = calcularEdadMesesExactos();
    let esMenor2 = edadDias > 0 && edadDias < 730;
    let esMenor5 = edadDias > 0 && edadDias < 1856;
    let esMayor19 = edadDias > 6939;
    let aplicaZscore = edadDias > 0 && edadDias <= 6939;
    let aplicaLactante = sexo === 'F' && edadMeses > 144 && edadMeses <= 600;
    let aplicaDiscapacidad = edadDias > 730;
    let antroData = {};
    let antroDataReady = false;

    inicializarVista();
    bindEventos();

    cargarDatosAntro().then(function() {
      antroDataReady = true;
      recalcular();
    }).catch(function(error) {
      console.error('No se pudieron cargar los JSON antropométricos:', error);
      antroDataReady = false;
      recalcular();
    });

    function inicializarVista() {
      actualizarEdadHidden();
      actualizarGrupoEdad();

      const aplicaEmbarazo = sexo === 'F' && !esMenor2;
      const aplicaCondiciones = aplicaEmbarazo || aplicaLactante || aplicaDiscapacidad;

      const stepCondiciones = document.getElementById('stepCondiciones');
      const sectionCondiciones = document.getElementById('sectionCondiciones');
      const bloqueCondiciones = document.getElementById('bloqueCondicionesEspeciales');
      const condicionesNoAplican = document.getElementById('condicionesNoAplican');

      if (!aplicaCondiciones) {
        stepCondiciones?.classList.add('antro-step-disabled');
        bloqueCondiciones?.classList.add('antro-hidden');
        condicionesNoAplican?.classList.remove('antro-hidden');

        deshabilitarControles(sectionCondiciones, true);

        if (currentStep === 2) {
          irPaso(1);
        }
      } else {
        stepCondiciones?.classList.remove('antro-step-disabled');
        bloqueCondiciones?.classList.remove('antro-hidden');
        condicionesNoAplican?.classList.add('antro-hidden');

        deshabilitarControles(sectionCondiciones, false);
      }

      controlarLactante();
      controlarEmbarazo(aplicaEmbarazo);
      controlarDiscapacidadBase();

      controlarMetodoTallaYPesoDiferencia();
      controlarCinturaYZscore();

      document.querySelectorAll('[data-menor5]').forEach(function(el) {
        el.classList.toggle('antro-hidden', !esMenor5);

        el.querySelectorAll('input, select, textarea').forEach(function(input) {
          input.disabled = !esMenor5;

          if (!esMenor5) {
            input.value = '';
          }
        });
      });

      toggleEmbarazo();
      toggleDiscapacidad();
      toggleTallaEstimada();
    }
    async function cargarDatosAntro() {
      const base = '<?= base_url('data/antro') ?>';

      const archivos = {
        percentiles: 'percentiles.json',
        interpretacionEmbarazo: 'interpretacion_embarazo.json',

        zcbiDias: 'zcbi_dias.json',
        zccDias: 'zcc_dias.json',

        zimceDias: 'zimce_dias.json',
        zimceMeses: 'zimce_meses.json',

        zpeDias: 'zpe_dias.json',
        zpeMeses: 'zpe_meses.json',

        zpesoTalla: 'zpeso_talla.json',
        zpesoTalla2: 'zpeso_talla2.json',

        zsubescapularDias: 'zsubescapular_dias.json',

        zteDias: 'zte_dias.json',
        zteMeses: 'zte_meses.json',
        zteMesesParte2: 'zte_meses_parte2.json',

        ztricipitalDias: 'ztricipital_dias.json'
      };

      const entradas = await Promise.all(
        Object.entries(archivos).map(async function([key, filename]) {
          const response = await fetch(base + '/' + filename, {
            cache: 'force-cache'
          });

          if (!response.ok) {
            throw new Error('No se pudo cargar ' + filename);
          }

          return [key, await response.json()];
        })
      );

      antroData = Object.fromEntries(entradas);
    }

    function controlarLactante() {
      const bloqueLactante = document.getElementById('bloqueLactante');

      if (aplicaLactante) {
        bloqueLactante?.classList.remove('antro-hidden');

        document.querySelectorAll('input[name="campos[lactante]"]').forEach(function(el) {
          el.disabled = false;
        });
      } else {
        bloqueLactante?.classList.add('antro-hidden');

        document.querySelectorAll('input[name="campos[lactante]"]').forEach(function(el) {
          el.disabled = true;
          el.checked = false;
        });
      }
    }

    function controlarEmbarazo(aplicaEmbarazo) {
      const bloqueEmbarazada = document.getElementById('bloqueEmbarazada');
      const embarazoCampos = document.getElementById('embarazoCampos');

      if (aplicaEmbarazo) {
        bloqueEmbarazada?.classList.remove('antro-hidden');

        document.querySelectorAll('input[name="campos[embarazada]"]').forEach(function(el) {
          el.disabled = false;
        });
      } else {
        bloqueEmbarazada?.classList.add('antro-hidden');
        embarazoCampos?.classList.add('antro-hidden');

        document.querySelectorAll(
          'input[name="campos[embarazada]"], #fum, #fechaEco, #semanasEco, #diasEco, #embarazo_imc_pregestacional_vista'
        ).forEach(function(el) {
          el.disabled = true;

          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        });
      }
    }

    function controlarDiscapacidadBase() {
      if (!aplicaDiscapacidad) {
        document.querySelectorAll(
          'input[name="campos[discapacidad]"], input[name="campos[se_mantiene_erguido]"], input[name="campos[ausencia_extremidades]"], #talla_estimada, #peso_ajustado'
        ).forEach(function(el) {
          el.disabled = true;

          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        });

        document.getElementById('bloqueErguido')?.classList.add('antro-hidden');
        document.getElementById('bloqueAusencia')?.classList.add('antro-hidden');
        document.getElementById('campoTallaEstimada')?.classList.add('antro-hidden');
        document.getElementById('campoPesoAjustado')?.classList.add('antro-hidden');
      } else {
        document.querySelectorAll('input[name="campos[discapacidad]"]').forEach(function(el) {
          el.disabled = false;
        });
      }
    }

    function controlarMetodoTallaYPesoDiferencia() {
      const filaMetodoEdema = document.getElementById('filaMetodoEdema');
      const bloqueMetodoTalla = document.getElementById('bloqueMetodoTalla');

      if (esMenor2) {
        bloqueMetodoTalla?.classList.remove('antro-hidden');
        filaMetodoEdema?.classList.remove('solo-edema');

        document.querySelectorAll('input[name="campos[metodo_medicion_talla]"]').forEach(function(el) {
          el.disabled = false;
        });

        btnPesoDiferencia?.classList.remove('antro-hidden');

        if (notaPesoDiferencia) {
          notaPesoDiferencia.textContent = 'Disponible porque el beneficiario es menor de 2 años.';
        }
      } else {
        bloqueMetodoTalla?.classList.add('antro-hidden');
        filaMetodoEdema?.classList.add('solo-edema');

        document.querySelectorAll('input[name="campos[metodo_medicion_talla]"]').forEach(function(el) {
          el.disabled = true;
          el.checked = false;
        });

        btnPesoDiferencia?.classList.add('antro-hidden');

        if (notaPesoDiferencia) {
          notaPesoDiferencia.textContent = 'Peso por diferencia solo se muestra para menores de 2 años.';
        }
      }
    }

    function controlarCinturaYZscore() {
      if (esMayor19) {
        campoCintura?.classList.remove('antro-hidden');
        resCinturaRow?.classList.remove('antro-hidden');

        if (cintura) {
          cintura.required = true;
          cintura.disabled = false;
        }
      } else {
        campoCintura?.classList.add('antro-hidden');
        resCinturaRow?.classList.add('antro-hidden');

        if (cintura) {
          cintura.required = false;
          cintura.disabled = true;
          cintura.value = '';
        }
      }

      if (aplicaZscore) {
        btnZscore?.classList.remove('antro-hidden');

        if (btnZscore) {
          btnZscore.disabled = false;
        }
      } else {
        btnZscore?.classList.add('antro-hidden');

        if (btnZscore) {
          btnZscore.disabled = true;
        }
      }
    }

    function getVisibleSteps() {
      return Array.from(steps)
        .filter(function(btn) {
          return !btn.classList.contains('antro-step-disabled');
        })
        .map(function(btn) {
          return parseInt(btn.dataset.step, 10);
        });
    }

    function irPasoSiguiente() {
      const visibles = getVisibleSteps();
      const index = visibles.indexOf(currentStep);

      if (index >= 0 && index < visibles.length - 1) {
        irPaso(visibles[index + 1]);
      }
    }

    function irPasoAnterior() {
      const visibles = getVisibleSteps();
      const index = visibles.indexOf(currentStep);

      if (index > 0) {
        irPaso(visibles[index - 1]);
      }
    }

    function deshabilitarControles(contenedor, disabled) {
      if (!contenedor) return;

      contenedor.querySelectorAll('input, select, textarea').forEach(function(el) {
        el.disabled = disabled;

        if (disabled) {
          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        }
      });
    }

    function bindEventos() {
      [peso, talla, cintura, fechaEvaluacion].forEach(function(el) {
        if (!el) return;

        el.addEventListener('input', recalcular);
        el.addEventListener('change', function() {
          actualizarReglasEdad();
          inicializarVista();
          recalcular();
        });
      });

      document.querySelectorAll('input[name="campos[edema]"]').forEach(function(radio) {
        radio.addEventListener('change', recalcular);
      });

      steps.forEach(function(btn) {
        btn.addEventListener('click', function() {
          irPaso(parseInt(btn.dataset.step, 10));
        });
      });

      document.getElementById('btnAnterior').addEventListener('click', irPasoAnterior);
      document.getElementById('btnSiguiente').addEventListener('click', irPasoSiguiente);

      const btnIrRequerido = document.getElementById('btnIrRequerido');

      if (btnIrRequerido) {
        btnIrRequerido.addEventListener('click', irCampoRequerido);
      }

      if (btnPesoDiferencia) {
        btnPesoDiferencia.addEventListener('click', function() {
          abrirModal('modalPesoDiferencia');
        });
      }

      if (btnZscore) {
        btnZscore.addEventListener('click', function() {
          if (!aplicaZscore) {
            return;
          }

          abrirModal('modalZscore');
          pintarZscoreResumen();
        });
      }

      document.querySelectorAll('[data-close-modal]').forEach(function(btn) {
        btn.addEventListener('click', cerrarModales);
      });

      document.querySelectorAll('.antro-modal-backdrop').forEach(function(modal) {
        modal.addEventListener('click', function(event) {
          if (event.target === modal) cerrarModales();
        });
      });

      ['pesoCargador', 'pesoAmbos'].forEach(function(id) {
        document.getElementById(id).addEventListener('input', calcularPesoDiferencia);
      });

      document.getElementById('guardarPesoCalculado').addEventListener('click', usarPesoCalculado);

      document.querySelectorAll('input[name="campos[embarazada]"]').forEach(function(radio) {
        radio.addEventListener('change', toggleEmbarazo);
      });

      document.querySelectorAll('input[name="campos[discapacidad]"]').forEach(function(radio) {
        radio.addEventListener('change', toggleDiscapacidad);
      });

      document.querySelectorAll('input[name="campos[se_mantiene_erguido]"]').forEach(function(radio) {
        radio.addEventListener('change', toggleTallaEstimada);
      });

      ['fum', 'fechaEco', 'semanasEco'].forEach(function(id) {
        const el = document.getElementById(id);
        if (el) el.addEventListener('change', calcularSemanasGestacion);
      });

      form.addEventListener('submit', validarGuardar);
    }

    function actualizarReglasEdad() {
      edadDias = calcularEdadDias();
      edadMeses = calcularEdadMesesExactos();

      esMenor2 = edadDias > 0 && edadDias < 730;
      esMenor5 = edadDias > 0 && edadDias < 1856;
      esMayor19 = edadDias > 6939;

      aplicaZscore = edadDias > 0 && edadDias <= 6939;
      aplicaLactante = sexo === 'F' && edadMeses > 144 && edadMeses <= 600;
      aplicaDiscapacidad = edadDias > 730;
    }

    function calcularEdadDias() {
      if (!fechaNacimiento || !fechaEvaluacion.value) {
        return 0;
      }

      const nac = new Date(fechaNacimiento + 'T00:00:00');
      const evalDate = new Date(fechaEvaluacion.value + 'T00:00:00');

      if (isNaN(nac.getTime()) || isNaN(evalDate.getTime())) {
        return 0;
      }

      return Math.max(0, Math.floor((evalDate - nac) / 86400000));
    }

    function calcularEdadMesesExactos() {
      if (!fechaNacimiento || !fechaEvaluacion.value) {
        return 0;
      }

      const nac = new Date(fechaNacimiento + 'T00:00:00');
      const evalDate = new Date(fechaEvaluacion.value + 'T00:00:00');

      if (Number.isNaN(nac.getTime()) || Number.isNaN(evalDate.getTime()) || evalDate < nac) {
        return 0;
      }

      let meses = (evalDate.getFullYear() - nac.getFullYear()) * 12;
      meses += evalDate.getMonth() - nac.getMonth();

      if (evalDate.getDate() < nac.getDate()) {
        meses--;
      }

      return Math.max(0, meses);
    }

    function actualizarEdadHidden() {
      hiddenEdadDias.value = edadDias > 0 ? edadDias : '';
      hiddenEdadMeses.value = edadDias > 0 ? Math.floor(edadDias / 30.4375) : '';
    }

    function irPaso(step) {
      const stepBtn = document.querySelector('.antro-step[data-step="' + step + '"]');

      if (stepBtn && stepBtn.classList.contains('antro-step-disabled')) {
        return;
      }

      currentStep = step;

      steps.forEach(function(btn) {
        btn.classList.toggle('antro-step-active', parseInt(btn.dataset.step, 10) === step);
      });

      sections.forEach(function(section) {
        section.classList.toggle('antro-section-active', parseInt(section.dataset.section, 10) === step);
      });
    }

    function recalcular() {
      const p = parseFloat(peso.value);
      const t = parseFloat(talla.value);
      const c = parseFloat(cintura.value);
      const edema = document.querySelector('input[name="campos[edema]"]:checked')?.value || '0';

      let imcValue = null;

      if (p > 0 && t > 0) {
        imcValue = p / Math.pow(t / 100, 2);
        hiddenImc.value = imcValue.toFixed(2);
      } else {
        hiddenImc.value = '';
      }

      const grupo = obtenerGrupoReporte();
      hiddenGrupo.value = grupo;

      document.getElementById('imcPreview').textContent = imcValue ? imcValue.toFixed(2) : '—';
      document.getElementById('grupoReportePreview').textContent = grupo;
      document.getElementById('resPeso').textContent = p > 0 ? p.toFixed(1) + ' kg' : '—';
      document.getElementById('resTalla').textContent = t > 0 ? t.toFixed(1) + ' cm' : '—';
      document.getElementById('resImc').textContent = imcValue ? imcValue.toFixed(2) : '—';
      document.getElementById('resEdema').textContent = edema === '1' ? 'Sí' : 'No';

      document.getElementById('zimcePreview').textContent = document.getElementById('zimce')?.value || '—';
      document.getElementById('ztePreview').textContent = document.getElementById('zte')?.value || '—';
      document.getElementById('resZimce').textContent = document.getElementById('zimce')?.value || '—';
      document.getElementById('resZte').textContent = document.getElementById('zte')?.value || '—';
      document.getElementById('resZpt').textContent = document.getElementById('zpt')?.value || '—';

      if (esMayor19) {
        document.getElementById('resCintura').textContent = c > 0 ? c.toFixed(1) + ' cm' : 'Pendiente';
        document.getElementById('resCintura').classList.toggle('antro-danger', !(c > 0));
      }

      if (antroDataReady && aplicaZscore) {
        recalcularZscoresAntro();
      } else {
        limpiarZscoresAntro();
      }

      actualizarInterpretacion(imcValue, c, edema);
      actualizarProgreso();
    }

    function obtenerGrupoReporte() {
      if (edadDias > 0 && edadDias < 730) return '< 2 años';
      if (edadDias >= 730 && edadDias < 1856) return '2 a 5 años';
      if (edadDias >= 1856 && edadDias <= 6939) return '5 a 19 años';
      if (edadDias > 6939) return '> 19 años';
      return 'Edad no válida';
    }

    function actualizarGrupoEdad() {
      const tag = document.getElementById('antroTagEdad');

      if (!tag) return;

      if (edadDias > 0 && edadDias < 730) {
        tag.textContent = 'Menor de 2 años';
      } else if (edadDias >= 730 && edadDias < 1856) {
        tag.textContent = '2 a 5 años';
      } else if (edadDias >= 1856 && edadDias <= 6939) {
        tag.textContent = '5 a 19 años';
      } else if (edadDias > 6939) {
        tag.textContent = 'Mayor de 19 años';
      } else {
        tag.textContent = 'Edad no válida';
      }
    }

    function actualizarInterpretacion(imcValue, cinturaValue, edema) {
      const box = document.getElementById('antroInterpretacionBox');
      const txt = document.getElementById('antroInterpretacionTexto');
      const tagImc = document.getElementById('antroTagImc');
      const estado = document.getElementById('resEstado');

      if (!box || !txt) return;

      box.classList.remove('antro-alert-info', 'antro-alert-danger');

      const pesoVal = parseFloat(peso?.value || '');
      const tallaVal = parseFloat(talla?.value || '');
      const sexoVal = sexo;

      if (!(edadDias > 0)) {
        pintarInterpretacion({
          texto: 'Revisar datos: fecha de evaluación o fecha de nacimiento inválida.',
          estado: 'Revisar datos',
          clase: 'danger'
        });
        return;
      }

      if (!(pesoVal > 0) || !(tallaVal > 0)) {
        pintarInterpretacion({
          texto: 'Completa peso y talla para calcular la interpretación combinada.',
          estado: 'Pendiente',
          clase: 'info'
        });
        return;
      }

      const embarazadaValor = document.querySelector('input[name="campos[embarazada]"]:checked')?.value || '0';

      if (sexoVal === 'F' && !esMenor2 && embarazadaValor === '1') {
        const resultadoEmb = interpretarEmbarazada();

        pintarInterpretacion(resultadoEmb);
        return;
      }

      if (aplicaZscore) {
        const resultadoMenor = interpretarMenor19(edema);

        pintarInterpretacion(resultadoMenor);
        return;
      }

      if (edadDias > 21550) {
        const resultadoAdultoMayor = interpretarAdultoMayor(imcValue, cinturaValue, sexoVal);

        pintarInterpretacion(resultadoAdultoMayor);
        return;
      }

      if (esMayor19) {
        const resultadoAdulto = interpretarAdulto(imcValue, cinturaValue, sexoVal);

        pintarInterpretacion(resultadoAdulto);
        return;
      }

      pintarInterpretacion({
        texto: 'Revisar datos.',
        estado: 'Revisar datos',
        clase: 'danger'
      });
    }

    function pintarInterpretacion(resultado) {
      const box = document.getElementById('antroInterpretacionBox');
      const txt = document.getElementById('antroInterpretacionTexto');
      const tagImc = document.getElementById('antroTagImc');
      const estado = document.getElementById('resEstado');

      if (!box || !txt) return;

      box.classList.remove('antro-alert-info', 'antro-alert-danger');

      if (resultado.clase === 'danger') {
        box.classList.add('antro-alert-danger');
      } else {
        box.classList.add('antro-alert-info');
      }

      txt.textContent = resultado.texto || 'Revisar datos.';

      if (tagImc) {
        tagImc.textContent = resultado.estado || 'Revisar datos';
      }

      if (estado) {
        estado.textContent = resultado.estado || 'Revisar datos';
      }

      if (hiddenEstado) {
        hiddenEstado.value = resultado.estado || '';
      }

      if (hiddenClasificacion) {
        hiddenClasificacion.value = resultado.estado || '';
      }
    }

    function interpretarMenor19(edema) {
      const zimce = parseFloat(document.getElementById('zimce')?.value || '');
      const zte = parseFloat(document.getElementById('zte')?.value || '');

      if (edema === '1') {
        return {
          texto: 'Edema presente. Indicadores asociados a peso no aplican. Requiere revisión clínica.',
          estado: 'Revisar por edema',
          clase: 'danger'
        };
      }

      if (Number.isNaN(zimce) || Number.isNaN(zte)) {
        return {
          texto: 'Completa los datos necesarios para calcular ZIMC/E y ZTE.',
          estado: 'Pendiente Z-Score',
          clase: 'info'
        };
      }

      const estadoImc = clasificarZimce(zimce);
      const estadoTalla = clasificarZte(zte);

      if (estadoImc.revisar || estadoTalla.revisar) {
        return {
          texto: 'Revisar datos: z-score fuera del rango admisible.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      const texto = estadoImc.texto + ' con ' + estadoTalla.texto;

      return {
        texto: texto,
        estado: texto,
        clase: estadoImc.clase === 'danger' || estadoTalla.clase === 'danger' ? 'danger' : estadoImc.clase
      };
    }

    function clasificarZimce(z) {
      if (z < -5 || z > 5) {
        return {
          texto: 'Revisar datos',
          clase: 'danger',
          revisar: true
        };
      }

      if (z >= -5 && z <= -3.01) {
        return {
          texto: 'Delgadez severa',
          clase: 'danger'
        };
      }

      if (z > -3.01 && z <= -2.01) {
        return {
          texto: 'Delgadez',
          clase: 'danger'
        };
      }

      if (z > -2.01 && z <= -1.01) {
        return {
          texto: 'Riesgo de delgadez',
          clase: 'warning'
        };
      }

      if (z > -1.01 && z <= 1.00) {
        return {
          texto: 'Peso adecuado',
          clase: 'info'
        };
      }

      if (z > 1.00 && z <= 2.00) {
        return {
          texto: 'Sobrepeso',
          clase: 'warning'
        };
      }

      if (z > 2.00 && z <= 3.00) {
        return {
          texto: 'Obesidad',
          clase: 'danger'
        };
      }

      if (z > 3.00 && z <= 5.00) {
        return {
          texto: 'Obesidad severa',
          clase: 'danger'
        };
      }

      return {
        texto: 'Revisar datos',
        clase: 'danger',
        revisar: true
      };
    }

    function clasificarZte(z) {
      if (z < -6 || z > 6) {
        return {
          texto: 'Revisar datos',
          clase: 'danger',
          revisar: true
        };
      }

      if (z >= -6 && z <= -3.01) {
        return {
          texto: 'talla muy baja',
          clase: 'danger'
        };
      }

      if (z > -3.01 && z <= -2.01) {
        return {
          texto: 'talla baja',
          clase: 'warning'
        };
      }

      if (z > -2.01 && z <= 2.00) {
        return {
          texto: 'talla adecuada',
          clase: 'info'
        };
      }

      if (z > 2.00 && z <= 6.00) {
        return {
          texto: 'talla alta',
          clase: 'info'
        };
      }

      return {
        texto: 'Revisar datos',
        clase: 'danger',
        revisar: true
      };
    }

    function interpretarAdulto(imcValue, cinturaValue, sexoVal) {
      if (!(imcValue > 0) || !(cinturaValue > 0)) {
        return {
          texto: 'Completa IMC y circunferencia de cintura para calcular la interpretación adulta.',
          estado: 'Pendiente cintura',
          clase: 'info'
        };
      }

      if (imcValue < 12 || imcValue > 80) {
        return {
          texto: 'Revisar datos: IMC fuera del rango admisible.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      const estadoImc = clasificarImcAdulto(imcValue);
      const riesgo = clasificarRiesgoCintura(cinturaValue, sexoVal);

      if (!estadoImc || !riesgo) {
        return {
          texto: 'Revisar datos.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      const texto = estadoImc.texto + ' con ' + riesgo.texto;

      return {
        texto: texto,
        estado: texto,
        clase: estadoImc.clase === 'danger' || riesgo.clase === 'danger' ? 'danger' : estadoImc.clase
      };
    }

    function clasificarImcAdulto(imc) {
      if (imc >= 12 && imc <= 16.00) {
        return {
          texto: 'Delgadez intensa',
          clase: 'danger'
        };
      }

      if (imc > 16.00 && imc <= 16.99) {
        return {
          texto: 'Delgadez moderada',
          clase: 'danger'
        };
      }

      if (imc > 16.99 && imc <= 18.49) {
        return {
          texto: 'Delgadez leve',
          clase: 'warning'
        };
      }

      if (imc > 18.49 && imc <= 24.99) {
        return {
          texto: 'Peso adecuado',
          clase: 'info'
        };
      }

      if (imc > 24.99 && imc <= 29.99) {
        return {
          texto: 'Sobrepeso',
          clase: 'warning'
        };
      }

      if (imc > 29.99 && imc <= 39.99) {
        return {
          texto: 'Obesidad',
          clase: 'danger'
        };
      }

      if (imc > 39.99 && imc <= 80) {
        return {
          texto: 'Obesidad severa',
          clase: 'danger'
        };
      }

      return null;
    }

    function clasificarRiesgoCintura(cintura, sexoVal) {
      if (sexoVal === 'M') {
        if (cintura < 94) {
          return {
            texto: 'riesgo bajo',
            clase: 'info'
          };
        }

        if (cintura >= 94 && cintura <= 101.9) {
          return {
            texto: 'riesgo incrementado',
            clase: 'warning'
          };
        }

        if (cintura > 101.9) {
          return {
            texto: 'riesgo incrementado sustancialmente',
            clase: 'danger'
          };
        }
      }

      if (sexoVal === 'F') {
        if (cintura < 80) {
          return {
            texto: 'riesgo bajo',
            clase: 'info'
          };
        }

        if (cintura >= 80 && cintura <= 87.9) {
          return {
            texto: 'riesgo incrementado',
            clase: 'warning'
          };
        }

        if (cintura > 87.9) {
          return {
            texto: 'riesgo incrementado sustancialmente',
            clase: 'danger'
          };
        }
      }

      return null;
    }

    function interpretarAdultoMayor(imcValue, cinturaValue, sexoVal) {
      if (!(imcValue > 0) || !(cinturaValue > 0)) {
        return {
          texto: 'Completa IMC y circunferencia de cintura para calcular la interpretación del adulto mayor.',
          estado: 'Pendiente cintura',
          clase: 'info'
        };
      }

      if (imcValue < 12 || imcValue > 80) {
        return {
          texto: 'Revisar datos: IMC fuera del rango admisible.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      const estadoImc = clasificarImcAdultoMayor(imcValue);
      const riesgo = clasificarRiesgoCintura(cinturaValue, sexoVal);

      if (!estadoImc || !riesgo) {
        return {
          texto: 'Revisar datos.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      const texto = estadoImc.texto + ' con ' + riesgo.texto;

      return {
        texto: texto,
        estado: texto,
        clase: estadoImc.clase === 'danger' || riesgo.clase === 'danger' ? 'danger' : estadoImc.clase
      };
    }

    function clasificarImcAdultoMayor(imc) {
      if (imc >= 12 && imc <= 18.99) {
        return {
          texto: 'Desnutrido',
          clase: 'danger'
        };
      }

      if (imc > 18.99 && imc <= 22.99) {
        return {
          texto: 'Delgado',
          clase: 'warning'
        };
      }

      if (imc > 22.99 && imc <= 27.99) {
        return {
          texto: 'Peso adecuado',
          clase: 'info'
        };
      }

      if (imc > 27.99 && imc <= 31.99) {
        return {
          texto: 'Sobrepeso',
          clase: 'warning'
        };
      }

      if (imc > 31.99 && imc <= 80) {
        return {
          texto: 'Obesidad',
          clase: 'danger'
        };
      }

      return null;
    }

    function interpretarEmbarazada() {
      const semanas = parseFloat(document.getElementById('embarazo_semanas')?.value || '');
      const imcPreg = parseFloat(document.getElementById('embarazo_imc_pregestacional')?.value || '');
      const ganancia = parseFloat(document.getElementById('embarazo_ganancia_kg')?.value || '');

      if (!(semanas > 3) || !(imcPreg > 0) || !(ganancia > 0)) {
        return {
          texto: 'Completa semanas de gestación, peso pregestacional, peso actual y talla para calcular la interpretación de embarazo.',
          estado: 'Embarazo pendiente',
          clase: 'info'
        };
      }

      const estadoImcPreg = clasificarImcPregestacional(imcPreg);

      if (!estadoImcPreg) {
        return {
          texto: 'Revisar datos de IMC pregestacional.',
          estado: 'Revisar datos',
          clase: 'danger'
        };
      }

      /*
        Aquí debe consultarse el JSON equivalente a interpretacion_emb.php:
        - semanas
        - bajopeso_inferior / superior
        - pesonormal_inferior / superior
        - sobrepeso_inferior / superior
        - obesidad_inferior / superior
      */

      return {
        texto: estadoImcPreg.texto + ' con ganancia de peso pendiente de validar contra tabla gestacional.',
        estado: estadoImcPreg.texto,
        clase: estadoImcPreg.clase
      };
    }

    function clasificarImcPregestacional(imcPreg) {
      if (imcPreg > 0 && imcPreg < 18.5) {
        return {
          texto: 'Bajo peso',
          clave: 'bajopeso',
          clase: 'danger'
        };
      }

      if (imcPreg >= 18.5 && imcPreg <= 24.9) {
        return {
          texto: 'Peso adecuado',
          clave: 'pesonormal',
          clase: 'info'
        };
      }

      if (imcPreg > 24.9 && imcPreg <= 29.9) {
        return {
          texto: 'Sobrepeso',
          clave: 'sobrepeso',
          clase: 'warning'
        };
      }

      if (imcPreg > 29.9) {
        return {
          texto: 'Obesidad',
          clave: 'obesidad',
          clase: 'danger'
        };
      }

      return null;
    }

    function calcularPercentilDesdeZ(z, percentilesJson) {
      const zNum = parseFloat(z);

      if (Number.isNaN(zNum) || zNum < -3 || zNum > 3) {
        return 'N/A';
      }

      const zRedondeado = Math.round(zNum * 100) / 100;
      const zBase = Math.trunc(zRedondeado * 10) / 10;
      const segundoDecimal = Math.round(Math.abs((zRedondeado - zBase) * 100));

      const fila = percentilesJson.find(function(row) {
        return parseFloat(String(row.p_normal).replace(',', '.')) === zBase;
      });

      if (!fila) {
        return 'N/A';
      }

      const key = 'p' + segundoDecimal;

      if (fila[key] === undefined) {
        return 'N/A';
      }

      return (parseFloat(String(fila[key]).replace(',', '.')) * 100).toFixed(1);
    }

    function actualizarProgreso() {
      let total = esMayor19 ? 3 : 2;
      let completos = 0;

      if (parseFloat(peso.value) > 0) completos++;
      if (parseFloat(talla.value) > 0) completos++;
      if (esMayor19 && parseFloat(cintura.value) > 0) completos++;

      const pct = total > 0 ? Math.round((completos / total) * 100) : 0;

      document.getElementById('antroProgressBar').style.width = pct + '%';
      document.getElementById('antroProgressText').textContent = completos + ' / ' + total + ' básicos completos';
    }

    function irCampoRequerido() {
      irPaso(1);

      setTimeout(function() {
        if (!(parseFloat(peso.value) > 0)) {
          peso.focus();
          return;
        }

        if (!(parseFloat(talla.value) > 0)) {
          talla.focus();
          return;
        }

        if (esMayor19 && !(parseFloat(cintura.value) > 0)) {
          cintura.focus();
        }
      }, 80);
    }

    function abrirModal(id) {
      document.getElementById(id).classList.add('antro-modal-open');
    }

    function cerrarModales() {
      document.querySelectorAll('.antro-modal-backdrop').forEach(function(modal) {
        modal.classList.remove('antro-modal-open');
      });
    }

    function calcularPesoDiferencia() {
      const cargador = parseFloat(document.getElementById('pesoCargador').value);
      const ambos = parseFloat(document.getElementById('pesoAmbos').value);
      const salida = document.getElementById('pesoCalculado');

      if (!(cargador > 0) || !(ambos > 0)) {
        salida.value = '';
        return;
      }

      if (ambos < cargador) {
        salida.value = '';
        return;
      }

      salida.value = (ambos - cargador).toFixed(1);
    }

    function usarPesoCalculado() {
      const calculado = parseFloat(document.getElementById('pesoCalculado').value);

      if (!(calculado > 0)) {
        mostrarError('Calcula un peso válido antes de guardar.');
        return;
      }

      peso.value = calculado.toFixed(1);
      cerrarModales();
      recalcular();
    }

    function toggleEmbarazo() {
      const embarazoCampos = document.getElementById('embarazoCampos');

      if (sexo !== 'F' || esMenor2) {
        embarazoCampos?.classList.add('antro-hidden');

        document.querySelectorAll(
          '#fum, #fechaEco, #semanasEco, #diasEco, #embarazo_imc_pregestacional_vista'
        ).forEach(function(el) {
          el.disabled = true;
          el.value = '';
        });

        return;
      }

      const valor = document.querySelector('input[name="campos[embarazada]"]:checked')?.value || '0';
      const mostrar = valor === '1';

      embarazoCampos?.classList.toggle('antro-hidden', !mostrar);

      document.querySelectorAll(
        '#fum, #fechaEco, #semanasEco, #diasEco, #embarazo_imc_pregestacional_vista'
      ).forEach(function(el) {
        el.disabled = !mostrar;

        if (!mostrar) {
          el.value = '';
        }
      });
    }

    function toggleDiscapacidad() {
      const bloqueErguido = document.getElementById('bloqueErguido');
      const bloqueAusencia = document.getElementById('bloqueAusencia');
      const campoTallaEstimada = document.getElementById('campoTallaEstimada');
      const campoPesoAjustado = document.getElementById('campoPesoAjustado');

      const controlesDiscapacidad = document.querySelectorAll(
        'input[name="campos[se_mantiene_erguido]"], input[name="campos[ausencia_extremidades]"], #talla_estimada, #peso_ajustado'
      );

      if (!aplicaDiscapacidad) {
        bloqueErguido?.classList.add('antro-hidden');
        bloqueAusencia?.classList.add('antro-hidden');
        campoTallaEstimada?.classList.add('antro-hidden');
        campoPesoAjustado?.classList.add('antro-hidden');

        controlesDiscapacidad.forEach(function(el) {
          el.disabled = true;

          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        });

        return;
      }

      const discapacidad = document.querySelector('input[name="campos[discapacidad]"]:checked')?.value || '0';
      const mostrar = discapacidad === '1';

      bloqueErguido?.classList.toggle('antro-hidden', !mostrar);
      bloqueAusencia?.classList.toggle('antro-hidden', !mostrar);
      campoPesoAjustado?.classList.toggle('antro-hidden', !mostrar);

      controlesDiscapacidad.forEach(function(el) {
        el.disabled = !mostrar;

        if (!mostrar) {
          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        }
      });

      if (!mostrar) {
        campoTallaEstimada?.classList.add('antro-hidden');
      }

      toggleTallaEstimada();
    }

    function toggleTallaEstimada() {
      const campoTallaEstimada = document.getElementById('campoTallaEstimada');
      const tallaEstimada = document.getElementById('talla_estimada');

      if (esMenor2) {
        campoTallaEstimada?.classList.add('antro-hidden');

        if (tallaEstimada) {
          tallaEstimada.disabled = true;
          tallaEstimada.value = '';
        }

        return;
      }

      const discapacidad = document.querySelector('input[name="campos[discapacidad]"]:checked')?.value || '0';
      const erguido = document.querySelector('input[name="campos[se_mantiene_erguido]"]:checked')?.value || '1';

      const mostrar = discapacidad === '1' && erguido === '0';

      campoTallaEstimada?.classList.toggle('antro-hidden', !mostrar);

      if (tallaEstimada) {
        tallaEstimada.disabled = !mostrar;

        if (!mostrar) {
          tallaEstimada.value = '';
        }
      }
    }

    function calcularSemanasGestacion() {
      const fum = document.getElementById('fum')?.value || '';
      const fechaEco = document.getElementById('fechaEco')?.value || '';
      const semanasEco = parseFloat(document.getElementById('semanasEco')?.value || '');
      const salida = document.getElementById('embarazo_semanas');

      if (!salida || !fechaEvaluacion.value) return;

      if (fum) {
        const inicio = new Date(fum + 'T00:00:00');
        const fin = new Date(fechaEvaluacion.value + 'T00:00:00');
        const diffDias = Math.floor((fin - inicio) / 86400000);

        salida.value = diffDias >= 0 ? Math.floor(diffDias / 7) : '';
        return;
      }

      if (fechaEco && semanasEco >= 0) {
        const ecoDate = new Date(fechaEco + 'T00:00:00');
        const evalDate = new Date(fechaEvaluacion.value + 'T00:00:00');
        const diffDias = Math.floor((evalDate - ecoDate) / 86400000);

        salida.value = diffDias >= 0 ? (semanasEco + Math.floor(diffDias / 7)).toFixed(1) : semanasEco.toFixed(1);
      }
    }

   function pintarZscoreResumen() {
  const pares = [
    ['z_pe', 'zpe'],
    ['pct_pe', 'zpe_percentil'],

    ['z_le', 'zte'],
    ['pct_le', 'zte_percentil'],

    ['z_pl', 'zpt'],
    ['pct_pl', 'zpt_percentil'],

    ['z_imce', 'zimce'],
    ['pct_imce', 'zimce_percentil'],

    ['z_cce', 'zcc'],
    ['pct_cce', 'zcc_percentil'],

    ['z_cbie', 'zcbi'],
    ['pct_cbie', 'zcbi_percentil'],

    ['z_pte', 'zptri'],
    ['pct_pte', 'zptri_percentil'],

    ['z_pse', 'zpsub'],
    ['pct_pse', 'zpsub_percentil']
  ];

  pares.forEach(function ([destino, origen]) {
    const destinoEl = document.getElementById(destino);
    const origenEl = document.getElementById(origen);

    if (destinoEl) {
      destinoEl.textContent = origenEl?.value || '—';
    }
  });
}
    function validarGuardar(event) {
      const errores = [];

      if (!(parseFloat(peso.value) > 0)) {
        errores.push('Debe registrar el peso.');
      }

      if (!(parseFloat(talla.value) > 0)) {
        errores.push('Debe registrar la talla.');
      }

      if (!aplicaDiscapacidad) {
        document.querySelectorAll(
          'input[name="campos[discapacidad]"], input[name="campos[se_mantiene_erguido]"], input[name="campos[ausencia_extremidades]"], #talla_estimada, #peso_ajustado'
        ).forEach(function(el) {
          el.disabled = true;

          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        });
      }

      if (sexo !== 'F' || esMenor2) {
        document.querySelectorAll(
          'input[name="campos[embarazada]"], input[name="campos[lactante]"], #fum, #fechaEco, #semanasEco, #diasEco, #embarazo_imc_pregestacional_vista, #embarazo_peso_pregestacional, #embarazo_peso_actual, #embarazo_talla, #embarazo_circ_brazo_izq'
        ).forEach(function(el) {
          el.disabled = true;

          if (el.type === 'radio' || el.type === 'checkbox') {
            el.checked = false;
          } else {
            el.value = '';
          }
        });
      }

      if (!aplicaLactante) {
        document.querySelectorAll('input[name="campos[lactante]"]').forEach(function(el) {
          el.disabled = true;
          el.checked = false;
        });
      }

      if (!esMenor2) {
        document.querySelectorAll('input[name="campos[metodo_medicion_talla]"]').forEach(function(el) {
          el.disabled = true;
          el.checked = false;
        });
      }

      if (esMayor19 && !(parseFloat(cintura.value) > 0)) {
        errores.push('En mayores de 19 años es obligatoria la circunferencia de cintura.');
      }

      if (errores.length > 0) {
        event.preventDefault();
        mostrarError(errores.join('<br>'));
        irCampoRequerido();
        return false;
      }
    }

    function mostrarError(mensaje) {
      const box = document.getElementById('antroError');

      box.innerHTML = mensaje;
      box.classList.remove('antro-hidden');

      setTimeout(function() {
        box.classList.add('antro-hidden');
      }, 5000);
    }
  });
</script>
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const fechaEvaluacion = document.getElementById('fecha_evaluacion');
    const fechaNacimiento = document.getElementById('fechaNacimientoBeneficiario');
    const edadTexto = document.getElementById('edadBeneficiarioTexto');

    if (!fechaEvaluacion || !fechaNacimiento || !edadTexto) {
      return;
    }

    function plural(valor, singular, plural) {
      return valor + ' ' + (valor === 1 ? singular : plural);
    }

    function calcularEdadTexto(fechaNac, fechaRef) {
      if (!fechaNac || !fechaRef) {
        return 'Edad no registrada';
      }

      const nacimiento = new Date(fechaNac + 'T00:00:00');
      const referencia = new Date(fechaRef + 'T00:00:00');

      if (Number.isNaN(nacimiento.getTime()) || Number.isNaN(referencia.getTime())) {
        return 'Edad no válida';
      }

      if (referencia < nacimiento) {
        return 'Edad no válida';
      }

      let anios = referencia.getFullYear() - nacimiento.getFullYear();
      let meses = referencia.getMonth() - nacimiento.getMonth();
      let dias = referencia.getDate() - nacimiento.getDate();

      if (dias < 0) {
        meses--;

        const ultimoDiaMesAnterior = new Date(
          referencia.getFullYear(),
          referencia.getMonth(),
          0
        ).getDate();

        dias += ultimoDiaMesAnterior;
      }

      if (meses < 0) {
        anios--;
        meses += 12;
      }

      return plural(anios, 'año', 'años') +
        ' | ' + plural(meses, 'mes', 'meses') +
        ' | ' + plural(dias, 'día', 'días');
    }

    function actualizarEdadBeneficiario() {
      edadTexto.textContent = calcularEdadTexto(
        fechaNacimiento.value,
        fechaEvaluacion.value
      );
    }
    function num(valor) {
  if (valor === null || valor === undefined || valor === '') return NaN;

  return parseFloat(String(valor).replace(',', '.'));
}

function round2(valor) {
  if (!Number.isFinite(valor)) return '';

  return (Math.round(valor * 100) / 100).toFixed(2);
}

function calcularZscoreLMS(valor, L, M, S) {
  valor = num(valor);
  L = num(L);
  M = num(M);
  S = num(S);

  if (!(valor > 0) || !(M > 0) || !(S > 0) || Number.isNaN(L)) {
    return NaN;
  }

  if (L === 0) {
    return Math.log(valor / M) / S;
  }

  return (Math.pow(valor / M, L) - 1) / (L * S);
}

function buscarFilaLMS(data, sexoKey, denominadorKey, sexoVal, denominador, precision = 0) {
  if (!Array.isArray(data)) return null;

  const denObjetivo = Number(denominador);

  return data.find(function (row) {
    const sexoRow = String(row[sexoKey] || '').toUpperCase();
    const denRow = num(row[denominadorKey]);

    if (sexoRow !== sexoVal) return false;

    if (precision === 1) {
      return Math.round(denRow * 10) / 10 === Math.round(denObjetivo * 10) / 10;
    }

    return Math.round(denRow) === Math.round(denObjetivo);
  }) || null;
}

function calcularDesdeFila(row, valor, lKey, mKey, sKey) {
  if (!row) return '';

  const z = calcularZscoreLMS(valor, row[lKey], row[mKey], row[sKey]);

  return Number.isFinite(z) ? round2(z) : '';
}

function setHiddenValue(id, value) {
  const el = document.getElementById(id);

  if (el) {
    el.value = value || '';
  }
}
function calcularPercentilDesdeZ(z) {
  const zNum = num(z);

  if (!Number.isFinite(zNum) || zNum < -3 || zNum > 3) {
    return 'N/A';
  }

  const signo = zNum < 0 ? -1 : 1;
  const abs = Math.abs(zNum);
  const baseAbs = Math.floor(abs * 10) / 10;
  const base = signo * baseAbs;
  const segundoDecimal = Math.round((abs - baseAbs) * 100);

  const fila = (antroData.percentiles || []).find(function (row) {
    return Math.abs(num(row.p_normal) - base) < 0.0001;
  });

  if (!fila) return 'N/A';

  const key = 'p' + Math.min(9, Math.max(0, segundoDecimal));
  const valor = num(fila[key]);

  if (!Number.isFinite(valor)) return 'N/A';

  return (valor * 100).toFixed(1);
}
function recalcularZscoresAntro() {
  const sexoVal = sexo === 'F' ? 'F' : 'M';
  const p = num(peso?.value);
  const t = num(talla?.value);
  const imcVal = num(document.getElementById('imc')?.value);

  const cc = num(document.getElementById('circ_cefalica')?.value);
  const cbi = num(document.getElementById('circ_brazo_izq')?.value);
  const pt = num(document.getElementById('pliegue_tricipital')?.value);
  const ps = num(document.getElementById('pliegue_subescapular')?.value);
    const edema = document.querySelector('input[name="campos[edema]"]:checked')?.value || '0';

if (edema === '1') {
  calcularZte(sexoVal, t);

  if (cc > 0) calcularZcc(sexoVal, cc);
  if (cbi > 0) calcularZcbi(sexoVal, cbi);
  if (pt > 0) calcularZptri(sexoVal, pt);
  if (ps > 0) calcularZpsub(sexoVal, ps);

  actualizarVistaZscores();
  return;
}
  limpiarZscoresAntro();

  if (!(edadDias > 0) || !(p > 0) || !(t > 0)) {
    return;
  }

  calcularZimce(sexoVal, imcVal);
  calcularZte(sexoVal, t);
  calcularZpe(sexoVal, p);
  calcularZpt(sexoVal, p, t);

  if (cc > 0) {
    calcularZcc(sexoVal, cc);
  }

  if (cbi > 0) {
    calcularZcbi(sexoVal, cbi);
  }

  if (pt > 0) {
    calcularZptri(sexoVal, pt);
  }

  if (ps > 0) {
    calcularZpsub(sexoVal, ps);
  }

  actualizarVistaZscores();
}
function calcularZimce(sexoVal, imcVal) {
  if (!(imcVal > 0)) return;

  let row = null;

  if (edadDias < 1856) {
    row = buscarFilaLMS(
      antroData.zimceDias,
      'idias_indicador_genero',
      'idias_indicador_denominador',
      sexoVal,
      edadDias
    );

    guardarZscoreConPercentil(
      'zimce',
      'zimce_percentil',
      calcularDesdeFila(row, imcVal, 'idias_indicador_coeficiente_l', 'idias_sd0_mediana', 'idias_indicador_coeficiente_s')
    );

    return;
  }

  row = buscarFilaLMS(
    antroData.zimceMeses,
    'i_indicador_genero',
    'i_indicador_denominador',
    sexoVal,
    edadMeses
  );

  guardarZscoreConPercentil(
    'zimce',
    'zimce_percentil',
    calcularDesdeFila(row, imcVal, 'i_indicador_coeficiente_l', 'i_indicador_coeficiente_m', 'i_indicador_coeficiente_s')
  );
}

function calcularZte(sexoVal, tallaVal) {
  let row = null;

  if (edadDias < 1856) {
    row = buscarFilaLMS(
      antroData.zteDias,
      'tdias_indicador_genero',
      'tdias_indicador_denominador',
      sexoVal,
      edadDias
    );

    guardarZscoreConPercentil(
      'zte',
      'zte_percentil',
      calcularDesdeFila(row, tallaVal, 'tdias_indicador_coeficiente_l', 'tdias_sd0_mediana', 'tdias_indicador_coeficiente_s')
    );

    return;
  }

  const zteMesesCombinado = []
    .concat(antroData.zteMeses || [])
    .concat(antroData.zteMesesParte2 || []);

  row = buscarFilaLMS(
    zteMesesCombinado,
    't_indicador_genero',
    't_indicador_denominador',
    sexoVal,
    edadMeses
  );

  guardarZscoreConPercentil(
    'zte',
    'zte_percentil',
    calcularDesdeFila(row, tallaVal, 't_indicador_coeficiente_l', 't_indicador_coeficiente_m', 't_indicador_coeficiente_s')
  );
}

function calcularZpe(sexoVal, pesoVal) {
  let row = null;

  if (edadDias < 1856) {
    row = buscarFilaLMS(
      antroData.zpeDias,
      'pdias_indicador_genero',
      'pdias_indicador_denominador',
      sexoVal,
      edadDias
    );

    guardarZscoreConPercentil(
      'zpe',
      'zpe_percentil',
      calcularDesdeFila(row, pesoVal, 'pdias_indicador_coeficiente_l', 'pdias_sd0_mediana', 'pdias_indicador_coeficiente_s')
    );

    return;
  }

  row = buscarFilaLMS(
    antroData.zpeMeses,
    'p_indicador_genero',
    'p_indicador_denominador',
    sexoVal,
    edadMeses
  );

  guardarZscoreConPercentil(
    'zpe',
    'zpe_percentil',
    calcularDesdeFila(row, pesoVal, 'p_indicador_coeficiente_l', 'p_indicador_coeficiente_m', 'p_indicador_coeficiente_s')
  );
}

function calcularZpt(sexoVal, pesoVal, tallaVal) {
  if (!(pesoVal > 0) || !(tallaVal > 0)) return;

  const tallaRedondeada = Math.round(tallaVal * 10) / 10;

  let data = [];

  if (tallaRedondeada < 65) {
    data = antroData.zpesoTalla2 || [];
  } else {
    data = antroData.zpesoTalla || [];
  }

  const row = buscarFilaLMS(
    data,
    'petadias_indicador_genero',
    'petadias_indicador_denominador',
    sexoVal,
    tallaRedondeada,
    1
  );

  guardarZscoreConPercentil(
    'zpt',
    'zpt_percentil',
    calcularDesdeFila(row, pesoVal, 'petadias_indicador_coeficiente_l', 'petadias_sd0_mediana', 'petadias_indicador_coeficiente_s')
  );
}

function calcularZcc(sexoVal, valor) {
  const row = buscarFilaLMS(
    antroData.zccDias,
    'ccdias_indicador_genero',
    'ccdias_indicador_denominador',
    sexoVal,
    edadDias
  );

  guardarZscoreConPercentil(
    'zcc',
    'zcc_percentil',
    calcularDesdeFila(row, valor, 'ccdias_indicador_coeficiente_l', 'ccdias_sd0', 'ccdias_indicador_coeficiente_s')
  );
}

function calcularZcbi(sexoVal, valor) {
  const row = buscarFilaLMS(
    antroData.zcbiDias,
    'cbidias_indicador_genero',
    'cbidias_indicador_denominador',
    sexoVal,
    edadDias
  );

  guardarZscoreConPercentil(
    'zcbi',
    'zcbi_percentil',
    calcularDesdeFila(row, valor, 'cbidias_indicador_coeficiente_l', 'cbidias_sd0', 'cbidias_indicador_coeficiente_s')
  );
}

function calcularZptri(sexoVal, valor) {
  const row = buscarFilaLMS(
    antroData.ztricipitalDias,
    'pt_indicador_genero',
    'pt_indicador_denominador',
    sexoVal,
    edadDias
  );

  guardarZscoreConPercentil(
    'zptri',
    'zptri_percentil',
    calcularDesdeFila(row, valor, 'pt_indicador_coeficiente_l', 'pt_sd0', 'pt_indicador_coeficiente_s')
  );
}

function calcularZpsub(sexoVal, valor) {
  const row = buscarFilaLMS(
    antroData.zsubescapularDias,
    'ps_indicador_genero',
    'ps_indicador_denominador',
    sexoVal,
    edadDias
  );

  guardarZscoreConPercentil(
    'zpsub',
    'zpsub_percentil',
    calcularDesdeFila(row, valor, 'ps_indicador_coeficiente_l', 'ps_sd0', 'ps_indicador_coeficiente_s')
  );
}
function guardarZscoreConPercentil(idZ, idPct, z) {
  setHiddenValue(idZ, z);

  const pct = z !== '' ? calcularPercentilDesdeZ(z) : '';

  setHiddenValue(idPct, pct);
}

function limpiarZscoresAntro() {
  [
    'zpe',
    'zpe_percentil',
    'zte',
    'zte_percentil',
    'zpt',
    'zpt_percentil',
    'zimce',
    'zimce_percentil',
    'zcc',
    'zcc_percentil',
    'zcbi',
    'zcbi_percentil',
    'zptri',
    'zptri_percentil',
    'zpsub',
    'zpsub_percentil'
  ].forEach(function (id) {
    setHiddenValue(id, '');
  });

  actualizarVistaZscores();
}

function actualizarVistaZscores() {
  const zimce = document.getElementById('zimce')?.value || '';
  const zte = document.getElementById('zte')?.value || '';
  const zpt = document.getElementById('zpt')?.value || '';

  document.getElementById('zimcePreview').textContent = zimce || '—';
  document.getElementById('ztePreview').textContent = zte || '—';

  document.getElementById('resZimce').textContent = zimce || '—';
  document.getElementById('resZte').textContent = zte || '—';
  document.getElementById('resZpt').textContent = zpt || '—';
}
    fechaEvaluacion.addEventListener('change', actualizarEdadBeneficiario);
    fechaEvaluacion.addEventListener('input', actualizarEdadBeneficiario);

    actualizarEdadBeneficiario();
  });
</script>
<?= $this->endSection() ?>
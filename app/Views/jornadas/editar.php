<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
  :root {
    --bg: #f4f7fb;
    --card: #ffffff;
    --line: #e6ebf3;
    --text: #1f2a44;
    --muted: #7c8aa5;
    --primary: #2563eb;
    --primary-2: #1d4ed8;
    --success-bg: #ecfdf3;
    --success: #198754;
    --danger-bg: #fef2f2;
    --danger: #dc2626;
    --shadow: 0 10px 30px rgba(31, 42, 68, .08);
  }

  * {
    box-sizing: border-box;
  }

  body {
    background: var(--bg);
    color: var(--text);
  }

  .jornada_edit-page {
    max-width: 1400px;
    margin: 24px auto;
    padding: 0 18px;
  }

  .jornada_edit-shell {
    background: #fff;
    border-radius: 24px;
    box-shadow: var(--shadow);
    overflow: hidden;
    border: 1px solid #eef2f7;
  }

  .jornada_edit-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 22px 28px;
    border-bottom: 1px solid var(--line);
    background: #fff;
  }

  .jornada_edit-topbar-left {
    display: flex;
    align-items: flex-start;
    gap: 16px;
  }

  .jornada_edit-back-btn {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    border: 1px solid var(--line);
    background: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 22px;
    color: #31415f;
  }

  .jornada_edit-title h1 {
    margin: 0;
    font-size: 40px;
    font-weight: 600;
    letter-spacing: -0.02em;
  }

  .jornada_edit-title p {
    margin: 8px 0 0;
    color: var(--muted);
    font-size: 18px;
  }

  .jornada_edit-content {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 20px;
    padding: 24px;
    background: linear-gradient(180deg, #f7f9fd 0%, #f4f7fb 100%);
  }

  .jornada_edit-left-col,
  .jornada_edit-right-col {
    display: flex;
    flex-direction: column;
    gap: 20px;
  }

  .jornada_edit-card {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 22px;
    box-shadow: 0 6px 18px rgba(24, 39, 75, .05);
    padding: 22px;
  }

  .jornada_edit-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
    font-size: 28px;
    font-weight: 700;
  }

  .jornada_edit-card-modern {
    background: var(--card);
    border: 1px solid var(--line);
    border-radius: 22px;
    box-shadow: 0 6px 18px rgba(24, 39, 75, .05);
    padding: 22px;
  }

  .jornada_edit-card-title-modern {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 22px;
    font-size: 28px;
    font-weight: 700;
  }

  .jornada_edit-grid-2 {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 18px 22px;
  }

  .jornada_edit-field {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .jornada_edit-field label {
    font-size: 16px;
    font-weight: 600;
    color: #31415f;
    margin: 0;
  }

  .jornada_edit-input,
  .jornada_edit-select {
    width: 100%;
    min-height: 56px;
    border: 1px solid #d7e0ec;
    border-radius: 14px;
    padding: 12px 16px;
    font-size: 16px;
    outline: none;
    background: #fff;
    color: var(--text);
  }

  .jornada_edit-input:focus,
  .jornada_edit-select:focus {
    border-color: #8fb3ff;
    box-shadow: 0 0 0 4px rgba(37, 99, 235, .10);
  }

  .jornada_edit-radio-group {
    display: flex;
    align-items: center;
    gap: 22px;
    min-height: 56px;
  }

  .jornada_edit-radio-option {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 17px;
    margin: 0;
  }

  .jornada_edit-search-box {
    position: relative;
  }

  .jornada_edit-search-box input {
    padding-left: 46px;
  }

  .jornada_edit-search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: #94a3b8;
  }

  .jornada_edit-location-grid {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 16px;
    margin-top: 18px;
  }

  .jornada_edit-readonly-input {
    background: #f8f9fa;
  }

  #map {
    height: 350px;
    border-radius: 18px;
    width: 100%;
    border: 1px solid var(--line);
  }

  .jornada_edit-pesquisa-selector {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 18px;
  }

  .jornada_edit-pesquisa-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    cursor: pointer;
    padding: 10px 6px;
  }

  .jornada_edit-pesquisa-item input[type="checkbox"] {
    display: none;
  }

  .jornada_edit-pesquisa-icon-wrap {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: .25s ease;
  }

  .jornada_edit-pesquisa-icon-wrap img {
    width: 34px;
    height: 34px;
  }

  .jornada_edit-pesquisa-icon-wrap .jornada_edit-icon-color {
    display: none;
  }

  .jornada_edit-pesquisa-icon-wrap .jornada_edit-icon-gris {
    display: block;
  }

  .jornada_edit-pesquisa-item input:checked+.jornada_edit-pesquisa-icon-wrap {
    border-color: #3695f5;
    background: #e8eaf8;
    transform: scale(1.08);
    box-shadow: 0 2px 8px rgba(54, 149, 245, .3);
  }

  .jornada_edit-pesquisa-item input:checked+.jornada_edit-pesquisa-icon-wrap .jornada_edit-icon-color {
    display: block;
  }

  .jornada_edit-pesquisa-item input:checked+.jornada_edit-pesquisa-icon-wrap .jornada_edit-icon-gris {
    display: none;
  }

  .jornada_edit-pesquisa-label {
    font-size: .85rem;
    font-weight: 600;
    color: #555;
    margin-top: 8px;
  }

  .jornada_edit-pesquisa-item input:checked~.jornada_edit-pesquisa-label {
    color: #101a61;
  }

  .jornada_edit-summary-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
    font-size: 16px;
  }

  .jornada_edit-summary-item {
    display: flex;
    justify-content: space-between;
    gap: 20px;
    border-bottom: 1px dashed #edf2f7;
    padding-bottom: 12px;
  }

  .jornada_edit-summary-item:last-child {
    border-bottom: none;
  }

  .jornada_edit-label-muted {
    color: var(--muted);
    font-weight: 500;
  }

  .jornada_edit-badge-success-modern {
    background: #ecfdf3;
    color: #198754;
    padding: 4px 14px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 14px;
  }

  .jornada_edit-badge-danger-modern {
    background: #fef2f2;
    color: #dc2626;
    padding: 4px 14px;
    border-radius: 999px;
    font-weight: 700;
    font-size: 14px;
  }

  .jornada_edit-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
  }

  .jornada_edit-chip {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #f0f4ff;
    border-radius: 999px;
    padding: 6px 14px 6px 8px;
    font-size: .85rem;
    font-weight: 600;
    color: #1f2a44;
  }

  .jornada_edit-chip-icon {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: .78rem;
  }

  .jornada_edit-chip-icon.jornada_edit-blue {
    background: #2478df;
  }

  .jornada_edit-chip-icon.jornada_edit-red {
    background: #e72713;
  }

  .jornada_edit-chip-icon.jornada_edit-purple {
    background: #341092;
  }

  .jornada_edit-chip-icon.jornada_edit-yellow {
    background: #ffc107;
  }

  .jornada_edit-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 28px;
    border-top: 1px solid var(--line);
    background: #fff;
  }

  .jornada_edit-note {
    font-size: 14px;
    color: var(--muted);
    max-width: 520px;
  }

  .jornada_edit-actions {
    display: flex;
    gap: 14px;
  }

  .jornada_edit-btn-modern {
    padding: 14px 30px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 16px;
    border: none;
    cursor: pointer;
  }

  .jornada_edit-btn-modern-secondary {
    background: #f1f5f9;
    color: #475569;
  }

  .jornada_edit-btn-modern-primary {
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff;
    box-shadow: 0 8px 20px rgba(37, 99, 235, .3);
  }

  .jornada_edit-inst-sugerencia {
    padding: 10px 14px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    font-size: .9rem;
  }

  .jornada_edit-inst-sugerencia:hover {
    background: #f0f4ff;
  }

  @media(max-width:900px) {
    .jornada_edit-content {
      grid-template-columns: 1fr;
    }

    .jornada_edit-grid-2 {
      grid-template-columns: 1fr;
    }

    .jornada_edit-location-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$iconos_color = [
  '1' => ['color' => 'antropometria-color.svg', 'gris' => 'antropometria2.svg', 'nombre' => 'Antropometría', 'emoji' => '📏', 'clase' => 'jornada_edit-yellow'],
  '2' => ['color' => 'sanguinea-color.svg', 'gris' => 'sanguinea2.svg', 'nombre' => 'Laboratorio', 'emoji' => '🩸', 'clase' => 'jornada_edit-red'],
  '3' => ['color' => 'visual-color.svg', 'gris' => 'visual2.svg', 'nombre' => 'Visual', 'emoji' => '👁', 'clase' => 'jornada_edit-purple'],
  '4' => ['color' => 'signos-vitales-color.svg', 'gris' => 'signosVitales2.svg', 'nombre' => 'Signos vitales', 'emoji' => '❤', 'clase' => 'jornada_edit-red'],
  '5' => ['color' => 'medicina-general-color.svg', 'gris' => 'medicinaGeneral2.svg', 'nombre' => 'Medicina general', 'emoji' => '🩺', 'clase' => 'jornada_edit-blue'],
  '6' => ['color' => 'vacunacion-color.svg', 'gris' => 'vacunacion2.svg', 'nombre' => 'Vacunación', 'emoji' => '💉', 'clase' => 'jornada_edit-blue'],
];

$pesquisasActivas = [];
if (!empty($pesquisasSeleccionadas)) {
  foreach ($pesquisasSeleccionadas as $pid) {
    if (isset($iconos_color[$pid])) {
      $pesquisasActivas[] = $iconos_color[$pid];
    }
  }
}
?>

<div class="jornada_edit-page">
  <div class="jornada_edit-shell">

    <div class="jornada_edit-topbar">
      <div class="jornada_edit-topbar-left">
        <a href="<?= base_url('jornadas') ?>" class="jornada_edit-back-btn">&larr;</a>
        <div class="jornada_edit-title">
          <h1>Editar Jornada</h1>
          <p>Modifica los datos de la jornada y sus servicios</p>
        </div>
      </div>
    </div>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/actualizar') ?>" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="id_jornada" value="<?= esc($jornada['id_jornada']) ?>">

      <main class="jornada_edit-content">
        <section class="jornada_edit-left-col">

          <!-- STATUS Y FECHA -->
          <div class="jornada_edit-card">
            <div class="jornada_edit-card-title"><span>Estado y Fecha</span></div>
            <div class="jornada_edit-grid-2">
              <div class="jornada_edit-field">
                <label for="status_jor">Estado</label>
                <select class="jornada_edit-select" id="status_jor" name="status_jor" required>
                  <option value="1" <?= ($jornada['status_jor'] == 1) ? 'selected' : '' ?>>Activa</option>
                  <option value="2" <?= ($jornada['status_jor'] == 2) ? 'selected' : '' ?>>Finalizada</option>
                </select>
              </div>
              <div class="jornada_edit-field">
                <label for="fecha_inicio">Fecha</label>
                <input class="jornada_edit-input" type="date" id="fecha_inicio" name="fecha_inicio"
                  value="<?= esc($jornada['fecha_inicio']) ?>" required>
              </div>
            </div>
          </div>

          <!-- DETALLES -->
          <div class="jornada_edit-card">
            <div class="jornada_edit-card-title"><span>Detalles de la Jornada</span></div>
            <div class="jornada_edit-grid-2">
              <div class="jornada_edit-field">
                <label for="nombre_jornada">Nombre de la Jornada</label>
                <input class="jornada_edit-input" type="text" id="nombre_jornada" name="nombre_jornada"
                  value="<?= esc($jornada['nombre_jornada']) ?>" required>
              </div>

              <div class="jornada_edit-field">
                <label for="organizacion_id">Organización</label>
                <select class="jornada_edit-select" id="organizacion_id" name="organizacion_id"
                  <?= $soloLectura ? 'disabled' : '' ?> required>
                  <?php foreach ($organizaciones as $o): ?>
                    <option value="<?= $o['id_organizacion'] ?>"
                      <?= ($o['id_organizacion'] == $jornada['organizacion_id']) ? 'selected' : '' ?>>
                      <?= esc($o['nombre_org']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>
                <?php if ($soloLectura): ?>
                  <input type="hidden" name="organizacion_id" value="<?= esc($jornada['organizacion_id']) ?>">
                <?php endif; ?>
              </div>

              <!-- FIX: Institución con sugerencias -->
              <div class="jornada_edit-field">
                <label for="nombre_institucion">Institución o Localidad</label>
                <div style="position:relative;">
                  <input type="hidden" name="institucion_id" id="institucion_id"
                    value="<?= esc($jornada['institucion_id'] ?? '') ?>">
                  <input class="jornada_edit-input" type="text" id="nombre_institucion" name="nombre_institucion"
                    value="<?= esc($jornada['nombre_institucion'] ?? '') ?>"
                    placeholder="Escribe para buscar o crear nueva..." autocomplete="off" required>
                  <div id="institucion-sugerencias" style="
                    position:absolute; top:100%; left:0; right:0; z-index:100;
                    background:#fff; border:1px solid #d9e2ef; border-radius:8px;
                    max-height:200px; overflow-y:auto; display:none;
                    box-shadow:0 4px 12px rgba(0,0,0,.1);
                  "></div>
                </div>
                <small style="color:#64748b; font-size:.8rem;">
                  Escribe el nombre del sitio físico. Si ya existe, selecciónalo de la lista.
                </small>
              </div>

              <div class="jornada_edit-field">
                <label>Tipo de Jornada</label>
                <div class="jornada_edit-radio-group">
                  <label class="jornada_edit-radio-option">
                    <input type="radio" name="tipo_jornada" value="publica"
                      <?= (($jornada['tipo_jornada'] ?? '') == 'publica') ? 'checked' : '' ?> required> Pública
                  </label>
                  <label class="jornada_edit-radio-option">
                    <input type="radio" name="tipo_jornada" value="privada"
                      <?= (($jornada['tipo_jornada'] ?? '') == 'privada') ? 'checked' : '' ?>> Privada
                  </label>
                </div>
              </div>
            </div>

            <!-- MAPA -->
            <div class="jornada_edit-field" style="margin-top:14px;">
              <label for="searchPlace">Ubicación en el mapa</label>
              <div class="jornada_edit-search-box">
                <span class="jornada_edit-search-icon">&#128269;</span>
                <input class="jornada_edit-input" type="text" id="searchPlace" placeholder="Buscar lugar o dirección...">
              </div>
            </div>

            <div id="map"></div>

            <div class="jornada_edit-location-grid">
              <div class="jornada_edit-field">
                <label for="pais">País</label>
                <input type="text" class="jornada_edit-input jornada_edit-readonly-input" name="pais" id="pais"
                  value="<?= esc($jornada['pais'] ?? '') ?>" readonly>
              </div>
              <div class="jornada_edit-field">
                <label for="estado">Estado</label>
                <input type="text" class="jornada_edit-input jornada_edit-readonly-input" name="estado" id="estado"
                  value="<?= esc($jornada['estado'] ?? '') ?>" readonly>
              </div>
              <div class="jornada_edit-field">
                <label for="ciudad">Ciudad</label>
                <input type="text" class="jornada_edit-input jornada_edit-readonly-input" name="ciudad" id="ciudad"
                  value="<?= esc($jornada['ciudad'] ?? '') ?>" readonly>
              </div>
            </div>

            <!-- FIX: Campos municipio, parroquia, detalle -->
            <div class="jornada_edit-location-grid" style="margin-top:12px;">
              <div class="jornada_edit-field">
                <label for="municipio">Municipio</label>
                <input type="text" class="jornada_edit-input" name="municipio" id="municipio"
                  value="<?= esc($jornada['municipio'] ?? '') ?>"
                  placeholder="Se completa con el mapa o escríbelo">
              </div>
              <div class="jornada_edit-field">
                <label for="parroquia">Parroquia</label>
                <input type="text" class="jornada_edit-input" name="parroquia" id="parroquia"
                  value="<?= esc($jornada['parroquia'] ?? '') ?>"
                  placeholder="Se completa con el mapa o escríbelo">
              </div>
              <div class="jornada_edit-field">
                <label for="detalle">Detalle / Referencia</label>
                <input type="text" class="jornada_edit-input" name="detalle" id="detalle"
                  value="<?= esc($jornada['detalle'] ?? '') ?>"
                  placeholder="Nombre de calle, punto de referencia, etc.">
              </div>
            </div>

            <input type="hidden" name="coords" id="coords" value="<?= esc($jornada['coordenadas'] ?? '') ?>">
          </div>

          <!-- PESQUISAS -->
          <div class="jornada_edit-card">
            <div class="jornada_edit-card-title"><span>Pesquisas</span></div>
            <div class="mb-3">
              <strong>Seleccionadas para esta Jornada (<?= count($pesquisasActivas) ?>)</strong>
            </div>

            <div class="jornada_edit-chips mb-4" id="selectedPesquisasPreview">
              <?php if (!empty($pesquisasActivas)): ?>
                <?php foreach ($pesquisasActivas as $pes): ?>
                  <div class="jornada_edit-chip">
                    <div class="jornada_edit-chip-icon <?= esc($pes['clase']) ?>"><?= esc($pes['emoji']) ?></div>
                    <span><?= esc($pes['nombre']) ?></span>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <span class="jornada_edit-label-muted">No hay pesquisas seleccionadas.</span>
              <?php endif; ?>
            </div>

            <label class="form-label">Seleccionar Pesquisas (al menos una)</label>

            <div class="jornada_edit-pesquisa-selector">
              <?php foreach ($pesquisas as $p):
                $id  = $p['idtipo_pesquisa'];
                $ico = $iconos_color[$id] ?? null;
                if (!$ico) continue;
                $checked = in_array($id, $pesquisasSeleccionadas) ? 'checked' : '';
              ?>
                <label class="jornada_edit-pesquisa-item">
                  <input type="checkbox" name="pesquisas[]" value="<?= $id ?>" <?= $checked ?>
                    data-nombre="<?= esc($ico['nombre']) ?>"
                    data-emoji="<?= esc($ico['emoji']) ?>"
                    data-clase="<?= esc($ico['clase']) ?>">
                  <div class="jornada_edit-pesquisa-icon-wrap">
                    <img src="<?= base_url('img/' . $ico['color']) ?>" class="jornada_edit-icon-gris" alt="<?= esc($ico['nombre']) ?>">
                    <img src="<?= base_url('img/' . $ico['gris']) ?>" class="jornada_edit-icon-color" alt="<?= esc($ico['nombre']) ?>">
                  </div>
                  <span class="jornada_edit-pesquisa-label"><?= esc($ico['nombre']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="text-danger mt-3" id="pesquisaError" style="display:none;">
              Selecciona al menos una pesquisa.
            </div>
          </div>

        </section>

        <!-- RESUMEN -->
        <aside class="jornada_edit-right-col">
          <div class="jornada_edit-card-modern">
            <div class="jornada_edit-card-title-modern"><span>Resumen</span></div>
            <div class="jornada_edit-summary-list">
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Estado:</span>
                <span id="resumenEstado" class="<?= $jornada['status_jor'] == 1 ? 'jornada_edit-badge-success-modern' : 'jornada_edit-badge-danger-modern' ?>">
                  <?= $jornada['status_jor'] == 1 ? 'Activa' : 'Finalizada' ?>
                </span>
              </div>
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Fecha:</span>
                <strong id="resumenFecha"><?= esc($jornada['fecha_inicio']) ?></strong>
              </div>
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Nombre:</span>
                <strong id="resumenNombre"><?= esc($jornada['nombre_jornada']) ?></strong>
              </div>
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Organización:</span>
                <strong id="resumenOrganizacion"><?= esc($jornada['nombre_org'] ?? '') ?></strong>
              </div>
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Tipo:</span>
                <strong id="resumenTipo"><?= esc(ucfirst($jornada['tipo_jornada'] ?? '')) ?></strong>
              </div>
              <div class="jornada_edit-summary-item">
                <span class="jornada_edit-label-muted">Ubicación:</span>
                <strong id="resumenUbicacion">
                  &#128205; <?= esc(($jornada['ciudad'] ?? '') . (empty($jornada['estado']) ? '' : ', ' . $jornada['estado'])) ?>
                </strong>
              </div>
              <div class="jornada_edit-summary-item" style="display:block;">
                <div class="jornada_edit-label-muted" style="margin-bottom:10px;">Pesquisas seleccionadas:</div>
                <div id="resumenPesquisas">
                  <?php if (!empty($pesquisasActivas)): ?>
                    <div class="jornada_edit-chips">
                      <?php foreach ($pesquisasActivas as $pes): ?>
                        <div class="jornada_edit-chip">
                          <div class="jornada_edit-chip-icon <?= esc($pes['clase']) ?>"><?= esc($pes['emoji']) ?></div>
                          <span><?= esc($pes['nombre']) ?></span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <span class="jornada_edit-label-muted">No hay pesquisas seleccionadas.</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </main>

      <footer class="jornada_edit-footer">
        <div class="jornada_edit-note">
          Actualiza la jornada manteniendo la organización, ubicación y pesquisas correctamente asociadas.
        </div>
        <div class="jornada_edit-actions">
          <a href="<?= base_url('jornadas') ?>" class="jornada_edit-btn-modern jornada_edit-btn-modern-secondary">Cancelar</a>
          <button type="submit" class="jornada_edit-btn-modern jornada_edit-btn-modern-primary">Actualizar</button>
        </div>
      </footer>
    </form>
  </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
  let map, marker;

  const estadosVE = {
    'Distrito Capital': 'Distrito Capital',
    'Estado Miranda': 'Miranda',
    'Estado Zulia': 'Zulia',
    'Estado Aragua': 'Aragua',
    'Estado Carabobo': 'Carabobo',
    'Estado Anzoátegui': 'Anzoátegui',
    'Estado Barinas': 'Barinas',
    'Estado Bolívar': 'Bolívar',
    'Estado Falcón': 'Falcón',
    'Estado Lara': 'Lara',
    'Estado Mérida': 'Mérida',
    'Estado Portuguesa': 'Portuguesa',
    'Estado Sucre': 'Sucre',
    'Estado Táchira': 'Táchira',
    'Estado Trujillo': 'Trujillo',
    'Estado Vargas': 'La Guaira',
  };

  document.addEventListener('DOMContentLoaded', () => {
    const coordsValue = document.getElementById('coords').value;
    let initialLat = 10.4806,
      initialLon = -66.9036;

    if (coordsValue && coordsValue.includes(',')) {
      const parts = coordsValue.split(',');
      initialLat = parseFloat(parts[0].trim());
      initialLon = parseFloat(parts[1].trim());
    }

    map = L.map('map').setView([initialLat, initialLon], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([initialLat, initialLon], {
      draggable: true
    }).addTo(map);

    marker.on('dragend', async function(e) {
      const pos = e.target.getLatLng();
      await updateAddress(pos.lat, pos.lng, true);
    });

    map.on('click', async function(e) {
      const {
        lat,
        lng
      } = e.latlng;
      marker.setLatLng([lat, lng]);
      await updateAddress(lat, lng, true);
    });

    document.getElementById('searchPlace').addEventListener('keypress', async (e) => {
      if (e.key === 'Enter') {
        e.preventDefault();
        const query = e.target.value.trim();
        if (query.length < 3) return;

        const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1&countrycodes=ve`;
        try {
          const response = await fetch(url, {
            headers: {
              'Accept': 'application/json'
            }
          });
          const results = await response.json();
          if (results.length > 0) {
            const {
              lat,
              lon
            } = results[0];
            map.setView([lat, lon], 15);
            marker.setLatLng([lat, lon]);
            await updateAddress(lat, lon, true);
          }
        } catch (err) {
          console.error('Error buscando lugar:', err);
        }
      }
    });

    sincronizarResumen();

    document.getElementById('fecha_inicio').addEventListener('input', sincronizarResumen);
    document.getElementById('nombre_jornada').addEventListener('input', sincronizarResumen);
    document.getElementById('organizacion_id')?.addEventListener('change', sincronizarResumen);
    document.getElementById('status_jor')?.addEventListener('change', sincronizarResumen);
    document.querySelectorAll('input[name="tipo_jornada"]').forEach(r => r.addEventListener('change', sincronizarResumen));
    document.querySelectorAll('input[name="pesquisas[]"]').forEach(chk => chk.addEventListener('change', sincronizarResumen));
  });

  async function updateAddress(lat, lon, reverseGeocode) {
    document.getElementById('coords').value = `${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}`;
    if (!reverseGeocode) return;

    try {
      const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
      const response = await fetch(url, {
        headers: {
          'Accept': 'application/json'
        }
      });
      const data = await response.json();
      const addr = data.address || {};

      let pais = addr.country || '';
      let estado = addr.state || addr.region || addr.county || '';
      let ciudad = addr.city || addr.town || addr.village || addr.municipality || addr.suburb || addr.city_district || addr.county || '';

      // FIX: Limpiar "Estado "
      estado = estado.replace(/^Estado\s+/i, '');
      if (estadosVE[estado]) estado = estadosVE[estado];

      let municipio = addr.county || addr.municipality || '';
      let parroquia = addr.suburb || addr.city_district || addr.neighbourhood || '';

      document.getElementById('pais').value = pais;
      document.getElementById('estado').value = estado;
      document.getElementById('ciudad').value = ciudad;
      document.getElementById('municipio').value = municipio;
      document.getElementById('parroquia').value = parroquia;

      actualizarResumenUbicacion();
    } catch (err) {
      console.error('Error obteniendo dirección:', err);
    }
  }

  function sincronizarResumen() {
    const fecha = document.getElementById('fecha_inicio').value;
    const nombre = document.getElementById('nombre_jornada').value;
    const org = document.getElementById('organizacion_id');
    const tipo = document.querySelector('input[name="tipo_jornada"]:checked');
    const statusSel = document.getElementById('status_jor');

    document.getElementById('resumenFecha').textContent = fecha || 'Sin fecha';
    document.getElementById('resumenNombre').textContent = nombre || 'Sin nombre';
    document.getElementById('resumenOrganizacion').textContent = org ? org.options[org.selectedIndex].text : '-';
    document.getElementById('resumenTipo').textContent = tipo ? (tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1)) : '';

    if (statusSel) {
      const badge = document.getElementById('resumenEstado');
      badge.textContent = statusSel.value == 1 ? 'Activa' : 'Finalizada';
      badge.className = statusSel.value == 1 ? 'jornada_edit-badge-success-modern' : 'jornada_edit-badge-danger-modern';
    }

    actualizarResumenPesquisas();
    actualizarResumenUbicacion();
  }

  function actualizarResumenUbicacion() {
    const ciudad = document.getElementById('ciudad').value || '';
    const estado = document.getElementById('estado').value || '';
    const ubicacion = [ciudad, estado].filter(Boolean).join(', ');
    document.getElementById('resumenUbicacion').textContent = ubicacion ? `📍 ${ubicacion}` : '📍 Sin ubicación';
  }

  function actualizarResumenPesquisas() {
    const checks = document.querySelectorAll('input[name="pesquisas[]"]:checked');
    const preview = document.getElementById('selectedPesquisasPreview');
    const resumen = document.getElementById('resumenPesquisas');

    if (checks.length === 0) {
      preview.innerHTML = '<span class="jornada_edit-label-muted">No hay pesquisas seleccionadas.</span>';
      resumen.innerHTML = '<span class="jornada_edit-label-muted">No hay pesquisas seleccionadas.</span>';
      return;
    }

    let html = '';
    checks.forEach(chk => {
      const nombre = chk.dataset.nombre || '';
      const emoji = chk.dataset.emoji || '🩺';
      const clase = chk.dataset.clase || 'jornada_edit-blue';
      html += `<div class="jornada_edit-chip"><div class="jornada_edit-chip-icon ${clase}">${emoji}</div><span>${nombre}</span></div>`;
    });

    preview.innerHTML = html;
    resumen.innerHTML = `<div class="jornada_edit-chips">${html}</div>`;
  }

  document.getElementById('formJornada').addEventListener('submit', function(e) {
    const checks = document.querySelectorAll("input[name='pesquisas[]']:checked");
    if (checks.length === 0) {
      e.preventDefault();
      document.getElementById('pesquisaError').style.display = 'block';
    } else {
      document.getElementById('pesquisaError').style.display = 'none';
    }
  });

  // ═══ SELECT DE INSTITUCIONES CON SUGERENCIAS ═══
  (function() {
    const inputInst = document.getElementById('nombre_institucion');
    const hiddenId = document.getElementById('institucion_id');
    const sugBox = document.getElementById('institucion-sugerencias');
    let debounceTimer = null;

    if (!inputInst || !sugBox) return;

    inputInst.addEventListener('input', function() {
      const val = this.value.trim();
      hiddenId.value = '';
      clearTimeout(debounceTimer);
      if (val.length < 2) {
        sugBox.style.display = 'none';
        return;
      }

      debounceTimer = setTimeout(async () => {
        try {
          const resp = await fetch(`<?= base_url('jornadas/buscar-instituciones') ?>?q=${encodeURIComponent(val)}`);
          const data = await resp.json();
          if (data.length === 0) {
            sugBox.style.display = 'none';
            return;
          }

          sugBox.innerHTML = data.map(item =>
            `<div class="jornada_edit-inst-sugerencia" data-id="${item.id_institucion}" data-nombre="${item.nombre_institucion}">${item.nombre_institucion}</div>`
          ).join('');
          sugBox.style.display = 'block';

          sugBox.querySelectorAll('.jornada_edit-inst-sugerencia').forEach(el => {
            el.addEventListener('click', function() {
              inputInst.value = this.dataset.nombre;
              hiddenId.value = this.dataset.id;
              sugBox.style.display = 'none';
            });
          });
        } catch (e) {
          console.error('Error buscando instituciones:', e);
        }
      }, 300);
    });

    document.addEventListener('click', function(e) {
      if (!inputInst.contains(e.target) && !sugBox.contains(e.target)) sugBox.style.display = 'none';
    });
  })();
</script>

<?php if (session('success')): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: '<?= esc(session('success')) ?>',
      confirmButtonText: 'OK'
    }).then(() => {
      window.location.href = "<?= base_url('jornadas') ?>";
    });
  </script>
<?php endif; ?>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreJornada = $jornada['nombre_jornada'] ?? 'Jornada';
$jornadaId     = $jornadaId ?? ($jornada['id'] ?? null);
$datos         = $datos ?? [];

$semaforo = array_merge([
  'verde'    => 0,
  'amarillo' => 0,
  'naranja'  => 0,
  'rojo'     => 0,
  'gris'     => 0,
], $semaforo ?? []);

$contadores = array_merge([
  'total'        => 0,
  'masculinos'   => 0,
  'femeninas'    => 0,
  'adulto_19_60' => 0,
  'adulto_mayor' => 0,
], $contadores ?? []);

$semTotal = array_sum($semaforo);
$sinGris  = $semTotal - ($semaforo['gris'] ?? 0);

if (! function_exists('rep_ad_pct')) {
  function rep_ad_pct(int $val, int $total): string
  {
    if ($total <= 0) {
      return '0.0';
    }

    return number_format(($val * 100) / $total, 1);
  }
}

if (! function_exists('rep_ad_fecha')) {
  function rep_ad_fecha(?string $fecha): string
  {
    if (empty($fecha)) {
      return '—';
    }

    $time = strtotime($fecha);
    return $time ? date('d/m/Y', $time) : '—';
  }
}

if (! function_exists('rep_ad_iniciales')) {
  function rep_ad_iniciales(string $nombre): string
  {
    $nombre = trim($nombre);
    if ($nombre === '') {
      return 'NA';
    }

    $partes = preg_split('/\s+/', $nombre);
    $ini = '';

    foreach (array_slice($partes, 0, 2) as $p) {
      $ini .= mb_strtoupper(mb_substr($p, 0, 1));
    }

    return $ini ?: 'NA';
  }
}

$riesgos = [
  'verde' => [
    'label' => 'Peso adecuado / ECNT bajo',
    'icon'  => '🛡',
    'color' => '#2db463',
    'soft'  => '#e8f8ef',
  ],
  'amarillo' => [
    'label' => 'ECNT leve',
    'icon'  => '♡',
    'color' => '#f4c84a',
    'soft'  => '#fff7df',
  ],
  'naranja' => [
    'label' => 'ECNT moderado',
    'icon'  => '⚠',
    'color' => '#ff9429',
    'soft'  => '#fff0e2',
  ],
  'rojo' => [
    'label' => 'Atención inmediata',
    'icon'  => '🚨',
    'color' => '#ef5350',
    'soft'  => '#fdeaea',
  ],
  'gris' => [
    'label' => 'Datos insuficientes',
    'icon'  => '🗎',
    'color' => '#a7b1bf',
    'soft'  => '#f1f4f8',
  ],
];

 
?>

<style>
  :root {
    --rep_ad_bg: #f5f8fc;
    --rep_ad_panel: #ffffff;
    --rep_ad_line: #e6edf5;
    --rep_ad_text: #102c63;
    --rep_ad_muted: #6e7f99;
    --rep_ad_primary: #1f66e5;
    --rep_ad_primary_dark: #0d3b91;
    --rep_ad_green: #2db463;
    --rep_ad_green_soft: #e8f8ef;
    --rep_ad_yellow: #f4c84a;
    --rep_ad_yellow_soft: #fff7df;
    --rep_ad_orange: #ff9429;
    --rep_ad_orange_soft: #fff0e2;
    --rep_ad_red: #ef5350;
    --rep_ad_red_soft: #fdeaea;
    --rep_ad_gray: #a7b1bf;
    --rep_ad_gray_soft: #f1f4f8;
    --rep_ad_shadow: 0 10px 30px rgba(15, 40, 82, .08);
  }

  .rep_ad_app,
  .rep_ad_app * {
    box-sizing: border-box;
  }

  .rep_ad_app {
    min-height: calc(100vh - 118px);
    background: var(--rep_ad_bg);
    color: var(--rep_ad_text);
    padding: 24px 24px 32px;
    font-family: Arial, Helvetica, sans-serif;
  }

  .rep_ad_app button,
  .rep_ad_app input,
  .rep_ad_app select {
    font: inherit;
  }

  .rep_ad_main {
    width: 100%;
  }

  .rep_ad_topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 24px;
    margin-bottom: 18px;
    padding-bottom: 14px;
    border-bottom: 1px solid var(--rep_ad_line);
  }

  .rep_ad_breadcrumbs {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    color: var(--rep_ad_muted);
    font-size: 14px;
  }

  .rep_ad_breadcrumbs strong {
    color: var(--rep_ad_text);
  }

  .rep_ad_topbar_actions {
    display: flex;
    align-items: center;
    gap: 12px;
    flex-wrap: wrap;
  }

  .rep_ad_update_info {
    color: var(--rep_ad_muted);
    font-size: 14px;
    white-space: nowrap;
  }

  .rep_ad_btn {
    border: 0;
    border-radius: 12px;
    padding: 12px 18px;
    cursor: pointer;
    transition: .2s ease;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    min-height: 44px;
  }

  .rep_ad_btn_primary {
    background: var(--rep_ad_primary);
    color: #fff;
    box-shadow: 0 10px 22px rgba(31, 102, 229, .18);
  }

  .rep_ad_btn_primary:hover {
    background: #1658cb;
    color: #fff;
  }

  .rep_ad_btn_secondary {
    background: #fff;
    color: var(--rep_ad_primary);
    border: 1px solid #bfd4fb;
  }

  .rep_ad_btn_secondary:hover {
    background: #f5f9ff;
    color: var(--rep_ad_primary);
  }

  .rep_ad_hero {
    background: var(--rep_ad_panel);
    border: 1px solid var(--rep_ad_line);
    border-radius: 22px;
    padding: 22px 26px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 18px;
    box-shadow: var(--rep_ad_shadow);
    margin-bottom: 18px;
  }

  .rep_ad_hero_left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .rep_ad_hero_icon {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: #ffefbe;
    display: grid;
    place-items: center;
    flex: 0 0 52px;
  }

  .rep_ad_hero_icon img {
    width: 34px;
    height: 34px;
    object-fit: contain;
  }

  .rep_ad_hero h1 {
    margin: 0 0 4px;
    font-size: 22px;
    font-weight: 800;
  }

  .rep_ad_hero p {
    margin: 0;
    color: var(--rep_ad_primary_dark);
    font-weight: 800;
  }

  .rep_ad_hero_chip {
    padding: 10px 14px;
    border-radius: 999px;
    background: #f7faff;
    color: #5d74a0;
    border: 1px solid #dbe7fb;
    white-space: nowrap;
    font-size: 14px;
    font-weight: 700;
  }

  .rep_ad_kpi_grid {
    display: grid;
    grid-template-columns: repeat(9, minmax(0, 1fr));
    gap: 14px;
    margin-bottom: 18px;
  }

  .rep_ad_kpi_card {
    background: var(--rep_ad_panel);
    border: 1px solid var(--rep_ad_line);
    border-radius: 18px;
    padding: 18px 14px;
    min-height: 112px;
    box-shadow: var(--rep_ad_shadow);
    display: flex;
    gap: 12px;
    align-items: flex-start;
  }

  .rep_ad_kpi_icon {
    width: 44px;
    height: 44px;
    border-radius: 14px;
    display: grid;
    place-items: center;
    font-size: 22px;
    flex: 0 0 44px;
  }

  .rep_ad_kpi_value {
    font-size: 20px;
    font-weight: 800;
    margin-bottom: 6px;
    color: var(--rep_ad_text);
  }

  .rep_ad_kpi_title {
    font-size: 13px;
    color: #445674;
    font-weight: 700;
    line-height: 1.35;
  }

  .rep_ad_kpi_subtitle {
    margin-top: 6px;
    font-size: 13px;
    color: #7a8aa6;
    font-weight: 800;
  }

  .rep_ad_risk_summary {
    background: var(--rep_ad_panel);
    border: 1px solid var(--rep_ad_line);
    border-radius: 18px;
    padding: 16px 18px;
    box-shadow: var(--rep_ad_shadow);
    margin-bottom: 18px;
  }

  .rep_ad_risk_bar {
    display: flex;
    width: 100%;
    height: 18px;
    background: #edf2f8;
    border-radius: 999px;
    overflow: hidden;
    margin-bottom: 14px;
  }

  .rep_ad_risk_segment {
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 800;
    color: #fff;
    min-width: 0;
  }

  .rep_ad_risk_legend {
    display: flex;
    flex-wrap: wrap;
    gap: 18px;
    justify-content: center;
  }

  .rep_ad_legend_item {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #536783;
    font-size: 14px;
    font-weight: 700;
  }

  .rep_ad_legend_dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
  }

  .rep_ad_filters_card,
  .rep_ad_table_card {
    background: var(--rep_ad_panel);
    border: 1px solid var(--rep_ad_line);
    border-radius: 18px;
    box-shadow: var(--rep_ad_shadow);
  }

  .rep_ad_filters_card {
    padding: 18px;
    margin-bottom: 18px;
  }

  .rep_ad_filters_row {
    display: grid;
    grid-template-columns: 2fr repeat(5, minmax(120px, 1fr)) auto;
    gap: 14px;
    align-items: end;
  }

  .rep_ad_field {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }

  .rep_ad_field label {
    font-size: 13px;
    color: #506685;
    font-weight: 800;
  }

  .rep_ad_field input,
  .rep_ad_field select {
    width: 100%;
    height: 44px;
    border: 1px solid #d7e1ee;
    border-radius: 12px;
    background: #fff;
    padding: 0 14px;
    color: var(--rep_ad_text);
    outline: 0;
  }

  .rep_ad_field input:focus,
  .rep_ad_field select:focus {
    border-color: #9bbcf7;
    box-shadow: 0 0 0 4px rgba(31, 102, 229, .08);
  }

  .rep_ad_field_action .rep_ad_btn {
    width: 100%;
    height: 44px;
  }

  .rep_ad_table_card {
    padding: 0;
    overflow: hidden;
  }

  .rep_ad_table_top {
    padding: 14px 18px;
    display: flex;
    justify-content: space-between;
    gap: 20px;
    align-items: center;
    border-bottom: 1px solid var(--rep_ad_line);
  }

  .rep_ad_table_meta {
    color: #5f7390;
    font-size: 14px;
    font-weight: 700;
  }

  .rep_ad_table_statuses {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 14px;
    color: #5f7390;
    font-size: 14px;
    font-weight: 700;
  }

  .rep_ad_dot_item {
    display: flex;
    align-items: center;
    gap: 6px;
  }

  .rep_ad_dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
  }

  .rep_ad_dot_green { background: var(--rep_ad_green); }
  .rep_ad_dot_yellow { background: var(--rep_ad_yellow); }
  .rep_ad_dot_orange { background: var(--rep_ad_orange); }
  .rep_ad_dot_red { background: var(--rep_ad_red); }
  .rep_ad_dot_gray { background: var(--rep_ad_gray); }

  .rep_ad_table_wrapper {
    overflow: auto;
  }

  .rep_ad_table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    min-width: 1320px;
  }

  .rep_ad_table thead th {
    text-align: left;
    font-size: 13px;
    color: #23406d;
    background: #f7faff;
    padding: 16px 12px;
    border-bottom: 1px solid var(--rep_ad_line);
    white-space: nowrap;
    position: sticky;
    top: 0;
    z-index: 2;
  }

  .rep_ad_table thead th small {
    color: #c9cacd;
    font-size: 12px;
    font-weight: 700;
  }

  .rep_ad_table tbody td {
    padding: 14px 12px;
    border-bottom: 1px solid #edf2f8;
    vertical-align: middle;
    font-size: 14px;
    color: #2d466e;
  }

  .rep_ad_table tbody tr:hover {
    background: #fbfdff;
  }

  .rep_ad_name_cell {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 220px;
  }

  .rep_ad_avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    color: #fff;
    font-size: 11px;
    font-weight: 800;
    flex-shrink: 0;
  }

  .rep_ad_badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 8px 10px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 800;
    line-height: 1.3;
    min-width: 180px;
  }

  .rep_ad_badge_verde {
    color: #198c48;
    background: var(--rep_ad_green_soft);
  }

  .rep_ad_badge_amarillo {
    color: #9f7a00;
    background: var(--rep_ad_yellow_soft);
  }

  .rep_ad_badge_naranja {
    color: #b96b14;
    background: var(--rep_ad_orange_soft);
  }

  .rep_ad_badge_rojo {
    color: #c63e3d;
    background: var(--rep_ad_red_soft);
  }

  .rep_ad_badge_gris {
    color: #66758a;
    background: var(--rep_ad_gray_soft);
  }

  .rep_ad_empty {
    padding: 44px 18px !important;
    text-align: center;
    color: #7a8aa6 !important;
  }

  .rep_ad_empty i {
    display: block;
    font-size: 30px;
    margin-bottom: 8px;
  }

  .rep_ad_table_card .dataTables_wrapper {
    padding: 0;
  }

  .rep_ad_table_card .dataTables_length,
  .rep_ad_table_card .dataTables_filter {
    display: none;
  }

  .rep_ad_table_card .dataTables_info {
    padding: 16px 18px !important;
    color: #5f7390 !important;
    font-size: 14px;
    font-weight: 700;
  }

  .rep_ad_table_card .dataTables_paginate {
    padding: 12px 18px 16px !important;
  }

  .rep_ad_table_card .dataTables_paginate .paginate_button {
    min-width: 36px;
    height: 36px;
    border-radius: 10px !important;
    border: 1px solid #d7e1ee !important;
    background: #fff !important;
    color: #436089 !important;
    font-weight: 800;
    margin: 0 3px;
  }

  .rep_ad_table_card .dataTables_paginate .paginate_button.current,
  .rep_ad_table_card .dataTables_paginate .paginate_button.current:hover {
    background: var(--rep_ad_primary) !important;
    border-color: var(--rep_ad_primary) !important;
    color: #fff !important;
  }

  @media (max-width: 1600px) {
    .rep_ad_kpi_grid {
      grid-template-columns: repeat(3, 1fr);
    }
  }

  @media (max-width: 1280px) {
    .rep_ad_filters_row {
      grid-template-columns: repeat(3, 1fr);
    }

    .rep_ad_field_search {
      grid-column: span 3;
    }
  }

  @media (max-width: 900px) {
    .rep_ad_app {
      padding: 16px;
    }

    .rep_ad_topbar,
    .rep_ad_hero,
    .rep_ad_table_top {
      flex-direction: column;
      align-items: flex-start;
    }

    .rep_ad_kpi_grid {
      grid-template-columns: repeat(2, 1fr);
    }

    .rep_ad_filters_row {
      grid-template-columns: 1fr;
    }

    .rep_ad_field_search {
      grid-column: auto;
    }
  }

  @media (max-width: 640px) {
    .rep_ad_kpi_grid {
      grid-template-columns: 1fr;
    }
  }
</style>

<div class="rep_ad_app">
  <main class="rep_ad_main">
    <div class="rep_ad_topbar">
      <div class="rep_ad_breadcrumbs">
        <span>Reportes</span>
        <span>›</span>
        <span>Antropometría</span>
        <span>›</span>
        <strong>Reporte antropométrico</strong>
      </div>

      <div class="rep_ad_topbar_actions">
        

        <a href="<?= site_url("jornadas/{$jornadaId}/reportes/antropometria/adultos/excel") ?>" class="rep_ad_btn rep_ad_btn_primary">
          <i class="bi bi-file-earmark-excel-fill"></i>
          Exportar Excel
        </a>

        <a href="<?= site_url("jornadas/{$jornadaId}/reportes") ?>" class="rep_ad_btn rep_ad_btn_secondary">
          ← Volver a reportes
        </a>
      </div>
</div>

    <section class="rep_ad_hero">
      <div class="rep_ad_hero_left">
        <div class="rep_ad_hero_icon">
          <img src="<?= base_url('img/antropometria2.svg') ?>" alt="Antropometría">
        </div>
        <div>
          <h1>Reporte antropométrico</h1>
          <p>Adultos (≥ 19 años)</p>
        </div>
      </div>

      <div class="rep_ad_hero_chip">Antropometría - <?= esc($nombreJornada) ?></div>
    </section>

    <section class="rep_ad_kpi_grid">
      <article class="rep_ad_kpi_card">
        <div class="rep_ad_kpi_icon" style="background:#edf4ff; color:#1f66e5">👥</div>
        <div>
          <div class="rep_ad_kpi_value"><?= esc((string)$contadores['total']) ?></div>
          <div class="rep_ad_kpi_title">Total evaluados</div>
        </div>
      </article>

      <article class="rep_ad_kpi_card">
        <div class="rep_ad_kpi_icon" style="background:#f3efff; color:#7c3aed">🚻</div>
        <div>
          <div class="rep_ad_kpi_value"><?= esc((string)$contadores['masculinos']) ?> / <?= esc((string)$contadores['femeninas']) ?></div>
          <div class="rep_ad_kpi_title">Masculino / Femenino</div>
        </div>
      </article>

      <article class="rep_ad_kpi_card">
        <div class="rep_ad_kpi_icon" style="background:#edf4ff; color:#2196f3">🧑</div>
        <div>
          <div class="rep_ad_kpi_value"><?= esc((string)$contadores['adulto_19_60']) ?></div>
          <div class="rep_ad_kpi_title">19 - 60 años</div>
        </div>
      </article>

      <article class="rep_ad_kpi_card">
        <div class="rep_ad_kpi_icon" style="background:#f5efff; color:#9b5de5">👴</div>
        <div>
          <div class="rep_ad_kpi_value"><?= esc((string)$contadores['adulto_mayor']) ?></div>
          <div class="rep_ad_kpi_title">> 60 años</div>
        </div>
      </article>

      <?php foreach ($riesgos as $key => $cfg): ?>
        <?php $pctBase = $key === 'gris' ? $semTotal : $sinGris; ?>
        <article class="rep_ad_kpi_card">
          <div class="rep_ad_kpi_icon" style="background:<?= esc($cfg['soft']) ?>; color:<?= esc($cfg['color']) ?>">
            <?= esc($cfg['icon']) ?>
          </div>
          <div>
            <div class="rep_ad_kpi_value"><?= esc((string)$semaforo[$key]) ?></div>
            <div class="rep_ad_kpi_title"><?= esc($cfg['label']) ?></div>
            <div class="rep_ad_kpi_subtitle"><?= esc(rep_ad_pct((int)$semaforo[$key], (int)$pctBase)) ?>%</div>
          </div>
        </article>
      <?php endforeach; ?>
    </section>

    <section class="rep_ad_risk_summary">
      <div class="rep_ad_risk_bar">
        <?php foreach ($riesgos as $key => $cfg): ?>
          <?php
          $valor = (int)($semaforo[$key] ?? 0);
          $width = $semTotal > 0 ? (($valor / $semTotal) * 100) : 0;
          ?>
          <?php if ($width > 0): ?>
            <div class="rep_ad_risk_segment" style="width:<?= esc(number_format($width, 4, '.', '')) ?>%; background:<?= esc($cfg['color']) ?>">
              <?= esc(rep_ad_pct($valor, (int)$semTotal)) ?>%
            </div>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>

      <div class="rep_ad_risk_legend">
        <?php foreach ($riesgos as $cfg): ?>
          <div class="rep_ad_legend_item">
            <span class="rep_ad_legend_dot" style="background:<?= esc($cfg['color']) ?>"></span>
            <span><?= esc($cfg['label']) ?></span>
          </div>
        <?php endforeach; ?>
      </div>
    </section>

    <section class="rep_ad_filters_card">
      <div class="rep_ad_filters_row">
        <div class="rep_ad_field rep_ad_field_search">
          <label for="rep_ad_searchInput">Buscar</label>
          <input type="text" id="rep_ad_searchInput" placeholder="Buscar por nombre, cédula u observación...">
        </div>

        <div class="rep_ad_field">
          <label for="rep_ad_sexoFilter">Sexo</label>
          <select id="rep_ad_sexoFilter">
            <option value="">Todos</option>
            <option value="M">Masculino</option>
            <option value="F">Femenino</option>
          </select>
        </div>

        <div class="rep_ad_field">
          <label for="rep_ad_ageFilter">Grupo etario</label>
          <select id="rep_ad_ageFilter">
            <option value="">Todos</option>
            <option value="19-60">19 - 60 años</option>
            <option value="60+">> 60 años</option>
          </select>
        </div>

        <div class="rep_ad_field">
          <label for="rep_ad_riskFilter">Estado de riesgo</label>
          <select id="rep_ad_riskFilter">
            <option value="">Todos</option>
            <option value="verde">Peso adecuado / ECNT bajo</option>
            <option value="amarillo">ECNT leve</option>
            <option value="naranja">ECNT moderado</option>
            <option value="rojo">Atención inmediata</option>
            <option value="gris">Datos insuficientes</option>
          </select>
        </div>

        <div class="rep_ad_field">
          <label for="rep_ad_rowsPerPage">Registros por página</label>
          <select id="rep_ad_rowsPerPage">
            <option value="10">10</option>
            <option value="25" selected>25</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
        </div>

        <div class="rep_ad_field rep_ad_field_action">
          <label>&nbsp;</label>
          <button type="button" class="rep_ad_btn rep_ad_btn_secondary" id="rep_ad_clearFilters">
            Limpiar filtros
          </button>
        </div>
      </div>
    </section>

    <section class="rep_ad_table_card">
      <div class="rep_ad_table_top">
        <div class="rep_ad_table_meta" id="rep_ad_tableMeta">Detalle por beneficiario</div>

        <div class="rep_ad_table_statuses">
          <span>Estados de interpretación</span>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_green"></i><span>Bajo</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_yellow"></i><span>Leve</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_orange"></i><span>Moderado</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_red"></i><span>Alto</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_gray"></i><span>Sin datos</span></div>
        </div>
      </div>

      <div class="rep_ad_table_wrapper">
        <table id="tablaAdultos" class="rep_ad_table">
          <thead>
            <tr>
              <th>#</th>
              <th>Nombre</th>
              <th>Identificación</th>
              <th>Sexo</th>
              <th>Fecha nac.</th>
              <th>Fecha eval.</th>
              <th>Edad</th>
              <th>Interpretación combinada</th>
              <th>Peso<br><small>(kg)</small></th>
              <th>Talla<br><small>(cm)</small></th>
              <th>IMC<br><small>(kg/m²)</small></th>
              <th>C. cintura<br><small>(cm)</small></th>
              <th>Edema</th>
              <th>Remisión</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($datos)): ?>
              <tr>
                <td colspan="15" class="rep_ad_empty">
                  <i class="bi bi-inbox"></i>
                  No hay registros de adultos en esta jornada.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($datos as $index => $d): ?>
                <?php
                $clase = $d['_clase'] ?? 'gris';
                if (! array_key_exists($clase, $riesgos)) {
                  $clase = 'gris';
                }

                $interp  = $d['_interpretacion'] ?? '—';
                $peso    = $d['peso'] ?? null;
                $talla   = $d['talla'] ?? null;
                $imc     = $d['imc'] ?? null;
                $cintura = $d['circ_cintura'] ?? null;
                $fnac    = $d['fecha_nacimiento'] ?? '';
                $fnac_f  = rep_ad_fecha($fnac ?: null);
                $fechaEval = $d['fecha_evaluacion'] ?? '';
                $fechaEval_f = rep_ad_fecha($fechaEval ?: null);
                $dias    = (float)($d['edad_dias_medicion'] ?? 0);
                $edadStr = '—';
                $edadGrupo = '';

                if ($dias > 0) {
                  $a = (int)floor($dias / 365.25);
                  $m = (int)floor(fmod($dias, 365.25) / 30.44);
                  $edadStr = "{$a} a. {$m} m.";
                  $edadGrupo = $a > 60 ? '60+' : '19-60';
                }

                $nombreCompleto = ucwords(strtolower((string)($d['nombre_completo'] ?? '')));
                $sexo = (string)($d['_sexo'] ?? '');
             
                ?>
                <tr data-risk="<?= esc($clase) ?>" data-sexo="<?= esc($sexo) ?>" data-age="<?= esc($edadGrupo) ?>">
                  <td><?= esc((string)($index + 1)) ?></td>
                  <td><?= esc($nombreCompleto ?: '—') ?></td>
                  <td><?= esc($d['id_digisalud'] ?? '—') ?></td>
                  <td><?= esc($sexo ?: '—') ?></td>
                  <td><?= esc($fnac_f) ?></td>
                  <td><?= esc($fechaEval_f) ?></td>
                  <td><?= esc($edadStr) ?></td>
                  <td>
                    <span class="rep_ad_badge rep_ad_badge_<?= esc($clase) ?>">
                      <?= esc($interp) ?>
                    </span>
                  </td>
                  <td><?= $peso !== null ? esc(number_format((float)$peso, 2)) : '—' ?></td>
                  <td><?= $talla !== null ? esc(number_format((float)$talla, 1)) : '—' ?></td>
                  <td><?= $imc !== null ? esc(number_format((float)$imc, 2)) : '—' ?></td>
                  <td><?= $cintura !== null && (float)$cintura > 0 ? esc(number_format((float)$cintura, 1)) : '—' ?></td>
                  <td><?= !empty($d['edema']) ? '<span class="badge bg-warning text-dark">Sí</span>' : 'No' ?></td>
                  <td><?= !empty($d['remision']) ? esc(ucfirst((string)$d['remision'])) : '—' ?></td>
                  <td class="text-muted" style="max-width:220px; white-space:normal"><?= esc($d['observaciones'] ?? '—') ?></td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>

<script>
  (function() {
    const hasRows = <?= empty($datos) ? 'false' : 'true' ?>;

    function repAdNormalize(value) {
      return String(value || '')
        .trim()
        .toLowerCase()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '');
    }

    function repAdNormalizeSexo(value) {
      const v = repAdNormalize(value);

      if (v === 'm' || v.includes('masculino') || v.includes('hombre')) {
        return 'M';
      }

      if (v === 'f' || v.includes('femenino') || v.includes('mujer')) {
        return 'F';
      }

      return String(value || '').trim().toUpperCase();
    }

    function repAdGetCellText(row, index) {
      return row && row.cells && row.cells[index]
        ? row.cells[index].textContent.trim()
        : '';
    }

    function repAdGetAgeGroup(row) {
      const explicitAge = row.getAttribute('data-age') || '';

      if (explicitAge) {
        return explicitAge;
      }

      const edadText = repAdGetCellText(row, 5);
      const match = edadText.match(/(\d+)/);
      const years = match ? parseInt(match[1], 10) : 0;

      if (!years) {
        return '';
      }

      return years > 60 ? '60+' : '19-60';
    }

    function repAdRowPassesFilters(row) {
      if (!row) {
        return true;
      }

      const search = repAdNormalize(document.getElementById('rep_ad_searchInput')?.value || '');
      const sexo = document.getElementById('rep_ad_sexoFilter')?.value || '';
      const age = document.getElementById('rep_ad_ageFilter')?.value || '';
      const risk = document.getElementById('rep_ad_riskFilter')?.value || '';

      const rowText = repAdNormalize(row.textContent || '');
      const rowSexo = repAdNormalizeSexo(row.getAttribute('data-sexo') || repAdGetCellText(row, 3));
      const rowAge = repAdGetAgeGroup(row);
      const rowRisk = row.getAttribute('data-risk') || '';

      if (search && !rowText.includes(search)) {
        return false;
      }

      if (sexo && rowSexo !== sexo) {
        return false;
      }

      if (age && rowAge !== age) {
        return false;
      }

      if (risk && rowRisk !== risk) {
        return false;
      }

      return true;
    }

    function repAdNativeFilter() {
      const rows = document.querySelectorAll('#tablaAdultos tbody tr[data-risk]');
      let visible = 0;

      rows.forEach(function(row) {
        const show = repAdRowPassesFilters(row);
        row.style.display = show ? '' : 'none';
        if (show) {
          visible++;
        }
      });

      const meta = document.getElementById('rep_ad_tableMeta');
      if (meta) {
        meta.textContent = 'Mostrando ' + visible + ' de ' + rows.length + ' registros';
      }
    }

    function repAdInit() {
      if (!hasRows) {
        return;
      }

      const $table = window.jQuery ? $('#tablaAdultos') : null;
      const hasDataTables = Boolean(window.jQuery && $.fn && $.fn.DataTable);

      if (!hasDataTables) {
        ['rep_ad_searchInput', 'rep_ad_sexoFilter', 'rep_ad_ageFilter', 'rep_ad_riskFilter'].forEach(function(id) {
          const el = document.getElementById(id);
          if (el) {
            el.addEventListener('input', repAdNativeFilter);
            el.addEventListener('change', repAdNativeFilter);
          }
        });

        const clearBtn = document.getElementById('rep_ad_clearFilters');
        if (clearBtn) {
          clearBtn.addEventListener('click', function() {
            document.getElementById('rep_ad_searchInput').value = '';
            document.getElementById('rep_ad_sexoFilter').value = '';
            document.getElementById('rep_ad_ageFilter').value = '';
            document.getElementById('rep_ad_riskFilter').value = '';
            document.getElementById('rep_ad_rowsPerPage').value = '25';
            repAdNativeFilter();
          });
        }

        repAdNativeFilter();
        return;
      }

      $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
        if (!settings || !settings.nTable || settings.nTable.id !== 'tablaAdultos') {
          return true;
        }

        const rowObj = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex] : null;
        const row = rowObj ? rowObj.nTr : null;

        return repAdRowPassesFilters(row);
      });

      const table = $table.DataTable({
        responsive: false,
        pageLength: 25,
        lengthChange: false,
        order: [[6, 'asc']],
        language: {
          url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
          search: '',
          searchPlaceholder: 'Buscar…',
          lengthMenu: 'Ver _MENU_ registros',
          zeroRecords: 'Sin resultados',
          info: 'Mostrando _START_ a _END_ de _TOTAL_ registros',
          infoEmpty: '0 registros',
          infoFiltered: '(filtrado de _MAX_ registros)',
          paginate: {
            previous: '‹',
            next: '›'
          }
        },
        dom: 'rt<"rep_ad_dt_footer"ip>',
        columnDefs: [{
          orderable: false,
          targets: [0, 12, 13, 14]
        }]
      });

      function repAdRedraw() {
        table.draw();
      }

      $('#rep_ad_searchInput').on('input keyup change', repAdRedraw);
      $('#rep_ad_sexoFilter, #rep_ad_ageFilter, #rep_ad_riskFilter').on('input change', repAdRedraw);

      $('#rep_ad_rowsPerPage').on('change', function() {
        table.page.len(Number(this.value || 25)).draw();
      });

      $('#rep_ad_clearFilters').on('click', function() {
        $('#rep_ad_searchInput').val('');
        $('#rep_ad_sexoFilter').val('');
        $('#rep_ad_ageFilter').val('');
        $('#rep_ad_riskFilter').val('');
        $('#rep_ad_rowsPerPage').val('25');

        table.search('').page.len(25).draw();
      });
    }

    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', repAdInit);
    } else {
      repAdInit();
    }
  })();
</script>

<?= $this->endSection() ?>

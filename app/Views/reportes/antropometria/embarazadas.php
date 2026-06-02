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
  'total' => 0,
], $contadores ?? []);

$semTotal = array_sum($semaforo);
$sinGris  = $semTotal - ($semaforo['gris'] ?? 0);

if (! function_exists('pctE')) {
  function pctE(int $val, int $total): string
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

$riesgos = [
  'verde' => [
    'label' => 'Ganancia adecuada',
    'icon'  => '🛡',
    'color' => '#2db463',
    'soft'  => '#e8f8ef',
  ],
  'naranja' => [
    'label' => 'Ganancia excesiva',
    'icon'  => '⚠',
    'color' => '#ff9429',
    'soft'  => '#fff0e2',
  ],
  'rojo' => [
    'label' => 'Ganancia insuficiente',
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

  .rep_ad_risk_summary,
  .rep_ad_iom_card,
  .rep_ad_filters_card,
  .rep_ad_table_card {
    background: var(--rep_ad_panel);
    border: 1px solid var(--rep_ad_line);
    border-radius: 18px;
    box-shadow: var(--rep_ad_shadow);
  }

  .rep_ad_risk_summary {
    padding: 16px 18px;
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

  .rep_ad_iom_card {
    padding: 16px 20px;
    margin-bottom: 18px;
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
  }

  .rep_ad_iom_card h6 {
    color: var(--rep_ad_text);
    font-weight: 800;
    margin: 0 12px 0 0;
    white-space: nowrap;
  }

  .iom-chip {
    padding: 5px 14px;
    border-radius: 20px;
    font-size: .78rem;
    font-weight: 700;
    border: 1px solid transparent;
  }

  .iom-bajo { background: var(--rep_ad_red_soft); color: #991B1B; border-color: #FCA5A5; }
  .iom-normal { background: var(--rep_ad_green_soft); color: #065F46; border-color: #6EE7B7; }
  .iom-sobre { background: var(--rep_ad_yellow_soft); color: #78350F; border-color: #FDE68A; }
  .iom-obesi { background: var(--rep_ad_orange_soft); color: #9A3412; border-color: #FDBA74; }

  .rep_ad_filters_card {
    padding: 18px;
    margin-bottom: 18px;
  }

  .rep_ad_filters_row {
    display: grid;
    grid-template-columns: 2fr repeat(4, minmax(120px, 1fr)) auto;
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
    flex-wrap: wrap;
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

  #tablaMenores {
    min-width: 1520px;
  }

  #tablaEmbarazadas {
    min-width: 1420px;
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
    color: #8a97aa;
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

  .sem-dot {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: inline-block;
    margin: 0 auto;
  }

  .sem-dot.verde { background: var(--rep_ad_green); }
  .sem-dot.amarillo { background: var(--rep_ad_yellow); }
  .sem-dot.naranja { background: var(--rep_ad_orange); }
  .sem-dot.rojo { background: var(--rep_ad_red); }
  .sem-dot.gris { background: var(--rep_ad_gray); }

  .interp-badge,
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
    white-space: normal;
  }

  .interp-verde,
  .rep_ad_badge_verde {
    color: #198c48;
    background: var(--rep_ad_green_soft);
  }

  .interp-amarillo,
  .rep_ad_badge_amarillo {
    color: #9f7a00;
    background: var(--rep_ad_yellow_soft);
  }

  .interp-naranja,
  .rep_ad_badge_naranja {
    color: #b96b14;
    background: var(--rep_ad_orange_soft);
  }

  .interp-rojo,
  .rep_ad_badge_rojo {
    color: #c63e3d;
    background: var(--rep_ad_red_soft);
  }

  .interp-gris,
  .rep_ad_badge_gris {
    color: #66758a;
    background: var(--rep_ad_gray_soft);
  }

  .zscore-chip {
    display: inline-block;
    padding: 2px 7px;
    border-radius: 6px;
    font-size: .76rem;
    font-weight: 600;
  }

  .zs-ok {
    background: #D1FAE5;
    color: #065F46;
  }

  .zs-warn {
    background: #FEF9C3;
    color: #78350F;
  }

  .zs-bad {
    background: #FEE2E2;
    color: #991B1B;
  }

  .zs-null {
    color: #9CA3AF;
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
        <a href="<?= site_url("jornadas/{$jornadaId}/reportes/antropometria/embarazadas/excel") ?>" class="rep_ad_btn rep_ad_btn_primary">
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
          <p>Embarazadas</p>
        </div>
      </div>

      <div class="rep_ad_hero_chip">Antropometría - <?= esc($nombreJornada) ?></div>
    </section>

    <section class="rep_ad_kpi_grid">
      <article class="rep_ad_kpi_card">
        <div class="rep_ad_kpi_icon" style="background:#edf4ff; color:#1f66e5">🤰</div>
        <div>
          <div class="rep_ad_kpi_value"><?= esc((string)$contadores['total']) ?></div>
          <div class="rep_ad_kpi_title">Total embarazadas evaluadas</div>
        </div>
      </article>

      <?php foreach ($riesgos as $key => $cfg): ?>
        <?php $pctBase = $key === 'gris' ? $semTotal : $sinGris; ?>
        <article class="rep_ad_kpi_card">
          <div class="rep_ad_kpi_icon" style="background:<?= esc($cfg['soft']) ?>; color:<?= esc($cfg['color']) ?>">
            <?= esc($cfg['icon']) ?>
          </div>
          <div>
            <div class="rep_ad_kpi_value"><?= esc((string)($semaforo[$key] ?? 0)) ?></div>
            <div class="rep_ad_kpi_title"><?= esc($cfg['label']) ?></div>
            <div class="rep_ad_kpi_subtitle"><?= esc(pctE((int)($semaforo[$key] ?? 0), (int)$pctBase)) ?>%</div>
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
              <?= esc(pctE($valor, (int)$semTotal)) ?>%
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

    <section class="rep_ad_iom_card">
      <h6><i class="bi bi-info-circle me-1"></i>Criterios IOM 2009 — Ganancia de peso gestacional</h6>
      <span class="iom-chip iom-bajo">Bajo peso (&lt;18.5): 12.5–18 kg</span>
      <span class="iom-chip iom-normal">Peso normal (18.5–24.9): 11.5–16 kg</span>
      <span class="iom-chip iom-sobre">Sobrepeso (25–29.9): 7–11.5 kg</span>
      <span class="iom-chip iom-obesi">Obesidad (&gt;30): 5–9 kg</span>
      <small class="text-muted ms-2">Los rangos se ajustan proporcionalmente según semanas de gestación</small>
    </section>

    <section class="rep_ad_table_card">
      <div class="rep_ad_table_top">
        <div class="rep_ad_table_meta">Detalle por beneficiaria</div>

        <div class="rep_ad_table_statuses">
          <span>Estados de interpretación</span>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_green"></i><span>Adecuada</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_orange"></i><span>Excesiva</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_red"></i><span>Insuficiente</span></div>
          <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_gray"></i><span>Sin datos</span></div>
        </div>
      </div>

      <div class="rep_ad_table_wrapper">
        <table id="tablaEmbarazadas" class="rep_ad_table">
          <thead>
            <tr>
              <th style="width:44px;text-align:center">🚦</th>
              <th>Nombre</th>
              <th>Cédula</th>
              <th>Fecha nac.</th>
              <th>Fecha eval.</th>
              <th>Edad</th>
              <th>Interpretación combinada</th>
              <th>Semanas<br><small>gestación</small></th>
              <th>Ganancia<br><small>kg</small></th>
              <th>IMC Pregest.</th>
              <th>Peso actual<br><small>kg</small></th>
              <th>Talla<br><small>cm</small></th>
              <th>FUM</th>
              <th>Fecha Eco</th>
              <th>Edema</th>
              <th>Observaciones</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($datos)): ?>
              <tr>
                <td colspan="16" class="rep_ad_empty">
                  <i class="bi bi-inbox"></i>
                  No hay registros de embarazadas en esta jornada.
                </td>
              </tr>
            <?php else: ?>
              <?php foreach ($datos as $d):
                $clase  = $d['_clase'] ?? 'gris';
                if (! array_key_exists($clase, $riesgos)) {
                  $clase = 'gris';
                }

                $interp = $d['_interpretacion'] ?? '—';
                $dias   = (float)($d['edad_dias_medicion'] ?? 0);
                $a      = $dias > 0 ? floor($dias / 365.25) : 0;
                $m      = $dias > 0 ? floor(fmod($dias, 365.25) / 30.44) : 0;

                $fnac        = $d['fecha_nacimiento'] ?? '';
                $fnac_f      = rep_ad_fecha($fnac ?: null);
                $fechaEval   = $d['fecha_evaluacion'] ?? '';
                $fechaEval_f = rep_ad_fecha($fechaEval ?: null);

                $fum   = $d['embarazo_fum'] ?? '';
                $fum_f = rep_ad_fecha($fum ?: null);
                $eco   = $d['embarazo_fecha_eco'] ?? '';
                $eco_f = rep_ad_fecha($eco ?: null);

                $semanas = (int)($d['_semanas_calc'] ?? 0);
                $nombreCompleto = ucwords(strtolower((string)($d['nombre_completo'] ?? '')));
              ?>
                <tr data-risk="<?= esc($clase) ?>">
                  <td><span class="sem-dot <?= esc($clase) ?>"></span></td>
                  <td><?= esc($nombreCompleto ?: '—') ?></td>
                  <td><?= esc($d['id_digisalud'] ?? $d['cedula'] ?? '—') ?></td>
                  <td><?= esc($fnac_f) ?></td>
                  <td><?= esc($fechaEval_f) ?></td>
                  <td><?= $dias > 0 ? "{$a} a. {$m} m." : '—' ?></td>
                  <td><span class="interp-badge interp-<?= esc($clase) ?>"><?= esc($interp) ?></span></td>
                  <td class="text-center fw-600">
                    <?= $semanas > 0 ? "<strong>{$semanas}</strong> sem." : '<span class="text-muted">—</span>' ?>
                  </td>
                  <td class="text-center fw-600">
                    <?= ($d['_ganancia'] ?? '—') !== '—'
                        ? "<strong>{$d['_ganancia']}</strong>"
                        : '<span class="text-muted">—</span>' ?>
                  </td>
                  <td class="text-center"><?= esc($d['_imc_preg'] ?? '—') ?></td>
                  <td><?= isset($d['peso']) && $d['peso'] ? esc(number_format((float)$d['peso'], 2)) : '—' ?></td>
                  <td><?= isset($d['talla']) && $d['talla'] ? esc(number_format((float)$d['talla'], 1)) : '—' ?></td>
                  <td><small><?= esc($fum_f) ?></small></td>
                  <td><small><?= esc($eco_f) ?></small></td>
                  <td><?= ($d['edema'] ?? 0) ? '<span class="badge bg-warning text-dark">Sí</span>' : 'No' ?></td>
                  <td class="text-muted" style="max-width:180px;white-space:normal"><?= esc($d['observaciones'] ?? '—') ?></td>
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
$(document).ready(function () {
  if ($.fn.DataTable) {
    $('#tablaEmbarazadas').DataTable({
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
        paginate: { previous: '‹', next: '›' },
      },
      dom: 'rt<"rep_ad_dt_footer"ip>',
      columnDefs: [{ orderable: false, targets: [0, 14, 15] }],
    });
  }
});
</script>

<?= $this->endSection() ?>

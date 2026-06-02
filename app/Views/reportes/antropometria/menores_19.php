<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreJornada = $jornada['nombre_jornada'] ?? 'Jornada';
$semTotal      = array_sum($semaforo);
$sinGris       = $semTotal - ($semaforo['gris'] ?? 0);

function pctM(int $val, int $total): string
{
  if ($total <= 0) return '0';
  return number_format(($val * 100) / $total, 1);
}
$riesgos = [
    'verde' => [
        'label' => 'Peso y talla adecuada',
        'color' => '#00B140',
    ],
    'amarillo' => [
        'label' => 'En riesgo',
        'color' => '#FFC609',
    ],
    'naranja' => [
        'label' => 'Leve / Moderado',
        'color' => '#FF8724',
    ],
    'rojo' => [
        'label' => 'Atención inmediata',
        'color' => '#E43312',
    ],
    'gris' => [
        'label' => 'Revisar Datos',
        'color' => '#9CA3AF',
    ],
];
?>

<style>
  :root {
    --rp-primary: #101a61;
    --rp-cyan: #00D4FF;
    --rp-verde: #00B140;
    --rp-amarillo: #FFC609;
    --rp-naranja: #FF8724;
    --rp-rojo: #E43312;
    --rp-gris: #9CA3AF;
    --rp-bg: #F0F4FA;
    --rp-card: #ffffff;
    --rp-radius: 12px;
  }

  .rp-shell {
    padding: 24px 28px 60px;
    background: var(--rp-bg);
    min-height: 100vh;
  }

  .rp-header {
    background: #fff;
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

  .rp-header-left {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .rp-header-icon {
    width: 52px;
    height: 52px;
    background: rgba(255, 255, 255, .12);
    border-radius: 12px;
    display: grid;
    place-items: center;
  }

  .rp-header-icon img {
    width: 36px;
  }

  .rp-header-sub {
    font-size: .8rem;
    opacity: .7;
    text-transform: uppercase;
    letter-spacing: .04em;
  }

  .rp-header-title {
    font-size: 1.4rem;
    font-weight: 700;
    margin: 0;
  }

  .rp-header-pill {
    background: rgba(255, 255, 255, .15);
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: .82rem;
  }

  .rp-btn-back {
    background: rgba(255, 255, 255, .18);
    border: 1px solid rgba(255, 255, 255, .3);
    color: #fff;
    border-radius: 8px;
    padding: 8px 18px;
    font-size: .85rem;
    text-decoration: none;
  }

  .rp-btn-back:hover {
    background: rgba(255, 255, 255, .3);
    color: #fff;
  }

  .rp-kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 14px;
    margin-bottom: 24px;
  }

  .rp-kpi {
    background: var(--rp-card);
    border-radius: var(--rp-radius);
    padding: 18px 16px;
    text-align: center;
    border-top: 4px solid #e5e7eb;
    box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
  }

  .rp-kpi.verde {
    border-top-color: var(--rp-verde);
  }

  .rp-kpi.amarillo {
    border-top-color: var(--rp-amarillo);
  }

  .rp-kpi.naranja {
    border-top-color: var(--rp-naranja);
  }

  .rp-kpi.rojo {
    border-top-color: var(--rp-rojo);
  }

  .rp-kpi.gris {
    border-top-color: var(--rp-gris);
  }

  .rp-kpi.total {
    border-top-color: var(--rp-cyan);
  }

  .rp-kpi-val {
    font-size: 2rem;
    font-weight: 800;
    color: var(--rp-primary);
    line-height: 1;
  }

  .rp-kpi-label {
    font-size: .75rem;
    color: #6B7280;
    margin-top: 4px;
  }

  .rp-kpi-pct {
    font-size: .8rem;
    color: #9CA3AF;
    margin-top: 2px;
  }

  .rp-dist-bar {
    display: flex;
    height: 10px;
    border-radius: 99px;
    overflow: hidden;
    margin-bottom: 24px;
    gap: 2px;
  }

  .rp-dist-seg {
    transition: flex .4s;
  }
   .rep_ad_risk_summary {
  background: var(--rp-card);
  border: 1px solid #EEF2F7;
  border-radius: var(--rp-radius);
  padding: 16px 18px;
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
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

.rep_ad_dot_green { background: var(--rp-verde); }
.rep_ad_dot_yellow { background: var(--rp-amarillo); }
.rep_ad_dot_orange { background: var(--rp-naranja); }
.rep_ad_dot_red { background: var(--rp-rojo); }
.rep_ad_dot_gray { background: var(--rp-gris); }
.rp-filters-card {
  background: var(--rp-card);
  border-radius: var(--rp-radius);
  box-shadow: 0 2px 12px rgba(0,0,0,.06);
  padding: 18px;
  margin-bottom: 18px;
  border: 1px solid #EEF2F7;
}

.rp-filters-row {
  display: grid;
  grid-template-columns: 2fr repeat(4, minmax(120px, 1fr)) auto;
  gap: 14px;
  align-items: end;
}

.rp-field {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.rp-field label {
  font-size: 13px;
  color: #506685;
  font-weight: 800;
}

.rp-field input,
.rp-field select {
  width: 100%;
  height: 44px;
  border: 1px solid #d7e1ee;
  border-radius: 12px;
  background: #fff;
  padding: 0 14px;
  color: var(--rp-primary);
  outline: 0;
}

.rp-field input:focus,
.rp-field select:focus {
  border-color: #9bbcf7;
  box-shadow: 0 0 0 4px rgba(31, 102, 229, .08);
}

.rp-btn-clear {
  width: 100%;
  height: 44px;
  border: 1px solid #bfd4fb;
  border-radius: 12px;
  background: #fff;
  color: #1f66e5;
  font-weight: 700;
  padding: 0 16px;
}

.rp-btn-clear:hover {
  background: #f5f9ff;
}

.rp-table-meta {
  color: #5f7390;
  font-size: 14px;
  font-weight: 700;
}

.rp-table-card .dataTables_length,
.rp-table-card .dataTables_filter {
  display: none;
}

.rp-table-card .dataTables_info {
  padding: 16px 18px !important;
  color: #5f7390 !important;
  font-size: 14px;
  font-weight: 700;
}

.rp-table-card .dataTables_paginate {
  padding: 12px 18px 16px !important;
}

@media (max-width: 1280px) {
  .rp-filters-row {
    grid-template-columns: repeat(3, 1fr);
  }

  .rp-field-search {
    grid-column: span 3;
  }
}

@media (max-width: 900px) {
  .rp-shell {
    padding: 16px;
  }

  .rp-filters-row {
    grid-template-columns: 1fr;
  }

  .rp-field-search {
    grid-column: auto;
  }
}
  .rp-table-card {
    background: var(--rp-card);
    border-radius: var(--rp-radius);
    box-shadow: 0 2px 12px rgba(0, 0, 0, .06);
    overflow: hidden;
  }

  .rp-table-toolbar {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    border-bottom: 1px solid #F3F4F6;
  }

  .rp-table-title {
    font-weight: 700;
    color: var(--rp-primary);
    font-size: 1rem;
  }

  .rp-btn-excel {
    background: #217346;
    color: #fff;
    border: none;
    border-radius: 7px;
    padding: 8px 16px;
    font-size: .82rem;
    display: flex;
    align-items: center;
    gap: 7px;
    text-decoration: none;
  }

  .rp-btn-excel:hover {
    background: #1a5c38;
    color: #fff;
  }

  .sem-dot {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    margin: 0 auto;
    font-weight: 700;
    font-size: .7rem;
    color: #fff;
  }

  .sem-dot.verde {
    background: var(--rp-verde);
  }

  .sem-dot.amarillo {
    background: var(--rp-amarillo);
    color: #333;
  }

  .sem-dot.naranja {
    background: var(--rp-naranja);
  }

  .sem-dot.rojo {
    background: var(--rp-rojo);
  }

  .sem-dot.gris {
    background: var(--rp-gris);
  }

  .interp-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 20px;
    font-size: .77rem;
    font-weight: 600;
    white-space: normal;
  }

  .interp-verde {
    background: #D1FAE5;
    color: #065F46;
  }

  .interp-amarillo {
    background: #FEF9C3;
    color: #78350F;
  }

  .interp-naranja {
    background: #FFEDD5;
    color: #9A3412;
  }

  .interp-rojo {
    background: #FEE2E2;
    color: #991B1B;
  }

  .interp-gris {
    background: #F3F4F6;
    color: #4B5563;
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
</style>

<div class="rp-shell">

  <!-- CABECERA -->
  <div class="rp-header">
    <div class="rp-header-left">
      <div class="rp-header-icon">
        <img src="<?= base_url('img/antropometria2.svg') ?>" alt="">
      </div>
      <div>
        <div class="rp-header-sub">Reporte Antropométrico</div>
        <h1 class="rp-header-title">Menores de 19 años</h1>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <span class="rp-header-pill"><i class="bi bi-calendar3"></i> <?= esc($nombreJornada) ?></span>
      <a href="<?= site_url("jornadas/{$jornadaId}/reportes") ?>" class="rp-btn-back">← Volver a reportes</a>
    </div>
  </div>

  <!-- KPIs -->
  <div class="rp-kpi-grid">
    <div class="rp-kpi total">
      <div class="rp-kpi-val"><?= $contadores['total'] ?></div>
      <div class="rp-kpi-label">Total evaluados</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#101a61">
      <div class="rp-kpi-val"><?= $contadores['masculinos'] ?> / <?= $contadores['femeninas'] ?></div>
      <div class="rp-kpi-label">M / F</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#6366F1">
      <div class="rp-kpi-val"><?= $contadores['menores_5'] ?></div>
      <div class="rp-kpi-label">&lt; 5 años</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#8B5CF6">
      <div class="rp-kpi-val"><?= $contadores['entre_5_19'] ?></div>
      <div class="rp-kpi-label">5 – 19 años</div>
    </div>
    <div class="rp-kpi verde">
      <div class="rp-kpi-val"><?= $semaforo['verde'] ?></div>
      <div class="rp-kpi-label">Peso y talla adecuada</div>
      <div class="rp-kpi-pct"><?= pctM($semaforo['verde'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi amarillo">
      <div class="rp-kpi-val"><?= $semaforo['amarillo'] ?></div>
      <div class="rp-kpi-label">En riesgo</div>
      <div class="rp-kpi-pct"><?= pctM($semaforo['amarillo'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi naranja">
      <div class="rp-kpi-val"><?= $semaforo['naranja'] ?></div>
      <div class="rp-kpi-label">Leve / Moderado</div>
      <div class="rp-kpi-pct"><?= pctM($semaforo['naranja'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi rojo">
      <div class="rp-kpi-val"><?= $semaforo['rojo'] ?></div>
      <div class="rp-kpi-label">Atención inmediata</div>
      <div class="rp-kpi-pct"><?= pctM($semaforo['rojo'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi gris">
      <div class="rp-kpi-val"><?= $semaforo['gris'] ?></div>
      <div class="rp-kpi-label">RevisarDatos</div>
    </div>
  </div>

 
<!-- BARRA -->
<!-- RESUMEN DE RIESGO -->
<section class="rep_ad_risk_summary">
  <div class="rep_ad_risk_bar">
    <?php foreach ($riesgos as $key => $cfg): ?>
      <?php
      $valor = (int)($semaforo[$key] ?? 0);
      $width = $semTotal > 0 ? (($valor / $semTotal) * 100) : 0;
      ?>
      <?php if ($width > 0): ?>
        <div class="rep_ad_risk_segment" style="width:<?= esc(number_format($width, 4, '.', '')) ?>%; background:<?= esc($cfg['color']) ?>">
          <?= esc(pctM($valor, (int)$semTotal)) ?>%
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
<!-- FILTROS -->
<section class="rp-filters-card">
  <div class="rp-filters-row">
    <div class="rp-field rp-field-search">
      <label for="rp_men_searchInput">Buscar</label>
      <input type="text" id="rp_men_searchInput" placeholder="Buscar por nombre, cédula u observación...">
    </div>

    <div class="rp-field">
      <label for="rp_men_sexoFilter">Sexo</label>
      <select id="rp_men_sexoFilter">
        <option value="">Todos</option>
        <option value="M">Masculino</option>
        <option value="F">Femenino</option>
      </select>
    </div>

    <div class="rp-field">
      <label for="rp_men_ageFilter">Grupo etario</label>
      <select id="rp_men_ageFilter">
        <option value="">Todos</option>
        <option value="<5">&lt; 5 años</option>
        <option value="5-19">5 – 19 años</option>
      </select>
    </div>

    <div class="rp-field">
      <label for="rp_men_riskFilter">Estado de riesgo</label>
      <select id="rp_men_riskFilter">
        <option value="">Todos</option>
        <option value="verde">Peso y talla adecuada</option>
        <option value="amarillo">En riesgo</option>
        <option value="naranja">Leve / Moderado</option>
        <option value="rojo">Atención inmediata</option>
        <option value="gris">Datos insuficientes</option>
      </select>
    </div>

    <div class="rp-field">
      <label for="rp_men_rowsPerPage">Registros por página</label>
      <select id="rp_men_rowsPerPage">
        <option value="10">10</option>
        <option value="25" selected>25</option>
        <option value="50">50</option>
        <option value="100">100</option>
      </select>
    </div>

    <div class="rp-field">
      <label>&nbsp;</label>
      <button type="button" class="rp-btn-clear" id="rp_men_clearFilters">Limpiar filtros</button>
    </div>
  </div>
</section>
  <!-- TABLA -->
  <div class="rp-table-card">
 <div class="rp-table-toolbar">
  <span class="rp-table-title"><i class="bi bi-table me-2"></i>Detalle por beneficiario</span>

  <div class="rep_ad_table_statuses">
    <span>Estados de interpretación</span>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_green"></i><span>Adecuado</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_yellow"></i><span>Riesgo</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_orange"></i><span>Leve / Moderado</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_red"></i><span>Inmediato</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_gray"></i><span>Revisar Datos</span></div>
  </div>

  <a href="<?= site_url("jornadas/{$jornadaId}/reportes/antropometria/menores-19/excel") ?>" class="rp-btn-excel">
    <i class="bi bi-file-earmark-excel-fill"></i> Exportar Excel
  </a>
</div>
    <div class="table-responsive">
      <table id="tablaMenores" class="table table-hover align-middle mb-0" style="font-size:.83rem;">
        <thead class="table-dark">
          <tr>
            <th style="width:44px;text-align:center">🚦</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Sexo</th>
            <th>F. Nac.</th>
            <th>F. Evaluación</th>
            <th>Edad antro.</th>
            <th>Interp. ZIMCE/ZTE</th>
            <th>Interp. ZPT/ZTE<br><small class="text-muted fw-normal">(solo &lt;5 años)</small></th>
            <th>Peso (kg)</th>
            <th>Talla (cm)</th>
            <th>IMC</th>
            <th>ZP/T</th>
            <th>ZP/E</th>
            <th>ZT/E</th>
            <th>ZIMC/E</th>

            <th>Edema</th>
            <th>Observaciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datos)): ?>
            <tr>
              <td colspan="17" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No hay registros de menores en esta jornada.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($datos as $d):
              $clase  = $d['_clase'] ?? 'gris';
              $i1     = $d['_interp_zimce_zte'] ?? '—';
              $i2     = $d['_interp_zpt_zte'] ?? '';
              $dias   = (float)($d['edad_dias_medicion'] ?? 0);
              $a = $dias > 0 ? floor($dias / 365.25) : 0;
              $m = $dias > 0 ? floor(($dias % 365.25) / 30.44) : 0;

              $fnac = $d['fecha_nacimiento'] ?? '';
              $fnac_f = $fnac ? date('d/m/Y', strtotime($fnac)) : '—';
              $fechaEval = $d['fecha_evaluacion'] ?? '';
$fechaEval_f = $fechaEval ? date('d/m/Y', strtotime($fechaEval)) : '—';
              $grupoEdad = $dias > 0 && $dias <= 1856 ? '<5' : '5-19';
              $sexo = strtoupper(trim((string)($d['_sexo'] ?? $d['sexo'] ?? '')));
              $zscore_class = function (?float $z): string {
                if ($z === null) return 'zs-null';
                if ($z < -3 || $z > 3) return 'zs-bad';
                if ($z < -2 || $z > 2) return 'zs-warn';
                return 'zs-ok';
              };
              $fz = fn($v) => $v !== null && $v !== '' ? number_format((float)$v, 2) : '—';
            ?>
              <tr data-risk="<?= esc($clase) ?>" data-sexo="<?= esc($sexo) ?>" data-age="<?= esc($grupoEdad) ?>">
                <td><span class="sem-dot <?= esc($clase) ?>"></span></td>
                <td class="fw-500"><?= esc(ucwords(strtolower((string)($d['nombre_completo'] ?? '')))) ?></td>
                <td><?= esc($d['cedula'] ?? '—') ?></td>
                <td><?= esc($sexo ?: '—') ?></td>
                <td><?= esc($fnac_f) ?></td>
                <td><?= esc($fechaEval_f) ?></td>
                <td><?= $dias > 0 ? "{$a} a. {$m} m." : '—' ?></td>
                <td><span class="interp-badge interp-<?= esc($clase) ?>"><?= esc($i1) ?></span></td>
                <td><?= $i2 ? "<span class='interp-badge interp-{$clase}'>" . esc($i2) . "</span>" : '<span class="text-muted">—</span>' ?></td>
                <td><?= $fz($d['peso'] ?? null) ?></td>
                <td><?= $fz($d['talla'] ?? null) ?></td>
                <td><?= $fz($d['imc'] ?? null) ?></td>
                <?php foreach (['zpt', 'zpe', 'zte', 'zimce'] as $zk):
                  $zv = isset($d[$zk]) && $d[$zk] !== null && $d[$zk] !== '' ? (float)$d[$zk] : null;
                  $zc = $zscore_class($zv);
                ?>
                  <td><span class="zscore-chip <?= $zc ?>"><?= $zv !== null ? number_format($zv, 2) : '—' ?></span></td>
                <?php endforeach; ?>
                <td><?= ($d['edema'] ?? 0) ? '<span class="badge bg-warning text-dark">Sí</span>' : 'No' ?></td>
                <td class="text-muted" style="max-width:180px;white-space:normal"><?= esc($d['observaciones'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<script>
(function () {
  const hasRows = <?= empty($datos) ? 'false' : 'true' ?>;

  function rpMenNormalize(value) {
    return String(value || '')
      .trim()
      .toLowerCase()
      .normalize('NFD')
      .replace(/[\u0300-\u036f]/g, '');
  }

  function rpMenNormalizeSexo(value) {
    const v = rpMenNormalize(value);

    if (v === 'm' || v.includes('masculino') || v.includes('hombre')) {
      return 'M';
    }

    if (v === 'f' || v.includes('femenino') || v.includes('mujer')) {
      return 'F';
    }

    return String(value || '').trim().toUpperCase();
  }

  function rpMenGetCellText(row, index) {
    return row && row.cells && row.cells[index]
      ? row.cells[index].textContent.trim()
      : '';
  }

  function rpMenRowPassesFilters(row) {
    if (!row) {
      return true;
    }

    const search = rpMenNormalize(document.getElementById('rp_men_searchInput')?.value || '');
    const sexo = document.getElementById('rp_men_sexoFilter')?.value || '';
    const age = document.getElementById('rp_men_ageFilter')?.value || '';
    const risk = document.getElementById('rp_men_riskFilter')?.value || '';

    const rowText = rpMenNormalize(row.textContent || '');
    const rowSexo = rpMenNormalizeSexo(row.getAttribute('data-sexo') || rpMenGetCellText(row, 3));
    const rowAge = row.getAttribute('data-age') || '';
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

  function rpMenNativeFilter() {
    const rows = document.querySelectorAll('#tablaMenores tbody tr[data-risk]');
    let visible = 0;

    rows.forEach(function(row) {
      const show = rpMenRowPassesFilters(row);
      row.style.display = show ? '' : 'none';

      if (show) {
        visible++;
      }
    });

    const meta = document.getElementById('rp_men_tableMeta');

    if (meta) {
      meta.textContent = 'Mostrando ' + visible + ' de ' + rows.length + ' registros';
    }
  }

  function rpMenInit() {
    if (!hasRows) {
      return;
    }

    const hasDataTables = Boolean(window.jQuery && $.fn && $.fn.DataTable);

    if (!hasDataTables) {
      ['rp_men_searchInput', 'rp_men_sexoFilter', 'rp_men_ageFilter', 'rp_men_riskFilter'].forEach(function(id) {
        const el = document.getElementById(id);

        if (el) {
          el.addEventListener('input', rpMenNativeFilter);
          el.addEventListener('change', rpMenNativeFilter);
        }
      });

      const clearBtn = document.getElementById('rp_men_clearFilters');

      if (clearBtn) {
        clearBtn.addEventListener('click', function() {
          document.getElementById('rp_men_searchInput').value = '';
          document.getElementById('rp_men_sexoFilter').value = '';
          document.getElementById('rp_men_ageFilter').value = '';
          document.getElementById('rp_men_riskFilter').value = '';
          document.getElementById('rp_men_rowsPerPage').value = '25';
          rpMenNativeFilter();
        });
      }

      rpMenNativeFilter();
      return;
    }

    $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
      if (!settings || !settings.nTable || settings.nTable.id !== 'tablaMenores') {
        return true;
      }

      const rowObj = settings.aoData && settings.aoData[dataIndex] ? settings.aoData[dataIndex] : null;
      const row = rowObj ? rowObj.nTr : null;

      return rpMenRowPassesFilters(row);
    });

    const table = $('#tablaMenores').DataTable({
      responsive: false,
      pageLength: 25,
      lengthChange: false,
      order: [[7, 'asc']],
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
      dom: 'rt<"rp_men_dt_footer"ip>',
      columnDefs: [{
        orderable: false,
        targets: [0, 16, 17]
      }]
    });

    function rpMenRedraw() {
      table.draw();
    }

    $('#rp_men_searchInput').on('input keyup change', rpMenRedraw);
    $('#rp_men_sexoFilter, #rp_men_ageFilter, #rp_men_riskFilter').on('input change', rpMenRedraw);

    $('#rp_men_rowsPerPage').on('change', function() {
      table.page.len(Number(this.value || 25)).draw();
    });

    $('#rp_men_clearFilters').on('click', function() {
      $('#rp_men_searchInput').val('');
      $('#rp_men_sexoFilter').val('');
      $('#rp_men_ageFilter').val('');
      $('#rp_men_riskFilter').val('');
      $('#rp_men_rowsPerPage').val('25');

      table.search('').page.len(25).draw();
    });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', rpMenInit);
  } else {
    rpMenInit();
  }
})();
</script>

<?= $this->endSection() ?>
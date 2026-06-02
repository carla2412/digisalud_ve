<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreJornada = $jornada['nombre_jornada'] ?? 'Jornada';
$semTotal      = array_sum($semaforo);
$sinGris       = $semTotal - ($semaforo['gris'] ?? 0);

function pctE(int $val, int $total): string {
    if ($total <= 0) return '0';
    return number_format(($val * 100) / $total, 1);
}
$riesgos = [
    'verde' => [
        'label' => 'Ganancia adecuada',
        'color' => '#00B140',
    ],
    'naranja' => [
        'label' => 'Ganancia excesiva',
        'color' => '#FF8724',
    ],
    'rojo' => [
        'label' => 'Ganancia insuficiente',
        'color' => '#E43312',
    ],
    'gris' => [
        'label' => 'Datos insuficientes',
        'color' => '#9CA3AF',
    ],
];
?>

<style>
:root {
  --rp-primary:#101a61; --rp-cyan:#00D4FF;
  --rp-verde:#00B140; --rp-naranja:#FF8724; --rp-rojo:#E43312; --rp-gris:#9CA3AF;
  --rp-bg:#F0F4FA; --rp-card:#ffffff; --rp-radius:12px;
}
.rp-shell{padding:24px 28px 60px;background:var(--rp-bg);min-height:100vh;}
.rp-header{
  background:linear-gradient(135deg,#7c1a6a 60%,#5a1250);
  border-radius:var(--rp-radius);padding:24px 32px;
  display:flex;align-items:center;justify-content:space-between;
  flex-wrap:wrap;gap:16px;margin-bottom:24px;color:#fff;
}
.rp-header-left{display:flex;align-items:center;gap:16px;}
.rp-header-icon{width:52px;height:52px;background:rgba(255,255,255,.15);
  border-radius:12px;display:grid;place-items:center;}
.rp-header-icon img{width:36px;}
.rp-header-sub{font-size:.8rem;opacity:.7;text-transform:uppercase;letter-spacing:.04em;}
.rp-header-title{font-size:1.4rem;font-weight:700;margin:0;}
.rp-header-pill{background:rgba(255,255,255,.15);border:1px solid rgba(255,255,255,.25);
  border-radius:20px;padding:5px 14px;font-size:.82rem;}
.rp-btn-back{background:rgba(255,255,255,.18);border:1px solid rgba(255,255,255,.3);
  color:#fff;border-radius:8px;padding:8px 18px;font-size:.85rem;text-decoration:none;}
.rp-btn-back:hover{background:rgba(255,255,255,.3);color:#fff;}

.rp-kpi-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));
  gap:14px;margin-bottom:24px;}
.rp-kpi{background:var(--rp-card);border-radius:var(--rp-radius);
  padding:18px 16px;text-align:center;border-top:4px solid #e5e7eb;
  box-shadow:0 2px 8px rgba(0,0,0,.06);}
.rp-kpi.verde   {border-top-color:var(--rp-verde);}
.rp-kpi.naranja {border-top-color:var(--rp-naranja);}
.rp-kpi.rojo    {border-top-color:var(--rp-rojo);}
.rp-kpi.gris    {border-top-color:var(--rp-gris);}
.rp-kpi.total   {border-top-color:var(--rp-cyan);}
.rp-kpi-val  {font-size:2rem;font-weight:800;color:var(--rp-primary);line-height:1;}
.rp-kpi-label{font-size:.75rem;color:#6B7280;margin-top:4px;}
.rp-kpi-pct  {font-size:.8rem;color:#9CA3AF;margin-top:2px;}

.rp-dist-bar{display:flex;height:10px;border-radius:99px;overflow:hidden;
  margin-bottom:24px;gap:2px;}
.rp-dist-seg{transition:flex .4s;}
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
.rep_ad_dot_orange { background: var(--rp-naranja); }
.rep_ad_dot_red { background: var(--rp-rojo); }
.rep_ad_dot_gray { background: var(--rp-gris); }
/* Leyenda IOM */
.rp-iom-card{
  background:var(--rp-card);border-radius:var(--rp-radius);
  padding:16px 20px;margin-bottom:20px;
  box-shadow:0 1px 6px rgba(0,0,0,.06);
  display:flex;flex-wrap:wrap;gap:10px;align-items:center;
}
.rp-iom-card h6{color:var(--rp-primary);font-weight:700;margin:0 12px 0 0;white-space:nowrap;}
.iom-chip{
  padding:5px 14px;border-radius:20px;font-size:.78rem;font-weight:600;
  border:1px solid transparent;
}
.iom-bajo    {background:#FEE2E2;color:#991B1B;border-color:#FCA5A5;}
.iom-normal  {background:#D1FAE5;color:#065F46;border-color:#6EE7B7;}
.iom-sobre   {background:#FEF9C3;color:#78350F;border-color:#FDE68A;}
.iom-obesi   {background:#FFEDD5;color:#9A3412;border-color:#FDBA74;}

.rp-table-card{background:var(--rp-card);border-radius:var(--rp-radius);
  box-shadow:0 2px 12px rgba(0,0,0,.06);overflow:hidden;}
.rp-table-toolbar{padding:16px 20px;display:flex;align-items:center;
  justify-content:space-between;flex-wrap:wrap;gap:12px;
  border-bottom:1px solid #F3F4F6;}
.rp-table-title{font-weight:700;color:var(--rp-primary);font-size:1rem;}
.rp-btn-excel{background:#217346;color:#fff;border:none;
  border-radius:7px;padding:8px 16px;font-size:.82rem;
  display:flex;align-items:center;gap:7px;text-decoration:none;}
.rp-btn-excel:hover{background:#1a5c38;color:#fff;}

.sem-dot{width:32px;height:32px;border-radius:50%;
  display:grid;place-items:center;margin:0 auto;
  font-weight:700;font-size:.7rem;color:#fff;}
.sem-dot.verde  {background:var(--rp-verde);}
.sem-dot.naranja{background:var(--rp-naranja);}
.sem-dot.rojo   {background:var(--rp-rojo);}
.sem-dot.gris   {background:var(--rp-gris);}

.interp-badge{display:inline-block;padding:3px 10px;border-radius:20px;
  font-size:.78rem;font-weight:600;white-space:normal;}
.interp-verde  {background:#D1FAE5;color:#065F46;}
.interp-naranja{background:#FFEDD5;color:#9A3412;}
.interp-rojo   {background:#FEE2E2;color:#991B1B;}
.interp-gris   {background:#F3F4F6;color:#4B5563;}

.sem-inline{width:10px;height:10px;border-radius:50%;display:inline-block;}
.si-verde  {background:var(--rp-verde);}
.si-naranja{background:var(--rp-naranja);}
.si-rojo   {background:var(--rp-rojo);}
.si-gris   {background:var(--rp-gris);}
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
        <h1 class="rp-header-title">Embarazadas</h1>
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
      <div class="rp-kpi-label">Total embarazadas evaluadas</div>
    </div>
    <div class="rp-kpi verde">
      <div class="rp-kpi-val"><?= $semaforo['verde'] ?></div>
      <div class="rp-kpi-label">Ganancia adecuada</div>
      <div class="rp-kpi-pct"><?= pctE($semaforo['verde'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi naranja">
      <div class="rp-kpi-val"><?= $semaforo['naranja'] ?></div>
      <div class="rp-kpi-label">Ganancia excesiva</div>
      <div class="rp-kpi-pct"><?= pctE($semaforo['naranja'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi rojo">
      <div class="rp-kpi-val"><?= $semaforo['rojo'] ?></div>
      <div class="rp-kpi-label">Ganancia insuficiente</div>
      <div class="rp-kpi-pct"><?= pctE($semaforo['rojo'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi gris">
      <div class="rp-kpi-val"><?= $semaforo['gris'] ?></div>
      <div class="rp-kpi-label">Datos insuficientes</div>
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

  <!-- LEYENDA IOM -->
  <div class="rp-iom-card">
    <h6><i class="bi bi-info-circle me-1"></i>Criterios IOM 2009 — Ganancia de peso gestacional</h6>
    <span class="iom-chip iom-bajo">Bajo peso (&lt;18.5): 12.5–18 kg</span>
    <span class="iom-chip iom-normal">Peso normal (18.5–24.9): 11.5–16 kg</span>
    <span class="iom-chip iom-sobre">Sobrepeso (25–29.9): 7–11.5 kg</span>
    <span class="iom-chip iom-obesi">Obesidad (&gt;30): 5–9 kg</span>
    <small class="text-muted ms-2">Los rangos se ajustan proporcionalmente según semanas de gestación</small>
  </div>

  <!-- TABLA -->
  <div class="rp-table-card">
   <div class="rp-table-toolbar">
  <span class="rp-table-title"><i class="bi bi-table me-2"></i>Detalle por beneficiaria</span>

  <div class="rep_ad_table_statuses">
    <span>Estados de interpretación</span>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_green"></i><span>Adecuada</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_orange"></i><span>Excesiva</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_red"></i><span>Insuficiente</span></div>
    <div class="rep_ad_dot_item"><i class="rep_ad_dot rep_ad_dot_gray"></i><span>Sin datos</span></div>
  </div>

  <a href="<?= site_url("jornadas/{$jornadaId}/reportes/antropometria/embarazadas/excel") ?>" class="rp-btn-excel">
    <i class="bi bi-file-earmark-excel-fill"></i> Exportar Excel
  </a>
</div>
    <div class="table-responsive">
      <table id="tablaEmbarazadas" class="table table-hover align-middle mb-0" style="font-size:.83rem;">
        <thead class="table-dark">
          <tr>
            <th style="width:44px;text-align:center">🚦</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>F. Nac.</th>
            <th>F. Evaluación</th>
            <th>Edad</th>
            <th>Interpretación combinada</th>
            <th>Semanas<br><small class="text-muted fw-normal">gestación</small></th>
            <th>Ganancia<br><small class="text-muted fw-normal">kg</small></th>
            <th>IMC Pregest.</th>
            <th>Peso actual<br><small class="text-muted fw-normal">kg</small></th>
            <th>Talla<br><small class="text-muted fw-normal">cm</small></th>
            <th>FUM</th>
            <th>Fecha Eco</th>
            <th>Edema</th>
            <th>Observaciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datos)): ?>
            <tr>
              <td colspan="16" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No hay registros de embarazadas en esta jornada.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($datos as $d):
              $clase  = $d['_clase'] ?? 'gris';
              $interp = $d['_interpretacion'] ?? '—';
              $dias   = (float)($d['edad_dias_medicion'] ?? 0);
              $a = $dias > 0 ? floor($dias / 365.25) : 0;
              $m = $dias > 0 ? floor(($dias % 365.25) / 30.44) : 0;
              $fnac = $d['fecha_nacimiento'] ?? '';
              $fnac_f = $fnac ? date('d/m/Y', strtotime($fnac)) : '—';
              $fechaEval = $d['fecha_evaluacion'] ?? '';
$fechaEval_f = $fechaEval ? date('d/m/Y', strtotime($fechaEval)) : '—';
              $fum = $d['embarazo_fum'] ?? '';
              $fum_f = $fum ? date('d/m/Y', strtotime($fum)) : '—';
              $eco = $d['embarazo_fecha_eco'] ?? '';
              $eco_f = $eco ? date('d/m/Y', strtotime($eco)) : '—';
              $semanas = (int)($d['_semanas_calc'] ?? 0);
            ?>
            <tr>
              <td><span class="sem-dot <?= esc($clase) ?>"></span></td>
              <td class="fw-500"><?= esc(ucwords(strtolower((string)($d['nombre_completo'] ?? '')))) ?></td>
              <td><?= esc($d['cedula'] ?? '—') ?></td>
              <td><?= esc($fnac_f) ?></td>
              <td><?= esc($fechaEval_f) ?></td>
              <td><?= $dias > 0 ? "{$a} a. {$m} m." : '—' ?></td>
              <td><span class="interp-badge interp-<?= esc($clase) ?>"><?= esc($interp) ?></span></td>
              <td class="text-center fw-600">
                <?= $semanas > 0 ? "<strong>{$semanas}</strong> sem." : '<span class="text-muted">—</span>' ?>
              </td>
              <td class="text-center fw-600">
                <?= $d['_ganancia'] !== '—'
                    ? "<strong>{$d['_ganancia']}</strong>"
                    : '<span class="text-muted">—</span>' ?>
              </td>
              <td class="text-center"><?= $d['_imc_preg'] ?? '—' ?></td>
              <td><?= isset($d['peso']) && $d['peso'] ? number_format((float)$d['peso'], 2) : '—' ?></td>
              <td><?= isset($d['talla']) && $d['talla'] ? number_format((float)$d['talla'], 1) : '—' ?></td>
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
  </div>

</div>

<script>
$(document).ready(function () {
  if ($.fn.DataTable) {
    $('#tablaEmbarazadas').DataTable({
      responsive: true, pageLength: 25, order: [[5,'asc']],
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        search: '', searchPlaceholder: 'Buscar…',
        lengthMenu: 'Ver _MENU_ registros', zeroRecords: 'Sin resultados',
        info: 'Mostrando _END_ de _TOTAL_',
        paginate: { previous: '‹', next: '›' },
      },
      dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rtip',
      columnDefs: [{ orderable: false, targets: [0,13,14] }],
    });
  }
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreJornada = $jornada['nombre_jornada'] ?? 'Jornada';
$semTotal      = array_sum($semaforo);
$sinGris       = $semTotal - ($semaforo['gris'] ?? 0);

function pctM(int $val, int $total): string {
    if ($total <= 0) return '0';
    return number_format(($val * 100) / $total, 1);
}
?>

<style>
:root {
  --rp-primary: #101a61; --rp-cyan: #00D4FF;
  --rp-verde: #00B140; --rp-amarillo: #FFC609;
  --rp-naranja: #FF8724; --rp-rojo: #E43312; --rp-gris: #9CA3AF;
  --rp-bg: #F0F4FA; --rp-card: #ffffff; --rp-radius: 12px;
}
.rp-shell { padding: 24px 28px 60px; background: var(--rp-bg); min-height: 100vh; }
.rp-header {
  background: linear-gradient(135deg, #1a3a8a 60%, #0e2060);
  border-radius: var(--rp-radius); padding: 24px 32px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 16px; margin-bottom: 24px; color: #fff;
}
.rp-header-left { display: flex; align-items: center; gap: 16px; }
.rp-header-icon { width: 52px; height: 52px; background: rgba(255,255,255,.12);
  border-radius: 12px; display: grid; place-items: center; }
.rp-header-icon img { width: 36px; }
.rp-header-sub { font-size: .8rem; opacity: .7; text-transform: uppercase; letter-spacing: .04em; }
.rp-header-title { font-size: 1.4rem; font-weight: 700; margin: 0; }
.rp-header-pill { background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
  border-radius: 20px; padding: 5px 14px; font-size: .82rem; }
.rp-btn-back { background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
  color: #fff; border-radius: 8px; padding: 8px 18px; font-size: .85rem;
  text-decoration: none; }
.rp-btn-back:hover { background: rgba(255,255,255,.3); color: #fff; }
.rp-kpi-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 14px; margin-bottom: 24px; }
.rp-kpi { background: var(--rp-card); border-radius: var(--rp-radius); padding: 18px 16px;
  text-align: center; border-top: 4px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0,0,0,.06); }
.rp-kpi.verde   { border-top-color: var(--rp-verde); }
.rp-kpi.amarillo{ border-top-color: var(--rp-amarillo); }
.rp-kpi.naranja { border-top-color: var(--rp-naranja); }
.rp-kpi.rojo    { border-top-color: var(--rp-rojo); }
.rp-kpi.gris    { border-top-color: var(--rp-gris); }
.rp-kpi.total   { border-top-color: var(--rp-cyan); }
.rp-kpi-val   { font-size: 2rem; font-weight: 800; color: var(--rp-primary); line-height: 1; }
.rp-kpi-label { font-size: .75rem; color: #6B7280; margin-top: 4px; }
.rp-kpi-pct   { font-size: .8rem; color: #9CA3AF; margin-top: 2px; }
.rp-dist-bar  { display: flex; height: 10px; border-radius: 99px; overflow: hidden;
  margin-bottom: 24px; gap: 2px; }
.rp-dist-seg  { transition: flex .4s; }
.rp-table-card { background: var(--rp-card); border-radius: var(--rp-radius);
  box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden; }
.rp-table-toolbar { padding: 16px 20px; display: flex; align-items: center;
  justify-content: space-between; flex-wrap: wrap; gap: 12px;
  border-bottom: 1px solid #F3F4F6; }
.rp-table-title { font-weight: 700; color: var(--rp-primary); font-size: 1rem; }
.rp-btn-excel { background: #217346; color: #fff; border: none;
  border-radius: 7px; padding: 8px 16px; font-size: .82rem;
  display: flex; align-items: center; gap: 7px;
  text-decoration: none; }
.rp-btn-excel:hover { background: #1a5c38; color: #fff; }
.sem-dot { width: 32px; height: 32px; border-radius: 50%;
  display: grid; place-items: center; margin: 0 auto;
  font-weight: 700; font-size: .7rem; color: #fff; }
.sem-dot.verde    { background: var(--rp-verde); }
.sem-dot.amarillo { background: var(--rp-amarillo); color: #333; }
.sem-dot.naranja  { background: var(--rp-naranja); }
.sem-dot.rojo     { background: var(--rp-rojo); }
.sem-dot.gris     { background: var(--rp-gris); }
.interp-badge { display: inline-block; padding: 3px 10px; border-radius: 20px;
  font-size: .77rem; font-weight: 600; white-space: normal; }
.interp-verde    { background: #D1FAE5; color: #065F46; }
.interp-amarillo { background: #FEF9C3; color: #78350F; }
.interp-naranja  { background: #FFEDD5; color: #9A3412; }
.interp-rojo     { background: #FEE2E2; color: #991B1B; }
.interp-gris     { background: #F3F4F6; color: #4B5563; }
.zscore-chip { display: inline-block; padding: 2px 7px; border-radius: 6px;
  font-size: .76rem; font-weight: 600; }
.zs-ok   { background: #D1FAE5; color: #065F46; }
.zs-warn { background: #FEF9C3; color: #78350F; }
.zs-bad  { background: #FEE2E2; color: #991B1B; }
.zs-null { color: #9CA3AF; }
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
      <div class="rp-kpi-label">Datos insuficientes</div>
    </div>
  </div>

  <!-- BARRA -->
  <?php if ($semTotal > 0): ?>
  <div class="rp-dist-bar mb-4">
    <?php
    foreach ([
      ['color'=>'#00B140','val'=>$semaforo['verde']],
      ['color'=>'#FFC609','val'=>$semaforo['amarillo']],
      ['color'=>'#FF8724','val'=>$semaforo['naranja']],
      ['color'=>'#E43312','val'=>$semaforo['rojo']],
      ['color'=>'#9CA3AF','val'=>$semaforo['gris']],
    ] as $s):
      $flex = $semTotal > 0 ? round(($s['val'] / $semTotal) * 100) : 0;
      if ($flex > 0):
    ?>
      <div class="rp-dist-seg" style="flex:<?= $flex ?>;background:<?= $s['color'] ?>;"></div>
    <?php endif; endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- TABLA -->
  <div class="rp-table-card">
    <div class="rp-table-toolbar">
      <span class="rp-table-title"><i class="bi bi-table me-2"></i>Detalle por beneficiario</span>
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

              $zscore_class = function(?float $z): string {
                  if ($z === null) return 'zs-null';
                  if ($z < -3 || $z > 3) return 'zs-bad';
                  if ($z < -2 || $z > 2) return 'zs-warn';
                  return 'zs-ok';
              };
              $fz = fn($v) => $v !== null && $v !== '' ? number_format((float)$v, 2) : '—';
            ?>
            <tr>
              <td><span class="sem-dot <?= esc($clase) ?>"></span></td>
              <td class="fw-500"><?= esc(ucwords(strtolower((string)($d['nombre_completo'] ?? '')))) ?></td>
              <td><?= esc($d['cedula'] ?? '—') ?></td>
              <td><?= esc($d['sexo'] ?? '') ?></td>
              <td><?= esc($fnac_f) ?></td>
              <td><?= $dias > 0 ? "{$a} a. {$m} m." : '—' ?></td>
              <td><span class="interp-badge interp-<?= esc($clase) ?>"><?= esc($i1) ?></span></td>
              <td><?= $i2 ? "<span class='interp-badge interp-{$clase}'>" . esc($i2) . "</span>" : '<span class="text-muted">—</span>' ?></td>
              <td><?= $fz($d['peso'] ?? null) ?></td>
              <td><?= $fz($d['talla'] ?? null) ?></td>
              <td><?= $fz($d['imc'] ?? null) ?></td>
              <?php foreach (['zpt','zpe','zte','zimce'] as $zk):
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
$(document).ready(function () {
  if ($.fn.DataTable) {
    $('#tablaMenores').DataTable({
      responsive: true, pageLength: 25, order: [[6,'asc']],
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        search: '', searchPlaceholder: 'Buscar…',
        lengthMenu: 'Ver _MENU_ registros', zeroRecords: 'Sin resultados',
        info: 'Mostrando _END_ de _TOTAL_',
        paginate: { previous: '‹', next: '›' },
      },
      dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rtip',
      columnDefs: [{ orderable: false, targets: [0,15,16] }],
    });
  }
});
</script>

<?= $this->endSection() ?>

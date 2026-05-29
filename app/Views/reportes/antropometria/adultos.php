<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreJornada = $jornada['nombre_jornada'] ?? 'Jornada';
$semTotal      = array_sum($semaforo);
$sinGris       = $semTotal - ($semaforo['gris'] ?? 0);

function pct(int $val, int $total): string {
    if ($total <= 0) return '0';
    return number_format(($val * 100) / $total, 1);
}
?>

<style>
/* ═══ VARIABLES ═══════════════════════════════════════════ */
:root {
  --rp-primary: #101a61;
  --rp-cyan:    #00D4FF;
  --rp-verde:   #00B140;
  --rp-amarillo:#FFC609;
  --rp-naranja: #FF8724;
  --rp-rojo:    #E43312;
  --rp-gris:    #9CA3AF;
  --rp-bg:      #F0F4FA;
  --rp-card:    #ffffff;
  --rp-radius:  12px;
}

/* ═══ LAYOUT ══════════════════════════════════════════════ */
.rp-shell { padding: 24px 28px 60px; background: var(--rp-bg); min-height: 100vh; }

/* ═══ CABECERA ════════════════════════════════════════════ */
.rp-header {
  background: linear-gradient(135deg, var(--rp-primary) 60%, #1e2d8a);
  border-radius: var(--rp-radius);
  padding: 24px 32px;
  display: flex; align-items: center; justify-content: space-between;
  flex-wrap: wrap; gap: 16px;
  margin-bottom: 24px; color: #fff;
}
.rp-header-left { display: flex; align-items: center; gap: 16px; }
.rp-header-icon { width: 52px; height: 52px; background: rgba(255,255,255,.12);
  border-radius: 12px; display: grid; place-items: center; }
.rp-header-icon img { width: 36px; }
.rp-header-sub { font-size: .8rem; opacity: .7; letter-spacing: .04em; text-transform: uppercase; }
.rp-header-title { font-size: 1.4rem; font-weight: 700; margin: 0; }
.rp-header-pill {
  background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
  border-radius: 20px; padding: 5px 14px; font-size: .82rem;
  display: flex; align-items: center; gap: 6px;
}
.rp-btn-back {
  background: rgba(255,255,255,.18); border: 1px solid rgba(255,255,255,.3);
  color: #fff; border-radius: 8px; padding: 8px 18px; font-size: .85rem;
  text-decoration: none; transition: background .2s;
}
.rp-btn-back:hover { background: rgba(255,255,255,.3); color: #fff; }

/* ═══ KPI SEMÁFORO ════════════════════════════════════════ */
.rp-kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 14px; margin-bottom: 24px;
}
.rp-kpi {
  background: var(--rp-card); border-radius: var(--rp-radius);
  padding: 18px 16px; text-align: center;
  border-top: 4px solid #e5e7eb;
  box-shadow: 0 2px 8px rgba(0,0,0,.06);
  transition: transform .15s;
}
.rp-kpi:hover { transform: translateY(-2px); }
.rp-kpi.verde   { border-top-color: var(--rp-verde); }
.rp-kpi.amarillo{ border-top-color: var(--rp-amarillo); }
.rp-kpi.naranja { border-top-color: var(--rp-naranja); }
.rp-kpi.rojo    { border-top-color: var(--rp-rojo); }
.rp-kpi.gris    { border-top-color: var(--rp-gris); }
.rp-kpi.total   { border-top-color: var(--rp-cyan); }
.rp-kpi-val  { font-size: 2rem; font-weight: 800; color: var(--rp-primary); line-height: 1; }
.rp-kpi-label{ font-size: .75rem; color: #6B7280; margin-top: 4px; font-weight: 500; }
.rp-kpi-pct  { font-size: .8rem; color: #9CA3AF; margin-top: 2px; }

/* ═══ BARRA DISTRIBUCIÓN ══════════════════════════════════ */
.rp-dist-bar { display: flex; height: 10px; border-radius: 99px; overflow: hidden; margin-bottom: 24px; gap: 2px; }
.rp-dist-seg { transition: flex .4s; }

/* ═══ LEYENDA GRUPOS ══════════════════════════════════════ */
.rp-groups {
  display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 24px;
}
.rp-group-chip {
  background: var(--rp-card); border-radius: 8px;
  padding: 10px 18px; font-size: .85rem;
  display: flex; align-items: center; gap: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,.07);
}
.rp-group-chip .val { font-weight: 700; font-size: 1.1rem; color: var(--rp-primary); }

/* ═══ TABLA ═══════════════════════════════════════════════ */
.rp-table-card {
  background: var(--rp-card); border-radius: var(--rp-radius);
  box-shadow: 0 2px 12px rgba(0,0,0,.06); overflow: hidden;
}
.rp-table-toolbar {
  padding: 16px 20px; display: flex; align-items: center;
  justify-content: space-between; flex-wrap: wrap; gap: 12px;
  border-bottom: 1px solid #F3F4F6;
}
.rp-table-title { font-weight: 700; color: var(--rp-primary); font-size: 1rem; }
.rp-btn-excel {
  background: #217346; color: #fff; border: none;
  border-radius: 7px; padding: 8px 16px; font-size: .82rem;
  display: flex; align-items: center; gap: 7px; cursor: pointer;
  text-decoration: none; transition: background .2s;
}
.rp-btn-excel:hover { background: #1a5c38; color: #fff; }

/* ═══ SEMÁFORO DOT ════════════════════════════════════════ */
.sem-dot {
  width: 32px; height: 32px; border-radius: 50%;
  display: grid; place-items: center; margin: 0 auto;
  font-weight: 700; font-size: .7rem; color: #fff;
}
.sem-dot.verde   { background: var(--rp-verde); }
.sem-dot.amarillo{ background: var(--rp-amarillo); color: #333; }
.sem-dot.naranja { background: var(--rp-naranja); }
.sem-dot.rojo    { background: var(--rp-rojo); }
.sem-dot.gris    { background: var(--rp-gris); }

/* ═══ BADGE INTERPRETACIÓN ════════════════════════════════ */
.interp-badge {
  display: inline-block; padding: 3px 10px; border-radius: 20px;
  font-size: .78rem; font-weight: 600; white-space: normal;
}
.interp-verde    { background: #D1FAE5; color: #065F46; }
.interp-amarillo { background: #FEF9C3; color: #78350F; }
.interp-naranja  { background: #FFEDD5; color: #9A3412; }
.interp-rojo     { background: #FEE2E2; color: #991B1B; }
.interp-gris     { background: #F3F4F6; color: #4B5563; }
</style>

<div class="rp-shell">

  <!-- CABECERA ─────────────────────────────────────── -->
  <div class="rp-header">
    <div class="rp-header-left">
      <div class="rp-header-icon">
        <img src="<?= base_url('img/antropometria2.svg') ?>" alt="">
      </div>
      <div>
        <div class="rp-header-sub">Reporte Antropométrico</div>
        <h1 class="rp-header-title">Adultos (≥ 19 años)</h1>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3 flex-wrap">
      <span class="rp-header-pill">
        <i class="bi bi-calendar3"></i> <?= esc($nombreJornada) ?>
      </span>
      <a href="<?= site_url("jornadas/{$jornadaId}/reportes") ?>" class="rp-btn-back">
        ← Volver a reportes
      </a>
    </div>
  </div>

  <!-- KPIs ─────────────────────────────────────────── -->
  <div class="rp-kpi-grid">
    <div class="rp-kpi total">
      <div class="rp-kpi-val"><?= $contadores['total'] ?></div>
      <div class="rp-kpi-label">Total evaluados</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#101a61">
      <div class="rp-kpi-val"><?= $contadores['masculinos'] ?> / <?= $contadores['femeninas'] ?></div>
      <div class="rp-kpi-label">Masculino / Femenino</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#6366F1">
      <div class="rp-kpi-val"><?= $contadores['adulto_19_60'] ?></div>
      <div class="rp-kpi-label">19 – 60 años</div>
    </div>
    <div class="rp-kpi total" style="border-top-color:#8B5CF6">
      <div class="rp-kpi-val"><?= $contadores['adulto_mayor'] ?></div>
      <div class="rp-kpi-label">> 60 años</div>
    </div>
    <div class="rp-kpi verde">
      <div class="rp-kpi-val"><?= $semaforo['verde'] ?></div>
      <div class="rp-kpi-label">Peso adecuado / ECNT bajo</div>
      <div class="rp-kpi-pct"><?= pct($semaforo['verde'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi amarillo">
      <div class="rp-kpi-val"><?= $semaforo['amarillo'] ?></div>
      <div class="rp-kpi-label">ECNT leve</div>
      <div class="rp-kpi-pct"><?= pct($semaforo['amarillo'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi naranja">
      <div class="rp-kpi-val"><?= $semaforo['naranja'] ?></div>
      <div class="rp-kpi-label">ECNT moderado</div>
      <div class="rp-kpi-pct"><?= pct($semaforo['naranja'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi rojo">
      <div class="rp-kpi-val"><?= $semaforo['rojo'] ?></div>
      <div class="rp-kpi-label">Atención inmediata</div>
      <div class="rp-kpi-pct"><?= pct($semaforo['rojo'], $sinGris) ?> %</div>
    </div>
    <div class="rp-kpi gris">
      <div class="rp-kpi-val"><?= $semaforo['gris'] ?></div>
      <div class="rp-kpi-label">Datos insuficientes</div>
      <div class="rp-kpi-pct"><?= pct($semaforo['gris'], $semTotal) ?> %</div>
    </div>
  </div>

  <!-- BARRA DISTRIBUCIÓN ───────────────────────────── -->
  <?php if ($semTotal > 0): ?>
  <div class="rp-dist-bar mb-4">
    <?php
      $segs = [
        'verde'    => ['color' => '#00B140', 'val' => $semaforo['verde']],
        'amarillo' => ['color' => '#FFC609', 'val' => $semaforo['amarillo']],
        'naranja'  => ['color' => '#FF8724', 'val' => $semaforo['naranja']],
        'rojo'     => ['color' => '#E43312', 'val' => $semaforo['rojo']],
        'gris'     => ['color' => '#9CA3AF', 'val' => $semaforo['gris']],
      ];
      foreach ($segs as $s):
        $flex = $semTotal > 0 ? round(($s['val'] / $semTotal) * 100) : 0;
        if ($flex > 0):
    ?>
      <div class="rp-dist-seg" style="flex: <?= $flex ?>; background: <?= $s['color'] ?>;"
           title="<?= $flex ?> %"></div>
    <?php endif; endforeach; ?>
  </div>
  <?php endif; ?>

  <!-- TABLA ────────────────────────────────────────── -->
  <div class="rp-table-card">
    <div class="rp-table-toolbar">
      <span class="rp-table-title">
        <i class="bi bi-table me-2"></i>Detalle por beneficiario
      </span>
      <a href="<?= site_url("jornadas/{$jornadaId}/reportes/antropometria/adultos/excel") ?>"
         class="rp-btn-excel">
        <i class="bi bi-file-earmark-excel-fill"></i> Exportar Excel
      </a>
    </div>

    <div class="table-responsive">
      <table id="tablaAdultos" class="table table-hover align-middle mb-0" style="font-size: .84rem;">
        <thead class="table-dark">
          <tr>
            <th style="width:44px; text-align:center">🚦</th>
            <th>Nombre</th>
            <th>Cédula</th>
            <th>Sexo</th>
            <th>Fecha Nac.</th>
            <th>Edad</th>
            <th>Interpretación combinada</th>
            <th>Peso (kg)</th>
            <th>Talla (cm)</th>
            <th>IMC</th>
            <th>C. Cintura (cm)</th>
            <th>Edema</th>
            <th>Remisión</th>
            <th>Observaciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($datos)): ?>
            <tr>
              <td colspan="14" class="text-center py-5 text-muted">
                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                No hay registros de adultos en esta jornada.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($datos as $d):
              $clase   = $d['_clase'] ?? 'gris';
              $interp  = $d['_interpretacion'] ?? '—';
              $peso    = $d['peso'] ?? null;
              $talla   = $d['talla'] ?? null;
              $imc     = $d['imc'] ?? null;
              $cintura = $d['circ_cintura'] ?? null;
              $fnac    = $d['fecha_nacimiento'] ?? '';
              $fnac_f  = $fnac ? date('d/m/Y', strtotime($fnac)) : '—';
              $edadStr = '';
              $dias    = (float)($d['edad_dias_medicion'] ?? 0);
              if ($dias > 0) {
                  $a = floor($dias / 365.25);
                  $m = floor(($dias % 365.25) / 30.44);
                  $edadStr = "{$a} a. {$m} m.";
              }
            ?>
            <tr>
              <td><span class="sem-dot <?= esc($clase) ?>" title="<?= esc(ucfirst($clase)) ?>"></span></td>
              <td class="fw-500"><?= esc(ucwords(strtolower((string)($d['nombre_completo'] ?? '')))) ?></td>
              <td><?= esc($d['cedula'] ?? '—') ?></td>
              <td><?= esc($d['_sexo'] ?? '') ?></td>
              <td><?= esc($fnac_f) ?></td>
              <td><?= esc($edadStr ?: '—') ?></td>
              <td>
                <span class="interp-badge interp-<?= esc($clase) ?>">
                  <?= esc($interp) ?>
                </span>
              </td>
              <td><?= $peso !== null ? esc(number_format((float)$peso, 2)) : '—' ?></td>
              <td><?= $talla !== null ? esc(number_format((float)$talla, 1)) : '—' ?></td>
              <td><?= $imc !== null ? esc(number_format((float)$imc, 2)) : '—' ?></td>
              <td><?= $cintura !== null && $cintura > 0 ? esc(number_format((float)$cintura, 1)) : '—' ?></td>
              <td><?= ($d['edema'] ?? 0) ? '<span class="badge bg-warning text-dark">Sí</span>' : 'No' ?></td>
              <td><?= $d['remision'] ? esc(ucfirst((string)$d['remision'])) : '—' ?></td>
              <td class="text-muted" style="max-width:200px; white-space:normal"><?= esc($d['observaciones'] ?? '—') ?></td>
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
    $('#tablaAdultos').DataTable({
      responsive: true,
      pageLength: 25,
      order: [[6, 'asc']],
      language: {
        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json',
        search: '',
        searchPlaceholder: 'Buscar…',
        lengthMenu: 'Ver _MENU_ registros',
        zeroRecords: 'Sin resultados',
        info: 'Mostrando _END_ de _TOTAL_ registros',
        infoEmpty: '0 registros',
        paginate: { previous: '‹', next: '›' },
      },
      dom: '<"d-flex justify-content-between align-items-center mb-2"lf>rtip',
      columnDefs: [
        { orderable: false, targets: [0, 11, 12, 13] },
      ],
    });
  }
});
</script>

<?= $this->endSection() ?>

<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));
$urlVolver = $jornadaId ? base_url("jornadas/{$jornadaId}/beneficiarios") : base_url('beneficiarios');
$get = static function(array $r, string $codigo, string $default = '—') {
    if (! isset($r[$codigo]) || $r[$codigo]['valor'] === null || $r[$codigo]['valor'] === '') return $default;
    return esc(trim($r[$codigo]['valor'] . ' ' . ($r[$codigo]['unidad'] ?? '')));
};
?>

<style>
.hist_ant-page{padding:22px;background:#f8fbff;min-height:calc(100vh - var(--app-header-height,0px))}.hist_ant-header{display:flex;justify-content:space-between;gap:18px;align-items:center;background:#fff;border:1px solid var(--ds-border);border-radius:26px;padding:22px;box-shadow:0 16px 40px rgba(15,23,42,.07);margin-bottom:18px}.hist_ant-title{margin:0;color:var(--ds-dark);font-size:1.5rem;font-weight:800}.hist_ant-subtitle{margin:4px 0 0;color:var(--ds-muted)}.hist_ant-btn{border:1px solid var(--ds-border);border-radius:12px;padding:10px 14px;text-decoration:none;font-weight:800;color:var(--ds-primary);background:#e9edff}.hist_ant-list{display:grid;gap:14px}.hist_ant-card{background:#fff;border:1px solid var(--ds-border);border-radius:22px;padding:18px;box-shadow:0 12px 28px rgba(15,23,42,.06)}.hist_ant-card-head{display:flex;justify-content:space-between;gap:12px;border-bottom:1px solid var(--ds-border);padding-bottom:12px;margin-bottom:12px}.hist_ant-date{font-weight:900;color:var(--ds-dark)}.hist_ant-place{color:var(--ds-muted);font-size:.86rem}.hist_ant-grid{display:grid;grid-template-columns:repeat(6,minmax(0,1fr));gap:10px}.hist_ant-kpi{background:#f8fbff;border:1px solid var(--ds-border);border-radius:16px;padding:12px}.hist_ant-kpi span{display:block;color:var(--ds-muted);font-size:.72rem;font-weight:800}.hist_ant-kpi strong{display:block;color:var(--ds-primary);font-size:1rem;margin-top:4px}.hist_ant-tags{display:flex;flex-wrap:wrap;gap:8px;margin-top:12px}.hist_ant-tag{background:#eef4ff;border:1px solid var(--ds-border);border-radius:999px;padding:7px 10px;color:var(--ds-dark);font-size:.78rem;font-weight:700}.hist_ant-empty{background:#fff;border:1px dashed var(--ds-border);border-radius:22px;padding:24px;text-align:center;color:var(--ds-muted)}@media(max-width:1000px){.hist_ant-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.hist_ant-header{align-items:flex-start;flex-direction:column}}@media(max-width:620px){.hist_ant-grid{grid-template-columns:1fr}}
</style>

<main class="hist_ant-page">
  <header class="hist_ant-header">
    <div>
      <h1 class="hist_ant-title">Historial de Antropometría</h1>
      <p class="hist_ant-subtitle">Beneficiario: <?= $nombreCompleto ?></p>
    </div>
    <a class="hist_ant-btn" href="<?= esc($urlVolver) ?>">Volver</a>
  </header>

  <?php if (empty($historial)): ?>
    <div class="hist_ant-empty">No hay evaluaciones de antropometría registradas para este beneficiario.</div>
  <?php else: ?>
    <section class="hist_ant-list">
      <?php foreach ($historial as $evaluacion): ?>
        <?php $r = $evaluacion['resultados'] ?? []; ?>
        <article class="hist_ant-card">
          <div class="hist_ant-card-head">
            <div>
              <div class="hist_ant-date"><?= esc(date('d/m/Y', strtotime($evaluacion['fecha_evaluacion'] ?? 'now'))) ?></div>
              <div class="hist_ant-place"><?= esc($evaluacion['nombre_jornada'] ?? $evaluacion['nombre_centro'] ?? 'Evaluación') ?></div>
            </div>
            <span class="hist_ant-tag"><?= $get($r, 'grupo_edad_reporte') ?></span>
          </div>
          <div class="hist_ant-grid">
            <div class="hist_ant-kpi"><span>Peso</span><strong><?= $get($r, 'peso') ?></strong></div>
            <div class="hist_ant-kpi"><span>Talla</span><strong><?= $get($r, 'talla') ?></strong></div>
            <div class="hist_ant-kpi"><span>IMC</span><strong><?= $get($r, 'imc') ?></strong></div>
            <div class="hist_ant-kpi"><span>ZIMC/E</span><strong><?= $get($r, 'zimce') ?></strong></div>
            <div class="hist_ant-kpi"><span>ZTE</span><strong><?= $get($r, 'zte') ?></strong></div>
            <div class="hist_ant-kpi"><span>ZPT</span><strong><?= $get($r, 'zpt') ?></strong></div>
          </div>
          <div class="hist_ant-tags">
            <span class="hist_ant-tag">Estado agregado: <?= $get($r, 'estado_nutricional_agregado') ?></span>
            <span class="hist_ant-tag">Clasificación: <?= $get($r, 'clasificacion_imc_talla') ?></span>
            <span class="hist_ant-tag">Remisión: <?= $get($r, 'remision') ?></span>
          </div>
          <?php if (! empty($evaluacion['observaciones'])): ?>
            <div class="hist_ant-tags"><span class="hist_ant-tag">Observaciones: <?= esc($evaluacion['observaciones']) ?></span></div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
</main>

<?= $this->endSection() ?>

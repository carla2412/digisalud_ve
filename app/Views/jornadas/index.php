<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
.ds-layout { display: flex; gap: 0; min-height: 100vh; }
.ds-main { flex: 1; padding: 32px 38px; background: #f5f7fb; }

.ds-header { display: flex; justify-content: space-between; align-items: center; }
.ds-header h1 { font-size: 1.9rem; font-weight: 800; color: #0b1b3f; margin: 0; }
.ds-header p { color: #64748b; margin: 4px 0 0; }

.ds-btn-primary {
    background: linear-gradient(135deg, #1476ff, #0059e8);
    color: white; padding: 15px 28px; border-radius: 10px;
    text-decoration: none; font-weight: 700;
    box-shadow: 0 10px 18px rgba(0, 102, 255, .25);
}

.ds-filters {
    display: grid; grid-template-columns: 1.3fr 1fr 1fr auto;
    gap: 16px; margin: 28px 0 22px; align-items: center;
}
.ds-search, .ds-filters select {
    height: 50px; border: 1px solid #d9e2ef; border-radius: 12px; background: #fff;
}
.ds-search { display: flex; align-items: center; padding: 0 18px; }
.ds-search input { border: none; outline: none; flex: 1; font-size: 1rem; }
.ds-filters select { padding: 0 18px; font-size: .95rem; color: #334155; cursor: pointer; }

.ds-btn-outline {
    height: 50px; border: 1px solid #d9e2ef; border-radius: 12px; background: #fff;
    padding: 0 20px; font-weight: 600; color: #64748b; cursor: pointer;
    text-decoration: none; display: flex; align-items: center; justify-content: center; white-space: nowrap;
}
.ds-btn-outline:hover { background: #f1f5f9; color: #334155; }

.ds-cards { display: flex; flex-direction: column; gap: 18px; }

.ds-card {
    position: relative; display: flex; gap: 28px; background: #fff;
    border-radius: 14px; padding: 28px;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .08); overflow: visible;
}
.ds-card::before {
    content: ""; position: absolute; left: 0; top: 0; width: 5px; height: 100%;
}
.ds-card.active::before { background: #22c55e; }
.ds-card.finished::before { background: #ef4444; }

.ds-card-icon {
    width: 84px; height: 84px; border-radius: 16px;
    display: grid; place-items: center; font-size: 2rem;
}
.ds-card-icon.active { background: #e7f8ee; }
.ds-card-icon.finished { background: #feecec; }

.ds-card-body { flex: 1; }
.ds-card-body h3 { margin: 4px 0; font-size: 1.35rem; }
.ds-org { margin: 0 0 16px; color: #64748b; }

.ds-pesquisas { display: flex; gap: 8px; margin-bottom: 22px; }
.ds-pesquisas span {
    width: 40px; height: 40px; border-radius: 50%; color: #fff;
    display: grid; place-items: center; font-size: .9rem;
}
.blue { background: #2478df; }
.purple { background: #341092; }
.orange { background: #ff4817; }
.violet { background: #5f539e; }
.red { background: #e72713; }
.yellow { background: #ffc107; }

.ds-location { color: #64748b; margin: 0; }

.ds-card-side {
    width: 380px; display: flex; flex-direction: column; align-items: flex-end;
}
.ds-status {
    padding: 8px 18px; border-radius: 999px; font-weight: 800; font-size: .85rem;
}
.ds-status.active { background: #dcfce7; color: #079445; }
.ds-status.finished { background: #fee2e2; color: #dc2626; }
.ds-card-side small { margin-top: 12px; color: #475569; font-weight: 600; }

.ds-actions { margin-top: auto; display: flex; align-items: center; gap: 14px; }
.ds-actions a {
    border: 1px solid #8bb7ff; color: #0066ff; background: #fff;
    padding: 12px 18px; border-radius: 9px; text-decoration: none; font-weight: 600;
}
.ds-actions a.disabled {
    opacity: .45; pointer-events: none; color: #64748b; border-color: #cbd5e1;
}

.ds-pagination {
    display: flex; justify-content: space-between; align-items: center;
    margin-top: 22px; color: #475569;
}
.ds-pagination-nav { display: flex; gap: 6px; }
.ds-pagination a, .ds-pagination span.pg-btn {
    width: 42px; height: 42px; border: 1px solid #dbe4ef; background: #fff;
    border-radius: 9px; display: inline-flex; align-items: center;
    justify-content: center; text-decoration: none; color: #334155; font-weight: 600;
}
.ds-pagination a.active { background: #126dff; color: white; border-color: #126dff; }
.ds-pagination a.disabled { opacity: .4; pointer-events: none; }

.alert { padding: 14px 20px; border-radius: 10px; margin-bottom: 16px; font-weight: 600; }
.alert-success { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
.alert-danger  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }
.alert-info    { background: #dbeafe; color: #1e40af; border: 1px solid #93bbfd; }

@media (max-width: 992px) {
    .ds-layout { flex-direction: column; }
    .ds-filters { grid-template-columns: 1fr; }
    .ds-card { flex-direction: column; }
    .ds-card-side { width: 100%; align-items: flex-start; }
    .ds-actions { margin-top: 20px; flex-wrap: wrap; }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$pesquisaMap = [
  '1' => ['nombre'=>'Antropometría','emoji'=>'antropometria2.svg','clase'=>'yellow'],
  '2' => ['nombre'=>'Laboratorio','emoji'=>'sanguinea2.svg','clase'=>'red'],
  '3' => ['nombre'=>'Visual','emoji'=>'visual2.svg','clase'=>'purple'],
  '4' => ['nombre'=>'Signos vitales','emoji'=>'signosVitales2.svg','clase'=>'red'],
  '5' => ['nombre'=>'Medicina general','emoji'=>'medicinaGeneral2.svg','clase'=>'purple'],
  '6' => ['nombre'=>'Vacunación','emoji'=>'vacunacion2.svg','clase'=>'blue'],
];

$busqueda       = $busqueda ?? '';
$status         = $status ?? '';
$orden          = $orden ?? 'desc';
$page           = $page ?? 1;
$perPage        = $perPage ?? 5;
$totalJornadas  = $totalJornadas ?? 0;
$totalPages     = $totalPages ?? 1;
?>

<div class="ds-layout">
    <main class="ds-main">

        <div class="ds-header">
            <div>
                <h1>Jornadas</h1>
                <p>Gestiona y consulta las jornadas de salud</p>
            </div>
            <a href="<?= base_url('jornadas/crear') ?>" class="ds-btn-primary">+ Crear Jornada</a>
        </div>

        <!-- ═══ FILTROS FUNCIONALES ═══ -->
        <form method="get" action="<?= base_url('jornadas') ?>" id="formFiltros">
            <section class="ds-filters">
                <div class="ds-search">
                    <input type="text" name="q" placeholder="Buscar jornadas..."
                           value="<?= esc($busqueda) ?>">
                    <span style="cursor:pointer;" onclick="document.getElementById('formFiltros').submit()">&#x2315;</span>
                </div>

                <select name="status" onchange="this.form.submit()">
                    <option value="">Estado: Todos</option>
                    <option value="1" <?= $status === '1' ? 'selected' : '' ?>>Activa</option>
                    <option value="2" <?= $status === '2' ? 'selected' : '' ?>>Finalizada</option>
                </select>

                <select name="orden" onchange="this.form.submit()">
                    <option value="desc" <?= $orden === 'desc' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="asc"  <?= $orden === 'asc'  ? 'selected' : '' ?>>Más antiguas</option>
                </select>

                <a href="<?= base_url('jornadas') ?>" class="ds-btn-outline">Limpiar filtros</a>
            </section>
        </form>

        <!-- ═══ ALERTAS ═══ -->
        <?php if (session('success')): ?>
            <div class="alert alert-success"><?= esc(session('success')) ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="alert alert-danger"><?= esc(session('error')) ?></div>
        <?php endif; ?>

        <!-- ═══ LISTADO ═══ -->
        <?php if (!empty($jornadas)): ?>
            <section class="ds-cards">
                <?php foreach ($jornadas as $jor): ?>
                    <?php
                        $esFinalizada = $jor['status_jor'] == 2;
                        $estadoTexto  = $jor['status_jor'] == 1 ? 'ACTIVA' : ($esFinalizada ? 'FINALIZADA' : 'INACTIVA');
                        $estadoClass  = $jor['status_jor'] == 1 ? 'active' : ($esFinalizada ? 'finished' : 'inactive');
                    ?>
                    <article class="ds-card <?= $estadoClass ?>">
                        <div class="ds-card-icon <?= $estadoClass ?>"></div>

                        <div class="ds-card-body">
                            <h3><?= esc($jor['nombre_jornada']) ?></h3>
                            <p class="ds-org"><?= esc($jor['nombre_org'] ?? 'Sin organización') ?></p>

                            <?php if (!empty($jor['pesquisas'])): ?>
                                <div class="ds-pesquisas">
                                    <?php foreach (explode(',', $jor['pesquisas']) as $pid): ?>
                                        <?php $pid = trim($pid); if (isset($pesquisaMap[$pid])): ?>
                                            <span class="<?= $pesquisaMap[$pid]['clase'] ?>"
                                                  title="<?= esc($pesquisaMap[$pid]['nombre']) ?>">
                                                 <img src="<?= base_url('img/' . $pesquisaMap[$pid]['emoji']) ?>" width="36">
                                
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <p class="ds-location">
                                <?= esc($jor['nombre_institucion'] ?? '') ?>
                                <?php if (!empty($jor['ciudad'])): ?> — <?= esc($jor['ciudad']) ?><?php endif; ?>
                            </p>
                        </div>

                        <div class="ds-card-side">
                            <span class="ds-status <?= $estadoClass ?>"><?= $estadoTexto ?></span>
                            <small>
                                <?= !empty($jor['fecha_inicio']) && $jor['fecha_inicio'] !== '0000-00-00'
                                    ? date('d M Y', strtotime($jor['fecha_inicio']))
                                    : 'Sin fecha' ?>
                            </small>

                            <div class="ds-actions">
                                <a href="<?= $esFinalizada ? '#' : base_url('jornadas/editar/' . $jor['id_jornada']) ?>"
                                   class="<?= $esFinalizada ? 'disabled' : '' ?>">Editar</a>

                                <?php if (in_array(session('id_rol'), [1,2,3,4])): ?>
                                    <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/usuarios') ?>">Usuarios</a>
                                <?php else: ?>
                                    <a href="#" class="disabled">Usuarios</a>
                                <?php endif; ?>

                                <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/beneficiarios') ?>">Beneficiarios</a>
                                <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/reportes') ?>">Reportes</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <!-- ═══ PAGINACIÓN ═══ -->
            <?php
                $qsBase = array_filter([
                    'q' => $busqueda, 'status' => $status, 'orden' => $orden,
                ], fn($v) => $v !== '' && $v !== null);

                $linkPage = function($p) use ($qsBase) {
                    return base_url('jornadas?' . http_build_query(array_merge($qsBase, ['page' => $p])));
                };
            ?>
            <footer class="ds-pagination">
                <span>Mostrando <?= (($page-1)*$perPage)+1 ?> a <?= min($page*$perPage, $totalJornadas) ?> de <?= $totalJornadas ?> jornadas</span>

                <?php if ($totalPages > 1): ?>
                <div class="ds-pagination-nav">
                    <a href="<?= $page > 1 ? $linkPage($page-1) : '#' ?>" class="<?= $page <= 1 ? 'disabled' : '' ?>">&#8249;</a>
                    <?php
                        $start = max(1, $page - 2);
                        $end   = min($totalPages, $page + 2);
                    ?>
                    <?php for ($i = $start; $i <= $end; $i++): ?>
                        <a href="<?= $linkPage($i) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                    <a href="<?= $page < $totalPages ? $linkPage($page+1) : '#' ?>" class="<?= $page >= $totalPages ? 'disabled' : '' ?>">&#8250;</a>
                </div>
                <?php endif; ?>
            </footer>

        <?php else: ?>
            <div class="alert alert-info" style="text-align:center; margin-top:24px;">
                <?= $busqueda !== '' || $status !== '' ? 'No se encontraron jornadas con los filtros aplicados.' : 'Crea tu primera jornada' ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    .jornada-layout {
        display: flex;
        gap: 0;
        min-height: 100vh;
    }

    .jornada-main {
        flex: 1;
        padding: 32px 38px;
        background: #f5f7fb;
    }

    .jornada-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .jornada-header h1 {
        font-size: 1.9rem;
        font-weight: 800;
        color: #0b1b3f;
        margin: 0;
    }

    .jornada-header p {
        color: #64748b;
        margin: 4px 0 0;
    }

    .jornada-btn-primary {
        background: linear-gradient(135deg, #1476ff, #0059e8);
        color: white;
        padding: 15px 28px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 700;
        box-shadow: 0 10px 18px rgba(0, 102, 255, .25);
    }

    .jornada-filters {
        display: grid;
        grid-template-columns: 1.3fr 1fr 1fr auto;
        gap: 16px;
        margin: 28px 0 22px;
        align-items: center;
    }

    .jornada-search,
    .jornada-filters select {
        height: 50px;
        border: 1px solid #d9e2ef;
        border-radius: 12px;
        background: #fff;
    }

    .jornada-search {
        display: flex;
        align-items: center;
        padding: 0 18px;
    }

    .jornada-search input {
        border: none;
        outline: none;
        flex: 1;
        font-size: 1rem;
    }

    .jornada-filters select {
        padding: 0 18px;
        font-size: .95rem;
        color: #334155;
        cursor: pointer;
    }

    .jornada-btn-outline {
        height: 50px;
        border: 1px solid #d9e2ef;
        border-radius: 12px;
        background: #fff;
        padding: 0 20px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        white-space: nowrap;
    }

    .jornada-btn-outline:hover {
        background: #f1f5f9;
        color: #334155;
    }

    .jornada-cards {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    .jornada-card {
        position: relative;
        display: flex;
        gap: 28px;
        background: #fff;
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
        overflow: visible;
    }

    .jornada-card::before {
        content: "";
        position: absolute;
        left: 0;
        top: 0;
        width: 5px;
        height: 100%;
    }

    .jornada-card.jornada-active::before {
        background: #22c55e;
    }

    .jornada-card.jornada-finished::before {
        background: #ef4444;
    }

    .jornada-card-icon {
        width: 84px;
        height: 84px;
        border-radius: 16px;
        display: grid;
        place-items: center;
        font-size: 2rem;
    }

    .jornada-card-icon.jornada-active {
        background: #e7f8ee;
    }

    .jornada-card-icon.jornada-finished {
        background: #feecec;
    }

    .jornada-card-body {
        flex: 1;
    }

    .jornada-card-body h3 {
        margin: 4px 0;
        font-size: 1.35rem;
    }

    .jornada-org {
        margin: 0 0 16px;
        color: #64748b;
    }

    .jornada-pesquisas {
        display: flex;
        gap: 8px;
        margin-bottom: 22px;
    }

    .jornada-pesquisas span {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        color: #fff;
        display: grid;
        place-items: center;
        font-size: .9rem;
    }

    .jornada-blue {
        background: #3695f5;
    }

    .jornada-purple {
        background: #341092;
    }

    .jornada-orange {
        background: #ff4817;
    }

    .jornada-violet {
        background: #5f539e;
    }

    .jornada-red {
        background: #e72713;
    }

    .jornada-yellow {
        background: #ffc107;
    }

    .jornada-location {
        color: #64748b;
        margin: 0;
    }

    .jornada-card-side {
        width: 380px;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
    }

    .jornada-status {
        padding: 8px 18px;
        border-radius: 999px;
        font-weight: 800;
        font-size: .85rem;
    }

    .jornada-status.jornada-active {
        background: #dcfce7;
        color: #28a745;
    }

    .jornada-status.jornada-finished {
        background: #fee2e2;
        color: #dc2626;
    }

    .jornada-card-side small {
        margin-top: 12px;
        color: #475569;
        font-weight: 600;
    }

    .jornada-actions {
        margin-top: auto;
        display: flex;
        align-items: center;
        gap: 14px;
    }

    .jornada-actions a {
        border: 1px solid #8bb7ff;
        color: #3695f5;
        background: #fff;
        padding: 12px 18px;
        border-radius: 9px;
        text-decoration: none;
        font-weight: 600;
    }

    .jornada-actions a.jornada-disabled {
        opacity: .45;
        pointer-events: none;
        color: #64748b;
        border-color: #cbd5e1;
    }

    .jornada-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 22px;
        color: #475569;
    }

    .jornada-pagination-nav {
        display: flex;
        gap: 6px;
    }

    .jornada-pagination a,
    .jornada-pagination span.pg-btn {
        width: 42px;
        height: 42px;
        border: 1px solid #dbe4ef;
        background: #fff;
        border-radius: 9px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #334155;
        font-weight: 600;
    }

    .jornada-pagination a.jornada-active {
        background: #126dff;
        color: white;
        border-color: #126dff;
    }

    .jornada-pagination a.jornada-disabled {
        opacity: .4;
        pointer-events: none;
    }

    .jornada-alert {
        padding: 14px 20px;
        border-radius: 10px;
        margin-bottom: 16px;
        font-weight: 600;
    }

    .jornada-alert-success {
        background: #dcfce7;
        color: #28a745;
        border: 1px solid #86efac;
    }

    .jornada-alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .jornada-alert-info {
        background: #dbeafe;
        color: #1e40af;
        border: 1px solid #93bbfd;
    }

    .jornada-pager-links nav {
        margin: 0;
    }

    /* ═══ PAGINACIÓN ═══ */
    .jornada-pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 22px;
        color: #475569;
    }

    .jornada-pager-links nav {
        margin: 0;
    }

    .jornada-pager-links ul,
    .jornada-pager-links .pagination {
        display: flex;
        gap: 8px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .jornada-pager-links li {
        list-style: none;
    }

    .jornada-pager-links a,
    .jornada-pager-links span {
        min-width: 42px;
        height: 42px;
        border: 1px solid #dbe4ef;
        background: #fff;
        border-radius: 9px;
        color: #64748b;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        padding: 0 12px;
        position: relative;
        transition: all .2s ease;
    }

    .jornada-pager-links a:hover {
        background: #eef6ff;
        color: #3695f5;
        border-color: #3695f5;
    }

    /* ACTIVO PARA TEMPLATE default_full DE CODEIGNITER */
    .jornada-pager-links li.active a,
    .jornada-pager-links li.active span,
    .jornada-pager-links .active a,
    .jornada-pager-links .active span,
    .jornada-pager-links a.active,
    .jornada-pager-links span.active {
        background: #3695f5 !important;
        color: #fff !important;
        border-color: #3695f5 !important;
        box-shadow: 0 8px 18px rgba(18, 109, 255, 0.28);
        transform: translateY(-1px);
    }

    /* Punto inferior en el número activo */
    .jornada-pager-links li.active a::after,
    .jornada-pager-links li.active span::after,
    .jornada-pager-links .active a::after,
    .jornada-pager-links .active span::after,
    .jornada-pager-links a.active::after,
    .jornada-pager-links span.active::after {

        width: 6px;
        height: 6px;
        background: #fff;
        border-radius: 50%;
        position: absolute;
        bottom: 5px;
    }

    /* Deshabilitados */
    .jornada-pager-links li.disabled a,
    .jornada-pager-links li.disabled span,
    .jornada-pager-links .disabled a,
    .jornada-pager-links .disabled span {
        opacity: .45;
        pointer-events: none;
    }

    @media (max-width: 992px) {
        .jornada-layout {
            flex-direction: column;
        }

        .jornada-filters {
            grid-template-columns: 1fr;
        }

        .jornada-card {
            flex-direction: column;
        }

        .jornada-card-side {
            width: 100%;
            align-items: flex-start;
        }

        .jornada-actions {
            margin-top: 20px;
            flex-wrap: wrap;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$pesquisaMap = [
    '1' => ['nombre' => 'Antropometría', 'emoji' => 'antropometria2.svg', 'clase' => 'jornada-yellow'],
    '2' => ['nombre' => 'Laboratorio', 'emoji' => 'sanguinea2.svg', 'clase' => 'jornada-red'],
    '3' => ['nombre' => 'Visual', 'emoji' => 'visual2.svg', 'clase' => 'jornada-violet'],
    '4' => ['nombre' => 'Signos vitales', 'emoji' => 'signosVitales2.svg', 'clase' => 'jornada-orange'],
    '5' => ['nombre' => 'Medicina general', 'emoji' => 'medicinaGeneral2.svg', 'clase' => 'jornada-purple'],
    '6' => ['nombre' => 'Vacunación', 'emoji' => 'vacunacion2.svg', 'clase' => 'jornada-blue'],
];

$busqueda       = $busqueda ?? '';
$status         = $status ?? '';
$orden          = $orden ?? 'desc';
$page           = $page ?? 1;
$perPage        = $perPage ?? 5;
$totalJornadas  = $totalJornadas ?? 0;
$totalPages     = $totalPages ?? 1;
?>

<div class="jornada-layout">
    <main class="jornada-main">

        <div class="jornada-header">
            <div>
                <h1>Jornadas</h1>
                <p>Gestiona y consulta las jornadas de salud</p>
            </div>
            <a href="<?= base_url('jornadas/crear') ?>" class="jornada-btn-primary">+ Crear Jornada</a>
        </div>

        <!-- ═══ FILTROS FUNCIONALES ═══ -->
        <form method="get" action="<?= base_url('jornadas') ?>" id="formFiltros">
            <section class="jornada-filters">
                <div class="jornada-search">
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
                    <option value="asc" <?= $orden === 'asc'  ? 'selected' : '' ?>>Más antiguas</option>
                </select>

                <a href="<?= base_url('jornadas') ?>" class="jornada-btn-outline">Limpiar filtros</a>
            </section>
        </form>

        <!-- ═══ ALERTAS ═══ -->
        <?php if (session('success')): ?>
            <div class="jornada-alert jornada-alert-success jornada-auto-dismiss"><?= session('success') ?></div>
        <?php endif; ?>
        <?php if (session('error')): ?>
            <div class="jornada-alert jornada-alert-danger jornada-auto-dismiss"><?= session('error') ?></div>
        <?php endif; ?>

        <!-- ═══ LISTADO ═══ -->
        <?php if (!empty($jornadas)): ?>
            <section class="jornada-cards">
                <?php foreach ($jornadas as $jor): ?>
                    <?php
                    $esFinalizada = $jor['status_jor'] == 2;
                    $estadoTexto  = $jor['status_jor'] == 1 ? 'ACTIVA' : ($esFinalizada ? 'FINALIZADA' : 'INACTIVA');
                    $estadoClass  = $jor['status_jor'] == 1 ? 'jornada-active' : ($esFinalizada ? 'jornada-finished' : 'jornada-inactive');
                    ?>
                    <article class="jornada-card <?= $estadoClass ?>">
                        <div class="jornada-card-icon <?= $estadoClass ?>"></div>

                        <div class="jornada-card-body">
                            <h3><?= esc($jor['nombre_jornada']) ?></h3>
                            <p class="jornada-org"><?= esc($jor['nombre_org'] ?? 'Sin organización') ?></p>

                            <?php if (!empty($jor['pesquisas'])): ?>
                                <div class="jornada-pesquisas">
                                    <?php foreach (explode(',', $jor['pesquisas']) as $pid): ?>
                                        <?php $pid = trim($pid);
                                        if (isset($pesquisaMap[$pid])): ?>
                                            <span class="<?= $pesquisaMap[$pid]['clase'] ?>"
                                                title="<?= esc($pesquisaMap[$pid]['nombre']) ?>">
                                                <img src="<?= base_url('img/' . $pesquisaMap[$pid]['emoji']) ?>" width="36">

                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <p class="jornada-location">
                                <?= esc($jor['nombre_institucion'] ?? '') ?>
                                <?php if (!empty($jor['ciudad'])): ?> — <?= esc($jor['ciudad']) ?><?php endif; ?>
                            </p>
                        </div>

                        <div class="jornada-card-side">
                            <span class="jornada-status <?= $estadoClass ?>"><?= $estadoTexto ?></span>
                            <small>
                                <?= !empty($jor['fecha_inicio']) && $jor['fecha_inicio'] !== '0000-00-00'
                                    ? date('d M Y', strtotime($jor['fecha_inicio']))
                                    : 'Sin fecha' ?>
                            </small>

                            <div class="jornada-actions">
                                <a href="<?= $esFinalizada ? '#' : base_url('jornadas/editar/' . $jor['id_jornada']) ?>"
                                    class="<?= $esFinalizada ? 'jornada-disabled' : '' ?>">Editar</a>

                                <?php if (in_array(session('id_rol'), [1, 2, 3, 4])): ?>
                                    <a href="<?= base_url('jornadas/' . $jor['id_jornada'] . '/usuarios') ?>">Usuarios</a>
                                <?php else: ?>
                                    <a href="#" class="jornada-disabled">Usuarios</a>
                                <?php endif; ?>

                                <a href="<?= base_url('jornadas/' . $jor['id_jornada'] . '/beneficiarios') ?>">Beneficiarios</a>
                                <a href="<?= base_url('jornadas/' . $jor['id_jornada'] . '/reportes') ?>">Reportes</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </section>

            <!-- ═══ PAGINACIÓN ═══ -->

            <?php
            $total       = $pager->getTotal('jornadas');
            $currentPage = $pager->getCurrentPage('jornadas');
            $perPageView = $pager->getPerPage('jornadas');

            $desde = $total > 0 ? (($currentPage - 1) * $perPageView) + 1 : 0;
            $hasta = min($currentPage * $perPageView, $total);
            ?>

            <footer class="jornada-pagination">
                <span>
                    Mostrando <?= $desde ?> a <?= $hasta ?> de <?= $total ?> jornadas
                </span>

                <div class="jornada-pager-links">
                    <?= $pager->links('jornadas', 'default_full') ?>
                </div>
            </footer>

        <?php else: ?>
            <div class="jornada-alert jornada-alert-info" style="text-align:center; margin-top:24px;">
                <?= $busqueda !== '' || $status !== '' ? 'No se encontraron jornadas con los filtros aplicados.' : 'Crea tu primera jornada' ?>
            </div>
        <?php endif; ?>
    </main>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.jornada-auto-dismiss');

        alerts.forEach(function(alert) {
            setTimeout(function() {
                alert.style.transition = 'opacity .35s ease, transform .35s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-8px)';

                setTimeout(function() {
                    alert.remove();
                }, 350);
            }, 3000);
        });
    });
</script>
<?= $this->endSection() ?>
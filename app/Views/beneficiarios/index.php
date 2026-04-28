<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
/* ═══════════════════════════════════════════
   BENEFICIARIOS — INDEX
   ═══════════════════════════════════════════ */
.beneficiarios-page{
    max-width:1280px;
    margin:0 auto;
    padding:0 16px;
}

.beneficiarios-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:24px;
    flex-wrap:wrap;
}

.org-chip{
    display:flex;
    align-items:center;
    gap:10px;
    background:#eef0ff;
    color:#1f2f78;
    border-radius:999px;
    padding:10px 20px;
    font-weight:700;
    font-size:.92rem;
}

.top-actions{
    display:flex;
    align-items:center;
    gap:14px;
}

.btn-export{
    display:flex;
    align-items:center;
    gap:6px;
    background:#10b981;
    color:#fff;
    border:0;
    border-radius:8px;
    padding:10px 18px;
    font-weight:700;
    font-size:.85rem;
    text-decoration:none;
    transition:background .2s;
}
.btn-export:hover{
    background: #059669;
    color:#fff;
}

.total-beneficiarios{
    display:flex;
    align-items:center;
    gap:6px;
    background:#f0f4ff;
    color:#101a61;
    border-radius:999px;
    padding:10px 18px;
    font-weight:800;
    font-size:1rem;
}

/* LAYOUT 2 COLUMNAS */
.beneficiarios-layout{
    display:grid;
    grid-template-columns:280px 1fr;
    gap:24px;
}

/* PANEL FILTROS */
.filters-panel{
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:20px;
    height:fit-content;
    position:sticky;
    top:20px;
}

.filters-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:18px;
}

.filters-header h5{
    margin:0;
    color:#1f2f78;
    font-weight:800;
}

.filters-header a{
    color:#6366f1;
    text-decoration:none;
    font-size:.85rem;
}

.filter-group{
    margin-bottom:18px;
}

.filter-group label{
    display:block;
    margin-bottom:7px;
    color:#6b7280;
    font-size:.88rem;
    font-weight:600;
}

.filter-control{
    width:100%;
    height:44px;
    border:1px solid #d9deea;
    border-radius:7px;
    padding:0 12px;
    color:#334155;
    background:#fff;
}

.search-control{
    position:relative;
}

.search-control input{
    padding-right:38px;
}

.search-control i{
    position:absolute;
    right:13px;
    top:50%;
    transform:translateY(-50%);
    color:#64748b;
}

.btn-filter{
    width:100%;
    height:46px;
    border:0;
    background:#3695f5;
    color:#fff;
    border-radius:8px;
    font-weight:800;
    cursor:pointer;
    transition:background .2s;
}
.btn-filter:hover{
    background: #1b7ae2;
}

/* PANEL LISTA */
.list-panel{
    padding:0;
}

.list-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:22px;
}

.list-title{
    display:flex;
    align-items:center;
    gap:12px;
}

.list-title h1{
    margin:0;
    font-size:1.75rem;
    font-weight:800;
    color:#1f2f78;
}

.result-badge{
    background:#eef0ff;
    color:#4f46e5;
    border-radius:999px;
    padding:6px 12px;
    font-weight:700;
    font-size:.85rem;
}

/* TARJETAS */
.beneficiario-card{
    display:grid;
    grid-template-columns:60px 1fr auto auto;
    gap:16px;
    align-items:center;
    background:#fff;
    border:1px solid #e5e7eb;
    border-radius:12px;
    padding:18px 20px;
    margin-bottom:12px;
    transition:box-shadow .2s;
}
.beneficiario-card:hover{
    box-shadow:0 4px 16px rgba(0,0,0,.06);
}

.avatar-circle{
    width:52px;
    height:52px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight:800;
    font-size:1rem;
    color:#fff;
}
.avatar-m{ background:#3b82f6; }
.avatar-f{ background:#ec4899; }

.benef-info h3{
    margin:0 0 4px;
    font-size:1rem;
    font-weight:700;
    color:#1e293b;
}
.benef-info small{
    color:#64748b;
    font-size:.82rem;
}
.benef-info .org-tag{
    display:inline-block;
    background:#f0f4ff;
    color:#3b5998;
    border-radius:4px;
    padding:2px 8px;
    font-size:.75rem;
    font-weight:600;
    margin-top:4px;
}

.status-box{
    text-align:center;
    padding:0 16px;
    border-left:1px solid #e5e7eb;
}

.status-box small{
    display:block;
    color:#6b7280;
}

.card-actions{
    display:flex;
    justify-content:flex-end;
    align-items:center;
    gap:14px;
}

.btn-history{
    border:1px solid #3695f5;
    color:#2563eb;
    background:#fff;
    border-radius:7px;
    padding:9px 14px;
    font-weight:800;
    font-size:.78rem;
    text-decoration:none;
     
    transition:all .2s;
}
.btn-history:hover{
    background: #1b7ae2;
    color:#fff;
}

.empty-state{
    text-align:center;
    color:#94a3b8;
    padding:45px;
    border:1px dashed #d9deea;
    border-radius:10px;
}

/* PAGINACIÓN */
.pagination-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-top:22px;
    color:#64748b;
}

.pagination-nav{
    display:flex;
    gap:4px;
}

.pagination-nav a,
.pagination-nav span{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:38px;
    height:38px;
    border-radius:8px;
    border:1px solid #d9deea;
    text-decoration:none;
    font-weight:600;
    font-size:.88rem;
    color:#334155;
    background:#fff;
    transition:all .2s;
}

.pagination-nav a:hover{
    background: #eef0ff;
    border-color: #3695f5;
    color:#4f46e5;
}

.pagination-nav .pg-active{
    background:#101a61;
    color:#fff;
    border-color:#101a61;
}

.pagination-nav .pg-disabled{
    opacity:.4;
    pointer-events:none;
}

@media(max-width:992px){
    .beneficiarios-layout{
        grid-template-columns:1fr;
    }
    .filters-panel{
        position:static;
    }
    .beneficiario-card{
        grid-template-columns:50px 1fr;
    }
    .status-box,
    .card-actions{
        grid-column:2;
        border-left:0;
        padding-left:0;
        justify-content:flex-start;
    }
}

@media(max-width:576px){
    .beneficiarios-top,
    .list-header,
    .pagination-footer{
        flex-direction:column;
        align-items:flex-start;
    }
    .org-chip{
        width:100%;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
    $rolActual = (int) session()->get('id_rol');
    $page      = $page ?? 1;
    $perPage   = $perPage ?? 15;
    $totalPages = $totalPages ?? 1;
?>

<div class="beneficiarios-page">

    <!-- ═══ BARRA SUPERIOR ═══ -->
    <div class="beneficiarios-top">
        <div class="org-chip">
            <i class="bi bi-bank"></i>
            <span>
                <?php if (in_array($rolActual, [1, 2])): ?>
                    <?= !empty($organizacion_id)
                        ? esc(array_column(array_filter($organizaciones, fn($o) => $o['id_organizacion'] == $organizacion_id), 'nombre_org')[0] ?? 'Organización')
                        : 'Todas las organizaciones' ?>
                <?php else: ?>
                    <?= esc(session('nombre_org') ?? 'Mi organización') ?>
                <?php endif; ?>
            </span>
        </div>

        <div class="top-actions">
            <a href="<?= base_url('beneficiarios/exportar?' . http_build_query(array_filter([
                'q' => $q ?? '',
                'organizacion_id' => $organizacion_id ?? ''
            ]))) ?>" class="btn-export">
                <i class="bi bi-file-earmark-excel"></i>
                Exportar Excel
            </a>

            
        </div>
    </div>

    <!-- ═══ LAYOUT 2 COLUMNAS ═══ -->
    <div class="beneficiarios-layout">

        <!-- FILTROS -->
        <aside class="filters-panel">
            <form method="get" action="<?= base_url('beneficiarios') ?>">

                <div class="filters-header">
                    <h5>Filtros</h5>
                    <a href="<?= base_url('beneficiarios') ?>">Limpiar</a>
                </div>

                <div class="filter-group">
                    <label>Buscar beneficiario</label>
                    <div class="search-control">
                        <input
                            type="text"
                            name="q"
                            value="<?= esc($q ?? '') ?>"
                            class="filter-control"
                            placeholder="Nombre, apellido o ID...">
                        <i class="bi bi-search"></i>
                    </div>
                </div>

                <?php if (in_array($rolActual, [1, 2])): ?>
                    <div class="filter-group">
                        <label>Organización</label>
                        <select name="organizacion_id" class="filter-control">
                            <option value="">Todas las organizaciones</option>
                            <?php foreach ($organizaciones ?? [] as $org): ?>
                                <option
                                    value="<?= esc($org['id_organizacion']) ?>"
                                    <?= ($organizacion_id ?? '') == $org['id_organizacion'] ? 'selected' : '' ?>>
                                    <?= esc($org['nombre_org']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="btn-filter">
                    <i class="bi bi-funnel me-1"></i> Aplicar filtros
                </button>

            </form>
        </aside>

        <!-- LISTA -->
        <main class="list-panel">

            <div class="list-header">
                <div class="list-title">
                    <h2>Beneficiarios</h2>
                     
                </div>
                <div class="total-beneficiarios">
                    <i class="bi bi-person-check"></i>
                    <span><?= esc($totalBeneficiarios ?? 0) ?></span>
                </div>
            </div>

            <?php if (!empty($beneficiarios)): ?>
                <?php foreach ($beneficiarios as $b): ?>
                    <?php
                        $nombreCompleto = esc(strtoupper(trim(($b['apellidos'] ?? '') . ', ' . ($b['nombres'] ?? ''))));
                        $iniciales = strtoupper(
                            mb_substr($b['nombres'] ?? 'B', 0, 1) .
                            mb_substr($b['apellidos'] ?? 'N', 0, 1)
                        );
                        $sexoClass = ($b['sexo'] ?? 'M') === 'F' ? 'avatar-f' : 'avatar-m';

                        // Edad
                        $edad = '—';
                        if (!empty($b['fecha_nacimiento'])) {
                            try {
                                $nac  = new \DateTime($b['fecha_nacimiento']);
                                $diff = (new \DateTime())->diff($nac);
                                $edad = $diff->y > 0
                                    ? $diff->y . ' año' . ($diff->y > 1 ? 's' : '')
                                    : ($diff->m > 0 ? $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '') : $diff->d . ' días');
                            } catch (\Exception $e) { $edad = '—'; }
                        }
                    ?>

                    <div class="beneficiario-card">
                        <div class="avatar-circle <?= $sexoClass ?>">
                            <?= $iniciales ?>
                        </div>

                        <div class="benef-info">
                            <h3><?= $nombreCompleto ?></h3>
                            <small>
                                <strong>ID:</strong> <?= esc($b['id_digisalud'] ?? '—') ?>
                                &nbsp;|&nbsp;
                                <strong>FN:</strong> <?= !empty($b['fecha_nacimiento']) ? date('d/m/Y', strtotime($b['fecha_nacimiento'])) : '—' ?>
                                (<?= $edad ?>)
                                &nbsp;|&nbsp;
                                <strong>Sexo:</strong> <?= esc($b['sexo'] ?? '—') ?>
                            </small>
                            <?php if (!empty($b['nombre_org'])): ?>
                                <div>
                                    <span class="org-tag">
                                        <i class="bi bi-building"></i>
                                        <?= esc($b['nombre_org']) ?>
                                    </span>
                                    <?php if (!empty($b['nombre_jornada'])): ?>
                                        <span class="org-tag">
                                            <i class="bi bi-calendar-event"></i>
                                            <?= esc($b['nombre_jornada']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="status-box">
                            <small>
                                <?php if (!empty($b['nombre_escuela'])): ?>
                                    <i class="bi bi-mortarboard"></i>
                                    <?= esc($b['nombre_escuela']) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </small>
                        </div>

                        <div class="card-actions">
                            <a href="<?= base_url('beneficiarios/historial/' . $b['id_beneficiario']) ?>"
                               class="btn-history">
                                Historial
                            </a>
                        </div>
                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="bi bi-search" style="font-size:2rem;display:block;margin-bottom:12px;"></i>
                    No se encontraron beneficiarios con los filtros aplicados.
                </div>
            <?php endif; ?>

            <!-- ═══ PAGINACIÓN ═══ -->
            <?php if ($totalPages > 1): ?>
                <div class="pagination-footer">
                    <p>
                        Mostrando
                        <?= (($page - 1) * $perPage) + 1 ?>
                        a
                        <?= min($page * $perPage, $totalBeneficiarios) ?>
                        de
                        <?= esc($totalBeneficiarios) ?> resultados
                    </p>

                    <div class="pagination-nav">
                        <?php
                            // Construir query string base
                            $qsBase = array_filter([
                                'q'               => $q ?? '',
                                'organizacion_id' => $organizacion_id ?? '',
                            ]);

                            $linkPage = function($p) use ($qsBase) {
                                $qs = array_merge($qsBase, ['page' => $p]);
                                return base_url('beneficiarios?' . http_build_query($qs));
                            };
                        ?>

                        <!-- Anterior -->
                        <?php if ($page > 1): ?>
                            <a href="<?= $linkPage($page - 1) ?>">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        <?php else: ?>
                            <span class="pg-disabled"><i class="bi bi-chevron-left"></i></span>
                        <?php endif; ?>

                        <!-- Números -->
                        <?php
                            $start = max(1, $page - 2);
                            $end   = min($totalPages, $page + 2);
                            if ($start > 1) echo '<a href="' . $linkPage(1) . '">1</a>';
                            if ($start > 2) echo '<span class="pg-disabled">…</span>';
                            for ($i = $start; $i <= $end; $i++):
                        ?>
                            <?php if ($i == $page): ?>
                                <span class="pg-active"><?= $i ?></span>
                            <?php else: ?>
                                <a href="<?= $linkPage($i) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php
                            endfor;
                            if ($end < $totalPages - 1) echo '<span class="pg-disabled">…</span>';
                            if ($end < $totalPages) echo '<a href="' . $linkPage($totalPages) . '">' . $totalPages . '</a>';
                        ?>

                        <!-- Siguiente -->
                        <?php if ($page < $totalPages): ?>
                            <a href="<?= $linkPage($page + 1) ?>">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        <?php else: ?>
                            <span class="pg-disabled"><i class="bi bi-chevron-right"></i></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="pagination-footer">
                    <p>Mostrando <?= count($beneficiarios ?? []) ?> de <?= esc($totalBeneficiarios ?? 0) ?> resultados</p>
                </div>
            <?php endif; ?>

        </main>

    </div>
</div>

<?= $this->endSection() ?>
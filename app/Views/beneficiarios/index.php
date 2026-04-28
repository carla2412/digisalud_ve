<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
.beneficiarios-page{
    background:#f6f7fb;
    min-height:calc(100vh - 70px);
    padding:22px;
    color:#26345d;
}

.beneficiarios-top{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-bottom:18px;
}

.org-chip{
    min-width:260px;
    height:52px;
    border:1px solid #d9deea;
    border-radius:8px;
    background:#fff;
    display:flex;
    align-items:center;
    gap:12px;
    padding:0 16px;
    color:#34427a;
    font-weight:700;
}

.top-actions{
    display:flex;
    align-items:center;
    gap:22px;
}

.btn-export{
    border:1px solid #356df0;
    color:#356df0;
    background:#fff;
    border-radius:7px;
    padding:12px 22px;
    font-weight:700;
    text-decoration:none;
    display:inline-flex;
    gap:8px;
    align-items:center;
}

.total-beneficiarios{
    display:flex;
    align-items:center;
    gap:10px;
    color:#8c50a7;
    font-size:1.8rem;
    font-weight:800;
}

.beneficiarios-layout{
    display:grid;
    grid-template-columns:300px 1fr;
    gap:24px;
}

.filters-panel,
.list-panel{
    background:#fff;
    border:1px solid #dde2ee;
    border-radius:10px;
    box-shadow:0 8px 24px rgba(15,23,42,.04);
}

.filters-panel{
    padding:22px;
}

.filters-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.filters-header h5{
    margin:0;
    color:#243473;
    font-weight:800;
    text-transform:uppercase;
    font-size:.92rem;
}

.filters-header a{
    color:#2563eb;
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

.status-buttons{
    display:flex;
    gap:8px;
}

.status-buttons button{
    border:1px solid #dde2ee;
    background:#fff;
    border-radius:999px;
    padding:9px 14px;
    color:#64748b;
    font-weight:600;
}

.status-buttons .active{
    background:#673ab7;
    color:#fff;
    border-color:#673ab7;
}

.btn-filter{
    width:100%;
    height:46px;
    border:0;
    background:#673ab7;
    color:#fff;
    border-radius:8px;
    font-weight:800;
}

.list-panel{
    padding:24px;
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

.order-box{
    display:flex;
    align-items:center;
    gap:10px;
}

.order-box label{
    color:#6b7280;
    font-size:.85rem;
}

.order-box select{
    height:42px;
    border:1px solid #d9deea;
    border-radius:7px;
    padding:0 12px;
    background:#fff;
}

.beneficiario-card{
    display:grid;
    grid-template-columns:72px 1fr 190px 170px;
    gap:20px;
    align-items:center;
    border:1px solid #e1e6f0;
    border-radius:10px;
    padding:18px;
    margin-bottom:12px;
    background:#fff;
    transition:.2s ease;
}

.beneficiario-card:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 25px rgba(15,23,42,.08);
}

.avatar{
    width:58px;
    height:58px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:1.35rem;
    font-weight:800;
}

.avatar-purple{background:#e8ddff;color:#5b38c2;}
.avatar-green{background:#d8f5e8;color:#18765c;}
.avatar-yellow{background:#fff0c8;color:#a56b00;}
.avatar-pink{background:#ffddeb;color:#a61b57;}
.avatar-blue{background:#dcecff;color:#2563eb;}

.beneficiario-info h4{
    margin:0 0 6px;
    color:#1f2f78;
    font-size:1rem;
    font-weight:800;
    text-transform:uppercase;
}

.beneficiario-info p{
    margin:3px 0;
    color:#6b7280;
    font-size:.9rem;
}

.beneficiario-info strong,
.beneficiario-info .label-blue{
    color:#2563eb;
}

.status-box{
    border-left:1px solid #e5e7eb;
    padding-left:20px;
}

.status-box span{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#dcfce7;
    color:#16803a;
    border-radius:6px;
    padding:7px 10px;
    font-size:.78rem;
    font-weight:800;
    margin-bottom:8px;
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
    border:1px solid #2563eb;
    color:#2563eb;
    background:#fff;
    border-radius:7px;
    padding:9px 14px;
    font-weight:800;
    font-size:.78rem;
    text-decoration:none;
    text-transform:uppercase;
}

.empty-state{
    text-align:center;
    color:#94a3b8;
    padding:45px;
    border:1px dashed #d9deea;
    border-radius:10px;
}

.pagination-footer{
    display:flex;
    justify-content:space-between;
    align-items:center;
    gap:16px;
    margin-top:22px;
    color:#64748b;
}

.pagination-wrapper{
    display:flex;
    justify-content:center;
}

@media(max-width:992px){
    .beneficiarios-layout{
        grid-template-columns:1fr;
    }

    .beneficiario-card{
        grid-template-columns:60px 1fr;
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

<div class="beneficiarios-page">

    <div class="beneficiarios-top">
        <div class="org-chip">
            <i class="bi bi-bank"></i>
            <span>
                <?= in_array(session('id_rol'), [1, 2])
                    ? 'Todas las organizaciones'
                    : esc(session('nombre_org') ?? 'Mi organización') ?>
            </span>
            <i class="bi bi-chevron-down ms-auto"></i>
        </div>

        <div class="top-actions">
            <a href="<?= base_url('beneficiarios/exportar?' . http_build_query([
                'q' => $q ?? '',
                'organizacion_id' => $organizacion_id ?? ''
            ])) ?>" class="btn-export">
                <i class="bi bi-cloud-arrow-down"></i>
                Exportar
            </a>

            <div class="total-beneficiarios">
                <i class="bi bi-person-check"></i>
                <span><?= esc($totalBeneficiarios ?? 0) ?></span>
            </div>
        </div>
    </div>

    <div class="beneficiarios-layout">

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
                            placeholder="Buscar por nombre o ID...">
                        <i class="bi bi-search"></i>
                    </div>
                </div>

                <?php if (in_array(session('id_rol'), [1, 2])): ?>
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

                <div class="filter-group">
                    <label>Estado</label>
                    <div class="status-buttons">
                        <button type="button" class="active">Todos</button>
                        <button type="button">Activos</button>
                    </div>
                </div>

                <button type="submit" class="btn-filter">
                    Aplicar filtros
                </button>

            </form>
        </aside>

        <main class="list-panel">

            <div class="list-header">
                <div class="list-title">
                    <h1>Beneficiarios</h1>
                    <span class="result-badge">
                        <?= count($beneficiarios ?? []) ?> resultados
                    </span>
                </div>

                <div class="order-box">
                    <label>Ordenar por</label>
                    <select>
                        <option>Nombre (A - Z)</option>
                    </select>
                </div>
            </div>

            <?php if (!empty($beneficiarios)): ?>
                <?php foreach ($beneficiarios as $index => $b): ?>

                    <?php
                        $nombreCompleto = trim(($b['apellidos'] ?? '') . ', ' . ($b['nombres'] ?? ''));
                        $iniciales = strtoupper(
                            mb_substr($b['nombres'] ?? 'B', 0, 1) .
                            mb_substr($b['apellidos'] ?? 'N', 0, 1)
                        );

                        $avatarClasses = [
                            'avatar-purple',
                            'avatar-green',
                            'avatar-yellow',
                            'avatar-pink',
                            'avatar-blue'
                        ];

                        $avatarClass = $avatarClasses[$index % count($avatarClasses)];
                    ?>

                    <div class="beneficiario-card">

                        <div class="avatar <?= esc($avatarClass) ?>">
                            <?= esc($iniciales) ?>
                        </div>

                        <div class="beneficiario-info">
                            <h4><?= esc($nombreCompleto ?: 'Beneficiario sin nombre') ?></h4>

                            <p>
                                <span class="label-blue">ID:</span>
                                <?= esc($b['identificacion'] ?? 'N/D') ?>

                                <span class="label-blue ms-2">FN:</span>
                                <?= esc($b['fecha_nacimiento'] ?? $b['fn'] ?? 'N/D') ?>
                            </p>

                            <p>
                                <i class="bi bi-calendar3"></i>
                                <?= esc($b['edad_texto'] ?? '-') ?>

                                <i class="bi bi-geo-alt ms-2"></i>
                                <?= esc($b['ubicacion'] ?? 'Venezuela') ?>
                            </p>

                            <p>
                                <span class="label-blue">Centro / Jornada:</span>
                                <strong>
                                    <?= esc($b['nombre_jornada'] ?? $b['centro'] ?? 'Sin jornada') ?>
                                </strong>
                            </p>
                        </div>

                        <div class="status-box">
                            <span>
                                <i class="bi bi-calendar-check"></i>
                                Registro activo
                            </span>
                            <small>
                                Creado:
                                <?= esc($b['fecha_nacimiento'] ?? $b['fn'] ?? '-') ?>
                            </small>
                        </div>

                        <div class="card-actions">
                            <a
                                href="<?= base_url('beneficiarios/' . $b['id_beneficiario'] . '/historial') ?>"
                                class="btn-history">
                                Ver historial
                            </a>

                            <i class="bi bi-three-dots-vertical"></i>
                        </div>

                    </div>

                <?php endforeach; ?>
            <?php else: ?>
                <div class="empty-state">
                    No se encontraron beneficiarios con los filtros aplicados.
                </div>
            <?php endif; ?>

            <div class="pagination-footer">
                <p>
                    Mostrando <?= count($beneficiarios ?? []) ?> de
                    <?= esc($totalBeneficiarios ?? 0) ?> resultados
                </p>

                <div class="pagination-wrapper">
                    <?= $pager->links('beneficiarios', 'default_full') ?>
                </div>
            </div>

        </main>

    </div>
</div>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    :root {
        --benef-primary: #3695f5;
        --benef-primary-dark: #1b7ae2;
        --benef-dark: #101a61;
        --benef-bg: #f5f8fc;
        --benef-light: #ffffff;
        --benef-soft: #eaf6ff;
        --benef-card: #ffffff;
        --benef-border: #dcecff;
        --benef-border-2: #e0e6ed;
        --benef-muted: #5b6472;
        --benef-success: #22a447;
        --benef-purple: #7c3aed;
        --benef-danger: #e62e45;
    }

    .benef-page {
        background: var(--benef-bg);
        min-height: 100vh;
        padding: 24px 16px;
    }

    .benef-container {
        max-width: 1500px;
        margin: 0 auto;
    }

    .benef-topbar {
        background: var(--benef-light);
        border: 1px solid var(--benef-border-2);
        border-radius: 18px;
        padding: 20px 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        margin-bottom: 24px;
    }

    .benef-title {
        margin: 0;
        color: var(--benef-dark);
        font-size: 30px;
        font-weight: 800;
    }

    .benef-subtitle {
        margin: 4px 0 0;
        color: var(--benef-muted);
        font-size: 14px;
    }

    .benef-export-btn {
        border: none;
        background: var(--benef-success);
        color: #fff;
        padding: 12px 18px;
        border-radius: 10px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }

    .benef-export-btn:hover {
        color: #fff;
        background: #1e913e;
    }

    .benef-filterbar {
        background: var(--benef-soft);
        border: 1px solid #d6eafe;
        border-radius: 22px;
        padding: 24px;
        margin-bottom: 28px;
    }

    .benef-filter-head {
        margin-bottom: 18px;
    }

    .benef-filter-title {
        margin: 0;
        color: var(--benef-dark);
        font-size: 22px;
        font-weight: 800;
    }

    .benef-filter-note {
        margin: 4px 0 0;
        color: var(--benef-muted);
        font-size: 14px;
    }

    .benef-filter-grid {
        display: grid;
        grid-template-columns: 1.4fr 1fr auto auto;
        gap: 16px;
        align-items: end;
    }

    .benef-field {
        display: flex;
        flex-direction: column;
        gap: 7px;
    }

    .benef-label {
        color: #4b5563;
        font-size: 13px;
        font-weight: 700;
    }

    .benef-input-wrap {
        position: relative;
    }

    .benef-input,
    .benef-select {
        width: 100%;
        border: 1px solid #cfe0f2;
        background: #fff;
        border-radius: 12px;
        padding: 12px 42px 12px 14px;
        font-size: 15px;
        color: #4b5563;
        outline: none;
        min-height: 48px;
    }

    .benef-input:focus,
    .benef-select:focus {
        border-color: var(--benef-primary);
        box-shadow: 0 0 0 3px rgba(54, 149, 245, 0.15);
    }

    .benef-search-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #4b5563;
        font-size: 18px;
        pointer-events: none;
    }

    .benef-apply-btn,
    .benef-clear-btn {
        min-height: 48px;
        border-radius: 12px;
        padding: 0 18px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .benef-apply-btn {
        border: none;
        background: var(--benef-primary);
        color: #fff;
    }

    .benef-apply-btn:hover {
        background: var(--benef-primary-dark);
    }

    .benef-clear-btn {
        border: 1px solid #cfe0f2;
        background: #fff;
        color: var(--benef-primary);
    }

    .benef-clear-btn:hover {
        color: var(--benef-primary-dark);
        background: #f7fbff;
    }

    .benef-main-grid {
        display: grid;
        grid-template-columns: 310px 1fr;
        gap: 28px;
    }

    .benef-summary-panel {
        background: #fff;
        border: 1px solid var(--benef-border-2);
        border-radius: 20px;
        padding: 24px;
        align-self: start;
        position: sticky;
        top: 20px;
    }

    .benef-summary-title {
        margin: 0 0 18px;
        color: var(--benef-dark);
        font-size: 22px;
        font-weight: 800;
    }

    .benef-stat-card {
        background: #f7fbff;
        border: 1px solid var(--benef-border-2);
        border-radius: 16px;
        padding: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 14px;
    }

    .benef-stat-icon {
        width: 46px;
        height: 46px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 800;
        flex: 0 0 auto;
    }

    .benef-stat-icon-primary {
        background: var(--benef-primary);
    }

    .benef-stat-icon-purple {
        background: var(--benef-purple);
    }

    .benef-stat-icon-success {
        background: var(--benef-success);
    }

    .benef-stat-number {
        margin: 0;
        color: var(--benef-dark);
        font-size: 20px;
        font-weight: 900;
        line-height: 1;
    }

    .benef-stat-label {
        margin: 4px 0 0;
        color: var(--benef-muted);
        font-size: 13px;
    }

    .benef-help-title {
        color: var(--benef-dark);
        font-size: 17px;
        font-weight: 800;
        margin: 28px 0 12px;
    }

    .benef-help-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .benef-help-list li {
        color: #4b5563;
        font-size: 13px;
        margin-bottom: 10px;
        display: flex;
        gap: 8px;
        line-height: 1.35;
    }

    .benef-help-list li::before {
        content: "✓";
        color: var(--benef-success);
        font-weight: 900;
        flex: 0 0 auto;
    }

    .benef-results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 18px;
    }

    .benef-results-title {
        margin: 0;
        color: var(--benef-dark);
        font-size: 28px;
        font-weight: 800;
    }

    .benef-results-count {
        margin: 4px 0 0;
        color: var(--benef-muted);
        font-size: 14px;
    }

    .benef-actions-top {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .benef-total-pill {
        border-radius: 999px;
        background: #fff;
        border: 1px solid var(--benef-border-2);
        color: var(--benef-dark);
        min-height: 42px;
        padding: 0 16px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 900;
    }

    .benef-list {
        display: flex;
        flex-direction: column;
        gap: 14px;
    }

    .benef-card {
        background: var(--benef-card);
        border: 1px solid var(--benef-border);
        border-radius: 18px;
        padding: 22px 24px;
        display: grid;
        grid-template-columns: 66px 1fr auto auto;
        gap: 20px;
        align-items: center;
        transition: box-shadow .2s ease, transform .2s ease;
    }

    .benef-card:hover {
        box-shadow: 0 6px 18px rgba(16, 26, 97, .08);
        transform: translateY(-1px);
    }

    .benef-avatar {
        width: 62px;
        height: 62px;
        border-radius: 50%;
        display: grid;
        place-items: center;
        color: #fff;
        font-weight: 900;
        font-size: 18px;
        background: var(--benef-primary);
    }

    .benef-avatar-f {
        background: var(--benef-danger);
    }

    .benef-avatar-m {
        background: var(--benef-primary);
    }

    .benef-name {
        margin: 0 0 8px;
        color: var(--benef-dark);
        font-size: 18px;
        font-weight: 900;
        text-transform: uppercase;
    }

    .benef-meta {
        margin: 0;
        color: #4b5563;
        font-size: 14px;
    }

    .benef-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 10px;
    }

    .benef-tag {
        background: #f8f5ff;
        border: 1px solid #eadcff;
        color: var(--benef-purple);
        border-radius: 8px;
        padding: 5px 9px;
        font-size: 12px;
        font-weight: 800;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .benef-school {
        color: #4b5563;
        font-size: 14px;
        border-left: 1px solid var(--benef-border-2);
        padding-left: 20px;
        min-width: 150px;
    }

    .benef-school-empty {
        color: #9ca3af;
    }

    .benef-card-actions {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .benef-action-btn {
        border-radius: 12px;
        min-height: 42px;
        padding: 0 16px;
        font-weight: 800;
        cursor: pointer;
        white-space: nowrap;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .benef-history-btn {
        border: 1px solid #b9ddff;
        background: #eef7ff;
        color: var(--benef-primary-dark);
    }

    .benef-history-btn:hover {
        background: var(--benef-primary);
        color: #fff;
    }

    .benef-empty-state {
        text-align: center;
        color: var(--benef-muted);
        padding: 45px;
        border: 1px dashed var(--benef-muted);
        border-radius: 16px;
        background: #fff;
    }

    .benef-empty-state i {
        font-size: 2rem;
        display: block;
        margin-bottom: 12px;
        color: var(--benef-primary);
    }

    .benef-pagination {
        margin-top: 20px;
        background: #fff;
        border: 1px solid var(--benef-border-2);
        border-radius: 16px;
        padding: 14px 18px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
    }

    .benef-page-info {
        color: #4b5563;
        font-size: 14px;
        margin: 0;
    }

    .benef-page-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .benef-page-btn {
        border: 1px solid #d1d5db;
        background: #fff;
        color: #4b5563;
        border-radius: 8px;
        min-width: 38px;
        height: 38px;
        padding: 0 12px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .benef-page-btn:hover {
        border-color: var(--benef-primary);
        color: var(--benef-primary-dark);
        background: #f7fbff;
    }

    .benef-page-btn-active {
        border-color: var(--benef-dark);
        background: var(--benef-dark);
        color: #fff;
    }

    .benef-page-btn-active:hover {
        color: #fff;
        background: var(--benef-dark);
    }

    .benef-page-btn-disabled {
        color: #9ca3af;
        cursor: not-allowed;
        opacity: .5;
        pointer-events: none;
    }

    @media (max-width: 1200px) {
        .benef-filter-grid {
            grid-template-columns: 1fr 1fr;
        }

        .benef-main-grid {
            grid-template-columns: 1fr;
        }

        .benef-summary-panel {
            display: none;
        }

        .benef-card {
            grid-template-columns: 60px 1fr;
        }

        .benef-school,
        .benef-card-actions {
            grid-column: 2;
            border-left: 0;
            padding-left: 0;
            justify-content: flex-start;
            flex-wrap: wrap;
        }
    }

    @media (max-width: 768px) {
        .benef-page {
            padding: 14px;
        }

        .benef-topbar,
        .benef-results-header,
        .benef-pagination {
            flex-direction: column;
            align-items: stretch;
        }

        .benef-filter-grid {
            grid-template-columns: 1fr;
        }

        .benef-card {
            padding: 18px;
            grid-template-columns: 1fr;
        }

        .benef-avatar {
            width: 54px;
            height: 54px;
        }

        .benef-school,
        .benef-card-actions {
            grid-column: auto;
        }

        .benef-action-btn {
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$rolActual  = (int) session()->get('id_rol');
$page       = $page ?? 1;
$perPage    = $perPage ?? 15;
$totalPages = $totalPages ?? 1;

$q               = $q ?? '';
$organizacion_id = $organizacion_id ?? '';
$totalBeneficiarios = $totalBeneficiarios ?? 0;

$organizacionesCount = count($organizaciones ?? []);
$jornadasCount = 0;

if (!empty($beneficiarios)) {
    $jornadasTemp = [];

    foreach ($beneficiarios as $benefTemp) {
        if (!empty($benefTemp['nombre_jornada'])) {
            $jornadasTemp[$benefTemp['nombre_jornada']] = true;
        }
    }

    $jornadasCount = count($jornadasTemp);
}

$exportQuery = array_filter([
    'q' => $q,
    'organizacion_id' => $organizacion_id,
]);

$limpiarUrl = base_url('beneficiarios');

$qsBase = array_filter([
    'q'               => $q,
    'organizacion_id' => $organizacion_id,
]);

$linkPage = function ($p) use ($qsBase) {
    $qs = array_merge($qsBase, ['page' => $p]);
    return base_url('beneficiarios?' . http_build_query($qs));
};
?>

<div class="benef-page">
    <div class="benef-container">

        <header class="benef-topbar">
            <div>
                <h1 class="benef-title">Beneficiarios</h1>
                <p class="benef-subtitle">
                    Consulta general de beneficiarios por organización o datos personales.
                </p>
            </div>

            <a href="<?= base_url('beneficiarios/exportar?' . http_build_query($exportQuery)) ?>" class="benef-export-btn">
                <i class="bi bi-file-earmark-excel"></i>
                Exportar Excel
            </a>
        </header>

        <section class="benef-filterbar">
            <div class="benef-filter-head">
                <h2 class="benef-filter-title">Filtros de búsqueda</h2>
                <p class="benef-filter-note">
                    Usa un solo filtro de organización para evitar duplicidad en la vista.
                </p>
            </div>

            <form class="benef-filter-grid" method="get" action="<?= base_url('beneficiarios') ?>">
                <div class="benef-field">
                    <label class="benef-label" for="benefSearch">Buscar beneficiario</label>
                    <div class="benef-input-wrap">
                        <input
                            type="text"
                            id="benefSearch"
                            name="q"
                            class="benef-input"
                            placeholder="Nombre, apellido o ID..."
                            value="<?= esc($q) ?>">
                        <span class="benef-search-icon">
                            <i class="bi bi-search"></i>
                        </span>
                    </div>
                </div>

                <?php if (in_array($rolActual, [1, 2], true)): ?>
                    <div class="benef-field">
                        <label class="benef-label" for="benefOrganizacion">Organización</label>
                        <select id="benefOrganizacion" name="organizacion_id" class="benef-select">
                            <option value="">Todas las organizaciones</option>

                            <?php foreach ($organizaciones ?? [] as $org): ?>
                                <option
                                    value="<?= esc($org['id_organizacion']) ?>"
                                    <?= ($organizacion_id == $org['id_organizacion']) ? 'selected' : '' ?>>
                                    <?= esc($org['nombre_org']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <button type="submit" class="benef-apply-btn">
                    <i class="bi bi-funnel"></i>
                    Aplicar filtros
                </button>

                <a href="<?= $limpiarUrl ?>" class="benef-clear-btn">
                    Limpiar
                </a>
            </form>
        </section>

        <main class="benef-main-grid">

            <aside class="benef-summary-panel">
                <h2 class="benef-summary-title">Resumen</h2>

                <div class="benef-stat-card">
                    <div class="benef-stat-icon benef-stat-icon-primary">
                        <?= esc($totalBeneficiarios) ?>
                    </div>
                    <div>
                        <p class="benef-stat-number"><?= esc($totalBeneficiarios) ?></p>
                        <p class="benef-stat-label">Beneficiarios según filtros</p>
                    </div>
                </div>

                <?php if (in_array($rolActual, [1, 2], true)): ?>
                    <div class="benef-stat-card">
                        <div class="benef-stat-icon benef-stat-icon-purple">
                            <?= esc($organizacionesCount) ?>
                        </div>
                        <div>
                            <p class="benef-stat-number"><?= esc($organizacionesCount) ?></p>
                            <p class="benef-stat-label">Organizaciones disponibles</p>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="benef-stat-card">
                    <div class="benef-stat-icon benef-stat-icon-success">
                        <?= esc($jornadasCount) ?>
                    </div>
                    <div>
                        <p class="benef-stat-number"><?= esc($jornadasCount) ?></p>
                        <p class="benef-stat-label">Jornadas en resultados</p>
                    </div>
                </div>
  
            </aside>

            <section class="benef-results">
                <div class="benef-results-header">
                    <div>
                        <h2 class="benef-results-title">Resultados</h2>
                        <p class="benef-results-count">
                            <?php if (!empty($beneficiarios)): ?>
                                Mostrando <?= count($beneficiarios) ?> de <?= esc($totalBeneficiarios) ?> beneficiarios
                            <?php else: ?>
                                No hay beneficiarios para mostrar
                            <?php endif; ?>
                        </p>
                    </div>

                    <div class="benef-actions-top">
                        <div class="benef-total-pill">
                            <i class="bi bi-person-check"></i>
                            <span><?= esc($totalBeneficiarios) ?></span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($beneficiarios)): ?>
                    <div class="benef-list">

                        <?php foreach ($beneficiarios as $b): ?>
                            <?php
                            $nombreCompleto = esc(strtoupper(trim(($b['apellidos'] ?? '') . ', ' . ($b['nombres'] ?? ''))));

                            $iniciales = strtoupper(
                                mb_substr($b['nombres'] ?? 'B', 0, 1) .
                                mb_substr($b['apellidos'] ?? 'N', 0, 1)
                            );

                            $sexoClass = ($b['sexo'] ?? 'M') === 'F'
                                ? 'benef-avatar-f'
                                : 'benef-avatar-m';

                            $edad = '—';

                            if (!empty($b['fecha_nacimiento'])) {
                                try {
                                    $nac  = new \DateTime($b['fecha_nacimiento']);
                                    $diff = (new \DateTime())->diff($nac);

                                    $edad = $diff->y > 0
                                        ? $diff->y . ' año' . ($diff->y > 1 ? 's' : '')
                                        : ($diff->m > 0
                                            ? $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '')
                                            : $diff->d . ' días');
                                } catch (\Exception $e) {
                                    $edad = '—';
                                }
                            }

                            $fechaNacimiento = !empty($b['fecha_nacimiento'])
                                ? date('d/m/Y', strtotime($b['fecha_nacimiento']))
                                : '—';
                            ?>

                            <article class="benef-card">
                                <div class="benef-avatar <?= $sexoClass ?>">
                                    <?= esc($iniciales) ?>
                                </div>

                                <div class="benef-info">
                                    <h3 class="benef-name"><?= $nombreCompleto ?></h3>

                                    <p class="benef-meta">
                                        <strong>ID:</strong> <?= esc($b['id_digisalud'] ?? '—') ?>
                                        &nbsp;|&nbsp;
                                        <strong>FN:</strong> <?= esc($fechaNacimiento) ?>
                                        (<?= esc($edad) ?>)
                                        &nbsp;|&nbsp;
                                        <strong>Sexo:</strong> <?= esc($b['sexo'] ?? '—') ?>
                                    </p>

                                    <?php if (!empty($b['nombre_org']) || !empty($b['nombre_jornada'])): ?>
                                        <div class="benef-tags">
                                            <?php if (!empty($b['nombre_org'])): ?>
                                                <span class="benef-tag">
                                                    <i class="bi bi-building"></i>
                                                    <?= esc($b['nombre_org']) ?>
                                                </span>
                                            <?php endif; ?>

                                            <?php if (!empty($b['nombre_jornada'])): ?>
                                                <span class="benef-tag">
                                                    <i class="bi bi-calendar-event"></i>
                                                    <?= esc($b['nombre_jornada']) ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="benef-school <?= empty($b['nombre_escuela']) ? 'benef-school-empty' : '' ?>">
                                    <?php if (!empty($b['nombre_escuela'])): ?>
                                        <i class="bi bi-mortarboard"></i>
                                        <?= esc($b['nombre_escuela']) ?>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </div>

                                <div class="benef-card-actions">
                                    <a href="<?= base_url('beneficiarios/historial/' . $b['id_beneficiario']) ?>"
                                       class="benef-action-btn benef-history-btn">
                                        Historial
                                    </a>
                                </div>
                            </article>

                        <?php endforeach; ?>

                    </div>
                <?php else: ?>
                    <div class="benef-empty-state">
                        <i class="bi bi-search"></i>
                        No se encontraron beneficiarios con los filtros aplicados.
                    </div>
                <?php endif; ?>

                <?php if ($totalPages > 1): ?>
                    <div class="benef-pagination">
                        <p class="benef-page-info">
                            Mostrando
                            <?= (($page - 1) * $perPage) + 1 ?>
                            a
                            <?= min($page * $perPage, $totalBeneficiarios) ?>
                            de
                            <?= esc($totalBeneficiarios) ?>
                            resultados
                        </p>

                        <div class="benef-page-actions">
                            <?php if ($page > 1): ?>
                                <a href="<?= $linkPage($page - 1) ?>" class="benef-page-btn">
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="benef-page-btn benef-page-btn-disabled">
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <?php
                            $start = max(1, $page - 2);
                            $end   = min($totalPages, $page + 2);
                            ?>

                            <?php if ($start > 1): ?>
                                <a href="<?= $linkPage(1) ?>" class="benef-page-btn">1</a>
                            <?php endif; ?>

                            <?php if ($start > 2): ?>
                                <span class="benef-page-btn benef-page-btn-disabled">…</span>
                            <?php endif; ?>

                            <?php for ($i = $start; $i <= $end; $i++): ?>
                                <?php if ($i == $page): ?>
                                    <span class="benef-page-btn benef-page-btn-active"><?= esc($i) ?></span>
                                <?php else: ?>
                                    <a href="<?= $linkPage($i) ?>" class="benef-page-btn"><?= esc($i) ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>

                            <?php if ($end < $totalPages - 1): ?>
                                <span class="benef-page-btn benef-page-btn-disabled">…</span>
                            <?php endif; ?>

                            <?php if ($end < $totalPages): ?>
                                <a href="<?= $linkPage($totalPages) ?>" class="benef-page-btn">
                                    <?= esc($totalPages) ?>
                                </a>
                            <?php endif; ?>

                            <?php if ($page < $totalPages): ?>
                                <a href="<?= $linkPage($page + 1) ?>" class="benef-page-btn">
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="benef-page-btn benef-page-btn-disabled">
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="benef-pagination">
                        <p class="benef-page-info">
                            Mostrando <?= count($beneficiarios ?? []) ?> de <?= esc($totalBeneficiarios) ?> resultados
                        </p>
                    </div>
                <?php endif; ?>
            </section>

        </main>

    </div>
</div>

<?= $this->endSection() ?>
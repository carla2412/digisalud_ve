<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$jornada_id = $jornada_id ?? ($jornada['id_jornada'] ?? null);
$pesquisas_jornada = $pesquisas_jornada ?? [];

if (!empty($jornada['pesquisas']) && empty($pesquisas_jornada)) {
    $pesquisas_jornada = array_map('trim', explode(',', $jornada['pesquisas']));
}
?>
<style>
    :root {
        --ds-primary: #3695f5;
        --ds-primary-dark: #1b7ae2;
        --ds-dark: #101a61;
        --ds-bg: #f5f8fc;
        --ds-light: #ffffff;
        --ds-border: #e0e6ed;
        --ds-muted: #6b7280;
        --ds-text: #1f2937;
        --ds-success: #13b76a;
        --ds-success-bg: #e6f8ef;
        --ds-warning: #f59e0b;
        --ds-danger: #ff4b3e;
        --shadow-sm: 0 4px 12px rgba(16, 26, 97, 0.06);
        --shadow-md: 0 10px 28px rgba(16, 26, 97, 0.10);
        --radius-lg: 18px;
    }

    body {
        background: var(--ds-bg);
    }

    .benef-page {
        width: min(1480px, calc(100% - 48px));
        margin: 0 auto;
        padding: 28px 0 36px;
    }

    .breadcrumb-digi-new {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        color: #536580;
        font-size: 14px;
        margin-bottom: 16px;
    }

    .breadcrumb-digi-new a {
        color: #536580;
        text-decoration: none;
        font-weight: 600;
    }

    .breadcrumb-digi-new .active {
        color: var(--ds-dark);
        font-weight: 600;
    }

    .benef-topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .benef-title h1 {
        display: flex;
        align-items: center;
        gap: 12px;
        margin: 0;
        color: var(--ds-dark);
        font-size: 30px;
        line-height: 1.2;

    }

    .benef-counter {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
        height: 28px;
        padding: 0 10px;
        border-radius: 999px;
        background: #dfe7f4;
        color: var(--ds-dark);
        font-size: 15px;
        font-weight: 600;
    }

    .benef-title p {
        margin: 6px 0 0;
        color: #536580;
        font-size: 15px;
    }

    .benef-actions {
        display: flex;
        align-items: center;
        gap: 18px;
        flex-wrap: wrap;
    }

    .benef-total-pill {
        height: 44px;
        min-width: 88px;
        padding: 0 18px;
        border: 1.5px solid var(--ds-primary);
        border-radius: 12px;
        color: var(--ds-primary-dark);
        background: var(--ds-light);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        font-weight: 600;
    }

    .benef-divider {
        width: 1px;
        height: 36px;
        background: #b9c3d0;
    }

    .ds-btn-primary {
        height: 46px;
        padding: 0 28px;
        border: none;
        border-radius: 12px;
        background: linear-gradient(135deg, #3a8cff, #176be8);
        color: white;
        font-weight: 600;
        font-size: 15px;
        cursor: pointer;
        box-shadow: 0 8px 18px rgba(47, 128, 237, .25);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .ds-btn-primary:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .benef-search {
        position: relative;
        margin: 0 0 18px;
        max-width: 520px;
    }

    .benef-search i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ds-primary-dark);
    }

    .benef-search input {
        width: 100%;
        height: 46px;
        border: 1px solid var(--ds-border);
        border-radius: 14px;
        padding: 0 16px 0 44px;
        outline: none;
        color: var(--ds-text);
        background: #fff;
        box-shadow: var(--shadow-sm);
    }

    .benef-search input:focus {
        border-color: var(--ds-primary);
        box-shadow: 0 0 0 3px rgba(54, 149, 245, .12);
    }

    .benef-cards-list {
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .beneficiary-card {
        position: relative;
        background: var(--ds-light);
        border: 1px solid var(--ds-border);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        padding: 22px 26px;
        transition: .2s ease;
    }

    .beneficiary-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .beneficiary-card.is-hidden {
        display: none !important;
    }

    .benef-card-main {
        display: grid;
        grid-template-columns: 112px 1fr 170px;
        gap: 24px;
        align-items: flex-start;
    }

    .benef-avatar-wrap {
        display: flex;
        justify-content: center;
    }

    .benef-avatar {
        width: 86px;
        height: 86px;
        border-radius: 50%;
        background: linear-gradient(145deg, #f3f8ff, #e6f1ff);
        border: 1.5px solid #c8defd;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        color: #1d6de2;
        overflow: hidden;
    }

    .benef-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .benef-avatar svg {
        width: 52px;
        height: 52px;
    }

    .benef-avatar::after {
        content: "";
        position: absolute;
        right: 8px;
        bottom: 8px;
        width: 15px;
        height: 15px;
        background: var(--ds-success);
        border: 3px solid white;
        border-radius: 50%;
    }

    .person-name {
        margin: 0 0 10px;
        color: var(--ds-dark);
        font-size: 20px;
        font-weight: 600;
        letter-spacing: .2px;
        text-transform: uppercase;
        padding-right: 35px;
    }

    .meta-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 14px;
        color: #374151;
    }

    .meta-item {
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .meta-label {
        color: #176be8;
        font-weight: 600;
    }

    .age-label {
        color: #00a8b5;
        font-weight: 600;
    }

    .meta-separator {
        width: 1px;
        height: 22px;
        background: #cfd7e3;
    }

    .representative {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 14px;
        color: #374151;
        font-size: 15px;
    }

    .representative strong {
        color: #f26b00;
    }

    .representative i {
        color: #f26b00;
    }

    .benef-card-actions {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 12px;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 34px;
        padding: 0 16px;
        border-radius: 999px;
        background: var(--ds-success-bg);
        color: #0b8f51;
        font-weight: 600;
        font-size: 14px;
        white-space: nowrap;
    }

    .btn-ficha {
        height: 40px;
        padding: 0 16px;
        border-radius: 10px;
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-primary-dark);
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-ficha:hover {
        color: var(--ds-primary-dark);
        border-color: var(--ds-primary);
        background: #f4f9ff;
    }

    .benef-card-menu {
        position: absolute;
        top: 18px;
        right: 20px;
        z-index: 5;
    }

    .benef-card-menu .btn {
        color: #7b8794;
        text-decoration: none;
        padding: 4px 6px;
        border-radius: 10px;
    }

    .benef-card-menu .btn:hover {
        background: #f1f5f9;
        color: var(--ds-dark);
    }

    .benef-card-menu .dropdown-menu {
        border-radius: 14px;
        border: 1px solid var(--ds-border);
        overflow: hidden;
        z-index: 9999;
    }

    .card-divider {
        height: 1px;
        background: var(--ds-border);
        margin: 18px 0 12px;
    }

    .research-block {
        display: grid;
        grid-template-columns: 90px 1fr;
        gap: 14px;
        align-items: center;
    }

    .research-title {
        color: var(--ds-dark);
        font-weight: 600;
        font-size: 15px;
    }

    .research-list {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 22px;
    }

    .research-item {
        display: flex;
        align-items: center;
        gap: 9px;
        min-width: 126px;
    }

    .research-icon-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.6px solid currentColor;
        background: #fff;
        color: #94a3b8;
        cursor: pointer;
        transition: .18s ease;
        padding: 7px;
    }

    .research-icon-btn img {
        width: 24px;
        height: 24px;
        object-fit: contain;
        display: block;
    }

    .research-icon-btn.evaluado {
        background: #f8fbff;
        box-shadow: 0 5px 12px rgba(16, 26, 97, .08);
    }

    .research-icon-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(16, 26, 97, .10);
    }

    .research-status {
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.1;
    }

    .research-status .ok {
        color: var(--ds-success);
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .research-status .pending {
        color: #7b8794;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .empty-state {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 18px;
        padding: 44px 24px;
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .empty-state img {
        width: 64px;
        opacity: .25;
        margin-bottom: 14px;
    }

    .empty-state p {
        color: #64748b;
        margin-bottom: 18px;
    }

    .pagination-wrap {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-top: 18px;
        color: #536580;
        font-size: 14px;
    }

    .pagination-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        flex-wrap: wrap;
    }

    .page-btn {
        min-width: 42px;
        height: 42px;
        padding: 0 14px;
        border: 1px solid var(--ds-border);
        background: white;
        border-radius: 10px;
        color: var(--ds-dark);
        font-weight: 600;
        cursor: pointer;
    }

    .page-btn.active {
        background: var(--ds-primary-dark);
        color: white;
        border-color: var(--ds-primary-dark);
        box-shadow: 0 8px 16px rgba(47, 128, 237, .22);
    }

    .page-btn:disabled {
        opacity: .45;
        cursor: not-allowed;
    }

    .page-btn.wide {
        min-width: 108px;
        color: #536580;
    }

    .page-btn.next {
        min-width: 118px;
        color: var(--ds-primary-dark);
    }

    .page-size {
        height: 42px;
        border: 1px solid var(--ds-border);
        border-radius: 10px;
        background: white;
        color: #536580;
        padding: 0 14px;
        font-weight: 600;
    }

    .pesquisa-modal-list {
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .pesquisa-modal-list li {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 18px;
        cursor: pointer;
        border-top: 1px solid #eef2f7;
        transition: .15s ease;
    }

    .pesquisa-modal-list li:hover {
        background: #f5f8fc;
    }

    .pesquisa-modal-list img {
        width: 34px;
        height: 34px;
        object-fit: contain;
    }

    .pesq-name {
        color: var(--ds-dark);
        font-weight: 600;
        font-size: 14px;
    }

    .pesq-desc {
        color: #64748b;
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .benef-card-main {
            grid-template-columns: 86px 1fr;
        }

        .benef-card-actions {
            grid-column: 2;
            flex-direction: row;
            align-items: center;
            justify-content: flex-start;
        }

        .research-block {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 760px) {
        .benef-page {
            width: min(100% - 28px, 100%);
            padding-top: 18px;
        }

        .benef-topbar,
        .pagination-wrap {
            flex-direction: column;
            align-items: flex-start;
        }

        .benef-card-main {
            grid-template-columns: 1fr;
        }

        .benef-avatar-wrap {
            justify-content: flex-start;
        }

        .benef-card-actions {
            grid-column: auto;
            flex-direction: row;
            align-items: center;
            flex-wrap: wrap;
        }

        .research-list {
            gap: 14px;
        }

        .research-item {
            min-width: 100%;
        }

        .benef-divider {
            display: none;
        }
    }

    /* estilo dicha*/
    .ficha-drawer-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, .38);
        z-index: 1040;
        opacity: 0;
        pointer-events: none;
        transition: .2s ease;
    }

    .ficha-drawer-backdrop.is-open {
        opacity: 1;
        pointer-events: auto;
    }

    .ficha-drawer {
        position: fixed;
        top: 0;
        right: 0;
        width: min(520px, 100%);
        height: 100vh;
        background: #fff;
        z-index: 1050;
        box-shadow: -18px 0 40px rgba(15, 23, 42, .18);
        transform: translateX(100%);
        transition: .25s ease;
        display: flex;
        flex-direction: column;
    }

    .ficha-drawer.is-open {
        transform: translateX(0);
    }

    .ficha-head {
        padding: 22px 24px;
        background: linear-gradient(135deg, #101a61, #176be8);
        color: #fff;
        display: flex;
        justify-content: space-between;
        gap: 16px;
    }

    .ficha-head h3 {
        margin: 0;
        font-size: 21px;
        font-weight: 700;
    }

    .ficha-head p {
        margin: 5px 0 0;
        opacity: .85;
        font-size: 14px;
    }

    .ficha-close {
        width: 38px;
        height: 38px;
        border: 0;
        border-radius: 12px;
        background: rgba(255, 255, 255, .14);
        color: #fff;
        font-size: 22px;
        line-height: 1;
    }

    .ficha-body {
        padding: 22px 24px;
        overflow-y: auto;
        flex: 1;
    }

    .ficha-profile {
        display: grid;
        grid-template-columns: 76px 1fr;
        gap: 16px;
        align-items: center;
        margin-bottom: 20px;
    }

    .ficha-avatar {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #eef6ff;
        border: 1px solid #c8defd;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        color: var(--ds-primary-dark);
    }

    .ficha-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .ficha-avatar i {
        font-size: 40px;
    }

    .ficha-name {
        margin: 0;
        color: var(--ds-dark);
        font-size: 20px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .ficha-meta {
        margin-top: 6px;
        color: #64748b;
        font-size: 14px;
    }

    .ficha-summary {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        margin: 18px 0;
    }

    .ficha-kpi {
        border: 1px solid var(--ds-border);
        border-radius: 14px;
        padding: 12px;
        background: #f8fbff;
        text-align: center;
    }

    .ficha-kpi strong {
        display: block;
        color: var(--ds-dark);
        font-size: 22px;
    }

    .ficha-kpi span {
        color: #64748b;
        font-size: 12px;
        font-weight: 600;
    }

    .ficha-progress {
        height: 10px;
        background: #e5eaf2;
        border-radius: 999px;
        overflow: hidden;
        margin-bottom: 22px;
    }

    .ficha-progress-bar {
        height: 100%;
        width: 0%;
        background: linear-gradient(135deg, #13b76a, #3a8cff);
        border-radius: inherit;
        transition: .25s ease;
    }

    .ficha-section-title {
        margin: 22px 0 12px;
        color: var(--ds-dark);
        font-size: 15px;
        font-weight: 700;
    }

    .ficha-pesquisa-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .ficha-pesquisa-item {
        border: 1px solid var(--ds-border);
        border-radius: 14px;
        padding: 12px;
        display: grid;
        grid-template-columns: 42px 1fr auto;
        align-items: center;
        gap: 12px;
        background: #fff;
    }

    .ficha-pesquisa-item img {
        width: 34px;
        height: 34px;
        object-fit: contain;
    }

    .ficha-pesquisa-name {
        color: var(--ds-dark);
        font-weight: 700;
        font-size: 14px;
    }

    .ficha-pesquisa-desc {
        color: #64748b;
        font-size: 12px;
    }

    .ficha-chip {
        border-radius: 999px;
        padding: 6px 10px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
    }

    .ficha-chip.ok {
        background: #e6f8ef;
        color: #0b8f51;
    }

    .ficha-chip.pending {
        background: #fff7e6;
        color: #b76a00;
    }

    .ficha-actions {
        padding: 18px 24px;
        border-top: 1px solid var(--ds-border);
        background: #f8fbff;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .ficha-action-btn {
        min-height: 42px;
        border-radius: 12px;
        border: 1px solid var(--ds-border);
        background: #fff;
        color: var(--ds-primary-dark);
        font-weight: 700;
        text-decoration: none;
        display: inline-flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }

    .ficha-action-btn.primary {
        border-color: var(--ds-primary-dark);
        background: var(--ds-primary-dark);
        color: #fff;
    }

    .ficha-action-btn:hover {
        color: var(--ds-primary-dark);
        background: #f4f9ff;
    }

    .ficha-action-btn.primary:hover {
        color: #fff;
        background: #176be8;
    }
</style>

<main class="benef-page">

    <nav class="breadcrumb-digi-new">
        <span><i class="bi bi-house-door"></i></span>
        <a href="<?= base_url('jornadas') ?>">Listado de Jornadas</a>
        <span>›</span>
        <span>Beneficiarios</span>
        <span>›</span>
        <span class="active">
            <?= esc($jornada['nombre_jornada'] ?? 'Jornada') ?>
            <?php if (!empty($jornada['fecha_inicio'])): ?>
                <?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?>
            <?php endif; ?>
        </span>
    </nav>

    <?php if (session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show auto-dismiss">
            <?= session('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
        <div class="alert alert-warning alert-dismissible fade show auto-dismiss">
            <?= session('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="benef-topbar">
        <div class="benef-title">
            <h1>
                Beneficiarios de la jornada
                <span class="benef-counter"><?= $total ?? count($beneficiarios ?? []) ?></span>
            </h1>
            <!-- <p>
                Jornada:
                <?= esc($jornada['nombre_jornada'] ?? 'Jornada') ?>
                <?php if (!empty($jornada['fecha_inicio'])): ?>
                    · <?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?>
                <?php endif; ?>
            </p> -->
        </div>

        <div class="benef-actions">
            <div class="benef-total-pill">
                <i class="bi bi-people"></i>
                <span><?= $total ?? count($beneficiarios ?? []) ?></span>
            </div>

            <div class="benef-divider"></div>

            <a href="<?= site_url('jornadas/' . $jornada_id . '/beneficiarios/buscar') ?>" class="ds-btn-primary">
                <i class="bi bi-plus-lg"></i>
                Registrar
            </a>
        </div>
    </div>

    <?php if (!empty($beneficiarios) && count($beneficiarios) > 5): ?>
        <div class="benef-search">
            <i class="bi bi-search"></i>
            <input type="text" id="filtrarBenef" placeholder="Filtrar por nombre, apellido o ID...">
        </div>
    <?php endif; ?>

    <?php if (!empty($beneficiarios)): ?>
        <?php
        $iconos_color = [
            '1' => ['img' => 'antropometria2.svg',      'gris' => 'antropometria-color.svg',      'nombre' => 'Antropometría'],
            '2' => ['img' => 'sanguinea2.svg',          'gris' => 'sanguinea-color.svg',          'nombre' => 'Laboratorio'],
            '3' => ['img' => 'visual2.svg',             'gris' => 'visual-color.svg',             'nombre' => 'Visual'],
            '4' => ['img' => 'signosVitales2.svg',      'gris' => 'signos-vitales-color.svg',     'nombre' => 'Signos vitales'],
            '5' => ['img' => 'medicinaGeneral2.svg',    'gris' => 'medicina-general-color.svg',   'nombre' => 'Medicina general'],
            '6' => ['img' => 'vacunacion2.svg',         'gris' => 'vacunacion-color.svg',         'nombre' => 'Vacunación'],
        ];
        ?>

        <section class="benef-cards-list" id="benefCardsList">
            <?php foreach ($beneficiarios as $b): ?>
                <?php
                $fechaNacimiento = $b['fecha_nacimiento'] ?? null;

                if (!empty($fechaNacimiento)) {
                    $nac  = new \DateTime($fechaNacimiento);
                    $diff = (new \DateTime())->diff($nac);
                    $edad = $diff->y . ' año' . ($diff->y != 1 ? 's' : '') . ', ' . $diff->m . ' mes(es) y ' . $diff->d . ' días';
                } else {
                    $edad = '—';
                }

                $evals = $evaluaciones[$b['id_beneficiario']] ?? [];
                $evalsJs = esc(
                    json_encode(
                        array_values(array_map('strval', $evals)),
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                    ),
                    'attr'
                );

                $tieneEvaluaciones = ! empty($evals);

                $nombreCompleto = trim(($b['nombres'] ?? '') . ' ' . ($b['apellidos'] ?? ''));
                $nombreCompletoJs = esc(
                    json_encode(
                        $nombreCompleto,
                        JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE
                    ),
                    'attr'
                );
                $searchText = strtolower(
                    trim(
                        ($b['apellidos'] ?? '') . ' ' .
                            ($b['nombres'] ?? '') . ' ' .
                            ($b['id_digisalud'] ?? '')
                    )
                );
                ?>

                <article
                    class="beneficiary-card"
                    data-benef-card="1"
                    data-search="<?= esc($searchText) ?>">

                    <div class="benef-card-menu dropdown">
                        <button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical fs-5"></i>
                        </button>

                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li>
                                <a class="dropdown-item" href="<?= base_url("beneficiarios/editar/{$b['id_beneficiario']}") ?>">
                                    <i class="bi bi-pencil-square me-2 text-primary"></i>
                                    Editar perfil
                                </a>
                            </li>

                            <li>
                                <a
                                    class="dropdown-item"
                                    href="#"
                                    onclick="abrirModalPesquisa(<?= (int) $b['id_beneficiario'] ?>, <?= $nombreCompletoJs ?>, <?= $evalsJs ?>, 'crear'); return false;">
                                    <i class="bi bi-clipboard2-pulse me-2 text-success"></i>
                                    Evaluar
                                </a>
                            </li>
                            <?php if ($tieneEvaluaciones): ?>
    <li>
        <a
            class="dropdown-item"
            href="#"
            onclick="abrirModalPesquisa(<?= (int) $b['id_beneficiario'] ?>, <?= $nombreCompletoJs ?>, <?= $evalsJs ?>, 'editar'); return false;">
            <i class="bi bi-pencil-square me-2 text-warning"></i>
            Editar evaluación
        </a>
    </li>
<?php endif; ?>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <?php if (! $tieneEvaluaciones): ?>
    <li>
        <hr class="dropdown-divider">
    </li>

    <li>
        <a
            class="dropdown-item text-danger"
            href="#"
            onclick="confirmarRemover(<?= (int) $jornada_id ?>, <?= (int) $b['id_beneficiario'] ?>); return false;">
            <i class="bi bi-x-circle me-2"></i>
            Retirar de la jornada
        </a>
    </li>
<?php endif; ?>
                        </ul>
                    </div>

                    <div class="benef-card-main">
                        <div class="benef-avatar-wrap">
                            <div class="benef-avatar">
                                <?php if (!empty($b['foto_url'])): ?>
                                    <img src="<?= base_url($b['foto_url']) ?>" alt="<?= esc($nombreCompleto) ?>">
                                <?php else: ?>
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="7" r="4"></circle>
                                        <path d="M5.5 21a6.5 6.5 0 0 1 13 0"></path>
                                    </svg>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div>
                            <h2 class="person-name">
                                <?= esc(strtoupper($b['apellidos'] ?? '')) ?>,
                                <?= esc(strtoupper($b['nombres'] ?? '')) ?>
                            </h2>

                            <div class="meta-row">
                                <span>
                                    <span class="meta-label">ID:</span>
                                    <?= esc($b['id_digisalud'] ?? '—') ?>
                                </span>

                                <span class="meta-separator"></span>

                                <span class="meta-item">
                                    <i class="bi bi-calendar3 meta-label"></i>
                                    <span class="meta-label">Fn:</span>
                                    <?= !empty($fechaNacimiento) ? date('d-m-Y', strtotime($fechaNacimiento)) : '—' ?>
                                </span>

                                <span class="meta-separator"></span>

                                <span class="meta-item">
                                    <i class="bi bi-person-standing age-label"></i>
                                    <span class="age-label">Edad:</span>
                                    <?= esc($edad) ?>
                                </span>
                            </div>

                            <div class="representative">
                                <i class="bi bi-person-badge"></i>
                                <strong>Representante:</strong>
                                <?php if (!empty($b['rep_nombres'])): ?>
                                    <?= esc(trim(($b['rep_nombres'] ?? '') . ' ' . ($b['rep_apellidos'] ?? ''))) ?>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="benef-card-actions">
                            <span>
                                <br>
                            </span>

                            <button
                                type="button"
                                class="btn-ficha"
                                onclick="abrirFichaRapida(<?= (int) $b['id_beneficiario'] ?>)">
                                <i class="bi bi-card-checklist"></i>
                                Ver ficha
                            </button>
                        </div>
                    </div>

                    <div class="card-divider"></div>

                    <div class="research-block">
                        <div class="research-title">Pesquisas</div>

                        <div class="research-list">
                            <?php foreach ($pesquisas_jornada as $p): ?>
                                <?php if (isset($iconos_color[$p])): ?>
                                    <?php
                                    $yaEvaluado = in_array($p, $evals);
                                    $icono = $yaEvaluado ? $iconos_color[$p]['img'] : $iconos_color[$p]['gris'];
                                    $clase = $yaEvaluado ? 'research-icon-btn evaluado' : 'research-icon-btn';
                                    $estadoTexto = $yaEvaluado ? 'Evaluado' : 'Pendiente';
                                    $estadoClase = $yaEvaluado ? 'ok' : 'pending';
                                    $estadoIcono = $yaEvaluado ? 'bi-check-circle-fill' : 'bi-clock';
                                    ?>

                                    <div class="research-item">
                                        <button
                                            class="<?= esc($clase) ?>"
                                            type="button"
                                            title="<?= esc($iconos_color[$p]['nombre'] . ' - ' . $estadoTexto) ?>"
                                            onclick="<?php if ($yaEvaluado && (string) $p === '2'): ?>
                                                abrirHistorialSanguinea(<?= (int) $b['id_beneficiario'] ?>)
                                            <?php else: ?>
                                                abrirEvaluar(<?= (int) $b['id_beneficiario'] ?>, '<?= esc($p) ?>', <?= json_encode($nombreCompleto) ?>)
                                            <?php endif; ?>">
                                            <img
                                                src="<?= base_url('img/' . $icono) ?>"
                                                alt="<?= esc($iconos_color[$p]['nombre']) ?>">
                                        </button>

                                        <span class="research-status">
                                            <span class="<?= esc($estadoClase) ?>">
                                                <i class="bi <?= esc($estadoIcono) ?>"></i>
                                                <?= esc($estadoTexto) ?>
                                            </span>
                                            <small><?= esc($iconos_color[$p]['nombre']) ?></small>
                                        </span>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <footer class="pagination-wrap" id="benefPaginationWrap">
            <div id="paginationInfo">Mostrando 1–15 de <?= (int) ($total ?? count($beneficiarios)) ?> beneficiarios</div>

            <div class="pagination-custom" id="paginationButtons"></div>

            <select class="page-size" id="pageSizeBenef">
                <option value="15" selected>15 por página</option>
                <option value="30">30 por página</option>
                <option value="45">45 por página</option>
            </select>
        </footer>

    <?php else: ?>
        <div class="empty-state">
            <img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" alt="Sin beneficiarios">
            <p>No hay beneficiarios en esta jornada</p>
            <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/buscar") ?>" class="ds-btn-primary">
                <i class="bi bi-plus-lg"></i>
                Registrar primer beneficiario
            </a>
        </div>
    <?php endif; ?>

</main>

<!-- MODAL PESQUISA -->
<div class="modal fade" id="modalPesquisa" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header" style="background:#101a61;color:#fff;">
                <h6 class="modal-title">
                    <i class="bi bi-clipboard2-pulse me-2"></i>
                    <span id="modalPesquisaTitulo">Evaluar:</span>
                    <span id="modalNombreBenef"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <p class="px-3 pt-3 text-muted" id="modalPesquisaTexto" style="font-size:.82rem;">
                    Selecciona la pesquisa:
                </p>
                <ul class="pesquisa-modal-list" id="listaPesquisasModal"></ul>
            </div>
        </div>
    </div>
</div>
<!-- DRAWER FICHA RÁPIDA -->
<div class="ficha-drawer-backdrop" id="fichaBackdrop" onclick="cerrarFichaRapida()"></div>

<aside class="ficha-drawer" id="fichaDrawer" aria-hidden="true">
    <div class="ficha-head">
        <div>
            <h3>Ficha rápida</h3>
            <p id="fichaJornadaTexto">Resumen del beneficiario en la jornada</p>
        </div>

        <button type="button" class="ficha-close" onclick="cerrarFichaRapida()">
            ×
        </button>
    </div>

    <div class="ficha-body" id="fichaBody">
        <div class="text-muted">Cargando ficha...</div>
    </div>

    <div class="ficha-actions" id="fichaActions" style="display:none;">
        <a href="#" class="ficha-action-btn" id="fichaEditarBtn">
            <i class="bi bi-pencil-square"></i>
            Editar
        </a>

        <a href="#" class="ficha-action-btn" id="fichaHistorialBtn">
            <i class="bi bi-clock-history"></i>
            Historial
        </a>

        <button type="button" class="ficha-action-btn primary" id="fichaEvaluarBtn">
            <i class="bi bi-clipboard2-pulse"></i>
            Evaluar pendiente
        </button>

        <button type="button" class="ficha-action-btn" onclick="cerrarFichaRapida()">
            Cerrar
        </button>
    </div>
</aside>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    const pesquisaInfo = {
        '1': {
            img: '<?= base_url("img/antropometria2.svg") ?>',
            nombre: 'Antropometría',
            desc: 'Peso, talla, IMC'
        },
        '2': {
            img: '<?= base_url("img/sanguinea2.svg") ?>',
            nombre: 'Laboratorio',
            desc: 'Hemoglobina, glucosa'
        },
        '3': {
            img: '<?= base_url("img/visual2.svg") ?>',
            nombre: 'Visual',
            desc: 'Agudeza visual'
        },
        '4': {
            img: '<?= base_url("img/signosVitales2.svg") ?>',
            nombre: 'Signos vitales',
            desc: 'Tensión, temperatura, FC'
        },
        '5': {
            img: '<?= base_url("img/medicinaGeneral2.svg") ?>',
            nombre: 'Medicina general',
            desc: 'Evaluación clínica'
        },
        '6': {
            img: '<?= base_url("img/vacunacion2.svg") ?>',
            nombre: 'Vacunación',
            desc: 'Control de vacunas'
        }
    };

    const pesquisasJornada = <?= json_encode(array_values($pesquisas_jornada ?? [])) ?>;
    const jornadaId = <?= (int) $jornada_id ?>;
    let fichaBeneficiarioActual = null;
    let fichaPendienteActual = null;

    function abrirFichaRapida(beneficiarioId) {
        const drawer = document.getElementById('fichaDrawer');
        const backdrop = document.getElementById('fichaBackdrop');
        const body = document.getElementById('fichaBody');
        const actions = document.getElementById('fichaActions');

        fichaBeneficiarioActual = beneficiarioId;
        fichaPendienteActual = null;

        body.innerHTML = '<div class="text-muted">Cargando ficha...</div>';
        actions.style.display = 'none';

        drawer.classList.add('is-open');
        backdrop.classList.add('is-open');
        drawer.setAttribute('aria-hidden', 'false');

        fetch(`<?= base_url('jornadas') ?>/${jornadaId}/beneficiarios/${beneficiarioId}/ficha-rapida`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.ok) {
                    throw new Error(data.message || 'No se pudo cargar la ficha.');
                }

                renderFichaRapida(data);
            })
            .catch(error => {
                body.innerHTML = `
                <div class="alert alert-warning mb-0">
                    ${escapeHtml(error.message)}
                </div>
            `;
            });
    }

    function cerrarFichaRapida() {
        const drawer = document.getElementById('fichaDrawer');
        const backdrop = document.getElementById('fichaBackdrop');

        drawer.classList.remove('is-open');
        backdrop.classList.remove('is-open');
        drawer.setAttribute('aria-hidden', 'true');
    }

    function renderFichaRapida(data) {
        const b = data.beneficiario;
        const resumen = data.resumen;
        const pesquisas = data.pesquisas || [];

        const body = document.getElementById('fichaBody');
        const actions = document.getElementById('fichaActions');

        const primeraPendiente = pesquisas.find(p => !p.evaluado);
        fichaPendienteActual = primeraPendiente || null;

        document.getElementById('fichaJornadaTexto').textContent =
            `${data.jornada.nombre} · ${data.jornada.fecha}`;

        const avatar = b.foto_url ?
            `<img src="${b.foto_url}" alt="${escapeHtml(b.nombre_completo)}">` :
            `<i class="bi bi-person"></i>`;

        const pesquisasHtml = pesquisas.length ?
            pesquisas.map(p => `
            <div class="ficha-pesquisa-item">
                <img src="${p.icono}" alt="${escapeHtml(p.nombre)}">

                <div>
                    <div class="ficha-pesquisa-name">${escapeHtml(p.nombre)}</div>
                    <div class="ficha-pesquisa-desc">${escapeHtml(p.desc || '')}</div>
                </div>

                <span class="ficha-chip ${p.evaluado ? 'ok' : 'pending'}">
                    <i class="bi ${p.evaluado ? 'bi-check-circle-fill' : 'bi-clock'}"></i>
                    ${p.evaluado ? 'Evaluado' : 'Pendiente'}
                </span>
            </div>
        `).join('') :
            `<div class="text-muted">Esta jornada no tiene pesquisas configuradas.</div>`;

        body.innerHTML = `
        <div class="ficha-profile">
            <div class="ficha-avatar">
                ${avatar}
            </div>

            <div>
                <h4 class="ficha-name">
                    ${escapeHtml(b.apellidos)}, ${escapeHtml(b.nombres)}
                </h4>

                <div class="ficha-meta">
                    <strong>ID:</strong> ${escapeHtml(b.id_digisalud || '—')}<br>
                    <strong>Fn:</strong> ${escapeHtml(b.fecha_nacimiento || '—')}<br>
                    <strong>Edad:</strong> ${escapeHtml(b.edad || '—')}<br>
                    <strong>Representante:</strong> ${escapeHtml(b.representante || '—')}
                </div>
            </div>
        </div>

        <div class="ficha-summary">
            <div class="ficha-kpi">
                <strong>${resumen.total_pesquisas}</strong>
                <span>Pesquisas</span>
            </div>

            <div class="ficha-kpi">
                <strong>${resumen.total_evaluadas}</strong>
                <span>Evaluadas</span>
            </div>

            <div class="ficha-kpi">
                <strong>${resumen.total_pendientes}</strong>
                <span>Pendientes</span>
            </div>
        </div>

        <div class="ficha-progress" title="${resumen.porcentaje_avance}% completado">
            <div class="ficha-progress-bar" style="width:${resumen.porcentaje_avance}%"></div>
        </div>

        <div class="ficha-section-title">Estado de pesquisas</div>

        <div class="ficha-pesquisa-list">
            ${pesquisasHtml}
        </div>
    `;

        document.getElementById('fichaEditarBtn').href = data.acciones.editar_url;
        document.getElementById('fichaHistorialBtn').href = data.acciones.historial_url;

        const evaluarBtn = document.getElementById('fichaEvaluarBtn');

        if (primeraPendiente) {
            evaluarBtn.disabled = false;
            evaluarBtn.style.opacity = '1';
            evaluarBtn.innerHTML = `
            <i class="bi bi-clipboard2-pulse"></i>
            Evaluar ${escapeHtml(primeraPendiente.nombre)}
        `;
        } else {
            evaluarBtn.disabled = true;
            evaluarBtn.style.opacity = '.55';
            evaluarBtn.innerHTML = `
            <i class="bi bi-check-circle"></i>
            Todo evaluado
        `;
        }

        actions.style.display = 'grid';
    }

    document.addEventListener('click', function(event) {
        if (event.target && event.target.id === 'fichaEvaluarBtn') {
            if (!fichaBeneficiarioActual || !fichaPendienteActual) {
                return;
            }

            navegarFormulario(fichaBeneficiarioActual, fichaPendienteActual.id);
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            cerrarFichaRapida();
        }
    });

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }
    /**
     * Evaluar beneficiario.
     * pid = '0' → Desde dropdown "Evaluar" → Abre modal selector.
     * pid > 0   → Desde icono de pesquisa → Navega directo al formulario.
     */
    /**
 * modo = 'crear'  -> muestra solo pesquisas pendientes
 * modo = 'editar' -> muestra solo pesquisas evaluadas
 */
function abrirModalPesquisa(bid, nombre, pesquisasEvaluadas, modo) {
    const evaluadas = (pesquisasEvaluadas || []).map(String);

    document.getElementById('modalNombreBenef').textContent = nombre;

    const titulo = document.getElementById('modalPesquisaTitulo');
    const texto = document.getElementById('modalPesquisaTexto');
    const lista = document.getElementById('listaPesquisasModal');

    const esEdicion = modo === 'editar';

    titulo.textContent = esEdicion ? 'Editar evaluación:' : 'Evaluar:';
    texto.textContent = esEdicion
        ? 'Selecciona la pesquisa registrada que deseas editar:'
        : 'Selecciona la pesquisa pendiente a evaluar:';

    lista.innerHTML = '';

    pesquisasJornada.forEach(function(p) {
        const pesquisaId = String(p);
        const info = pesquisaInfo[pesquisaId];
        if (!info) return;

        const yaEvaluada = evaluadas.includes(pesquisaId);

        if (!esEdicion && yaEvaluada) return;
        if (esEdicion && !yaEvaluada) return;

        const li = document.createElement('li');
        li.innerHTML =
            '<img src="' + info.img + '" alt="' + info.nombre + '">' +
            '<div>' +
            '<div class="pesq-name">' + info.nombre + '</div>' +
            '<div class="pesq-desc">' + info.desc + '</div>' +
            '</div>';

        li.addEventListener('click', function() {
            const modalEl = document.getElementById('modalPesquisa');
            const modalInst = bootstrap.Modal.getInstance(modalEl);
            if (modalInst) modalInst.hide();

            navegarFormulario(bid, pesquisaId);
        });

        lista.appendChild(li);
    });

    if (!lista.children.length) {
        const li = document.createElement('li');
        li.style.cursor = 'default';
        li.innerHTML =
            '<div>' +
            '<div class="pesq-name">' + (esEdicion ? 'Sin evaluaciones registradas' : 'Sin pesquisas pendientes') + '</div>' +
            '<div class="pesq-desc">' + (esEdicion ? 'Este beneficiario no tiene evaluaciones para editar.' : 'Todas las pesquisas de esta jornada ya fueron evaluadas.') + '</div>' +
            '</div>';
        lista.appendChild(li);
    }

    const modalPesquisa = new bootstrap.Modal(document.getElementById('modalPesquisa'));
    modalPesquisa.show();
}

function abrirEvaluar(bid, pid, nombre) {
    if (String(pid) === '0') {
        abrirModalPesquisa(bid, nombre, [], 'crear');
        return;
    }

    navegarFormulario(bid, String(pid));
}

    function navegarFormulario(beneficiarioId, tipoPesquisaId) {
        window.location.href = '<?= base_url("evaluaciones/formulario") ?>/' + beneficiarioId + '/' + tipoPesquisaId + '?jornada_id=' + jornadaId;
    }

    function abrirHistorialSanguinea(beneficiarioId) {
        window.location.href = '<?= base_url("evaluaciones/historial-sanguinea") ?>/' + beneficiarioId + '?jornada_id=' + jornadaId;
    }


    document.addEventListener('DOMContentLoaded', function() {
        var cards = document.querySelectorAll('.beneficiary-card');
        var totalCards = cards.length;

        var pageSizeSelect = document.getElementById('pageSizeBenef');
        var paginationInfo = document.getElementById('paginationInfo');
        var paginationBtns = document.getElementById('paginationButtons');

        var pageSize = parseInt(pageSizeSelect ? pageSizeSelect.value : 15);
        var currentPage = 1;

        function render() {
            var start = (currentPage - 1) * pageSize;
            var end = start + pageSize;
            var totalPages = Math.ceil(totalCards / pageSize);

            cards.forEach(function(card, i) {
                card.style.display = (i >= start && i < end) ? '' : 'none';
            });

            if (paginationInfo) {
                paginationInfo.textContent = 'Mostrando ' + (start + 1) + '–' + Math.min(end, totalCards) + ' de ' + totalCards + ' beneficiarios';
            }

            if (paginationBtns) {
                paginationBtns.innerHTML = '';

                var prevBtn = document.createElement('button');
                prevBtn.className = 'page-btn wide';
                prevBtn.textContent = '← Anterior';
                prevBtn.disabled = (currentPage === 1);
                prevBtn.addEventListener('click', function() {
                    currentPage--;
                    render();
                });
                paginationBtns.appendChild(prevBtn);

                for (var i = 1; i <= totalPages; i++) {
                    var btn = document.createElement('button');
                    btn.className = 'page-btn' + (i === currentPage ? ' active' : '');
                    btn.textContent = i;
                    btn.setAttribute('data-page', i);
                    btn.addEventListener('click', function() {
                        currentPage = parseInt(this.getAttribute('data-page'));
                        render();
                    });
                    paginationBtns.appendChild(btn);
                }

                var nextBtn = document.createElement('button');
                nextBtn.className = 'page-btn next';
                nextBtn.textContent = 'Siguiente →';
                nextBtn.disabled = (currentPage === totalPages || totalPages === 0);
                nextBtn.addEventListener('click', function() {
                    currentPage++;
                    render();
                });
                paginationBtns.appendChild(nextBtn);
            }
        }

        if (pageSizeSelect) {
            pageSizeSelect.addEventListener('change', function() {
                pageSize = parseInt(pageSizeSelect.value);
                currentPage = 1;
                render();
            });
        }

        if (totalCards > 0) render();

        var filtro = document.getElementById('filtrarBenef');
        if (filtro) {
            filtro.addEventListener('keyup', function() {
                var texto = this.value.toLowerCase().trim();
                cards.forEach(function(card) {
                    var search = card.getAttribute('data-search') || '';
                    card.style.display = search.indexOf(texto) !== -1 ? '' : 'none';
                });
            });
        }
    });

    function confirmarRemover(jornadaId, beneficiarioId) {
        Swal.fire({
            title: '¿Retirar beneficiario?',
            text: 'Se desasociará de esta jornada.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e53e3e',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Sí, retirar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = `<?= base_url('jornadas') ?>/${jornadaId}/beneficiarios/desasociar/${beneficiarioId}`;
            }
        });
    }
</script>
<?= $this->endSection() ?>
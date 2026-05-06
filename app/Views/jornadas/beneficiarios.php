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
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 850;
    }

    .age-label {
        color: #00a8b5;
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 850;
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
        font-weight: 700;
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
        font-weight: 850;
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
            <p>
                Jornada:
                <?= esc($jornada['nombre_jornada'] ?? 'Jornada') ?>
                <?php if (!empty($jornada['fecha_inicio'])): ?>
                    · <?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?>
                <?php endif; ?>
            </p>
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

                    $nombreCompleto = trim(($b['nombres'] ?? '') . ' ' . ($b['apellidos'] ?? ''));
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
                    data-search="<?= esc($searchText) ?>"
                >

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
                                    onclick="abrirEvaluar(<?= (int) $b['id_beneficiario'] ?>, '0', <?= json_encode($nombreCompleto) ?>); return false;"
                                >
                                    <i class="bi bi-clipboard2-pulse me-2 text-success"></i>
                                    Evaluar
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a
                                    class="dropdown-item text-danger"
                                    href="#"
                                    onclick="confirmarRemover(<?= (int) $jornada_id ?>, <?= (int) $b['id_beneficiario'] ?>); return false;"
                                >
                                    <i class="bi bi-x-circle me-2"></i>
                                    Retirar de la jornada
                                </a>
                            </li>
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
                            <span >
                                <br>
                            </span>

                            <a href="<?= base_url("beneficiarios/editar/{$b['id_beneficiario']}") ?>" class="btn-ficha">
                                <i class="bi bi-card-checklist"></i>
                                Ver ficha
                            </a>
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
                                            onclick="abrirEvaluar(<?= (int) $b['id_beneficiario'] ?>, '<?= esc($p) ?>', <?= json_encode($nombreCompleto) ?>)"
                                        >
                                            <img
                                                src="<?= base_url('img/' . $icono) ?>"
                                                alt="<?= esc($iconos_color[$p]['nombre']) ?>"
                                            >
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

<!-- MODAL EVALUAR -->
<div class="modal fade" id="modalEvaluar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:18px; overflow:hidden;">
            <div class="modal-header" style="background:#101a61;color:#fff;">
                <h6 class="modal-title">
                    <i class="bi bi-clipboard2-pulse me-2"></i>
                    Evaluar:
                    <span id="modalNombreBenef"></span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-0">
                <p class="px-3 pt-3 text-muted" style="font-size:.82rem;">
                    Selecciona la pesquisa a evaluar:
                </p>
                <ul class="pesquisa-modal-list" id="listaPesquisasModal"></ul>
            </div>
        </div>
    </div>
</div>

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

function abrirEvaluar(bid, pid, nombre) {
    if (String(pid) === '0') {
        document.getElementById('modalNombreBenef').textContent = nombre;

        const lista = document.getElementById('listaPesquisasModal');
        lista.innerHTML = '';

        pesquisasJornada.forEach(p => {
            const pesquisaId = String(p);
            const info = pesquisaInfo[pesquisaId];
            if (!info) return;

    
<?= $this->endSection() ?>
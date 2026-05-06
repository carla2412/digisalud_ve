<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(($beneficiario['nombres'] ?? '') . ' ' . ($beneficiario['apellidos'] ?? ''));

$fechaNacimiento = $beneficiario['fecha_nacimiento'] ?? null;
$edad = '—';

if (!empty($fechaNacimiento)) {
    $nac  = new DateTime($fechaNacimiento);
    $diff = (new DateTime())->diff($nac);
    $edad = $diff->y . ' años, ' . $diff->m . ' mes(es)';
}

function labValor(array $resultados, array $codigos, string $fallback = '—'): string
{
    foreach ($codigos as $codigo) {
        if (isset($resultados[$codigo]) && $resultados[$codigo]['valor'] !== null && $resultados[$codigo]['valor'] !== '') {
            $valor = $resultados[$codigo]['valor'];
            $unidad = $resultados[$codigo]['unidad'] ?? '';
            return trim($valor . ' ' . $unidad);
        }
    }

    return $fallback;
}

function estadoHemoglobina($valor): array
{
    if ($valor === null || $valor === '' || !is_numeric($valor)) {
        return ['Sin dato', 'neutral'];
    }

    $v = (float) $valor;

    if ($v < 12) {
        return ['Hemoglobina baja', 'danger'];
    }

    if ($v > 17.5) {
        return ['Hemoglobina alta', 'warning'];
    }

    return ['Hemoglobina normal', 'success'];
}
?>

<style>
    :root {
        --ds-primary: #176be8;
        --ds-dark: #101a61;
        --ds-bg: #f5f8fc;
        --ds-border: #e0e6ed;
        --ds-text: #1f2937;
        --ds-muted: #64748b;
        --ds-danger: #ef2f1b;
        --ds-success: #06b84f;
        --ds-warning: #f59e0b;
    }

    body {
        background: var(--ds-bg);
    }

    .blood-page {
        width: min(1180px, calc(100% - 48px));
        margin: 0 auto;
        padding: 28px 0 42px;
    }

    .blood-hero {
        background: linear-gradient(135deg, #1558c8, #176be8);
        border-radius: 24px;
        padding: 26px 30px;
        color: #fff;
        box-shadow: 0 18px 34px rgba(23, 107, 232, .22);
        display: flex;
        justify-content: space-between;
        gap: 22px;
        align-items: center;
        margin-bottom: 24px;
    }

    .blood-person {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .blood-avatar {
        width: 76px;
        height: 76px;
        border-radius: 50%;
        background: #fff;
        color: #176be8;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 38px;
        font-weight: 700;
        border: 4px solid rgba(255,255,255,.35);
    }

    .blood-person h1 {
        margin: 0 0 4px;
        font-size: 28px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .blood-person p {
        margin: 0;
        opacity: .92;
        font-size: 15px;
    }

    .blood-back {
        border: 1px solid rgba(255,255,255,.5);
        color: #fff;
        text-decoration: none;
        border-radius: 12px;
        padding: 10px 16px;
        font-weight: 600;
    }

    .blood-title {
        background: #fff;
        border: 1px solid var(--ds-border);
        border-radius: 18px;
        padding: 20px 24px;
        margin-bottom: 18px;
        box-shadow: 0 8px 20px rgba(16, 26, 97, .06);
    }

    .blood-title h2 {
        color: var(--ds-dark);
        margin: 0;
        letter-spacing: .08em;
        font-size: 20px;
        text-transform: uppercase;
    }

    .blood-title-bar {
        width: 180px;
        height: 6px;
        background: #7654b7;
        border-radius: 999px;
        margin-top: 16px;
    }

    .blood-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(330px, 1fr));
        gap: 18px;
    }

    .blood-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid var(--ds-border);
        box-shadow: 0 12px 28px rgba(16, 26, 97, .10);
        overflow: hidden;
        position: relative;
    }

    .blood-card-head {
        padding: 22px 22px 14px;
        border-bottom: 1px solid #edf1f7;
        position: relative;
    }

    .blood-date {
        color: #536580;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .blood-card h3 {
        color: var(--ds-danger);
        font-size: 23px;
        letter-spacing: .06em;
        margin: 0 0 6px;
        text-transform: uppercase;
        font-weight: 800;
    }

    .blood-center {
        color: #536580;
        font-weight: 700;
        font-size: 14px;
        text-transform: uppercase;
    }

    .blood-drop {
        width: 58px;
        height: 58px;
        border-radius: 50%;
        background: var(--ds-danger);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 20px;
        top: 24px;
        font-size: 28px;
    }

    .blood-row {
        display: flex;
        justify-content: space-between;
        gap: 18px;
        padding: 13px 22px;
        border-bottom: 1px solid #edf1f7;
        color: #536580;
        font-weight: 700;
    }

    .blood-row strong {
        color: #334155;
        text-align: right;
    }

    .blood-status {
        padding: 12px 22px;
        color: #fff;
        text-transform: uppercase;
        font-weight: 800;
        letter-spacing: -.02em;
    }

    .blood-status.success {
        background: var(--ds-success);
    }

    .blood-status.warning {
        background: var(--ds-warning);
    }

    .blood-status.danger {
        background: var(--ds-danger);
    }

    .blood-status.neutral {
        background: #64748b;
    }

    .blood-extra {
        padding: 14px 22px 20px;
    }

    .blood-extra-title {
        font-size: 13px;
        color: var(--ds-muted);
        font-weight: 700;
        margin-bottom: 10px;
        text-transform: uppercase;
    }

    .blood-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .blood-tag {
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        color: #334155;
        border-radius: 999px;
        padding: 7px 10px;
        font-size: 12px;
        font-weight: 700;
    }

    .empty-history {
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        padding: 42px;
        text-align: center;
        color: var(--ds-muted);
    }

    @media (max-width: 720px) {
        .blood-page {
            width: min(100% - 28px, 100%);
        }

        .blood-hero {
            flex-direction: column;
            align-items: flex-start;
        }

        .blood-person {
            align-items: flex-start;
        }

        .blood-person h1 {
            font-size: 22px;
        }
    }
</style>

<main class="blood-page">

    <section class="blood-hero">
        <div class="blood-person">
            <div class="blood-avatar">
                <?= esc(mb_substr($beneficiario['nombres'] ?? 'B', 0, 1)) ?>
            </div>

            <div>
                <h1><?= esc($nombreCompleto) ?></h1>
                <p>
                    <?= esc($beneficiario['sexo'] ?? '—') ?> /
                    <?= esc($edad) ?>
                    <?php if (!empty($beneficiario['id_digisalud'])): ?>
                        · ID: <?= esc($beneficiario['id_digisalud']) ?>
                    <?php endif; ?>
                </p>
            </div>
        </div>

        <a class="blood-back" href="<?= base_url('jornadas/' . $jornadaId . '/beneficiarios') ?>">
            ← Volver
        </a>
    </section>

    <section class="blood-title">
        <h2>Historial sanguíneo</h2>
        <div class="blood-title-bar"></div>
    </section>

    <?php if (empty($historial)): ?>
        <div class="empty-history">
            No hay evaluaciones sanguíneas registradas para este beneficiario.
        </div>
    <?php else: ?>

        <section class="blood-grid">
            <?php foreach ($historial as $eval): ?>
                <?php
                $r = $eval['resultados'];

                $hemoglobinaRaw = null;
                foreach (['hemoglobina', 'hb', 'hgb'] as $codigoHb) {
                    if (isset($r[$codigoHb])) {
                        $hemoglobinaRaw = $r[$codigoHb]['valor'];
                        break;
                    }
                }

                [$estadoHb, $estadoClase] = estadoHemoglobina($hemoglobinaRaw);

                $fecha = !empty($eval['fecha_evaluacion'])
                    ? date('d/m/Y', strtotime($eval['fecha_evaluacion']))
                    : '—';

                $centro = $eval['nombre_centro']
                    ?? $eval['nombre_jornada']
                    ?? 'Centro / jornada no indicada';

                $hemoglobina = labValor($r, ['hemoglobina', 'hb', 'hgb']);
                $glucosa = labValor($r, ['glucosa', 'glicemia']);
                ?>

                <article class="blood-card">
                    <div class="blood-card-head">
                        <div class="blood-date"><?= esc($fecha) ?></div>
                        <h3>Sanguíneo</h3>
                        <div class="blood-center"><?= esc($centro) ?></div>
                        <div class="blood-drop">
                            <i class="bi bi-droplet"></i>
                        </div>
                    </div>

                    <div class="blood-row">
                        <span>Edad</span>
                        <strong><?= esc($edad) ?></strong>
                    </div>

                    <div class="blood-row">
                        <span>Hemoglobina</span>
                        <strong><?= esc($hemoglobina) ?></strong>
                    </div>

                    <div class="blood-status <?= esc($estadoClase) ?>">
                        <?= esc($estadoHb) ?>
                    </div>

                    <div class="blood-row">
                        <span>Glucosa</span>
                        <strong><?= esc($glucosa) ?></strong>
                    </div>

                    <div class="blood-extra">
                        <div class="blood-extra-title">Otros resultados</div>

                        <div class="blood-tags">
                            <?php foreach ($r as $codigo => $item): ?>
                                <?php if (in_array($codigo, ['hemoglobina', 'hb', 'hgb', 'glucosa', 'glicemia'])) continue; ?>
                                <?php if ($item['valor'] === null || $item['valor'] === '') continue; ?>

                                <span class="blood-tag">
                                    <?= esc($item['nombre']) ?>:
                                    <?= esc(trim($item['valor'] . ' ' . ($item['unidad'] ?? ''))) ?>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

    <?php endif; ?>

</main>

<?= $this->endSection() ?>
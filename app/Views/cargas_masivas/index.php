<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<?php
$jornadas = $jornadas ?? [];
$plantillas = $plantillas ?? [];
$tiposCarga = $tiposCarga ?? [];
?>
<style>
    .carga_mas-page {
        width: min(1280px, calc(100% - 48px));
        margin: 0 auto;
        padding: 28px 0 40px;
    }

    .carga_mas-hero {
        background: linear-gradient(135deg, #101a61, #176be8);
        color: #fff;
        border-radius: 24px;
        padding: 28px;
        box-shadow: 0 18px 38px rgba(16, 26, 97, .16);
        margin-bottom: 22px;
        display: flex;
        justify-content: space-between;
        gap: 20px;
        align-items: flex-start;
    }

    .carga_mas-hero h1 {
        margin: 0;
        font-size: 30px;
        font-weight: 700;
    }

    .carga_mas-hero p {
        margin: 8px 0 0;
        opacity: .88;
        max-width: 720px;
    }

    .carga_mas-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .13);
        border: 1px solid rgba(255, 255, 255, .25);
        font-weight: 600;
        white-space: nowrap;
    }

    .carga_mas-grid {
        display: grid;
        grid-template-columns: 1fr 1.2fr;
        gap: 18px;
        align-items: start;
    }

    .carga_mas-card {
        background: #fff;
        border: 1px solid var(--ds-border, #e0e6ed);
        border-radius: 20px;
        box-shadow: var(--shadow-sm, 0 8px 18px rgba(16, 26, 97, .06));
        overflow: hidden;
    }

    .carga_mas-card-head {
        padding: 20px 22px;
        border-bottom: 1px solid var(--ds-border, #e0e6ed);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 14px;
    }

    .carga_mas-card-head h2 {
        margin: 0;
        color: var(--ds-dark, #101a61);
        font-size: 20px;
        font-weight: 700;
    }

    .carga_mas-card-head p {
        margin: 4px 0 0;
        color: #64748b;
        font-size: 14px;
    }

    .carga_mas-card-body {
        padding: 22px;
    }

    .carga_mas-template-list,
    .carga_mas-load-list {
        display: grid;
        gap: 12px;
    }

    .carga_mas-template-item,
    .carga_mas-load-item {
        border: 1px solid var(--ds-border, #e0e6ed);
        border-radius: 16px;
        padding: 16px;
        background: #f8fbff;
        display: grid;
        grid-template-columns: 42px 1fr auto;
        gap: 14px;
        align-items: center;
    }

    .carga_mas-icon {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #eaf3ff;
        color: #176be8;
        font-size: 21px;
    }

    .carga_mas-icon img {
        width: 28px;
        height: 28px;
        object-fit: contain;
    }

    .carga_mas-item-title {
        color: var(--ds-dark, #101a61);
        font-weight: 700;
        font-size: 15px;
        margin-bottom: 2px;
    }

    .carga_mas-item-desc {
        color: #64748b;
        font-size: 13px;
    }

    .carga_mas-btn {
        height: 40px;
        border: 0;
        border-radius: 12px;
        padding: 0 16px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        white-space: nowrap;
        cursor: pointer;
    }

    .carga_mas-btn-primary {
        background: #176be8;
        color: #fff;
    }

    .carga_mas-btn-primary:hover {
        color: #fff;
        background: #0f5fd1;
    }

    .carga_mas-btn-soft {
        background: #eef6ff;
        color: #176be8;
        border: 1px solid #cfe3ff;
    }

    .carga_mas-btn-soft:hover {
        color: #176be8;
        background: #e4f0ff;
    }

    .carga_mas-btn:disabled,
    .carga_mas-load-item.carga_mas-disabled .carga_mas-btn {
        opacity: .5;
        cursor: not-allowed;
        pointer-events: none;
    }

    .carga_mas-form-row {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        margin-bottom: 18px;
    }

    .carga_mas-label {
        color: var(--ds-dark, #101a61);
        font-weight: 700;
        font-size: 14px;
    }

    .carga_mas-select,
    .carga_mas-file {
        min-height: 46px;
        border: 1px solid var(--ds-border, #e0e6ed);
        border-radius: 14px;
        padding: 0 14px;
        background: #fff;
        color: #334155;
    }

    .carga_mas-help {
        color: #64748b;
        font-size: 13px;
        margin-top: 4px;
    }

    .carga_mas-jornada-box {
        background: #f8fbff;
        border: 1px dashed #cfe3ff;
        border-radius: 16px;
        padding: 14px 16px;
        margin-bottom: 18px;
        color: #475569;
    }

    .carga_mas-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 10px;
    }

    .carga_mas-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 999px;
        padding: 6px 10px;
        background: #eef6ff;
        color: #176be8;
        font-size: 12px;
        font-weight: 700;
    }

    .carga_mas-load-item.carga_mas-disabled {
        filter: grayscale(.15);
        background: #f8fafc;
        opacity: .7;
    }

    .carga_mas-results {
        display: none;
        margin-top: 18px;
    }

    .carga_mas-kpis {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }

    .carga_mas-kpi {
        border: 1px solid var(--ds-border, #e0e6ed);
        border-radius: 14px;
        padding: 12px;
        background: #fff;
        text-align: center;
    }

    .carga_mas-kpi strong {
        display: block;
        color: var(--ds-dark, #101a61);
        font-size: 22px;
    }

    .carga_mas-kpi span {
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
    }

    .carga_mas-alert-list {
        display: grid;
        gap: 10px;
    }

    .carga_mas-alert {
        border-radius: 14px;
        padding: 12px 14px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
    }

    .carga_mas-alert-warning {
        background: #fff7e6;
        border-color: #ffd98a;
    }

    .carga_mas-alert-danger {
        background: #fff1f2;
        border-color: #fecdd3;
    }

    .carga_mas-alert-info {
        background: #eef6ff;
        border-color: #bfdbfe;
    }

    @media (max-width: 1000px) {
        .carga_mas-grid {
            grid-template-columns: 1fr;
        }

        .carga_mas-kpis {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 700px) {
        .carga_mas-page {
            width: min(100% - 28px, 100%);
        }

        .carga_mas-hero,
        .carga_mas-template-item,
        .carga_mas-load-item {
            grid-template-columns: 1fr;
        }

        .carga_mas-hero {
            display: block;
        }
    }
</style>

<main class="carga_mas-page">
    <section class="carga_mas-hero">
        <div>
            <h1>Carga masiva</h1>
            <p>Gestiona plantillas y carga evaluaciones por jornada. Las opciones se habilitan de acuerdo con las pesquisas asociadas a la jornada seleccionada.</p>
        </div>
        <span class="carga_mas-pill">
            <i class="bi bi-shield-check"></i>
            Roles 1 al 4
        </span>
    </section>

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

    <section class="carga_mas-grid">
        <article class="carga_mas-card">
            <div class="carga_mas-card-head">
                <div>
                    <h2>Plantillas</h2>
                    <p>Descarga el formato antes de cargar datos.</p>
                </div>
            </div>

            <div class="carga_mas-card-body">
                <div class="carga_mas-template-list">
                    <?php foreach ($plantillas as $plantilla): ?>
                        <div class="carga_mas-template-item">
                            <span class="carga_mas-icon">
                                <i class="bi <?= esc($plantilla['icono']) ?>"></i>
                            </span>

                            <div>
                                <div class="carga_mas-item-title"><?= esc($plantilla['nombre']) ?></div>
                                <div class="carga_mas-item-desc"><?= esc($plantilla['descripcion']) ?></div>
                            </div>

                            <a class="carga_mas-btn carga_mas-btn-soft" href="<?= site_url('cargas-masivas/plantillas/' . $plantilla['codigo']) ?>">
                                <i class="bi bi-download"></i>
                                Descargar
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </article>

        <article class="carga_mas-card">
            <div class="carga_mas-card-head">
                <div>
                    <h2>Cargar evaluaciones</h2>
                    <p>Selecciona una jornada para habilitar sus cargas disponibles.</p>
                </div>
            </div>

            <div class="carga_mas-card-body">
                <div class="carga_mas-form-row">
                    <label class="carga_mas-label" for="cargaMasJornada">Jornada</label>
                    <select class="carga_mas-select" id="cargaMasJornada">
                        <option value="">Seleccione una jornada...</option>
                        <?php foreach ($jornadas as $j): ?>
                            <?php
                            $pesquisas = array_values(array_filter(array_map('intval', explode(',', (string) ($j['pesquisas'] ?? '')))));
                            $texto = trim(($j['nombre_jornada'] ?? 'Jornada') . ' - ' . ($j['nombre_institucion'] ?? '') . ' - ' . ($j['nombre_org'] ?? ''));
                            ?>
                            <option
                                value="<?= (int) $j['id_jornada'] ?>"
                                data-pesquisas="<?= esc(json_encode($pesquisas), 'attr') ?>"
                                data-nombre="<?= esc($j['nombre_jornada'] ?? 'Jornada', 'attr') ?>"
                                data-institucion="<?= esc($j['nombre_institucion'] ?? '', 'attr') ?>"
                                data-fecha="<?= esc(!empty($j['fecha_inicio']) ? date('d-m-Y', strtotime($j['fecha_inicio'])) : 'Sin fecha', 'attr') ?>"
                                data-status="<?= (int) ($j['status_jor'] ?? 0) ?>">
                                <?= esc($texto) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="carga_mas-help">Solo verás jornadas permitidas para tu rol y organización.</div>
                </div>

                <div class="carga_mas-jornada-box" id="cargaMasJornadaBox">
                    Selecciona una jornada para ver las pesquisas disponibles.
                </div>

                <div class="carga_mas-load-list">
                    <?php foreach ($tiposCarga as $tipo): ?>
                        <form
                            class="carga_mas-load-item carga_mas-disabled"
                            data-carga-item="1"
                            data-tipo="<?= esc($tipo['codigo']) ?>"
                            data-pesquisa-id="<?= (int) $tipo['tipo_pesquisa_id'] ?>"
                            enctype="multipart/form-data">

                            <span class="carga_mas-icon">
                                <img src="<?= base_url('img/' . $tipo['icono']) ?>" alt="<?= esc($tipo['nombre']) ?>">
                            </span>

                            <div>
                                <div class="carga_mas-item-title"><?= esc($tipo['nombre']) ?></div>
                                <div class="carga_mas-item-desc"><?= esc($tipo['descripcion']) ?></div>
                                <input type="file" class="form-control form-control-sm mt-2" name="archivo_excel" accept=".xlsx,.xls" disabled>
                            </div>

                            <button type="submit" class="carga_mas-btn carga_mas-btn-primary" disabled>
                                <i class="bi bi-cloud-arrow-up"></i>
                                Procesar
                            </button>
                        </form>
                    <?php endforeach; ?>
                </div>

                <section class="carga_mas-results" id="cargaMasResults"></section>
            </div>
        </article>
    </section>
</main>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function() {
    const jornadaSelect = document.getElementById('cargaMasJornada');
    const jornadaBox = document.getElementById('cargaMasJornadaBox');
    const resultBox = document.getElementById('cargaMasResults');
    const forms = Array.from(document.querySelectorAll('[data-carga-item="1"]'));

    function safeJson(value, fallback) {
        try {
            return JSON.parse(value || '[]');
        } catch (e) {
            return fallback;
        }
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function actualizarEstado() {
        const option = jornadaSelect.options[jornadaSelect.selectedIndex];
        const jornadaId = jornadaSelect.value;
        const pesquisas = jornadaId ? safeJson(option.dataset.pesquisas, []) : [];
        const status = jornadaId ? parseInt(option.dataset.status || '0', 10) : 0;

        resultBox.style.display = 'none';
        resultBox.innerHTML = '';

        if (!jornadaId) {
            jornadaBox.innerHTML = 'Selecciona una jornada para ver las pesquisas disponibles.';
        } else {
            const badges = pesquisas.length
                ? pesquisas.map(id => '<span class="carga_mas-badge"><i class="bi bi-check-circle"></i> Pesquisa ' + id + '</span>').join('')
                : '<span class="text-muted">Sin pesquisas configuradas</span>';

            jornadaBox.innerHTML =
                '<strong>' + escapeHtml(option.dataset.nombre || 'Jornada') + '</strong><br>' +
                '<span>' + escapeHtml(option.dataset.institucion || '') + '</span><br>' +
                '<small>Fecha: ' + escapeHtml(option.dataset.fecha || 'Sin fecha') + '</small>' +
                '<div class="carga_mas-badges">' + badges + '</div>';
        }

        forms.forEach(form => {
            const pesquisaId = parseInt(form.dataset.pesquisaId || '0', 10);
            const enabled = !!jornadaId && status === 1 && pesquisas.includes(pesquisaId);
            const file = form.querySelector('input[type="file"]');
            const btn = form.querySelector('button[type="submit"]');

            form.classList.toggle('carga_mas-disabled', !enabled);
            file.disabled = !enabled;
            btn.disabled = !enabled;

            if (!enabled) {
                file.value = '';
            }
        });
    }

    function renderLista(titulo, items, tipo) {
        if (!items || !items.length) return '';

        return '<div class="carga_mas-alert carga_mas-alert-' + tipo + '">' +
            '<strong>' + escapeHtml(titulo) + '</strong>' +
            '<ul class="mb-0 mt-2">' + items.slice(0, 30).map(item => {
                const errores = Array.isArray(item.errores) ? item.errores.join('; ') : (item.mensaje || '');
                return '<li>Fila ' + escapeHtml(item.fila || '-') + ' - ' + escapeHtml(item.id_digisalud || '') + ' ' + escapeHtml(item.nombre || '') + ': ' + escapeHtml(errores) + '</li>';
            }).join('') +
            (items.length > 30 ? '<li>... y ' + (items.length - 30) + ' más.</li>' : '') +
            '</ul>' +
            '</div>';
    }

    function renderResultados(data) {
        const guardados = data.guardados || 0;
        const yaEvaluados = data.ya_evaluados || 0;
        const noExisten = data.no_existen || [];
        const noAsociados = data.no_asociados || [];
        const errores = data.errores || [];
        const total = data.total_procesados || 0;

        resultBox.innerHTML =
            '<div class="carga_mas-kpis">' +
                '<div class="carga_mas-kpi"><strong>' + total + '</strong><span>Procesados</span></div>' +
                '<div class="carga_mas-kpi"><strong>' + guardados + '</strong><span>Guardados</span></div>' +
                '<div class="carga_mas-kpi"><strong>' + yaEvaluados + '</strong><span>Ya evaluados</span></div>' +
                '<div class="carga_mas-kpi"><strong>' + noExisten.length + '</strong><span>No existen</span></div>' +
                '<div class="carga_mas-kpi"><strong>' + noAsociados.length + '</strong><span>No asociados</span></div>' +
            '</div>' +
            '<div class="carga_mas-alert-list">' +
                renderLista('Beneficiarios no existentes', noExisten, 'danger') +
                renderLista('Beneficiarios no asociados a la jornada', noAsociados, 'warning') +
                renderLista('Errores / registros no cargados', errores, 'info') +
            '</div>';

        resultBox.style.display = 'block';
    }

    jornadaSelect.addEventListener('change', actualizarEstado);

    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();

            const jornadaId = jornadaSelect.value;
            const tipo = form.dataset.tipo;
            const file = form.querySelector('input[type="file"]');
            const btn = form.querySelector('button[type="submit"]');

            if (!jornadaId || !file.files.length) {
                Swal.fire('Archivo requerido', 'Selecciona una jornada y un archivo Excel.', 'warning');
                return;
            }

            const formData = new FormData();
            formData.append('archivo_excel', file.files[0]);

            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando';

            fetch('<?= site_url('cargas-masivas/procesar') ?>/' + jornadaId + '/' + tipo, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (!data.ok) {
                    throw new Error(data.error || data.mensaje || 'No se pudo procesar el archivo.');
                }

                renderResultados(data);
                Swal.fire('Carga procesada', 'Revisa el resumen de resultados.', 'success');
            })
            .catch(error => {
                Swal.fire('No se pudo procesar', error.message, 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-cloud-arrow-up"></i> Procesar';
            });
        });
    });

    actualizarEstado();
})();
</script>
<?= $this->endSection() ?>

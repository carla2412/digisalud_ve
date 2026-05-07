<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
$nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));
$nombrePesquisa = 'Signos vitales';
$esEdicion      = ! empty($evaluacionExistente);
$evalId         = $evaluacionExistente['id_evaluacion'] ?? '';
$obsExistente   = $evaluacionExistente['observaciones'] ?? '';

$urlRetorno = $jornadaId
    ? base_url("jornadas/{$jornadaId}/beneficiarios")
    : base_url("centros/{$centroId}/beneficiarios");
?>
<style>
    :root {
        --lab-primary: #101a61;
        --lab-primary-soft: #e8edff;
        --lab-bg: #f4f7fb;
        --lab-card: #ffffff;
        --lab-text: #172033;
        --lab-muted: #64748b;
        --lab-border: #dbe3ef;
        --lab-danger: #dc2626;
        --lab-warning: #f59e0b;
        --lab-success: #16a34a;
        --lab-actions-h: 72px;
        --lab-sidebar-w: 72px;
    }

    body {
        background: var(--lab-bg);
    }

    .signos-card,
    .signos-summary-card {
        background: var(--lab-card);
        border: 1px solid var(--lab-border);
        border-radius: 22px;
        box-shadow: 0 16px 36px rgba(15, 23, 42, .06);
    }

    .signos-card {
        padding: 22px;
    }

    .signos-summary-card {
        padding: 18px;
    }

    .signos-section-header {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
        border-bottom: 2px solid #2f80ff;
        padding-bottom: 12px;
    }

    .signos-section-header h2,
    .signos-summary-card h2,
    .signos-summary-card h3 {
        margin: 0;
        color: var(--lab-primary);
        font-size: 1rem;
        font-weight: 900;
    }

    .signos-section-header p {
        margin: 4px 0 0;
        color: var(--lab-muted);
        font-size: .84rem;
    }

    .signos-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 14px;
    }

    .signos-field {
        grid-column: span 6;
    }

    .signos-field--full {
        grid-column: 1 / -1;
    }

    .signos-field label {
        display: block;
        color: #334155;
        font-size: .78rem;
        font-weight: 800;
        margin-bottom: 6px;
    }

    .signos-field input,
    .signos-field select,
    .signos-field textarea {
        width: 100%;
        border: 1.5px solid var(--lab-border);
        border-radius: 12px;
        background: #fff;
        color: var(--lab-text);
        font-size: .86rem;
        padding: 10px 12px;
        outline: none;
        transition: .15s ease;
    }

    .signos-field input:focus,
    .signos-field select:focus,
    .signos-field textarea:focus {
        border-color: var(--lab-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .signos-field small,
    .signos-field .form-text {
        display: block;
        margin-top: 5px;
        color: var(--lab-muted);
        font-size: .72rem;
    }

    .input-unit {
        display: flex;
        align-items: center;
        border: 1.5px solid var(--lab-border);
        border-radius: 12px;
        overflow: hidden;
        background: #fff;
        transition: .15s ease;
    }

    .input-unit:focus-within {
        border-color: var(--lab-primary);
        box-shadow: 0 0 0 3px rgba(16, 26, 97, .08);
    }

    .input-unit input {
        border: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .input-unit span {
        padding: 0 12px;
        color: var(--lab-muted);
        font-size: .76rem;
        font-weight: 800;
        border-left: 1px solid var(--lab-border);
        white-space: nowrap;
    }

    .summary-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        padding: 12px 0;
        border-bottom: 1px solid #edf2f7;
        font-size: .82rem;
    }

    .summary-row:last-child {
        border-bottom: 0;
    }

    .summary-row span {
        color: var(--lab-muted);
    }

    .summary-row strong {
        color: var(--lab-primary);
        text-align: right;
    }

   .actions-bar {
    position: sticky;
    bottom: 0;
    z-index: 20;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 10px;
    width: 100%;
    max-width: 100%;
    min-height: var(--lab-actions-h);
    margin: 18px 0 0;
    padding: 14px 0 0;
    background: transparent;
    border-top: 1px solid var(--lab-border);
    box-sizing: border-box;
}

    .actions-bar .btn,
.actions-bar button {
        border: 0;
        border-radius: 12px;
        min-height: 42px;
        padding: 0 16px;
        font-weight: 900;
        font-size: .84rem;
        cursor: pointer;
        transition: .2s ease;
    }
.actions-bar .btn:disabled,
.actions-bar button:disabled {
    opacity: .55;
    cursor: not-allowed;
}
    .btn--primary {
        background: var(--lab-primary);
        color: #fff;
    }

    .btn--primary:hover {
        background: #17227a;
        color: #fff;
    }

    .btn--secondary {
        background: var(--lab-primary-soft);
        color: var(--lab-primary);
    }

    .btn--ghost {
        background: transparent;
        color: var(--lab-muted);
    }

    .btn--ghost:hover,
    .btn--secondary:hover {
        color: var(--lab-primary);
    }

    .field--warning input,
    .field--warning .input-unit {
        border-color: var(--lab-warning) !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, .1) !important;
    }

    .is-invalid {
        border-color: var(--lab-danger) !important;
        box-shadow: 0 0 0 3px rgba(220, 38, 38, .08) !important;
    }

    @media (max-width: 1100px) {
        .signos-layout {
            grid-template-columns: 1fr !important;
        }

        .signos-summary-panel {
            position: static !important;
        }
    }

    @media (max-width: 760px) {
        .signos-field {
            grid-column: 1 / -1;
        }

        .actions-bar {
            flex-wrap: wrap;
            padding: 12px 14px;
        }

        .actions-bar .btn {
            flex: 1 1 auto;
        }
    }

.eval-page {
    display: grid;
    grid-template-columns: var(--lab-sidebar-w) minmax(0, 1fr);
    min-height: 100dvh;
    background: var(--lab-bg);
    overflow: clip;
}

    /* ─── Sidebar izquierdo: iconos de pesquisas ─── */
    .eval-sidebar {
        background: #101a61;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 12px 0;
        gap: 6px;
    }

    .eval-sidebar-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: 2px solid transparent;
        background: rgba(255, 255, 255, .08);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: .2s;
        padding: 0;
        position: relative;
        text-decoration: none;
    }

    .eval-sidebar-btn img {
        width: 24px;
        height: 24px;
        filter: brightness(0) invert(1);
        opacity: .5;
        transition: .2s;
    }

    .eval-sidebar-btn:hover {
        background: rgba(255, 255, 255, .15);
    }

    .eval-sidebar-btn:hover img {
        opacity: .9;
    }

    .eval-sidebar-btn.active {
        background: #fff;
        border-color: #00D4FF;
    }

    .eval-sidebar-btn.active img {
        filter: none;
        opacity: 1;
    }

    .eval-sidebar-btn.evaluado::after {
        content: '';
        position: absolute;
        bottom: -2px;
        right: -2px;
        width: 12px;
        height: 12px;
        background: #28a745;
        border-radius: 50%;
        border: 2px solid #101a61;
    }

    .eval-sidebar-btn[title]::before {
        content: attr(title);
        position: absolute;
        left: 54px;
        top: 50%;
        transform: translateY(-50%);
        background: #1a2332;
        color: #fff;
        padding: 4px 10px;
        border-radius: 6px;
        font-size: .72rem;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity .15s;
        z-index: 10;
    }

    .eval-sidebar-btn:hover[title]::before {
        opacity: 1;
    }

    /* ─── Header de evaluación ─── */
    .sv-eval-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 24px;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }

    .sv-eval-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .sv-eval-header-left img {
        width: 36px;
        height: 36px;
    }

    .sv-eval-header-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #101a61;
    }

    .sv-eval-header-subtitle {
        font-size: .8rem;
        color: #64748b;
    }

    .sv-eval-header-badge {
        font-size: .7rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    .sv-eval-header-badge.new {
        background: #dbeafe;
        color: #1e40af;
    }

    .sv-eval-header-badge.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .sv-btn-volver {
        font-size: .82rem;
        color: #64748b;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .sv-btn-volver:hover {
        color: #101a61;
    }

    .sv-eval-content {
        overflow-y: auto;
    }

    /* ─── Responsive ─── */
    @media (max-width: 900px) {
        .eval-page {
            grid-template-columns: 1fr;
        }

        .eval-sidebar {
            flex-direction: row;
            padding: 8px 12px;
            overflow-x: auto;
        }
    }

    .signos-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 320px;
    gap: 18px;
    align-items: start;
}

.signos-content {
    min-width: 0;
}

.signos-summary-panel {
    display: flex;
    flex-direction: column;
    gap: 14px;
    position: sticky;
    top: 16px;
}
</style>

<div class="eval-page">

    <!-- ═══ SIDEBAR IZQUIERDO: Pesquisas de la jornada ═══ -->
    <aside class="eval-sidebar">
        <?php foreach ($pesquisasActividad as $pid): ?>
            <?php
            $info = $infoPesquisas[$pid] ?? null;
            if (! $info) continue;
            $esActiva    = ((int) $pid === (int) $tipoPesquisaId);
            $yaEvaluada  = in_array($pid, $pesquisasEvaluadas);
            $clases      = 'eval-sidebar-btn';
            if ($esActiva)   $clases .= ' active';
            if ($yaEvaluada) $clases .= ' evaluado';

            $urlPesquisa = base_url("evaluaciones/formulario/{$beneficiario['id_beneficiario']}/{$pid}")
                . ($jornadaId ? "?jornada_id={$jornadaId}" : "?centro_id={$centroId}");
            ?>
            <a href="<?= $urlPesquisa ?>"
                class="<?= $clases ?>"
                title="<?= esc($info['nombre']) ?>">
                <img src="<?= base_url('img/' . ($esActiva ? $info['img'] : $info['gris'])) ?>"
                    alt="<?= esc($info['nombre']) ?>">
            </a>
        <?php endforeach; ?>
    </aside>

    <!-- ═══ ÁREA PRINCIPAL ═══ -->
    <div class="sv-eval-content">

        <!-- Header -->
        <div class="sv-eval-header">
            <div class="sv-eval-header-left">
                <img src="<?= base_url('img/signosVitales2.svg') ?>"
                    alt="Signos vitales">
                <div>
                    <div class="sv-eval-header-title">Signos vitales</div>
                    <div class="sv-eval-header-subtitle"><?= $nombreCompleto ?></div>
                </div>
                <span class="sv-eval-header-badge <?= $esEdicion ? 'edit' : 'new' ?>">
                    <?= $esEdicion ? 'Editando' : 'Nueva evaluación' ?>
                </span>
            </div>
            <a href="<?= $urlRetorno ?>" class="sv-btn-volver">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- ═══════════════════════════════════════════════════════════ -->
        <!-- FORMULARIO SIGNOS VITALES (HTML proporcionado — sin modificar diseño) -->
        <!-- ═══════════════════════════════════════════════════════════ -->

        <div class="container-fluid py-3" id="evaluacionSignosVitalesApp">
            <div class="row g-3">

                

                <div class="col-12 col-xl-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">

                            <div class="alert alert-info d-flex align-items-start gap-2 mb-4">
                                <div>
                                    <strong>Consejo:</strong>
                                    completa primero los valores numéricos. El sistema validará rangos y resaltará posibles alertas.
                                </div>
                            </div>

                            <form id="formSignosVitales" novalidate>
                                <!-- Hidden fields para el backend -->
                                <input type="hidden" name="beneficiario_id" value="<?= (int) $beneficiario['id_beneficiario'] ?>">
                                <input type="hidden" name="tipo_pesquisa_id" value="<?= (int) $tipoPesquisaId ?>">
                                <input type="hidden" name="jornada_id" value="<?= (int) $jornadaId ?>">
                                <input type="hidden" name="centro_id" value="<?= (int) $centroId ?>">
                                <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">

                                <div class="row g-3">

                                    <div class="col-12 col-md-6">
                                        <label for="sv_tension_sistolica" class="form-label">
                                            Tensión arterial sistólica
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                class="form-control campo-signo-vital"
                                                id="sv_tension_sistolica"
                                                name="campos[tension_sistolica]"
                                                data-codigo="tension_sistolica"
                                                data-label="Tensión sistólica"
                                                data-unidad="mmHg"
                                                data-min="70"
                                                data-max="180"
                                                data-alerta-min="90"
                                                data-alerta-max="140"
                                                placeholder="Ej: 120"
                                                value="<?= esc($valoresExistentes['tension_sistolica'] ?? '') ?>"
                                                required>
                                            <span >mmHg</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 90 - 140 mmHg.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="sv_tension_diastolica" class="form-label">
                                            Tensión arterial diastólica
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                class="form-control campo-signo-vital"
                                                id="sv_tension_diastolica"
                                                name="campos[tension_diastolica]"
                                                data-codigo="tension_diastolica"
                                                data-label="Tensión diastólica"
                                                data-unidad="mmHg"
                                                data-min="40"
                                                data-max="120"
                                                data-alerta-min="60"
                                                data-alerta-max="90"
                                                placeholder="Ej: 80"
                                                value="<?= esc($valoresExistentes['tension_diastolica'] ?? '') ?>"
                                                required>
                                            <span >mmHg</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 60 - 90 mmHg.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="sv_frecuencia_cardiaca" class="form-label">
                                            Frecuencia cardíaca
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                class="form-control campo-signo-vital"
                                                id="sv_frecuencia_cardiaca"
                                                name="campos[frecuencia_cardiaca]"
                                                data-codigo="frecuencia_cardiaca"
                                                data-label="Frecuencia cardíaca"
                                                data-unidad="lpm"
                                                data-min="30"
                                                data-max="220"
                                                data-alerta-min="60"
                                                data-alerta-max="100"
                                                placeholder="Ej: 72"
                                                value="<?= esc($valoresExistentes['frecuencia_cardiaca'] ?? '') ?>"
                                                required>
                                            <span >lpm</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 60 - 100 lpm.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="sv_frecuencia_respiratoria" class="form-label">
                                            Frecuencia respiratoria
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                class="form-control campo-signo-vital"
                                                id="sv_frecuencia_respiratoria"
                                                name="campos[frecuencia_respiratoria]"
                                                data-codigo="frecuencia_respiratoria"
                                                data-label="Frecuencia respiratoria"
                                                data-unidad="rpm"
                                                data-min="5"
                                                data-max="60"
                                                data-alerta-min="12"
                                                data-alerta-max="20"
                                                placeholder="Ej: 16"
                                                value="<?= esc($valoresExistentes['frecuencia_respiratoria'] ?? '') ?>"
                                                required>
                                            <span >rpm</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 12 - 20 rpm.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="sv_temperatura" class="form-label">
                                            Temperatura
                                            <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                step="0.1"
                                                class="form-control campo-signo-vital"
                                                id="sv_temperatura"
                                                name="campos[temperatura]"
                                                data-codigo="temperatura"
                                                data-label="Temperatura"
                                                data-unidad="°C"
                                                data-min="30"
                                                data-max="45"
                                                data-alerta-min="36"
                                                data-alerta-max="37.5"
                                                placeholder="Ej: 36.8"
                                                value="<?= esc($valoresExistentes['temperatura'] ?? '') ?>"
                                                required>
                                            <span >°C</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 36.0 - 37.5 °C.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12 col-md-6">
                                        <label for="sv_saturacion_oxigeno" class="form-label">
                                            Saturación de oxígeno
                                        </label>
                                        <div class="input-unit">
                                            <input
                                                type="number"
                                                class="form-control campo-signo-vital"
                                                id="sv_saturacion_oxigeno"
                                                name="campos[saturacion_o2]"
                                                data-codigo="saturacion_o2"
                                                data-label="Saturación de oxígeno"
                                                data-unidad="%"
                                                data-min="50"
                                                data-max="100"
                                                data-alerta-min="95"
                                                data-alerta-max="100"
                                                placeholder="Ej: 98"
                                                value="<?= esc($valoresExistentes['saturacion_o2'] ?? '') ?>">
                                            <span >%</span>
                                        </div>
                                        <div class="form-text">Rango esperado: 95 - 100%.</div>
                                        <div class="invalid-feedback"></div>
                                    </div>

                                    <div class="col-12">
                                        <label for="sv_remision" class="form-label">
                                            ¿Requiere remisión?
                                        </label>
                                        <select
                                            class="form-select campo-signo-vital"
                                            id="sv_remision"
                                            name="campos[especialista_vitales]"
                                            data-codigo="especialista_vitales"
                                            data-label="Remisión"
                                            data-unidad="">
                                            <option value="">Seleccione una opción</option>
                                            <option value="n" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 'n') ? 'selected' : '' ?>>No requiere remisión</option>
                                            <option value="s" <?= (($valoresExistentes['especialista_vitales'] ?? '') === 's') ? 'selected' : '' ?>>Sí, requiere remisión</option>
                                        </select>
                                        <div class="form-text">
                                            Usa esta opción si los signos vitales sugieren seguimiento médico.
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <label for="sv_observaciones" class="form-label">
                                            Observaciones
                                        </label>
                                        <textarea
                                            class="form-control campo-signo-vital"
                                            id="sv_observaciones"
                                            name="campos[observaciones_vitales]"
                                            data-codigo="observaciones_vitales"
                                            data-label="Observaciones"
                                            data-unidad=""
                                            rows="4"
                                            placeholder="Ej: paciente estable, refiere mareo leve, se recomienda hidratación..."><?= esc($valoresExistentes['observaciones_vitales'] ?? '') ?></textarea>
                                    </div>

                                </div>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-4">
                    <div class="card border-0 shadow-sm position-sticky" style="top: 1rem;">
                        <div class="card-body">

                            <h5 class="mb-3">Resumen rápido</h5>

                            <div id="estadoGeneralSignos" class="alert alert-secondary mb-3">
                                Completa los signos vitales para ver el estado general.
                            </div>

                            <div class="list-group list-group-flush mb-3" id="resumenSignosVitales">
                                <div class="list-group-item px-0 text-muted">
                                    Sin datos registrados.
                                </div>
                            </div>

                            <div class="border rounded p-3 bg-light">
                                <h6 class="mb-2">Alertas</h6>
                                <div id="alertasSignosVitales" class="small text-muted">
                                    No hay alertas por ahora.
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div><!-- /.sv-eval-content -->

</div><!-- /.eval-page -->
<div class="actions-bar signos-actions-bar">
    <button id="btnCancelarSignos" type="button" class="btn btn--ghost">
        Cancelar
    </button>

    <button id="btnLimpiarSignos" type="button" class="btn btn--secondary">
        Limpiar
    </button>

    <button id="btnGuardarSignos" type="button" class="btn btn--primary">
        Guardar evaluación
    </button>
</div>
<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
    (function() {
        const form = document.getElementById('formSignosVitales');
        const campos = Array.from(document.querySelectorAll('.campo-signo-vital'));
        const resumen = document.getElementById('resumenSignosVitales');
        const alertas = document.getElementById('alertasSignosVitales');
        const estadoGeneral = document.getElementById('estadoGeneralSignos');
        const btnGuardar = document.getElementById('btnGuardarSignos');
        const btnLimpiar = document.getElementById('btnLimpiarSignos');
        const btnCancelar = document.getElementById('btnCancelarSignos');
        const summaryCampos = document.getElementById('summaryCamposSignos');
        const summaryAlertas = document.getElementById('summaryAlertasSignos');
        const summaryRemision = document.getElementById('summaryRemisionSignos');
        const summaryObservaciones = document.getElementById('summaryObservacionesSignos');
        const URL_RETORNO = '<?= $urlRetorno ?>';

        function obtenerValorCampo(campo) {
            if (campo.tagName === 'SELECT') {
                return campo.value;
            }
            if (campo.tagName === 'TEXTAREA') {
                return campo.value.trim();
            }
            return campo.value !== '' ? Number(campo.value) : '';
        }

        function cancelarSignos() {
            const confirmar = window.confirm('¿Desea cancelar la evaluación? Los cambios no guardados se perderán.');

            if (!confirmar) {
                return;
            }

            window.location.href = URL_RETORNO;
        }

        function obtenerTextoValor(campo) {
            const valor = obtenerValorCampo(campo);
            const unidad = campo.dataset.unidad || '';

            if (valor === '' || valor === null || typeof valor === 'undefined') {
                return 'Pendiente';
            }
            if (campo.tagName === 'SELECT') {
                const option = campo.options[campo.selectedIndex];
                return option && option.value ? option.text : 'Pendiente';
            }
            if (campo.tagName === 'TEXTAREA') {
                return valor || 'Sin observaciones';
            }
            return `${valor} ${unidad}`.trim();
        }

        function validarCampo(campo) {
            const valor = obtenerValorCampo(campo);
            const esObligatorio = campo.hasAttribute('required');
            const min = campo.dataset.min !== undefined ? Number(campo.dataset.min) : null;
            const max = campo.dataset.max !== undefined ? Number(campo.dataset.max) : null;
            const feedback = campo.closest('.col-12, .col-md-6')?.querySelector('.invalid-feedback');

            campo.classList.remove('is-invalid', 'is-valid');

            if (esObligatorio && valor === '') {
                campo.classList.add('is-invalid');
                if (feedback) feedback.textContent = 'Este campo es obligatorio.';
                return false;
            }

            if (valor !== '' && typeof valor === 'number') {
                if (min !== null && valor < min) {
                    campo.classList.add('is-invalid');
                    if (feedback) feedback.textContent = `El valor mínimo permitido es ${min}.`;
                    return false;
                }
                if (max !== null && valor > max) {
                    campo.classList.add('is-invalid');
                    if (feedback) feedback.textContent = `El valor máximo permitido es ${max}.`;
                    return false;
                }
            }

            if (valor !== '') {
                campo.classList.add('is-valid');
            }

            return true;
        }

        function generarAlertas() {
            const listaAlertas = [];

            campos.forEach((campo) => {
                if (campo.type !== 'number') return;

                const valor = obtenerValorCampo(campo);
                if (valor === '') return;

                const alertaMin = Number(campo.dataset.alertaMin);
                const alertaMax = Number(campo.dataset.alertaMax);
                const label = campo.dataset.label;
                const unidad = campo.dataset.unidad || '';

                if (!Number.isNaN(alertaMin) && valor < alertaMin) {
                    listaAlertas.push({
                        tipo: 'bajo',
                        mensaje: `${label}: ${valor} ${unidad} está por debajo del rango esperado.`
                    });
                }

                if (!Number.isNaN(alertaMax) && valor > alertaMax) {
                    listaAlertas.push({
                        tipo: 'alto',
                        mensaje: `${label}: ${valor} ${unidad} está por encima del rango esperado.`
                    });
                }
            });

            return listaAlertas;
        }

        function actualizarResumen() {
            const camposConValor = campos.filter((campo) => obtenerValorCampo(campo) !== '');
            const listaAlertas = generarAlertas();

            if (camposConValor.length === 0) {
                resumen.innerHTML = `
        <div class="list-group-item px-0 text-muted">
          Sin datos registrados.
        </div>
      `;
            } else {
                resumen.innerHTML = campos.map((campo) => {
                    const label = campo.dataset.label;
                    const texto = obtenerTextoValor(campo);

                    return `
          <div class="list-group-item px-0 d-flex justify-content-between gap-3">
            <span class="text-muted">${label}</span>
            <strong class="text-end">${texto}</strong>
          </div>
        `;
                }).join('');
            }

            if (listaAlertas.length === 0) {
                alertas.className = 'small text-muted';
                alertas.innerHTML = 'No hay alertas por ahora.';
                estadoGeneral.className = 'alert alert-success mb-3';
                estadoGeneral.innerHTML = '<strong>Estado general:</strong> sin alertas críticas detectadas.';
            } else {
                alertas.className = 'small text-danger';
                alertas.innerHTML = `
        <ul class="mb-0 ps-3">
          ${listaAlertas.map(alerta => `<li>${alerta.mensaje}</li>`).join('')}
        </ul>
      `;
                estadoGeneral.className = 'alert alert-warning mb-3';
                estadoGeneral.innerHTML = `
        <strong>Revisar:</strong> se detectaron ${listaAlertas.length} valor(es) fuera del rango esperado.
      `;
            }

            if (camposConValor.length === 0) {
                estadoGeneral.className = 'alert alert-secondary mb-3';
                estadoGeneral.innerHTML = 'Completa los signos vitales para ver el estado general.';
            }

            const totalCampos = campos.length;
            const completados = campos.filter((campo) => obtenerValorCampo(campo) !== '').length;
            const listaAlertas = generarAlertas();

            if (summaryCampos) {
                summaryCampos.textContent = `${completados}/${totalCampos}`;
            }

            if (summaryAlertas) {
                summaryAlertas.textContent = String(listaAlertas.length);
            }

            if (summaryRemision) {
                const remision = document.getElementById('sv_remision');
                const textoRemision = remision && remision.value ?
                    remision.options[remision.selectedIndex].text :
                    'No definida';

                summaryRemision.textContent = textoRemision;
            }

            if (summaryObservaciones) {
                const observaciones = document.getElementById('sv_observaciones')?.value.trim();

                summaryObservaciones.textContent = observaciones || 'Sin observaciones registradas.';
            }
        }

        function validarFormulario() {
            let valido = true;
            campos.forEach((campo) => {
                const campoValido = validarCampo(campo);
                if (!campoValido) valido = false;
            });
            return valido;
        }

        /**
         * Guardar evaluación via AJAX al endpoint existente /evaluaciones/guardar
         */
        async function guardarEvaluacion() {
            const valido = validarFormulario();
            actualizarResumen();

            if (!valido) {
                estadoGeneral.className = 'alert alert-danger mb-3';
                estadoGeneral.innerHTML = '<strong>Faltan datos:</strong> revisa los campos marcados antes de guardar.';
                return;
            }

            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Guardando...';

            try {
                const formData = new FormData(form);

                // Agregar observaciones generales (del textarea de observaciones_vitales)
                // El campo observaciones del formulario va como campo genérico de la evaluación
                const obsTextarea = document.getElementById('sv_observaciones');
                if (obsTextarea) {
                    formData.set('observaciones', obsTextarea.value.trim());
                }

                // Agregar token CSRF
                const csrfName = '<?= csrf_token() ?>';
                const csrfHash = '<?= csrf_hash() ?>';
                formData.append(csrfName, csrfHash);

                const resp = await fetch('<?= base_url("evaluaciones/guardar") ?>', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const data = await resp.json();

                if (data.ok) {
                    estadoGeneral.className = 'alert alert-success mb-3';
                    estadoGeneral.innerHTML = '<strong>Listo:</strong> evaluación guardada correctamente.';

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Guardado!',
                            text: data.mensaje || 'Evaluación de signos vitales guardada.',
                            confirmButtonColor: '#101a61',
                        }).then(() => {
                            if (data.url_retorno) {
                                window.location.href = data.url_retorno;
                            }
                        });
                    } else {
                        alert(data.mensaje || 'Evaluación guardada.');
                        if (data.url_retorno) {
                            window.location.href = data.url_retorno;
                        }
                    }
                } else {
                    estadoGeneral.className = 'alert alert-danger mb-3';
                    estadoGeneral.innerHTML = `<strong>Error:</strong> ${data.mensaje || 'No se pudo guardar.'}`;

                    // Resaltar campo con error
                    if (data.campo) {
                        const campoError = document.querySelector(`[data-codigo="${data.campo}"]`);
                        if (campoError) {
                            campoError.classList.add('is-invalid');
                            campoError.focus();
                        }
                    }

                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.mensaje || 'No se pudo guardar la evaluación.',
                            confirmButtonColor: '#101a61',
                        });
                    }
                }
            } catch (err) {
                console.error('Error guardando evaluación:', err);
                estadoGeneral.className = 'alert alert-danger mb-3';
                estadoGeneral.innerHTML = '<strong>Error:</strong> no se pudo conectar con el servidor.';

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor. Intenta de nuevo.',
                        confirmButtonColor: '#101a61',
                    });
                }
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerHTML = 'Guardar evaluación';
            }
        }

        function limpiarFormulario() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: '¿Limpiar formulario?',
                    text: 'Se borrarán todos los valores ingresados.',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#101a61',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, limpiar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        ejecutarLimpieza();
                    }
                });
            } else {
                if (confirm('¿Limpiar todos los campos?')) {
                    ejecutarLimpieza();
                }
            }
        }

        function ejecutarLimpieza() {
            form.reset();
            campos.forEach((campo) => {
                campo.classList.remove('is-valid', 'is-invalid');
            });
            actualizarResumen();
        }

        // Event listeners
        campos.forEach((campo) => {
            campo.addEventListener('input', () => {
                validarCampo(campo);
                actualizarResumen();
            });
            campo.addEventListener('change', () => {
                validarCampo(campo);
                actualizarResumen();
            });
        });

        btnGuardar.addEventListener('click', guardarEvaluacion);
        btnLimpiar.addEventListener('click', limpiarFormulario);
        btnCancelar.addEventListener('click', cancelarSignos);
        // Inicializar resumen
        actualizarResumen();
    })();
</script>
<?= $this->endSection() ?>
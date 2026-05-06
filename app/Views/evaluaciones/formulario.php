<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<?php
    $nombreCompleto = trim(esc($beneficiario['nombres'] ?? '') . ' ' . esc($beneficiario['apellidos'] ?? ''));
    $nombrePesquisa = esc($tipoPesquisa['descripcion_view'] ?? $tipoPesquisa['nombre_tipo'] ?? 'Evaluación');
    $esEdicion      = ! empty($evaluacionExistente);
    $evalId         = $evaluacionExistente['id_evaluacion'] ?? '';
    $obsExistente   = $evaluacionExistente['observaciones'] ?? '';

    $urlRetorno = $jornadaId
        ? base_url("jornadas/{$jornadaId}/beneficiarios")
        : base_url("centros/{$centroId}/beneficiarios");
?>

<style>
    .eval-page {
        display: grid;
        grid-template-columns: 60px 1fr 280px;
        min-height: calc(100vh - 70px);
        background: #f4f6fb;
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
        background: rgba(255,255,255,.08);
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: .2s;
        padding: 0;
        position: relative;
    }

    .eval-sidebar-btn img {
        width: 24px;
        height: 24px;
        filter: brightness(0) invert(1);
        opacity: .5;
        transition: .2s;
    }

    .eval-sidebar-btn:hover {
        background: rgba(255,255,255,.15);
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

    /* ─── Área central: formulario ─── */
    .eval-main {
        padding: 20px 28px;
        overflow-y: auto;
    }

    .eval-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 18px;
        gap: 12px;
    }

    .eval-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .eval-header-left img {
        width: 36px;
        height: 36px;
    }

    .eval-header-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #101a61;
    }

    .eval-header-subtitle {
        font-size: .8rem;
        color: #64748b;
    }

    .eval-header-badge {
        font-size: .7rem;
        padding: 3px 10px;
        border-radius: 20px;
        font-weight: 700;
    }

    .eval-header-badge.new {
        background: #dbeafe;
        color: #1e40af;
    }

    .eval-header-badge.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .btn-volver {
        font-size: .82rem;
        color: #64748b;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .btn-volver:hover {
        color: #101a61;
    }

    .eval-fecha-row {
        margin-bottom: 18px;
    }

    .eval-fecha-row label {
        font-size: .78rem;
        font-weight: 700;
        color: #101a61;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .eval-fecha-input {
        height: 38px;
        border: 1.5px solid #c7d2e0;
        border-radius: 8px;
        padding: 0 12px;
        font-size: .85rem;
        color: #1a202c;
        background: #f8fafd;
        max-width: 200px;
    }

    /* ─── Secciones del formulario ─── */
    .eval-seccion {
        margin-bottom: 16px;
    }

    .eval-seccion-titulo {
        font-size: .78rem;
        font-weight: 800;
        color: #101a61;
        text-transform: uppercase;
        letter-spacing: .4px;
        margin: 0 0 8px;
        padding: 5px 12px;
        background: #eef1f8;
        border-radius: 6px;
        border-left: 3px solid #101a61;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .eval-seccion-titulo .toggle-icon {
        transition: transform .2s;
        font-size: .7rem;
        color: #8896a7;
    }

    .eval-seccion-titulo.collapsed .toggle-icon {
        transform: rotate(-90deg);
    }

    .eval-seccion-body {
        transition: max-height .3s ease;
        overflow: hidden;
    }

    .eval-seccion-body.collapsed {
        max-height: 0 !important;
        overflow: hidden;
    }

    .eval-campo-wrap {
        margin-bottom: 8px;
    }

    .eval-campo-wrap label {
        font-size: .76rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 2px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .eval-campo-wrap .eval-unidad {
        font-weight: 400;
        color: #8896a7;
        font-size: .7rem;
    }

    .eval-campo-wrap .eval-obligatorio {
        color: #e53e3e;
        font-weight: 700;
    }

    .eval-input {
        width: 100%;
        height: 34px;
        border: 1.5px solid #d2d8e0;
        border-radius: 7px;
        padding: 0 10px;
        font-size: .82rem;
        color: #1a202c;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
    }

    .eval-input:focus {
        border-color: #101a61;
        box-shadow: 0 0 0 2px rgba(16, 26, 97, .08);
        outline: none;
    }

    .eval-input.is-invalid {
        border-color: #e53e3e;
        box-shadow: 0 0 0 2px rgba(229, 62, 62, .1);
    }

    textarea.eval-input {
        height: auto;
        padding: 8px 10px;
    }

    select.eval-input {
        appearance: auto;
    }

    .eval-campo-oculto {
        display: none !important;
    }

    /* ─── Panel derecho: observaciones + guardar ─── */
    .eval-panel-right {
        background: #fff;
        border-left: 1px solid #e2e8f0;
        padding: 20px 18px;
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .eval-panel-right h6 {
        font-size: .78rem;
        font-weight: 800;
        color: #101a61;
        text-transform: uppercase;
        margin: 0 0 6px;
    }

    .eval-obs-textarea {
        width: 100%;
        min-height: 120px;
        border: 1.5px solid #d2d8e0;
        border-radius: 8px;
        padding: 10px;
        font-size: .82rem;
        resize: vertical;
        color: #1a202c;
        background: #f8fafd;
    }

    .eval-obs-textarea:focus {
        border-color: #101a61;
        outline: none;
        box-shadow: 0 0 0 2px rgba(16, 26, 97, .08);
    }

    .eval-remitir {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: .82rem;
        color: #64748b;
    }

    .eval-remitir i {
        color: #e53e3e;
        font-size: 1rem;
    }

    .btn-guardar-eval {
        width: 100%;
        height: 44px;
        border: 2px solid #101a61;
        border-radius: 10px;
        background: #fff;
        color: #101a61;
        font-weight: 800;
        font-size: .88rem;
        cursor: pointer;
        transition: .2s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin-top: auto;
    }

    .btn-guardar-eval:hover {
        background: #101a61;
        color: #fff;
    }

    .btn-guardar-eval:disabled {
        opacity: .5;
        cursor: not-allowed;
    }

    .eval-error-msg {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #991b1b;
        padding: 8px 12px;
        border-radius: 8px;
        font-size: .8rem;
        display: none;
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

        .eval-panel-right {
            border-left: none;
            border-top: 1px solid #e2e8f0;
        }
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

    <!-- ═══ ÁREA CENTRAL: Formulario ═══ -->
    <main class="eval-main">

        <!-- Header -->
        <div class="eval-header">
            <div class="eval-header-left">
                <img src="<?= base_url('img/' . ($infoPesquisas[$tipoPesquisaId]['img'] ?? 'sanguinea2.svg')) ?>"
                     alt="<?= esc($nombrePesquisa) ?>">
                <div>
                    <div class="eval-header-title"><?= esc($nombrePesquisa) ?></div>
                    <div class="eval-header-subtitle"><?= $nombreCompleto ?></div>
                </div>
                <span class="eval-header-badge <?= $esEdicion ? 'edit' : 'new' ?>">
                    <?= $esEdicion ? 'Editando' : 'Nueva evaluación' ?>
                </span>
            </div>
            <a href="<?= $urlRetorno ?>" class="btn-volver">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Fecha de evaluación -->
        <div class="eval-fecha-row">
            <label>Fecha de evaluación</label><br>
            <input type="date" class="eval-fecha-input" id="eval_fecha"
                   value="<?= date('Y-m-d') ?>" readonly>
        </div>

        <!-- Formulario dinámico por secciones -->
        <form id="formEvaluacion" autocomplete="off">
            <input type="hidden" name="beneficiario_id" value="<?= (int) $beneficiario['id_beneficiario'] ?>">
            <input type="hidden" name="tipo_pesquisa_id" value="<?= (int) $tipoPesquisaId ?>">
            <input type="hidden" name="jornada_id" value="<?= (int) $jornadaId ?>">
            <input type="hidden" name="centro_id" value="<?= (int) $centroId ?>">
            <input type="hidden" name="evaluacion_id" value="<?= esc($evalId) ?>">

            <?php foreach ($itemsAgrupados as $seccion => $items): ?>
                <?php
                    // No mostrar sección "observaciones_lab" aquí — va en panel derecho
                    if (in_array($seccion, ['observaciones_lab', 'seguimiento_visual', 'seguimiento_vitales'])) continue;

                    $nombreSeccion = $nombresSecciones[$seccion] ?? ucfirst(str_replace('_', ' ', $seccion));
                ?>

                <div class="eval-seccion">
                    <h6 class="eval-seccion-titulo" onclick="toggleSeccion(this)">
                        <?= esc($nombreSeccion) ?>
                        <i class="bi bi-chevron-down toggle-icon"></i>
                    </h6>

                    <div class="eval-seccion-body">
                        <div class="row g-2">
                            <?php foreach ($items as $item): ?>
                                <?php
                                    $codigo     = $item['codigo'];
                                    $valorPrev  = $valoresExistentes[$codigo] ?? '';
                                    $oculto     = ! empty($item['depende_de']) ? 'eval-campo-oculto' : '';
                                    $dependeAttr = '';
                                    if (! empty($item['depende_de'])) {
                                        $dependeAttr = "data-depende-de=\"{$item['depende_de']}\" data-depende-valor=\"{$item['depende_valor']}\"";
                                    }
                                    $unidadStr = $item['unidad'] ? "<span class=\"eval-unidad\">({$item['unidad']})</span>" : '';
                                    $reqStr    = $item['obligatorio'] ? '<span class="eval-obligatorio">*</span>' : '';
                                ?>

                                <div class="col-md-<?= (int) $item['ancho_col'] ?> eval-campo-wrap <?= $oculto ?>"
                                     id="wrap_<?= esc($codigo) ?>"
                                     <?= $dependeAttr ?>>

                                    <label for="campo_<?= esc($codigo) ?>">
                                        <?= esc($item['nombre']) ?> <?= $unidadStr ?> <?= $reqStr ?>
                                    </label>

                                    <?php if ($item['tipo_dato'] === 'number'): ?>
                                        <input type="number" step="any"
                                               class="eval-input"
                                               name="campos[<?= esc($codigo) ?>]"
                                               id="campo_<?= esc($codigo) ?>"
                                               value="<?= esc($valorPrev) ?>"
                                               <?= $item['valor_min'] !== null ? "min=\"{$item['valor_min']}\"" : '' ?>
                                               <?= $item['valor_max'] !== null ? "max=\"{$item['valor_max']}\"" : '' ?>
                                               <?= $item['placeholder'] ? "placeholder=\"" . esc($item['placeholder']) . "\"" : '' ?>
                                               data-codigo="<?= esc($codigo) ?>">

                                    <?php elseif ($item['tipo_dato'] === 'select'): ?>
                                        <select class="eval-input"
                                                name="campos[<?= esc($codigo) ?>]"
                                                id="campo_<?= esc($codigo) ?>"
                                                data-codigo="<?= esc($codigo) ?>">
                                            <option value="">— Seleccionar —</option>
                                            <?php foreach ($item['opciones'] as $opt): ?>
                                                <option value="<?= esc($opt['valor']) ?>"
                                                    <?= ($valorPrev == $opt['valor']) ? 'selected' : '' ?>>
                                                    <?= esc($opt['texto']) ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                    <?php elseif ($item['tipo_dato'] === 'textarea'): ?>
                                        <textarea class="eval-input"
                                                  name="campos[<?= esc($codigo) ?>]"
                                                  id="campo_<?= esc($codigo) ?>"
                                                  rows="2"
                                                  data-codigo="<?= esc($codigo) ?>"
                                                  <?= $item['placeholder'] ? "placeholder=\"" . esc($item['placeholder']) . "\"" : '' ?>><?= esc($valorPrev) ?></textarea>

                                    <?php elseif ($item['tipo_dato'] === 'date'): ?>
                                        <input type="date"
                                               class="eval-input"
                                               name="campos[<?= esc($codigo) ?>]"
                                               id="campo_<?= esc($codigo) ?>"
                                               value="<?= esc($valorPrev) ?>"
                                               data-codigo="<?= esc($codigo) ?>">

                                    <?php else: ?>
                                        <input type="text"
                                               class="eval-input"
                                               name="campos[<?= esc($codigo) ?>]"
                                               id="campo_<?= esc($codigo) ?>"
                                               value="<?= esc($valorPrev) ?>"
                                               <?= $item['placeholder'] ? "placeholder=\"" . esc($item['placeholder']) . "\"" : '' ?>
                                               data-codigo="<?= esc($codigo) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </form>
    </main>

    <!-- ═══ PANEL DERECHO: Observaciones + Guardar ═══ -->
    <aside class="eval-panel-right">
        <div>
            <h6>Observaciones</h6>
            <textarea class="eval-obs-textarea"
                      id="eval_observaciones"
                      placeholder="Observaciones generales..."
                      form="formEvaluacion"
                      name="observaciones"><?= esc($obsExistente) ?></textarea>
        </div>

        <?php
            // Buscar el item "remitir_especialista" de esta pesquisa en las secciones de panel derecho
            $itemRemitir = null;
            foreach ($itemsAgrupados as $seccion => $items) {
                foreach ($items as $item) {
                    if (strpos($item['codigo'], 'remitir_especialista') !== false ||
                        strpos($item['codigo'], 'especialista_') !== false) {
                        $itemRemitir = $item;
                        break 2;
                    }
                }
            }
        ?>

        <?php if ($itemRemitir): ?>
            <div class="eval-remitir">
                <i class="bi bi-exclamation-triangle"></i>
                <label for="campo_<?= esc($itemRemitir['codigo']) ?>" style="font-weight:600; cursor:pointer;">
                    Remitir a especialista
                </label>
                <select class="eval-input"
                        name="campos[<?= esc($itemRemitir['codigo']) ?>]"
                        id="campo_<?= esc($itemRemitir['codigo']) ?>"
                        form="formEvaluacion"
                        style="width:70px; height:30px; font-size:.78rem;"
                        data-codigo="<?= esc($itemRemitir['codigo']) ?>">
                    <option value="">—</option>
                    <?php foreach (($itemRemitir['opciones'] ?? []) as $opt): ?>
                        <option value="<?= esc($opt['valor']) ?>"
                            <?= (($valoresExistentes[$itemRemitir['codigo']] ?? '') == $opt['valor']) ? 'selected' : '' ?>>
                            <?= esc($opt['texto']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>

        <!-- Mensaje de error -->
        <div class="eval-error-msg" id="evalErrorMsg">
            <i class="bi bi-exclamation-triangle me-1"></i>
            <span id="evalErrorText"></span>
        </div>

        <!-- Botón guardar -->
        <button type="button" class="btn-guardar-eval" id="btnGuardarEval" onclick="guardarEvaluacion()">
            GUARDAR
        </button>
    </aside>

</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
/**
 * Toggle sección colapsable
 */
function toggleSeccion(titulo) {
    const body = titulo.nextElementSibling;
    titulo.classList.toggle('collapsed');
    body.classList.toggle('collapsed');
}

/**
 * Dependencias condicionales: mostrar/ocultar campos según valor padre
 */
function activarDependencias() {
    document.querySelectorAll('[data-depende-de]').forEach(wrap => {
        const codigoPadre   = wrap.dataset.dependeDe;
        const valorRequerido = wrap.dataset.dependeValor;
        const padre = document.getElementById('campo_' + codigoPadre);

        if (!padre) return;

        const verificar = () => {
            if (padre.value === valorRequerido) {
                wrap.classList.remove('eval-campo-oculto');
            } else {
                wrap.classList.add('eval-campo-oculto');
                const input = wrap.querySelector('.eval-input');
                if (input) input.value = '';
            }
        };

        padre.addEventListener('change', verificar);
        verificar();
    });
}

activarDependencias();

/**
 * Guardar evaluación via AJAX
 */
async function guardarEvaluacion() {
    const form     = document.getElementById('formEvaluacion');
    const btn      = document.getElementById('btnGuardarEval');
    const errorDiv = document.getElementById('evalErrorMsg');

    errorDiv.style.display = 'none';
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));

    btn.disabled  = true;
    btn.textContent = 'Guardando...';

    try {
        const formData = new FormData(form);
        // Agregar observaciones del panel derecho
        formData.set('observaciones', document.getElementById('eval_observaciones').value);
        // CSRF
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        const res  = await fetch('<?= base_url("evaluaciones/guardar") ?>', {
            method: 'POST',
            body: formData,
        });

        const data = await res.json();

        if (!data.ok) {
            if (data.campo) {
                const campoErr = document.getElementById('campo_' + data.campo);
                if (campoErr) {
                    campoErr.classList.add('is-invalid');
                    campoErr.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    campoErr.focus();
                }
            }
            document.getElementById('evalErrorText').textContent = data.mensaje || 'Error al guardar.';
            errorDiv.style.display = 'block';
            return;
        }

        // Éxito — redirigir a la lista de beneficiarios
        Swal.fire({
            icon: 'success',
            title: '¡Evaluación guardada!',
            text: data.mensaje,
            confirmButtonColor: '#101a61',
            timer: 1800,
            showConfirmButton: false,
        }).then(() => {
            if (data.url_retorno) {
                window.location.href = data.url_retorno;
            }
        });

    } catch (err) {
        console.error('Error guardando:', err);
        document.getElementById('evalErrorText').textContent = 'Error de conexión al guardar.';
        errorDiv.style.display = 'block';
    } finally {
        btn.disabled = false;
        btn.textContent = 'GUARDAR';
    }
}
</script>
<?= $this->endSection() ?>
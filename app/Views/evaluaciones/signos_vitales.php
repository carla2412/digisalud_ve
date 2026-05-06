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
    .eval-page {
        display: grid;
        grid-template-columns: 60px 1fr;
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

            <div class="col-12">
              <div class="card border-0 shadow-sm">
                <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                  <div>
                    <h4 class="mb-1">Signos vitales</h4>
                    <p class="text-muted mb-0">
                      Registra los valores clínicos principales de forma rápida y sencilla.
                    </p>
                  </div>

                  <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" id="btnLimpiarSignos">
                      Limpiar
                    </button>
                    <button type="button" class="btn btn-primary" id="btnGuardarSignos">
                      Guardar evaluación
                    </button>
                  </div>
                </div>
              </div>
            </div>

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
                        <div class="input-group">
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
                            required
                          >
                          <span class="input-group-text">mmHg</span>
                        </div>
                        <div class="form-text">Rango esperado: 90 - 140 mmHg.</div>
                        <div class="invalid-feedback"></div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="sv_tension_diastolica" class="form-label">
                          Tensión arterial diastólica
                          <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
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
                            required
                          >
                          <span class="input-group-text">mmHg</span>
                        </div>
                        <div class="form-text">Rango esperado: 60 - 90 mmHg.</div>
                        <div class="invalid-feedback"></div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="sv_frecuencia_cardiaca" class="form-label">
                          Frecuencia cardíaca
                          <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
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
                            required
                          >
                          <span class="input-group-text">lpm</span>
                        </div>
                        <div class="form-text">Rango esperado: 60 - 100 lpm.</div>
                        <div class="invalid-feedback"></div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="sv_frecuencia_respiratoria" class="form-label">
                          Frecuencia respiratoria
                          <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
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
                            required
                          >
                          <span class="input-group-text">rpm</span>
                        </div>
                        <div class="form-text">Rango esperado: 12 - 20 rpm.</div>
                        <div class="invalid-feedback"></div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="sv_temperatura" class="form-label">
                          Temperatura
                          <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
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
                            required
                          >
                          <span class="input-group-text">°C</span>
                        </div>
                        <div class="form-text">Rango esperado: 36.0 - 37.5 °C.</div>
                        <div class="invalid-feedback"></div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label for="sv_saturacion_oxigeno" class="form-label">
                          Saturación de oxígeno
                        </label>
                        <div class="input-group">
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
                            value="<?= esc($valoresExistentes['saturacion_o2'] ?? '') ?>"
                          >
                          <span class="input-group-text">%</span>
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
                          data-unidad=""
                        >
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
                          placeholder="Ej: paciente estable, refiere mareo leve, se recomienda hidratación..."
                        ><?= esc($valoresExistentes['observaciones_vitales'] ?? '') ?></textarea>
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

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
(function () {
  const form = document.getElementById('formSignosVitales');
  const campos = Array.from(document.querySelectorAll('.campo-signo-vital'));
  const resumen = document.getElementById('resumenSignosVitales');
  const alertas = document.getElementById('alertasSignosVitales');
  const estadoGeneral = document.getElementById('estadoGeneralSignos');
  const btnGuardar = document.getElementById('btnGuardarSignos');
  const btnLimpiar = document.getElementById('btnLimpiarSignos');

  function obtenerValorCampo(campo) {
    if (campo.tagName === 'SELECT') {
      return campo.value;
    }
    if (campo.tagName === 'TEXTAREA') {
      return campo.value.trim();
    }
    return campo.value !== '' ? Number(campo.value) : '';
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
      const csrfName  = '<?= csrf_token() ?>';
      const csrfHash  = '<?= csrf_hash() ?>';
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

  // Inicializar resumen
  actualizarResumen();
})();
</script>
<?= $this->endSection() ?>
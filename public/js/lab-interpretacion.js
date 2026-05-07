/**
 * ═══════════════════════════════════════════════════════════════════
 * DigiSalud — Interpretación clínica para Laboratorio (pesquisa 2)
 * ═══════════════════════════════════════════════════════════════════
 *
 * Archivo: public/js/lab-interpretacion.js
 *
 * Funciones puras que implementan:
 *   1. Clasificación de anemia por hemoglobina (edad, sexo, embarazo)
 *   2. Riesgo cardiometabólico (triglicéridos, HDL, VLDL, LDL)
 *   3. Semáforo de anemia (conteo acumulado por jornada/centro)
 *   4. Validación inteligente pre-guardado
 *
 * Se inicializa automáticamente si existe el div#labDatosBeneficiario
 * con los data-attributes necesarios.
 *
 * NO modifica la función guardarEvaluacion() existente. Solo agrega
 * un interceptor que se ejecuta ANTES del POST AJAX.
 */

var LabInterpretacion = (function () {
    'use strict';

    // ══════════════════════════════════════════════════════════════
    // CONFIGURACIÓN: Colores y clases CSS para badges
    // ══════════════════════════════════════════════════════════════

    var CLASES = {
        normal:   { clase: 'lab-badge lab-badge-normal',   icono: 'bi-check-circle',         texto: 'Normal' },
        leve:     { clase: 'lab-badge lab-badge-leve',     icono: 'bi-exclamation-triangle', texto: 'Anemia leve' },
        moderada: { clase: 'lab-badge lab-badge-moderada', icono: 'bi-exclamation-triangle', texto: 'Anemia moderada' },
        severa:   { clase: 'lab-badge lab-badge-severa',   icono: 'bi-exclamation-circle',   texto: 'Anemia severa' },
        revisar:  { clase: 'lab-badge lab-badge-revisar',  icono: 'bi-question-circle',      texto: 'Revisar datos' },
        sin_dato: { clase: 'lab-badge lab-badge-revisar',  icono: 'bi-dash-circle',          texto: 'Sin registrar' }
    };

    var CLASES_CARDIO = {
        normal:       { clase: 'lab-badge lab-badge-normal',   icono: 'bi-check-circle',         texto: 'Normal' },
        limite_alto:  { clase: 'lab-badge lab-badge-leve',     icono: 'bi-exclamation-triangle', texto: 'Límite alto' },
        alto:         { clase: 'lab-badge lab-badge-moderada', icono: 'bi-exclamation-triangle', texto: 'Alto' },
        muy_alto:     { clase: 'lab-badge lab-badge-severa',   icono: 'bi-exclamation-circle',   texto: 'Muy alto' },
        riesgo:       { clase: 'lab-badge lab-badge-moderada', icono: 'bi-heart-pulse',          texto: 'Riesgo cardiometabólico' }
    };

    // ══════════════════════════════════════════════════════════════
    // 1. CLASIFICACIÓN DE ANEMIA
    // ══════════════════════════════════════════════════════════════

    /**
     * Clasifica anemia según hemoglobina, edad en días, sexo y embarazo.
     *
     * @param {number} hb          - Hemoglobina en g/dL
     * @param {number} edadDias    - Edad en días al momento de la medición
     * @param {string} sexo        - 'M' o 'F'
     * @param {string} embarazada  - 's' o 'n'
     * @returns {object} { clave, texto, rango, clase, icono }
     */
    function clasificarAnemia(hb, edadDias, sexo, embarazada) {
        // Validaciones previas
        if (hb === null || hb === undefined || hb === '' || isNaN(hb)) {
            return _resultado('sin_dato', '', CLASES);
        }

        hb = parseFloat(hb);

        if (edadDias <= 0) {
            return _resultado('revisar', 'Edad inválida', CLASES);
        }

        if (hb > 20 || (hb > 0 && hb < 3)) {
            return _resultado('revisar', 'Hb fuera de rango válido', CLASES);
        }

        // A. Edad 183–1825 días (≈6 meses a 5 años)
        if (edadDias >= 183 && edadDias <= 1825) {
            if (hb >= 11.0 && hb <= 20)  return _resultado('normal',   '11.0–20',     CLASES);
            if (hb >= 10.0 && hb <= 10.9) return _resultado('leve',     '10.0–10.9',   CLASES);
            if (hb >= 7.0  && hb <= 9.9)  return _resultado('moderada', '7.0–9.9',     CLASES);
            if (hb >= 3.0  && hb < 7.0)   return _resultado('severa',   '3.0–<7.0',    CLASES);
            return _resultado('revisar', '', CLASES);
        }

        // B. Edad 1826–4382 días (≈5 a 12 años)
        if (edadDias >= 1826 && edadDias <= 4382) {
            if (hb >= 11.5 && hb <= 20)   return _resultado('normal',   '11.5–20',     CLASES);
            if (hb >= 11.0 && hb <= 11.4)  return _resultado('leve',     '11.0–11.4',   CLASES);
            if (hb >= 8.0  && hb <= 10.9)  return _resultado('moderada', '8.0–10.9',    CLASES);
            if (hb >= 3.0  && hb < 8.0)    return _resultado('severa',   '3.0–<8.0',    CLASES);
            return _resultado('revisar', '', CLASES);
        }

        // C. Edad 4383–5478 días (≈12 a 15 años)
        if (edadDias >= 4383 && edadDias <= 5478) {
            if (hb >= 12.0 && hb <= 20)   return _resultado('normal',   '12.0–20',     CLASES);
            if (hb >= 11.0 && hb <= 11.9)  return _resultado('leve',     '11.0–11.9',   CLASES);
            if (hb >= 8.0  && hb <= 10.9)  return _resultado('moderada', '8.0–10.9',    CLASES);
            if (hb >= 3.0  && hb < 8.0)    return _resultado('severa',   '3.0–<8.0',    CLASES);
            return _resultado('revisar', '', CLASES);
        }

        // D. Edad >= 5479 días (≈15+ años) — depende de sexo/embarazo
        if (edadDias >= 5479) {
            // D.1 Masculino
            if (sexo === 'M') {
                if (hb >= 13.0 && hb <= 20)   return _resultado('normal',   '13.0–20',     CLASES);
                if (hb >= 11.0 && hb <= 12.9)  return _resultado('leve',     '11.0–12.9',   CLASES);
                if (hb >= 8.0  && hb <= 10.9)  return _resultado('moderada', '8.0–10.9',    CLASES);
                if (hb >= 3.0  && hb < 8.0)    return _resultado('severa',   '3.0–<8.0',    CLASES);
                return _resultado('revisar', '', CLASES);
            }

            // D.2 Femenino embarazada
            if (sexo === 'F' && embarazada === 's') {
                if (hb >= 11.0 && hb <= 20)   return _resultado('normal',   '11.0–20',     CLASES);
                if (hb >= 10.0 && hb <= 10.9)  return _resultado('leve',     '10.0–10.9',   CLASES);
                if (hb >= 7.0  && hb <= 9.9)   return _resultado('moderada', '7.0–9.9',     CLASES);
                if (hb >= 3.0  && hb < 7.0)    return _resultado('severa',   '3.0–<7.0',    CLASES);
                return _resultado('revisar', '', CLASES);
            }

            // D.3 Femenino no embarazada
            if (hb >= 12.0 && hb <= 20)   return _resultado('normal',   '12.0–20',     CLASES);
            if (hb >= 11.0 && hb <= 11.9)  return _resultado('leve',     '11.0–11.9',   CLASES);
            if (hb >= 8.0  && hb <= 10.9)  return _resultado('moderada', '8.0–10.9',    CLASES);
            if (hb >= 3.0  && hb < 8.0)    return _resultado('severa',   '3.0–<8.0',    CLASES);
            return _resultado('revisar', '', CLASES);
        }

        // Edad < 183 días — fuera de rango de evaluación
        return _resultado('revisar', 'Menor de 6 meses', CLASES);
    }

    // ══════════════════════════════════════════════════════════════
    // 2. RIESGO CARDIOMETABÓLICO
    // ══════════════════════════════════════════════════════════════

    /**
     * Clasifica triglicéridos.
     */
    function clasificarTrigliceridos(valor) {
        if (!valor && valor !== 0) return null;
        valor = parseFloat(valor);
        if (isNaN(valor)) return null;

        if (valor < 150)               return _resultado('normal',      '<150 mg/dL',      CLASES_CARDIO);
        if (valor >= 150 && valor <= 199) return _resultado('limite_alto', '150–199 mg/dL',   CLASES_CARDIO);
        if (valor >= 200 && valor <= 499) return _resultado('alto',        '200–499 mg/dL',   CLASES_CARDIO);
        if (valor >= 500)               return _resultado('muy_alto',    '≥500 mg/dL',      CLASES_CARDIO);
        return null;
    }

    /**
     * Clasifica HDL-Colesterol según sexo.
     */
    function clasificarHDL(valor, sexo) {
        if (!valor && valor !== 0) return null;
        valor = parseFloat(valor);
        if (isNaN(valor)) return null;

        if (sexo === 'M') {
            return valor >= 40
                ? _resultado('normal', '≥40 mg/dL', CLASES_CARDIO)
                : _resultado('riesgo', '<40 mg/dL',  CLASES_CARDIO);
        }
        // Femenino
        return valor >= 50
            ? _resultado('normal', '≥50 mg/dL', CLASES_CARDIO)
            : _resultado('riesgo', '<50 mg/dL',  CLASES_CARDIO);
    }

    /**
     * Clasifica VLDL-Colesterol.
     */
    function clasificarVLDL(valor) {
        if (!valor && valor !== 0) return null;
        valor = parseFloat(valor);
        if (isNaN(valor)) return null;

        return valor < 30
            ? _resultado('normal', '<30 mg/dL', CLASES_CARDIO)
            : _resultado('riesgo', '≥30 mg/dL', CLASES_CARDIO);
    }

    /**
     * Clasifica LDL-Colesterol.
     */
    function clasificarLDL(valor) {
        if (!valor && valor !== 0) return null;
        valor = parseFloat(valor);
        if (isNaN(valor)) return null;

        return valor < 100
            ? _resultado('normal', '<100 mg/dL', CLASES_CARDIO)
            : _resultado('riesgo', '≥100 mg/dL', CLASES_CARDIO);
    }

    // ══════════════════════════════════════════════════════════════
    // 3. VALIDACIÓN PRE-GUARDADO
    // ══════════════════════════════════════════════════════════════

    /**
     * Revisa valores antes de guardar y retorna advertencias.
     * @returns {Array} Lista de objetos { tipo: 'warning'|'info'|'error', mensaje }
     */
    function validarPreGuardado(edadDias, sexo, embarazada) {
        var advertencias = [];
        var hbInput = document.getElementById('campo_hemoglobina');
        var hb = hbInput ? parseFloat(hbInput.value) : NaN;

        // Edad inválida
        if (edadDias <= 0) {
            advertencias.push({
                tipo: 'error',
                mensaje: 'No se puede calcular interpretación de anemia: edad inválida. Verifique la fecha de nacimiento del beneficiario.'
            });
        }

        // Hemoglobina en rango "revisar"
        if (!isNaN(hb) && (hb > 20 || (hb > 0 && hb < 3))) {
            advertencias.push({
                tipo: 'warning',
                mensaje: 'El valor de hemoglobina (' + hb.toFixed(1) + ' g/dL) está en rango de "Revisar datos". ¿Desea guardar de todas formas?'
            });
        }

        // Hemoglobina vacía
        if (hbInput && (hbInput.value === '' || hbInput.value === null)) {
            advertencias.push({
                tipo: 'info',
                mensaje: 'No se registró hemoglobina. La clasificación de anemia quedará como "Sin registrar datos de hemoglobina".'
            });
        }

        // Riesgos cardiometabólicos
        var riesgos = [];
        var campos_cardio = [
            { id: 'campo_trigliceridos',   nombre: 'Triglicéridos',   fn: function(v) { return clasificarTrigliceridos(v); } },
            { id: 'campo_hdl_colesterol',  nombre: 'HDL-Colesterol',  fn: function(v) { return clasificarHDL(v, sexo); } },
            { id: 'campo_vldl_colesterol', nombre: 'VLDL-Colesterol', fn: function(v) { return clasificarVLDL(v); } },
            { id: 'campo_ldl_colesterol',  nombre: 'LDL-Colesterol',  fn: function(v) { return clasificarLDL(v); } }
        ];

        campos_cardio.forEach(function(c) {
            var el = document.getElementById(c.id);
            if (!el || el.value === '') return;
            var result = c.fn(el.value);
            if (result && result.clave !== 'normal') {
                riesgos.push(c.nombre + ': ' + result.texto + ' (' + result.rango + ')');
            }
        });

        if (riesgos.length > 0) {
            advertencias.push({
                tipo: 'warning',
                mensaje: 'Se detectaron ' + riesgos.length + ' indicador(es) de riesgo cardiometabólico:\n• ' + riesgos.join('\n• ')
            });
        }

        return advertencias;
    }

    // ══════════════════════════════════════════════════════════════
    // 4. RENDERIZADO DE BADGES
    // ══════════════════════════════════════════════════════════════

    /**
     * Inserta/actualiza un badge debajo de un input.
     */
    function renderBadge(inputId, resultado) {
        var input = document.getElementById(inputId);
        if (!input) return;

        var wrap = input.closest('.eval-campo-wrap');
        if (!wrap) return;

        // Remover badge anterior si existe
        var anterior = wrap.querySelector('.lab-badge');
        if (anterior) anterior.remove();

        if (!resultado) return;

        var badge = document.createElement('div');
        badge.className = resultado.clase;
        badge.innerHTML = '<i class="bi ' + resultado.icono + '"></i> ' +
                          resultado.texto +
                          (resultado.rango ? ' <span class="lab-badge-rango">(' + resultado.rango + ')</span>' : '');

        // Insertar después del input (o del contenedor del input)
        input.parentNode.insertBefore(badge, input.nextSibling);
    }

    // ══════════════════════════════════════════════════════════════
    // 5. SEMÁFORO DE ANEMIA (carga AJAX)
    // ══════════════════════════════════════════════════════════════

    /**
     * Carga y renderiza el widget de semáforo en el panel derecho.
     */
    function cargarSemaforo(baseUrl, jornadaId, centroId) {
        var contenedor = document.getElementById('labSemaforoWidget');
        if (!contenedor) return;

        var url = baseUrl + 'lab/semaforo?';
        if (jornadaId) url += 'jornada_id=' + jornadaId;
        else if (centroId) url += 'centro_id=' + centroId;
        else return;

        contenedor.innerHTML = '<div class="lab-semaforo-loading"><i class="bi bi-hourglass-split"></i> Cargando semáforo...</div>';

        fetch(url, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(data) {
            if (!data.ok) {
                contenedor.innerHTML = '<div class="lab-semaforo-loading">No hay datos disponibles</div>';
                return;
            }
            renderSemaforo(contenedor, data);
        })
        .catch(function() {
            contenedor.innerHTML = '<div class="lab-semaforo-loading">Error al cargar</div>';
        });
    }

    /**
     * Renderiza el HTML del semáforo.
     */
    function renderSemaforo(contenedor, data) {
        var s = data.semaforo;
        var total = s.verde + s.amarillo + s.naranja + s.rojo;

        var pct = function(val) {
            return total > 0 ? Math.round(val * 100 / total) : 0;
        };

        var html = '' +
            '<div class="lab-semaforo-header">' +
                '<i class="bi bi-bar-chart-fill"></i> ' +
                '<strong>Semáforo de anemia</strong>' +
                '<span class="lab-semaforo-total">' + data.total_evaluados + ' evaluados</span>' +
            '</div>' +
            '<div class="lab-semaforo-grid">' +
                _semaforoItem('verde',    s.verde,    'Normal',   pct(s.verde)) +
                _semaforoItem('amarillo', s.amarillo, 'Leve',     pct(s.amarillo)) +
                _semaforoItem('naranja',  s.naranja,  'Moderada', pct(s.naranja)) +
                _semaforoItem('rojo',     s.rojo,     'Severa',   pct(s.rojo)) +
                _semaforoItem('gris',     s.gris,     'Revisar',  '—') +
            '</div>' +
            '<div class="lab-semaforo-barra">' +
                (total > 0 ? (
                    '<div class="lab-barra-seg lab-barra-verde"    style="width:' + pct(s.verde)    + '%"></div>' +
                    '<div class="lab-barra-seg lab-barra-amarillo" style="width:' + pct(s.amarillo) + '%"></div>' +
                    '<div class="lab-barra-seg lab-barra-naranja"  style="width:' + pct(s.naranja)  + '%"></div>' +
                    '<div class="lab-barra-seg lab-barra-rojo"     style="width:' + pct(s.rojo)     + '%"></div>'
                ) : '<div class="lab-barra-seg lab-barra-gris" style="width:100%"></div>') +
            '</div>' +
            '<div class="lab-semaforo-nota">' +
                'Porcentajes sobre total sin incluir "Revisar datos"' +
            '</div>';

        contenedor.innerHTML = html;
    }

    function _semaforoItem(color, count, label, pct) {
        return '<div class="lab-sem-item lab-sem-' + color + '">' +
                    '<div class="lab-sem-count">' + count + '</div>' +
                    '<div class="lab-sem-label">' + label + '</div>' +
                    '<div class="lab-sem-pct">' + (typeof pct === 'number' ? pct + '%' : pct) + '</div>' +
               '</div>';
    }

    // ══════════════════════════════════════════════════════════════
    // 6. INICIALIZACIÓN
    // ══════════════════════════════════════════════════════════════

    function init() {
        var datosDiv = document.getElementById('labDatosBeneficiario');
        if (!datosDiv) return; // No estamos en formulario de laboratorio

        var fechaNac  = datosDiv.dataset.fechaNacimiento || '';
        var sexo      = datosDiv.dataset.sexo || 'M';
        var baseUrl   = datosDiv.dataset.baseUrl || '/';
        var jornadaId = datosDiv.dataset.jornadaId || '';
        var centroId  = datosDiv.dataset.centroId || '';

        // Calcular edad en días
        var edadDias = 0;
        if (fechaNac) {
            var nacimiento = new Date(fechaNac);
            var hoy        = new Date();
            var diffMs     = hoy.getTime() - nacimiento.getTime();
            edadDias       = Math.floor(diffMs / (1000 * 60 * 60 * 24));
        }

        // Obtener valor de embarazada del formulario
        function getEmbarazada() {
            var sel = document.getElementById('campo_embarazada_lab');
            return sel ? sel.value : 'n';
        }

        // ─── Listeners para hemoglobina ───
        var campoHb = document.getElementById('campo_hemoglobina');
        if (campoHb) {
            campoHb.addEventListener('input', function () {
                var result = clasificarAnemia(this.value, edadDias, sexo, getEmbarazada());
                renderBadge('campo_hemoglobina', result);
            });
            // Evaluar valor precargado (edición)
            if (campoHb.value !== '') {
                var resultInicial = clasificarAnemia(campoHb.value, edadDias, sexo, getEmbarazada());
                renderBadge('campo_hemoglobina', resultInicial);
            }
        }

        // ─── Listener para cambio de embarazada (re-evalúa hemoglobina) ───
        var campoEmb = document.getElementById('campo_embarazada_lab');
        if (campoEmb) {
            campoEmb.addEventListener('change', function () {
                if (campoHb && campoHb.value !== '') {
                    var r = clasificarAnemia(campoHb.value, edadDias, sexo, getEmbarazada());
                    renderBadge('campo_hemoglobina', r);
                }
            });
        }

        // ─── Listeners para riesgo cardiometabólico ───
        var camposTri = document.getElementById('campo_trigliceridos');
        if (camposTri) {
            camposTri.addEventListener('input', function () {
                var r = clasificarTrigliceridos(this.value);
                renderBadge('campo_trigliceridos', r);
            });
            if (camposTri.value !== '') {
                renderBadge('campo_trigliceridos', clasificarTrigliceridos(camposTri.value));
            }
        }

        var campoHdl = document.getElementById('campo_hdl_colesterol');
        if (campoHdl) {
            campoHdl.addEventListener('input', function () {
                renderBadge('campo_hdl_colesterol', clasificarHDL(this.value, sexo));
            });
            if (campoHdl.value !== '') {
                renderBadge('campo_hdl_colesterol', clasificarHDL(campoHdl.value, sexo));
            }
        }

        var campoVldl = document.getElementById('campo_vldl_colesterol');
        if (campoVldl) {
            campoVldl.addEventListener('input', function () {
                renderBadge('campo_vldl_colesterol', clasificarVLDL(this.value));
            });
            if (campoVldl.value !== '') {
                renderBadge('campo_vldl_colesterol', clasificarVLDL(campoVldl.value));
            }
        }

        var campoLdl = document.getElementById('campo_ldl_colesterol');
        if (campoLdl) {
            campoLdl.addEventListener('input', function () {
                renderBadge('campo_ldl_colesterol', clasificarLDL(this.value));
            });
            if (campoLdl.value !== '') {
                renderBadge('campo_ldl_colesterol', clasificarLDL(campoLdl.value));
            }
        }

        // ─── Cargar semáforo ───
        cargarSemaforo(baseUrl, jornadaId, centroId);

        // ─── Interceptor de guardado ───
        _instalarInterceptor(edadDias, sexo, getEmbarazada);
    }

    // ══════════════════════════════════════════════════════════════
    // 7. INTERCEPTOR PRE-GUARDADO
    // ══════════════════════════════════════════════════════════════

    function _instalarInterceptor(edadDias, sexo, getEmbarazada) {
        // Guardar referencia a la función original
        if (typeof window.guardarEvaluacion !== 'function') return;

        var _guardarOriginal = window.guardarEvaluacion;

        window.guardarEvaluacion = function () {
            var advertencias = validarPreGuardado(edadDias, sexo, getEmbarazada());

            // Sin advertencias → ejecutar guardado normal
            if (advertencias.length === 0) {
                _guardarOriginal();
                return;
            }

            // Separar errores de advertencias/info
            var errores = advertencias.filter(function(a) { return a.tipo === 'error'; });
            var warns   = advertencias.filter(function(a) { return a.tipo !== 'error'; });

            // Si hay errores graves → bloquear
            if (errores.length > 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'No se puede guardar',
                    html: errores.map(function(e) { return e.mensaje; }).join('<br><br>'),
                    confirmButtonColor: '#101a61'
                });
                return;
            }

            // Advertencias → pedir confirmación
            var htmlWarns = warns.map(function(w) {
                var icono = w.tipo === 'warning' ? '⚠️' : 'ℹ️';
                return '<div style="text-align:left;margin-bottom:8px;font-size:.9rem;">' +
                       icono + ' ' + w.mensaje.replace(/\n/g, '<br>') +
                       '</div>';
            }).join('');

            Swal.fire({
                icon: 'warning',
                title: 'Advertencias detectadas',
                html: htmlWarns,
                showCancelButton: true,
                confirmButtonText: 'Guardar de todas formas',
                cancelButtonText: 'Revisar valores',
                confirmButtonColor: '#101a61',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then(function(result) {
                if (result.isConfirmed) {
                    _guardarOriginal();
                }
            });
        };
    }

    // ══════════════════════════════════════════════════════════════
    // HELPERS
    // ══════════════════════════════════════════════════════════════

    function _resultado(clave, rango, mapa) {
        var cfg = mapa[clave] || mapa.revisar;
        return {
            clave:  clave,
            texto:  cfg.texto,
            rango:  rango,
            clase:  cfg.clase,
            icono:  cfg.icono
        };
    }

    // ══════════════════════════════════════════════════════════════
    // API PÚBLICA
    // ══════════════════════════════════════════════════════════════

    return {
        init:                    init,
        clasificarAnemia:        clasificarAnemia,
        clasificarTrigliceridos: clasificarTrigliceridos,
        clasificarHDL:           clasificarHDL,
        clasificarVLDL:          clasificarVLDL,
        clasificarLDL:           clasificarLDL,
        validarPreGuardado:      validarPreGuardado,
        cargarSemaforo:          cargarSemaforo
    };

})();

// Auto-inicializar al cargar el DOM
document.addEventListener('DOMContentLoaded', function () {
    LabInterpretacion.init();
});
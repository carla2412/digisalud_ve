/**
 * DigiSalud — Interpretación clínica para Laboratorio (pesquisa 2)
 * Archivo: public/js/lab-interpretacion.js — v1.3
 *
 * Corregido v1.3:
 *   - Eliminado semáforo AJAX (no aplica — es clasificación INDIVIDUAL)
 *   - Widget "Estado clínico" en panel derecho se actualiza en tiempo real
 *   - Badges debajo de inputs + indicadores en panel derecho
 *   - Todo client-side, sin endpoints server-side
 *   - Clases CSS: lab-interp-badge-* (sin colisión con .lab-badge)
 *   - renderBadge busca .field como wrapper (wizard layout)
 *   - Todo en try/catch para nunca romper el wizard
 */

var LabInterpretacion = (function () {
    'use strict';

    // ═══ Configuración de clases CSS ═══

    var CLASES = {
        normal:   { clase: 'lab-interp-badge lab-interp-badge-normal',   icono: 'bi-check-circle',         texto: 'Normal' },
        leve:     { clase: 'lab-interp-badge lab-interp-badge-leve',     icono: 'bi-exclamation-triangle', texto: 'Anemia leve' },
        moderada: { clase: 'lab-interp-badge lab-interp-badge-moderada', icono: 'bi-exclamation-triangle', texto: 'Anemia moderada' },
        severa:   { clase: 'lab-interp-badge lab-interp-badge-severa',   icono: 'bi-exclamation-circle',   texto: 'Anemia severa' },
        revisar:  { clase: 'lab-interp-badge lab-interp-badge-revisar',  icono: 'bi-question-circle',      texto: 'Revisar datos' },
        sin_dato: { clase: 'lab-interp-badge lab-interp-badge-revisar',  icono: 'bi-dash-circle',          texto: 'Sin registrar' }
    };

    var CLASES_CARDIO = {
        normal:       { clase: 'lab-interp-badge lab-interp-badge-normal',   icono: 'bi-check-circle',         texto: 'Normal' },
        limite_alto:  { clase: 'lab-interp-badge lab-interp-badge-leve',     icono: 'bi-exclamation-triangle', texto: 'Límite alto' },
        alto:         { clase: 'lab-interp-badge lab-interp-badge-moderada', icono: 'bi-exclamation-triangle', texto: 'Alto' },
        muy_alto:     { clase: 'lab-interp-badge lab-interp-badge-severa',   icono: 'bi-exclamation-circle',   texto: 'Muy alto' },
        riesgo:       { clase: 'lab-interp-badge lab-interp-badge-moderada', icono: 'bi-heart-pulse',          texto: 'Riesgo cardiometabólico' }
    };

    // Mapa de clave → clase CSS del indicador en panel derecho
    var IND_CLASES = {
        normal: 'lab-ind-normal', leve: 'lab-ind-leve', moderada: 'lab-ind-moderada',
        severa: 'lab-ind-severa', revisar: 'lab-ind-revisar', sin_dato: 'lab-ind-pending',
        limite_alto: 'lab-ind-leve', alto: 'lab-ind-moderada', muy_alto: 'lab-ind-severa',
        riesgo: 'lab-ind-moderada', pending: 'lab-ind-pending'
    };

    // ═══ 1. CLASIFICACIÓN DE ANEMIA ═══

    function clasificarAnemia(hb, edadDias, sexo, embarazada) {
        if (hb === null || hb === undefined || hb === '' || isNaN(hb)) return _r('sin_dato', '', CLASES);
        hb = parseFloat(hb);
        if (edadDias <= 0) return _r('revisar', 'Edad inválida', CLASES);
        if (hb > 20 || (hb > 0 && hb < 3)) return _r('revisar', 'Hb fuera de rango válido', CLASES);

        if (edadDias >= 183 && edadDias <= 1825) {
            if (hb >= 11.0 && hb <= 20)   return _r('normal',   '11.0–20',   CLASES);
            if (hb >= 10.0 && hb <= 10.9) return _r('leve',     '10.0–10.9', CLASES);
            if (hb >= 7.0  && hb <= 9.9)  return _r('moderada', '7.0–9.9',   CLASES);
            if (hb >= 3.0  && hb < 7.0)   return _r('severa',   '3.0–<7.0',  CLASES);
            return _r('revisar', '', CLASES);
        }
        if (edadDias >= 1826 && edadDias <= 4382) {
            if (hb >= 11.5 && hb <= 20)   return _r('normal',   '11.5–20',   CLASES);
            if (hb >= 11.0 && hb <= 11.4) return _r('leve',     '11.0–11.4', CLASES);
            if (hb >= 8.0  && hb <= 10.9) return _r('moderada', '8.0–10.9',  CLASES);
            if (hb >= 3.0  && hb < 8.0)   return _r('severa',   '3.0–<8.0',  CLASES);
            return _r('revisar', '', CLASES);
        }
        if (edadDias >= 4383 && edadDias <= 5478) {
            if (hb >= 12.0 && hb <= 20)   return _r('normal',   '12.0–20',   CLASES);
            if (hb >= 11.0 && hb <= 11.9) return _r('leve',     '11.0–11.9', CLASES);
            if (hb >= 8.0  && hb <= 10.9) return _r('moderada', '8.0–10.9',  CLASES);
            if (hb >= 3.0  && hb < 8.0)   return _r('severa',   '3.0–<8.0',  CLASES);
            return _r('revisar', '', CLASES);
        }
        if (edadDias >= 5479) {
            if (sexo === 'M') {
                if (hb >= 13.0 && hb <= 20)   return _r('normal',   '13.0–20',   CLASES);
                if (hb >= 11.0 && hb <= 12.9) return _r('leve',     '11.0–12.9', CLASES);
                if (hb >= 8.0  && hb <= 10.9) return _r('moderada', '8.0–10.9',  CLASES);
                if (hb >= 3.0  && hb < 8.0)   return _r('severa',   '3.0–<8.0',  CLASES);
                return _r('revisar', '', CLASES);
            }
            if (sexo === 'F' && embarazada === 's') {
                if (hb >= 11.0 && hb <= 20)   return _r('normal',   '11.0–20',   CLASES);
                if (hb >= 10.0 && hb <= 10.9) return _r('leve',     '10.0–10.9', CLASES);
                if (hb >= 7.0  && hb <= 9.9)  return _r('moderada', '7.0–9.9',   CLASES);
                if (hb >= 3.0  && hb < 7.0)   return _r('severa',   '3.0–<7.0',  CLASES);
                return _r('revisar', '', CLASES);
            }
            if (hb >= 12.0 && hb <= 20)   return _r('normal',   '12.0–20',   CLASES);
            if (hb >= 11.0 && hb <= 11.9) return _r('leve',     '11.0–11.9', CLASES);
            if (hb >= 8.0  && hb <= 10.9) return _r('moderada', '8.0–10.9',  CLASES);
            if (hb >= 3.0  && hb < 8.0)   return _r('severa',   '3.0–<8.0',  CLASES);
            return _r('revisar', '', CLASES);
        }
        return _r('revisar', 'Menor de 6 meses', CLASES);
    }

    // ═══ 2. RIESGO CARDIOMETABÓLICO ═══

    function clasificarTrigliceridos(v) {
        if (!v && v !== 0) return null; v = parseFloat(v); if (isNaN(v)) return null;
        if (v < 150) return _r('normal', '<150 mg/dL', CLASES_CARDIO);
        if (v <= 199) return _r('limite_alto', '150–199 mg/dL', CLASES_CARDIO);
        if (v <= 499) return _r('alto', '200–499 mg/dL', CLASES_CARDIO);
        return _r('muy_alto', '≥500 mg/dL', CLASES_CARDIO);
    }
    function clasificarHDL(v, sexo) {
        if (!v && v !== 0) return null; v = parseFloat(v); if (isNaN(v)) return null;
        if (sexo === 'M') return v >= 40 ? _r('normal', '≥40', CLASES_CARDIO) : _r('riesgo', '<40', CLASES_CARDIO);
        return v >= 50 ? _r('normal', '≥50', CLASES_CARDIO) : _r('riesgo', '<50', CLASES_CARDIO);
    }
    function clasificarVLDL(v) {
        if (!v && v !== 0) return null; v = parseFloat(v); if (isNaN(v)) return null;
        return v < 30 ? _r('normal', '<30', CLASES_CARDIO) : _r('riesgo', '≥30', CLASES_CARDIO);
    }
    function clasificarLDL(v) {
        if (!v && v !== 0) return null; v = parseFloat(v); if (isNaN(v)) return null;
        return v < 100 ? _r('normal', '<100', CLASES_CARDIO) : _r('riesgo', '≥100', CLASES_CARDIO);
    }

    // ═══ 3. VALIDACIÓN PRE-GUARDADO ═══

    function validarPreGuardado(edadDias, sexo, embarazada) {
        var warns = [];
        var hbInput = document.getElementById('campo_hemoglobina');
        var hb = hbInput ? parseFloat(hbInput.value) : NaN;

        if (edadDias <= 0) {
            warns.push({ tipo: 'error', mensaje: 'No se puede calcular interpretación de anemia: edad inválida. Verifique la fecha de nacimiento.' });
        }
        if (!isNaN(hb) && (hb > 20 || (hb > 0 && hb < 3))) {
            warns.push({ tipo: 'warning', mensaje: 'Hemoglobina (' + hb.toFixed(1) + ' g/dL) en rango "Revisar datos". ¿Desea guardar?' });
        }
        if (hbInput && (hbInput.value === '' || hbInput.value === null)) {
            warns.push({ tipo: 'info', mensaje: 'No se registró hemoglobina. La clasificación quedará como "Sin registrar".' });
        }

        var riesgos = [];
        [
            { id: 'campo_trigliceridos', nombre: 'Triglicéridos', fn: clasificarTrigliceridos },
            { id: 'campo_hdl_colesterol', nombre: 'HDL', fn: function(v) { return clasificarHDL(v, sexo); } },
            { id: 'campo_vldl_colesterol', nombre: 'VLDL', fn: clasificarVLDL },
            { id: 'campo_ldl_colesterol', nombre: 'LDL', fn: clasificarLDL }
        ].forEach(function(c) {
            var el = document.getElementById(c.id);
            if (!el || el.value === '') return;
            var res = c.fn(el.value);
            if (res && res.clave !== 'normal') riesgos.push(c.nombre + ': ' + res.texto);
        });

        if (riesgos.length > 0) {
            warns.push({ tipo: 'warning', mensaje: riesgos.length + ' riesgo(s) cardiometabólico(s):\n• ' + riesgos.join('\n• ') });
        }
        return warns;
    }

    // ═══ 4. RENDERIZADO DE BADGES (debajo de inputs) ═══

    function renderBadge(inputId, resultado) {
        var input = document.getElementById(inputId);
        if (!input) return;
        var wrap = input.closest('.field');
        if (!wrap) return;

        var anterior = wrap.querySelector('.lab-interp-badge');
        if (anterior) anterior.remove();
        if (!resultado) return;

        var badge = document.createElement('div');
        badge.className = resultado.clase;
        badge.innerHTML = '<i class="bi ' + resultado.icono + '"></i> ' +
            resultado.texto +
            (resultado.rango ? ' <span class="lab-interp-badge-rango">(' + resultado.rango + ')</span>' : '');
        wrap.appendChild(badge);
    }

    // ═══ 5. ACTUALIZAR INDICADOR EN PANEL DERECHO ═══

    function actualizarIndicador(elementId, resultado, textoDefault) {
        var el = document.getElementById(elementId);
        if (!el) return;

        if (!resultado) {
            el.className = 'lab-indicador lab-ind-pending';
            el.style.display = 'none';
            return;
        }

        // Mostrar el indicador
        el.style.display = 'flex';

        // Asignar clase de color
        var claseColor = IND_CLASES[resultado.clave] || 'lab-ind-pending';
        el.className = 'lab-indicador ' + claseColor;

        // Actualizar texto
        var valorEl = el.querySelector('.lab-indicador-valor');
        if (valorEl) {
            var texto = resultado.texto;
            if (resultado.rango) texto += ' (' + resultado.rango + ')';
            valorEl.textContent = texto;
        }
    }

    // ═══ 6. INIT ═══

    function init() {
        try {
            var datosDiv = document.getElementById('labDatosBeneficiario');
            if (!datosDiv) return;

            var fechaNac = datosDiv.dataset.fechaNacimiento || '';
            var sexo     = datosDiv.dataset.sexo || 'M';

            var edadDias = 0;
            if (fechaNac) {
                var nac = new Date(fechaNac + 'T00:00:00');
                var hoy = new Date(); hoy.setHours(0,0,0,0);
                edadDias = Math.floor((hoy.getTime() - nac.getTime()) / 86400000);
            }

            function getEmb() {
                var sel = document.getElementById('campo_embarazada_lab');
                return sel ? sel.value : 'n';
            }

            // ─── Hemoglobina → badge + indicador panel ───
            _bindCampo('campo_hemoglobina', function(val) {
                var res = clasificarAnemia(val, edadDias, sexo, getEmb());
                actualizarIndicador('labIndicadorAnemia', res);
                return res;
            });

            // Re-evaluar hemoglobina al cambiar embarazada
            var campoEmb = document.getElementById('campo_embarazada_lab');
            if (campoEmb) {
                campoEmb.addEventListener('change', function() {
                    var hb = document.getElementById('campo_hemoglobina');
                    if (hb && hb.value !== '') {
                        var res = clasificarAnemia(hb.value, edadDias, sexo, getEmb());
                        renderBadge('campo_hemoglobina', res);
                        actualizarIndicador('labIndicadorAnemia', res);
                    }
                });
            }

            // ─── Cardiometabólicos → badge + indicador panel ───
            _bindCampo('campo_trigliceridos', function(val) {
                var res = clasificarTrigliceridos(val);
                actualizarIndicador('labIndicadorTri', res);
                return res;
            });
            _bindCampo('campo_hdl_colesterol', function(val) {
                var res = clasificarHDL(val, sexo);
                actualizarIndicador('labIndicadorHdl', res);
                return res;
            });
            _bindCampo('campo_ldl_colesterol', function(val) {
                var res = clasificarLDL(val);
                actualizarIndicador('labIndicadorLdl', res);
                return res;
            });
            _bindCampo('campo_vldl_colesterol', function(val) {
                var res = clasificarVLDL(val);
                actualizarIndicador('labIndicadorVldl', res);
                return res;
            });

            // ─── Interceptor de guardado ───
            try { _instalarInterceptor(edadDias, sexo, getEmb); } catch(e) { console.warn('[Lab]', e); }

        } catch (error) {
            console.error('[LabInterpretacion] Error en init:', error);
        }
    }

    function _bindCampo(campoId, fnClasificar) {
        var campo = document.getElementById(campoId);
        if (!campo) return;
        campo.addEventListener('input', function() { renderBadge(campoId, fnClasificar(this.value)); });
        if (campo.value !== '' && campo.value !== null) {
            renderBadge(campoId, fnClasificar(campo.value));
        }
    }

    // ═══ 7. INTERCEPTOR PRE-GUARDADO ═══

    function _instalarInterceptor(edadDias, sexo, getEmb) {
        if (typeof window.guardarEvaluacion !== 'function') return;
        if (window._labInterceptorInstalado) return;
        window._labInterceptorInstalado = true;

        var _original = window.guardarEvaluacion;

        window.guardarEvaluacion = function() {
            try {
                var advertencias = validarPreGuardado(edadDias, sexo, getEmb());
                if (advertencias.length === 0) return _original();

                var errores = advertencias.filter(function(a) { return a.tipo === 'error'; });
                var warns = advertencias.filter(function(a) { return a.tipo !== 'error'; });

                if (errores.length > 0) {
                    Swal.fire({ icon: 'error', title: 'No se puede guardar', html: errores.map(function(e) { return e.mensaje; }).join('<br><br>'), confirmButtonColor: '#101a61' });
                    return;
                }

                Swal.fire({
                    icon: 'warning', title: 'Advertencias detectadas',
                    html: warns.map(function(w) {
                        return '<div style="text-align:left;margin-bottom:8px;font-size:.9rem;">' +
                            (w.tipo === 'warning' ? '⚠️' : 'ℹ️') + ' ' + w.mensaje.replace(/\n/g, '<br>') + '</div>';
                    }).join(''),
                    showCancelButton: true, confirmButtonText: 'Guardar de todas formas',
                    cancelButtonText: 'Revisar valores', confirmButtonColor: '#101a61',
                    cancelButtonColor: '#6c757d', reverseButtons: true
                }).then(function(result) { if (result.isConfirmed) _original(); });

            } catch (error) {
                console.error('[Lab] Interceptor falló, guardando normal:', error);
                _original();
            }
        };
    }

    // ═══ HELPERS ═══

    function _r(clave, rango, mapa) {
        var cfg = mapa[clave] || mapa.revisar;
        return { clave: clave, texto: cfg.texto, rango: rango, clase: cfg.clase, icono: cfg.icono };
    }

    return {
        init: init, clasificarAnemia: clasificarAnemia,
        clasificarTrigliceridos: clasificarTrigliceridos, clasificarHDL: clasificarHDL,
        clasificarVLDL: clasificarVLDL, clasificarLDL: clasificarLDL,
        validarPreGuardado: validarPreGuardado
    };
})();

document.addEventListener('DOMContentLoaded', function() {
    try { LabInterpretacion.init(); } catch(e) { console.error('[Lab] Fatal:', e); }
});
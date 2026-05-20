(function () {
  'use strict';

  const $ = (s, ctx = document) => ctx.querySelector(s);
  const $$ = (s, ctx = document) => Array.from(ctx.querySelectorAll(s));
  const toNum = (v) => {
    if (v === null || v === undefined || v === '') return null;
    const n = parseFloat(String(v).replace(',', '.'));
    return Number.isFinite(n) ? n : null;
  };
  const fmt = (n, d = 2) => Number.isFinite(n) ? n.toFixed(d) : '';
  const setVal = (id, value) => { const el = $('#' + id); if (el) el.value = value ?? ''; };
  const getVal = (id) => { const el = $('#' + id); return el ? el.value : ''; };
  const setText = (id, value) => { const el = $('#' + id); if (el) el.textContent = value || '—'; };

  const state = { step: 0, data: {}, manifest: {}, edadDias: 0, edadMeses: 0, sexo: 'M' };

  document.addEventListener('DOMContentLoaded', init);

  async function init() {
    state.sexo = ($('#antroSexo')?.value || 'M').toUpperCase() === 'F' ? 'F' : 'M';
    state.manifest = parseManifest();
    calcEdad();
    toggleByEdadSexo();
    await loadData();
    bindUI();
    recalcAll();
    updateProgress();
  }

  function parseManifest() {
    try { return JSON.parse($('#antroJsonManifest')?.value || '{}'); } catch (e) { return {}; }
  }

  async function loadData() {
    const entries = Object.entries(state.manifest || {});
    await Promise.all(entries.map(async ([key, url]) => {
      try {
        const res = await fetch(url, { cache: 'force-cache' });
        state.data[key] = res.ok ? await res.json() : [];
      } catch (e) {
        state.data[key] = [];
      }
    }));
  }

  function bindUI() {
    $$('.antro-step').forEach(btn => btn.addEventListener('click', () => showStep(parseInt(btn.dataset.step, 10))));
    $('#btnPrev')?.addEventListener('click', () => showStep(Math.max(0, state.step - 1)));
    $('#btnNext')?.addEventListener('click', () => showStep(Math.min(3, state.step + 1)));
    $$('input, select, textarea').forEach(el => el.addEventListener('input', recalcAll));
    $$('input[type=radio]').forEach(el => el.addEventListener('change', () => { toggleByEdadSexo(); recalcAll(); }));

    $('#btnPesoDiferencia')?.addEventListener('click', () => openModal('modalPesoDiferencia'));
    $('#btnZscore')?.addEventListener('click', () => { fillZscoreModal(); openModal('modalZscore'); });
    $$('[data-close-modal]').forEach(btn => btn.addEventListener('click', () => btn.closest('.antro-modal')?.classList.remove('show')));
    $('#pesoCargador')?.addEventListener('input', calcPesoDiferencia);
    $('#pesoAmbos')?.addEventListener('input', calcPesoDiferencia);
    $('#guardarPesoCalculado')?.addEventListener('click', () => {
      const p = toNum(getVal('pesoCalculado'));
      if (!p) return showError('Debe calcular un peso válido.');
      $('#peso').value = fmt(p, 1);
      $('#modalPesoDiferencia')?.classList.remove('show');
      recalcAll();
    });

    $('#formAntropometria')?.addEventListener('submit', submitForm);
  }

  function openModal(id) { $('#' + id)?.classList.add('show'); }

  function showStep(n) {
    state.step = n;
    $$('.antro-section').forEach(sec => sec.classList.toggle('active', parseInt(sec.dataset.section, 10) === n));
    $$('.antro-step').forEach(btn => btn.classList.toggle('active', parseInt(btn.dataset.step, 10) === n));
    updateProgress();
  }

  function updateProgress() {
    const required = ['peso', 'talla', 'fecha_evaluacion'];
    let done = required.filter(id => getVal(id)).length;
    const pct = Math.round((done / required.length) * 100);
    setText('antroProgressText', `${done} / ${required.length} básicos`);
    const fill = $('#antroProgressFill');
    if (fill) fill.style.width = pct + '%';
    $$('.antro-step').forEach((btn, idx) => btn.classList.toggle('done', idx < state.step));
  }

  function calcEdad() {
    const nac = $('#antroFechaNacimiento')?.value;
    const fechaEval = $('#fecha_evaluacion')?.value || new Date().toISOString().slice(0, 10);
    if (!nac) return;
    const a = new Date(nac + 'T00:00:00');
    const b = new Date(fechaEval + 'T00:00:00');
    const diff = Math.floor((b - a) / 86400000);
    state.edadDias = Math.max(0, diff);
    state.edadMeses = Math.floor(state.edadDias / 30.4375);
    setVal('edad_dias_medicion', state.edadDias);
    setVal('edad_meses_medicion', state.edadMeses);
  }

  function toggleByEdadSexo() {
    calcEdad();
    const menor5 = state.edadDias < 1826;
    $$('.antro-menor5').forEach(el => el.classList.toggle('antro-hidden', !menor5));
    const mujerAdulta = state.sexo === 'F' && state.edadDias >= 6939;
    $$('.antro-mujer').forEach(el => el.classList.toggle('antro-hidden', !mujerAdulta));
    const embarazada = document.querySelector('input[name="campos[embarazada]"]:checked')?.value === '1' && mujerAdulta;
    $$('.antro-embarazo').forEach(el => el.classList.toggle('antro-hidden', !embarazada));
    const discapacidad = document.querySelector('input[name="campos[discapacidad]"]:checked')?.value === '1';
    $$('.antro-discapacidad').forEach(el => el.classList.toggle('antro-hidden', !discapacidad));
  }

  function recalcAll() {
    calcEdad();
    toggleByEdadSexo();
    const peso = toNum(getVal('peso_ajustado')) || toNum(getVal('peso'));
    const talla = toNum(getVal('talla_estimada')) || toNum(getVal('talla'));
    const edema = document.querySelector('input[name="campos[edema]"]:checked')?.value === '1';

    if (peso && talla) {
      const imc = peso / Math.pow(talla / 100, 2);
      setVal('imc', fmt(imc, 2));
      setVal('imcVista', fmt(imc, 2));
      setText('sumImc', fmt(imc, 2));
    }

    setText('sumPeso', peso ? `${fmt(peso, 1)} kg` : '—');
    setText('sumTalla', talla ? `${fmt(talla, 1)} cm` : '—');
    setGrupoEdadReporte();

    if (!edema && peso && talla) {
      calcZScores(peso, talla);
    } else {
      clearZScores();
      if (edema) setVal('estado_nutricional_agregado', 'Revisión clínica por edema');
    }

    calcEmbarazo(peso, talla);
    classifyAggregated();
    updateSummary();
    updateProgress();
  }

  function setGrupoEdadReporte() {
    let grupo = '';
    if (state.edadDias < 1826) grupo = '< de 5 años';
    else if (state.edadDias <= 6939) grupo = '5 a 19 años';
    else grupo = '> 19 años';
    setVal('grupo_edad_reporte', grupo);
    setVal('grupoEdadVista', grupo);
  }

  function calcZScores(peso, talla) {
    const imc = toNum(getVal('imc'));
    const cc = toNum(getVal('circ_cefalica'));
    const cbi = toNum(getVal('circ_brazo_izq'));
    const pt = toNum(getVal('pliegue_tricipital'));
    const ps = toNum(getVal('pliegue_subescapular'));

    const zpe = state.edadDias <= 3653 ? zEdadPeso(peso) : null;
    setResult('zpe', zpe);
    const zte = state.edadDias <= 6939 ? zEdadTalla(talla) : null;
    setResult('zte', zte);
    const zimce = state.edadDias <= 6939 && imc ? zEdadImc(imc) : null;
    setResult('zimce', zimce);
    const zpt = state.edadDias <= 1856 ? zPesoTalla(peso, talla) : null;
    setResult('zpt', zpt);
    const zcc = state.edadDias <= 1856 && cc ? zGenericByDias('zcc_dias', cc, ['ccdias_indicador_genero','ccdias_indicador_denominador','ccdias_sd0','ccdias_indicador_coeficiente_l','ccdias_indicador_coeficiente_s']) : null;
    setResult('zcc', zcc);
    const zcbi = state.edadDias >= 91 && state.edadDias <= 1856 && cbi ? zGenericByDias('zcbi_dias', cbi, ['cbidias_indicador_genero','cbidias_indicador_denominador','cbidias_sd0','cbidias_indicador_coeficiente_l','cbidias_indicador_coeficiente_s']) : null;
    setResult('zcbi', zcbi);
    const zptri = state.edadDias >= 91 && state.edadDias <= 1856 && pt ? zGenericByDias('ztricipital_dias', pt, ['pt_indicador_genero','pt_indicador_denominador','pt_sd0','pt_indicador_coeficiente_l','pt_indicador_coeficiente_s']) : null;
    setResult('zptri', zptri);
    const zpsub = state.edadDias >= 91 && state.edadDias <= 1856 && ps ? zGenericByDias('zsubescapular_dias', ps, ['ps_indicador_genero','ps_indicador_denominador','ps_sd0','ps_indicador_coeficiente_l','ps_indicador_coeficiente_s']) : null;
    setResult('zpsub', zpsub);
  }

  function setResult(code, z) {
    if (z === null || z === undefined || !Number.isFinite(z)) {
      setVal(code, ''); setVal(code + '_percentil', ''); return;
    }
    setVal(code, fmt(z, 2));
    setVal(code + '_percentil', zToPercentile(z));
  }

  function clearZScores() {
    ['zpe','zte','zpt','zimce','zcc','zcbi','zptri','zpsub'].forEach(c => setResult(c, null));
  }

  function calcZ(valor, media, L, S) {
    valor = toNum(valor); media = toNum(media); L = toNum(L); S = toNum(S);
    if (!valor || !media || S === 0 || L === null) return null;
    let z = (Math.pow(valor / media, L) - 1) / (L * S);
    if (z < -3 || z > 3) z = valoresExtremos(z, valor, media, L, S);
    return z;
  }

  function valoresExtremos(z, valor, media, L, S) {
    const exp = 1 / L;
    if (z < -3) {
      const sd2 = media * Math.pow(1 + L * S * -2, exp);
      const sd3 = media * Math.pow(1 + L * S * -3, exp);
      return -3 + ((valor - sd3) / (sd2 - sd3));
    }
    if (z > 3) {
      const sd2 = media * Math.pow(1 + L * S * 2, exp);
      const sd3 = media * Math.pow(1 + L * S * 3, exp);
      return 3 + ((valor - sd3) / (sd3 - sd2));
    }
    return z;
  }

  function zEdadPeso(peso) {
    if (state.edadDias <= 1856) {
      return zGenericByDias('zpe_dias', peso, ['pdias_indicador_genero','pdias_indicador_denominador','pdias_sd0_mediana','pdias_indicador_coeficiente_l','pdias_indicador_coeficiente_s']);
    }
    return zGenericByMeses('zpe_meses', peso, ['p_indicador_genero','p_indicador_denominador','p_indicador_coeficiente_m','p_indicador_coeficiente_l','p_indicador_coeficiente_s']);
  }

  function zEdadTalla(talla) {
    if (state.edadDias <= 1856) {
      return zGenericByDias('zte_dias', talla, ['tdias_indicador_genero','tdias_indicador_denominador','tdias_sd0_mediana','tdias_indicador_coeficiente_l','tdias_indicador_coeficiente_s']);
    }
    const key = state.sexo === 'F' ? 'zte_meses_f' : 'zte_meses_m';
    return zGenericByMeses(key, talla, ['t_indicador_genero','t_indicador_denominador','t_indicador_coeficiente_m','t_indicador_coeficiente_l','t_indicador_coeficiente_s']);
  }

  function zEdadImc(imc) {
    if (state.edadDias <= 1856) {
      return zGenericByDias('zimce_dias', imc, ['idias_indicador_genero','idias_indicador_denominador','idias_sd0_mediana','idias_indicador_coeficiente_l','idias_indicador_coeficiente_s']);
    }
    return zGenericByMeses('zimce_meses', imc, ['i_indicador_genero','i_indicador_denominador','i_indicador_coeficiente_m','i_indicador_coeficiente_l','i_indicador_coeficiente_s']);
  }

  function zPesoTalla(peso, talla) {
    const key = talla >= 65 ? 'zpt_65_120' : 'zpt_45_110';
    const rows = state.data[key] || [];
    const denom = Math.round(talla * 10) / 10;
    let row = rows.find(r => norm(r.petadias_indicador_genero) === state.sexo && Math.abs(toNum(r.petadias_indicador_denominador) - denom) < 0.051);
    if (!row) row = nearest(rows.filter(r => norm(r.petadias_indicador_genero) === state.sexo), 'petadias_indicador_denominador', denom);
    return row ? calcZ(peso, row.petadias_sd0_mediana, row.petadias_indicador_coeficiente_l, row.petadias_indicador_coeficiente_s) : null;
  }

  function zGenericByDias(key, value, fields) {
    const [genderF, denomF, mediaF, lF, sF] = fields;
    const rows = state.data[key] || [];
    let row = rows.find(r => norm(r[genderF]) === state.sexo && parseInt(toNum(r[denomF]), 10) === state.edadDias);
    if (!row) row = nearest(rows.filter(r => norm(r[genderF]) === state.sexo), denomF, state.edadDias);
    return row ? calcZ(value, row[mediaF], row[lF], row[sF]) : null;
  }

  function zGenericByMeses(key, value, fields) {
    const [genderF, denomF, mediaF, lF, sF] = fields;
    const rows = (state.data[key] || []).filter(r => norm(r[genderF]) === state.sexo);
    const mes = Math.floor(state.edadDias / 30.4375);
    const frac = (state.edadDias / 30.4375) - mes;
    const r1 = rows.find(r => parseInt(toNum(r[denomF]), 10) === mes) || nearest(rows, denomF, mes);
    const r2 = rows.find(r => parseInt(toNum(r[denomF]), 10) === mes + 1) || r1;
    if (!r1) return null;
    const media = interp(toNum(r1[mediaF]), toNum(r2[mediaF]), frac);
    const L = interp(toNum(r1[lF]), toNum(r2[lF]), frac);
    const S = interp(toNum(r1[sF]), toNum(r2[sF]), frac);
    return calcZ(value, media, L, S);
  }

  function nearest(rows, field, target) {
    return rows.reduce((best, r) => {
      const v = toNum(r[field]);
      if (v === null) return best;
      if (!best) return r;
      return Math.abs(v - target) < Math.abs(toNum(best[field]) - target) ? r : best;
    }, null);
  }

  function interp(a, b, f) {
    if (a === null || b === null) return a ?? b;
    return a + ((b - a) * f);
  }

  function norm(v) { return String(v || '').trim().toUpperCase(); }

  function zToPercentile(z) {
    const p = normalCdf(z) * 100;
    if (!Number.isFinite(p)) return '';
    if (p < 0.01) return '<0.01';
    if (p > 99.99) return '>99.99';
    return p.toFixed(2);
  }

  function normalCdf(x) {
    return 0.5 * (1 + erf(x / Math.sqrt(2)));
  }

  function erf(x) {
    const sign = x >= 0 ? 1 : -1;
    x = Math.abs(x);
    const a1 = 0.254829592, a2 = -0.284496736, a3 = 1.421413741, a4 = -1.453152027, a5 = 1.061405429, p = 0.3275911;
    const t = 1 / (1 + p * x);
    const y = 1 - (((((a5 * t + a4) * t) + a3) * t + a2) * t + a1) * t * Math.exp(-x * x);
    return sign * y;
  }

  function classifyAggregated() {
    const zte = toNum(getVal('zte'));
    const zimc = toNum(getVal('zimce'));
    const zpt = toNum(getVal('zpt'));
    const ponderal = zpt ?? zimc;
    let estado = '';
    if (ponderal === null && zte === null) estado = '';
    else if (ponderal !== null && ponderal > 1) estado = 'malnutrición por exceso';
    else if (ponderal !== null && ponderal < -1 && zte !== null && zte < -2) estado = 'déficit agudo más crónico';
    else if (ponderal !== null && ponderal < -1 && (zte === null || zte >= -2)) estado = 'déficit agudo';
    else if (zte !== null && zte < -2 && ponderal !== null && ponderal >= -1 && ponderal <= 1) estado = 'déficit crónico';
    else estado = 'sin malnutrición agregada';
    setVal('estado_nutricional_agregado', estado);
    setVal('estadoAgregadoVista', estado);

    const clasePeso = ponderal === null ? '' : ponderal < -3 ? 'Delgadez severa' : ponderal < -2 ? 'Delgadez' : ponderal < -1 ? 'Riesgo de delgadez' : ponderal <= 1 ? 'Peso adecuado' : ponderal <= 2 ? 'Sobrepeso' : ponderal <= 3 ? 'Obesidad' : 'Obesidad severa';
    const claseTalla = zte === null ? '' : zte < -3 ? 'Talla muy baja' : zte < -2 ? 'Talla baja' : zte <= 2 ? 'Talla adecuada' : 'Talla alta';
    setVal('clasificacion_imc_talla', [clasePeso, claseTalla].filter(Boolean).join(' con '));
  }

  function calcEmbarazo(peso, talla) {
    const embarazada = document.querySelector('input[name="campos[embarazada]"]:checked')?.value === '1';
    if (!embarazada) return;
    let semanas = null;
    const fum = getVal('fum');
    if (fum) {
      const start = new Date(fum + 'T00:00:00');
      const evalDate = new Date((getVal('fecha_evaluacion') || new Date().toISOString().slice(0, 10)) + 'T00:00:00');
      semanas = Math.floor((evalDate - start) / 604800000);
    }
    const fechaEco = getVal('fechaEco');
    const semanasEco = toNum(getVal('semanasEco'));
    if (fechaEco && semanasEco !== null) {
      const eco = new Date(fechaEco + 'T00:00:00');
      const evalDate = new Date((getVal('fecha_evaluacion') || new Date().toISOString().slice(0, 10)) + 'T00:00:00');
      semanas = semanasEco + Math.floor((evalDate - eco) / 604800000);
    }
    if (semanas !== null) setVal('embarazo_semanas', Math.max(0, semanas));
    if (peso && talla) setVal('embarazo_imc_pregestacional', getVal('imc'));
  }

  function updateSummary() {
    setText('sumZimce', getVal('zimce'));
    setText('sumZte', getVal('zte'));
    setText('sumZpt', getVal('zpt'));
    setText('sumEstado', getVal('estado_nutricional_agregado'));
    const resumen = [
      `Grupo: ${getVal('grupo_edad_reporte') || '—'}`,
      `IMC: ${getVal('imc') || '—'}`,
      `Clasificación: ${getVal('clasificacion_imc_talla') || '—'}`,
      `Estado agregado: ${getVal('estado_nutricional_agregado') || '—'}`
    ].join('\n');
    setVal('resumenNutricional', resumen);
  }

  function fillZscoreModal() {
    const map = [
      ['P/L', 'zpt'], ['P/E', 'zpe'], ['L/E', 'zte'], ['IMC/E', 'zimce'],
      ['CC/E', 'zcc'], ['CBI/E', 'zcbi'], ['PT/E', 'zptri'], ['PS/E', 'zpsub']
    ];
    const html = map.map(([label, code]) => `<tr><td>${label}</td><td>${getVal(code + '_percentil') || '—'}</td><td>${getVal(code) || '—'}</td><td><button type="button" class="antro-btn antro-btn-soft" disabled>2da iteración</button></td></tr>`).join('');
    const tbody = $('#tablaZscore');
    if (tbody) tbody.innerHTML = html;
  }

  function calcPesoDiferencia() {
    const a = toNum(getVal('pesoCargador'));
    const b = toNum(getVal('pesoAmbos'));
    if (a === null || b === null) { setVal('pesoCalculado', ''); return; }
    if (b < a) { setVal('pesoCalculado', ''); showError('El peso de ambos no puede ser menor al peso del cargador.'); return; }
    setVal('pesoCalculado', fmt(b - a, 1));
  }

  async function submitForm(ev) {
    ev.preventDefault();
    hideError();
    if (!getVal('fecha_evaluacion')) return showError('La fecha de evaluación es obligatoria.');
    if (!getVal('peso') || !getVal('talla')) return showError('Peso y talla son obligatorios.');
    const form = ev.currentTarget;
    const btn = $('#btnGuardarAntro');
    if (btn) btn.disabled = true;
    try {
      const res = await fetch(form.action, { method: 'POST', body: new FormData(form), headers: { 'X-Requested-With': 'XMLHttpRequest' } });
      const json = await res.json();
      if (!json.ok) { showError(json.mensaje || 'No se pudo guardar.'); return; }
      if (json.url_retorno) window.location.href = json.url_retorno;
      else showError(json.mensaje || 'Evaluación guardada correctamente.');
    } catch (e) {
      showError('Error de conexión al guardar la evaluación.');
    } finally {
      if (btn) btn.disabled = false;
    }
  }

  function showError(msg) {
    const el = $('#antroError');
    if (!el) return;
    el.textContent = msg;
    el.classList.add('show');
  }

  function hideError() { $('#antroError')?.classList.remove('show'); }
})();

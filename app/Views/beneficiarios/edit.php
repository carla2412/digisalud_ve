<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>

<?= $this->endSection() ?>
<?= $this->section('content') ?>
<?php
$b   = $beneficiario ?? [];
$dir = $direccion ?? null;
$esc = $escolaridad ?? null;
$fam = $familiar ?? null;

$antClinico  = $antClinico ?? [];
$antSocio    = $antSocio ?? [];
$usaLentes   = $usaLentes ?? false;
$observacion = $observacion ?? '';
?>
<div class="container my-4">
    <div class="create-container">
        <div class="breadcrumb-digi"><a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt; <span class="active">Editar beneficiario</span></div>
        <h4 class="create-title">Editar perfil del beneficiario</h4>
        <p class="create-sub"><?= esc(strtoupper($b['apellidos'] . ', ' . $b['nombres'])) ?></p>
      



        <div class="id-preview">
            <div class="id-preview-label">ID Digisalud</div>
            <div class="id-preview-val" id="idPreview"><?= esc($b['id_digisalud']) ?></div>
        </div>

        <form method="post" action="<?= base_url("beneficiarios/actualizar/{$b['id_beneficiario']}") ?>" novalidate>
            <?= csrf_field() ?>

            <!-- DATOS PERSONALES -->
            <div class="mb-4">
                <div class="section-header"><i class="bi bi-person"></i> Datos personales</div>
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Nombres *</label><input type="text" name="nombres" id="fNombres" class="form-control" required value="<?= esc($b['nombres']) ?>" oninput="actualizarIdPreview()"></div>
                    <div class="col-md-6"><label class="form-label">Apellidos *</label><input type="text" name="apellidos" id="fApellidos" class="form-control" required value="<?= esc($b['apellidos']) ?>" oninput="actualizarIdPreview()"></div>
                    <div class="col-md-4"><label class="form-label">Fecha de nacimiento *</label><input type="date" name="fecha_nacimiento" id="fFecha" class="form-control" required value="<?= esc($b['fecha_nacimiento']) ?>" oninput="actualizarIdPreview()"></div>
                    <div class="col-md-4"><label class="form-label">Sexo *</label><select name="sexo" id="fSexo" class="form-select" required onchange="actualizarIdPreview()">
                            <option value="">Seleccionar...</option>
                            <option value="M" <?= $b['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                            <option value="F" <?= $b['sexo'] == 'F' ? 'selected' : '' ?>>Femenino</option>
                        </select></div>
                    <div class="col-md-4"><label class="form-label">País de nacimiento *</label><select name="pais_nacimiento" id="fPais" class="form-select" required onchange="actualizarIdPreview()"><?php foreach (['Venezuela', 'Colombia', 'Peru', 'Ecuador', 'Brasil', 'El Salvador', 'Otro'] as $p): ?><option value="<?= $p ?>" <?= ($b['pais_nacimiento'] ?? '') == $p ? 'selected' : '' ?>><?= $p ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" value="<?= esc($b['telefono'] ?? '') ?>"></div>
                    <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" value="<?= esc($b['correo'] ?? '') ?>"></div>
                </div>
            </div>

            <!-- DIRECCIÓN -->
            <div class="toggle-bar <?= $dir ? 'active' : '' ?>" onclick="toggleSeccion(this,'secDireccion')"><i class="bi bi-geo-alt" style="font-size:1.1rem;color:#101a61;"></i>
                <div>
                    <div class="toggle-label">Dirección de residencia</div>
                    <div class="toggle-desc"><?= $dir ? esc(($dir['estado'] ?? '') . ' - ' . ($dir['ciudad'] ?? '')) : 'Sin dirección' ?></div>
                </div>
            </div>
            <div class="toggle-content <?= $dir ? 'open' : '' ?>" id="secDireccion">
                <input type="hidden" name="direccion_activa" id="hDireccion" value="<?= $dir ? '1' : '' ?>">
                <div class="row g-3 py-3">
                    <div class="col-md-6"><label class="form-label">País</label><input type="text" name="pais" class="form-control" value="Venezuela" readonly style="background:#f8f9fa;"></div>
                    <div class="col-md-6"><label class="form-label">Estado</label><select id="estado" name="estado" class="form-select" data-valor="<?= esc($dir['estado'] ?? '') ?>">
                            <option value="">Selecciona...</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Municipio</label><select id="municipio" name="municipio" class="form-select" data-valor="<?= esc($dir['municipio'] ?? '') ?>">
                            <option value="">Selecciona...</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Parroquia</label><select id="parroquia" name="parroquia" class="form-select" data-valor="<?= esc($dir['parroquia'] ?? '') ?>">
                            <option value="">Selecciona...</option>
                        </select></div>
                    <div class="col-12"><label class="form-label">Ciudad o localidad</label><input type="text" name="ciudad" id="ciudad" class="form-control" value="<?= esc($dir['ciudad'] ?? '') ?>"></div>
                </div>
            </div>

            <!-- ESCOLARIDAD -->
            <div class="toggle-bar mt-2 <?= $esc ? 'active' : '' ?>" onclick="toggleSeccion(this,'secEscolaridad')"><i class="bi bi-mortarboard" style="font-size:1.1rem;color:#101a61;"></i>
                <div>
                    <div class="toggle-label">Escolaridad</div>
                    <div class="toggle-desc"><?= $esc ? esc(($esc['nombre_escuela'] ?? '') . ' - ' . ($esc['grado'] ?? '')) : 'Sin escolaridad' ?></div>
                </div>
            </div>
            <div class="toggle-content <?= $esc ? 'open' : '' ?>" id="secEscolaridad">
                <input type="hidden" name="escolaridad_activa" id="hEscolaridad" value="<?= $esc ? '1' : '' ?>">
                <div class="row g-3 py-3">
                    <div class="col-12"><label class="form-label">Nombre de la escuela</label><input type="text" name="nombre_escuela" class="form-control" value="<?= esc($esc['nombre_escuela'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Grado</label><select name="grado" class="form-select"><?php foreach (['' => 'Seleccionar...', '1er grado' => '1er grado', '2do grado' => '2do grado', '3er grado' => '3er grado', '4to grado' => '4to grado', '5to grado' => '5to grado', '6to grado' => '6to grado', '1er año' => '1er año', '2do año' => '2do año', '3er año' => '3er año', '4to año' => '4to año', '5to año' => '5to año'] as $k => $v): ?><option value="<?= $k ?>" <?= ($esc['grado'] ?? '') == $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-4"><label class="form-label">Sección</label><input type="text" name="seccion" class="form-control" value="<?= esc($esc['seccion'] ?? '') ?>"></div>
                    <div class="col-md-4"><label class="form-label">Turno</label><select name="turno" class="form-select"><?php foreach (['' => 'Seleccionar...', 'Mañana' => 'Mañana', 'Tarde' => 'Tarde', 'Integral' => 'Integral'] as $k => $v): ?><option value="<?= $k ?>" <?= ($esc['turno'] ?? '') == $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
                </div>
            </div>

            <!-- FAMILIAR -->
            <div class="toggle-bar mt-2 <?= $fam ? 'active' : '' ?>" onclick="toggleSeccion(this,'secFamiliar')"><i class="bi bi-people" style="font-size:1.1rem;color:#101a61;"></i>
                <div>
                    <div class="toggle-label">Familiar / Representante</div>
                    <div class="toggle-desc"><?= $fam ? esc(($fam['relacion'] ?? '') . ': ' . ($fam['rep_nombres'] ?? '') . ' ' . ($fam['rep_apellidos'] ?? '')) : 'Sin representante' ?></div>
                </div>
            </div>
            <div class="toggle-content <?= $fam ? 'open' : '' ?>" id="secFamiliar">
                <input type="hidden" name="familiar_activo" id="hFamiliar" value="<?= $fam ? '1' : '' ?>">
                <div class="row g-3 py-3">
                    <div class="col-md-6"><label class="form-label">Relación</label><select name="relacion" class="form-select"><?php foreach (['' => 'Seleccionar...', 'Madre' => 'Madre', 'Padre' => 'Padre', 'Abuelo/a' => 'Abuelo/a', 'Tío/a' => 'Tío/a', 'Hermano/a' => 'Hermano/a', 'Otro' => 'Otro'] as $k => $v): ?><option value="<?= $k ?>" <?= ($fam['relacion'] ?? '') == $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
                    <div class="col-md-6"><label class="form-label">Teléfono representante</label><input type="tel" name="telefono_representante" class="form-control" value="<?= esc($fam['telefono'] ?? '') ?>"></div>
                    <div class="col-12">
                        <label class="form-label">Buscar representante</label>
                        <input type="text" id="buscarRep" class="form-control" placeholder="Buscar..." value="<?= $fam ? esc(($fam['rep_nombres'] ?? '') . ' ' . ($fam['rep_apellidos'] ?? '')) : '' ?>">
                        <input type="hidden" name="representante_id" id="representanteId" value="<?= esc($fam['beneficiario_id_representante'] ?? '') ?>">
                        <div id="repResultados" class="mt-2"></div>
                        <?php if ($fam && !empty($fam['rep_nombres'])): ?>
                            <div id="repSeleccionado">
                                <div class="rep-selected"><i class="bi bi-check-circle-fill text-success me-1"></i><strong><?= esc($fam['rep_nombres'] . ' ' . $fam['rep_apellidos']) ?></strong> <span style="color:#888;font-size:.75rem;">(<?= esc($fam['rep_id_digisalud'] ?? '') ?>)</span><span style="cursor:pointer;float:right;color:#dc3545;" onclick="limpiarRep()">✕</span></div>
                            </div>
                        <?php else: ?>
                            <div id="repSeleccionado" style="display:none;"></div>
                        <?php endif; ?>
                    </div>
                    <div class="col-12" id="repNuevoBox" style="display:none;">
                        <div class="rep-nuevo-form">
                            <p style="font-size:.78rem;font-weight:600;color:#101a61;margin-bottom:8px;"><i class="bi bi-person-plus me-1"></i> Registrar representante nuevo</p>
                            <div class="row g-2">
                                <div class="col-md-6"><label class="form-label">Nombres *</label><input type="text" name="rep_nombres" class="form-control form-control-sm"></div>
                                <div class="col-md-6"><label class="form-label">Apellidos *</label><input type="text" name="rep_apellidos" class="form-control form-control-sm"></div>
                                <div class="col-md-4"><label class="form-label">Fecha nac.</label><input type="date" name="rep_fecha_nacimiento" class="form-control form-control-sm"></div>
                                <div class="col-md-4"><label class="form-label">Sexo</label><select name="rep_sexo" class="form-select form-select-sm">
                                        <option value="M">M</option>
                                        <option value="F">F</option>
                                    </select></div>
                                <div class="col-md-4"><label class="form-label">Teléfono</label><input type="tel" name="rep_telefono_nuevo" class="form-control form-control-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ANTECEDENTES -->
            <?php $tieneAnt = !empty($antClinico) || !empty($antSocio) || $usaLentes; ?>
            <div class="toggle-bar mt-2 <?= $tieneAnt ? 'active' : '' ?>" onclick="toggleSeccion(this,'secAntecedentes')"><i class="bi bi-heart-pulse" style="font-size:1.1rem;color:#101a61;"></i>
                <div>
                    <div class="toggle-label">Antecedentes clínicos y socioeconómicos</div>
                    <div class="toggle-desc"><?= $tieneAnt ? count($antClinico) + count($antSocio) . ' antecedente(s)' : 'Sin antecedentes' ?></div>
                </div>
            </div>
            <div class="toggle-content <?= $tieneAnt ? 'open' : '' ?>" id="secAntecedentes">
                <div class="py-3">
                    <div class="chk-lentes">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="usa_lentes" value="1" id="chkUsaLentes" <?= $usaLentes ? 'checked' : '' ?>><label class="form-check-label" for="chkUsaLentes"><strong><i class="bi bi-eyeglasses me-1"></i> ¿Usa lentes correctivos?</strong></label></div>
                    </div>
                    <hr class="my-3">
                    <label class="form-label" style="font-size:.8rem;color:#6c757d;">Buscar antecedente clínico:</label>
                    <input type="text" id="buscarAntecedente" class="form-control" placeholder="Ej: diabetes, asma...">
                    <div id="antResultados" class="mt-1" style="max-height:200px;overflow-y:auto;"></div>
                    <div class="tag-container" id="antSeleccionados"><?php foreach ($antClinico as $a): ?><span class="tag-item"><?= esc($a['descripcion']) ?><input type="hidden" name="antecedentes[]" value="<?= $a['id_antecedente'] ?>"><span class="tag-remove" onclick="rmAnt(this,<?= $a['id_antecedente'] ?>)">×</span></span><?php endforeach; ?></div>
                    <hr class="my-3">
                    <label class="form-label" style="font-size:.8rem;color:#6c757d;">Buscar dato socioeconómico:</label>
                    <input type="text" id="buscarSocioeconomico" class="form-control" placeholder="Ej: aguas, techo...">
                    <div id="socResultados" class="mt-1" style="max-height:200px;overflow-y:auto;"></div>
                    <div class="tag-container" id="socSeleccionados"><?php foreach ($antSocio as $a): ?><span class="tag-item"><?= esc($a['descripcion']) ?><input type="hidden" name="antecedentes[]" value="<?= $a['id_antecedente'] ?>"><span class="tag-remove" onclick="rmAnt(this,<?= $a['id_antecedente'] ?>)">×</span></span><?php endforeach; ?></div>
                    <div class="mt-3"><label class="form-label">Observaciones</label><textarea name="observacion_antecedentes" class="form-control" rows="2"><?= esc($observacion) ?></textarea></div>
                </div>
            </div>

            <!-- BOTONES -->
            <div class="d-flex justify-content-end gap-3 mt-4 pt-3" style="border-top:1px solid #eee;">
                <a href="javascript:history.back()" class="btn-cancelar">Cancelar</a>
                <button type="submit" class="btn-guardar"> Guardar cambios</button>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="<?= base_url('js/venezuela.js') ?>"></script>
<script>
    // ═══════════════════════════════════════════════════════
    // URLs AJAX — deben coincidir EXACTAMENTE con Routes.php
    // ═══════════════════════════════════════════════════════
    const URL_BUSCAR_REP = <?= json_encode(base_url('beneficiarios/buscar-ajax')) ?>;
    const URL_ANTECEDENTES = <?= json_encode(base_url('beneficiarios/antecedentes-ajax')) ?>;

    function toggleSeccion(bar, id) {
        const s = document.getElementById(id);
        const o = s.classList.contains('open');
        s.classList.toggle('open');
        bar.classList.toggle('active');
        const m = {
            secDireccion: 'hDireccion',
            secEscolaridad: 'hEscolaridad',
            secFamiliar: 'hFamiliar'
        };
        if (m[id]) document.getElementById(m[id]).value = o ? '' : '1';
    }

    function actualizarIdPreview() {
        const n = (document.getElementById('fNombres').value || '').trim(),
            a = (document.getElementById('fApellidos').value || '').trim(),
            f = document.getElementById('fFecha').value || '',
            s = document.getElementById('fSexo').value || '_',
            p = (document.getElementById('fPais').value || 'VE').substring(0, 2).toUpperCase();
        const np = n.split(' '),
            ap = a.split(' ');
        const p1 = (np[0] || '___').substring(0, 3).toUpperCase(),
            p2 = np[1] ? np[1][0].toUpperCase() : '',
            a1 = (ap[0] || '___').substring(0, 3).toUpperCase(),
            a2 = ap[1] ? ap[1][0].toUpperCase() : '',
            fd = f.replace(/-/g, '') || '________';
        document.getElementById('idPreview').textContent = `${p}${s}${p1}${p2}${a1}${a2}${fd}`;
    }

    // ═══════════════════════════════════════════════════════
    // DIRECCIÓN — cascading selects con venezuela.js
    // ═══════════════════════════════════════════════════════
    $(document).ready(function() {
        const $e = $('#estado'),
            $m = $('#municipio'),
            $p = $('#parroquia');
        const vE = $e.data('valor') || '',
            vM = $m.data('valor') || '',
            vP = $p.data('valor') || '';
        if (typeof ubicaciones !== 'undefined') {
            Object.keys(ubicaciones).forEach(e => {
                $e.append(`<option value="${e}" ${e===vE?'selected':''}>${e}</option>`);
            });
        }
        $e.select2({
            placeholder: 'Selecciona...'
        });
        $m.select2({
            placeholder: 'Selecciona...'
        });
        $p.select2({
            placeholder: 'Selecciona...'
        });
        if (vE && ubicaciones[vE]) {
            Object.keys(ubicaciones[vE]).forEach(m => {
                $m.append(`<option value="${m}" ${m===vM?'selected':''}>${m}</option>`);
            });
            $m.trigger('change.select2');
            if (vM) {
                (ubicaciones[vE][vM] || []).forEach(pp => {
                    $p.append(`<option value="${pp}" ${pp===vP?'selected':''}>${pp}</option>`);
                });
                $p.trigger('change.select2');
            }
        }
        $e.on('change', function() {
            const est = this.value;
            $m.empty().append(new Option('', ''));
            $p.empty().append(new Option('', ''));
            $('#ciudad').val('');
            Object.keys(ubicaciones[est] || {}).forEach(m => $m.append(new Option(m, m)));
            $m.trigger('change.select2');
        });
        $m.on('change', function() {
            const est = $e.val(),
                mun = this.value;
            const parrs = ubicaciones[est]?.[mun] || [];
            $p.empty().append(new Option('', ''));
            parrs.forEach(p => $p.append(new Option(p, p)));
            $p.trigger('change.select2');
            if (parrs.length > 0) $('#ciudad').val(parrs[0]);
        });
        $p.on('change', function() {
            if (this.value) $('#ciudad').val(this.value);
        });
    });

    // ═══════════════════════════════════════════════════════
    // REPRESENTANTE — buscar existente o registrar nuevo
    // FIX: usa URL_BUSCAR_REP (beneficiarios/buscar-ajax)
    // ═══════════════════════════════════════════════════════
    let repTimer;
    document.getElementById('buscarRep').addEventListener('input', function() {
        clearTimeout(repTimer);
        const q = this.value.trim();
        const c = document.getElementById('repResultados'),
            n = document.getElementById('repNuevoBox');
        if (q.length < 2) {
            c.innerHTML = '';
            n.style.display = 'none';
            return;
        }
        repTimer = setTimeout(() => {
            // ── FIX: URL correcta con kebab-case ──
            fetch(`${URL_BUSCAR_REP}?q=${encodeURIComponent(q)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.length === 0) {
                        c.innerHTML = `
                            <p style="font-size:.78rem;color:#888;">No encontrado</p>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="habilitarNuevoRep()">
                                <i class="bi bi-person-plus"></i> Registrar nuevo
                            </button>`;
                        return;
                    }
                    n.style.display = 'none';
                    let h = '';
                    data.forEach(b => {
                        const nom = `${(b.nombres||'')} ${(b.apellidos||'')}`.trim();
                        const digi = b.id_digisalud || '';
                        h += `<div class="rep-result" onclick="seleccionarRep(${b.id_beneficiario},'${nom.replace(/'/g,"\\'")}','${digi}')"><strong>${(b.apellidos||'').toUpperCase()}, ${(b.nombres||'').toUpperCase()}</strong> <span style="color:#888;font-size:.72rem;">— ${digi}</span></div>`;
                    });
                    c.innerHTML = h;
                })
                .catch(err => {
                    console.error('Error buscando representante:', err);
                    c.innerHTML = '<p class="text-danger small">Error de conexión</p>';
                });
        }, 300);
    });

    function seleccionarRep(id, nom, digi) {
        document.getElementById('representanteId').value = id;
        document.getElementById('repResultados').innerHTML = '';
        document.getElementById('buscarRep').value = nom;
        document.getElementById('repNuevoBox').style.display = 'none';
        document.getElementById('repSeleccionado').style.display = 'block';
        document.getElementById('repSeleccionado').innerHTML = `<div class="rep-selected"><i class="bi bi-check-circle-fill text-success me-1"></i><strong>${nom}</strong> <span style="color:#888;font-size:.75rem;">(${digi})</span><span style="cursor:pointer;float:right;color:#dc3545;" onclick="limpiarRep()">✕</span></div>`;
    }

    function limpiarRep() {
        document.getElementById('representanteId').value = '';
        document.getElementById('buscarRep').value = '';
        document.getElementById('repSeleccionado').style.display = 'none';
        document.getElementById('repSeleccionado').innerHTML = '';
        document.getElementById('repNuevoBox').style.display = 'none';
    }

    function habilitarNuevoRep() {
        document.getElementById('representanteId').value = '';
        document.getElementById('repResultados').innerHTML = '';
        document.getElementById('repNuevoBox').style.display = 'block';
    }

    // ═══════════════════════════════════════════════════════
    // ANTECEDENTES — buscar clínicos y socioeconómicos
    // FIX: usa URL_ANTECEDENTES (beneficiarios/antecedentes-ajax)
    //       con parámetros q y tipo
    // ═══════════════════════════════════════════════════════
    const antSet = new Set();
    document.querySelectorAll('#antSeleccionados input[name="antecedentes[]"],#socSeleccionados input[name="antecedentes[]"]').forEach(i => {
        antSet.add(parseInt(i.value));
    });

    function initBuscAnt(iId, rId, cId, tipo) {
        let t;
        document.getElementById(iId).addEventListener('input', function() {
            clearTimeout(t);
            const q = this.value.trim();
            const c = document.getElementById(rId);
            if (q.length < 2) {
                c.innerHTML = '';
                return;
            }
            t = setTimeout(() => {
                // ── FIX: URL correcta + parámetros q y tipo ──
                fetch(`${URL_ANTECEDENTES}?q=${encodeURIComponent(q)}&tipo=${encodeURIComponent(tipo)}`)
                    .then(r => r.json())
                    .then(data => {
                        let h = '';
                        data.forEach(a => {
                            if (antSet.has(a.id_antecedente)) return;
                            h += `<div class="rep-result" onclick="addAnt(${a.id_antecedente},'${(a.descripcion||'').replace(/'/g,"\\'")}','${cId}')">${a.descripcion} <span style="color:#888;font-size:.72rem;">(${a.tipo})</span></div>`;
                        });
                        c.innerHTML = h || '<p style="font-size:.78rem;color:#888;">Sin resultados</p>';
                    })
                    .catch(err => {
                        console.error('Error buscando antecedentes:', err);
                        c.innerHTML = '<p class="text-danger small">Error de conexión</p>';
                    });
            }, 300);
        });
    }

    function addAnt(id, desc, cId) {
        if (antSet.has(id)) return;
        antSet.add(id);
        const c = document.getElementById(cId);
        const s = document.createElement('span');
        s.className = 'tag-item';
        s.innerHTML = `${desc}<input type="hidden" name="antecedentes[]" value="${id}"><span class="tag-remove" onclick="rmAnt(this,${id})">×</span>`;
        c.appendChild(s);
        document.getElementById(cId === 'antSeleccionados' ? 'antResultados' : 'socResultados').innerHTML = '';
        document.getElementById(cId === 'antSeleccionados' ? 'buscarAntecedente' : 'buscarSocioeconomico').value = '';
    }

    function rmAnt(el, id) {
        antSet.delete(id);
        el.parentElement.remove();
    }

    initBuscAnt('buscarAntecedente', 'antResultados', 'antSeleccionados', 'Antecedentes Clínicos');
    initBuscAnt('buscarSocioeconomico', 'socResultados', 'socSeleccionados', 'Datos Socioeconómicos');


    <?php
$jornadaIdRetorno = service('request')->getGet('jornada_id') ?? 2;
?>

 <?php if (session()->getFlashdata('success')): ?>
    <?php $flashSuccess = session()->getFlashdata('success'); ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.addEventListener('DOMContentLoaded', function () {
            Swal.fire({
                title: "Beneficiario actualizado correctamente",
                text: "",
                icon: "success",
                confirmButtonText: "Continuar",
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
             window.location.replace("<?= site_url('jornadas/' . $jornadaIdRetorno . '/beneficiarios') ?>");
            });
        });
    </script>
<?php endif; ?>
</script>
<?= $this->endSection() ?>
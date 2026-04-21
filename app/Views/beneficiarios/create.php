<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>

</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-4">
<div class="create-container">

    <div class="breadcrumb-digi">
        <a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt;
        <a href="<?= base_url("jornadas/$jornada_id/beneficiarios") ?>">Beneficiarios</a> &gt;
        <a href="<?= base_url("beneficiarios/buscar/$jornada_id") ?>">Buscar</a> &gt;
        <span class="active">Nuevo registro</span>
    </div>

    <h4 class="create-title">Registrar nuevo beneficiario</h4>
    <p class="create-sub">El ID Digisalud se genera automáticamente.</p>

    <div class="id-preview">
        <div class="id-preview-label">ID Digisalud (autogenerado)</div>
        <div class="id-preview-val" id="idPreview">__ · _ · ___ · ___ · ________</div>
    </div>

    <form method="post" action="<?= base_url("beneficiarios/store/$jornada_id") ?>" id="formBeneficiario" novalidate>
        <?= csrf_field() ?>

        <!-- ═══ DATOS PERSONALES ═══ -->
        <div class="mb-4">
            <div class="section-header"><i class="bi bi-person"></i> Datos personales</div>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">Nombres *</label><input type="text" name="nombres" id="fNombres" class="form-control" required placeholder="Ej: María José" oninput="actualizarIdPreview()"></div>
                <div class="col-md-6"><label class="form-label">Apellidos *</label><input type="text" name="apellidos" id="fApellidos" class="form-control" required placeholder="Ej: García López" oninput="actualizarIdPreview()"></div>
                <div class="col-md-4"><label class="form-label">Fecha de nacimiento *</label><input type="date" name="fecha_nacimiento" id="fFecha" class="form-control" required oninput="actualizarIdPreview()"></div>
                <div class="col-md-4"><label class="form-label">Sexo *</label><select name="sexo" id="fSexo" class="form-select" required onchange="actualizarIdPreview()"><option value="">Seleccionar...</option><option value="M">Masculino</option><option value="F">Femenino</option></select></div>
                <div class="col-md-4"><label class="form-label">País de nacimiento *</label><select name="pais_nacimiento" id="fPais" class="form-select" required onchange="actualizarIdPreview()"><option value="Venezuela">Venezuela</option><option value="Colombia">Colombia</option><option value="Peru">Perú</option><option value="Ecuador">Ecuador</option><option value="Brasil">Brasil</option><option value="El Salvador">El Salvador</option><option value="Otro">Otro</option></select></div>
                <div class="col-md-6"><label class="form-label">Teléfono</label><input type="tel" name="telefono" class="form-control" placeholder="+58 412 1234567"></div>
                <div class="col-md-6"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control" placeholder="ejemplo@correo.com"></div>
            </div>
        </div>

        <!-- ═══ DIRECCIÓN ═══ -->
        <div class="toggle-bar" onclick="toggleSeccion(this,'secDireccion')"><i class="bi bi-geo-alt" style="font-size:1.1rem;color:#101a61;"></i><div><div class="toggle-label">Dirección de residencia</div><div class="toggle-desc">Opcional — carga automática desde venezuela.js</div></div></div>
        <div class="toggle-content" id="secDireccion">
            <input type="hidden" name="direccion_activa" id="hDireccion" value="">
            <div class="row g-3 py-3">
                <div class="col-md-6"><label class="form-label">País</label><input type="text" name="pais" class="form-control" value="Venezuela" readonly style="background:#f8f9fa;"></div>
                <div class="col-md-6"><label class="form-label">Estado</label><select id="estado" name="estado" class="form-select"><option value="">Selecciona un estado...</option></select></div>
                <div class="col-md-6"><label class="form-label">Municipio</label><select id="municipio" name="municipio" class="form-select"><option value="">Selecciona un municipio...</option></select></div>
                <div class="col-md-6"><label class="form-label">Parroquia / Ciudad</label><select id="parroquia" name="parroquia" class="form-select"><option value="">Selecciona...</option></select></div>
                <div class="col-12"><label class="form-label">Ciudad o localidad</label><input type="text" name="ciudad" id="ciudad" class="form-control" placeholder="Se carga automático o escribe manualmente"></div>
            </div>
        </div>

        <!-- ═══ ESCOLARIDAD ═══ -->
        <div class="toggle-bar mt-2" onclick="toggleSeccion(this,'secEscolaridad')"><i class="bi bi-mortarboard" style="font-size:1.1rem;color:#101a61;"></i><div><div class="toggle-label">Escolaridad</div><div class="toggle-desc">Opcional — se registra fecha y usuario para historial</div></div></div>
        <div class="toggle-content" id="secEscolaridad">
            <input type="hidden" name="escolaridad_activa" id="hEscolaridad" value="">
            <div class="row g-3 py-3">
                <div class="col-12"><label class="form-label">Nombre de la escuela</label><input type="text" name="nombre_escuela" class="form-control" placeholder="Ej: U.E. Simón Bolívar"></div>
                <div class="col-md-4"><label class="form-label">Grado</label><select name="grado" class="form-select"><option value="">Seleccionar...</option><option>1er grado</option><option>2do grado</option><option>3er grado</option><option>4to grado</option><option>5to grado</option><option>6to grado</option><option>1er año</option><option>2do año</option><option>3er año</option><option>4to año</option><option>5to año</option></select></div>
                <div class="col-md-4"><label class="form-label">Sección</label><input type="text" name="seccion" class="form-control" placeholder="A, B, C..."></div>
                <div class="col-md-4"><label class="form-label">Turno</label><select name="turno" class="form-select"><option value="">Seleccionar...</option><option>Mañana</option><option>Tarde</option><option>Integral</option></select></div>
            </div>
        </div>

        <!-- ═══ FAMILIAR / REPRESENTANTE ═══ -->
        <div class="toggle-bar mt-2" onclick="toggleSeccion(this,'secFamiliar')"><i class="bi bi-people" style="font-size:1.1rem;color:#101a61;"></i><div><div class="toggle-label">Familiar / Representante</div><div class="toggle-desc">Opcional — busca existente o registra nuevo</div></div></div>
        <div class="toggle-content" id="secFamiliar">
            <input type="hidden" name="familiar_activo" id="hFamiliar" value="">
            <div class="row g-3 py-3">
                <div class="col-md-6"><label class="form-label">Relación</label><select name="relacion" class="form-select"><option value="">Seleccionar...</option><option>Madre</option><option>Padre</option><option>Abuelo/a</option><option>Tío/a</option><option>Hermano/a</option><option>Otro</option></select></div>
                <div class="col-md-6"><label class="form-label">Teléfono del representante</label><input type="tel" name="telefono_representante" class="form-control" placeholder="+58 412 1234567"></div>
                <div class="col-12">
                    <label class="form-label">Buscar representante existente</label>
                    <input type="text" id="buscarRep" class="form-control" placeholder="Buscar por nombre o ID Digisalud...">
                    <input type="hidden" name="representante_id" id="representanteId">
                    <div id="repResultados" class="mt-2"></div>
                    <div id="repSeleccionado" style="display:none;"></div>
                </div>
                <div class="col-12"><div class="chk-evaluar"><div class="form-check"><input class="form-check-input" type="checkbox" name="evaluar_representante" value="1" id="chkEvaluarRep"><label class="form-check-label" for="chkEvaluarRep"><strong>¿Evaluar al representante en esta jornada?</strong><br><small class="text-muted">El representante también será agregado como beneficiario de la jornada.</small></label></div></div></div>
                <div class="col-12" id="repNuevoBox" style="display:none;">
                    <div class="rep-nuevo-form">
                        <p style="font-size:.78rem;font-weight:600;color:#101a61;margin-bottom:8px;"><i class="bi bi-person-plus me-1"></i> Registrar representante nuevo</p>
                        <div class="row g-2">
                            <div class="col-md-6"><label class="form-label">Nombres *</label><input type="text" name="rep_nombres" class="form-control form-control-sm"></div>
                            <div class="col-md-6"><label class="form-label">Apellidos *</label><input type="text" name="rep_apellidos" class="form-control form-control-sm"></div>
                            <div class="col-md-4"><label class="form-label">Fecha nac.</label><input type="date" name="rep_fecha_nacimiento" class="form-control form-control-sm"></div>
                            <div class="col-md-4"><label class="form-label">Sexo</label><select name="rep_sexo" class="form-select form-select-sm"><option value="M">M</option><option value="F">F</option></select></div>
                            <div class="col-md-4"><label class="form-label">Teléfono</label><input type="tel" name="rep_telefono_nuevo" class="form-control form-control-sm" placeholder="+58..."></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ ANTECEDENTES ═══ -->
        <div class="toggle-bar mt-2" onclick="toggleSeccion(this,'secAntecedentes')"><i class="bi bi-heart-pulse" style="font-size:1.1rem;color:#101a61;"></i><div><div class="toggle-label">Antecedentes clínicos y socioeconómicos</div><div class="toggle-desc">Opcional — busca en la base de datos</div></div></div>
        <div class="toggle-content" id="secAntecedentes">
            <div class="py-3">
                <!-- CHECKBOX USA LENTES (destacado) -->
                <div class="chk-lentes">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="usa_lentes" value="1" id="chkUsaLentes">
                        <label class="form-check-label" for="chkUsaLentes">
                            <strong><i class="bi bi-eyeglasses me-1"></i> ¿Usa lentes correctivos?</strong>
                        </label>
                    </div>
                </div>

                <hr class="my-3">

                <label class="form-label" style="font-size:.8rem;color:#6c757d;">Buscar antecedente clínico:</label>
                <input type="text" id="buscarAntecedente" class="form-control" placeholder="Ej: diabetes, asma, hipertensión...">
                <div id="antResultados" class="mt-1" style="max-height:200px;overflow-y:auto;"></div>
                <div class="tag-container" id="antSeleccionados"></div>

                <hr class="my-3">

                <label class="form-label" style="font-size:.8rem;color:#6c757d;">Buscar dato socioeconómico:</label>
                <input type="text" id="buscarSocioeconomico" class="form-control" placeholder="Ej: aguas, techo, electricidad...">
                <div id="socResultados" class="mt-1" style="max-height:200px;overflow-y:auto;"></div>
                <div class="tag-container" id="socSeleccionados"></div>

                <div class="mt-3"><label class="form-label">Observaciones generales</label><textarea name="observacion_antecedentes" class="form-control" rows="2" placeholder="Notas adicionales..."></textarea></div>
            </div>
        </div>

        <!-- ═══ BOTONES ═══ -->
        <div class="d-flex justify-content-end gap-3 mt-4 pt-3" style="border-top:1px solid #eee;">
            <a href="<?= base_url("beneficiarios/buscar/$jornada_id") ?>" class="btn-cancelar">Cancelar</a>
            <button type="submit" class="btn-guardar"><i class="bi bi-check-lg"></i> Guardar y asociar a jornada</button>
        </div>
    </form>
</div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="<?= base_url('js/venezuela.js') ?>"></script>
<script>
function toggleSeccion(bar,id){const s=document.getElementById(id);const o=s.classList.contains('open');s.classList.toggle('open');bar.classList.toggle('active');const m={secDireccion:'hDireccion',secEscolaridad:'hEscolaridad',secFamiliar:'hFamiliar'};if(m[id])document.getElementById(m[id]).value=o?'':'1';}
function actualizarIdPreview(){const n=(document.getElementById('fNombres').value||'').trim(),a=(document.getElementById('fApellidos').value||'').trim(),f=document.getElementById('fFecha').value||'',s=document.getElementById('fSexo').value||'_',p=(document.getElementById('fPais').value||'VE').substring(0,2).toUpperCase();const np=n.split(' '),ap=a.split(' ');const p1=(np[0]||'___').substring(0,3).toUpperCase(),p2=np[1]?np[1][0].toUpperCase():'',a1=(ap[0]||'___').substring(0,3).toUpperCase(),a2=ap[1]?ap[1][0].toUpperCase():'',fd=f.replace(/-/g,'')||'________';document.getElementById('idPreview').textContent=`${p}${s}${p1}${p2}${a1}${a2}${fd}`;}

$(document).ready(function(){
    const $e=$('#estado'),$m=$('#municipio'),$p=$('#parroquia');
    if(typeof ubicaciones!=='undefined'){Object.keys(ubicaciones).forEach(e=>$e.append(new Option(e,e)));}
    $e.select2({placeholder:'Selecciona un estado'});$m.select2({placeholder:'Selecciona un municipio'});$p.select2({placeholder:'Selecciona...'});
    $e.on('change',function(){const est=this.value;const muns=Object.keys(ubicaciones[est]||{});$m.empty().append(new Option('',''));$p.empty().append(new Option('',''));$('#ciudad').val('');muns.forEach(m=>$m.append(new Option(m,m)));$m.trigger('change.select2');});
    $m.on('change',function(){const est=$e.val(),mun=this.value;const parrs=ubicaciones[est]?.[mun]||[];$p.empty().append(new Option('',''));parrs.forEach(p=>$p.append(new Option(p,p)));$p.trigger('change.select2');if(parrs.length>0)$('#ciudad').val(parrs[0]);});
    $p.on('change',function(){if(this.value)$('#ciudad').val(this.value);});
});

let repTimer;
document.getElementById('buscarRep').addEventListener('input',function(){clearTimeout(repTimer);const q=this.value.trim();const c=document.getElementById('repResultados'),n=document.getElementById('repNuevoBox');if(q.length<2){c.innerHTML='';n.style.display='none';return;}
repTimer=setTimeout(()=>{fetch(`/beneficiarios/buscarAjax?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(data=>{if(data.length===0){c.innerHTML='<p style="font-size:.78rem;color:#888;">No encontrado</p>';n.style.display='block';return;}n.style.display='none';let h='';data.forEach(b=>{h+=`<div class="rep-result" onclick="seleccionarRep(${b.id_beneficiario},'${b.nombres} ${b.apellidos}','${b.id_digisalud||''}')"><strong>${b.apellidos.toUpperCase()}, ${b.nombres.toUpperCase()}</strong> <span style="color:#888;font-size:.72rem;">— ${b.id_digisalud||''}</span></div>`;});c.innerHTML=h;});},300);});
function seleccionarRep(id,nombre,idDigi){document.getElementById('representanteId').value=id;document.getElementById('repResultados').innerHTML='';document.getElementById('buscarRep').value=nombre;document.getElementById('repNuevoBox').style.display='none';document.getElementById('repSeleccionado').style.display='block';document.getElementById('repSeleccionado').innerHTML=`<div class="rep-selected"><i class="bi bi-check-circle-fill text-success me-1"></i><strong>${nombre}</strong> <span style="color:#888;font-size:.75rem;">(${idDigi})</span><span style="cursor:pointer;float:right;color:#dc3545;" onclick="limpiarRep()">✕</span></div>`;}
function limpiarRep(){document.getElementById('representanteId').value='';document.getElementById('buscarRep').value='';document.getElementById('repSeleccionado').style.display='none';document.getElementById('repSeleccionado').innerHTML='';}

const antSet=new Set();
function initBuscAnt(inputId,resId,contId,tipo){let t;document.getElementById(inputId).addEventListener('input',function(){clearTimeout(t);const q=this.value.trim();const c=document.getElementById(resId);if(q.length<2){c.innerHTML='';return;}
t=setTimeout(()=>{fetch(`/beneficiarios/buscarAntecedentesAjax?q=${encodeURIComponent(q)}&tipo=${encodeURIComponent(tipo)}`).then(r=>r.json()).then(data=>{let h='';data.forEach(a=>{if(antSet.has(a.id_antecedente))return;h+=`<div class="rep-result" onclick="addAnt(${a.id_antecedente},'${a.descripcion.replace(/'/g,"\\'")}','${contId}')">${a.descripcion} <span style="color:#888;font-size:.72rem;">(${a.tipo})</span></div>`;});c.innerHTML=h||'<p style="font-size:.78rem;color:#888;">Sin resultados</p>';});},300);});}
function addAnt(id,desc,contId){if(antSet.has(id))return;antSet.add(id);const c=document.getElementById(contId);const s=document.createElement('span');s.className='tag-item';s.innerHTML=`${desc}<input type="hidden" name="antecedentes[]" value="${id}"><span class="tag-remove" onclick="rmAnt(this,${id})">×</span>`;c.appendChild(s);document.getElementById(contId==='antSeleccionados'?'antResultados':'socResultados').innerHTML='';document.getElementById(contId==='antSeleccionados'?'buscarAntecedente':'buscarSocioeconomico').value='';}
function rmAnt(el,id){antSet.delete(id);el.parentElement.remove();}
initBuscAnt('buscarAntecedente','antResultados','antSeleccionados','Antecedentes Clínicos');
initBuscAnt('buscarSocioeconomico','socResultados','socSeleccionados','Datos Socioeconómicos');
</script>
<?= $this->endSection() ?>
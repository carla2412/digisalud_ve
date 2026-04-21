<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>
<style>
.buscar-container{max-width:750px;margin:0 auto}.breadcrumb-digi{font-size:.82rem;color:#6c757d;margin-bottom:1rem}.breadcrumb-digi a{color:#6c757d;text-decoration:none}.breadcrumb-digi a:hover{color:#101a61}.breadcrumb-digi .active{font-weight:600;color:#0b1b3f}
.filtro-title{font-size:.85rem;font-weight:700;color:#0b1b3f;text-transform:uppercase;margin-bottom:12px;letter-spacing:.5px}
.search-row{display:flex;gap:10px;margin-bottom:1rem;align-items:center;flex-wrap:wrap}.search-row input{flex:1;min-width:200px;padding:10px 16px;border:1.5px solid #c5cad0;border-radius:25px;font-size:.9rem}.search-row input:focus{outline:none;border-color:#101a61;box-shadow:0 0 0 3px rgba(16,26,97,.08)}
.btn-buscar{background:transparent;color:#101a61;border:1.5px solid #101a61;border-radius:25px;padding:8px 24px;font-size:.82rem;font-weight:600;text-transform:uppercase;cursor:pointer;white-space:nowrap}.btn-buscar:hover{background:#101a61;color:#fff}
.resultado-header{display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px;margin-bottom:1rem}.resultado-count{font-size:.82rem;font-weight:700;color:#0b1b3f;text-transform:uppercase}
.btn-registrar-nuevo{background:transparent;color:#101a61;border:1.5px solid #101a61;border-radius:25px;padding:6px 20px;font-size:.82rem;font-weight:600;text-transform:uppercase;text-decoration:none;transition:all .2s}.btn-registrar-nuevo:hover{background:#101a61;color:#fff}
.resultado-card{border-top:1px solid #e0e4e8;padding:16px 8px;display:flex;align-items:flex-start;justify-content:space-between;gap:12px}.resultado-card:last-child{border-bottom:1px solid #e0e4e8}
.resultado-info{flex:1}.resultado-nombre{font-size:.9rem;font-weight:700;color:#0b1b3f;text-transform:uppercase;margin-bottom:3px}
.resultado-meta{font-size:.78rem;color:#555;line-height:1.6}.resultado-meta .lbl{font-weight:600}.resultado-meta .lbl-id{color:#0d6efd}.resultado-meta .lbl-fn{color:#0d6efd}.resultado-meta .lbl-rep{color:#e67e22}
.btn-agregar{background:transparent;color:#101a61;border:1.5px solid #101a61;border-radius:25px;padding:6px 18px;font-size:.78rem;font-weight:600;text-transform:uppercase;cursor:pointer;white-space:nowrap;transition:all .2s}.btn-agregar:hover{background:#101a61;color:#fff}
.empty-state{text-align:center;padding:2.5rem 1rem;color:#6c757d}.hint-text{text-align:center;color:#adb5bd;font-size:.82rem;padding:2rem 0}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div class="container my-4"><div class="buscar-container">
    <div class="breadcrumb-digi"><a href="<?=base_url('jornadas')?>">Jornadas</a> &gt; <a href="<?=base_url("jornadas/$jornada_id/beneficiarios")?>">Beneficiarios</a> &gt; <span class="active">Buscar o registrar</span></div>
    <div class="filtro-title">Filtros / Búsqueda de beneficiarios</div>
    <div class="search-row"><input type="text" id="campoBusqueda" placeholder="Nombre, apellido o ID Digisalud..." autocomplete="off"><button class="btn-buscar" onclick="ejecutarBusqueda()">Buscar</button></div>
    <div class="resultado-header" id="resultadoHeader" style="display:none;"><span class="resultado-count" id="resultadoCount"></span><a href="<?=base_url("beneficiarios/create/$jornada_id")?>" class="btn-registrar-nuevo">+ Registrar nuevo</a></div>
    <div id="resultados"></div>
    <div id="sinResultados" style="display:none;"><div class="empty-state"><i class="bi bi-person-x" style="font-size:2.5rem;color:#dee2e6;"></i><p class="mt-2">No se encontró ningún beneficiario</p><a href="<?=base_url("beneficiarios/create/$jornada_id")?>" class="btn-registrar-nuevo">+ Registrar nuevo</a></div></div>
    <div id="estadoInicial"><p class="hint-text">Escribe al menos 2 caracteres y presiona Buscar</p><div class="text-center mt-3"><a href="<?=base_url("beneficiarios/create/$jornada_id")?>" class="btn-registrar-nuevo">+ Registrar nuevo beneficiario</a></div></div>
</div></div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
const jornadaId=<?=$jornada_id?>;
document.getElementById('campoBusqueda').addEventListener('keydown',function(e){if(e.key==='Enter')ejecutarBusqueda();});
function ejecutarBusqueda(){const q=document.getElementById('campoBusqueda').value.trim();if(q.length<2)return;document.getElementById('estadoInicial').style.display='none';
fetch(`/beneficiarios/buscarAjax?q=${encodeURIComponent(q)}`).then(r=>r.json()).then(data=>{const c=document.getElementById('resultados'),s=document.getElementById('sinResultados'),h=document.getElementById('resultadoHeader'),cnt=document.getElementById('resultadoCount');
if(data.length===0){c.innerHTML='';s.style.display='block';h.style.display='none';return;}s.style.display='none';h.style.display='flex';cnt.textContent=`Se encontró ${data.length} beneficiario(s)`;
let html='';data.forEach(b=>{const ff=b.fecha_nacimiento?b.fecha_nacimiento.split('-').reverse().join('/'):'';const p=b.parentesco||'';
html+=`<div class="resultado-card"><div class="resultado-info"><div class="resultado-nombre">${(b.apellidos||'').toUpperCase()}, ${(b.nombres||'').toUpperCase()}</div><div class="resultado-meta"><span class="lbl lbl-id">ID:</span>${b.id_digisalud||'—'} <span class="lbl lbl-fn">FN:</span> ${ff} ${b.edad?' - '+b.edad:''}</div>${p?`<div class="resultado-meta"><span class="lbl lbl-rep">Parentesco:</span> ${p}</div>`:'<div class="resultado-meta"><span class="lbl lbl-rep">Parentesco:</span></div>'}</div><form method="post" action="/jornadas/${jornadaId}/asociar/${b.id_beneficiario}" style="margin:0;"><button type="submit" class="btn-agregar">+ Agregar</button></form></div>`;});c.innerHTML=html;});}
document.getElementById('campoBusqueda').focus();
</script>
<?= $this->endSection() ?>
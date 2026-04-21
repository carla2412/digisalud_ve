<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>
<style>
.benef-count{display:flex;align-items:center;gap:8px}.benef-count img{width:32px}.benef-count span{font-size:1.4rem;font-weight:700;color:#101a61}
.btn-registrar{background:transparent;color:#101a61;border:2px solid #101a61;border-radius:25px;padding:6px 20px;font-size:.85rem;font-weight:600;text-transform:uppercase;letter-spacing:.5px;text-decoration:none;transition:all .2s}.btn-registrar:hover{background:#101a61;color:#fff}
.benef-card{background:#fff;border:1px solid #e9ecef;border-radius:8px;padding:1rem 1.2rem;margin-bottom:.75rem;transition:box-shadow .2s;position:relative}.benef-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.08)}
.benef-card-name{font-size:.95rem;font-weight:700;color:#0b1b3f;text-transform:uppercase;margin-bottom:4px}
.benef-card-meta{font-size:.8rem;color:#555;margin-bottom:10px;line-height:1.5}.benef-card-meta .label-id{color:#0d6efd;font-weight:600}.benef-card-meta .label-fn{color:#0d6efd;font-weight:600}.benef-card-meta .label-rep{color:#e67e22;font-weight:600}
.pesquisa-icons{display:flex;flex-wrap:wrap;gap:8px}
.pesquisa-btn{width:38px;height:38px;border-radius:50%;border:2px solid #ddd;background:#f8f9fa;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .2s;padding:0}
.pesquisa-btn:hover{border-color:#101a61;background:#e8eaf8;transform:scale(1.1)}
.pesquisa-btn.evaluado{border-color:#28a745;background:#d4edda}
.pesquisa-btn img{width:24px;height:24px}
.benef-card-menu{position:absolute;top:12px;right:12px}.benef-card-menu .btn{padding:2px 6px;font-size:1.2rem;color:#999;line-height:1}
.benef-card-menu .dropdown-item{font-size:.82rem;padding:8px 16px}.benef-card-menu .dropdown-item i{width:20px;text-align:center}
.pesquisa-modal-list{list-style:none;padding:0;margin:0}.pesquisa-modal-list li{display:flex;align-items:center;gap:12px;padding:12px 16px;border-bottom:1px solid #eee;cursor:pointer;border-radius:6px;transition:background .15s}.pesquisa-modal-list li:hover{background:#f0f4ff}.pesquisa-modal-list li:last-child{border-bottom:none}.pesquisa-modal-list li img{width:36px;height:36px}.pesquisa-modal-list li .pesq-name{font-weight:600;color:#0b1b3f;font-size:.9rem}.pesquisa-modal-list li .pesq-desc{font-size:.78rem;color:#888}
.breadcrumb-digi{font-size:.82rem;color:#6c757d;margin-bottom:1rem}.breadcrumb-digi a{color:#6c757d;text-decoration:none}.breadcrumb-digi a:hover{color:#101a61}.breadcrumb-digi .active{font-weight:600;color:#0b1b3f}
.search-inline{position:relative;margin-bottom:1rem}.search-inline input{width:100%;padding:10px 14px 10px 38px;border:1px solid #dee2e6;border-radius:25px;font-size:.85rem}.search-inline input:focus{outline:none;border-color:#101a61;box-shadow:0 0 0 3px rgba(16,26,97,.1)}.search-inline .icon-search{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#aaa}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container my-4">

    <div class="breadcrumb-digi">
        <a href="<?= base_url('jornadas') ?>">Listado de Jornadas</a> : Beneficiarios &gt;
        <span class="active"><?= esc($jornada['nombre_jornada'] ?? 'Jornada') ?> <?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?></span>
    </div>

    <?php if (session('success')): ?><div class="alert alert-success alert-dismissible fade show"><?= session('success') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>
    <?php if (session('error')): ?><div class="alert alert-warning alert-dismissible fade show"><?= session('error') ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div><?php endif; ?>

    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
        <div class="benef-count"><img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" alt=""><span><?= $total ?? 0 ?></span></div>
        <span style="color:#ccc;font-size:1.5rem;">|</span>
        <a href="<?= base_url("beneficiarios/buscar/$jornada_id") ?>" class="btn-registrar">+ Registrar</a>
    </div>

    <?php if (!empty($beneficiarios) && count($beneficiarios) > 5): ?>
    <div class="search-inline"><i class="bi bi-search icon-search"></i><input type="text" id="filtrarBenef" placeholder="Filtrar por nombre o ID..."></div>
    <?php endif; ?>

    <?php if (!empty($beneficiarios)): ?>
        <?php
            $iconos_color = [
                '1'=>['img'=>'antropometria2.svg','gris'=>'antropometria-gris.svg','nombre'=>'Antropometría'],
                '2'=>['img'=>'sanguinea2.svg','gris'=>'sanguinea-gris.svg','nombre'=>'Laboratorio'],
                '3'=>['img'=>'optica2.svg','gris'=>'optica-gris.svg','nombre'=>'Visual'],
                '4'=>['img'=>'signosVitales2.svg','gris'=>'signosVitales-gris.svg','nombre'=>'Signos vitales'],
                '5'=>['img'=>'medicinaGeneral2.svg','gris'=>'medicinaGeneral-gris.svg','nombre'=>'Medicina general'],
                '6'=>['img'=>'vacunacion2.svg','gris'=>'vacunacion-gris.svg','nombre'=>'Vacunación'],
            ];
        ?>

        <?php foreach ($beneficiarios as $b): ?>
            <?php
                $nac = new \DateTime($b['fecha_nacimiento']);
                $diff = (new \DateTime())->diff($nac);
                $edad = $diff->y.' año'.($diff->y>1?'s':'').' con '.$diff->m.' mes(es) y '.$diff->d.' dias';
                $evals = $evaluaciones[$b['id_beneficiario']] ?? [];
            ?>
            <div class="benef-card" data-search="<?= strtolower(esc($b['apellidos'].' '.$b['nombres'].' '.($b['id_digisalud']??''))) ?>">

                <!-- ═══ MENÚ 3 PUNTOS: Editar, Evaluar, Retirar ═══ -->
                <div class="benef-card-menu dropdown">
                    <button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li>
                            <a class="dropdown-item" href="<?= base_url("beneficiarios/editar/{$b['id_beneficiario']}") ?>">
                                <i class="bi bi-pencil-square me-2 text-primary"></i>Editar perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#"
                               onclick="abrirEvaluar(<?=$b['id_beneficiario']?>,'0','<?=esc($b['nombres'].' '.$b['apellidos'])?>')">
                                <i class="bi bi-clipboard2-pulse me-2 text-success"></i>Evaluar
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-danger" href="#"
                               onclick="confirmarRemover(<?=$jornada_id?>,<?=$b['id_beneficiario']?>)">
                                <i class="bi bi-x-circle me-2"></i>Retirar de la jornada
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="benef-card-name"><?= esc(strtoupper($b['apellidos'])) ?>, <?= esc(strtoupper($b['nombres'])) ?></div>
                <div class="benef-card-meta">
                    <span class="label-id">ID:</span><?= esc($b['id_digisalud']??'—') ?>
                    | <span class="label-fn">FN:</span> <?= date('d-m-Y', strtotime($b['fecha_nacimiento'])) ?> - <?= $edad ?>
                    <?php if (!empty($b['rep_nombres'])): ?>
                        | <span class="label-rep">Representante:</span> <?= esc($b['rep_nombres'].' '.$b['rep_apellidos']) ?>
                    <?php endif; ?>
                </div>
                <div class="pesquisa-icons">
                    <?php foreach ($pesquisas_jornada as $p): ?>
                        <?php if (isset($iconos_color[$p])):
                            $yaEvaluado = in_array($p, $evals);
                            $icono = $yaEvaluado ? $iconos_color[$p]['img'] : $iconos_color[$p]['gris'];
                            $clase = $yaEvaluado ? 'pesquisa-btn evaluado' : 'pesquisa-btn';
                        ?>
                            <button class="<?=$clase?>" type="button" title="<?=esc($iconos_color[$p]['nombre'])?>"
                                    onclick="abrirEvaluar(<?=$b['id_beneficiario']?>,'<?=$p?>','<?=esc($b['nombres'].' '.$b['apellidos'])?>')">
                                <img src="<?=base_url('img/'.$icono)?>" alt="<?=esc($iconos_color[$p]['nombre'])?>">
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="text-center py-5">
            <img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" width="60" class="mb-3 opacity-25">
            <p class="text-muted">No hay beneficiarios en esta jornada</p>
            <a href="<?= base_url("beneficiarios/buscar/$jornada_id") ?>" class="btn-registrar">+ Registrar primer beneficiario</a>
        </div>
    <?php endif; ?>
</div>

<!-- MODAL EVALUAR -->
<div class="modal fade" id="modalEvaluar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background:#101a61;color:#fff;">
                <h6 class="modal-title"><i class="bi bi-clipboard2-pulse me-2"></i>Evaluar: <span id="modalNombreBenef"></span></h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <p class="px-3 pt-3 text-muted" style="font-size:.82rem;">Selecciona la pesquisa a evaluar:</p>
                <ul class="pesquisa-modal-list" id="listaPesquisasModal"></ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const pesquisaInfo={
    '1':{img:'<?=base_url("img/antropometria2.svg")?>',nombre:'Antropometría',desc:'Peso, talla, IMC'},
    '2':{img:'<?=base_url("img/sanguinea2.svg")?>',nombre:'Laboratorio',desc:'Hemoglobina, glucosa'},
    '3':{img:'<?=base_url("img/optica2.svg")?>',nombre:'Visual',desc:'Agudeza visual'},
    '4':{img:'<?=base_url("img/signosVitales2.svg")?>',nombre:'Signos vitales',desc:'Tensión, temperatura, FC'},
    '5':{img:'<?=base_url("img/medicinaGeneral2.svg")?>',nombre:'Medicina general',desc:'Evaluación clínica'},
    '6':{img:'<?=base_url("img/vacunacion2.svg")?>',nombre:'Vacunación',desc:'Control de vacunas'},
};
const pesquisasJornada=<?=json_encode($pesquisas_jornada)?>;

function abrirEvaluar(bid,pid,nombre){
    document.getElementById('modalNombreBenef').textContent=nombre;
    const lista=document.getElementById('listaPesquisasModal');lista.innerHTML='';
    pesquisasJornada.forEach(p=>{
        const info=pesquisaInfo[p];if(!info)return;
        const li=document.createElement('li');
        li.innerHTML=`<img src="${info.img}"><div><div class="pesq-name">${info.nombre}</div><div class="pesq-desc">${info.desc}</div></div>`;
        li.onclick=()=>{
            Swal.fire({icon:'info',title:info.nombre,text:'Módulo de evaluación próximamente.',confirmButtonColor:'#101a61'});
            bootstrap.Modal.getInstance(document.getElementById('modalEvaluar')).hide();
        };
        lista.appendChild(li);
    });
    new bootstrap.Modal(document.getElementById('modalEvaluar')).show();
}

function confirmarRemover(j,b){
    Swal.fire({
        title:'¿Retirar beneficiario?',
        text:'Se retirará de esta jornada. Podrás volver a agregarlo después.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#dc3545',
        cancelButtonColor:'#6c757d',
        confirmButtonText:'Sí, retirar',
        cancelButtonText:'Cancelar'
    }).then(r=>{if(r.isConfirmed)window.location.href=`/jornadas/${j}/desasociar/${b}`;});
}

const fi=document.getElementById('filtrarBenef');
if(fi){fi.addEventListener('input',function(){const t=this.value.toLowerCase();document.querySelectorAll('.benef-card').forEach(c=>{c.style.display=(c.getAttribute('data-search')||'').includes(t)?'block':'none';});});}
</script>
<?= $this->endSection() ?>
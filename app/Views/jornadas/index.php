<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container my-4">

    <!-- BOTÓN CREAR (VA A LA PÁGINA) -->
    <div class="d-flex justify-content-start mb-3">
        <a href="<?= base_url('jornadas/crear') ?>" class="btn principal  ">
            + Crear Jornada
        </a>
    </div>

    <!-- ALERTA -->
    <?php if (session('success')): ?>
        <div class="alert alert-success"><?= session('success') ?></div>
    <?php endif; ?>

    <!-- LISTADO DE JORNADAS -->
    <?php if (!empty($jornadas)): ?>
        <?php foreach ($jornadas as $jor): ?>

            <div class="jornada-card">
                <div class="jornada-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0"><?= esc($jor['nombre_jornada']) ?></h5>
                        <small class="text-muted">
                              <?= esc($jor['nombre_org'] ?? 'Sin organización') ?>
                        </small>
                    </div>

                    <div class="text-end">
                        <span class="estado me-3">
                            <?= $jor['status_jor'] == 1 ? 'ACTIVA' : ($jor['status_jor'] == 2 ? 'FINALIZADA' : 'INACTIVA') ?>
                        </span>
                        <div class="fw-bold">
                            <small class="text-muted">
                                <?= date('d M Y', strtotime($jor['fecha_inicio'])) ?>
                            </small>
                        </div>
                    </div>
                </div>

                <div class="p-3">
                    <div class="iconos-pesquisas" style="padding: 6px 6px 10px 6px;">
                         <?php if (!empty($jor['pesquisas'])): ?>

    <?php 
        $iconos = [
            '1'   => 'antropometria2.svg',
            '2'   => 'sanguinea2.svg',
            '3'   => 'visual2.svg',
            '4'  => 'signosVitales2.svg',
            '5'  => 'medicinaGeneral2.svg',
            '6'  => 'vacunacion2.svg'
        ];
    ?>

    <?php foreach (explode(',', $jor['pesquisas']) as $p): ?>
        
        <?php 
            $p = trim(strtoupper($p));
            if (isset($iconos[$p])): 
        ?>
            <img src="<?= base_url('img/' . $iconos[$p]) ?>" width="36">
        <?php endif; ?>

    <?php endforeach; ?>

<?php endif; ?>

                    </div>
                </div>

                <div class="jornada-footer d-flex justify-content-between align-items-center px-3 pb-3 flex-wrap">
                  
                    
                    <p class="mb-2 small text-secondary  text-start">
                        <img src="<?= base_url('img/ubicacion-azul.svg') ?>" width="15">
                        <?= esc($jor['nombre_institucion'] ?? 'Sin institución') ?> /
                        <?= esc($jor['ciudad'] ?? 'Sin ubicación') ?> 
                    </p>
                       
                    <div class="d-flex flex-wrap gap-2">
                        <!-- Luego puedes cambiar esto a una página de edición -->
                        <button class="btn btn-outline-primary btn-sm" disabled>Editar</button>
                        <button class="btn btn-outline-primary btn-sm" disabled>Usuarios</button>
                        <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/beneficiarios') ?>" 
                        class="btn btn-outline-primary btn-sm">
                            Beneficiarios
                        </a>
                        <button class="btn btn-outline-primary btn-sm" disabled>Reportes</button>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info text-center mt-4">
            Crea tu primera jornada
        </div>
    <?php endif; ?>

</div>

<?= $this->endSection() ?>

<?php // SIN modal_form, SIN sección scripts para editar ?>

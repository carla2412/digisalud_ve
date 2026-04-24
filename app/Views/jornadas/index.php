<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container my-4">

    <!-- BOTÓN CREAR -->
    <div class="d-flex justify-content-start mb-3">
        <a href="<?= base_url('jornadas/crear') ?>" class="btn-primary-custom">
            + Crear Jornada
        </a>
    </div>

    <!-- ALERTA -->
    <?php if (session('success')): ?>
        <div class="alert alert-success auto-dismiss"><?= session('success') ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger auto-dismiss"><?= session('error') ?></div>
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
                        <?php
                            $estadoTexto = $jor['status_jor'] == 1 ? 'ACTIVA' : ($jor['status_jor'] == 2 ? 'FINALIZADA' : 'INACTIVA');
                            $estadoClase = $jor['status_jor'] == 1 ? 'text-success' : ($jor['status_jor'] == 2 ? 'text-danger' : 'text-secondary');
                        ?>

                        <span class="estado me-3 <?= $estadoClase ?>">
                            <?= $estadoTexto ?>
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
            '1'  => 'antropometria2.svg',
            '2'  => 'sanguinea2.svg',
            '3'  => 'visual2.svg',
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
                  
                    <p class="mb-2 small text-secondary text-start">
                        <img src="<?= base_url('img/ubicacion-azul.svg') ?>" width="15">
                        <?= esc($jor['nombre_institucion'] ?? 'Sin institución') ?> /
                        <?= esc($jor['ciudad'] ?? 'Sin ubicación') ?> 
                    </p>
                       
                    <div class="d-flex flex-wrap gap-2">
                        <?php if ($jor['status_jor'] != 2): ?>
                            <a href="<?= base_url('jornadas/editar/' . $jor['id_jornada']) ?>" 
                            class="btn btn-outline-primary btn-sm">
                                Editar
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary btn-sm" disabled>
                                Editar
                            </button>
                        <?php endif; ?>
                        <?php if (in_array(session('id_rol'), [1,2,3,4])): ?>
                            <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/usuarios') ?>" 
                            class="btn btn-outline-primary btn-sm">
                                Usuarios
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-primary btn-sm" disabled>Usuarios</button>
                        <?php endif; ?>
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
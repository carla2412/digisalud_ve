<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<div class="container py-4">

    <!-- Mensaje de éxito -->
    <?php if (session('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>


    <!-- CARD PRINCIPAL DEL PERFIL -->
    <div class="card shadow-lg mb-4 rounded-4 border-0">
        <div class="card-body">

            <div class="d-flex align-items-center mb-4">
                <img src="https://ui-avatars.com/api/?name=<?= urlencode($perfil['nombres'].' '.$perfil['apellidos']) ?>&size=120"
                     class="rounded-circle shadow-sm me-3">

                <div>
                    <h3 class="mb-0"><?= $perfil['nombres'] . ' ' . $perfil['apellidos'] ?></h3>
                    <h6 class="text-muted mb-1"><?= $perfil['descripcion_rol'] ?></h6>
                    <h3>  <span class="badge secundaria"><?= $perfil['nombre_org'] ?></span></h3>
                   
                </div>
            </div>

            <hr>

            <!-- FORMULARIO -->
            <form action="<?= site_url('perfil/actualizar') ?>" method="post" class="mt-3">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Nombres</label>
                        <input type="text" name="nombres" class="form-control" value="<?= $perfil['nombres'] ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Apellidos</label>
                        <input type="text" name="apellidos" class="form-control" value="<?= $perfil['apellidos'] ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Sexo</label>
                        <select name="genero" class="form-select">
                            <option value="M" <?= $perfil['genero']=='M'?'selected':'' ?>>Masculino</option>
                            <option value="F" <?= $perfil['genero']=='F'?'selected':'' ?>>Femenino</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="<?= $perfil['fecha_nacimiento'] ?>">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-bold">Profesión</label>
                        <select id="profesion" name="profesion" class=" form-control">
                            <?php if (!empty($perfil['profesion'])): ?>
                                <option value="<?= $perfil['profesion'] ?>" selected>
                                    <?= $perfil['profesion'] ?>
                                </option>
                            <?php endif; ?>
                        </select>


                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Correo electrónico</label>
                        <input type="email" name="email" class="form-control" value="<?= $perfil['email'] ?>">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="<?= $perfil['telefono'] ?>">
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn principal px-4">Actualizar perfil</button>
                </div>

            </form>
        </div>
    </div>

 <div class="card-header principal text-white rounded-top-4">
            <h5 class="mb-0 text-center">Has Participado en:  </h5>
        </div>
    <!-- ESTADÍSTICAS -->
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm rounded-4 border-0 text-center p-4">
                 
                <h2 class="fondo_principal"><?= $estadisticas['jornadas'] ?></h2>Jornadas
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm rounded-4 border-0 text-center p-4">
                
                <h2 class="fondo_secundaria"><?= $estadisticas['centros'] ?></h2>Centros
            </div>
        </div>
    </div>


    <!-- HISTORIAL DE JORNADAS -->
    <div class="card shadow-sm rounded-4 border-0 mb-4">
        <div class="card-header principal text-white rounded-top-4">
            <h5 class="mb-0  text-center ">Historial de Jornadas</h5>
        </div>

        <div class="card-body">
            <?php if ($estadisticas['detalle_jornadas']): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Jornada</th>
                        <th>Rol</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($estadisticas['detalle_jornadas'] as $j): ?>
                    <tr>
                        <td><?= $j['nombre_jornada'] ?></td>
                        <td><span class="badge bg-info text-dark"><?= $j['nombre_rol'] ?></span></td>
                        <td><?= date("d/m/Y", strtotime($j['fecha_asignacion'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="text-muted">No ha participado en jornadas.</p>
            <?php endif; ?>
        </div>
    </div>


    <!-- HISTORIAL DE CENTROS -->
    <div class="card shadow-sm rounded-4 border-0 mb-5">
        <div class="card-header principal text-white rounded-top-4">
            <h5 class="mb-0  text-center ">Historial de Centros</h5>
        </div>

        <div class="card-body">
            <?php if ($estadisticas['detalle_centros']): ?>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Centro</th>
                        <th>Rol</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($estadisticas['detalle_centros'] as $c): ?>
                    <tr>
                        <td><?= $c['nombre_centro'] ?></td>
                        <td><span class="badge bg-warning text-dark"><?= $c['nombre_rol'] ?></span></td>
                        <td><?= date("d/m/Y", strtotime($c['fecha_asignacion'])) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p class="text-muted">No ha participado en centros.</p>
            <?php endif; ?>
        </div>
    </div>

</div>
 
<?= $this->endSection() ?>
 
 <?= $this->section('scripts') ?>

<script>
$(document).ready(function () {
 
    const dataProfesiones = profesiones.map(p => ({
        id: p,
        text: p
    }));

    $("#profesion").select2({
        placeholder: "Escribe tu profesión...",
        allowClear: true,
        width: "100%",
        minimumInputLength: 1,
        dropdownAutoWidth: true,
        data: dataProfesiones
    });

});
</script>

<?= $this->endSection() ?>
 
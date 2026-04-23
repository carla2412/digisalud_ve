<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?= $this->section('css') ?>
<style>
 /* Colores y Variables de DigiSalud (Friendly Palette) */
:root {
    --ds-primary: #5c9cd8;
    --ds-teal: #1dbfb5;
    --ds-teal-light: #ecfdf5;
    --ds-bg-light: #f8fafc;
    --ds-card-friendly-shadow: rgba(0,0,0,0.03);
}

/* Base de la vista de Perfil */
.ds-profile-view {
    background-color: var(--ds-bg-light);
}

.text-primary-ds {
    color: var(--ds-primary) !important;
}

.text-teal {
    color: var(--ds-teal) !important;
}

/* Tarjetas Amigables */
.user-profile-card {
    border-radius: 18px;
    background-color: #ffffff;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.user-profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 18px var(--ds-card-friendly-shadow) !important;
}

/* Tarjetas de Estadística Compactas */
.ds-stat-card-friendly .card-title {
    font-size: 2.8rem;
    letter-spacing: -2px;
}

.ds-stat-card-friendly .card-text {
    line-height: 1.4;
    color: #8e9db0;
}

/* Avatar */
.avatar-refiled {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.avatar-refiled img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Badges */
.bg-teal-light {
    background-color: var(--ds-teal-light) !important;
}

/* Tamaños */
.fs-7 { font-size: 0.95rem; }
.fs-8 { font-size: 0.88rem; }
.fs-9 { font-size: 0.78rem; }

/* Botones */
.btn-primary-ds {
    background-color: var(--ds-primary);
    color: #fff;
    border: none;
}

.btn-primary-ds:hover {
    background-color: #4b8cc7;
    color: #fff;
}

.btn-outline-light-ds {
    color: var(--ds-primary);
    border-color: #dee2e6;
}

.btn-outline-light-ds:hover {
    color: var(--ds-teal);
    border-color: var(--ds-teal);
    background-color: var(--ds-teal-light);
}

/* Utilidades */
.shadow-xs {
    box-shadow: 0 1px 3px rgba(0,0,0,0.02) !important;
}
</style>
<?= $this->endSection() ?>
 <div class="container-fluid py-4 ds-profile-view">

    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
        <h2 class="text-primary-ds fw-bold m-0">Tu Perfil</h2>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fas fa-bell"></i>
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill shadow-sm">
                <i class="fas fa-cog"></i>
            </button>
        </div>
    </div>

    <!-- IMPORTANTE:
         enctype="multipart/form-data" para permitir subir archivos
         action => deja la ruta que ya uses en tu controlador -->
    <form action="<?= base_url('perfil/actualizar') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="row g-4 mb-4">
            <div class="col-xl-5 col-lg-6">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-4">
                    <div class="d-flex align-items-center mb-4">

                        <div class="avatar-refiled me-4 shadow-sm border border-light">
                            <?php
                                $foto = !empty($perfil['foto_usr'])
                                    ? base_url('uploads/foto_usr/'.$perfil['foto_usr'])
                                    : base_url('assets/images/placeholder-user.png');
                                ?>

                            <img src="<?= $foto ?>" class="img-fluid rounded-circle">
                          
                             
                        </div>

                        <div class="flex-grow-1">
                            <h4 class="card-title mb-0 fw-bold text-dark">
                                <?= esc($usuario['nombre'] ?? 'Usuario') ?>
                            </h4>

                            <small class="text-muted fs-7">
                                <?= esc($usuario['cargo'] ?? 'Sin cargo asignado') ?>
                            </small>

                            <div class="mt-2">
                                <span class="badge rounded-pill bg-teal-light text-teal px-3 py-2 border border-teal border-opacity-25 fs-8">
                                    <i class="fas fa-id-card me-1 small"></i>
                                    <?= esc($usuario['organizacion'] ?? 'Digisalud') ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Campo para subir foto -->
                    <div class="mb-3">
                        <label for="foto_usr" class="form-label fw-semibold fs-8 text-muted">
                            Foto de perfil
                        </label>
                        <input type="file" name="foto_usr" class="form-control" accept=".jpg,.jpeg,.png">
                        <small class="text-muted fs-9">
                            Formatos permitidos: JPG, JPEG, PNG
                        </small>

                        <?php if (session('errors.foto_usr')): ?>
                            <div class="text-danger fs-9 mt-1">
                                <?= session('errors.foto_usr') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary-ds">
                            Actualizar Foto de perfil
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-xl-7 col-lg-6">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="card-subtitle fs-8 fw-semibold text-muted text-uppercase m-0">
                            Mis Datos Principales
                        </h6>
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3 shadow-xs fs-8 text-teal">
                            <i class="fas fa-pencil ms-1 fs-9"></i> Editar
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-0 fs-8 text-muted">Nombre</p>
                            <p class="mb-0 fw-bold fs-7 text-dark">
                                <?= esc($usuario['nombre'] ?? '') ?>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p class="mb-0 fs-8 text-muted">Correo</p>
                            <p class="mb-0 fw-bold fs-7 text-dark text-break">
                                <?= esc($usuario['correo'] ?? '') ?>
                            </p>
                        </div>

                        <div class="col-md-6 mt-4">
                            <p class="mb-0 fs-8 text-muted">Teléfono</p>
                            <p class="mb-0 fw-bold fs-7 text-dark text-break">
                                <?= esc($usuario['telefono'] ?? '') ?>
                            </p>
                        </div>

                        <div class="col-md-12 mt-4">
                            <p class="mb-0 fs-8 text-muted">Profesión</p>
                            <span class="badge rounded-pill bg-light text-dark fs-8 px-3 py-2 mt-1 border">
                                <i class="fas fa-code me-1 fs-9 text-muted"></i>
                                <?= esc($usuario['profesion'] ?? 'No especificada') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4 row-cols-1 row-cols-md-2 row-cols-lg-4">
            <div class="col">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-3 ds-stat-card-friendly">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-8 fw-semibold text-dark m-0">Jornadas Participadas</span>
                        <i class="fas fa-clipboard-list text-muted fs-8"></i>
                    </div>
                    <h1 class="card-title fw-bolder text-teal m-0">
                        <?= esc($estadisticas['jornadas'] ?? 0) ?>
                    </h1>
                    <p class="card-text fs-8 text-muted mt-2">Resumen de tus jornadas registradas.</p>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-3 ds-stat-card-friendly">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-8 fw-semibold text-dark m-0">Centros Asociados</span>
                        <i class="fas fa-hospital text-muted fs-8"></i>
                    </div>
                    <h1 class="card-title fw-bolder text-primary-ds m-0">
                        <?= esc($estadisticas['centros'] ?? 0) ?>
                    </h1>
                    <p class="card-text fs-8 text-muted mt-2">Centros vinculados a tu actividad.</p>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-3 ds-stat-card-friendly">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-8 fw-semibold text-dark m-0">Historial Jornadas</span>
                        <i class="fas fa-history text-muted fs-8"></i>
                    </div>
                    <p class="card-text fs-8 text-muted mt-2">Consulta de actividad reciente.</p>
                </div>
            </div>

            <div class="col">
                <div class="card h-100 border-0 shadow-sm user-profile-card p-3 ds-stat-card-friendly">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <span class="fs-8 fw-semibold text-dark m-0">Historial Centros</span>
                        <i class="fas fa-history text-muted fs-8"></i>
                    </div>
                    <p class="card-text fs-8 text-muted mt-2">Seguimiento de centros asociados.</p>
                </div>
            </div>
        </div>
    </form>
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
 
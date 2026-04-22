<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
/* Contenedor centrado al 80% */
.container-custom {
    width: 80%;
    max-width: 1400px;
}

/* Tarjetas Mini */
.mini-card {
    border-radius: 12px;
    transition: all 0.3s ease;
}

.mini-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.08) !important;
}

.mini-logo {
    width: 50px;
    height: 50px;
    border: 2px solid #f8f9fa;
}

.small-badge {
    font-size: 10px;
    padding: 4px 8px;
}

.bg-soft-primary {
    background-color: #eef2ff;
}

.btn-action {
    font-size: 0.9rem;
    text-decoration: none;
}

/* Buscador */
.search-group {
    background: white;
    border: 1px solid #eee;
}

.search-group input:focus {
    box-shadow: none;
}

@media (max-width: 992px) {
    .container-custom {
        width: 95%; /* En móviles ocupa más espacio */
    }
}
</style>
 <div class="main-wrapper py-4">
    <div class="container-custom mx-auto">
        
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 small">
                        <li class="breadcrumb-item"><a href="<?= base_url('inicio') ?>" class="text-muted">Inicio</a></li>
                        <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">Organizaciones</li>
                    </ol>
                </nav>
                <h1 class="h3 text-dark fw-bold m-0">Organizaciones</h1>
            </div>
            <div class="col-md-6 text-md-end mt-3 mt-md-0">
                <a href="<?= base_url('organizaciones/crear') ?>" class="btn btn-primary rounded-pill shadow-sm btn-sm px-3">
                    <i class="fas fa-plus me-2"></i>Nueva Organización
                </a>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-12">
                <div class="input-group search-group shadow-sm rounded-pill">
                    <span class="input-group-text bg-white border-0 ps-3">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" id="searchOrg" class="form-control border-0 py-2" placeholder="Buscar organización por nombre, tipo o categoría...">
                </div>
            </div>
        </div>

        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-3" id="orgGrid">
            <?php foreach ($organizaciones as $org): ?>
                <div class="col org-item">
                    <div class="card h-100 border-0 shadow-sm mini-card <?= $org['status_org'] == 2 ? 'bg-light opacity-75' : '' ?>">
                        <div class="card-body p-3 text-center">
                            
                            <div class="mini-logo mx-auto mb-2 shadow-sm rounded-circle bg-white d-flex align-items-center justify-content-center">
                                <?php if (!empty($org['logo'])): ?>
                                    <img src="<?= base_url('uploads/logos/' . $org['logo']) ?>" class="img-fluid rounded-circle" alt="Logo">
                                <?php else: ?>
                                    <span class="text-primary fw-bold small"><?= strtoupper(substr($org['nombre_org'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>

                            <h6 class="fw-bold text-dark mb-1 text-truncate org-name"><?= $org['nombre_org'] ?></h6>
                            <span class="badge bg-soft-primary text-primary mb-2 small-badge">
                                <?= $org['tipo'] ?? 'ONG' ?>
                            </span>

                            <div class="contact-info-mini mb-3">
                                <p class="small text-muted m-0 text-truncate"><i class="fas fa-envelope me-1"></i><?= $org['correo'] ?></p>
                            </div>

                            <div class="d-flex justify-content-center gap-2 border-top pt-2">
                                <a href="<?= base_url('organizaciones/editar/' . $org['id_organizacion']) ?>" class="btn btn-link text-primary p-0 btn-action" title="Editar">
                                    <i class="fas fa-pen"></i>
                                </a>
                                
                                <?php if ($org['status_org'] == 2): ?>
                                    <button class="btn btn-link text-success p-0 btn-action" onclick="cambiarStatus(<?= $org['id_organizacion'] ?>, 1)" title="Desbloquear">
                                        <i class="fas fa-unlock"></i>
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-link text-warning p-0 btn-action" onclick="cambiarStatus(<?= $org['id_organizacion'] ?>, 2)" title="Bloquear">
                                        <i class="fas fa-ban"></i>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>
<!-- {{-- Auto-dismiss flash messages en 5 segundos --}} -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const alerts = document.querySelectorAll('.alert.auto-dismiss');
        alerts.forEach(function (alert) {
            setTimeout(function () {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
                bsAlert.close();
            }, 5000);
        });
    });
</script>

<?= $this->endSection() ?>
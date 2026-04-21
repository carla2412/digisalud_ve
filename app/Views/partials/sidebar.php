<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        DIGISALUD
        <span id="closeSidebar" class="close-btn">&times;</span>
    </div>

    <ul>
        <li>
            <a href="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/digisaludMenu2.svg') ?>" alt="Inicio" width="30" height="30">
                <span>Inicio</span>
            </a>
            </li>

            <li><a href="#" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" alt="Beneficiarios" width="30" height="30">
                    <span>Beneficiarios</span>
                </a>
            </li>
            <li><a href="#" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/centro.svg') ?>" alt="Mis Centros" width="30" height="30">
                    <span>Mis Centros</span>
                </a>
            </li>
        <li><a  href="<?= site_url('jornadas') ?>" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/jornada-outline-azul.svg') ?>" alt="Mis Jornadas" width="30" height="30">
                    <span>Mis Jornadas</span>
                </a>
            </li>
        
        <li><a  href="<?= site_url('usuarios') ?>" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/usuarios.svg') ?>" alt="Usuarios" width="30" height="30">
                    <span>Usuarios</span>
                </a>
            </li>
        
    </ul>
</div>

<div class="overlay" id="overlay"></div>

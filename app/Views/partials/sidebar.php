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
 

<?php
// Obtener rol de la sesión activa icon_home_off.png
$rolActual = (int) session()->get('id_rol');
?>

<?php if (in_array($rolActual, [1, 2, 3], true)) : ?>
 
    <li class="nav-item <?= (uri_string() === 'organizaciones' || str_starts_with(uri_string(), 'organizaciones')) ? 'active' : '' ?>">
        <a class="nav-link <?= str_starts_with(uri_string(), 'organizaciones') ? 'active' : '' ?>"
           href="<?= base_url('organizaciones') ?>">
            <img src="<?= base_url('img/icon_home_off.png') ?>" alt="Usuarios" width="30" height="30">
            <span>Organizaciones</span>
        </a>
    </li>
<?php endif; ?>

 

<?php /*
<?php if (in_array((int) session()->get('id_rol'), [1, 2, 3], true)) : ?>
    <li class="<?= ($currentPage === 'organizaciones') ? 'active' : '' ?>">
        <a href="<?= base_url('organizaciones') ?>">
            <i class="fa fa-building"></i>
            <span>Organizaciones</span>
        </a>
    </li>
<?php endif; ?>
*/ ?>
        
    </ul>
</div>

<div class="overlay" id="overlay"></div>

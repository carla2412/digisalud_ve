<?php
$rolActual = (int) session()->get('id_rol');
$currentUri = uri_string();
?>

<div class="sidebar" id="sidebar">
    <div class="sidebar-header">
        DIGISALUD
        <span id="closeSidebar" class="close-btn">&times;</span>
    </div>

    <ul>
        <li class="<?= $currentUri === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= site_url('dashboard') ?>" class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/digisaludMenu2.svg') ?>" alt="Inicio" width="30" height="30">
                <span>Inicio</span>
            </a>
        </li>

        <?php if (in_array($rolActual, [1, 2, 3], true)) : ?>
            <li class="<?= str_starts_with($currentUri, 'beneficiarios') ? 'active' : '' ?>">
                <a href="<?= site_url('beneficiarios') ?>" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" alt="Beneficiarios" width="30" height="30">
                    <span>Beneficiarios</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="<?= str_starts_with($currentUri, 'centros') ? 'active' : '' ?>">
            <a href="<?= site_url('centros') ?>" class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/centro.svg') ?>" alt="Mis Centros" width="30" height="30">
                <span>Mis Centros</span>
            </a>
        </li>

        <li class="<?= str_starts_with($currentUri, 'jornadas') ? 'active' : '' ?>">
            <a href="<?= site_url('jornadas') ?>" class="d-flex align-items-center gap-2">
                <img src="<?= base_url('img/jornada-outline-azul.svg') ?>" alt="Mis Jornadas" width="30" height="30">
                <span>Mis Jornadas</span>
            </a>
        </li>

        <?php if (in_array($rolActual, [1, 2], true)) : ?>
            <li class="<?= str_starts_with($currentUri, 'usuarios') ? 'active' : '' ?>">
                <a href="<?= site_url('usuarios') ?>" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/usuarios.svg') ?>" alt="Usuarios" width="30" height="30">
                    <span>Usuarios</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (in_array($rolActual, [1, 2, 3], true)) : ?>
            <li class="<?= str_starts_with($currentUri, 'organizaciones') ? 'active' : '' ?>">
                <a href="<?= base_url('organizaciones') ?>" class="d-flex align-items-center gap-2">
                    <img src="<?= base_url('img/icon_home_off.png') ?>" alt="Organizaciones" width="30" height="30">
                    <span>Organizaciones</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>

<div class="overlay" id="overlay"></div>
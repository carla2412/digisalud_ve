<?php
$rolActual = (int) session()->get('id_rol');
$currentUri = uri_string();
?>

<div class="sidebar ds-sidebar" id="sidebar">
    <div class="ds-sidebar-header">
        <div class="ds-brand">
            <span class=""><img src="<?= base_url('img/icon/Isotipo_digisalud.png') ?>" alt="Inicio" height="26"></span>
            <span>Digisalud</span>
        </div>

        <span id="closeSidebar" class="ds-close-btn">&times;</span>
    </div>

    <ul class="ds-menu">
        <li class="ds-menu-item <?= $currentUri === 'dashboard' ? 'active' : '' ?>">
            <a href="<?= site_url('dashboard') ?>">
                <span class="ds-icon ds-icon-blue">
                    <img src="<?= base_url('img/digisaludMenu2.svg') ?>" alt="Inicio">
                </span>
                <span>Inicio</span>
            </a>
        </li>

        <?php if (in_array($rolActual, [1, 2, 3], true)) : ?>
            <li class="ds-menu-item <?= str_starts_with($currentUri, 'beneficiarios') ? 'active' : '' ?>">
                <a href="<?= site_url('beneficiarios') ?>">
                    <span class="ds-icon ds-icon-green">
                        <img src="<?= base_url('img/beneficiario-evaluado-azul.svg') ?>" alt="Beneficiarios">
                    </span>
                    <span>Beneficiarios</span>
                    <span class="ds-arrow">›</span>
                </a>
            </li>
        <?php endif; ?>

        <li class="ds-menu-item <?= str_starts_with($currentUri, 'centros') ? 'active' : '' ?>">
            <a href="<?= site_url('centros') ?>">
                <span class="ds-icon ds-icon-purple">
                    <img src="<?= base_url('img/centro.svg') ?>" alt="Mis Centros">
                </span>
                <span>Mis Centros</span>
                <span class="ds-arrow">›</span>
            </a>
        </li>

        <li class="ds-menu-item <?= str_starts_with($currentUri, 'jornadas') ? 'active' : '' ?>">
            <a href="<?= site_url('jornadas') ?>">
                <span class="ds-icon ds-icon-yellow">
                    <img src="<?= base_url('img/jornada-outline-azul.svg') ?>" alt="Mis Jornadas">
                </span>
                <span>Mis Jornadas</span>
                <span class="ds-arrow">›</span>
            </a>
        </li>
        <?php if (in_array($rolActual, [1, 2, 3, 4], true)) : ?>
            <li class="ds-menu-item <?= str_starts_with($currentUri, 'cargas-masivas') ? 'active' : '' ?>">
                <a href="<?= site_url('cargas-masivas') ?>">
                    <span class="ds-icon ds-icon-blue">
                        <i class="bi bi-cloud-arrow-up" style="font-size:20px;"></i>
                    </span>
                    <span>Carga masiva</span>
                    <span class="ds-arrow">›</span>
                </a>
            </li>
        <?php endif; ?>
        <?php if (in_array($rolActual, [1, 2, 3, 4, 5, 6, 7], true)) : ?>
            <li class="ds-menu-item <?= str_starts_with($currentUri, 'usuarios') ? 'active' : '' ?>">
                <a href="<?= site_url('usuarios') ?>">
                    <span class="ds-icon ds-icon-violet">
                        <img src="<?= base_url('img/usuarios.svg') ?>" alt="Usuarios">
                    </span>
                    <span>Usuarios</span>
                    <span class="ds-arrow">›</span>
                </a>
            </li>
        <?php endif; ?>

        <?php if (in_array($rolActual, [1, 2, 3], true)) : ?>
            <li class="ds-menu-item <?= str_starts_with($currentUri, 'organizaciones') ? 'active' : '' ?>">
                <a href="<?= base_url('organizaciones') ?>">
                    <span class="ds-icon ds-icon-blue">
                        <img src="<?= base_url('img/icon_home_off.png') ?>" alt="Organizaciones">
                    </span>
                    <span>Organizaciones</span>
                    <span class="ds-arrow">›</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="ds-sidebar-footer">
        <!--<a href="#" class="ds-footer-link">
            <span class="ds-footer-icon">?</span>
            <span>Ayuda</span>
        </a> 
        -->

        <a href="<?= site_url('logout') ?>" class="ds-footer-link">
            <span>↪</span>
            <span>Cerrar sesión</span>
        </a>
    </div>
</div>

<div class="overlay" id="overlay"></div>
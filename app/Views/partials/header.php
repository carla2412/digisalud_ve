<header class="text-white py-2 px-3">
    <div class="container-fluid d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center gap-2">

            <!--  <button class="btn" id="menuToggle">
                <img src="<?= base_url('img/icon/icon_menu.png') ?>" width="22">
            </button>-->
            <button id="openSidebar" type="button" class="btn btn-outline-light">
                ☰
            </button>
            <button class="btn" onclick="window.history.back();">
                <img src="<?= base_url('img/icon/icon_back.png') ?>" width="35">
            </button>

            <!-- <div class="input-group input-group-sm ms-2" style="width:180px;">
                <span class="input-group-text bg-light border-0">
                    <img src="<?= base_url('img/icon/search-blue.png') ?>" width="16">
                </span>
                <input type="text" class="form-control border-0" placeholder="Búsqueda">
            </div> -->

        </div>

        <img src="<?= base_url('img/isotipo_digisalud.png') ?>" height="60">

        <div class="d-flex align-items-center gap-3">

            <button class="btn position-relative">
                <img src="<?= base_url('img/icon/icon_notification.png') ?>" width="35">
            </button>

            <div class="text-end d-none d-sm-block">
                <h4 class="mb-0 text-white"><?= session('nombre_completo') ?></h4>
                <h6 class="text-light opacity-75"><?= session('nombre_org') ?></h6>
            </div>

            <div class="dropdown">
                <a class="d-flex align-items-center dropdown-toggle text-white" data-bs-toggle="dropdown">
<?php
$fotoUrl = session('foto_url');

$avatar = !empty($fotoUrl)
    ? base_url($fotoUrl)
    : base_url('img/avatar-default.jpg');
?>

<img 
    src="<?= esc($avatar) ?>" 
    width="36" 
    height="36"
    class="rounded-circle object-fit-cover"
    alt="Foto de perfil"
>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="<?= site_url('perfil') ?>">
                            Perfil
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= site_url('logout') ?>">Cerrar sesión</a></li>
                </ul>
            </div>

        </div>

    </div>
</header>

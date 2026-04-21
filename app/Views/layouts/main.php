<!DOCTYPE html>
<html lang="es">
<head>
    <!-- INICIO LAYOUT MAIN -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Usuarios | Digisalud') ?></title>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <!-- CSS propio -->
    <link rel="stylesheet" href="<?= base_url('css/app.css') ?>">
    <!-- DataTables 2 + Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/v/bs5/dt-2.0.0/datatables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
     <!-- FIN LAYOUT MAIN -->
    <?= $this->renderSection('css') ?>
</head>

<body>

    <?= $this->include('partials/header') ?>
    <?= $this->include('partials/sidebar') ?>

    <main class="container-fluid py-4">
        <?= $this->renderSection('content') ?>
    </main>
   <?= $this->include('partials/footer') ?>
    <!-- Tus scripts22 personalizados -->
    <?= $this->renderSection('scripts') ?>

 
</body>
</html>


<div class="container mt-4">

<h3>Nuevo beneficiario</h3>

<form method="post" action="/beneficiarios/store/<?= $jornada_id ?>">

<?php echo view('beneficiarios/form') ?>

<button class="btn btn-success mt-3">
Guardar
</button>

</form>

</div>
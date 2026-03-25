<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>


<div class="container mt-4">

<h3>Beneficiarios de la jornada</h3>

<a href="/beneficiarios/buscar" class="btn btn-primary mb-3">
+ Agregar beneficiario
</a>

<table class="table table-bordered">

<thead>
<tr>
<th>ID DIGISALUD</th>
<th>Nombres</th>
<th>Apellidos</th>
</tr>
</thead>

<tbody>

<?php foreach($beneficiarios as $b): ?>

<tr>
<td><?= $b['id_digisalud'] ?></td>
<td><?= $b['nombres'] ?></td>
<td><?= $b['apellidos'] ?></td>
</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>



<?= $this->endSection() ?>
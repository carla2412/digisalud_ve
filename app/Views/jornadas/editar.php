<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>

<style>
/* Reutilizar estilos de crear */
</style>

<?= $this->endSection() ?>
<?= $this->section('content') ?>

<div class="container mt-5">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-4 text-center">Editar Jornada</h4>

    <!-- Mostrar errores de validación -->
    <?php if (session('errors')): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach (session('errors') as $err): ?>
            <li><?= esc($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (session('error')): ?>
      <div class="alert alert-danger"><?= session('error') ?></div>
    <?php endif; ?>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/actualizar') ?>" novalidate>
      <?= csrf_field() ?>

      <!-- Campo oculto: ID de la jornada -->
      <input type="hidden" name="id_jornada" value="<?= esc($jornada['id_jornada']) ?>">

      <!-- Fecha -->
      <div class="mb-3">
        <label class="form-label">Fecha</label>
        <input type="date" class="form-control" name="fecha_inicio" 
               value="<?= esc($jornada['fecha_inicio']) ?>" required>
      </div>

      <!-- Nombre de jornada -->
      <div class="mb-3">
        <label class="form-label">Nombre de la Jornada</label>
        <input type="text" class="form-control" name="nombre_jornada" 
               value="<?= esc($jornada['nombre_jornada']) ?>" required>
      </div>

      <!-- Organización -->
      <div class="mb-3">
        <label class="form-label">Nombre de la Organización</label>

        <select class="form-select" name="organizacion_id" 
                <?= $soloLectura ? 'disabled' : '' ?> required>

          <?php foreach ($organizaciones as $o): ?>
            <option value="<?= $o['id_organizacion'] ?>"
              <?= ($o['id_organizacion'] == $jornada['organizacion_id']) ? 'selected' : '' ?>
            >
              <?= esc($o['nombre_org']) ?>
            </option>
          <?php endforeach; ?>

        </select>

        <!-- Si está deshabilitado, enviar valor oculto -->
        <?php if ($soloLectura): ?>
          <input type="hidden" name="organizacion_id" value="<?= esc($jornada['organizacion_id']) ?>">
        <?php endif; ?>
      </div>

      <!-- Pesquisas (checkboxes con in_array para marcar las existentes) -->
      <div class="mb-3">
        <label class="form-label">Seleccionar Pesquisa (al menos una)</label><br>

        <?php foreach ($pesquisas as $p): ?>
          <label class="form-check form-check-inline">
            <input 
              class="form-check-input" 
              type="checkbox" 
              name="pesquisas[]" 
              value="<?= $p['idtipo_pesquisa'] ?>"
              <?= in_array($p['idtipo_pesquisa'], $pesquisasSeleccionadas) ? 'checked' : '' ?>
            >
            <?= ucfirst(strtolower(esc($p['descripcion_view']))) ?>
          </label>
        <?php endforeach; ?>

        <div class="text-danger mt-2" id="pesquisaError" style="display:none;">
          Selecciona al menos una pesquisa.
        </div>
      </div>

      <!-- Botones -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-4">Actualizar</button>
        <a href="<?= base_url('jornadas') ?>" class="btn btn-secondary px-4 ms-2">Cancelar</a>
      </div>

    </form>
  </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<script>
// Validación de pesquisas antes de enviar
document.getElementById('formJornada').addEventListener('submit', function (e) {
    const checks = document.querySelectorAll("input[name='pesquisas[]']:checked");
    if (checks.length === 0) {
        e.preventDefault();
        document.getElementById('pesquisaError').style.display = 'block';
    } else {
        document.getElementById('pesquisaError').style.display = 'none';
    }
});
</script>

<!-- SweetAlert éxito -->
<?php if (session('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: '<?= esc(session('success')) ?>',
    confirmButtonText: 'OK'
}).then(() => {
    window.location.href = "<?= base_url('jornadas') ?>";
});
</script>
<?php endif; ?>

<?= $this->endSection() ?>
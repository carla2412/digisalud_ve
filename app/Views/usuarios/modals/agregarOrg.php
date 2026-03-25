<!-- MODAL AGREGAR A ORGANIZACIÓN -->
<div class="modal fade" id="modalAgregarOrg">
  <div class="modal-dialog">
    <form id="formAgregarOrg" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Usuario a Organización</h5>
        <button class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <input type="hidden" id="agregarId">

        <label class="form-label">Seleccione Organización</label>
        <select class="form-select" id="selectOrg" required>
          <option value="">-- Seleccione --</option>
          <?php foreach($organizaciones as $org): ?>
            <option value="<?= $org['id_organizacion'] ?>">
              <?= $org['nombre_org'] ?>
            </option>
          <?php endforeach; ?>
        </select>

        <label class="form-label mt-3">Seleccione Rol</label>
        <select class="form-select" id="selectRol" required name="id_rol">
    <option value="">-- Seleccione --</option>
    <?php foreach($roles as $r): ?>
        <?php 
            // Ejemplo: No mostrar el rol con ID 1 (Admin) 
            // o solo mostrar si la descripción no es 'SuperUsuario'
            if ($r['id_rol'] != 1 && $r['id_rol'] != 2 && $r['id_rol'] != 3): 
        ?>
            <option value="<?= $r['id_rol'] ?>">
                <?= $r['descripcion_rol'] ?>
            </option>
        <?php endif; ?>
    <?php endforeach; ?>
</select>

      </div>

      <div class="modal-footer">
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

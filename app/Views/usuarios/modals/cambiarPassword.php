<!-- MODAL CAMBIAR CONTRASEÑA -->
<div class="modal fade" id="modalPassword" tabindex="-1">
  <div class="modal-dialog">
    <form id="formPassword" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar contraseña del usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <input type="hidden" id="passId">

          <div class="mb-3">
              <label class="form-label">Nueva contraseña</label>
              <input type="password" class="form-control" id="nuevoPass" required>
          </div>

          <div class="mb-3">
              <label class="form-label">Confirmar contraseña</label>
              <input type="password" class="form-control" id="confirmPass" required>
          </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

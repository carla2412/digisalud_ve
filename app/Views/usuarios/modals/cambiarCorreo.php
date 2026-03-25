<!-- MODAL CAMBIAR CORREO -->
<div class="modal fade" id="modalCorreo" tabindex="-1">
  <div class="modal-dialog">
    <form id="formCorreo" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar correo del usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <input type="hidden" id="correoId">

          <div class="mb-3">
              <label class="form-label">Nuevo correo</label>
              <input type="email" class="form-control" id="nuevoCorreo" required placeholder="correo@ejemplo.com">
          </div>
      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

<!-- MODAL CONFIRMAR BLOQUEO -->
<div class="modal fade" id="modalBloqueo" tabindex="-1">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Bloquear Usuario</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
          <p id="textoBloqueo" class="text-center"></p>
          <input type="hidden" id="bloqueoId">
      </div>

      <div class="modal-footer d-flex justify-content-between">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button id="btnConfirmBloqueo" class="btn  btnalgo2">Aceptar</button>
      </div>
    </div>
  </div>
</div>

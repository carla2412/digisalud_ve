<div class="modal fade" id="modalLocalidad" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content p-3">

      <div class="modal-header">
        <h5 class="modal-title">Buscar o Crear Localidad</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <input type="text" id="searchPlace" class="form-control mb-3" placeholder="Buscar lugar...">

        <div id="map" style="width:100%;height:350px;border-radius:8px;"></div>

        <div class="row mt-3">
          <div class="col-md-6">
            <label class="form-label">País</label>
            <input type="text" id="modal_pais" class="form-control" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label">Estado</label>
            <input type="text" id="modal_estado" class="form-control" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label">Ciudad</label>
            <input type="text" id="modal_ciudad" class="form-control" readonly>
          </div>

          <div class="col-md-6">
            <label class="form-label">Coordenadas</label>
            <input type="text" id="modal_coords" class="form-control" readonly>
          </div>
        </div>

      </div>

      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button class="btn btn-success" id="btnUsarLocalidad" data-bs-dismiss="modal">Usar esta ubicación</button>
      </div>

    </div>
  </div>
</div>

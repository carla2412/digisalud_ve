<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>

<!-- SOLO ESTA PÁGINA: Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
#map { height: 350px; border-radius: 8px; width: 100%; }
</style>

<?= $this->endSection() ?>
<?= $this->section('content') ?>


<div class="container mt-5">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-4 text-center">Crear Jornada</h4>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/guardar') ?>" novalidate>
      <?= csrf_field() ?>

      <!-- Fecha -->
      <div class="mb-3">
        <label class="form-label">Fecha</label>
        <input type="date" class="form-control" name="fecha_inicio" required>
      </div>

      <!-- Nombre de jornada -->
      <div class="mb-3">
        <label class="form-label">Nombre de la Jornada</label>
        <input type="text" class="form-control" name="nombre_jornada" required>
      </div>

      <!-- Organización -->
      <div class="mb-3">
            <label class="form-label">Nombre de la Organización</label>

            <select class="form-select" name="organizacion_id" 
                    <?= $soloLectura ? 'disabled' : '' ?> required>

                <?php foreach ($organizaciones as $o): ?>
                    <option value="<?= $o['id_organizacion'] ?>"
                        <?= ($o['id_organizacion'] == $orgSesion) ? 'selected' : '' ?>
                    >
                        <?= $o['nombre_org'] ?>
                    </option>
                <?php endforeach; ?>

            </select>

            <!-- Importante: si está deshabilitado, enviar valor oculto -->
            <?php if ($soloLectura): ?>
                <input type="hidden" name="organizacion_id" value="<?= $orgSesion ?>">
            <?php endif; ?>
        </div>

      <!-- Localidad + Mapa -->
      <div class="mb-3">
        <label class="form-label">Institución o Localidad</label>
        <div class="input-group">
          <input type="text" class="form-control" id="localidad" name="localidad">
          <span class="input-group-text" data-bs-toggle="modal" data-bs-target="#modalLocalidad">🔍</span>
        </div>
      </div>

      <!-- Campos ocultos del mapa -->
      <input type="hidden" name="pais" id="pais">
      <input type="hidden" name="estado" id="estado">
      <input type="hidden" name="ciudad" id="ciudad">
      <input type="hidden" name="coords" id="coords">

      <!-- Tipo de jornada -->
      <div class="mb-3">
        <label class="form-label">Tipo de Jornada</label>
        <div class="d-flex gap-3">
          <label><input type="radio" name="tipo_jornada" value="publica" required> Pública</label>
          <label><input type="radio" name="tipo_jornada" value="privada"> Privada</label>
        </div>
      </div>

     <div class="mb-3">
    <label class="form-label">Seleccionar Pesquisa (al menos una)</label><br>

    <?php foreach ($pesquisas as $p): ?>
        <label class="form-check form-check-inline">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="pesquisas[]" 
                value="<?= $p['idtipo_pesquisa'] ?>"
            >
            <?= ucfirst(strtolower($p['descripcion_view'])) ?>
        </label>
    <?php endforeach; ?>

    <div class="text-danger mt-2" id="pesquisaError" style="display:none;">
        Selecciona al menos una pesquisa.
    </div>
</div>


      <!-- Botones -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-4">Guardar</button>
        <a href="<?= base_url('jornadas') ?>" class="btn btn-secondary px-4 ms-2">Cancelar</a>
      </div>

    </form>
  </div>
</div>

<!-- MODAL MAPA (exacto al HTML original) -->
<?= $this->include('jornadas/modal_localidad') ?>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<!-- SOLO ESTA PÁGINA: Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

 <script>
let map, marker;

/* ==========================================================
   INICIALIZACIÓN DEL MAPA AL ABRIR EL MODAL
   ========================================================== */
const modal = document.getElementById('modalLocalidad');

modal.addEventListener('shown.bs.modal', () => {

    if (!map) {
        // Centro inicial Caracas
        const initialLat = 10.4806;
        const initialLon = -66.9036;

        // Crear mapa
        map = L.map('map').setView([initialLat, initialLon], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Crear marcador
        marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

        // Evento: mover marcador con el mouse (drag)
        marker.on('drag', function (e) {
            const pos = e.target.getLatLng();
            updateAddress(pos.lat, pos.lng, false); // en vivo sin reverse geocoding completo
        });

        // Evento: soltar marcador (dragend) → reverse geocoding completo
        marker.on('dragend', function (e) {
            const pos = e.target.getLatLng();
            updateAddress(pos.lat, pos.lng, true);
        });

        // Evento: clic en el mapa → mover marcador ahí
        map.on('click', function (e) {
            const { lat, lng } = e.latlng;
            marker.setLatLng([lat, lng]);
            updateAddress(lat, lng, true);
        });

        // Buscar por texto
        document.getElementById('searchPlace').addEventListener('keypress', async (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                const query = e.target.value.trim();
                if (query.length < 3) return;

                const url = `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`;
                const results = await fetch(url).then(r => r.json());

                if (results.length > 0) {
                    const { lat, lon } = results[0];

                    map.setView([lat, lon], 15);
                    marker.setLatLng([lat, lon]);

                    updateAddress(lat, lon, true);
                }
            }
        });
    }

    // Arreglar Leaflet dentro del modal
    setTimeout(() => map.invalidateSize(true), 300);
});

const estadosVE = {
    "Miranda State": "Miranda",
    "Miranda": "Miranda",
    "Capital District": "Distrito Capital",
    "Distrito Capital": "Distrito Capital",
    "Vargas": "La Guaira",
    "Vargas State": "La Guaira",
    "La Guaira State": "La Guaira",
    "Zulia State": "Zulia",
    "Aragua State": "Aragua",
    "Carabobo State": "Carabobo",
    "Lara State": "Lara",
    "Anzoategui State": "Anzoátegui",
    "Bolivar State": "Bolívar",
    "Táchira State": "Táchira",
    "Yaracuy State": "Yaracuy",
    "Sucre State": "Sucre",
    "Falcon State": "Falcón",
    "Guarico State": "Guárico",
    "Apure State": "Apure",
    "Amazonas State": "Amazonas",
    "Barinas State": "Barinas",
    "Cojedes State": "Cojedes",
    "Delta Amacuro State": "Delta Amacuro",
    "Monagas State": "Monagas",
    "Merida State": "Mérida",
    "Nueva Esparta State": "Nueva Esparta",
    "Portuguesa State": "Portuguesa",
    "Trujillo State": "Trujillo"
};

/* ==========================================================
   FUNCIÓN PARA ACTUALIZAR CAMPOS
   reverseGeocode = true → llama a la API de Nominatim
   reverseGeocode = false → solo actualiza coordenadas
   ========================================================== */
async function updateAddress(lat, lon, reverseGeocode = true) {

    document.getElementById('modal_coords').value = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;
    document.getElementById('coords').value = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;

    if (!reverseGeocode) return;

    try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
        const data = await fetch(url).then(r => r.json());
        const addr = data.address || {};

        const pais   = addr.country || '';
        let estado   = addr.state || addr.region || '';
        const ciudad = addr.city || addr.town || addr.village || '';

        // TRADUCCIÓN DE ESTADOS DE VENEZUELA
        if (estadosVE[estado]) {
            estado = estadosVE[estado];
        }

        // Mostrar en el MODAL
        document.getElementById('modal_pais').value   = pais;
        document.getElementById('modal_estado').value = estado;
        document.getElementById('modal_ciudad').value = ciudad;

        // Guardar en FORMULARIO PRINCIPAL
        document.getElementById('pais').value   = pais;
        document.getElementById('estado').value = estado;
        document.getElementById('ciudad').value = ciudad;

        // Rellenar campo "Localidad"
        const inputLocalidad = document.getElementById('localidad');
         

    } catch (err) {
        console.error('Reverse geocoding error:', err);
    }
}

</script>

<script>
// Validación de pesquisas
document.getElementById('formJornada').addEventListener('submit', function (e) {
    const checks = document.querySelectorAll("input[name='pesquisas[]']:checked");
    if (checks.length === 0) {
        e.preventDefault();
        document.getElementById('pesquisaError').style.display = 'block';
    }
});
</script>

<!-- SweetAlert éxito -->
<?php if (session('success')): ?>
<script>
Swal.fire({
    icon: 'success',
    title: 'Jornada creada correctamente',
    confirmButtonText: 'OK'
}).then(() => {
    window.location.href = "<?= base_url('jornadas') ?>";
});
</script>
<?php endif; ?>

<?= $this->endSection() ?>

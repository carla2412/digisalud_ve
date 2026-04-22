<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>

<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
#map { height: 350px; border-radius: 8px; width: 100%; }

/* ── Pesquisa selector con iconos ── */
.pesquisa-selector {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    margin-top: 8px;
}
.pesquisa-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: pointer;
    width: 80px;
    text-align: center;
}
.pesquisa-item input[type="checkbox"] {
    display: none;
}
.pesquisa-icon-wrap {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    border: 3px solid #dee2e6;
    background: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.25s ease;
    padding: 0;
}
.pesquisa-icon-wrap img {
    width: 34px;
    height: 34px;
}
.pesquisa-icon-wrap .icon-color { display: none; }
.pesquisa-icon-wrap .icon-gris  { display: block; }

/* Cuando está checkeado */
.pesquisa-item input:checked + .pesquisa-icon-wrap {
    border-color: #3695f5;
    background: #e8eaf8;
    transform: scale(1.1);
    box-shadow: 0 2px 8px rgba(54, 149, 245, 0.3);
}
.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-color { display: block; }
.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-gris  { display: none; }

.pesquisa-label {
    font-size: 0.72rem;
    font-weight: 600;
    color: #555;
    margin-top: 6px;
    line-height: 1.1;
}
.pesquisa-item input:checked ~ .pesquisa-label {
    color: #101a61;
}
</style>

<?= $this->endSection() ?>
<?= $this->section('content') ?>

<?php
    // Definir iconos de pesquisas
    $iconos_color = [
        '1' => ['color' => 'antropometria-color.svg', 		'gris' => 'antropometria2.svg',          'nombre' => 'Antropometría'],
        '2' => ['color' => 'sanguinea-color.svg',  			'gris' => 'sanguinea2.svg',              'nombre' => 'Laboratorio'],
        '3' => ['color' => 'visual-color.svg',				'gris' => 'visual2.svg',                 'nombre' => 'Visual'],
        '4' => ['color' => 'signos-vitales-color.svg',		'gris' => 'signosVitales2.svg',          'nombre' => 'Signos vitales'],
        '5' => ['color' => 'medicina-general-color.svg',	'gris' => 'medicinaGeneral2.svg',        'nombre' => 'Medicina general'],
        '6' => ['color' => 'vacunacion-color.svg', 			'gris' => 'vacunacion2.svg',             'nombre' => 'Vacunación'],
    ];

    // Parsear coordenadas existentes para centrar el mapa
    $lat = 10.4806;
    $lon = -66.9036;
    if (!empty($jornada['coordenadas'])) {
        $parts = explode(',', $jornada['coordenadas']);
        if (count($parts) == 2) {
            $lat = floatval(trim($parts[0]));
            $lon = floatval(trim($parts[1]));
        }
    }
?>

<div class="container mt-5 mb-5">
  <div class="card p-4 shadow-sm">
    <h4 class="mb-4 text-center">Editar Jornada</h4>

    <!-- Errores de validación -->
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

      <!-- Campo oculto: ID -->
      <input type="hidden" name="id_jornada" value="<?= esc($jornada['id_jornada']) ?>">

      <!-- ═══ STATUS ═══ -->
      <div class="mb-3">
        <label class="form-label">Estado de la Jornada</label>
        <select class="form-select" name="status_jor" required>
          <option value="1" <?= ($jornada['status_jor'] == 1) ? 'selected' : '' ?>>Activa</option>
          <option value="2" <?= ($jornada['status_jor'] == 2) ? 'selected' : '' ?>>Finalizada</option>
        </select>
      </div>

      <!-- ═══ FECHA ═══ -->
      <div class="mb-3">
        <label class="form-label">Fecha</label>
        <input type="date" class="form-control" name="fecha_inicio" 
               value="<?= esc($jornada['fecha_inicio']) ?>" required>
      </div>

      <!-- ═══ NOMBRE ═══ -->
      <div class="mb-3">
        <label class="form-label">Nombre de la Jornada</label>
        <input type="text" class="form-control" name="nombre_jornada" 
               value="<?= esc($jornada['nombre_jornada']) ?>" required>
      </div>

      <!-- ═══ ORGANIZACIÓN ═══ -->
      <div class="mb-3">
        <label class="form-label">Organización</label>
        <select class="form-select" name="organizacion_id" 
                <?= $soloLectura ? 'disabled' : '' ?> required>
          <?php foreach ($organizaciones as $o): ?>
            <option value="<?= $o['id_organizacion'] ?>"
              <?= ($o['id_organizacion'] == $jornada['organizacion_id']) ? 'selected' : '' ?>>
              <?= esc($o['nombre_org']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <?php if ($soloLectura): ?>
          <input type="hidden" name="organizacion_id" value="<?= esc($jornada['organizacion_id']) ?>">
        <?php endif; ?>
      </div>

      <!-- ═══ LOCALIDAD ═══ -->
      <div class="mb-3">
        <label class="form-label">Institución o Localidad</label>
        <input type="text" class="form-control" id="localidad" name="localidad" 
               value="<?= esc($jornada['nombre_institucion'] ?? '') ?>">
      </div>

      <!-- ═══ TIPO JORNADA (público / privado) ═══ -->
      <div class="mb-3">
        <label class="form-label">Tipo de Jornada</label>
        <div class="d-flex gap-3">
          <label>
            <input type="radio" name="tipo_jornada" value="publica" 
                   <?= (($jornada['tipo_jornada'] ?? '') == 'publica') ? 'checked' : '' ?> required> 
            Pública
          </label>
          <label>
            <input type="radio" name="tipo_jornada" value="privada" 
                   <?= (($jornada['tipo_jornada'] ?? '') == 'privada') ? 'checked' : '' ?>> 
            Privada
          </label>
        </div>
      </div>

      <!-- ═══ MAPA INLINE (visible en la vista, no en modal) ═══ -->
      <div class="mb-3">
        <label class="form-label">Ubicación en el mapa</label>
        <input type="text" id="searchPlace" class="form-control mb-2" placeholder="Buscar lugar...">
        <div id="map"></div>

        <div class="row mt-3">
          <div class="col-md-4">
            <label class="form-label small text-muted">País</label>
            <input type="text" class="form-control" name="pais" id="pais" 
                   value="<?= esc($jornada['pais'] ?? '') ?>" readonly style="background:#f8f9fa;">
          </div>
          <div class="col-md-4">
            <label class="form-label small text-muted">Estado</label>
            <input type="text" class="form-control" name="estado" id="estado" 
                   value="<?= esc($jornada['estado'] ?? '') ?>" readonly style="background:#f8f9fa;">
          </div>
          <div class="col-md-4">
            <label class="form-label small text-muted">Ciudad</label>
            <input type="text" class="form-control" name="ciudad" id="ciudad" 
                   value="<?= esc($jornada['ciudad'] ?? '') ?>" readonly style="background:#f8f9fa;">
          </div>
        </div>

        <input type="hidden" name="coords" id="coords" 
               value="<?= esc($jornada['coordenadas'] ?? '') ?>">
      </div>

      <!-- ═══ PESQUISAS CON ICONOS ═══ -->
      <div class="mb-3">
        <label class="form-label">Seleccionar Pesquisas (al menos una)</label>

        <div class="pesquisa-selector">
          <?php foreach ($pesquisas as $p): 
              $id  = $p['idtipo_pesquisa'];
              $ico = $iconos_color[$id] ?? null;
              if (!$ico) continue;
              $checked = in_array($id, $pesquisasSeleccionadas) ? 'checked' : '';
          ?>
            <label class="pesquisa-item">
              <input type="checkbox" name="pesquisas[]" value="<?= $id ?>" <?= $checked ?>>
              <div class="pesquisa-icon-wrap">
                <img src="<?= base_url('img/' . $ico['gris']) ?>" class="icon-color" alt="<?= $ico['nombre'] ?>">
                <img src="<?= base_url('img/' . $ico['color']) ?>"  class="icon-gris"  alt="<?= $ico['nombre'] ?>">
              </div>
              <span class="pesquisa-label"><?= $ico['nombre'] ?></span>
            </label>
          <?php endforeach; ?>
        </div>

        <div class="text-danger mt-2" id="pesquisaError" style="display:none;">
          Selecciona al menos una pesquisa.
        </div>
      </div>

      <!-- ═══ BOTONES ═══ -->
      <div class="text-center mt-4">
        <button type="submit" class="btn btn-primary px-4">Actualizar</button>
        <a href="<?= base_url('jornadas') ?>" class="btn btn-secondary px-4 ms-2">Cancelar</a>
      </div>

    </form>
  </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>

<!-- Leaflet JS -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
/* ==========================================================
   MAPA INLINE — Se inicializa al cargar la página
   ========================================================== */
let map, marker;

const initialLat = <?= $lat ?>;
const initialLon = <?= $lon ?>;

document.addEventListener('DOMContentLoaded', () => {
    // Crear mapa
    map = L.map('map').setView([initialLat, initialLon], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Marcador draggable
    marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

    // Drag en vivo
    marker.on('drag', function (e) {
        const pos = e.target.getLatLng();
        updateAddress(pos.lat, pos.lng, false);
    });

    // Soltar marcador → reverse geocoding
    marker.on('dragend', function (e) {
        const pos = e.target.getLatLng();
        updateAddress(pos.lat, pos.lng, true);
    });

    // Click en mapa → mover marcador
    map.on('click', function (e) {
        const { lat, lng } = e.latlng;
        marker.setLatLng([lat, lng]);
        updateAddress(lat, lng, true);
    });

    // Buscar por texto (Enter)
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

    // Fix tamaño mapa
    setTimeout(() => map.invalidateSize(true), 300);
});

/* ── Traducción estados de Venezuela ── */
const estadosVE = {
    "Miranda State": "Miranda", "Miranda": "Miranda",
    "Capital District": "Distrito Capital", "Distrito Capital": "Distrito Capital",
    "Vargas": "La Guaira", "Vargas State": "La Guaira", "La Guaira State": "La Guaira",
    "Zulia State": "Zulia", "Aragua State": "Aragua", "Carabobo State": "Carabobo",
    "Lara State": "Lara", "Anzoategui State": "Anzoátegui", "Bolivar State": "Bolívar",
    "Táchira State": "Táchira", "Yaracuy State": "Yaracuy", "Sucre State": "Sucre",
    "Falcon State": "Falcón", "Guarico State": "Guárico", "Apure State": "Apure",
    "Amazonas State": "Amazonas", "Barinas State": "Barinas", "Cojedes State": "Cojedes",
    "Delta Amacuro State": "Delta Amacuro", "Monagas State": "Monagas",
    "Merida State": "Mérida", "Nueva Esparta State": "Nueva Esparta",
    "Portuguesa State": "Portuguesa", "Trujillo State": "Trujillo"
};

/* ── Actualizar campos de dirección ── */
async function updateAddress(lat, lon, reverseGeocode = true) {
    document.getElementById('coords').value = `${lat.toFixed(6)}, ${lon.toFixed(6)}`;

    if (!reverseGeocode) return;

    try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
        const data = await fetch(url).then(r => r.json());
        const addr = data.address || {};

        const pais   = addr.country || '';
        let estado   = addr.state || addr.region || '';
        const ciudad = addr.city || addr.town || addr.village || '';

        if (estadosVE[estado]) {
            estado = estadosVE[estado];
        }

        document.getElementById('pais').value   = pais;
        document.getElementById('estado').value = estado;
        document.getElementById('ciudad').value = ciudad;

    } catch (err) {
        console.error('Reverse geocoding error:', err);
    }
}

/* ── Validación pesquisas ── */
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
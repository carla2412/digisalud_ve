<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
:root{
  --bg:#f4f7fb;
  --card:#fff;
  --line:#e6ebf3;
  --text:#1f2a44;
  --muted:#7c8aa5;
  --primary:#2563eb;
  --primary-2:#1d4ed8;
  --shadow:0 10px 30px rgba(31,42,68,.08);
}

body{background:var(--bg);color:var(--text);}
.page{max-width:1400px;margin:24px auto;padding:0 18px;}
.shell{background:#fff;border-radius:24px;box-shadow:var(--shadow);overflow:hidden;border:1px solid #eef2f7;}

.topbar{
  display:flex;
  justify-content:space-between;
  align-items:center;
  padding:22px 28px;
  border-bottom:1px solid var(--line);
}

.topbar-left{display:flex;align-items:flex-start;gap:16px;}

.back-btn{
  width:44px;
  height:44px;
  border-radius:12px;
  border:1px solid var(--line);
  background:#fff;
  display:flex;
  align-items:center;
  justify-content:center;
  text-decoration:none;
  font-size:22px;
  color:#31415f;
}

.title h1{margin:0;font-size:40px;font-weight:800;}
.title p{margin:8px 0 0;color:var(--muted);font-size:18px;}

.content{
  display:grid;
  grid-template-columns:2fr 1fr;
  gap:20px;
  padding:24px;
  background:linear-gradient(180deg,#f7f9fd 0%, #f4f7fb 100%);
}

.left-col,.right-col{display:flex;flex-direction:column;gap:20px;}

.card-modern{
  background:var(--card);
  border:1px solid var(--line);
  border-radius:22px;
  box-shadow:0 6px 18px rgba(24,39,75,.05);
  padding:22px;
}

.card-title-modern{
  display:flex;
  align-items:center;
  gap:12px;
  margin-bottom:22px;
  font-size:28px;
  font-weight:700;
}

.card-title-modern .icon{
  width:42px;
  height:42px;
  border-radius:14px;
  background:#eef4ff;
  color:var(--primary);
  display:flex;
  align-items:center;
  justify-content:center;
}

.grid-2{
  display:grid;
  grid-template-columns:1fr 1fr;
  gap:18px 22px;
}

.field{display:flex;flex-direction:column;gap:10px;}
.field label{font-size:16px;font-weight:600;color:#31415f;margin:0;}

.input,.select{
  width:100%;
  min-height:56px;
  border:1px solid #d7e0ec;
  border-radius:14px;
  padding:12px 16px;
  font-size:16px;
  outline:none;
  background:#fff;
  color:var(--text);
}

.input:focus,.select:focus{
  border-color:#8fb3ff;
  box-shadow:0 0 0 4px rgba(37,99,235,.10);
}

.radio-group{
  display:flex;
  align-items:center;
  gap:22px;
  min-height:56px;
}

.radio-option{
  display:flex;
  align-items:center;
  gap:10px;
  font-size:17px;
  margin:0;
}

.search-box{position:relative;}
.search-box input{padding-left:46px;}
.search-icon{
  position:absolute;
  left:16px;
  top:50%;
  transform:translateY(-50%);
  color:#94a3b8;
}

.location-grid{
  display:grid;
  grid-template-columns:1fr 1fr 1fr;
  gap:16px;
  margin-top:18px;
}

.readonly-input{background:#f8f9fa;}

#map{
  height:350px;
  border-radius:18px;
  width:100%;
  border:1px solid var(--line);
}

.pesquisa-selector{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(120px,1fr));
  gap:18px;
}

.pesquisa-item{
  display:flex;
  flex-direction:column;
  align-items:center;
  text-align:center;
  cursor:pointer;
  padding:10px 6px;
}

.pesquisa-item input[type="checkbox"]{display:none;}

.pesquisa-icon-wrap{
  width:64px;
  height:64px;
  border-radius:50%;
  border:3px solid #dee2e6;
  background:#f8f9fa;
  display:flex;
  align-items:center;
  justify-content:center;
  transition:.25s ease;
}

.pesquisa-icon-wrap img{width:34px;height:34px;}
.pesquisa-icon-wrap .icon-color{display:none;}
.pesquisa-icon-wrap .icon-gris{display:block;}

.pesquisa-item input:checked + .pesquisa-icon-wrap{
  border-color:#3695f5;
  background:#e8eaf8;
  transform:scale(1.08);
  box-shadow:0 2px 8px rgba(54,149,245,.3);
}

.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-color{display:block;}
.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-gris{display:none;}

.pesquisa-label{
  font-size:.85rem;
  font-weight:600;
  color:#555;
  margin-top:8px;
}

.pesquisa-item input:checked ~ .pesquisa-label{color:#101a61;}

.summary-list{display:flex;flex-direction:column;gap:18px;font-size:16px;}

.summary-item{
  display:flex;
  justify-content:space-between;
  gap:20px;
  border-bottom:1px dashed #edf2f7;
  padding-bottom:12px;
}

.summary-item:last-child{border-bottom:none;padding-bottom:0;}
.label-muted{color:var(--muted);}

.badge-success-modern{
  display:inline-flex;
  padding:7px 12px;
  border-radius:10px;
  font-size:14px;
  font-weight:700;
  background:#eaf8ef;
  color:#198754;
  border:1px solid #ccebd5;
}

.chips{display:flex;gap:12px;flex-wrap:wrap;}

.chip{
  display:inline-flex;
  align-items:center;
  gap:10px;
  border:1px solid var(--line);
  padding:10px 14px;
  border-radius:999px;
  background:#fbfdff;
  font-weight:600;
}

.chip-icon{
  width:34px;
  height:34px;
  border-radius:50%;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#fff;
}

.yellow{background:#f5b400;}
.red{background:#e94b35;}
.blue{background:#2d8cf0;}
.purple{background:#8b7cf6;}
.green{background:#5cb85c;}
.teal{background:#14b8a6;}

.footer{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:20px;
  padding:22px 24px;
  background:#fff;
  border-top:1px solid var(--line);
}

.note{
  flex:1;
  border:1px solid #bcd1f5;
  background:#f5f9ff;
  color:#4f6b95;
  border-radius:16px;
  padding:16px 18px;
}

.actions{display:flex;gap:14px;}

.btn-modern{
  min-width:160px;
  height:56px;
  border:none;
  border-radius:14px;
  font-size:18px;
  font-weight:700;
  cursor:pointer;
  display:inline-flex;
  align-items:center;
  justify-content:center;
  text-decoration:none;
}

.btn-modern-secondary{
  background:#fff;
  border:1px solid #dbe4f0;
  color:#334155;
}

.btn-modern-primary{
  background:var(--primary);
  color:#fff;
  box-shadow:0 12px 24px rgba(37,99,235,.22);
}

.btn-modern-primary:hover{background:var(--primary-2);color:#fff;}

@media(max-width:1200px){
  .content{grid-template-columns:1fr;}
}

@media(max-width:768px){
  .title h1{font-size:28px;}
  .grid-2,.location-grid{grid-template-columns:1fr;}
  .footer{flex-direction:column;align-items:stretch;}
  .actions{flex-direction:column;width:100%;}
  .btn-modern{width:100%;}
}
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?php
$iconos_color = [
  '1' => ['color'=>'antropometria-color.svg','gris'=>'antropometria2.svg','nombre'=>'Antropometría','emoji'=>'⚖','clase'=>'yellow'],
  '2' => ['color'=>'sanguinea-color.svg','gris'=>'sanguinea2.svg','nombre'=>'Laboratorio','emoji'=>'🩸','clase'=>'red'],
  '3' => ['color'=>'visual-color.svg','gris'=>'visual2.svg','nombre'=>'Visual','emoji'=>'👁','clase'=>'purple'],
  '4' => ['color'=>'signos-vitales-color.svg','gris'=>'signosVitales2.svg','nombre'=>'Signos vitales','emoji'=>'❤','clase'=>'red'],
  '5' => ['color'=>'medicina-general-color.svg','gris'=>'medicinaGeneral2.svg','nombre'=>'Medicina general','emoji'=>'🩺','clase'=>'blue'],
  '6' => ['color'=>'vacunacion-color.svg','gris'=>'vacunacion2.svg','nombre'=>'Vacunación','emoji'=>'💉','clase'=>'blue'],
];
?>

<div class="page">
  <div class="shell">

    <header class="topbar">
      <div class="topbar-left">
        <a href="<?= base_url('jornadas') ?>" class="back-btn">←</a>
        <div class="title">
          <h1>Crear Jornada</h1>
          <p>Registra una nueva jornada y selecciona sus servicios</p>
        </div>
      </div>
    </header>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/guardar') ?>" novalidate>
      <?= csrf_field() ?>

      <main class="content">
        <section class="left-col">

          <div class="card-modern">
            <div class="card-title-modern">
             
              <span>Fecha</span>
            </div>

            <div class="field">
              <label for="fecha_inicio">Fecha de la Jornada</label>
              <input type="date" class="input" id="fecha_inicio" name="fecha_inicio" required>
            </div>
          </div>

          <div class="card-modern">
            <div class="card-title-modern">
              
              <span>Detalles de la Jornada</span>
            </div>

            <div class="grid-2">
              <div class="field">
                <label for="nombre_jornada">Nombre de la Jornada</label>
                <input type="text" class="input" id="nombre_jornada" name="nombre_jornada" required>
              </div>

              <div class="field">
                <label for="organizacion_id">Nombre de la Organización</label>
                <select class="select" id="organizacion_id" name="organizacion_id"
                        <?= $soloLectura ? 'disabled' : '' ?> required>
                  <?php foreach ($organizaciones as $o): ?>
                    <option value="<?= $o['id_organizacion'] ?>"
                      <?= ($o['id_organizacion'] == $orgSesion) ? 'selected' : '' ?>>
                      <?= esc($o['nombre_org']) ?>
                    </option>
                  <?php endforeach; ?>
                </select>

                <?php if ($soloLectura): ?>
                  <input type="hidden" name="organizacion_id" value="<?= esc($orgSesion) ?>">
                <?php endif; ?>
              </div>

              <div class="field">
                <label for="localidad">Institución o Localidad</label>
                <input type="text" class="input" id="localidad" name="localidad">
                </div>

              <div class="field">
                <label>Tipo de Jornada</label>
                <div class="radio-group">
                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="publica" required>
                    Pública
                  </label>

                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="privada">
                    Privada
                  </label>
                </div>
              </div>
            </div>
                    <div class="field" style="margin-top:18px;">
  <label for="searchPlace">Ubicación en el mapa</label>
  <div class="search-box">
    <span class="search-icon">🔍</span>
    <input class="input" type="text" id="searchPlace" placeholder="Buscar lugar o dirección...">
  </div>
</div>

<div id="map"></div>
            <div class="location-grid">
              <div class="field">
                <label>País</label>
                <input type="text" class="input readonly-input" name="pais" id="pais" readonly>
              </div>

              <div class="field">
                <label>Estado</label>
                <input type="text" class="input readonly-input" name="estado" id="estado" readonly>
              </div>

              <div class="field">
                <label>Ciudad</label>
                <input type="text" class="input readonly-input" name="ciudad" id="ciudad" readonly>
              </div>
            </div>

            <input type="hidden" name="coords" id="coords">
          </div>

          <div class="card-modern">
            <div class="card-title-modern">
               
              <span>Pesquisas</span>
            </div>

            <label class="form-label mb-3">Seleccionar Pesquisa (al menos una)</label>

            <div class="pesquisa-selector">
              <?php foreach ($pesquisas as $p):
                $id = $p['idtipo_pesquisa'];
                $ico = $iconos_color[$id] ?? null;
              ?>
                <?php if ($ico): ?>
                  <label class="pesquisa-item">
                    <input type="checkbox"
                           name="pesquisas[]"
                           value="<?= $id ?>"
                           data-nombre="<?= esc($ico['nombre']) ?>"
                           data-emoji="<?= esc($ico['emoji']) ?>"
                           data-clase="<?= esc($ico['clase']) ?>">

                    <div class="pesquisa-icon-wrap">
                      <img src="<?= base_url('img/' . $ico['color']) ?>" class="icon-gris" alt="<?= esc($ico['nombre']) ?>">
                      <img src="<?= base_url('img/' . $ico['gris']) ?>" class="icon-color" alt="<?= esc($ico['nombre']) ?>">
                    </div>

                    <span class="pesquisa-label"><?= esc($ico['nombre']) ?></span>
                  </label>
                <?php else: ?>
                  <label class="form-check form-check-inline">
                    <input class="form-check-input" type="checkbox" name="pesquisas[]" value="<?= $id ?>">
                    <?= esc(ucfirst(strtolower($p['descripcion_view']))) ?>
                  </label>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>

            <div class="text-danger mt-3" id="pesquisaError" style="display:none;">
              Selecciona al menos una pesquisa.
            </div>
          </div>

        </section>

        <aside class="right-col">
          <div class="card-modern">
            <div class="card-title-modern">
              
              <span>Resumen</span>
            </div>

            <div class="summary-list">
              <div class="summary-item">
                <span class="label-muted">Estado:</span>
                <span class="badge-success-modern">Activa</span>
              </div>

              <div class="summary-item">
                <span class="label-muted">Fecha:</span>
                <strong id="resumenFecha">Sin fecha</strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Nombre:</span>
                <strong id="resumenNombre">Sin nombre</strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Organización:</span>
                <strong id="resumenOrganizacion">-</strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Tipo:</span>
                <strong id="resumenTipo">Sin definir</strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Ubicación:</span>
                <strong id="resumenUbicacion">📍 Sin ubicación</strong>
              </div>

              <div class="summary-item" style="display:block;">
                <div class="label-muted mb-2">Pesquisas seleccionadas:</div>
                <div id="resumenPesquisas">
                  <span class="label-muted">Ninguna seleccionada</span>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </main>

      <footer class="footer">
        <div class="note">
          Selecciona una ubicación desde el buscador para completar país, estado, ciudad y coordenadas.
        </div>

        <div class="actions">
          <a href="<?= base_url('jornadas') ?>" class="btn-modern btn-modern-secondary">Cancelar</a>
          <button type="submit" class="btn-modern btn-modern-primary">Guardar</button>
        </div>
      </footer>
    </form>
  </div>
</div>

 

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>

<script>
 
let map, marker;

document.addEventListener('DOMContentLoaded', () => {
  const initialLat = 10.4806;
  const initialLon = -66.9036;

  map = L.map('map').setView([initialLat, initialLon], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
  }).addTo(map);

  marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

  marker.on('dragend', async function(e) {
    const pos = e.target.getLatLng();
    await updateAddress(pos.lat, pos.lng, true);
  });

  map.on('click', async function(e) {
    const { lat, lng } = e.latlng;
    marker.setLatLng([lat, lng]);
    await updateAddress(lat, lng, true);
  });

  document.getElementById('searchPlace').addEventListener('keypress', async (e) => {
    if (e.key === 'Enter') {
      e.preventDefault();

      const query = e.target.value.trim();
      if (query.length < 3) return;

      const url = `https://nominatim.openstreetmap.org/search?format=json&addressdetails=1&q=${encodeURIComponent(query)}`;

      const results = await fetch(url, {
        headers: { 'Accept': 'application/json' }
      }).then(r => r.json());

      if (results.length > 0) {
        const lat = parseFloat(results[0].lat);
        const lon = parseFloat(results[0].lon);

        map.setView([lat, lon], 15);
        marker.setLatLng([lat, lon]);

        await updateAddress(lat, lon, true);
      }
    }
  });

  setTimeout(() => map.invalidateSize(true), 300);
});



const estadosVE = {
  "Miranda State":"Miranda",
  "Miranda":"Miranda",
  "Capital District":"Distrito Capital",
  "Distrito Capital":"Distrito Capital",
  "Vargas":"La Guaira",
  "Vargas State":"La Guaira",
  "La Guaira State":"La Guaira",
  "Zulia State":"Zulia",
  "Aragua State":"Aragua",
  "Carabobo State":"Carabobo",
  "Lara State":"Lara",
  "Anzoategui State":"Anzoátegui",
  "Bolivar State":"Bolívar",
  "Táchira State":"Táchira",
  "Yaracuy State":"Yaracuy",
  "Sucre State":"Sucre",
  "Falcon State":"Falcón",
  "Guarico State":"Guárico",
  "Apure State":"Apure",
  "Amazonas State":"Amazonas",
  "Barinas State":"Barinas",
  "Cojedes State":"Cojedes",
  "Delta Amacuro State":"Delta Amacuro",
  "Monagas State":"Monagas",
  "Merida State":"Mérida",
  "Nueva Esparta State":"Nueva Esparta",
  "Portuguesa State":"Portuguesa",
  "Trujillo State":"Trujillo"
};

async function updateAddress(lat, lon, reverseGeocode = true) {
  document.getElementById('coords').value =
    `${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}`;

  if (!reverseGeocode) return;

  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;

    const response = await fetch(url, {
      headers: { 'Accept': 'application/json' }
    });

    const data = await response.json();
    const addr = data.address || {};

    let pais = addr.country || '';
    let estado = addr.state || addr.region || addr.county || '';
    let ciudad =
      addr.city ||
      addr.town ||
      addr.village ||
      addr.municipality ||
      addr.suburb ||
      addr.city_district ||
      addr.county ||
      '';

    if (estadosVE[estado]) {
      estado = estadosVE[estado];
    }

    document.getElementById('pais').value = pais;
    document.getElementById('estado').value = estado;
    document.getElementById('ciudad').value = ciudad;

    actualizarResumenUbicacion();

  } catch (err) {
    console.error('Error obteniendo dirección:', err);
  }
}


function sincronizarResumen() {
  const fecha = document.getElementById('fecha_inicio').value;
  const nombre = document.getElementById('nombre_jornada').value;
  const org = document.getElementById('organizacion_id');
  const tipo = document.querySelector('input[name="tipo_jornada"]:checked');

  document.getElementById('resumenFecha').textContent = fecha || 'Sin fecha';
  document.getElementById('resumenNombre').textContent = nombre || 'Sin nombre';
  document.getElementById('resumenOrganizacion').textContent = org ? org.options[org.selectedIndex].text : '-';
  document.getElementById('resumenTipo').textContent = tipo ? tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1) : 'Sin definir';

  actualizarResumenPesquisas();
  actualizarResumenUbicacion();
}

function actualizarResumenUbicacion() {
  const ciudad = document.getElementById('ciudad').value || '';
  const estado = document.getElementById('estado').value || '';
  const ubicacion = [ciudad, estado].filter(Boolean).join(', ');

  document.getElementById('resumenUbicacion').textContent = ubicacion ? `📍 ${ubicacion}` : '📍 Sin ubicación';
}

function actualizarResumenPesquisas() {
  const checks = document.querySelectorAll('input[name="pesquisas[]"]:checked');
  const resumen = document.getElementById('resumenPesquisas');

  if (checks.length === 0) {
    resumen.innerHTML = '<span class="label-muted">Ninguna seleccionada</span>';
    return;
  }

  let html = '<div class="chips">';
  checks.forEach(chk => {
    const nombre = chk.dataset.nombre || chk.parentElement.textContent.trim();
    const emoji = chk.dataset.emoji || '🩺';
    const clase = chk.dataset.clase || 'blue';

    html += `
      <div class="chip">
        <div class="chip-icon ${clase}">${emoji}</div>
        <span>${nombre}</span>
      </div>
    `;
  });
  html += '</div>';

  resumen.innerHTML = html;
}

document.addEventListener('DOMContentLoaded', () => {
  sincronizarResumen();

  document.getElementById('fecha_inicio').addEventListener('input', sincronizarResumen);
  document.getElementById('nombre_jornada').addEventListener('input', sincronizarResumen);
  document.getElementById('organizacion_id')?.addEventListener('change', sincronizarResumen);

  document.querySelectorAll('input[name="tipo_jornada"]').forEach(r => {
    r.addEventListener('change', sincronizarResumen);
  });

  document.querySelectorAll('input[name="pesquisas[]"]').forEach(chk => {
    chk.addEventListener('change', sincronizarResumen);
  });
});

document.getElementById('formJornada').addEventListener('submit', function(e) {
  const checks = document.querySelectorAll("input[name='pesquisas[]']:checked");

  if (checks.length === 0) {
    e.preventDefault();
    document.getElementById('pesquisaError').style.display = 'block';
  }
});
</script>

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
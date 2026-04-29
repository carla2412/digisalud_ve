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

.topbar{display:flex;justify-content:space-between;align-items:center;padding:22px 28px;border-bottom:1px solid var(--line);}
.topbar-left{display:flex;align-items:flex-start;gap:16px;}
.back-btn{width:44px;height:44px;border-radius:12px;border:1px solid var(--line);background:#fff;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:22px;color:#31415f;}
.title h1{margin:0;font-size:40px;font-weight:800;}
.title p{margin:8px 0 0;color:var(--muted);font-size:18px;}

.content{display:grid;grid-template-columns:2fr 1fr;gap:20px;padding:24px;background:linear-gradient(180deg,#f7f9fd 0%, #f4f7fb 100%);}
.left-col,.right-col{display:flex;flex-direction:column;gap:20px;}

.card-modern{background:var(--card);border:1px solid var(--line);border-radius:22px;box-shadow:0 6px 18px rgba(24,39,75,.05);padding:22px;}
.card-title-modern{display:flex;align-items:center;gap:12px;margin-bottom:22px;font-size:28px;font-weight:700;}
.grid-2{display:grid;grid-template-columns:1fr 1fr;gap:18px 22px;}
.field{display:flex;flex-direction:column;gap:10px;}
.field label{font-size:16px;font-weight:600;color:#31415f;margin:0;}

.input,.select{width:100%;min-height:56px;border:1px solid #d7e0ec;border-radius:14px;padding:12px 16px;font-size:16px;outline:none;background:#fff;color:var(--text);}
.input:focus,.select:focus{border-color:#8fb3ff;box-shadow:0 0 0 4px rgba(37,99,235,.10);}

.radio-group{display:flex;align-items:center;gap:22px;min-height:56px;}
.radio-option{display:flex;align-items:center;gap:10px;font-size:17px;margin:0;}

.search-box{position:relative;}
.search-box input{padding-left:46px;}
.search-icon{position:absolute;left:16px;top:50%;transform:translateY(-50%);color:#94a3b8;}

.location-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:18px;}
.readonly-input{background:#f8f9fa;}

#map{height:350px;border-radius:18px;width:100%;border:1px solid var(--line);}

.pesquisa-selector{display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:18px;}
.pesquisa-item{display:flex;flex-direction:column;align-items:center;text-align:center;cursor:pointer;padding:10px 6px;}
.pesquisa-item input[type="checkbox"]{display:none;}
.pesquisa-icon-wrap{width:64px;height:64px;border-radius:50%;border:3px solid #dee2e6;background:#f8f9fa;display:flex;align-items:center;justify-content:center;transition:.25s ease;}
.pesquisa-icon-wrap img{width:34px;height:34px;}
.pesquisa-icon-wrap .icon-color{display:none;}
.pesquisa-icon-wrap .icon-gris{display:block;}
.pesquisa-item input:checked + .pesquisa-icon-wrap{border-color:#3695f5;background:#e8eaf8;transform:scale(1.08);box-shadow:0 2px 8px rgba(54,149,245,.3);}
.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-color{display:block;}
.pesquisa-item input:checked + .pesquisa-icon-wrap .icon-gris{display:none;}
.pesquisa-label{font-size:.85rem;font-weight:600;color:#555;margin-top:8px;}
.pesquisa-item input:checked ~ .pesquisa-label{color:#101a61;}

.summary-list{display:flex;flex-direction:column;gap:18px;font-size:16px;}
.summary-item{display:flex;justify-content:space-between;gap:20px;border-bottom:1px dashed #edf2f7;padding-bottom:12px;}
.summary-item:last-child{border-bottom:none;}
.label-muted{color:var(--muted);font-weight:500;}
.badge-success-modern{background:#ecfdf3;color:#198754;padding:4px 14px;border-radius:999px;font-weight:700;font-size:14px;}
.chips{display:flex;flex-wrap:wrap;gap:10px;}
.chip{display:flex;align-items:center;gap:8px;background:#f0f4ff;border-radius:999px;padding:6px 14px 6px 8px;font-size:.85rem;font-weight:600;color:#1f2a44;}
.chip-icon{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.78rem;}
.chip-icon.blue{background:#2478df;}.chip-icon.red{background:#e72713;}.chip-icon.purple{background:#341092;}.chip-icon.yellow{background:#ffc107;}

.footer{display:flex;justify-content:space-between;align-items:center;padding:20px 28px;border-top:1px solid var(--line);background:#fff;}
.note{font-size:14px;color:var(--muted);max-width:520px;}
.actions{display:flex;gap:14px;}
.btn-modern{padding:14px 30px;border-radius:12px;font-weight:700;font-size:16px;border:none;cursor:pointer;}
.btn-modern-secondary{background:#f1f5f9;color:#475569;}
.btn-modern-primary{background:linear-gradient(135deg,#2563eb,#1d4ed8);color:#fff;box-shadow:0 8px 20px rgba(37,99,235,.3);}

/* Sugerencias institución */
.inst-sugerencia{padding:10px 14px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:.9rem;}
.inst-sugerencia:hover{background:#f0f4ff;}

@media(max-width:900px){
  .content{grid-template-columns:1fr;}
  .grid-2{grid-template-columns:1fr;}
  .location-grid{grid-template-columns:1fr;}
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$iconos_color = [
  '1' => ['color'=>'antropometria-color.svg','gris'=>'antropometria2.svg','nombre'=>'Antropometría','emoji'=>'📏','clase'=>'yellow'],
  '2' => ['color'=>'sanguinea-color.svg','gris'=>'sanguinea2.svg','nombre'=>'Laboratorio','emoji'=>'🩸','clase'=>'red'],
  '3' => ['color'=>'visual-color.svg','gris'=>'visual2.svg','nombre'=>'Visual','emoji'=>'👁','clase'=>'purple'],
  '4' => ['color'=>'signos-vitales-color.svg','gris'=>'signosVitales2.svg','nombre'=>'Signos vitales','emoji'=>'❤','clase'=>'red'],
  '5' => ['color'=>'medicina-general-color.svg','gris'=>'medicinaGeneral2.svg','nombre'=>'Medicina general','emoji'=>'🩺','clase'=>'blue'],
  '6' => ['color'=>'vacunacion-color.svg','gris'=>'vacunacion2.svg','nombre'=>'Vacunación','emoji'=>'💉','clase'=>'blue'],
];
?>

<div class="page">
  <div class="shell">

    <div class="topbar">
      <div class="topbar-left">
        <a href="<?= base_url('jornadas') ?>" class="back-btn">&larr;</a>
        <div class="title">
          <h1>Crear Jornada</h1>
          <p>Registra una nueva jornada y selecciona sus servicios</p>
        </div>
      </div>
    </div>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/guardar') ?>" novalidate>
      <?= csrf_field() ?>

      <main class="content">
        <section class="left-col">

          <!-- FECHA -->
          <div class="card-modern">
            <div class="card-title-modern">
              <label for="fecha_inicio">Fecha de la Jornada</label>
            </div>
            <div class="field">
              <input type="date" class="input" id="fecha_inicio" name="fecha_inicio" required>
            </div>
          </div>

          <!-- DETALLES -->
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

              <!-- FIX: Institución con sugerencias (Select2 style) -->
              <div class="field">
                <label for="nombre_institucion">Institución o Localidad</label>
                <div style="position:relative;">
                  <input type="hidden" name="institucion_id" id="institucion_id" value="">
                  <input type="text" class="input" id="nombre_institucion" name="nombre_institucion"
                         placeholder="Escribe para buscar o crear nueva..." autocomplete="off" required>
                  <div id="institucion-sugerencias" style="
                    position:absolute; top:100%; left:0; right:0; z-index:100;
                    background:#fff; border:1px solid #d9e2ef; border-radius:8px;
                    max-height:200px; overflow-y:auto; display:none;
                    box-shadow:0 4px 12px rgba(0,0,0,.1);
                  "></div>
                </div>
                <small style="color:#64748b; font-size:.8rem;">
                  Escribe el nombre del sitio físico. Si ya existe, selecciónalo de la lista.
                </small>
              </div>

              <div class="field">
                <label>Tipo de Jornada</label>
                <div class="radio-group">
                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="publica" required> Pública
                  </label>
                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="privada"> Privada
                  </label>
                </div>
              </div>
            </div>

            <!-- MAPA -->
            <div class="field" style="margin-top:18px;">
              <label for="searchPlace">Ubicación en el mapa</label>
              <div class="search-box">
                <span class="search-icon">&#128269;</span>
                <input class="input" type="text" id="searchPlace" placeholder="Buscar lugar o dirección...">
              </div>
            </div>

            <div id="map"></div>

            <!-- Campos de ubicación auto-completados por el mapa -->
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

            <!-- FIX: Campos municipio, parroquia y detalle -->
            <div class="location-grid" style="margin-top:12px;">
              <div class="field">
                <label for="municipio">Municipio</label>
                <input type="text" class="input" name="municipio" id="municipio"
                       placeholder="Se completa con el mapa o escríbelo">
              </div>
              <div class="field">
                <label for="parroquia">Parroquia</label>
                <input type="text" class="input" name="parroquia" id="parroquia"
                       placeholder="Se completa con el mapa o escríbelo">
              </div>
              <div class="field">
                <label for="detalle">Detalle / Referencia</label>
                <input type="text" class="input" name="detalle" id="detalle"
                       placeholder="Nombre de calle, punto de referencia, etc.">
              </div>
            </div>

            <input type="hidden" name="coords" id="coords">
          </div>

          <!-- PESQUISAS -->
          <div class="card-modern">
            <div class="card-title-modern">
              <span>Pesquisas</span>
            </div>

            <label class="form-label mb-3">Seleccionar Pesquisa (al menos una)</label>

            <div class="pesquisa-selector">
              <?php foreach ($pesquisas as $p):
                $id  = $p['idtipo_pesquisa'];
                $ico = $iconos_color[$id] ?? null;
              ?>
                <?php if ($ico): ?>
                  <label class="pesquisa-item">
                    <input type="checkbox" name="pesquisas[]" value="<?= $id ?>"
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

        <!-- RESUMEN -->
        <aside class="right-col">
          <div class="card-modern">
            <div class="card-title-modern"><span>Resumen</span></div>
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
                <strong id="resumenUbicacion">&#128205; Sin ubicación</strong>
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

// Mapa de corrección de estados de Venezuela
const estadosVE = {
  'Distrito Capital': 'Distrito Capital',
  'Estado Miranda': 'Miranda',
  'Estado Zulia': 'Zulia',
  'Estado Aragua': 'Aragua',
  'Estado Carabobo': 'Carabobo',
  'Estado Anzoátegui': 'Anzoátegui',
  'Estado Barinas': 'Barinas',
  'Estado Bolívar': 'Bolívar',
  'Estado Falcón': 'Falcón',
  'Estado Lara': 'Lara',
  'Estado Mérida': 'Mérida',
  'Estado Portuguesa': 'Portuguesa',
  'Estado Sucre': 'Sucre',
  'Estado Táchira': 'Táchira',
  'Estado Trujillo': 'Trujillo',
  'Estado Vargas': 'La Guaira',
};

document.addEventListener('DOMContentLoaded', () => {
  const initialLat = 10.4806;
  const initialLon = -66.9036;

  map = L.map('map').setView([initialLat, initialLon], 13);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; OpenStreetMap contributors'
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

      const url = `https://nominatim.openstreetmap.org/search?format=jsonv2&q=${encodeURIComponent(query)}&limit=1&countrycodes=ve`;

      try {
        const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const results = await response.json();

        if (results.length > 0) {
          const { lat, lon } = results[0];
          map.setView([lat, lon], 15);
          marker.setLatLng([lat, lon]);
          await updateAddress(lat, lon, true);
        }
      } catch (err) {
        console.error('Error buscando lugar:', err);
      }
    }
  });

  sincronizarResumen();

  document.getElementById('fecha_inicio').addEventListener('input', sincronizarResumen);
  document.getElementById('nombre_jornada').addEventListener('input', sincronizarResumen);
  document.getElementById('organizacion_id')?.addEventListener('change', sincronizarResumen);
  document.querySelectorAll('input[name="tipo_jornada"]').forEach(r => r.addEventListener('change', sincronizarResumen));
  document.querySelectorAll('input[name="pesquisas[]"]').forEach(chk => chk.addEventListener('change', sincronizarResumen));
});

async function updateAddress(lat, lon, reverseGeocode) {
  document.getElementById('coords').value = `${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}`;

  if (!reverseGeocode) return;

  try {
    const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;
    const response = await fetch(url, { headers: { 'Accept': 'application/json' } });
    const data = await response.json();
    const addr = data.address || {};

    let pais = addr.country || '';
    let estado = addr.state || addr.region || addr.county || '';
    let ciudad = addr.city || addr.town || addr.village || addr.municipality || addr.suburb || addr.city_district || addr.county || '';

    // FIX: Limpiar prefijo "Estado " que devuelve Nominatim
    estado = estado.replace(/^Estado\s+/i, '');
    
    if (estadosVE[estado]) {
      estado = estadosVE[estado];
    }

    // FIX: Extraer municipio y parroquia
    let municipio = addr.county || addr.municipality || '';
    let parroquia = addr.suburb || addr.city_district || addr.neighbourhood || '';
    municipio = municipio.replace(/^Municipio\s+/i, '');
    document.getElementById('pais').value = pais;
    document.getElementById('estado').value = estado;
    document.getElementById('ciudad').value = ciudad;
    document.getElementById('municipio').value = municipio;
    document.getElementById('parroquia').value = parroquia;

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
    html += `<div class="chip"><div class="chip-icon ${clase}">${emoji}</div><span>${nombre}</span></div>`;
  });
  html += '</div>';
  resumen.innerHTML = html;
}

// Validación pesquisas antes de enviar
document.getElementById('formJornada').addEventListener('submit', function(e) {
  const checks = document.querySelectorAll("input[name='pesquisas[]']:checked");
  if (checks.length === 0) {
    e.preventDefault();
    document.getElementById('pesquisaError').style.display = 'block';
  }
});

// ═══ SELECT DE INSTITUCIONES CON SUGERENCIAS ═══
(function() {
  const inputInst   = document.getElementById('nombre_institucion');
  const hiddenId    = document.getElementById('institucion_id');
  const sugBox      = document.getElementById('institucion-sugerencias');
  let debounceTimer = null;

  if (!inputInst || !sugBox) return;

  inputInst.addEventListener('input', function() {
    const val = this.value.trim();
    hiddenId.value = '';

    clearTimeout(debounceTimer);
    if (val.length < 2) { sugBox.style.display = 'none'; return; }

    debounceTimer = setTimeout(async () => {
      try {
        const resp = await fetch(`<?= base_url('jornadas/buscar-instituciones') ?>?q=${encodeURIComponent(val)}`);
        const data = await resp.json();

        if (data.length === 0) { sugBox.style.display = 'none'; return; }

        sugBox.innerHTML = data.map(item =>
          `<div class="inst-sugerencia" data-id="${item.id_institucion}" data-nombre="${item.nombre_institucion}">
            ${item.nombre_institucion}
          </div>`
        ).join('');

        sugBox.style.display = 'block';

        sugBox.querySelectorAll('.inst-sugerencia').forEach(el => {
          el.addEventListener('click', function() {
            inputInst.value = this.dataset.nombre;
            hiddenId.value  = this.dataset.id;
            sugBox.style.display = 'none';
          });
        });
      } catch (e) {
        console.error('Error buscando instituciones:', e);
      }
    }, 300);
  });

  document.addEventListener('click', function(e) {
    if (!inputInst.contains(e.target) && !sugBox.contains(e.target)) {
      sugBox.style.display = 'none';
    }
  });
})();
</script>

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
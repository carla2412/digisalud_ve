<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />

<style>
  :root{
    --bg:#f4f7fb;
    --card:#ffffff;
    --line:#e6ebf3;
    --text:#1f2a44;
    --muted:#7c8aa5;
    --primary:#2563eb;
    --primary-2:#1d4ed8;
    --success-bg:#ecfdf3;
    --success:#198754;
    --danger-bg:#fef2f2;
    --danger:#dc2626;
    --shadow:0 10px 30px rgba(31,42,68,.08);
  }

  * { box-sizing: border-box; }

  body{
    background:var(--bg);
    color:var(--text);
  }

  .page{
    max-width:1400px;
    margin:24px auto;
    padding:0 18px;
  }

  .shell{
    background:#fff;
    border-radius:24px;
    box-shadow:var(--shadow);
    overflow:hidden;
    border:1px solid #eef2f7;
  }

  .topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:22px 28px;
    border-bottom:1px solid var(--line);
    background:#fff;
  }

  .topbar-left{
    display:flex;
    align-items:flex-start;
    gap:16px;
  }

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

  .title h1{
    margin:0;
    font-size:40px;
    font-weight:800;
    letter-spacing:-0.02em;
  }

  .title p{
    margin:8px 0 0;
    color:var(--muted);
    font-size:18px;
  }

  .content{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    padding:24px;
    background:linear-gradient(180deg,#f7f9fd 0%, #f4f7fb 100%);
  }

  .left-col, .right-col{
    display:flex;
    flex-direction:column;
    gap:20px;
  }

  .card{
    background:var(--card);
    border:1px solid var(--line);
    border-radius:22px;
    box-shadow:0 6px 18px rgba(24,39,75,.05);
    padding:22px;
  }

  .card-title{
    display:flex;
    align-items:center;
    gap:12px;
    margin-bottom:22px;
    font-size:28px;
    font-weight:700;
  }

  .card-title .icon{
    width:42px;
    height:42px;
    border-radius:14px;
    background:#eef4ff;
    color:var(--primary);
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:20px;
    font-weight:700;
  }

  .grid-2{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:18px 22px;
  }

  .field{
    display:flex;
    flex-direction:column;
    gap:10px;
  }

  .field label{
    font-size:16px;
    font-weight:600;
    color:#31415f;
    margin:0;
  }

  .input, .select{
    width:100%;
    min-height:56px;
    border:1px solid #d7e0ec;
    border-radius:14px;
    padding:12px 16px;
    font-size:16px;
    outline:none;
    background:#fff;
    color:var(--text);
    transition:.2s ease;
  }

  .input:focus, .select:focus{
    border-color:#8fb3ff;
    box-shadow:0 0 0 4px rgba(37,99,235,.10);
  }

  .readonly-input{
    background:#f8f9fa !important;
  }

  .search-box{
    position:relative;
  }

  .search-box input{
    padding-left:46px;
  }

  .search-icon{
    position:absolute;
    left:16px;
    top:50%;
    transform:translateY(-50%);
    color:#94a3b8;
    font-size:18px;
  }

  .radio-group{
    display:flex;
    align-items:center;
    gap:22px;
    min-height:56px;
    padding:10px 0;
  }

  .radio-option{
    display:flex;
    align-items:center;
    gap:10px;
    font-size:17px;
    color:#334155;
    margin:0;
  }

  .location-grid{
    display:grid;
    grid-template-columns:1fr 1fr 1fr;
    gap:16px;
    margin-top:18px;
  }

  #map{
    height:320px;
    width:100%;
    border-radius:18px;
    overflow:hidden;
    border:1px solid var(--line);
    margin-top:12px;
  }

  .summary-list{
    display:flex;
    flex-direction:column;
    gap:18px;
    font-size:16px;
  }

  .summary-item{
    display:flex;
    justify-content:space-between;
    gap:20px;
    border-bottom:1px dashed #edf2f7;
    padding-bottom:12px;
  }

  .summary-item:last-child{
    border-bottom:none;
    padding-bottom:0;
  }

  .label-muted{
    color:var(--muted);
  }

  .badge{
    display:inline-flex;
    align-items:center;
    padding:7px 12px;
    border-radius:10px;
    font-size:14px;
    font-weight:700;
  }

  .badge-success{
    background:#eaf8ef;
    color:#198754;
    border:1px solid #ccebd5;
  }

  .badge-danger{
    background:#fef2f2;
    color:#dc2626;
    border:1px solid #fecaca;
  }

  .chips{
    display:flex;
    gap:12px;
    flex-wrap:wrap;
  }

  .chip{
    display:inline-flex;
    align-items:center;
    gap:10px;
    border:1px solid var(--line);
    padding:10px 14px;
    border-radius:999px;
    background:#fbfdff;
    min-height:50px;
    font-weight:600;
    color:#334155;
  }

  .chip-icon{
    width:34px;
    height:34px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#fff;
    font-size:16px;
    font-weight:700;
  }

  .yellow{ background: #f9f513; }
  .red{ background:#e94b35; }
  .orange{background: #f78a04;}
  .pink{background: #ff79ef;}
  .blue{ background: #2d8cf0; }
  .purple{ background: #af3eff; }
  .green{ background: #74d274; }
  .teal{ background:#14b8a6; }

  .pesquisa-selector{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(110px,1fr));
    gap:18px;
    margin-top:8px;
  }

  .pesquisa-item{
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:flex-start;
    text-align:center;
    cursor:pointer;
    padding:8px 6px;
  }

  .pesquisa-item input[type="checkbox"]{
    display:none;
  }

  .pesquisa-icon-wrap{
    width:64px;
    height:64px;
    border-radius:50%;
    border:3px solid #dee2e6;
    background:#f8f9fa;
    display:flex;
    align-items:center;
    justify-content:center;
    transition:all .25s ease;
    box-shadow:0 8px 16px rgba(15,23,42,.06);
  }

  .pesquisa-icon-wrap img{
    width:34px;
    height:34px;
  }

  .pesquisa-icon-wrap .icon-color { display:none; }
  .pesquisa-icon-wrap .icon-gris { display:block; }

  .pesquisa-item input:checked + .pesquisa-icon-wrap{
    border-color:#3695f5;
    background:#e8eaf8;
    transform:scale(1.08);
    box-shadow:0 2px 8px rgba(54,149,245,.3);
  }

  .pesquisa-item input:checked + .pesquisa-icon-wrap .icon-color{ display:block; }
  .pesquisa-item input:checked + .pesquisa-icon-wrap .icon-gris{ display:none; }

  .pesquisa-label{
    font-size:.85rem;
    font-weight:600;
    color:#555;
    margin-top:8px;
    line-height:1.2;
  }

  .pesquisa-item input:checked ~ .pesquisa-label{
    color:#101a61;
  }

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
    font-size:15px;
  }

  .actions{
    display:flex;
    gap:14px;
  }

  .btn-modern{
    min-width:160px;
    height:56px;
    border:none;
    border-radius:14px;
    font-size:18px;
    font-weight:700;
    cursor:pointer;
    transition:.2s ease;
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

  .btn-modern-primary:hover{ background:var(--primary-2); color:#fff; }
  .btn-modern-secondary:hover{ background:#f8fafc; color:#334155; }

  .alert-modern{
    border-radius:16px;
    margin-bottom:18px;
  }

  @media (max-width: 1200px){
    .content{ grid-template-columns:1fr; }
  }

  @media (max-width: 768px){
    .title h1{ font-size:28px; }
    .card-title{ font-size:22px; }
    .grid-2,
    .location-grid{
      grid-template-columns:1fr;
    }
    .footer{
      flex-direction:column;
      align-items:stretch;
    }
    .actions{
      width:100%;
      flex-direction:column;
    }
    .btn-modern{
      width:100%;
    }
  }
</style>
<?= $this->endSection() ?>


<?= $this->section('content') ?>

<?php
$iconos_color = [
    '1' => ['color' => 'antropometria-color.svg',      'gris' => 'antropometria2.svg',   'nombre' => 'Antropometría',   'emoji' => '⚖', 'clase' => 'orange'],
    '2' => ['color' => 'sanguinea-color.svg',          'gris' => 'sanguinea2.svg',       'nombre' => 'Laboratorio',     'emoji' => '🩸', 'clase' => 'green'],
    '3' => ['color' => 'visual-color.svg',             'gris' => 'visual2.svg',          'nombre' => 'Visual',          'emoji' => '👁', 'clase' => 'purple'],
    '4' => ['color' => 'signos-vitales-color.svg',     'gris' => 'signosVitales2.svg',   'nombre' => 'Signos vitales',  'emoji' => '❤', 'clase' => 'orange'],
    '5' => ['color' => 'medicina-general-color.svg',   'gris' => 'medicinaGeneral2.svg', 'nombre' => 'Medicina general','emoji' => '🩺', 'clase' => 'red'],
    '6' => ['color' => 'vacunacion-color.svg',         'gris' => 'vacunacion2.svg',      'nombre' => 'Vacunación',      'emoji' => '💉', 'clase' => 'yellow'],
];

$lat = 10.4806;
$lon = -66.9036;
if (!empty($jornada['coordenadas'])) {
    $parts = explode(',', $jornada['coordenadas']);
    if (count($parts) == 2) {
        $lat = floatval(trim($parts[0]));
        $lon = floatval(trim($parts[1]));
    }
}

$estadoTexto = ($jornada['status_jor'] == 2) ? 'Finalizada' : 'Activa';
$estadoBadge = ($jornada['status_jor'] == 2) ? 'badge-danger' : 'badge-success';

$pesquisasActivas = [];
foreach ($pesquisas as $p) {
    $id = $p['idtipo_pesquisa'];
    if (in_array($id, $pesquisasSeleccionadas) && isset($iconos_color[$id])) {
        $pesquisasActivas[] = $iconos_color[$id];
    }
}
?>

<div class="page">
  <div class="shell">

    <header class="topbar">
      <div class="topbar-left">
        <a href="<?= base_url('jornadas') ?>" class="back-btn">←</a>
        <div class="title">
          <h1>Editar Jornada</h1>
          <p>Administra la información y servicios de la jornada</p>
        </div>
      </div>
    </header>

    <form id="formJornada" method="post" action="<?= base_url('jornadas/actualizar') ?>" novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="id_jornada" value="<?= esc($jornada['id_jornada']) ?>">

      <main class="content">

        <section class="left-col">

          <?php if (session('errors')): ?>
            <div class="alert alert-danger alert-modern">
              <ul class="mb-0">
                <?php foreach (session('errors') as $err): ?>
                  <li><?= esc($err) ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          <?php endif; ?>

          <?php if (session('error')): ?>
            <div class="alert alert-danger alert-modern"><?= session('error') ?></div>
          <?php endif; ?>

          <div class="card">
            <div class="card-title">
              
              <span>Estado y Fecha</span>
            </div>

            <div class="grid-2">
              <div class="field">
                <label for="status_jor">Estado de la Jornada</label>
                <select class="select" id="status_jor" name="status_jor" required>
                  <option value="1" <?= ($jornada['status_jor'] == 1) ? 'selected' : '' ?>>Activa</option>
                  <option value="2" <?= ($jornada['status_jor'] == 2) ? 'selected' : '' ?>>Finalizada</option>
                </select>
              </div>

              <div class="field">
                <label for="fecha_inicio">Fecha</label>
                <input class="input" type="date" id="fecha_inicio" name="fecha_inicio"
                       value="<?= esc($jornada['fecha_inicio']) ?>" required>
              </div>
            </div>
          </div>

          <div class="card">
            <div class="card-title">
              
              <span>Detalles de la Jornada</span>
            </div>

            <div class="grid-2">
              <div class="field">
                <label for="nombre_jornada">Nombre de la Jornada</label>
                <input class="input" type="text" id="nombre_jornada" name="nombre_jornada"
                       value="<?= esc($jornada['nombre_jornada']) ?>" required>
              </div>

              <div class="field">
                <label for="organizacion_id">Organización</label>
                <select class="select" id="organizacion_id" name="organizacion_id"
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

              <div class="field">
                <label for="localidad">Institución o Localidad</label>
                <input class="input" type="text" id="localidad" name="localidad"
                       value="<?= esc($jornada['nombre_institucion'] ?? '') ?>">
              </div>

              <div class="field">
                <label>Tipo de Jornada</label>
                <div class="radio-group">
                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="publica"
                      <?= (($jornada['tipo_jornada'] ?? '') == 'publica') ? 'checked' : '' ?> required>
                    Pública
                  </label>
                  <label class="radio-option">
                    <input type="radio" name="tipo_jornada" value="privada"
                      <?= (($jornada['tipo_jornada'] ?? '') == 'privada') ? 'checked' : '' ?>>
                    Privada
                  </label>
                </div>
              </div>
            </div>

            <div class="field" style="margin-top:14px;">
              <label for="searchPlace">Ubicación en el mapa</label>
              <div class="search-box">
                <span class="search-icon">🔍</span>
                <input class="input" type="text" id="searchPlace" placeholder="Buscar lugar o dirección...">
              </div>
            </div>

            <div id="map"></div>

            <div class="location-grid">
              <div class="field">
                <label for="pais">País</label>
                <input type="text" class="input readonly-input" name="pais" id="pais"
                       value="<?= esc($jornada['pais'] ?? '') ?>" readonly>
              </div>

              <div class="field">
                <label for="estado">Estado</label>
                <input type="text" class="input readonly-input" name="estado" id="estado"
                       value="<?= esc($jornada['estado'] ?? '') ?>" readonly>
              </div>

              <div class="field">
                <label for="ciudad">Ciudad</label>
                <input type="text" class="input readonly-input" name="ciudad" id="ciudad"
                       value="<?= esc($jornada['ciudad'] ?? '') ?>" readonly>
              </div>
            </div>

            <input type="hidden" name="coords" id="coords" value="<?= esc($jornada['coordenadas'] ?? '') ?>">
          </div>

          <div class="card">
            <div class="card-title">
              
              <span>Pesquisas</span>
            </div>

            <div class="mb-3">
              <strong>Seleccionadas para esta Jornada (<?= count($pesquisasActivas) ?>)</strong>
            </div>

            <div class="chips mb-4" id="selectedPesquisasPreview">
              <?php if (!empty($pesquisasActivas)): ?>
                <?php foreach ($pesquisasActivas as $pes): ?>
                  <div class="chip">
                    <div class="chip-icon <?= esc($pes['clase']) ?>"><?= esc($pes['emoji']) ?></div>
                    <span><?= esc($pes['nombre']) ?></span>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <span class="label-muted">No hay pesquisas seleccionadas.</span>
              <?php endif; ?>
            </div>

            <label class="form-label">Seleccionar Pesquisas (al menos una)</label>

            <div class="pesquisa-selector">
              <?php foreach ($pesquisas as $p):
                  $id  = $p['idtipo_pesquisa'];
                  $ico = $iconos_color[$id] ?? null;
                  if (!$ico) continue;
                  $checked = in_array($id, $pesquisasSeleccionadas) ? 'checked' : '';
              ?>
                <label class="pesquisa-item">
                  <input type="checkbox"
                         name="pesquisas[]"
                         value="<?= $id ?>"
                         <?= $checked ?>
                         data-nombre="<?= esc($ico['nombre']) ?>"
                         data-emoji="<?= esc($ico['emoji']) ?>"
                         data-clase="<?= esc($ico['clase']) ?>">
                  <div class="pesquisa-icon-wrap">
                    <img src="<?= base_url('img/' . $ico['color']) ?>" class="icon-gris" alt="<?= esc($ico['nombre']) ?>">
                    <img src="<?= base_url('img/' . $ico['gris']) ?>" class="icon-color" alt="<?= esc($ico['nombre']) ?>">
                  </div>
                  <span class="pesquisa-label"><?= esc($ico['nombre']) ?></span>
                </label>
              <?php endforeach; ?>
            </div>

            <div class="text-danger mt-3" id="pesquisaError" style="display:none;">
              Selecciona al menos una pesquisa.
            </div>
          </div>
        </section>

        <aside class="right-col">
          <div class="card">
            <div class="card-title">
              
              <span>Resumen de la Jornada</span>
            </div>

            <div class="summary-list">
              <div class="summary-item">
                <span class="label-muted">Estado:</span>
                <span class="badge <?= $estadoBadge ?>" id="resumenEstado"><?= esc($estadoTexto) ?></span>
              </div>

              <div class="summary-item">
                <span class="label-muted">Fecha:</span>
                <strong id="resumenFecha"><?= esc($jornada['fecha_inicio']) ?></strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Nombre:</span>
                <strong id="resumenNombre"><?= esc($jornada['nombre_jornada']) ?></strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Organización:</span>
                <strong id="resumenOrganizacion">
                  <?php
                    $orgTexto = '';
                    foreach ($organizaciones as $o) {
                      if ($o['id_organizacion'] == $jornada['organizacion_id']) {
                        $orgTexto = $o['nombre_org'];
                        break;
                      }
                    }
                    echo esc($orgTexto);
                  ?>
                </strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Tipo:</span>
                <strong id="resumenTipo"><?= esc(ucfirst($jornada['tipo_jornada'] ?? '')) ?></strong>
              </div>

              <div class="summary-item">
                <span class="label-muted">Ubicación:</span>
                <strong id="resumenUbicacion">
                  📍 <?= esc(($jornada['ciudad'] ?? '') . (empty($jornada['estado']) ? '' : ', ' . $jornada['estado'])) ?>
                </strong>
              </div>

              <div class="summary-item" style="display:block;">
                <div class="label-muted" style="margin-bottom:10px;">Pesquisas seleccionadas:</div>
                <div id="resumenPesquisas">
                  <?php if (!empty($pesquisasActivas)): ?>
                    <div class="chips">
                      <?php foreach ($pesquisasActivas as $pes): ?>
                        <div class="chip">
                          <div class="chip-icon <?= esc($pes['clase']) ?>"><?= esc($pes['emoji']) ?></div>
                          <span><?= esc($pes['nombre']) ?></span>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  <?php else: ?>
                    <span class="label-muted">No hay pesquisas seleccionadas.</span>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        </aside>
      </main>

      <footer class="footer">
        <div class="note">
          Actualiza la jornada manteniendo la organización, ubicación y pesquisas correctamente asociadas.
        </div>

        <div class="actions">
          <a href="<?= base_url('jornadas') ?>" class="btn-modern btn-modern-secondary">Cancelar</a>
          <button type="submit" class="btn-modern btn-modern-primary">Actualizar</button>
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

const initialLat = <?= $lat ?>;
const initialLon = <?= $lon ?>;

document.addEventListener('DOMContentLoaded', () => {
    map = L.map('map').setView([initialLat, initialLon], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    marker = L.marker([initialLat, initialLon], { draggable: true }).addTo(map);

    marker.on('dragend', async function (e) {
    const pos = e.target.getLatLng();
    await updateAddress(pos.lat, pos.lng, true);
    });

    map.on('click', async function (e) {
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
            headers: {
                'Accept': 'application/json'
            }
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

    sincronizarResumen();
    bindResumenEventos();
});

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
async function updateAddress(lat, lon, reverseGeocode = true) {
    const coordsInput = document.getElementById('coords');
    const paisInput   = document.getElementById('pais');
    const estadoInput = document.getElementById('estado');
    const ciudadInput = document.getElementById('ciudad');

    coordsInput.value = `${parseFloat(lat).toFixed(6)}, ${parseFloat(lon).toFixed(6)}`;

    if (!reverseGeocode) return;

    try {
        const url = `https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`;

        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json'
            }
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

        if (typeof estadosVE !== 'undefined' && estadosVE[estado]) {
            estado = estadosVE[estado];
        }

        paisInput.value = pais;
        estadoInput.value = estado;
        ciudadInput.value = ciudad;

        actualizarResumenUbicacion();

    } catch (err) {
        console.error('Error obteniendo dirección:', err);
    }
}
function bindResumenEventos() {
    const status = document.getElementById('status_jor');
    const fecha = document.getElementById('fecha_inicio');
    const nombre = document.getElementById('nombre_jornada');
    const org = document.getElementById('organizacion_id');
    const radios = document.querySelectorAll('input[name="tipo_jornada"]');
    const checks = document.querySelectorAll('input[name="pesquisas[]"]');

    status.addEventListener('change', sincronizarResumen);
    fecha.addEventListener('input', sincronizarResumen);
    nombre.addEventListener('input', sincronizarResumen);

    if (org) {
        org.addEventListener('change', sincronizarResumen);
    }

    radios.forEach(r => r.addEventListener('change', sincronizarResumen));
    checks.forEach(c => c.addEventListener('change', sincronizarResumen));
}

function sincronizarResumen() {
    const status = document.getElementById('status_jor');
    const fecha = document.getElementById('fecha_inicio');
    const nombre = document.getElementById('nombre_jornada');
    const org = document.getElementById('organizacion_id');
    const tipo = document.querySelector('input[name="tipo_jornada"]:checked');

    const resumenEstado = document.getElementById('resumenEstado');
    const resumenFecha = document.getElementById('resumenFecha');
    const resumenNombre = document.getElementById('resumenNombre');
    const resumenOrganizacion = document.getElementById('resumenOrganizacion');
    const resumenTipo = document.getElementById('resumenTipo');

    if (status.value === '2') {
        resumenEstado.textContent = 'Finalizada';
        resumenEstado.className = 'badge badge-danger';
    } else {
        resumenEstado.textContent = 'Activa';
        resumenEstado.className = 'badge badge-success';
    }

    resumenFecha.textContent = fecha.value || '';
    resumenNombre.textContent = nombre.value || '';
    resumenOrganizacion.textContent = org ? org.options[org.selectedIndex].text : resumenOrganizacion.textContent;
    resumenTipo.textContent = tipo ? (tipo.value.charAt(0).toUpperCase() + tipo.value.slice(1)) : '';

    actualizarResumenPesquisas();
    actualizarResumenUbicacion();
}

function actualizarResumenUbicacion() {
    const ciudad = document.getElementById('ciudad').value || '';
    const estado = document.getElementById('estado').value || '';
    const resumenUbicacion = document.getElementById('resumenUbicacion');

    const ubicacion = [ciudad, estado].filter(Boolean).join(', ');
    resumenUbicacion.textContent = ubicacion ? `📍 ${ubicacion}` : '📍 Sin ubicación';
}

function actualizarResumenPesquisas() {
    const checks = document.querySelectorAll('input[name="pesquisas[]"]:checked');
    const preview = document.getElementById('selectedPesquisasPreview');
    const resumen = document.getElementById('resumenPesquisas');

    if (checks.length === 0) {
        preview.innerHTML = '<span class="label-muted">No hay pesquisas seleccionadas.</span>';
        resumen.innerHTML = '<span class="label-muted">No hay pesquisas seleccionadas.</span>';
        return;
    }

    let html = '';
    checks.forEach(chk => {
        const nombre = chk.dataset.nombre || '';
        const emoji = chk.dataset.emoji || '🩺';
        const clase = chk.dataset.clase || 'blue';

        html += `
            <div class="chip">
              <div class="chip-icon ${clase}">${emoji}</div>
              <span>${nombre}</span>
            </div>
        `;
    });

    preview.innerHTML = html;
    resumen.innerHTML = `<div class="chips">${html}</div>`;
}

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
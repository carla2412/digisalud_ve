<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>
<div class="container my-4">
    <div class="buscar-container">
        <div class="breadcrumb-digi"><a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt; <a href="<?= base_url("jornadas/$jornada_id/beneficiarios") ?>">Beneficiarios</a> &gt; <span class="active">Buscar o registrar</span></div>
        <div class="filtro-title">Filtros / Búsqueda de beneficiarios</div>
        <div class="search-row"><input type="text" id="campoBusqueda" placeholder="Nombre, apellido o ID Digisalud..." autocomplete="off">
        <button class="btn-buscar" onclick="ejecutarBusqueda()">Buscar</button></div>
        <div class="resultado-header" id="resultadoHeader" style="display:none;">
            <span class="resultado-count" id="resultadoCount"></span>
            <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/create") ?>" class="btn-registrar-nuevo">
                + Registrar nuevo
            </a>
        </div>

        <div id="sinResultados" style="display:none;">
            <div class="empty-state">
                <i class="bi bi-person-x" style="font-size:2.5rem;color:#dee2e6;"></i>
                <p class="mt-2">No se encontró ningún beneficiario</p>
                <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/create") ?>" class="btn-registrar-nuevo">
                    + Registrar nuevo
                </a>
            </div>
        </div>

        <div id="estadoInicial">
            <p class="hint-text">Escribe al menos 2 caracteres y presiona Buscar</p>
            <div class="text-center mt-3">
                <a href="<?= base_url("jornadas/$jornada_id/beneficiarios/create") ?>" class="btn-registrar-nuevo">
                    + Registrar nuevo beneficiario
                </a>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
<script>
    const jornadaId = <?= $jornada_id ?>;
    document.getElementById('campoBusqueda').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') ejecutarBusqueda();
    });

    function ejecutarBusqueda() {
        const q = document.getElementById('campoBusqueda').value.trim();
        if (q.length < 2) return;
        document.getElementById('estadoInicial').style.display = 'none';
        fetch(`<?= base_url('beneficiarios/buscar-ajax') ?>?q=${encodeURIComponent(q)}`).then(r => r.json()).then(data => {
            const c = document.getElementById('resultados'),
                s = document.getElementById('sinResultados'),
                h = document.getElementById('resultadoHeader'),
                cnt = document.getElementById('resultadoCount');
            if (data.length === 0) {
                c.innerHTML = '';
                s.style.display = 'block';
                h.style.display = 'none';
                return;
            }
            s.style.display = 'none';
            h.style.display = 'flex';
            cnt.textContent = `Se encontró ${data.length} beneficiario(s)`;
            let html = '';
            data.forEach(b => {
                const ff = b.fecha_nacimiento ? b.fecha_nacimiento.split('-').reverse().join('/') : '';
                const p = b.parentesco || '';
                html += `<div class="resultado-card"><div class="resultado-info"><div class="resultado-nombre">${(b.apellidos||'').toUpperCase()}, ${(b.nombres||'').toUpperCase()}</div><div class="resultado-meta"><span class="lbl lbl-id">ID:</span>${b.id_digisalud||'—'} <span class="lbl lbl-fn">FN:</span> ${ff} ${b.edad?' - '+b.edad:''}</div>${p?`<div class="resultado-meta"><span class="lbl lbl-rep">Parentesco:</span> ${p}</div>`:'<div class="resultado-meta"><span class="lbl lbl-rep">Representante:</span></div>'}</div><form method="post" action="/jornadas/${jornadaId}/asociar/${b.id_beneficiario}" style="margin:0;"><button type="submit" class="btn-agregar">+ Agregar</button></form></div>`;
            });
            c.innerHTML = html;
        });
    }
    document.getElementById('campoBusqueda').focus();
</script>
<?= $this->endSection() ?>
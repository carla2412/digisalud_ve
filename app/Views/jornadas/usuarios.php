<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
/* ═══ LAYOUT DOS PANELES ═══ */
.usuarios-jornada-wrap {
    display: flex;
    gap: 0;
    min-height: calc(100vh - 120px);
}

.panel-izq {
    flex: 1;
    border-right: 1px solid #e9ecef;
    padding: 1.5rem;
    overflow-y: auto;
}

.panel-der {
    width: 420px;
    min-width: 360px;
    padding: 1.5rem;
    background: #fff;
    overflow-y: auto;
    position: relative;
}

@media (max-width: 992px) {
    .usuarios-jornada-wrap {
        flex-direction: column;
    }
    .panel-der {
        width: 100%;
        min-width: auto;
        border-right: none;
        border-top: 1px solid #e9ecef;
    }
}

/* ═══ PANEL IZQUIERDO: BÚSQUEDA ═══ */
.buscar-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: #0b1b3f;
    margin-bottom: 1rem;
}

.filtro-label {
    font-size: .75rem;
    font-weight: 700;
    color: #555;
    text-transform: uppercase;
    letter-spacing: .5px;
    margin-bottom: .5rem;
}

.campo-palabra {
    font-size: .8rem;
    color: #888;
    margin-bottom: 2px;
}

.search-input-jornada {
    width: 100%;
    border: none;
    border-bottom: 1.5px solid #c5cad0;
    padding: 8px 0;
    font-size: .95rem;
    outline: none;
    background: transparent;
    margin-bottom: 1.5rem;
}

.search-input-jornada:focus {
    border-bottom-color: #3695f5;
}

.btn-buscar-jornada {
    border: 1.5px solid #3695f5;
    color: #3695f5;
    background: #fff;
    border-radius: 25px;
    padding: 6px 24px;
    font-size: .85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    cursor: pointer;
    transition: all .2s;
}

.btn-buscar-jornada:hover {
    background: #3695f5;
    color: #fff;
}

.iconos-accion {
    display: flex;
    gap: 10px;
    align-items: center;
}

.icono-circular {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 2px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all .2s;
    background: #f8f9fa;
}

.icono-circular:hover {
    border-color: #3695f5;
    background: #e8eaf8;
}

/* ═══ RESULTADO DE BÚSQUEDA ═══ */
.usuario-resultado {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 14px 16px;
    border-bottom: 1px solid #eee;
    transition: background .15s;
    cursor: pointer;
}

.usuario-resultado:hover {
    background: #f0f4ff;
}

.usuario-resultado .nombre {
    font-size: 1rem;
    font-weight: 700;
    color: #0b1b3f;
}

.usuario-resultado .profesion {
    font-size: .82rem;
    font-weight: 600;
    color: #333;
}

.usuario-resultado .organizacion {
    font-size: .78rem;
    color: #888;
}

.check-asignado {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    border: 2px solid #28a745;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #28a745;
    font-size: .9rem;
}

/* ═══ PANEL DERECHO: DETALLE JORNADA ═══ */
.jornada-detail-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #0b1b3f;
    margin-bottom: 2px;
}

.jornada-detail-org {
    font-size: .9rem;
    color: #555;
    margin-bottom: 1rem;
}

.detail-label {
    font-size: .72rem;
    font-weight: 600;
    color: #888;
    text-transform: capitalize;
    margin-bottom: 2px;
}

.detail-value {
    font-size: .9rem;
    font-weight: 600;
    color: #0b1b3f;
    margin-bottom: 1rem;
}

.roles-list label {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 4px 0;
    font-size: .88rem;
    color: #333;
    cursor: pointer;
}

.roles-list input[type="radio"] {
    accent-color: #3695f5;
}

.close-panel {
    position: absolute;
    top: 12px;
    right: 16px;
    font-size: 1.2rem;
    color: #999;
    cursor: pointer;
    text-decoration: none;
}

.close-panel:hover {
    color: #333;
}

/* ═══ TABLA DE ASIGNADOS ═══ */
.tabla-asignados {
    margin-top: 1.5rem;
}

.tabla-asignados .asignado-card {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 14px;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    margin-bottom: 8px;
    background: #fff;
}

.tabla-asignados .asignado-info .nombre {
    font-weight: 600;
    font-size: .9rem;
    color: #0b1b3f;
}

.tabla-asignados .asignado-info .rol-badge {
    font-size: .75rem;
    padding: 2px 10px;
    border-radius: 12px;
    background: #e8eaf8;
    color: #101a61;
    font-weight: 600;
}

.btn-quitar {
    border: none;
    background: transparent;
    color: #dc3545;
    font-size: .8rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 4px;
    transition: background .15s;
}

.btn-quitar:hover {
    background: #fde8ea;
}

.sin-permiso-msg {
    font-size: .8rem;
    color: #888;
    font-style: italic;
    margin-top: 1rem;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="container-fluid my-0 px-0">

    <div class="breadcrumb-digi px-3 pt-3">
        <a href="<?= base_url('jornadas') ?>">Jornadas</a> &gt;
        <span class="active">Usuarios — <?= esc($jornada['nombre_jornada']) ?></span>
    </div>

    <div class="usuarios-jornada-wrap">

        <!-- ═══════════════════════════════════════ -->
        <!-- PANEL IZQUIERDO: Búsqueda de usuarios  -->
        <!-- ═══════════════════════════════════════ -->
        <div class="panel-izq">

            <h4 class="buscar-title">Busca tu usuario</h4>
            <div class="filtro-label">FILTRO(S)</div>

            <div class="campo-palabra">Palabra clave</div>
            <input type="text" id="campoBusqueda" class="search-input-jornada"
                   placeholder="Nombre o correo..." autocomplete="off">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <button class="btn-buscar-jornada" onclick="buscarUsuarios()">BUSCAR</button>
            </div>

            <!-- Resultados de búsqueda -->
            <div id="resultadosBusqueda"></div>

            <div id="estadoInicial" class="text-center mt-4">
                <p class="hint-text" style="color:#aaa; font-size:.85rem;">
                    Escribe al menos 2 caracteres y presiona Buscar
                </p>
            </div>

            <!-- ═══ USUARIOS YA ASIGNADOS ═══ -->
            <div class="tabla-asignados" id="tablaAsignados">
                <h6 class="fw-bold mt-4 mb-3" style="color:#0b1b3f;">
                    <i class="bi bi-people-fill"></i> Usuarios asignados (<?= count($usuariosAsignados) ?>)
                </h6>

                <?php if (!empty($usuariosAsignados)): ?>
                    <?php foreach ($usuariosAsignados as $ua): ?>
                        <div class="asignado-card" id="asignado-<?= $ua['id_ruc'] ?>">
                            <div class="asignado-info">
                                <div class="nombre"><?= esc($ua['nombres'] . ' ' . $ua['apellidos']) ?></div>
                                <span class="rol-badge"><?= esc($ua['nombre_rol']) ?></span>
                                <small class="text-muted ms-2"><?= esc($ua['nombre_org'] ?? '') ?></small>
                            </div>
                            <?php if (in_array($rolSesion, [1,2,3,4])): ?>
                                <button class="btn-quitar" onclick="eliminarUsuario(<?= $ua['id_ruc'] ?>, '<?= esc($ua['nombres'] . ' ' . $ua['apellidos']) ?>')">
                                    <i class="bi bi-x-circle"></i> Quitar
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted" style="font-size:.85rem;">No hay usuarios asignados aún.</p>
                <?php endif; ?>
            </div>

        </div>

        <!-- ═══════════════════════════════════════ -->
        <!-- PANEL DERECHO: Detalle de la jornada   -->
        <!-- ═══════════════════════════════════════ -->
        <div class="panel-der">

            <a href="<?= base_url('jornadas') ?>" class="close-panel">&times;</a>

            <div class="jornada-detail-title"><?= esc($jornada['nombre_jornada']) ?></div>
            <div class="jornada-detail-org"><?= esc($jornada['nombre_org'] ?? '') ?></div>

            <!-- Pesquisas -->
            <div class="detail-label">Pesquisas</div>
            <div class="mb-3" style="padding: 4px 0;">
                <?php
                    $iconos = [
                        '1' => 'antropometria2.svg',
                        '2' => 'sanguinea2.svg',
                        '3' => 'visual2.svg',
                        '4' => 'signosVitales2.svg',
                        '5' => 'medicinaGeneral2.svg',
                        '6' => 'vacunacion2.svg',
                    ];
                ?>
                <?php if (!empty($jornada['pesquisas'])): ?>
                    <?php foreach (explode(',', $jornada['pesquisas']) as $p): ?>
                        <?php $p = trim($p); if (isset($iconos[$p])): ?>
                            <img src="<?= base_url('img/' . $iconos[$p]) ?>" width="36" class="me-1">
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Ubicación -->
            <div class="detail-label">Ubicación</div>
            <div class="detail-value"><?= esc($jornada['ciudad'] ?? 'Sin ubicación') ?></div>

            <!-- Fechas -->
            <div class="row">
                <div class="col-6">
                    <div class="detail-label">Fecha inicio</div>
                    <div class="detail-value"><?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?></div>
                </div>
                <div class="col-6">
                    <div class="detail-label">Fecha Fin</div>
                    <div class="detail-value"><?= date('d-m-Y', strtotime($jornada['fecha_inicio'])) ?></div>
                </div>
            </div>

            <!-- Roles disponibles para asignar -->
            <div class="detail-label">Asignar Rol</div>
            <div class="roles-list mb-3">
                <label><input type="radio" name="rol_asignar" value="4"> Organizador</label>
                <label><input type="radio" name="rol_asignar" value="5"> Coordinador</label>
                <label><input type="radio" name="rol_asignar" value="6"> Registrador</label>
                <label><input type="radio" name="rol_asignar" value="7"> Data</label>
            </div>

            <!-- Status -->
            <div class="row">
                <div class="col-6">
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <?= $jornada['status_jor'] == 1 ? 'En Marcha' : ($jornada['status_jor'] == 2 ? 'Finalizada' : 'Inactiva') ?>
                    </div>
                </div>
                <div class="col-6">
                    <div class="detail-label">N de usuarios asignados</div>
                    <div class="detail-value" id="conteoAsignados"><?= count($usuariosAsignados) ?></div>
                </div>
            </div>

            <?php if (!in_array($rolSesion, [1,2,3,4])): ?>
                <p class="sin-permiso-msg">
                    <i class="bi bi-lock"></i> No tienes permisos para asignar o eliminar usuarios.
                </p>
            <?php endif; ?>

        </div>

    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

const jornadaId    = <?= $jornada_id ?>;
const rolSesion    = <?= $rolSesion ?>;
const puedeAsignar = [1,2,3,4].includes(rolSesion);
const csrfName     = '<?= csrf_token() ?>';
const csrfToken    = '<?= csrf_hash() ?>';

// ══════════════════════════════
// BUSCAR USUARIOS
// ══════════════════════════════
document.getElementById('campoBusqueda').addEventListener('keydown', function(e) {
    if (e.key === 'Enter') buscarUsuarios();
});

function buscarUsuarios() {
    const q = document.getElementById('campoBusqueda').value.trim();
    if (q.length < 2) return;

    document.getElementById('estadoInicial').style.display = 'none';

    fetch(`<?= base_url('jornadas/usuarios/buscar-ajax') ?>?q=${encodeURIComponent(q)}&jornada_id=${jornadaId}`)
        .then(r => r.json())
        .then(data => {
            const c = document.getElementById('resultadosBusqueda');

            if (data.length === 0) {
                c.innerHTML = '<p class="text-muted text-center mt-3" style="font-size:.85rem;">No se encontraron usuarios</p>';
                return;
            }

            let html = '';
            data.forEach(u => {
                const yaAsignado = u.ya_asignado;
                const profesion  = u.profesion || 'Sin Profesión';
                const org        = u.nombre_org || 'Independiente';

                html += `<div class="usuario-resultado" 
                              data-id="${u.id_usuario}" 
                              onclick="${!yaAsignado && puedeAsignar ? 'seleccionarUsuario(' + u.id_usuario + ')' : ''}">
                    <div>
                        <div class="nombre">${u.nombres} ${u.apellidos}</div>
                        <div class="profesion">${profesion}</div>
                        <div class="organizacion">${org}</div>
                    </div>
                    ${yaAsignado
                        ? '<div class="check-asignado"><i class="bi bi-check-lg"></i></div>'
                        : (puedeAsignar
                            ? '<span style="font-size:.75rem;color:#3695f5;"><i class="bi bi-plus-circle"></i> Asignar</span>'
                            : '')
                    }
                </div>`;
            });

            c.innerHTML = html;
        });
}

// ══════════════════════════════
// SELECCIONAR Y ASIGNAR USUARIO
// ══════════════════════════════
function seleccionarUsuario(idUsuario) {
    if (!puedeAsignar) return;

    const rolSeleccionado = document.querySelector('input[name="rol_asignar"]:checked');

    if (!rolSeleccionado) {
        Swal.fire({
            icon: 'warning',
            title: 'Selecciona un rol',
            text: 'Primero selecciona un rol en el panel derecho antes de asignar un usuario.',
            confirmButtonColor: '#3695f5'
        });
        return;
    }

    const idRol = rolSeleccionado.value;

    Swal.fire({
        title: '¿Asignar usuario?',
        text: 'Se asignará con el rol seleccionado a esta jornada.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, asignar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#3695f5'
    }).then(result => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append(csrfName, csrfToken);
            formData.append('id_usuario', idUsuario);
            formData.append('id_rol', idRol);

            fetch(`<?= base_url('jornadas') ?>/${jornadaId}/usuarios/asignar`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario asignado',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.error || 'No se pudo asignar el usuario'
                    });
                }
            });
        }
    });
}

// ══════════════════════════════
// ELIMINAR USUARIO DE JORNADA
// ══════════════════════════════
function eliminarUsuario(idRuc, nombre) {
    if (!puedeAsignar) return;

    Swal.fire({
        title: '¿Quitar usuario?',
        text: `Se eliminará a ${nombre} de esta jornada.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, quitar',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545'
    }).then(result => {
        if (result.isConfirmed) {
            const formData = new FormData();
            formData.append(csrfName, csrfToken);
            formData.append('id_ruc', idRuc);

            fetch(`<?= base_url('jornadas') ?>/${jornadaId}/usuarios/eliminar`, {
                method: 'POST',
                body: formData
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario eliminado',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: resp.error || 'No se pudo quitar el usuario'
                    });
                }
            });
        }
    });
}

</script>
<?= $this->endSection() ?>
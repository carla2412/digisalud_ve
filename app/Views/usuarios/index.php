<?= $this->extend('layouts/main') ?>

<?= $this->section('css') ?>
<style>
    * {
        box-sizing: border-box;
    }

    .usu_mod-org-page {
        background: #eef2f7;
        padding: 20px;
    }

    .usu_mod-org-container {
        max-width: 1600px;
        margin: 0 auto;
        background: #fff;
        border-radius: 24px;
        padding: 28px 32px 34px;
        box-shadow: 0 8px 30px rgba(31, 42, 68, 0.08);
    }

    .usu_mod-org-breadcrumb {
        font-size: 14px;
        color: #6d7890;
        margin-bottom: 18px;
    }

    .usu_mod-org-breadcrumb a {
        color: #6d7890;
        text-decoration: none;
    }

    .usu_mod-org-breadcrumb span {
        color:  var(--ds-primary) 
        font-weight: 600;
    }

    .usu_mod-topbar {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 24px;
    }

    .usu_mod-title h1 {
        font-size: 56px;
        line-height: 1.1;
        margin-bottom: 8px;
        color: #101a61;
    }

    .usu_mod-title p {
        font-size: 18px;
        color: #6b7280;
        margin: 0;
    }

    .usu_mod-filters {
        display: grid;
        grid-template-columns: 1fr;
        gap: 14px;
        margin-bottom: 36px;
    }

    .usu_mod-input-custom {
        background: #fff;
        border: 1px solid #dbe3f0;
        border-radius: 16px;
        height: 56px;
        display: flex;
        align-items: center;
        padding: 0 18px;
        color: #667085;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .usu_mod-input-custom input {
        border: none;
        outline: none;
        width: 100%;
        font-size: 16px;
        margin-left: 12px;
        background: transparent;
        color: #334155;
    }

    .usu_mod-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 28px;
        margin-bottom: 26px;
        overflow: visible !important;
    }

    .usu_mod-user-item {
        position: relative;
        overflow: visible !important;
        z-index: 1;
    }

    .usu_mod-user-card {
        position: relative;
        background: #fff;
        border-radius: 24px;
        padding: 26px;
        min-height: 320px;
        box-shadow: 0 10px 28px rgba(31, 42, 68, 0.08);
        overflow: visible !important;
        border: 1px solid #eef2f8;
        transition: all 0.25s ease;
        z-index: 1;
    }

    .usu_mod-user-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 34px rgba(31, 42, 68, 0.12);
    }

    .usu_mod-user-card.usu_mod-inactivo {
        opacity: 0.75;
        background: #f8fafc;
    }

    .usu_mod-menu {
        position: absolute;
        right: 20px;
        top: 18px;
        font-size: 24px;
        color: #667085;
        z-index: 10;
    }

    .usu_mod-avatar {
        width: 72px;
        height: 72px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 22px;
        overflow: hidden;
        position: relative;
        z-index: 2;
    }

    .usu_mod-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .usu_mod-blue {
        background:  var(--ds-bg) ;
        color:  var(--ds-primary) ;
    }

    .usu_mod-green {
        background: #dff4e7;
        color:  var(--ds-success) ;
    }

    .usu_mod-purple {
        background: #eedfff;
        color: #7c3aed;
    }

    .usu_mod-user-card h3 {
        font-size: 22px;
        margin-bottom: 10px;
        color: #14213d;
        position: relative;
        z-index: 2;
    }

    .usu_mod-tag {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 13px;
        font-weight: 600;
        margin-bottom: 18px;
        position: relative;
        z-index: 2;
    }

    .usu_mod-tag.usu_mod-blue {
        background: #e7f0ff;
        color:  var(--ds-primary) 
    }

    .usu_mod-tag.usu_mod-green {
        background: #e8f8ee;
        color: #16a34a;
    }

    .usu_mod-tag.usu_mod-purple {
        background: #f1e8ff;
        color: #7c3aed;
    }

    .usu_mod-tag.usu_mod-red {
        background: #fee2e2;
        color: #dc2626;
    }

    .usu_mod-tag.usu_mod-gray {
        background: #eef2f7;
        color: #64748b;
    }

    .usu_mod-email,
    .usu_mod-org-info {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #5b6478;
        font-size: 16px;
        margin-bottom: 12px;
        position: relative;
        z-index: 2;
        word-break: break-word;
    }

    .usu_mod-divider {
        height: 1px;
        background: #e8edf5;
        margin: 22px 0;
        position: relative;
        z-index: 2;
    }

    .usu_mod-actions {
        display: flex;
        gap: 12px;
        position: relative;
        z-index: 20;
        flex-wrap: wrap;
    }

    .usu_mod-btn-action-custom {
        border-radius: 14px;
        padding: 12px 20px;
        font-size: 15px;
        font-weight: 600;
        border: 1px solid #dbe3f0;
        background: #fff;
        cursor: pointer;
        text-decoration: none;
        transition: all .2s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color:  var(--ds-primary) 
    }

    .usu_mod-btn-action-custom:hover {
        transform: translateY(-1px);
        text-decoration: none;
        background: #f8fbff;
        color: #1b7ae2;
    }

    .dropdown-menu {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(31, 42, 68, 0.15);
        z-index: 9999;
    }

    .dropdown-item {
        
        padding: 10px 16px;
    }

    .dropdown-item i {
        width: 18px;
    }

    .usu_mod-bg-shape {
        position: absolute;
        right: -40px;
        bottom: -50px;
        width: 220px;
        height: 220px;
        border-radius: 50%;
        opacity: 0.18;
        z-index: 0;
    }

    .usu_mod-shape-blue {
        background: #bcd3ff;
    }

    .usu_mod-shape-green {
        background: #b9ebc9;
    }

    .usu_mod-shape-purple {
        background: #dec5ff;
    }

    .usu_mod-shape-red {
        background: #ffc5d3;
    }

    .usu_mod-shape-icon {
        position: absolute;
        right: 34px;
        bottom: 40px;
        font-size: 64px;
        opacity: 0.18;
        z-index: 1;
    }

    .usu_mod-meta-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: #667085;
        margin-top: 10px;
        font-size: 16px;
        gap: 16px;
        flex-wrap: wrap;
    }

    .usu_mod-empty-state {
        grid-column: 1 / -1;
        background: #fff;
        border: 1px dashed #cbd5e1;
        border-radius: 20px;
        padding: 40px 20px;
        text-align: center;
        color: #64748b;
    }

    .usu_mod-user-item:has(.dropdown-menu.show) {
        z-index: 99999;
    }

    .usu_mod-user-card:has(.dropdown-menu.show) {
        z-index: 99999;
    }

    .dropdown {
        position: relative;
        z-index: 100000;
    }


    .dropdown-menu {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 12px 30px rgba(31, 42, 68, 0.15);
        z-index: 100001 !important;
    }

    @media (max-width: 1200px) {
        .usu_mod-cards {
            grid-template-columns: 1fr 1fr;
        }

        .usu_mod-topbar {
            flex-direction: column;
            align-items: stretch;
        }

        .usu_mod-title h1 {
            font-size: 40px;
        }
    }

    @media (max-width: 768px) {
        .usu_mod-org-page {
            padding: 12px;
        }

        .usu_mod-org-container {
            padding: 20px;
            border-radius: 18px;
        }

        .usu_mod-cards {
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .usu_mod-title h1 {
            font-size: 32px;
        }

        .usu_mod-actions {
            flex-direction: column;
        }

        .usu_mod-btn-action-custom {
            justify-content: center;
            width: 100%;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<div class="usu_mod-org-page">
    <div class="usu_mod-org-container">

        <div class="usu_mod-org-breadcrumb">
            <a href="<?= base_url('inicio') ?>">Inicio</a> &nbsp;›&nbsp; <span>Usuarios</span>
        </div>

        <div class="usu_mod-topbar">
            <div class="usu_mod-title">
                <h1>Usuarios</h1>
                <p>Gestiona los usuarios registrados en Digisalud.</p>
            </div>
        </div>

        <div class="usu_mod-filters">
            <div class="usu_mod-input-custom">
                <i class="fas fa-search"></i>
                <input
                    type="text"
                    id="searchUser"
                    placeholder="Buscar usuario por nombre, correo, rol u organización...">
            </div>
        </div>

        <div class="usu_mod-cards" id="contenedorUsuarios"></div>

        <div id="sinResultados" class="usu_mod-empty-state d-none">
            <i class="fas fa-users-slash fa-2x mb-3"></i>
            <h4 class="mb-2">No se encontraron usuarios</h4>
            <p class="mb-0">Intenta con otro criterio de búsqueda.</p>
        </div>

        <div class="usu_mod-meta-row">
            <div>
                Mostrando <span id="totalUsuarios">0</span> usuario<span id="pluralUsuarios">s</span>
            </div>
        </div>

    </div>
</div>

<?= $this->include('usuarios/modals/agregarOrg') ?>
<?= $this->include('usuarios/modals/cambiarCorreo') ?>
<?= $this->include('usuarios/modals/cambiarPassword') ?>
<?= $this->include('usuarios/modals/confirmarBloqueo') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    $(document).ready(function() {

        const orgSesion = <?= json_encode($orgSesion ?? null) ?>;
        const rolSesion = parseInt(<?= json_encode($rolSesion ?? 0) ?>, 10) || 0;
        const baseUrl = '<?= rtrim(base_url(), "/") ?>/';

        const csrfName = '<?= csrf_token() ?>';
        let csrfToken = '<?= csrf_hash() ?>';

        const urlListado = "<?= base_url('usuarios/listado') ?>";
        const urlAgregarOrgBase = "<?= base_url('usuarios/agregar-organizacion') ?>";
        const urlCambiarCorreoBase = "<?= base_url('usuarios/cambiar-correo') ?>";
        const urlCambiarPassBase = "<?= base_url('usuarios/cambiar-password') ?>";
        const urlBloquearBase = "<?= base_url('usuarios/bloquear') ?>";

        let usuariosData = [];

        function escapeHtml(value) {
            return String(value ?? '')
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        function cargarUsuarios() {
            $.ajax({
                url: urlListado,
                type: "GET",
                dataType: "json",
                success: function(response) {
                    usuariosData = response.data || [];
                    $('#totalUsuarios').text(usuariosData.length);
                    $('#pluralUsuarios').text(usuariosData.length === 1 ? '' : 's');
                    renderUsuarios(usuariosData);
                },
                error: function(xhr, status, error) {
                    console.error("Error cargando usuarios:", error);

                    $('#contenedorUsuarios').html(`
                    <div class="usu_mod-empty-state">
                        <i class="fas fa-triangle-exclamation fa-2x mb-3"></i>
                        <h4 class="mb-2">Error al cargar usuarios</h4>
                        <p class="mb-0">Ocurrió un problema al consultar el listado.</p>
                    </div>
                `);
                }
            });
        }

        function nombreRolAmigable(rolNombreOriginal) {
            if (!rolNombreOriginal) return 'Sin rol';

            const roles = {
                'ADMIN_DIGI': 'Digisalud',
                'ADMIN_ORG': 'Administrador Org',
                'REGISTRO': 'Registro de Data',
                'ADMINISTRADOR': 'TI Digisalud',
                'COORDINADOR': 'Coordinador Org',
                'VIEWER': 'Ver Data'
            };

            return roles[rolNombreOriginal] || rolNombreOriginal;
        }

        function generarAvatar(row, color) {
            const nombre = row.nombres ?? '';
            const apellido = row.apellidos ?? '';
            const fotoUrl = row.foto_url ?? '';

            if (fotoUrl) {
                return `
                <div class="usu_mod-avatar ${color}">
                    <img 
                        src="${baseUrl}${escapeHtml(fotoUrl)}" 
                        alt="${escapeHtml(nombre)}"
                        onerror="this.remove(); this.parentElement.innerHTML='${escapeHtml((nombre.charAt(0) || '?').toUpperCase())}'"
                    >
                </div>
            `;
            }

            const inicialN = nombre ? nombre.charAt(0).toUpperCase() : '';
            const inicialA = apellido ? apellido.charAt(0).toUpperCase() : '';
            const iniciales = inicialN + inicialA || '?';

            return `<div class="usu_mod-avatar ${color}">${escapeHtml(iniciales)}</div>`;
        }

        function renderUsuarios(data) {
            const contenedor = $('#contenedorUsuarios');
            const sinResultados = $('#sinResultados');

            contenedor.html('');

            if (!data.length) {
                sinResultados.removeClass('d-none');
                return;
            }

            sinResultados.addClass('d-none');

            const colores = ['usu_mod-blue', 'usu_mod-green', 'usu_mod-purple'];
            const shapes = ['usu_mod-shape-blue', 'usu_mod-shape-green', 'usu_mod-shape-purple', 'usu_mod-shape-red'];

            data.forEach((row, index) => {
                const color = colores[index % colores.length];
                const shapeColor = shapes[index % shapes.length];

                const nombre = row.nombres ?? '';
                const apellido = row.apellidos ?? '';
                const correo = row.email ?? '';
                const organizacion = row.nombre_organizacion ?? 'Independiente';
                const idUsuario = row.id_usuario ?? '';
                const rolNombreOriginal = row.nombre_rol ?? '';
                const nombreMostrarRol = nombreRolAmigable(rolNombreOriginal);
                const organizacionId = parseInt(row.organizacion_id ?? 0, 10);
                const statusUsu = parseInt(row.status_usu ?? 1, 10);

                const esIndependiente = organizacionId === 1;
                const puedeAgregarOrg = esIndependiente && [1, 2, 3].includes(rolSesion);
                const puedeBloquear = [1, 2, 3].includes(rolSesion);

                const tagRol = rolNombreOriginal ?
                    `<span class="usu_mod-tag ${color}"><i class="fas fa-user-shield me-1"></i>${escapeHtml(nombreMostrarRol)}</span>` :
                    `<span class="usu_mod-tag usu_mod-red"><i class="fas fa-exclamation-triangle me-1"></i>Sin rol</span>`;

                let acciones = '';

                if (puedeAgregarOrg) {
                    acciones += `
                    <li>
                        <a class="dropdown-item usu_mod-btnAgregarOrg" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-person-fill-add me-2"></i> Agregar organización
                        </a>
                    </li>
                `;
                }

                if (puedeBloquear && !puedeAgregarOrg) {
                    acciones += `
                    <li>
                        <a class="dropdown-item usu_mod-btnCorreo" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-envelope-fill me-2"></i> Cambiar correo
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item usu_mod-btnPass" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-key-fill me-2"></i> Cambiar contraseña
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger usu_mod-btnBloquear" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-lock-fill me-2"></i> Bloquear / desbloquear
                        </a>
                    </li>
                `;
                }

                if (acciones === '') {
                    acciones = `<li><span class="dropdown-item-text text-muted small px-3">Sin acciones disponibles</span></li>`;
                }

                const avatarHtml = generarAvatar(row, color);

                const searchText = `${nombre} ${apellido} ${correo} ${organizacion} ${nombreMostrarRol}`.toLowerCase();

                const card = `
                <div class="usu_mod-user-item" data-search="${escapeHtml(searchText)}">
                    <div class="usu_mod-user-card ${statusUsu !== 1 ? 'usu_mod-inactivo' : ''}">
                        <div class="usu_mod-menu">
                            <i class="fas fa-user"></i>
                        </div>

                        ${avatarHtml}

                        <h3>${escapeHtml(nombre)} ${escapeHtml(apellido)}</h3>

                        ${tagRol}

                        <div class="usu_mod-org-info">
                            <i class="fas fa-building"></i>
                            <span>${escapeHtml(organizacion)}</span>
                        </div>

                        <div class="usu_mod-email">
                            <i class="fas fa-envelope"></i>
                            <span>${escapeHtml(correo || '—')}</span>
                        </div>

                        <div class="usu_mod-divider"></div>

                        <div class="usu_mod-actions">
                            <div class="dropdown">
                                <button 
                                    class="usu_mod-btn-action-custom dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false"
                                >
                                    <i class="fas fa-wrench"></i>
                                    Acciones
                                </button>

                                <ul class="dropdown-menu dropdown-menu-end">
                                    ${acciones}
                                </ul>
                            </div>
                        </div>

                        <div class="usu_mod-bg-shape ${shapeColor}"></div>
                        <div class="usu_mod-shape-icon"><i class="fas fa-user"></i></div>
                    </div>
                </div>
            `;

                contenedor.append(card);
            });

            activarEventos();
        }

        $('#searchUser').on('keyup', function() {
            const texto = $(this).val().toLowerCase().trim();
            let visibles = 0;

            $('.usu_mod-user-item').each(function() {
                const contenido = String($(this).data('search') || '');

                if (contenido.includes(texto)) {
                    $(this).show();
                    visibles++;
                } else {
                    $(this).hide();
                }
            });

            $('#totalUsuarios').text(visibles);
            $('#pluralUsuarios').text(visibles === 1 ? '' : 's');

            if (visibles === 0) {
                $('#sinResultados').removeClass('d-none');
            } else {
                $('#sinResultados').addClass('d-none');
            }
        });

        function cerrarModal(idModal) {
            const el = document.getElementById(idModal);
            const modal = bootstrap.Modal.getInstance(el);
            if (modal) modal.hide();
        }

        function activarEventos() {
            $('.usu_mod-btnAgregarOrg').off('click').on('click', function() {
                $('#agregarId').val($(this).data('id'));
                new bootstrap.Modal(document.getElementById('modalAgregarOrg')).show();
            });

            $('.usu_mod-btnBloquear').off('click').on('click', function() {
                $('#bloqueoId').val($(this).data('id'));
                $('#textoBloqueo').html("¿Deseas <b>cambiar el estado</b> de este usuario?");
                new bootstrap.Modal(document.getElementById('modalBloqueo')).show();
            });

            $('.usu_mod-btnCorreo').off('click').on('click', function() {
                $('#correoId').val($(this).data('id'));
                $('#nuevoCorreo').val("");
                new bootstrap.Modal(document.getElementById('modalCorreo')).show();
            });

            $('.usu_mod-btnPass').off('click').on('click', function() {
                $('#passId').val($(this).data('id'));
                $('#nuevoPass').val("");
                $('#confirmPass').val("");
                new bootstrap.Modal(document.getElementById('modalPassword')).show();
            });
        }

        $('#formAgregarOrg').on('submit', function(e) {
            e.preventDefault();

            fetch(`${urlAgregarOrgBase}/${$('#agregarId').val()}`, {
                    method: "POST",
                    body: new URLSearchParams({
                        [csrfName]: csrfToken,
                        organizacion_id: $('#selectOrg').val(),
                        id_rol: $('#selectRol').val()
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: d.error,
                            confirmButtonColor: '#00A86B'
                        });
                        return;
                    }

                    cerrarModal('modalAgregarOrg');

                    Swal.fire({
                        icon: 'success',
                        title: 'Usuario actualizado',
                        text: 'La organización fue asignada correctamente.',
                        confirmButtonColor: '#00A86B'
                    });

                    cargarUsuarios();
                })
                .catch(err => console.error(err));
        });

        $('#formCorreo').on('submit', function(e) {
            e.preventDefault();

            fetch(`${urlCambiarCorreoBase}/${$('#correoId').val()}`, {
                    method: "POST",
                    body: new URLSearchParams({
                        [csrfName]: csrfToken,
                        email: $('#nuevoCorreo').val()
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: d.error,
                            confirmButtonColor: '#00A86B'
                        });
                        return;
                    }

                    cerrarModal('modalCorreo');

                    Swal.fire({
                        icon: 'success',
                        title: 'Correo actualizado',
                        text: 'El correo se cambió correctamente.',
                        confirmButtonColor: '#00A86B'
                    });

                    cargarUsuarios();
                })
                .catch(err => console.error(err));
        });

        $('#formPassword').on('submit', function(e) {
            e.preventDefault();

            if ($('#nuevoPass').val() !== $('#confirmPass').val()) {
                Swal.fire({
                    icon: 'warning',
                    title: '¡Las contraseñas no coinciden!',
                    text: 'Por favor revisar.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#00A86B'
                });
                return;
            }

            fetch(`${urlCambiarPassBase}/${$('#passId').val()}`, {
                    method: "POST",
                    body: new URLSearchParams({
                        [csrfName]: csrfToken,
                        password: $('#nuevoPass').val()
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: d.error,
                            confirmButtonColor: '#00A86B'
                        });
                        return;
                    }

                    cerrarModal('modalPassword');

                    Swal.fire({
                        icon: 'success',
                        title: '¡Contraseña actualizada!',
                        text: 'La contraseña se cambió correctamente.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#00A86B'
                    });
                })
                .catch(err => console.error(err));
        });

        $('#btnConfirmBloqueo').on('click', function() {
            fetch(`${urlBloquearBase}/${$('#bloqueoId').val()}`, {
                    method: "POST",
                    body: new URLSearchParams({
                        [csrfName]: csrfToken
                    })
                })
                .then(r => r.json())
                .then(d => {
                    if (d.error) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Atención',
                            text: d.error,
                            confirmButtonColor: '#00A86B'
                        });
                        return;
                    }

                    cerrarModal('modalBloqueo');

                    Swal.fire({
                        icon: 'success',
                        title: 'Estado actualizado',
                        text: 'El estado del usuario fue modificado correctamente.',
                        confirmButtonColor: '#00A86B'
                    });

                    cargarUsuarios();
                })
                .catch(err => console.error(err));
        });

        cargarUsuarios();
    });
</script>

<?= $this->endSection() ?>
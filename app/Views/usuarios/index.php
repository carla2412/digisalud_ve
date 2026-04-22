<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<main class="container-fluid py-4 diestra">

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-primary">Gestión de Usuarios</h2>
            <p class="text-muted mb-0">Administración general de usuarios Digisalud</p>
        </div>

        <div class="d-flex align-items-center gap-3 flex-wrap">
            <span class="text-muted small">
                Mostrando <span id="totalUsuarios">0</span> usuarios
            </span>

            <div class="input-group shadow-sm buscador-usuarios">
                <input type="text" class="form-control border-end-0" placeholder="Buscar usuario..." id="searchUser">
                <span class="input-group-text bg-white border-start-0">
                    <i class="fas fa-search text-muted"></i>
                </span>
            </div>
        </div>
    </div>

    <!-- contenedor de tarjetas -->
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 g-4" id="contenedorUsuarios">
        <!-- Se llena por JS -->
    </div>

    <!-- mensaje sin resultados -->
    <div id="sinResultados" class="text-center py-5 d-none">
        <div class="card border-0 shadow-sm p-4">
            <i class="fas fa-users-slash fs-1 text-muted mb-3"></i>
            <h5 class="mb-1">No se encontraron usuarios</h5>
            <p class="text-muted mb-0">Intenta con otro criterio de búsqueda.</p>
        </div>
    </div>

</main>

<?= $this->include('usuarios/modals/agregarOrg') ?>
<?= $this->include('usuarios/modals/cambiarCorreo') ?>
<?= $this->include('usuarios/modals/cambiarPassword') ?>
<?= $this->include('usuarios/modals/confirmarBloqueo') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<style>
    :root {
        --ds-primary: #5c9cd8;
        --ds-bg-light: #f8fafc;
    }

    body {
        background-color: var(--ds-bg-light);
    }

    .buscador-usuarios {
        max-width: 320px;
    }

     

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    }

    .avatar-circle {
        width: 48px;
        height: 48px;
        background-color: #e2e8f0;
        color: #475569;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 0.9rem;
        border: 2px solid #fff;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        text-transform: uppercase;
    }

    .bg-light-success {
        background-color: #ecfdf5 !important;
     }

    .bg-light-danger {
        background-color: #fef2f2 !important;
    }

    .input-group .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }

    .dropdown-menu {
        border-radius: 12px;
    }

    .dropdown-item i {
        width: 18px;
    }

    .card-text-mail {
        word-break: break-word;
    }
 
.user-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    border-radius: 16px;
    background: #fff;
    position: relative;
    overflow: visible !important;
    z-index: 1;
    min-height: unset !important;
}

.user-card .card-body {
    padding: 0.9rem 1rem !important;
}

.user-card .card-title {
    font-size: 1rem;
    line-height: 1.2;
    margin-bottom: 0.15rem !important;
}

.user-card small,
.user-card .card-text,
.user-card .badge {
    font-size: 0.82rem;
}

.user-card .mb-3 {
    margin-bottom: 0.75rem !important;
}

.user-card .mb-4 {
    margin-bottom: 0.9rem !important;
}

.user-card .pt-3 {
    padding-top: 0.75rem !important;
}

.user-card .border-top {
    margin-top: 0.5rem !important;
}

.user-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important;
    z-index: 5;
}
 .diestra {
    position: relative;
 
    width: 80%;
    
}


#contenedorUsuarios .col {
    position: relative;
  
}

.avatar-circle {
    width: 40px;
    height: 40px;
    background-color: #e2e8f0;
    color: #475569;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 0.8rem;
    border: 2px solid #fff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    text-transform: uppercase;
}

.dropdown {
    position: relative;
}

.dropdown-menu {
    z-index: 9999 !important;
    position: absolute;
    border-radius: 12px;
}
</style>

<script>
$(document).ready(function () {

    // ==============================
    //   VARIABLES DE SESIÓN + CSRF
    // ==============================
    const orgSesion = <?= json_encode($orgSesion) ?>;
    const rolSesion = parseInt(<?= json_encode($rolSesion) ?>, 10) || 0;

    const csrfName  = '<?= csrf_token() ?>';
    let csrfToken   = '<?= csrf_hash() ?>';

    const urlListado           = "<?= base_url('usuarios/listado') ?>";
    const urlAgregarOrgBase    = "<?= base_url('usuarios/agregar-organizacion') ?>";
    const urlCambiarCorreoBase = "<?= base_url('usuarios/cambiar-correo') ?>";
    const urlCambiarPassBase   = "<?= base_url('usuarios/cambiar-password') ?>";
    const urlBloquearBase      = "<?= base_url('usuarios/bloquear') ?>";

    let usuariosData = [];

    // ==============================
    //   CARGAR USUARIOS
    // ==============================
    function cargarUsuarios() {
        $.ajax({
            url: urlListado,
            type: "GET",
            dataType: "json",
            success: function(response) {
                usuariosData = response.data || [];
                $('#totalUsuarios').text(usuariosData.length);
                renderUsuarios(usuariosData);
            },
            error: function(xhr, status, error) {
                console.error("Error cargando usuarios:", error);

                $('#contenedorUsuarios').html(`
                    <div class="col-12">
                        <div class="alert alert-danger shadow-sm">
                            Ocurrió un error al cargar los usuarios.
                        </div>
                    </div>
                `);
            }
        });
    }

    // ==============================
    //   RENDER TARJETAS
    // ==============================
    function renderUsuarios(data) {
        const contenedor = $('#contenedorUsuarios');
        const sinResultados = $('#sinResultados');

        contenedor.html('');

        if (!data.length) {
            sinResultados.removeClass('d-none');
            return;
        }

        sinResultados.addClass('d-none');

        data.forEach(row => {
            const nombre = row.nombres ?? '';
            const apellido = row.apellidos ?? '';
            const correo = row.email ?? '';
            const organizacion = row.nombre_organizacion ?? 'Independiente';
            const idUsuario = row.id_usuario ?? '';
            const rolNombreOriginal = row.nombre_rol ?? '';
            const organizacionId = parseInt(row.organizacion_id ?? 0, 10);

            const inicialNombre = nombre ? nombre.charAt(0) : '';
            const inicialApellido = apellido ? apellido.charAt(0) : '';
            const iniciales = `${inicialNombre}${inicialApellido}`.toUpperCase();

            // Mostrar nombre amigable del rol
            let nombreMostrarRol = rolNombreOriginal;
            if (!rolNombreOriginal) {
                nombreMostrarRol = 'Sin rol';
            } else if (rolNombreOriginal === 'ADMIN_DIGI') {
                nombreMostrarRol = 'Digisalud';
            } else if (rolNombreOriginal === 'ADMIN_ORG') {
                nombreMostrarRol = 'Administrador';
            } else if (rolNombreOriginal === 'REGISTRO') {
                nombreMostrarRol = 'Registro de Data';
            } else if (rolNombreOriginal === 'ADMINISTRADOR') {
                nombreMostrarRol = 'TI Digisalud';
            } else if (rolNombreOriginal === 'COORDINADOR') {
                nombreMostrarRol = 'Coordinador';
            } else if (rolNombreOriginal === 'VIEWER') {
                nombreMostrarRol = 'Ver Data';
            }

            const badgeRol = rolNombreOriginal
                ? `
                    <span class="badge rounded-pill bg-light-success text-success px-3 py-2 border border-success border-opacity-25">
                        <i class="fas fa-user-shield me-1 small"></i> ${nombreMostrarRol}
                    </span>
                  `
                : `
                    <span class="badge rounded-pill bg-light-danger text-danger px-3 py-2 border border-danger border-opacity-25">
                        <i class="fas fa-exclamation-triangle me-1 small"></i> Sin rol
                    </span>
                  `;

            // permisos
            const esIndependiente = organizacionId === 1;
            const puedeAgregarOrg = esIndependiente && [1,2,3].includes(rolSesion);
            const puedeBloquear   = [1,2,3].includes(rolSesion);

            let acciones = '';

            if (puedeAgregarOrg) {
                acciones += `
                    <li>
                        <a class="dropdown-item btnAgregarOrg" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-person-fill-add me-2"></i> Agregar organización
                        </a>
                    </li>
                `;
            }

            if (puedeBloquear && !puedeAgregarOrg) {
                acciones += `
                    <li>
                        <a class="dropdown-item btnCorreo" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-envelope-fill me-2"></i> Cambiar correo
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item btnPass" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-key-fill me-2"></i> Cambiar contraseña
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger btnBloquear" href="javascript:void(0)" data-id="${idUsuario}">
                            <i class="bi bi-lock-fill me-2"></i> Bloquear / desbloquear
                        </a>
                    </li>
                `;
            }

            if (acciones === '') {
                acciones = `
                    <li>
                        <span class="dropdown-item-text text-muted small">Sin acciones disponibles</span>
                    </li>
                `;
            }

            const card = `
                <div class="col user-item"
                     data-search="${(nombre + ' ' + apellido + ' ' + correo + ' ' + organizacion + ' ' + nombreMostrarRol).toLowerCase()}">
                    <div class="card border-0 shadow-sm user-card">
                        <div class="card-body">
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-circle me-3">
                                    <span>${iniciales}</span>
                                </div>
                                <div>
                                    <h5 class="card-title mb-0 fw-bold text-dark">
                                        ${nombre} ${apellido}
                                    </h5>
                                    <small class="text-muted">${organizacion}</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                ${badgeRol}
                            </div>

                            <p class="card-text small text-muted mb-4 card-text-mail">
                                <i class="far fa-envelope me-1"></i> ${correo}
                            </p>

                            <div class="d-flex justify-content-between align-items-center border-top pt-2">
                              
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle px-3 rounded-pill"
                                            type="button"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        Acciones <i class="fas fa-wrench ms-1 small"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        ${acciones}
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            contenedor.append(card);
        });

        activarEventos();
    }

    // ==============================
    //   BUSCADOR LOCAL
    // ==============================
    $('#searchUser').on('keyup', function() {
        const texto = $(this).val().toLowerCase().trim();
        let visibles = 0;

        $('.user-item').each(function() {
            const contenido = $(this).data('search');

            if (contenido.includes(texto)) {
                $(this).removeClass('d-none');
                visibles++;
            } else {
                $(this).addClass('d-none');
            }
        });

        if (visibles === 0) {
            $('#sinResultados').removeClass('d-none');
        } else {
            $('#sinResultados').addClass('d-none');
        }
    });

    // Helper para cerrar modales por id
    function cerrarModal(idModal) {
        const el = document.getElementById(idModal);
        const modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
    }

    // ==============================
    //   ACTIVAR EVENTOS
    // ==============================
    function activarEventos() {

        $('.btnAgregarOrg').off('click').on('click', function () {
            const id = $(this).data('id');
            $('#agregarId').val(id);
            const modal = new bootstrap.Modal(document.getElementById('modalAgregarOrg'));
            modal.show();
        });

        $('.btnBloquear').off('click').on('click', function () {
            const id = $(this).data('id');
            $('#bloqueoId').val(id);
            $('#textoBloqueo').html("¿Deseas <b>cambiar el estado</b> de este usuario?");
            const modal = new bootstrap.Modal(document.getElementById('modalBloqueo'));
            modal.show();
        });

        $('.btnCorreo').off('click').on('click', function () {
            const id = $(this).data('id');
            $('#correoId').val(id);
            $('#nuevoCorreo').val("");
            const modal = new bootstrap.Modal(document.getElementById('modalCorreo'));
            modal.show();
        });

        $('.btnPass').off('click').on('click', function () {
            const id = $(this).data('id');
            $('#passId').val(id);
            $('#nuevoPass').val("");
            $('#confirmPass').val("");
            const modal = new bootstrap.Modal(document.getElementById('modalPassword'));
            modal.show();
        });
    }

    // =======================================================
    //   FORMULARIO: AGREGAR ORGANIZACIÓN
    // =======================================================
    $('#formAgregarOrg').on('submit', function(e) {
        e.preventDefault();

        fetch(`${urlAgregarOrgBase}/${$('#agregarId').val()}`, {
            method: "POST",
            body: new URLSearchParams({
                [csrfName]: csrfToken,
                organizacion_id: $('#selectOrg').val(),
                rol_id: $('#selectRol').val()
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

    // =======================================================
    //   FORMULARIO: CAMBIAR CORREO
    // =======================================================
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

    // =======================================================
    //   FORMULARIO: CAMBIAR CONTRASEÑA
    // =======================================================
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

    // =======================================================
    //   FORMULARIO: CONFIRMAR BLOQUEO
    // =======================================================
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

    // cargar al iniciar
    cargarUsuarios();

});
</script>

<?= $this->endSection() ?>
<?= $this->extend('layouts/main') ?>

<?= $this->section('content') ?>

<main class="container my-5">

  <h2 class="mb-4 fw-bold fondo_principal">Gestión de Usuarios</h2>

  <!-- BUSCADOR PERSONALIZADO (opcional, lo conectamos a DataTables) -->
  <!-- <div class="row mb-4">
    <div class="col-md-4">
      <label class="form-label">Buscar usuario</label>
      <input type="text" id="buscarUsuario" class="form-control" placeholder="Buscar nombre, correo u organización">
    </div>
  </div> -->

  <div class="table-responsive shadow-sm border rounded">
    <table class="table table-hover align-middle mb-0" id="tablaUsuarios">
      <thead >
        <tr >
          <th>Nombre</th>
          <th>Apellido</th>
          <th>Correo</th>
          <th>Organización</th>
          <th>Rol</th>
          <th class="text-center">Acción</th>
        </tr>
      </thead>
      <tbody>
        <!-- SIN FOREACH: DataTables lo llenará por AJAX -->
      </tbody>
    </table>
  </div>
</main>


<?= $this->include('usuarios/modals/agregarOrg') ?>
<?= $this->include('usuarios/modals/agregarOrg') ?>
<?= $this->include('usuarios/modals/cambiarCorreo') ?>
<?= $this->include('usuarios/modals/cambiarPassword') ?>
<?= $this->include('usuarios/modals/confirmarBloqueo') ?>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>

<script>
$(document).ready(function() {

    // ==============================
    //   VARIABLES DE SESIÓN + CSRF
    // ==============================
    const orgSesion = <?= json_encode($orgSesion) ?>;
 

    const csrfName  = '<?= csrf_token() ?>';
    const csrfToken = '<?= csrf_hash() ?>';

    const urlListado          = "<?= base_url('usuarios/listado') ?>";
    const urlAgregarOrgBase   = "<?= base_url('usuarios/agregar-organizacion') ?>";
    const urlCambiarCorreoBase= "<?= base_url('usuarios/cambiar-correo') ?>";
    const urlCambiarPassBase  = "<?= base_url('usuarios/cambiar-password') ?>";
    const urlBloquearBase     = "<?= base_url('usuarios/bloquear') ?>";
 
    // lo convierto a entero, si falla queda 0
    const rolSesion = parseInt(<?= json_encode($rolSesion) ?>, 10) || 0;

    // ==============================
    //   INICIALIZAR DATATABLE
    // ==============================
window.tabla = $('#tablaUsuarios').DataTable({

        processing: true,
        serverSide: false,
        responsive: true,
        autoWidth: false,
        ajax: {
            url: urlListado,
            type: "GET",
            dataSrc: "data"
        },
        columns: [
            { data: 'nombres' },
            { data: 'apellidos' },
            { data: 'email' },
            
            { 
                data: 'nombre_organizacion',
                render: d => d ?? 'Independiente'
            },
            //aqui va el rol
{ 
    data: 'nombre_rol',
    defaultContent: '', 
    render: function(data, type, row) {
        // Si no hay datos, mostramos "Sin rol"
        if (!data) {
            return '<span class="badge bg-danger text-dark border warning">Sin rol</span>';
        }

        // Lógica de reemplazo de nombres
        let nombreMostrar = data; // Valor por defecto por si viene otro rol

        if (data === 'ADMIN_DIGI') {
            nombreMostrar = 'Digisalud';
        } else if (data === 'ADMIN_ORG') {
            nombreMostrar = 'Administrador';
        }  else if (data === 'REGISTRO') {
            nombreMostrar = 'Registro de Data';
        } else if (data === 'ADMINISTRADOR') {
            nombreMostrar = 'TI Digisalud';
        } else if (data === 'COORDINADOR') {
            nombreMostrar = 'Coordinador';
        } else if (data === 'VIEWER') {
            nombreMostrar = 'Ver Data';
        }

        // Retornamos el diseño con el nombre cambiado
        return `<span class="badge bg-light text-dark border">${nombreMostrar}</span>`;
    }
},
                
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {

                    const esIndependiente = row.organizacion_id == 1;
                    const puedeAgregarOrg = esIndependiente && [1,2,3].includes(rolSesion);
                    // const puedeBloquear   = rolSesion == 1;
                    const puedeBloquear   =  [1,2,3].includes(rolSesion);
                    const puedeEditar     = [1,2].includes(rolSesion);

                    let html = "";

                    // ---------- AGREGAR A ORGANIZACIÓN ----------
                    if (puedeAgregarOrg) {
                        html += `
                            <button class="btn btn-sm btnAgregarOrg  "
                                data-id="${row.id_usuario}"
                                title="Agregar a organización">
                                <i class="bi bi-person-fill-add"></i>
                            </button>
                        `;
                    } else {
                        html += ``;
                    }

                    // ---------- BLOQUEAR ----------
                    if (puedeBloquear && !puedeAgregarOrg  ) {
                        html += `
                            <button class="btn btn-sm btnBloquear  "
                                data-id="${row.id_usuario}"
                                title="Bloquear / desbloquear">
                                <i class="bi bi-lock-fill"></i>
                            </button>
                        `;
                    } else {
                        html += `
                            
                        `;
                    }

                    // ---------- CAMBIAR CORREO ----------
                   
                    if (puedeBloquear && !puedeAgregarOrg) {
                        html += `
                            <button class="btn btn-sm btnCorreo"
                                data-id="${row.id_usuario}"
                                title="Cambiar correo">
                                <i class="bi bi-envelope-fill"></i>
                            </button>
                        `;
                    } else {
                        html += `
                            
                        `;
                    }


                    // ---------- CAMBIAR CONTRASEÑA ----------
                    if (puedeBloquear && !puedeAgregarOrg) {
                        html += `
                            <button class="btn btn-sm btnPass"
                                data-id="${row.id_usuario}"
                                title="Cambiar contraseña">
                                <i class="bi bi-key-fill"></i>
                            </button>
                        `;
                    } else {
                        html += `
                            
                        `;
                    }


                    return html;
                }
            }
        ],
        language: {
            url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        }
    });


    // =======================================================
    //    EVENTOS DE BOTONES (SE REACTIVAN CON draw.dt)
    // =======================================================
    $('#tablaUsuarios').on('draw.dt', function () {

        // ==========================
        //   AGREGAR A ORGANIZACIÓN
        // ==========================
        $('.btnAgregarOrg').off().on('click', function () {
            const id = $(this).data('id');
            $('#agregarId').val(id);
            const modal = new bootstrap.Modal(document.getElementById('modalAgregarOrg'));
            modal.show();
        });

        // ==========================
        //       BLOQUEAR
        // ==========================
        $('.btnBloquear').off().on('click', function () {
            const id = $(this).data('id');

            $('#bloqueoId').val(id);
            $('#textoBloqueo').html("¿Deseas <b>cambiar el estado</b> de este usuario?");
            const modal = new bootstrap.Modal(document.getElementById('modalBloqueo'));
            modal.show();
        });

        // ==========================
        //     CAMBIAR CORREO
        // ==========================
        $('.btnCorreo').off().on('click', function () {
            const id = $(this).data('id');

            $('#correoId').val(id);
            $('#nuevoCorreo').val("");

            const modal = new bootstrap.Modal(document.getElementById('modalCorreo'));
            modal.show();
        });

        // ==========================
        //   CAMBIAR CONTRASEÑA
        // ==========================
        $('.btnPass').off().on('click', function () {
            const id = $(this).data('id');

            $('#passId').val(id);
            $('#nuevoPass').val("");
            $('#confirmPass').val("");

            const modal = new bootstrap.Modal(document.getElementById('modalPassword'));
            modal.show();
        });

    });


    // Helper para cerrar modales por id
    function cerrarModal(idModal) {
        const el = document.getElementById(idModal);
        const modal = bootstrap.Modal.getInstance(el);
        if (modal) modal.hide();
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
                alert(d.error);
                return;
            }

            cerrarModal('modalAgregarOrg');
            tabla.ajax.reload(null, false);
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
                alert(d.error);
                return;
            }

            cerrarModal('modalCorreo');
            tabla.ajax.reload(null, false);
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
            title: '¡Las Contraseñas no coinciden!',
            text: 'Por favor revisar',
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
                alert(d.error);
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
                alert(d.error);
                return;
            }

            cerrarModal('modalBloqueo');
            tabla.ajax.reload(null, false);
        })
        .catch(err => console.error(err));
    });

});
</script>

<?= $this->endSection() ?>
 
 
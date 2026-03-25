  <?= view('templates/head', ['title' => 'Crear Organización | Digisalud']) ?>

 
  <div class="container mt-5 mb-5">
    <div class="row justify-content-center">
      <div class="col-md-10 col-lg-8">
        <div class="card p-4">
          <h4 class="text-center mb-4">Cuenta Institucional: Crear Organización</h4>

          <form id="formOrganizacion" method="post" action="<?= site_url('registro/organizacion') ?>" novalidate>
            <?= csrf_field() ?>
            <!-- ================= DATOS DE LA ORGANIZACIÓN ================= -->
            <div class="section-title mb-3">Datos de la Organización</div>
 
            <!-- Nombre -->
            <div class="mb-3">
              <label for="nombreOrg" class="form-label">Nombre de la Organización</label>
              <input type="text" class="form-control" id="nombreOrg" name="nombreOrg" required>
              <div class="invalid-feedback">Por favor ingresa el nombre de la organización.</div>
            </div>
               <!-- Username / email -->
            <div class="mb-3">
              <label for="email" class="form-label">Correo Institucional</label>
              <input type="email" class="form-control" id="email" name="email" placeholder="usuario@organizacion.org" required>
              <div class="invalid-feedback">Ingresa un email válido.</div>
            </div>
             <div class="row">
              <!-- Tipo -->
              <div class="col-12 col-md-6 mb-3">
                <label for="tipoOrg" class="form-label">Tipo</label>
                <select class="form-select" id="tipoOrg" name="tipoOrg" required>
                  <option value="">Selecciona un tipo...</option>
                  <option>Escolar</option>
                  <option>Comedor</option>
                  <option>Empresa Privada</option>
                  <option>Casa hogar</option>
                  <option>ONG</option>
                  <option>Alcaldía</option>
                  <option>Gobernación</option>
                  <option>Mixto</option>
                  <option> 	Organismo Público	</option>
                </select>
                <div class="invalid-feedback">Selecciona el tipo de organización.</div>
              </div>

              <!-- Categoría -->
              <div class="col-12 col-md-6 mb-3">
                <label for="categoriaOrg" class="form-label">Categoría</label>
                <select class="form-select" id="categoriaOrg" name="categoriaOrg" required>
                  <option value="">Selecciona una categoría...</option>
                  <option>Alimentación</option>
                  <option>Programa Nutricional</option>
                  <option>Atención Médica</option>
                  <option>Voluntariado</option>
                  <option>Donante</option>
                </select>
                <div class="invalid-feedback">Selecciona una categoría.</div>
              </div>
            </div>

            <div class="row">
              <!-- País (fijo) -->
              <div class="col-12 col-md-6 mb-3">
                <label for="pais" class="form-label">País</label>
                <input type="text" class="form-control" id="pais" name="pais" value="Venezuela" readonly>
              </div>

              <!-- Estado -->
              <div class="col-12 col-md-6 mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select id="estado" name="estado" class="form-select" required style="border:2px solid red;">
                  <option value="">Selecciona un estado...</option>
                </select>
              </div>
            </div>

              
          <div class="row">
            <!-- MUNICIPIO -->
              <div class="col-12 col-md-6 mb-3">
              <label for="municipio" class="form-label">Municipio</label>
              <select id="municipio" name="municipio" class="form-select" required>
                <option value="">Selecciona una municipio...</option>
              </select>
              </div>

              <!-- parroquia -->
              <div class="col-12 col-md-6 mb-3">
              <label for="parroquia" class="form-label">Parroquia</label>
              <select id="parroquia" name="parroquia" class="form-select" required>
                <option value="">Selecciona una Parroquia...</option>
              </select>
            </div>
          </div>
          <div class="row">
            <div class="col-12 col-md-6 mb-3">
              <label for="ciudad" class="form-label">Ciudad o Localidad</label>
              <input type="text" class="form-control" id="ciudad" name="ciudad">
            </div>  

            <div class="col-12 col-md-6 mb-3">
              <label for="detalle" class="form-label">Detalle (opcional)</label>
              <input type="text" class="form-control" id="detalle" name="detalle" placeholder="Calle, edificio, referencia...">
            </div>
          </div>
             
            <!-- ================= PERSONA DE CONTACTO ================= -->
            <div class="section-title mb-3">Persona de Contacto</div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nombres" class="form-label">Nombres</label>
                <input type="text" class="form-control" id="nombres" name="nombres" required>
                <div class="invalid-feedback">Ingresa los nombres.</div>
              </div>

              <div class="col-md-6 mb-3">
                <label for="apellidos" class="form-label">Apellidos</label>
                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                <div class="invalid-feedback">Ingresa los apellidos.</div>
              </div>
            </div>

            <!-- Género -->
            <div class="mb-3">
              <label class="form-label">Género</label>
              <div class="d-flex gap-3">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="genero" id="generoF" value="F" required>
                  <label class="form-check-label" for="generoF">Femenino</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="genero" id="generoM" value="M" required>
                  <label class="form-check-label" for="generoM">Masculino</label>
                </div>
                
                 
              </div>
               
            </div>

            <!-- Teléfono -->
            <div class="mb-3">
              <label for="telefono" class="form-label">Número de Teléfono</label>
              <input type="tel" class="form-control" id="telefono"  name="telefono"  pattern="[0-9+ ]{6,15}" placeholder="+58 412 1234567" required>
              <div class="invalid-feedback">Ingresa un número válido.</div>
            </div>

            
                        <!-- Profesión -->
          <div class="mb-3">
          <label for="profesion" class="form-label">Profesión</label>
          <select id="profesion" name="profesion" class="form-select" required></select>
          <div class="invalid-feedback">Ingresa una profesión válida.</div>
        </div>

            
           
            <!-- Contraseña -->
            <div class="mb-3">
              <label for="contrasena" class="form-label">Contraseña</label>
              <input type="password" class="form-control" id="contrasena" name="contrasena" minlength="6" required>
              <div class="invalid-feedback">La contraseña debe tener al menos 6 caracteres.</div>
            </div>

            <!-- Confirmar contraseña -->
            <div class="mb-4">
              <label for="confirmarContrasena" class="form-label">Confirmar Contraseña</label>
              <input type="password" class="form-control" id="confirmarContrasena" name="confirmarContrasena" required>
              <div class="invalid-feedback">Las contraseñas no coinciden.</div>
            </div>

            <!-- Botones -->
            <div class="text-center">
              <button type="submit" class="btn btn-primary px-4">Registrar</button>
              <button type="reset" class="btn btn-secondary px-4 ms-2">Limpiar</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
 <footer>
    © Digisalud 2025. Derechos reservados. V2.0.1
  </footer>
  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?= base_url('js/venezuela.js') ?>"></script>
<script>
  document.getElementById("telefono").addEventListener("input", function(e) {
    let value = e.target.value.replace(/\D/g, ""); // solo números

    // Si comienza con 58, lo quitamos para evitar duplicación
    if (value.startsWith("58")) {
        value = value.substring(2);
    }

    let formatted = "+58 ";

    if (value.length > 0) {
        formatted += value.substring(0, 3);
    }
    if (value.length > 3) {
        formatted += " " + value.substring(3, 10);
    }

    e.target.value = formatted;
});

</script>
  <script>
$(document).ready(function() {

  const $estado = $('#estado');
  const $municipio = $('#municipio');
  const $parroquia = $('#parroquia');
     const $profesion = $('#profesion');
  // ====== PROFESIONES ======
  if (Array.isArray(profesiones)) {
    profesiones.forEach(function(p) {
      $profesion.append(new Option(p, p));
    });
  }
  // Cargar estados
  Object.keys(ubicaciones).forEach(estado => {
    $estado.append(new Option(estado, estado));
  });

  // Select2
  $estado.select2({ placeholder: 'Selecciona un estado' });
  $municipio.select2({ placeholder: 'Selecciona un municipio' });
  $parroquia.select2({ placeholder: 'Selecciona una parroquia' });

  // Al cambiar el estado → cargar municipios
  $estado.on('change', function() {
    const estadoSeleccionado = this.value;

    const municipios = Object.keys(ubicaciones[estadoSeleccionado] || {});

    // Limpiar dependientes
    $municipio.empty().append(new Option('', ''));
    $parroquia.empty().append(new Option('', ''));

    municipios.forEach(mun => {
      $municipio.append(new Option(mun, mun));
    });

    $municipio.trigger('change.select2');
    $parroquia.trigger('change.select2');
  });

  // Al cambiar el municipio → cargar parroquias
  $municipio.on('change', function() {
    const estadoSeleccionado = $estado.val();
    const municipioSeleccionado = this.value;

    const parroquias = ubicaciones[estadoSeleccionado]?.[municipioSeleccionado] || [];

    $parroquia.empty().append(new Option('', ''));

    parroquias.forEach(pq => {
      $parroquia.append(new Option(pq, pq));
    });

    $parroquia.trigger('change.select2');
  });

});
</script>
  <!-- Validación -->
 <script>
  const form = document.getElementById('formOrganizacion');

  form.addEventListener('submit', (e) => {
    // Validar contraseñas
    const pass = document.getElementById('contrasena');
    const confirm = document.getElementById('confirmarContrasena');

    if (pass.value !== confirm.value) {
      confirm.setCustomValidity("Las contraseñas no coinciden");
    } else {
      confirm.setCustomValidity("");
    }

    if (!form.checkValidity()) {
      e.preventDefault();
      e.stopPropagation();
    }

    form.classList.add('was-validated');
  });
</script>

 <script>
$(document).ready(function () {

    const lista = profesiones; // del JSON profesiones.js

    $("#profesion").select2({
        placeholder: "Escribe tu profesión...",
        allowClear: true,
        data: [], // empieza vacío
        minimumInputLength: 1, // 👈 SOLO aparecerán resultados cuando escriba
        width: "100%",
        dropdownAutoWidth: true,
        matcher: function(params, data) {
            // Sin texto → nada
            if ($.trim(params.term) === '') {
                return null;
            }

            // Filtrar por coincidencia
            if (data.text.toLowerCase().indexOf(params.term.toLowerCase()) > -1) {
                return data;
            }

            return null;
        }
    });

    // Evento: cada vez que escriba, filtrar profesiones
    $('#profesion').on('select2:open', function () {
        let input = $('.select2-search__field');

        input.off('keyup').on('keyup', function () {
            let term = $(this).val().toLowerCase();

            let filtradas = lista
                .filter(p => p.toLowerCase().includes(term))
                .slice(0, 5); // 👈 SOLO 5 RESULTADOS

            let opciones = filtradas.map(p => ({ id: p, text: p }));

            $("#profesion").empty().select2({
                data: opciones,
                placeholder: "Escribe tu profesión...",
                allowClear: true,
                width: "100%"
            });

            // Reescribir lo que el usuario digitó
            setTimeout(() => {
                $('.select2-search__field').val(term).trigger('keyup');
            }, 10);
        });
    });

});

</script>
<script src="<?= base_url('js/profesiones.js') ?>"></script>
 
<?php
  // Leer una sola vez el flash y guardarlo en variable
  $flashSuccess = session()->getFlashdata('success');
?>
<?php if (!empty($flashSuccess)): ?>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    // Usar DOMContentLoaded para no depender del cache del navegador
    window.addEventListener('DOMContentLoaded', function () {
      Swal.fire({
        title: "✅ Registro exitoso",
        text: <?= json_encode($flashSuccess) ?>, // más seguro que imprimir PHP en crudo
        icon: "success",
        confirmButtonText: "Continuar"
      }).then(() => {
        // replace() evita volver con el botón Atrás al formulario con el popup
        window.location.replace("<?= site_url('login') ?>");
      });
    });
  </script>
<?php endif; ?>


</body>
</html>

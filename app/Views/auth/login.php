<?= view('templates/head', ['title' => 'Login | Digisalud']) ?>


  <main class="login-container">
    <div class="login-card">
      <h4 class="text-center mb-4">Iniciar Sesión</h4>
<?php if(session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif; ?>

<form method="post" action="<?= site_url('auth/attempt') ?>">

    <?= csrf_field() ?>

    <div class="mb-3">
      <input type="text" class="form-control" name="identity" placeholder="Usuario o correo" required autocomplete="off">
    </div>

    <div class="mb-3">
      <input type="password" class="form-control"  name="password" placeholder="Contraseña" required autocomplete="off">
    </div>
    
    <div class="d-grid">
      <button type="submit" class="btn btn-primary">Entrar</button>
    </div>
</form>
<br>

<a href="#" class="small text-decoration-none" style="color:grey;">¿Olvidaste tu contraseña?</a>
      <!-- Nueva sección -->
      <div class="extra-links mt-4">
        <p class="text-muted mb-1">Crea una cuenta </p>
       
         <div class="d-flex justify-content-between flex-wrap px-2">
            <a class="morado" href="<?= site_url('registro/individual') ?>">Individual</a>
            <a class="morado" href="<?= site_url('registro/organizacion') ?>">Institucional</a>
          </div>

        <br><br>
        <div class="d-flex justify-content-between flex-wrap px-2 morado">
          <a class="morado"  href="#" target="_blank" style="color:grey;" >Políticas de privacidad</a>
          <a  class="morado"  href="#" target="_blank" style="color:grey;">Condiciones</a>

        </div>
      </div>
    </div>

    
  </main>

  <footer>
    © Digisalud 2026. Derechos reservados. V2.0.1
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
  <script  >
  function redirigirAIndex() {
  window.location.href = 'index.html';
}

</script>
</body>
</html>

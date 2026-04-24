<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
 
<?= $this->section('css') ?>
<style>
 
/*index organizacion*/
  * {
    box-sizing: border-box;
  }

  .org-page {
    background: #eef2f7;
    padding: 20px;
  }

  .org-container {
    max-width: 1600px;
    margin: 0 auto;
    background: #f9fbff;
    border-radius: 24px;
    padding: 28px 32px 34px;
    box-shadow: 0 8px 30px rgba(31, 42, 68, 0.08);
  }

  .org-breadcrumb {
    font-size: 14px;
    color: #6d7890;
    margin-bottom: 18px;
  }

  .org-breadcrumb a {
    color: #6d7890;
    text-decoration: none;
  }

  .org-breadcrumb span {
    color: #3695f5;
    font-weight: 600;
  }

  .topbar {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 20px;
    margin-bottom: 24px;
  }

  .title h1 {
    font-size: 56px;
    line-height: 1.1;
    margin-bottom: 8px;
    color: #101a61;
  }

  .title p {
    font-size: 18px;
    color: #6b7280;
    margin: 0;
  }

  .btn-primary-custom {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: #3695f5;
    color: #fff;
    border: none;
    border-radius: 14px;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 600;
    cursor: pointer;
    box-shadow: 0 8px 18px rgba(31, 111, 255, 0.25);
    transition: 0.2s ease;
    text-decoration: none;
  }

  .btn-primary-custom:hover {
    transform: translateY(-1px);
    background: #1b7ae2;
    color: #fff;
    text-decoration: none;
  }

  .filters {
    display: grid;
    grid-template-columns: 1fr;
    gap: 14px;
    margin-bottom: 36px;
  }

  .input-custom {
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

  .input-custom input {
    border: none;
    outline: none;
    width: 100%;
    font-size: 16px;
    margin-left: 12px;
    background: transparent;
    color: #334155;
  }

  .cards {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 28px;
    margin-bottom: 26px;
  }

  .org-card {
    position: relative;
    background: #fff;
    border-radius: 24px;
    padding: 26px;
    min-height: 320px;
    box-shadow: 0 10px 28px rgba(31, 42, 68, 0.08);
    overflow: hidden;
    border: 1px solid #eef2f8;
    transition: all 0.25s ease;
  }

  .org-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 34px rgba(31, 42, 68, 0.12);
  }

  .org-card.inactiva {
    opacity: 0.75;
    background: #f8fafc;
  }

  .menu {
    position: absolute;
    right: 20px;
    top: 18px;
    font-size: 24px;
    color: #667085;
    z-index: 2;
  }

  .avatar {
    width: 72px;
    height: 72px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    font-weight: 700;
    margin-bottom: 22px;
    overflow: hidden;
    position: relative;
    z-index: 2;
  }

  .avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
  }

  .blue { background: #dce8ff; color: #3695f5; }
  .green { background: #dff4e7; color: #22c55e; }
  .purple { background: #eedfff; color: #7c3aed; }

  .org-card h3 {
    font-size: 22px;
    margin-bottom: 10px;
    color: #14213d;
    position: relative;
    z-index: 2;
  }

  .tag {
    display: inline-block;
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 18px;
    position: relative;
    z-index: 2;
  }

  .tag.blue { background: #e7f0ff; color: #3695f5; }
  .tag.green { background: #e8f8ee; color: #16a34a; }
  .tag.purple { background: #f1e8ff; color: #7c3aed; }
  .tag.gray { background: #eef2f7; color: #64748b; }

  .email {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #5b6478;
    font-size: 16px;
    margin-bottom: 24px;
    position: relative;
    z-index: 2;
    word-break: break-word;
  }

  .divider {
    height: 1px;
    background: #e8edf5;
    margin-bottom: 22px;
    position: relative;
    z-index: 2;
  }

  .actions {
    display: flex;
    gap: 12px;
    position: relative;
    z-index: 2;
    flex-wrap: wrap;
  }

  .btn-action-custom {
    border-radius: 14px;
    padding: 12px 20px;
    font-size: 15px;
    font-weight: 700;
    border: 1px solid;
    background: #fff;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
  }

  .btn-action-custom:hover {
    transform: translateY(-1px);
    text-decoration: none;
  }

  .btn-edit {
    color: #ea8a00;
    border-color: #f2c27c;
  }

  .btn-edit:hover {
    color: #c96f00;
    background: #fff8ef;
  }

  .btn-block {
    color: #ef4444;
    border-color: #f3a7a7;
  }

  .btn-block:hover {
    color: #dc2626;
    background: #fff5f5;
  }

  .btn-activate {
    color: #16a34a;
    border-color: #86efac;
  }

  .btn-activate:hover {
    color: #15803d;
    background: #f0fdf4;
  }

  .bg-shape {
    position: absolute;
    right: -40px;
    bottom: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    opacity: 0.18;
    z-index: 0;
  }

  .shape-blue { background: #bcd3ff; }
  .shape-green { background: #b9ebc9; }
  .shape-purple { background: #dec5ff; }

  .shape-icon {
    position: absolute;
    right: 34px;
    bottom: 40px;
    font-size: 64px;
    opacity: 0.18;
    z-index: 1;
  }

  .meta-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #667085;
    margin-top: 10px;
    font-size: 16px;
    gap: 16px;
    flex-wrap: wrap;
  }

  .empty-state {
    grid-column: 1 / -1;
    background: #fff;
    border: 1px dashed #cbd5e1;
    border-radius: 20px;
    padding: 40px 20px;
    text-align: center;
    color: #64748b;
  }

  @media (max-width: 1200px) {
    .cards {
      grid-template-columns: 1fr 1fr;
    }

    .topbar {
      flex-direction: column;
      align-items: stretch;
    }

    .title h1 {
      font-size: 40px;
    }

    .btn-primary-custom {
      width: fit-content;
    }
  }

  @media (max-width: 768px) {
    .org-page {
      padding: 12px;
    }

    .org-container {
      padding: 20px;
      border-radius: 18px;
    }

    .cards {
      grid-template-columns: 1fr;
      gap: 20px;
    }

    .title h1 {
      font-size: 32px;
    }

    .actions {
      flex-direction: column;
    }

    .btn-action-custom {
      justify-content: center;
      width: 100%;
    }
  }

</style>
<?= $this->endSection() ?>
<div class="org-page">
  <div class="org-container">
    <div class="org-breadcrumb">
      <a href="<?= base_url('inicio') ?>">Inicio</a> &nbsp;›&nbsp; <span>Organizaciones</span>
    </div>

    <div class="topbar">
      <div class="title">
        <h1>Organizaciones</h1>
        <p>Gestiona y administra las organizaciones registradas. </p>
      </div>

      <a href="<?= base_url('organizaciones/crear') ?>" class="btn-primary-custom">
        <i class="fas fa-plus"></i>
        Nueva Organización
      </a>
    </div>

    <div class="filters">
      <div class="input-custom">
        <i class="fas fa-search"></i>
        <input
          type="text"
          id="searchOrg"
          placeholder="Buscar organización por nombre, tipo o categoría..."
        />
      </div>
    </div>

    <div class="cards" id="orgGrid">
      <?php if (!empty($organizaciones)): ?>
        <?php
          $colores = ['blue', 'green', 'purple'];
          $shapeColores = ['shape-blue', 'shape-green', 'shape-purple'];
        ?>

        <?php foreach ($organizaciones as $i => $org): ?>
          <?php
            $color = $colores[$i % count($colores)];
            $shapeColor = $shapeColores[$i % count($shapeColores)];
            $nombre = $org['nombre_org'] ?? 'Sin nombre';
            $tipo = !empty($org['tipo']) ? $org['tipo'] : 'ONG';
            $correo = !empty($org['correo']) ? $org['correo'] : '—';
            $logo = $org['logo_url'] ?? '';
            $inactiva = ((int)($org['status_org'] ?? 1) === 2);
          ?>
          <div class="org-item">
            <div class="org-card <?= $inactiva ? 'inactiva' : '' ?>">
              <div class="menu">
                <i class="fas fa-building"></i>
              </div>
 
              <div class="avatar <?= $color ?>">
                <?php if (!empty($logo)): ?>
                  <img src="<?= base_url('uploads/logos/' . $logo) ?>" alt="Logo de <?= esc($nombre) ?>">
                <?php else: ?>
                  <?= strtoupper(substr($nombre, 0, 1)) ?>
                <?php endif; ?>
              </div>

              <h3 class="org-name"><?= esc($nombre) ?></h3>

              <span class="tag <?= $color ?>">
                <?= esc($tipo) ?>
              </span>

              <div class="email">
                <i class="fas fa-envelope"></i>
                <span><?= esc($correo) ?></span>
              </div>

              <div class="divider"></div>

              <div class="actions">
                <a
                  href="<?= base_url('organizaciones/editar/' . $org['id_organizacion']) ?>"
                  class="btn-action-custom btn-edit"
                  title="Editar Organización"
                >
                  <i class="fas fa-pen"></i>
                  Editar
                </a>

                <?php if ((int)$org['status_org'] === 1): ?>
                  <button
                    type="button"
                    onclick="cambiarStatus(<?= $org['id_organizacion'] ?>, 2)"
                    class="btn-action-custom btn-block"
                    title="Bloquear"
                  >
                    <i class="fas fa-ban"></i>
                    Bloquear
                  </button>
                <?php else: ?>
                  <button
                    type="button"
                    onclick="cambiarStatus(<?= $org['id_organizacion'] ?>, 1)"
                    class="btn-action-custom btn-activate"
                    title="Activar"
                  >
                    <i class="fas fa-check"></i>
                    Activar
                  </button>
                <?php endif; ?>
              </div>

              <div class="bg-shape <?= $shapeColor ?>"></div>
              <div class="shape-icon"><i class="fas fa-building"></i></div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="empty-state">
          <i class="fas fa-building fa-2x mb-3"></i>
          <h4 class="mb-2">No hay organizaciones registradas</h4>
          <p class="mb-0">Cuando agregues organizaciones, aparecerán aquí.</p>
        </div>
      <?php endif; ?>
    </div>

    <div class="meta-row">
      <div id="orgCount">
        Mostrando <?= count($organizaciones) ?> organización<?= count($organizaciones) !== 1 ? 'es' : '' ?>
      </div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('searchOrg');
    const orgItems = document.querySelectorAll('#orgGrid .org-item');
    const orgCount = document.getElementById('orgCount');

    function actualizarConteo(visible) {
      orgCount.textContent = `Mostrando ${visible} organizacion${visible !== 1 ? 'es' : ''}`;
    }

    if (searchInput) {
      searchInput.addEventListener('keyup', function () {
        const term = this.value.toLowerCase().trim();
        let visibles = 0;

        orgItems.forEach(item => {
          const texto = item.textContent.toLowerCase();
          const mostrar = texto.includes(term);

          item.style.display = mostrar ? '' : 'none';

          if (mostrar) {
            visibles++;
          }
        });

        actualizarConteo(visibles);
      });
    }

    actualizarConteo(document.querySelectorAll('#orgGrid .org-item').length);
  });
</script>

<?= $this->endSection() ?>
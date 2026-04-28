<?= $this->extend('layouts/main') ?>
<?= $this->section('css') ?>
<style>
    body {
    background: #f4f8fc;
    font-family: "Roboto", sans-serif;
    color: #0b2551;
}

.ds-layout {
    display: flex;
    width: min(100%, 1500px);
    min-height: 100vh;
    margin: 0 auto;
    padding: 12px;
    gap: 12px;
}



.ds-logo {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.4rem;
    font-weight: 800;
    margin-bottom: 40px;
}

.ds-logo-icon {
    width: 34px;
    height: 34px;
    background: #126dff;
    color: white;
    border-radius: 50%;
    display: grid;
    place-items: center;
}

.ds-nav {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ds-nav a {
    text-decoration: none;
    color: #44546f;
    padding: 14px 18px;
    border-radius: 10px;
    font-weight: 600;
}

.ds-nav a.active,
.ds-nav a:hover {
    background: #eef5ff;
    color: #0066ff;
}

.ds-help {
    margin-top: auto;
    border: 1px solid #cfe0ff;
    border-radius: 14px;
    padding: 22px;
    text-align: center;
}

.ds-help p {
    color: #64748b;
    font-size: .9rem;
}

.ds-help button,
.ds-btn-outline {
    border: 1px solid #8bb7ff;
    background: #fff;
    color: #0066ff;
    border-radius: 10px;
    padding: 10px 18px;
    font-weight: 600;
}

.ds-user {
    margin-top: 26px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.ds-avatar {
    width: 42px;
    height: 42px;
    border-radius: 50%;
    background: #126dff;
    color: #fff;
    display: grid;
    place-items: center;
    font-weight: bold;
}

.ds-user small {
    display: block;
    color: #64748b;
}

.ds-main {
    flex: 1;
    background: #fff;
    border-radius: 16px;
    padding: 26px 34px;
    box-shadow: 0 8px 28px rgba(15, 23, 42, .06);
}

.ds-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.ds-header h1 {
    margin: 0;
    font-size: 2rem;
}

.ds-header p {
    color: #718096;
    margin-top: 6px;
}

.ds-btn-primary {
    background: linear-gradient(135deg, #1476ff, #0059e8);
    color: white;
    padding: 15px 28px;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 700;
    box-shadow: 0 10px 18px rgba(0, 102, 255, .25);
}

.ds-filters {
    display: grid;
    grid-template-columns: 1.3fr 1fr 1fr auto;
    gap: 26px;
    margin: 28px 0 22px;
}

.ds-search,
.ds-filters select {
    height: 56px;
    border: 1px solid #d9e2ef;
    border-radius: 12px;
    background: #fff;
}

.ds-search {
    display: flex;
    align-items: center;
    padding: 0 18px;
}

.ds-search input {
    border: none;
    outline: none;
    flex: 1;
    font-size: 1rem;
}

.ds-filters select {
    padding: 0 18px;
    font-size: 1rem;
    color: #334155;
}

.ds-cards {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.ds-card {
    position: relative;
    display: flex;
    gap: 28px;
    background: #fff;
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 8px 22px rgba(15, 23, 42, .08);
    overflow: visible;
}

.ds-card::before {
    content: "";
    position: absolute;
    left: 0;
    top: 0;
    width: 5px;
    height: 100%;
}

.ds-card.active::before {
    background: #22c55e;
}

.ds-card.finished::before {
    background: #ef4444;
}

.ds-card-icon {
    width: 84px;
    height: 84px;
    border-radius: 16px;
    display: grid;
    place-items: center;
    font-size: 2rem;
}

.ds-card-icon.active {
    background: #e7f8ee;
}

.ds-card-icon.finished {
    background: #feecec;
}

.ds-card-body {
    flex: 1;
}

.ds-card-body h3 {
    margin: 4px 0;
    font-size: 1.35rem;
}

.ds-org {
    margin: 0 0 16px;
    color: #64748b;
}

.ds-pesquisas {
    display: flex;
    gap: 8px;
    margin-bottom: 22px;
}

.ds-pesquisas span {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    color: #fff;
    display: grid;
    place-items: center;
    font-size: .9rem;
}

.blue { background: #2478df; }
.purple { background: #341092; }
.orange { background: #ff4817; }
.violet { background: #5f539e; }
.red { background: #e72713; }
.yellow { background: #ffc107; }

.ds-location {
    color: #64748b;
    margin: 0;
}

.ds-card-side {
    width: 380px;
    display: flex;
    flex-direction: column;
    align-items: flex-end;
}

.ds-status {
    padding: 8px 18px;
    border-radius: 999px;
    font-weight: 800;
    font-size: .85rem;
}

.ds-status.active {
    background: #dcfce7;
    color: #079445;
}

.ds-status.finished {
    background: #fee2e2;
    color: #dc2626;
}

.ds-card-side small {
    margin-top: 12px;
    color: #475569;
    font-weight: 600;
}

.ds-actions {
    margin-top: auto;
    display: flex;
    align-items: center;
    gap: 14px;
}

.ds-actions a,
.ds-more button {
    border: 1px solid #8bb7ff;
    color: #0066ff;
    background: #fff;
    padding: 12px 18px;
    border-radius: 9px;
    text-decoration: none;
    font-weight: 600;
}

.ds-actions a.disabled {
    opacity: .45;
    pointer-events: none;
    color: #64748b;
    border-color: #cbd5e1;
}

.ds-more {
    position: relative;
}

.ds-dropdown {
    display: none;
    position: absolute;
    right: 0;
    top: 48px;
    background: #fff;
    min-width: 170px;
    border-radius: 12px;
    box-shadow: 0 12px 28px rgba(15, 23, 42, .18);
    padding: 8px;
    z-index: 50;
}

.ds-dropdown a {
    display: block;
    border: none;
    padding: 10px 12px;
    color: #334155;
}

.ds-more:hover .ds-dropdown {
    display: block;
}

.ds-pagination {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 22px;
    color: #475569;
}

.ds-pagination button {
    width: 42px;
    height: 42px;
    border: 1px solid #dbe4ef;
    background: #fff;
    border-radius: 9px;
}

.ds-pagination button.active {
    background: #126dff;
    color: white;
}

@media (max-width: 992px) {
    .ds-layout {
        flex-direction: column;
    }
 
    .ds-filters {
        grid-template-columns: 1fr;
    }

    .ds-card {
        flex-direction: column;
    }

    .ds-card-side {
        width: 100%;
        align-items: flex-start;
    }

    .ds-actions {
        margin-top: 20px;
        flex-wrap: wrap;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="ds-layout"   >
    <!-- CONTENT -->
    <main class="ds-main"   >

        <div class="ds-header">
            <div>
                <h1>Jornadas</h1>
                <p>Gestiona y consulta las jornadas de salud</p>
            </div>

            <a href="<?= base_url('jornadas/crear') ?>" class="ds-btn-primary">
                + Crear Jornada
            </a>
        </div>

        <!-- FILTERS -->
        <section class="ds-filters">
            <div class="ds-search">
                <input type="text" placeholder="Buscar jornadas...">
                <span>⌕</span>
            </div>

            <select>
                <option>Estado: Todos</option>
                <option>Activa</option>
                <option>Finalizada</option>
                <option>Inactiva</option>
            </select>

            <select>
                <option>Fecha: Todas</option>
                <option>Más recientes</option>
                <option>Más antiguas</option>
            </select>

            <button class="ds-btn-outline">Limpiar filtros</button>
        </section>
        <!-- ALERTA -->
    <?php if (session('success')): ?>
        <div class="alert alert-success auto-dismiss"><?= session('success') ?></div>
    <?php endif; ?>
    <?php if (session('error')): ?>
        <div class="alert alert-danger auto-dismiss"><?= session('error') ?></div>
    <?php endif; ?>

    
        <!-- CARDS -->
        <!-- LISTADO DE JORNADAS -->
        <?php if (!empty($jornadas)): ?>
             <section class="ds-cards">

            <?php foreach ($jornadas as $jor): ?>

                <?php
                    $esFinalizada = $jor['status_jor'] == 2;
                    $estadoTexto = $jor['status_jor'] == 1 ? 'ACTIVA' : ($esFinalizada ? 'FINALIZADA' : 'INACTIVA');
                    $estadoClass = $jor['status_jor'] == 1 ? 'active' : ($esFinalizada ? 'finished' : 'inactive');
                ?>

                <article class="ds-card <?= $estadoClass ?>">

                    <div class="ds-card-icon <?= $estadoClass ?>">
                        
                    </div>

                    <div class="ds-card-body">
                        <h3><?= esc($jor['nombre_jornada']) ?></h3>
                        <p class="ds-org"><?= esc($jor['nombre_org'] ?? 'Digisalud') ?></p>

                        <div class="ds-pesquisas">
                            <div class="iconos-pesquisas" style="padding: 6px 6px 10px 6px;">
                         <?php if (!empty($jor['pesquisas'])): ?>

                <?php 
                    $iconos = [
                        '1'  => 'antropometria2.svg',
                        '2'  => 'sanguinea2.svg',
                        '3'  => 'visual2.svg',
                        '4'  => 'signosVitales2.svg',
                        '5'  => 'medicinaGeneral2.svg',
                        '6'  => 'vacunacion2.svg'
                    ];
                ?>

                    <?php foreach (explode(',', $jor['pesquisas']) as $p): ?>
                        <?php 
                            $p = trim(strtoupper($p));
                            if (isset($iconos[$p])): 
                        ?>
                            <img src="<?= base_url('img/' . $iconos[$p]) ?>" width="36">
                        <?php endif; ?>
                    <?php endforeach; ?>

                <?php endif; ?>

                    </div>
                        </div>

                        <p class="ds-location">
                            📍 <?= esc($jor['ciudad'] ?? 'Caracas') ?>
                        </p>
                    </div>

                    <div class="ds-card-side">
                        <span class="ds-status <?= $estadoClass ?>">
                            <?= $estadoTexto ?>
                        </span>

                        <small>
                            <?= date('d M Y', strtotime($jor['fecha_inicio'] ?? $jor['fecha_jornada'])) ?>
                        </small>

                        <div class="ds-actions">
                             

                             <a href="<?= $esFinalizada ? '#' : base_url('jornadas/editar/' . $jor['id_jornada']) ?>"
                                class=" btn <?= $esFinalizada ? 'disabled' : '' ?>  btn-outline-primary btn-sm"
                            >Editar </a> 

                            <?php if (in_array(session('id_rol'), [1,2,3,4])): ?>
                                <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/usuarios') ?>" 
                                class="btn btn-outline-primary btn-sm">
                                    Usuarios
                                </a>
                            <?php else: ?>
                                <a href="" class="btn btn-outline-primary btn-sm" disabled>Usuarios</a>
                            <?php endif; ?>

                            <a href="<?= base_url('jornadas/'.$jor['id_jornada'].'/beneficiarios') ?>" class="btn btn-outline-primary btn-sm">
                                Beneficiarios
                            </a>

                            <a href="<?= base_url('jornadas/' . $jor['id_jornada'] . '/reportes') ?> " class="btn btn-outline-primary btn-sm">Reportes</a>
                           <!--  <div class="ds-more">
                                <button>•••</button>
                                <div class="ds-dropdown">
                                   
                                    
                                    <a href="<?= base_url('jornadas/' . $jor['id_jornada'] . '/reportes') ?>">Reportes</a>
                                </div>
                            </div> -->
                        </div>
                    </div>

                </article>

            <?php endforeach; ?>

        </section>
        <?php else: ?>
            <div class="alert alert-info text-center mt-4">
                Crea tu primera jornada
            </div>
        <?php endif; ?>











        <footer class="ds-pagination">
            <span>Mostrando 1 a <?= count($jornadas) ?> de <?= count($jornadas) ?> jornadas</span>

            <div>
                <button>‹</button>
                <button class="active">1</button>
                <button>›</button>
            </div>
        </footer>

    </main>
</div>

<?= $this->endSection() ?>
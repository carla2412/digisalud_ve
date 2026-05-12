 <?= $this->extend('layouts/main') ?>

 <?= $this->section('css') ?>
 <style>
     :root {
         --ds-primary: #3695f5;
         --ds-dark: #1b7ae2;
         --ds-text: #101a61;
         --ds-muted: #6c757d;
         --ds-bg: #f5f7fb;
         --ds-border: #e6eaf0;
         --ds-success: #16a34a;
     }

     .org_create-page-wrapper {
         max-width: 1180px;
         margin: 0 auto;
         padding: 24px 16px 40px;
     }

     .org_create-breadcrumb {
         font-size: .82rem;
         color: var(--ds-muted);
         margin-bottom: 18px;
     }

     .org_create-breadcrumb a {
         color: var(--ds-muted);
         text-decoration: none;
     }

     .org_create-breadcrumb strong {
         color: var(--ds-text);
     }

     .org_create-page-header {
         display: flex;
         justify-content: space-between;
         gap: 20px;
         align-items: center;
         margin-bottom: 20px;
     }

     .org_create-page-header h1 {
         font-size: 1.45rem;
         font-weight: 700;
         color: var(--ds-text);
         margin: 0;
     }

     .org_create-page-header p {
         color: var(--ds-muted);
         font-size: .9rem;
         margin: 4px 0 0;
     }

     .org_create-info-box {
         display: flex;
         align-items: center;
         gap: 10px;
         background: #eef7ff;
         border: 1px solid #cfe9ff;
         color: var(--ds-text);
         padding: 12px 16px;
         border-radius: 14px;
         min-width: 260px;
     }

     .org_create-info-box i {
         color: var(--ds-primary);
         font-size: 1.25rem;
     }

     .org_create-info-box strong {
         display: block;
         font-size: .85rem;
     }

     .org_create-info-box span {
         font-size: .75rem;
         color: var(--ds-muted);
     }

     .org_create-org-card {
         background: #fff;
         border: 1px solid var(--ds-border);
         border-radius: 24px;
         box-shadow: 0 14px 35px rgba(15, 23, 42, .06);
         overflow: hidden;
     }

     .org_create-org-layout {
         display: grid;
         grid-template-columns: 280px 1fr;
         min-height: 520px;
     }

     .org_create-org-steps {
         background: linear-gradient(180deg, #f8fbff, #eef5ff);
         border-right: 1px solid var(--ds-border);
         padding: 24px;
     }

     .org_create-step {
         display: flex;
         gap: 12px;
         align-items: flex-start;
         padding: 14px;
         border-radius: 16px;
         color: var(--ds-muted);
         margin-bottom: 10px;
     }

     .org_create-step.active,
     .org_create-step.is-open {
         background: #fff;
         color: var(--ds-text);
         box-shadow: 0 8px 18px rgba(15, 23, 42, .05);
     }

     .org_create-step i {
         width: 36px;
         height: 36px;
         display: inline-flex;
         align-items: center;
         justify-content: center;
         border-radius: 12px;
         background: #e8f3ff;
         color: var(--ds-primary);
     }

     .org_create-step strong {
         display: block;
         font-size: .86rem;
     }

     .org_create-step span {
         display: block;
         font-size: .72rem;
         color: var(--ds-muted);
     }

     .org_create-org-form {
         padding: 28px;
     }

     .org_create-section-title {
         display: flex;
         align-items: center;
         gap: 10px;
         margin-bottom: 16px;
     }

     .org_create-section-title.mt {
         margin-top: 28px;
     }

     .org_create-section-title i {
         color: var(--ds-primary);
         font-size: 1.2rem;
     }

     .org_create-section-title h2 {
         font-size: 1rem;
         font-weight: 700;
         color: var(--ds-text);
         margin: 0;
     }

     .org_create-form-grid {
         display: grid;
         grid-template-columns: repeat(2, minmax(0, 1fr));
         gap: 18px;
     }

     .org_create-form-group label {
         display: block;
         font-size: .82rem;
         font-weight: 600;
         color: var(--ds-text);
         margin-bottom: 7px;
     }

     .org_create-form-group label span {
         color: #dc3545;
     }

     .org_create-form-group input,
     .org_create-form-group select {
         width: 100%;
         border: 1px solid var(--ds-border);
         border-radius: 13px;
         padding: 11px 13px;
         font-size: .88rem;
         outline: none;
         background: #fff;
     }

     .org_create-form-group input:focus,
     .org_create-form-group select:focus {
         border-color: var(--ds-primary);
         box-shadow: 0 0 0 4px rgba(54, 149, 245, .12);
     }

     .org_create-input-icon {
         position: relative;
     }

     .org_create-input-icon i {
         position: absolute;
         left: 13px;
         top: 50%;
         transform: translateY(-50%);
         color: #94a3b8;
     }

     .org_create-input-icon input {
         padding-left: 40px;
     }

     .org_create-action-card {
         margin-top: 24px;
         border: 1px solid var(--ds-border);
         border-radius: 18px;
         padding: 18px;
         background: #fafcff;
     }

     .org_create-action-card-header {
         display: flex;
         justify-content: space-between;
         gap: 16px;
         align-items: center;
     }

     .org_create-action-info {
         display: flex;
         align-items: center;
         gap: 12px;
     }

     .org_create-action-info>i {
         width: 42px;
         height: 42px;
         display: inline-flex;
         justify-content: center;
         align-items: center;
         border-radius: 14px;
         background: #e8f3ff;
         color: var(--ds-primary);
         font-size: 1.15rem;
     }

     .org_create-action-info strong {
         display: block;
         color: var(--ds-text);
         font-size: .9rem;
     }

     .org_create-action-info span {
         display: block;
         color: var(--ds-muted);
         font-size: .76rem;
     }

     .org_create-btn-outline,
     .org_create-btn-cancel,
     .org_create-btn-save {
         border: none;
         text-decoration: none;
         cursor: pointer;
         border-radius: 13px;
         font-size: .86rem;
         font-weight: 600;
         display: inline-flex;
         align-items: center;
         gap: 8px;
     }

     .org_create-btn-outline {
         background: #fff;
         color: var(--ds-dark);
         border: 1px solid var(--ds-border);
         padding: 10px 16px;
     }

     .org_create-btn-outline:hover {
         border-color: var(--ds-primary);
         color: var(--ds-primary);
     }

     .org_create-btn-outline.success {
         color: var(--ds-success);
     }

     .org_create-toggle-content {
         max-height: 0;
         overflow: hidden;
         transition: max-height .3s ease;
     }

     .org_create-toggle-content.open {
         max-height: 900px;
     }

     .org_create-address-fields {
         padding-top: 18px;
     }

     .org_create-drop-zone {
         margin-top: 16px;
         border: 2px dashed #d1d5db;
         background: #fff;
         border-radius: 16px;
         padding: 22px;
         text-align: center;
         color: var(--ds-muted);
         font-size: .82rem;
         cursor: pointer;
     }

     .org_create-drop-zone:hover {
         border-color: var(--ds-primary);
         background: #f8fbff;
     }

     .org_create-logo-preview {
         width: 96px;
         height: 96px;
         border-radius: 50%;
         object-fit: cover;
         margin-top: 14px;
         border: 4px solid #fff;
         box-shadow: 0 8px 18px rgba(15, 23, 42, .12);
     }

     .org_create-form-actions {
         border-top: 1px solid var(--ds-border);
         padding: 18px 28px;
         display: flex;
         justify-content: space-between;
         align-items: center;
         background: #fbfdff;
     }

     .org_create-btn-cancel {
         color: var(--ds-muted);
         border: 1px solid var(--ds-border);
         padding: 11px 20px;
         background: #fff;
     }

     .org_create-btn-save {
         color: #fff;
         background: #3695f5;
         padding: 12px 24px;
     }

     .org_create-btn-save:hover {
         color: #fff;
         background: #1b7ae2;
     }

     @media (max-width: 900px) {

         .org_create-page-header,
         .org_create-action-card-header {
             flex-direction: column;
             align-items: flex-start;
         }

         .org_create-org-layout {
             grid-template-columns: 1fr;
         }

         .org_create-org-steps {
             border-right: 0;
             border-bottom: 1px solid var(--ds-border);
         }

         .org_create-form-grid {
             grid-template-columns: 1fr;
         }
     }
 </style>
 <?= $this->endSection() ?>

 <?= $this->section('content') ?>

 <div class="org_create-page-wrapper">

     <div class="org_create-breadcrumb">
         <a href="<?= base_url('/') ?>">Inicio</a> /
         <a href="<?= base_url('organizaciones') ?>">Organizaciones</a> /
         <strong>Nueva Organización</strong>
     </div>

     <div class="org_create-page-header">
         <div>
             <h1>Nueva Organización</h1>
             <p>Completa los datos para registrar una nueva organización en el sistema.</p>
         </div>

         <div class="org_create-info-box">
             <i class="bi bi-info-circle"></i>
             <div>
                 <strong>Información</strong>
                 <span>Los campos marcados con <b>*</b> son obligatorios.</span>
             </div>
         </div>
     </div>

     <?php if (session()->getFlashdata('errors')): ?>
         <div class="alert alert-danger alert-dismissible fade show" role="alert">
             <ul class="mb-0">
                 <?php foreach (session()->getFlashdata('errors') as $err): ?>
                     <li><?= esc($err) ?></li>
                 <?php endforeach; ?>
             </ul>
             <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
         </div>
     <?php endif; ?>

     <form action="<?= base_url('organizaciones/guardar') ?>" method="post" enctype="multipart/form-data" class="org_create-org-card" novalidate>
         <?= csrf_field() ?>

         <div class="org_create-org-layout">

             <aside class="org_create-org-steps">
                 <div class="org_create-step active">
                     <i class="bi bi-building"></i>
                     <div>
                         <strong>Datos Básicos</strong>
                         <span>Información general</span>
                     </div>
                 </div>

                 <div class="org_create-step">
                     <i class="bi bi-telephone"></i>
                     <div>
                         <strong>Contacto</strong>
                         <span>Datos de comunicación</span>
                     </div>
                 </div>

                 <div class="org_create-step" id="stepDireccion">
                     <i class="bi bi-geo-alt"></i>
                     <div>
                         <strong>Dirección</strong>
                         <span>Ubicación de la organización</span>
                     </div>
                 </div>

                 <div class="org_create-step" id="stepLogo">
                     <i class="bi bi-image"></i>
                     <div>
                         <strong>Logo</strong>
                         <span>Imagen de la organización</span>
                     </div>
                 </div>
             </aside>

             <section class="org_create-org-form">

                 <div class="org_create-section-title">
                     <i class="bi bi-building"></i>
                     <h2>Datos Básicos</h2>
                 </div>

                 <div class="">
                     <div class="org_create-form-group">
                         <label for="nombre_org">Nombre de la organización <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-building"></i>
                             <input type="text" id="nombre_org" name="nombre_org"
                                 value="<?= old('nombre_org') ?>"
                                 placeholder="Ej: Fundación Salud Para Todos" required>
                         </div>
                     </div>

                 </div><br>
                 <div class="org_create-form-grid">


                     <div class="org_create-form-group">
                         <label for="tipo">Tipo <span>*</span></label>
                         <select id="tipo" name="tipo" class="form-select" required>
                             <option value="">Selecciona un tipo...</option>
                             <?php
                                $tipos = ['Escolar', 'Comedor', 'Empresa Privada', 'Casa hogar', 'ONG', 'Alcaldía', 'Gobernación', 'Mixto', 'Organismo Público'];
                                foreach ($tipos as $t): ?>
                                 <option value="<?= esc($t) ?>" <?= old('tipo') === $t ? 'selected' : '' ?>>
                                     <?= esc($t) ?>
                                 </option>
                             <?php endforeach; ?>
                         </select>
                     </div>

                     <div class="org_create-form-group">
                         <label for="categoria">Categoría <span>*</span></label>
                         <select id="categoria" name="categoria" class="form-select" required>
                             <option value="">Selecciona una categoría...</option>
                             <?php
                                $categorias = ['Pública', 'Privada', 'Social', 'Educativa', 'Salud', 'Comunitaria'];
                                foreach ($categorias as $c): ?>
                                 <option value="<?= esc($c) ?>" <?= old('categoria') === $c ? 'selected' : '' ?>>
                                     <?= esc($c) ?>
                                 </option>
                             <?php endforeach; ?>
                         </select>
                     </div>

                     <div class="org_create-form-group">
                         <label for="responsable_nombres">Nombre del responsable <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-person"></i>
                             <input type="text"
                                 id="responsable_nombres"
                                 name="responsable_nombres"
                                 value="<?= old('responsable_nombres') ?>"
                                 placeholder="Ej: Juan"
                                 required>
                         </div>
                     </div>

                     <div class="org_create-form-group">
                         <label for="responsable_apellidos">Apellido del responsable <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-person"></i>
                             <input type="text"
                                 id="responsable_apellidos"
                                 name="responsable_apellidos"
                                 value="<?= old('responsable_apellidos') ?>"
                                 placeholder="Ej: Pérez"
                                 required>
                         </div>
                     </div>

                     <div class="org_create-form-group">
                         <label for="password">Contraseña <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-key"></i>
                             <input type="password"
                                 id="password"
                                 name="password"
                                 minlength="6"
                                 placeholder="Mínimo 6 caracteres"
                                 required>
                         </div>
                     </div>

                     <div class="org_create-form-group">
                         <label for="confirmar_password">Confirmar contraseña <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-key-fill"></i>
                             <input type="password"
                                 id="confirmar_password"
                                 name="confirmar_password"
                                 minlength="6"
                                 placeholder="Repite la contraseña"
                                 required>
                         </div>
                     </div>
                 </div>
                 <div class="org_create-section-title  mt">
                     <i class="bi bi-telephone"></i>
                     <h2>Contacto</h2>
                 </div>

                 <div class="org_create-form-grid">
                     <div class="org_create-form-group">
                         <label for="telefono">Teléfono <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-telephone"></i>
                             <input type="text" id="telefono" name="telefono"
                                 value="<?= old('telefono') ?>"
                                 placeholder="Ej: 412 0000000" required>
                         </div>
                     </div>

                     <div class="org_create-form-group">
                         <label for="email">Correo electrónico <span>*</span></label>
                         <div class="org_create-input-icon">
                             <i class="bi bi-envelope"></i>
                             <input type="email" id="email" name="email"
                                 value="<?= old('email') ?>"
                                 placeholder="ejemplo@organizacion.org" required>
                         </div>
                     </div>
                 </div>

                 <div class="org_create-action-card org_create-address-card">
                     <div class="org_create-action-card-header">
                         <div class="org_create-action-info">
                             <i class="bi bi-geo-alt"></i>
                             <div>
                                 <strong>Dirección</strong>

                             </div>
                         </div>

                         <button type="button" class="org_create-btn-outline" onclick="toggleSeccion('secDireccion', 'stepDireccion')">
                             <i class="bi bi-map"></i>
                             Buscar dirección
                         </button>
                     </div>

                     <div class="org_create-toggle-content" id="secDireccion">
                         <input type="hidden" name="direccion_activa" id="hDireccion" value="<?= old('direccion_activa') ?>">

                         <div class="org_create-form-grid org_create-address-fields">
                             <div class="org_create-form-group">
                                 <label for="pais">País</label>
                                 <input type="text" id="pais" name="pais" value="Venezuela" readonly>
                             </div>

                             <div class="org_create-form-group">
                                 <label for="estado">Estado</label>
                                 <select id="estado" name="estado">
                                     <option value="">Selecciona un estado...</option>
                                 </select>
                             </div>

                             <div class="org_create-form-group">
                                 <label for="municipio">Municipio</label>
                                 <select id="municipio" name="municipio">
                                     <option value="">Selecciona un municipio...</option>
                                 </select>
                             </div>

                             <div class="org_create-form-group">
                                 <label for="parroquia">Parroquia</label>
                                 <select id="parroquia" name="parroquia">
                                     <option value="">Selecciona...</option>
                                 </select>
                             </div>

                             <div class="org_create-form-group">
                                 <label for="ciudad">Ciudad o localidad</label>
                                 <input type="text" name="ciudad" id="ciudad"
                                     value="<?= old('ciudad') ?>"
                                     placeholder="Se carga automático o escribe manualmente">
                             </div>
                             <div class="org_create-form-group">
                                 <label for="detalle">Detalle de dirección</label>
                                 <input type="text"
                                     name="detalle"
                                     id="detalle"
                                     value="<?= old('detalle') ?>"
                                     placeholder="Ej: Calle, avenida, edificio, punto de referencia">
                             </div>
                         </div>
                     </div>
                 </div>

                 <div class="org_create-action-card org_create-logo-card">
                     <div class="org_create-action-card-header">
                         <div class="org_create-action-info">
                             <i class="bi bi-image"></i>
                             <div>
                                 <strong>Logo de la organización</strong>
                                 <span>Opcional — PNG, JPG o JPEG máx. 2MB</span>
                             </div>
                         </div>

                         <label class="org_create-btn-outline success">
                             <i class="bi bi-cloud-arrow-up"></i>
                             Subir logo
                             <input type="file" name="logo" id="logo-input" accept=".png,.jpg,.jpeg,image/png,image/jpg,image/jpeg" hidden>
                         </label>
                     </div>

                     <div class="org_create-drop-zone" id="logo-dropzone">
                         <div id="dz-message">
                             <i class="bi bi-cloud-arrow-up" style="font-size:2rem;"></i>
                             <div>Arrastra y suelta tu archivo aquí o haz clic para seleccionar</div>
                         </div>

                         <img id="org_create-logo-preview" src="#" alt="Previsualización" class="org_create-logo-preview d-none">
                     </div>
                 </div>

             </section>
         </div>

         <div class="org_create-form-actions">
             <a href="<?= base_url('organizaciones') ?>" class="org_create-btn-cancel">Cancelar</a>

             <button type="submit" class="org_create-btn-save">
                 <i class="bi bi-check-circle"></i>
                 Guardar Organización
             </button>
         </div>

     </form>
 </div>

 <?= $this->endSection() ?>

 <?= $this->section('scripts') ?>
 <script src="<?= base_url('js/venezuela.js') ?>"></script>
 <script>
     document.getElementById("telefono").addEventListener("input", function(e) {
         let value = e.target.value.replace(/\D/g, "");

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
     const estadoActual = "<?= esc(old('estado', '')) ?>";
     const municipioActual = "<?= esc(old('municipio', '')) ?>";
     const parroquiaActual = "<?= esc(old('parroquia', '')) ?>";

     const logoDropzone = document.getElementById('logo-dropzone');
     const logoInput = document.getElementById('logo-input');

     logoDropzone.addEventListener('click', function() {
         logoInput.click();
     });

     logoDropzone.addEventListener('dragover', function(e) {
         e.preventDefault();
         this.style.borderColor = '#3695f5';
     });

     logoDropzone.addEventListener('dragleave', function() {
         this.style.borderColor = '#d1d5db';
     });

     logoDropzone.addEventListener('drop', function(e) {
         e.preventDefault();
         this.style.borderColor = '#d1d5db';

         if (e.dataTransfer.files.length > 0) {
             logoInput.files = e.dataTransfer.files;
             mostrarPreviewLogo(e.dataTransfer.files[0]);
         }
     });

     logoInput.addEventListener('change', function(e) {
         const file = e.target.files[0];
         if (!file) return;
         mostrarPreviewLogo(file);
     });

     function mostrarPreviewLogo(file) {
         const reader = new FileReader();

         reader.onload = function(ev) {
             const preview = document.getElementById('org_create-logo-preview');
             preview.src = ev.target.result;
             preview.classList.remove('d-none');
             document.getElementById('dz-message').classList.add('d-none');

             const stepLogo = document.getElementById('stepLogo');
             if (stepLogo) stepLogo.classList.add('is-open');
         };

         reader.readAsDataURL(file);
     }

     function toggleSeccion(id, stepId) {
         const sec = document.getElementById(id);
         const step = document.getElementById(stepId);

         if (!sec) return;

         const isOpen = sec.classList.contains('open');
         sec.classList.toggle('open');

         if (step) {
             step.classList.toggle('is-open');
         }

         const hidden = sec.querySelector('input[type="hidden"]');
         if (hidden) {
             hidden.value = isOpen ? '' : '1';
         }
     }

     document.addEventListener('DOMContentLoaded', function() {
         const $e = document.getElementById('estado');
         const $m = document.getElementById('municipio');
         const $p = document.getElementById('parroquia');
         const $ciudad = document.getElementById('ciudad');

         if (typeof ubicaciones !== 'undefined') {
             Object.keys(ubicaciones).forEach(function(estado) {
                 const opt = document.createElement('option');
                 opt.value = estado;
                 opt.textContent = estado;
                 $e.appendChild(opt);
             });
         }

         function cargarMunicipios(est, munSel) {
             $m.innerHTML = '<option value="">Selecciona un municipio...</option>';
             $p.innerHTML = '<option value="">Selecciona...</option>';

             if (!est || typeof ubicaciones === 'undefined' || !ubicaciones[est]) return;

             Object.keys(ubicaciones[est]).forEach(function(mun) {
                 const opt = document.createElement('option');
                 opt.value = mun;
                 opt.textContent = mun;

                 if (mun === munSel) {
                     opt.selected = true;
                 }

                 $m.appendChild(opt);
             });
         }

         function cargarParroquias(est, mun, parSel) {
             $p.innerHTML = '<option value="">Selecciona...</option>';

             if (!est || !mun || typeof ubicaciones === 'undefined' || !ubicaciones[est] || !ubicaciones[est][mun]) return;

             ubicaciones[est][mun].forEach(function(par) {
                 const opt = document.createElement('option');
                 opt.value = par;
                 opt.textContent = par;

                 if (par === parSel) {
                     opt.selected = true;
                 }

                 $p.appendChild(opt);
             });
         }

         $e.addEventListener('change', function() {
             $ciudad.value = '';
             cargarMunicipios(this.value, '');
         });

         $m.addEventListener('change', function() {
             const est = $e.value;
             const mun = this.value;

             cargarParroquias(est, mun, '');

             const parroquias = ubicaciones?.[est]?.[mun] || [];
             $ciudad.value = parroquias.length > 0 ? parroquias[0] : '';
         });

         $p.addEventListener('change', function() {
             if (this.value) {
                 $ciudad.value = this.value;
             }
         });

         if (estadoActual) {
             $e.value = estadoActual;
             cargarMunicipios(estadoActual, municipioActual);

             if (municipioActual) {
                 cargarParroquias(estadoActual, municipioActual, parroquiaActual);
             }

             toggleSeccion('secDireccion', 'stepDireccion');
         }
     });
 </script>
 <?= $this->endSection() ?>
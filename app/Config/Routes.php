<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Auth::login');

// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');
$routes->post('auth/attempt', 'Auth::attempt');

// Registro individual
$routes->get('registro/individual', 'Registro::individual');
$routes->get('registro/individual/validar-email', 'Registro::validarEmailIndividual');
$routes->post('registro/individual', 'Registro::guardarIndividual');

// Registro organizacion
$routes->get('registro/organizacion', 'Registro::organizacion');
$routes->post('registro/organizacion', 'Registro::guardarOrganizacion');

// DASHBOARD
$routes->get('dashboard', 'Dashboard::index');

// ═══ PERFIL DE USUARIO ═══
$routes->get('perfil', 'PerfilController::index');
$routes->get('perfil/validar-email', 'PerfilController::validarEmail');
$routes->post('perfil/actualizar', 'PerfilController::actualizar');
$routes->post('perfil/subir-foto', 'PerfilController::subirFoto');

// usuarios
$routes->group('usuarios', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'UsuariosController::index');
    $routes->get('listado', 'UsuariosController::listadoAjax');
    $routes->post('cambiar-correo/(:num)', 'UsuariosController::cambiarCorreo/$1');
    $routes->post('cambiar-password/(:num)', 'UsuariosController::cambiarPassword/$1');
    $routes->post('bloquear/(:num)', 'UsuariosController::bloquear/$1');
    $routes->post('agregar-organizacion/(:num)', 'UsuariosController::agregarOrganizacion/$1');
});

// Acciones JORNADAS
$routes->group('jornadas', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'Jornadas::index');
    $routes->get('listar', 'Jornadas::listar');
    $routes->post('guardar', 'Jornadas::guardar');
    $routes->get('crear', 'Jornadas::crear');
    $routes->get('editar/(:num)',  'Jornadas::editar/$1');
    $routes->post('actualizar',    'Jornadas::actualizar');
    $routes->post('cambiar-status', 'Jornadas::cambiarStatus');
    $routes->get('buscar-instituciones', 'Jornadas::buscarInstituciones');
});

// =====================================================
// RUTAS MÓDULO USUARIOS EN JORNADAS
// Agregar al final de app/Config/Routes.php
// =====================================================

$routes->group('jornadas', ['filter' => 'auth'], function ($routes) {
    // Vista principal de usuarios de una jornada
    $routes->get('(:num)/usuarios', 'JornadaUsuariosController::index/$1');
    // Buscar usuarios AJAX
    $routes->get('usuarios/buscar-ajax', 'JornadaUsuariosController::buscarUsuarioAjax');
    // Asignar usuario a jornada (POST)
    $routes->post('(:num)/usuarios/asignar', 'JornadaUsuariosController::asignar/$1');
    // Eliminar usuario de jornada (POST)
    $routes->post('(:num)/usuarios/eliminar', 'JornadaUsuariosController::eliminar/$1');
    // Listar asignados AJAX
    $routes->get('(:num)/usuarios/listar', 'JornadaUsuariosController::listarAsignados/$1');
});

// BENEFICIARIOS jornadas
// ✅ DESPUÉS — protegido con auth
$routes->group('jornadas', ['filter' => 'auth'], function ($routes) {
    $routes->get('(:num)/beneficiarios',        'JornadaBeneficiariosController::index/$1');
    $routes->get('(:num)/beneficiarios/create', 'BeneficiariosController::create/$1');
    $routes->post('(:num)/beneficiarios/store', 'BeneficiariosController::store/$1');
    $routes->get('(:num)/beneficiarios/buscar', 'JornadaBeneficiariosController::buscar/$1');
    $routes->post('(:num)/asociar/(:num)',       'JornadaBeneficiariosController::asociar/$1/$2');
    $routes->get('(:num)/desasociar/(:num)',     'JornadaBeneficiariosController::desasociar/$1/$2');
    $routes->get('(:num)/beneficiarios/(:num)/ficha-rapida','JornadaBeneficiariosController::fichaRapida/$1/$2'
);
});

// =====================================================
// FRAGMENTO PARA app/Config/Routes.php
// REEMPLAZAR el group('beneficiarios') existente con este:
// =====================================================

// BENEFICIARIOS - LISTADO GENERAL
$routes->get('beneficiarios', 'BeneficiariosController::index');
// EXPORTAR BENEFICIARIOS
$routes->get('beneficiarios/exportar', 'BeneficiariosController::exportar');
// HISTORIAL BENEFICIARIO
$routes->get('beneficiarios/(:num)/historial', 'BeneficiariosController::historial/$1');
// AJAX
$routes->get('beneficiarios/buscar-ajax', 'BeneficiariosController::buscarAjax');
$routes->get('beneficiarios/antecedentes-ajax', 'BeneficiariosController::antecedentesAjax');
$routes->get('beneficiarios/buscar-antecedentes-ajax', 'BeneficiariosController::buscarAntecedentesAjax');

// EDITAR BENEFICIARIO
$routes->get('beneficiarios/editar/(:num)', 'BeneficiariosController::edit/$1');
$routes->post('beneficiarios/actualizar/(:num)', 'BeneficiariosController::update/$1');

// ================================================================
// MÓDULO: Organizaciones
// Acceso: roles 1, 2, 3 (verificado en controlador)
// ================================================================

$routes->get('organizaciones',               'Organizaciones::index',     ['as' => 'organizaciones.index']);
$routes->get('organizaciones/crear',         'Organizaciones::create',    ['as' => 'organizaciones.create']);
$routes->post('organizaciones/guardar',      'Organizaciones::store',     ['as' => 'organizaciones.store']);
$routes->get('organizaciones/editar/(:num)', 'Organizaciones::edit/$1',   ['as' => 'organizaciones.edit']);
$routes->get('organizaciones/validar-email/(:num)', 'OrganizacionesEmailValidation::validar/$1');
$routes->post('organizaciones/update/(:num)', 'Organizaciones::update/$1', ['as' => 'organizaciones.update']);
// ═══════════════════════════════════════════════════════════════
// EVALUACIONES (transversal jornadas y centros)
// Agregar al final de app/Config/Routes.php
// ═══════════════════════════════════════════════════════════════
 
$routes->group('evaluaciones', ['filter' => 'auth'], function ($routes) {
    // Página completa del formulario de evaluación
    $routes->get('formulario/(:num)/(:num)', 'EvaluacionesController::formulario/$1/$2');
    // Guardar evaluación (POST AJAX)
    $routes->post('guardar', 'EvaluacionesController::guardar');
    // ═══ LABORATORIO: Interpretación clínica ═══
    $routes->get('lab/semaforo', 'LabInterpretacionController::semaforo', ['filter' => 'auth']);
});

$routes->get('evaluaciones/historial-sanguinea/(:num)', 'EvaluacionesController::historialSanguinea/$1');

// Si prefieres dejarlo fuera del grupo, igual que historial-sanguinea actual:
$routes->get('evaluaciones/historial-antropometria/(:num)', 'EvaluacionesController::historialAntropometria/$1');
 
 // =====================================================
// MÓDULO CENTRALIZADO DE CARGAS MASIVAS
// =====================================================
$routes->group('cargas-masivas', ['filter' => 'auth'], function ($routes) {
    // Ruta base del módulo
    $routes->get('', 'CargaMasivaController::index');
    $routes->get('/', 'CargaMasivaController::index');

    // Plantillas
    $routes->get('plantillas/(:segment)', 'CargaMasivaController::descargarPlantilla/$1');

    // Información de jornada seleccionada
    $routes->get('jornada/(:num)', 'CargaMasivaController::jornadaInfo/$1');

    // Procesar carga por jornada y tipo
    $routes->post('procesar/(:num)/(:segment)', 'CargaMasivaController::procesar/$1/$2');
});
// ═══════════════════════════════════════════════════════════════
// RUTAS — Reportes Antropométricos
// Agregar DENTRO del grupo existente $routes->group('jornadas', ...)
// ═══════════════════════════════════════════════════════════════

$routes->group('jornadas', static function ($routes) {
    // Home de reportes (ya existe)
    $routes->get('(:num)/reportes', 'Reportes::home/$1');

    // ─── Antropometría: vistas detalle ──────────────────────────
    $routes->get('(:num)/reportes/antropometria/adultos',
        'ReportesAntropometriaController::adultos/$1');

    $routes->get('(:num)/reportes/antropometria/menores-19',
        'ReportesAntropometriaController::menores19/$1');

    $routes->get('(:num)/reportes/antropometria/embarazadas',
        'ReportesAntropometriaController::embarazadas/$1');

    // ─── Antropometría: exportar Excel ──────────────────────────
    $routes->get('(:num)/reportes/antropometria/adultos/excel',
        'ReportesAntropometriaController::exportarExcel/$1/adultos');

    $routes->get('(:num)/reportes/antropometria/menores-19/excel',
        'ReportesAntropometriaController::exportarExcel/$1/menores-19');

    $routes->get('(:num)/reportes/antropometria/embarazadas/excel',
        'ReportesAntropometriaController::exportarExcel/$1/embarazadas');
});
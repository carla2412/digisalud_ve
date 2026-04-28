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
$routes->post('registro/individual', 'Registro::guardarIndividual');

// Registro organizacion
$routes->get('registro/organizacion', 'Registro::organizacion');
$routes->post('registro/organizacion', 'Registro::guardarOrganizacion');



// DASHBOARD
$routes->get('dashboard', 'Dashboard::index');

// ═══ PERFIL DE USUARIO ═══
$routes->get('perfil', 'PerfilController::index');
$routes->post('perfil/actualizar', 'PerfilController::actualizar');
$routes->post('perfil/subir-foto', 'PerfilController::subirFoto');  // ← NUEVA RUTA

// usuarios
$routes->group('usuarios', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'UsuariosController::index');
    $routes->get('listado', 'UsuariosController::listadoAjax');
    $routes->post('cambiar-correo/(:num)', 'UsuariosController::cambiarCorreo/$1');
    $routes->post('cambiar-password/(:num)', 'UsuariosController::cambiarPassword/$1');
    $routes->post('bloquear/(:num)', 'UsuariosController::bloquear/$1');
    $routes->post('agregar-organizacion/(:num)', 'UsuariosController::agregarOrganizacion/$1');
});

// Acciones JORNADAS
$routes->group('jornadas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Jornadas::index');
    $routes->get('listar', 'Jornadas::listar');
    $routes->post('guardar', 'Jornadas::guardar');
    $routes->get('crear', 'Jornadas::crear');
    $routes->get('editar/(:num)',  'Jornadas::editar/$1');      // ← NUEVO
    $routes->post('actualizar',    'Jornadas::actualizar');     // ← NUEVO
    $routes->post('cambiar-status', 'Jornadas::cambiarStatus');
});
// =====================================================
// RUTAS MÓDULO USUARIOS EN JORNADAS
// Agregar al final de app/Config/Routes.php
// =====================================================

$routes->group('jornadas', ['filter' => 'auth'], function($routes){
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
// BENEFICIARIOS
$routes->group('jornadas', function($routes){
    $routes->get('(:num)/beneficiarios', 'JornadaBeneficiariosController::index/$1');
    $routes->post('(:num)/asociar/(:num)', 'JornadaBeneficiariosController::asociar/$1/$2');
    $routes->get('(:num)/desasociar/(:num)', 'JornadaBeneficiariosController::desasociar/$1/$2');
});
/* 
$routes->group('beneficiarios', function($routes){
    $routes->get('buscar/(:num)', 'BeneficiariosController::buscar/$1');
    $routes->get('buscarAjax', 'BeneficiariosController::buscarAjax');
    $routes->get('buscarAntecedentesAjax', 'BeneficiariosController::buscarAntecedentesAjax');
    $routes->get('create/(:num)', 'BeneficiariosController::create/$1');
    $routes->post('store/(:num)', 'BeneficiariosController::store/$1');
    $routes->get('editar/(:num)', 'BeneficiariosController::edit/$1');
    $routes->post('actualizar/(:num)', 'BeneficiariosController::update/$1');
}); */

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

// CREAR BENEFICIARIO DESDE JORNADA
$routes->get('jornadas/(:num)/beneficiarios/create', 'BeneficiariosController::create/$1');
$routes->post('jornadas/(:num)/beneficiarios/store', 'BeneficiariosController::store/$1');

// BUSCAR BENEFICIARIO DESDE JORNADA
$routes->get('jornadas/(:num)/beneficiarios/buscar', 'BeneficiariosController::buscar/$1');

// AJAX
$routes->get('beneficiarios/buscar-ajax', 'BeneficiariosController::buscarAjax');
$routes->get('beneficiarios/antecedentes-ajax', 'BeneficiariosController::buscarAntecedentesAjax');

// EDITAR BENEFICIARIO
$routes->get('beneficiarios/editar/(:num)', 'BeneficiariosController::edit/$1');
$routes->post('beneficiarios/actualizar/(:num)', 'BeneficiariosController::update/$1');
// ================================================================
// MÓDULO: Organizaciones
// Acceso: roles 1, 2, 3 (verificado en controlador)
// ================================================================

$routes->get('organizaciones',              'Organizaciones::index',  ['as' => 'organizaciones.index']);
$routes->get('organizaciones/crear',        'Organizaciones::create', ['as' => 'organizaciones.create']);
$routes->post('organizaciones/guardar',     'Organizaciones::store',  ['as' => 'organizaciones.store']);
$routes->get('organizaciones/editar/(:num)', 'Organizaciones::edit/$1',   ['as' => 'organizaciones.edit']);
$routes->post('organizaciones/update/(:num)', 'Organizaciones::update/$1', ['as' => 'organizaciones.update']);
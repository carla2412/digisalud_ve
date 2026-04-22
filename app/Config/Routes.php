<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// $routes->get('/', 'Home::index');
$routes->get('/', 'Auth::login');
// Login
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');

// Registro individual
$routes->get('registro/individual', 'Registro::individual');
$routes->post('registro/individual', 'Registro::guardarIndividual');

// Registro organizacion
$routes->get('registro/organizacion', 'Registro::organizacion');
$routes->post('registro/organizacion', 'Registro::guardarOrganizacion');

// LOGIN
$routes->get('login', 'Auth::login');
$routes->post('auth/attempt', 'Auth::attempt');   // ← CAMBIA esta línea

// DASHBOARD
$routes->get('dashboard', 'Dashboard::index');

// perfil de usuario
$routes->get('perfil', 'PerfilController::index');
$routes->post('perfil/actualizar', 'PerfilController::actualizar');

// usuarios
$routes->group('usuarios', ['filter' => 'auth'], function($routes) {

    // Vista principal
    $routes->get('/', 'UsuariosController::index');

    // Listado AJAX
    $routes->get('listado', 'UsuariosController::listadoAjax');

    // Acciones POST
    $routes->post('cambiar-correo/(:num)', 'UsuariosController::cambiarCorreo/$1');
    $routes->post('cambiar-password/(:num)', 'UsuariosController::cambiarPassword/$1');
    $routes->post('bloquear/(:num)', 'UsuariosController::bloquear/$1');
    $routes->post('agregar-organizacion/(:num)', 'UsuariosController::agregarOrganizacion/$1');
});
 // =====================================================
// RUTAS MÓDULO BENEFICIARIOS — reemplazar en app/Config/Routes.php
// =====================================================

// Acciones JORNADAS
/**
 * =====================================================
 * ARCHIVO: app/Config/Routes.php
 * =====================================================
 * 
 * INSTRUCCIÓN: Reemplazar el grupo 'jornadas' con filter auth.
 */

// Acciones JORNADAS
$routes->group('jornadas', ['filter' => 'auth'], function($routes) {
    $routes->get('/',              'Jornadas::index');
    $routes->get('listar',         'Jornadas::listar');
    $routes->get('crear',          'Jornadas::crear');
    $routes->post('guardar',       'Jornadas::guardar');
    $routes->get('editar/(:num)',  'Jornadas::editar/$1');      // ← NUEVO
    $routes->post('actualizar',    'Jornadas::actualizar');     // ← NUEVO
    $routes->post('cambiar-status','Jornadas::cambiarStatus');
     // NUEVA RUTA
    $routes->get('(:num)/beneficiarios', 'BeneficiariosController::listar/$1');
});
 

// El group('beneficiarios') completo debe quedar así:
 
$routes->group('beneficiarios', function($routes){
    $routes->get('buscar/(:num)', 'BeneficiariosController::buscar/$1');
    $routes->get('buscarAjax', 'BeneficiariosController::buscarAjax');
    $routes->get('buscarAntecedentesAjax', 'BeneficiariosController::buscarAntecedentesAjax');
    $routes->get('create/(:num)', 'BeneficiariosController::create/$1');
    $routes->post('store/(:num)', 'BeneficiariosController::store/$1');
 
    // EDITAR BENEFICIARIO
    $routes->get('editar/(:num)', 'BeneficiariosController::edit/$1');
    $routes->post('actualizar/(:num)', 'BeneficiariosController::update/$1');
});


// ================================================================
// MÓDULO: Organizaciones
// Acceso: roles 1, 2, 3 (verificado en controlador)
// ================================================================
 
$routes->get('organizaciones',              'Organizaciones::index',  ['as' => 'organizaciones.index']);
$routes->get('organizaciones/crear',        'Organizaciones::create', ['as' => 'organizaciones.create']);
$routes->post('organizaciones/guardar',     'Organizaciones::store',  ['as' => 'organizaciones.store']);
$routes->get('organizaciones/editar/(:num)', 'Organizaciones::edit/$1',   ['as' => 'organizaciones.edit']);
$routes->post('organizaciones/update/(:num)', 'Organizaciones::update/$1', ['as' => 'organizaciones.update']);
 
// Endpoint seguro para servir logos (fuera del public root)
$routes->get('organizaciones/logo/(:segment)', 'Organizaciones::logo/$1', ['as' => 'organizaciones.logo']);
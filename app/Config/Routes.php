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

    // Acciones JORNADAS
$routes->group('jornadas', ['filter' => 'auth'], function($routes) {
    $routes->get('/', 'Jornadas::index');                   // Lista
    $routes->get('listar', 'Jornadas::listar');             // DataTable
    $routes->post('guardar', 'Jornadas::guardar');          // Crear / Editar
    $routes->get('crear', 'Jornadas::crear');
    $routes->post('cambiar-status', 'Jornadas::cambiarStatus');
});

// =====================================================
// RUTAS MÓDULO BENEFICIARIOS — reemplazar en app/Config/Routes.php
// (borrar las rutas viejas de jornadas/beneficiarios y beneficiarios)
// =====================================================

$routes->group('jornadas', function($routes){
    $routes->get('(:num)/beneficiarios', 'JornadaBeneficiariosController::index/$1');
    $routes->post('(:num)/asociar/(:num)', 'JornadaBeneficiariosController::asociar/$1/$2');
    $routes->get('(:num)/desasociar/(:num)', 'JornadaBeneficiariosController::desasociar/$1/$2');
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
 
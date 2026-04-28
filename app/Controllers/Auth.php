<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolesUsuariosContextoModel;
use App\Models\OrganizacionModel;
class Auth extends BaseController
{
    public function __construct()
    {
        helper('url'); // 👈 cargado una sola vez para todo el controlador
    }
    public function login()
{
    // Si ya está logueado, enviarlo al dashboard
    if (session()->get('logged_in')) {
        return redirect()->to('/dashboard');
    }

    return view('auth/login');
}



  public function attempt()
{
    $identity = $this->request->getPost('identity');
    $password = $this->request->getPost('password');

    $usuarioModel = new UsuarioModel();
    $rolesContextoModel = new RolesUsuariosContextoModel();
    $orgModel = new OrganizacionModel(); // 👈 Aquí cargas el modelo de organizaciones

    // Buscar usuario por email o username
    $user = $usuarioModel->buscarPorEmailOUsername($identity);

    if (!$user) {
        return redirect()->back()->with('error', 'Usuario no encontrado');
    }

    // Validar contraseña
    if (!password_verify($password, $user['password_hash'])) {
        return redirect()->back()->with('error', 'Contraseña incorrecta');
    }

    // Obtener rol y organización del usuario
    $contexto = $rolesContextoModel
        ->where('id_usuario', $user['id_usuario'])
        ->where('status_urc', 1)
        ->first();

    if (!$contexto) {
        return redirect()->back()->with('error', 'El usuario no tiene rol asignado.');
    }

    // ============================
    // 🚀 Obtener nombre de la organización
    // ============================

    $nombreOrg = '';

    if (!empty($contexto['organizacion_id'])) {
        $org = $orgModel->find($contexto['organizacion_id']);
        $nombreOrg = $org ? $org['nombre_org'] : '';
    }

    // ============================
    // 🚀 Guardar datos en sesión
    // ============================

    session()->set([
        'logged_in'       => true,
        'id_usuario'      => $user['id_usuario'],
        'nombres'         => $user['nombres'],
        'apellidos'       => $user['apellidos'],
        'nombre_completo' => $user['nombres'] . ' ' . $user['apellidos'],
        'id_rol'          => $contexto['id_rol'],
        'organizacion_id' => $contexto['organizacion_id'],
        'nombre_org'      => $nombreOrg,
        'foto_url'        => $user['foto_url'] ?? null
    ]);

    return redirect()->to('/dashboard');
}


    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }
}

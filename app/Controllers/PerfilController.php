<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolesUsuariosContextoModel;

class PerfilController extends BaseController
{
    public function index()
    {
        $id = session('id_usuario');

        $usuarioModel = new UsuarioModel();
        $rolesModel   = new RolesUsuariosContextoModel();

        $perfil = $usuarioModel->obtenerPerfil($id);
         // 🔥 AGREGAR CONSULTA PARA OBTENER EL ROL DEL USUARIO
        $rolData = $rolesModel
              ->select('roles.nombre_rol, roles.descripcion_rol')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol')
            ->where('roles_usuarios_contexto.id_usuario', $id)
            ->first();

    // Agregamos el rol al arreglo perfil
    $perfil['rol'] = $rolData['nombre_rol'] ?? 'Sin rol asignado';
    $perfil['descripcion_rol']  = $rolData['descripcion_rol'] ?? '';
    
    $estadisticas = [
            'jornadas' => $rolesModel->contarJornadas($id),
            'centros'  => $rolesModel->contarCentros($id),
            'detalle_jornadas' => $rolesModel->obtenerJornadasUsuario($id),
            'detalle_centros'  => $rolesModel->obtenerCentrosUsuario($id),
        ];

        return view('perfil/index', compact('perfil','estadisticas'));
    }

    public function actualizar()
    {
        $id = session('id_usuario');

        $data = [
            'nombres'          => $this->request->getPost('nombres'),
            'apellidos'        => $this->request->getPost('apellidos'),
            'genero'           => $this->request->getPost('genero'),
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
            'email'            => $this->request->getPost('email'),
            'username'       => explode('@', $this->request->getPost('email'))[0],
            'profesion'        => $this->request->getPost('profesion'),
            'telefono'         => $this->request->getPost('telefono')
        ];

        $usuarioModel = new UsuarioModel();
        $usuarioModel->update($id, $data);

        return redirect()->to('perfil')->with('success','Perfil actualizado correctamente');
    }
}

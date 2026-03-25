<?php

namespace App\Controllers;

use App\Models\OrganizacionModel;
use App\Models\RolModel;
use App\Models\UsuarioModel;

class UsuariosController extends BaseController
{
    protected $usuarioModel;
    
    private function puedeEditarUsuarios(): bool
    {
        return in_array(session()->get('id_rol'), [1,2,3,4]);
    }
    private function esAdmin(): bool
    {
        return session()->get('id_rol') == 1;
    }

    public function __construct()
        {
            $this->usuarioModel = new UsuarioModel();
            helper(['form', 'url']);
        }
    public function index()
        {
            $rol = session()->get('id_rol');
            $orgId = session()->get('organizacion_id');

            $organizacionModel = new OrganizacionModel();
            $rolModel = new RolModel();
            
            $rolSesion = session()->get('id_rol');

            // ADMIN APP y ADMIN DIGI → ven todos los roles
            if (in_array($rolSesion, [1,2])) {
                $data['roles'] = $rolModel->findAll();
            } 
            // ADMIN ORG → solo roles de su organización
            else if ($rolSesion == 3) {
                $data['roles'] = $rolModel
                    ->whereIn('id_rol', [4,5,6,7])
                    ->findAll();
            }
            // Otros roles no deberían usar este modal
            else {
                $data['roles'] = [];
            }


            // 🔍 Capturar texto del buscador
            $buscar = $this->request->getGet('q');

            // ============================
            // ADMIN GLOBAL + ADMIN DIGI
            // ============================
            if (in_array($rol, [1, 2])) 
            {
                if ($buscar) {
                    // Búsqueda global con join
                    $data['usuarios'] = $this->usuarioModel->buscarUsuarios($buscar);
                } else {
                    // Listado normal con organización
                    $data['usuarios'] = $this->usuarioModel->usuariosConOrganizacion();
                }

                $data['organizaciones'] = $organizacionModel->findAll();
            }

            // ============================
            // USUARIOS LIMITADOS A SU ORG
            // ============================
            else 
            {
                if ($buscar) {
                    $data['usuarios'] = $this->usuarioModel->buscarUsuariosPorOrg($buscar, $orgId);
            // ya incluye filtrado por organización
                } 
                else {
                    $data['usuarios'] = $this->usuarioModel
                                            ->usuariosConOrganizacionFiltrado($orgId);
                }

                $data['organizaciones'] = $organizacionModel
                                            ->where('id_organizacion', $orgId)
                                            ->findAll();
            }

             // 🔥 Estas dos variables SON NECESARIAS PARA TU JAVASCRIPT
            $data['orgSesion'] = $orgId;
            $data['rolSesion'] = $rol;   // <<<<<< AÑADIR ESTO

            return view('usuarios/index', $data);
        }



    private function validarAcceso($usuario)
        {
            $rol = session()->get('id_rol');
            $orgSesion = session()->get('organizacion_id');

            if (!in_array($rol, [1,2,3])) {
                return false;
            }
            if ($usuario['organizacion_id'] != $orgSesion) {
                return false;
            }
            return true;
        }

    public function bloquear($id)
{
    if (!$this->esAdmin()) {
        return $this->response->setJSON(['error' => 'No autorizado']);
    }

    $usuario = $this->usuarioModel->find($id);

    log_message('debug', "bloquear: id={$id}, status_usu={$usuario['status_usu']}");

    $nuevoStatus = $usuario['status_usu'] == 1 ? 0 : 1;

    $this->usuarioModel->update($id, [
        'status_usu' => $nuevoStatus
    ]);

    log_message('debug', "bloquear: updated id={$id}, nuevoStatus={$nuevoStatus}");

    return $this->response->setJSON(['success' => true]);
}


    public function cambiarCorreo($id)
    {
        if (!$this->puedeEditarUsuarios()) {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $nuevoCorreo = $this->request->getPost('email');

        // Extraer username antes del @
        $username = explode('@', $nuevoCorreo)[0];

        // Actualizar en BD
        $this->usuarioModel->update($id, [
            'email' => $nuevoCorreo,
            'username' => $username
        ]);

        return $this->response->setJSON(['success' => true]);
    }



    public function cambiarPassword($id)
        {
            if (!$this->puedeEditarUsuarios()) {
                return $this->response->setJSON(['error' => 'No autorizado']);
            }

            $hash = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);

            $this->usuarioModel->update($id, [
                'password_hash' => $hash
            ]);

            return $this->response->setJSON(['success' => true]);
        }

    public function agregarOrganizacion($id)
    {
        $rolSesion = session()->get('id_rol');

        if (!in_array($rolSesion, [1,2,3])) {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $usuario = $this->usuarioModel->find($id);

        if ($usuario['organizacion_id'] != 1) {
            return $this->response->setJSON(['error' => 'El usuario no es independiente']);
        }

        $orgNueva = $this->request->getPost('organizacion_id');
        $rolNuevo = $this->request->getPost('id_rol');

        // Actualizar usuario
        $this->usuarioModel->update($id, [
            'organizacion_id' => $orgNueva,
        ]);

        // Registrar rol en el contexto
        db_connect()->table('roles_usuarios_contexto')->insert([
            'id_usuario'      => $id,
            'id_rol'          => $rolNuevo,
            'organizacion_id' => $orgNueva,
            'status_urc'      => 1
        ]);

        return $this->response->setJSON(['success' => true]);
}

    public function listadoAjax()
    {
        $rol   = session()->get('id_rol');
        $orgId = session()->get('organizacion_id');

        // ADMIN GLOBAL + DIGI (rol 1 y 2): ven todos
        if (in_array($rol, [1, 2])) {
            $usuarios = $this->usuarioModel->usuariosConOrganizacion();
        } 
        // OTROS: sólo su organización
        else {
            // Usuarios independientes + usuarios de su organización
            $usuarios = $this->usuarioModel
                     ->usuariosIndependientesYOtros($orgId);
        }

        // DataTables espera un JSON con al menos "data"
        return $this->response->setJSON([
            'data' => $usuarios
        ]);
    }




}

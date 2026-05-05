<?php
// ========================================================
// ARCHIVO: app/Controllers/JornadaUsuariosController.php
// Gestión de usuarios participantes en una jornada
// ========================================================

namespace App\Controllers;

use App\Models\JornadaModel;
use App\Models\UsuarioModel;
use App\Models\RolesUsuariosContextoModel;

class JornadaUsuariosController extends BaseController
{
    /**
     * Vista principal: muestra panel de búsqueda + detalle de jornada
     */
    public function index($jornada_id)
    {
        $jornadaModel = new JornadaModel();

        // Datos de la jornada con pesquisas, institución y ubicación
        $jornada = $jornadaModel
            ->select("jornadas.*, instituciones.nombre_institucion, dir.ciudad,
                      GROUP_CONCAT(DISTINCT tpa.idtipo_pesquisa ORDER BY tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->where('jornadas.id_jornada', $jornada_id)
            ->groupBy('jornadas.id_jornada')
            ->first();

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada');
        }

        // Usuarios ya asignados a esta jornada
        $rucModel = new RolesUsuariosContextoModel();
        $usuariosAsignados = $rucModel
    ->select('
        roles_usuarios_contexto.*,
        usuarios.nombres,
        usuarios.apellidos,
        usuarios.profesion,
        usuarios.organizacion_id AS organizacion_principal_id,
        org_principal.nombre_org AS nombre_org_principal,
        org_contexto.nombre_org AS nombre_org_contexto,
        roles.nombre_rol
    ')
    ->join('usuarios', 'usuarios.id_usuario = roles_usuarios_contexto.id_usuario')
    ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol')
    ->join('organizacion AS org_principal', 'org_principal.id_organizacion = usuarios.organizacion_id', 'left')
    ->join('organizacion AS org_contexto', 'org_contexto.id_organizacion = roles_usuarios_contexto.organizacion_id', 'left')
    ->where('roles_usuarios_contexto.jornada_id', $jornada_id)
    ->where('roles_usuarios_contexto.tipo_contexto', 'JORNADA')
    ->where('roles_usuarios_contexto.status_urc', 1)
    ->findAll();

        // Rol del usuario en sesión para controlar permisos en la vista
        $rolSesion = session('id_rol');

        return view('jornadas/usuarios', [
            'jornada'           => $jornada,
            'jornada_id'        => $jornada_id,
            'usuariosAsignados' => $usuariosAsignados,
            'rolSesion'         => $rolSesion,
        ]);
    }

    /**
     * Búsqueda AJAX de usuarios por palabra clave
     */
    public function buscarUsuarioAjax()
    {
        $term = $this->request->getGet('q');
        $jornada_id = $this->request->getGet('jornada_id');

        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $usuarioModel = new UsuarioModel();

        $usuarios = $usuarioModel
            ->select('usuarios.id_usuario, usuarios.nombres, usuarios.apellidos, 
                      usuarios.profesion, usuarios.organizacion_id,
                      organizacion.nombre_org')
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->where('usuarios.status_usu', 1)
            ->groupStart()
                ->like('usuarios.nombres', $term)
                ->orLike('usuarios.apellidos', $term)
                ->orLike('usuarios.email', $term)
            ->groupEnd()
            ->limit(15)
            ->findAll();

        // Marcar los que ya están asignados a esta jornada
        if ($jornada_id) {
            $rucModel = new RolesUsuariosContextoModel();
            $asignados = $rucModel
                ->where('jornada_id', $jornada_id)
                ->where('status_urc', 1)
                ->findAll();
            $idsAsignados = array_column($asignados, 'id_usuario');

            foreach ($usuarios as &$u) {
                $u['ya_asignado'] = in_array($u['id_usuario'], $idsAsignados);
            }
        }

        return $this->response->setJSON($usuarios);
    }

    /**
     * Asignar usuario a jornada con un rol (POST AJAX)
     */
    public function asignar($jornada_id)
    {
        $rolSesion = session('id_rol');

        // Solo roles 1,2,3,4 pueden asignar
        if (!in_array($rolSesion, [1, 2, 3, 4])) {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $id_usuario = $this->request->getPost('id_usuario');
        $id_rol     = $this->request->getPost('id_rol');

        // Validar que el rol asignado sea 4,5,6 o 7
        if (!in_array((int)$id_rol, [4, 5, 6, 7])) {
            return $this->response->setJSON(['error' => 'Rol no válido para jornada']);
        }

        $rucModel = new RolesUsuariosContextoModel();

        // Verificar si ya está asignado (activo) en esta jornada
        $existe = $rucModel
            ->where('id_usuario', $id_usuario)
            ->where('jornada_id', $jornada_id)
            ->where('tipo_contexto', 'JORNADA')
            ->where('status_urc', 1)
            ->first();

        if ($existe) {
            return $this->response->setJSON(['error' => 'El usuario ya está asignado a esta jornada']);
        }

        // Verificar si existía pero fue desactivado → reactivar
        $inactivo = $rucModel
            ->where('id_usuario', $id_usuario)
            ->where('jornada_id', $jornada_id)
            ->where('tipo_contexto', 'JORNADA')
            ->where('status_urc', 0)
            ->first();

        if ($inactivo) {
            $rucModel->update($inactivo['id_ruc'], [
                'id_rol'          => $id_rol,
                'status_urc'      => 1,
                'fecha_asignacion' => date('Y-m-d H:i:s'),
            ]);
        } else {
            // Obtener la organización de la jornada
            $jornadaModel = new JornadaModel();
            $jornada = $jornadaModel->find($jornada_id);

            $rucModel->insert([
                'id_usuario'       => $id_usuario,
                'id_rol'           => $id_rol,
                'organizacion_id'  => $jornada['organizacion_id'] ?? null,
                'jornada_id'       => $jornada_id,
                'centro_id'        => null,
                'tipo_contexto'    => 'JORNADA',
                'fecha_asignacion' => date('Y-m-d H:i:s'),
                'status_urc'       => 1,
            ]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Eliminar (desactivar) usuario de una jornada (POST AJAX)
     */
    public function eliminar($jornada_id)
    {
        $rolSesion = session('id_rol');

        // Solo roles 1,2,3,4 pueden eliminar
        if (!in_array($rolSesion, [1, 2, 3, 4])) {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $id_ruc = $this->request->getPost('id_ruc');

        $rucModel = new RolesUsuariosContextoModel();
        $registro = $rucModel->find($id_ruc);

        if (!$registro || $registro['jornada_id'] != $jornada_id) {
            return $this->response->setJSON(['error' => 'Registro no encontrado']);
        }

        $rucModel->update($id_ruc, ['status_urc' => 0]);

        return $this->response->setJSON(['success' => true]);
    }

    /**
     * Listar usuarios asignados a una jornada (AJAX)
     */
    public function listarAsignados($jornada_id)
    {
        $rucModel = new RolesUsuariosContextoModel();

        $usuarios = $rucModel
    ->select('
        roles_usuarios_contexto.id_ruc,
        roles_usuarios_contexto.id_rol,
        roles_usuarios_contexto.organizacion_id AS organizacion_contexto_id,
        roles_usuarios_contexto.fecha_asignacion,
        usuarios.id_usuario,
        usuarios.nombres,
        usuarios.apellidos,
        usuarios.profesion,
        usuarios.organizacion_id AS organizacion_principal_id,
        org_principal.nombre_org AS nombre_org_principal,
        org_contexto.nombre_org AS nombre_org_contexto,
        roles.nombre_rol
    ')
    ->join('usuarios', 'usuarios.id_usuario = roles_usuarios_contexto.id_usuario')
    ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol')
    ->join('organizacion AS org_principal', 'org_principal.id_organizacion = usuarios.organizacion_id', 'left')
    ->join('organizacion AS org_contexto', 'org_contexto.id_organizacion = roles_usuarios_contexto.organizacion_id', 'left')
    ->where('roles_usuarios_contexto.jornada_id', $jornada_id)
    ->where('roles_usuarios_contexto.tipo_contexto', 'JORNADA')
    ->where('roles_usuarios_contexto.status_urc', 1)
    ->findAll();

        return $this->response->setJSON(['data' => $usuarios]);
    }
}
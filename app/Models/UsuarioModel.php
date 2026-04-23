<?php
namespace App\Models;
use CodeIgniter\Model;

class UsuarioModel extends Model
{
    protected $table            = 'usuarios';
    protected $primaryKey       = 'id_usuario';
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    
    protected $allowedFields    = [
        'nombres', 'apellidos', 'genero', 'fecha_nacimiento', 
        'email', 'username', 'password_hash',
        'telefono', 'direccion_id', 'organizacion_id', 
        'status_usu', 'profesion',
        'creado_en', 'creado_por',
        'foto_url'
    ];

    public function buscarPorEmailOUsername(string $identity)
    {
        return $this->groupStart()
                        ->where('email', $identity)
                        ->orWhere('username', $identity)
                    ->groupEnd()
                    ->first();
    }

    public function usuariosPorOrganizacion($organizacionId)
    {
        return $this->where('organizacion_id', $organizacionId)->findAll();
    }

    public function usuariosConOrganizacion()
    {
        return $this->select("
                usuarios.*, 
                organizacion.nombre_org AS nombre_organizacion, 
                roles.nombre_rol AS nombre_rol
            ")
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->join('roles_usuarios_contexto', 'roles_usuarios_contexto.id_usuario = usuarios.id_usuario', 'left')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol', 'left')
            ->where('usuarios.status_usu', 1)
            ->groupBy('usuarios.id_usuario')
            ->findAll();
    }

    public function usuariosConOrganizacionFiltrado($orgId)
    {
        return $this->select('usuarios.*, organizacion.nombre_org AS nombre_organizacion')
                    ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
                    ->where('usuarios.organizacion_id', $orgId)
                    ->findAll();
    }

    public function buscarUsuariosPorOrg($texto, $orgId)
    {
        return $this->select('usuarios.*, organizacion.nombre_org AS nombre_organizacion')
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->where('usuarios.organizacion_id', $orgId)
            ->groupStart()
                ->like('usuarios.nombres', $texto)
                ->orLike('usuarios.apellidos', $texto)
                ->orLike('usuarios.email', $texto)
                ->orLike('organizacion.nombre_org', $texto)
            ->groupEnd()
            ->findAll();
    }

    public function buscarUsuarios($texto)
    {
        return $this->select('usuarios.*, organizacion.nombre_org AS nombre_organizacion')
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->groupStart()
                ->like('usuarios.nombres', $texto)
                ->orLike('usuarios.apellidos', $texto)
                ->orLike('usuarios.email', $texto)
                ->orLike('organizacion.nombre_org', $texto)
            ->groupEnd()
            ->findAll();
    }

    public function usuariosIndependientesYOtros($orgId)
    {
        return $this->select("
                usuarios.*, 
                organizacion.nombre_org AS nombre_organizacion, 
                roles.nombre_rol AS nombre_rol
            ")
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->join('roles_usuarios_contexto', 'roles_usuarios_contexto.id_usuario = usuarios.id_usuario', 'left')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol', 'left') 
            ->whereIn('usuarios.organizacion_id', [1, $orgId])
            ->groupBy('usuarios.id_usuario')
            ->findAll();
    }

    // PERFIL COMPLETO
    public function obtenerPerfil($id_usuario)
    {
        return $this->select("
                usuarios.*, 
                organizacion.nombre_org AS nombre_org,
                organizacion.tipo,
                organizacion.categoria,
                organizacion.telefono AS telefono_org,
                organizacion.correo AS correo_org
            ")
            ->join('organizacion', 'organizacion.id_organizacion = usuarios.organizacion_id', 'left')
            ->where('usuarios.id_usuario', $id_usuario)
            ->first();
    }
} //  llave clase
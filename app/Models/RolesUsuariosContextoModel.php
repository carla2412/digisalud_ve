<?php

namespace App\Models;

use CodeIgniter\Model;

class RolesUsuariosContextoModel extends Model
{
    protected $table = 'roles_usuarios_contexto';
    protected $primaryKey = 'id_ruc';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_usuario',
        'id_rol',
        'organizacion_id',
        'jornada_id',
        'centro_id',
        'tipo_contexto',
        'fecha_asignacion',
        'status_urc'
    ];
    // JORNADAS PARTICIPADAS
    public function obtenerJornadasUsuario($id)
    {
        return $this->select("
                    jornadas.nombre_jornada,
                    roles.nombre_rol,
                    fecha_asignacion
                ")
            ->join('jornadas', 'jornadas.id_jornada = roles_usuarios_contexto.jornada_id', 'left')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol', 'left')
            ->where('id_usuario', $id)
            ->where('jornada_id IS NOT NULL')
            ->orderBy('fecha_asignacion', 'DESC')
            ->findAll();
    }

    // CENTROS PARTICIPADOS
    public function obtenerCentrosUsuario($id)
    {
        return $this->select("
                    centros.nombre_centro,
                    roles.nombre_rol,
                    fecha_asignacion
                ")
            ->join('centros', 'centros.id_centro = roles_usuarios_contexto.centro_id', 'left')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol', 'left')
            ->where('id_usuario', $id)
            ->where('centro_id IS NOT NULL')
            ->orderBy('fecha_asignacion', 'DESC')
            ->findAll();
    }

    // CONTADORES
    public function contarJornadas($id)
    {
        return $this->where('id_usuario', $id)
            ->where('jornada_id IS NOT NULL')
            ->countAllResults();
    }

    public function contarCentros($id)
    {
        return $this->where('id_usuario', $id)
            ->where('centro_id IS NOT NULL')
            ->countAllResults();
    }
}// llave clase

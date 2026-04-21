<?php
// ========================================================
// ARCHIVO: app/Models/EscolaridadModel.php
// REEMPLAZAR COMPLETO
// ========================================================

namespace App\Models;

use CodeIgniter\Model;

class EscolaridadModel extends Model
{
    protected $table      = 'escolaridad';
    protected $primaryKey = 'escolaridad_id';
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_beneficiario',
        'nombre_escuela',
        'grado',
        'seccion',
        'turno',
        'status_esc',      // 1=activo (año actual), 0=histórico
        'creado_en',
        'creado_por',
        'modificado_en',
        'modificado_por',
    ];

    /**
     * Obtener escolaridad activa de un beneficiario
     */
    public function getActiva($id_beneficiario)
    {
        return $this->where('id_beneficiario', $id_beneficiario)
                    ->where('status_esc', 1)
                    ->first();
    }

    /**
     * Obtener historial escolar completo de un beneficiario
     */
    public function getHistorial($id_beneficiario)
    {
        return $this->where('id_beneficiario', $id_beneficiario)
                    ->orderBy('creado_en', 'DESC')
                    ->findAll();
    }

    /**
     * Cambio de año/grado: marca el registro actual como histórico
     * y crea uno nuevo con los datos actualizados
     */
    public function cambiarAnio($id_beneficiario, $nuevosData, $usuario_id = null)
    {
        // 1) Marcar el registro actual como histórico
        $this->where('id_beneficiario', $id_beneficiario)
             ->where('status_esc', 1)
             ->set([
                 'status_esc'    => 0,
                 'modificado_en' => date('Y-m-d H:i:s'),
                 'modificado_por'=> $usuario_id,
             ])
             ->update();

        // 2) Crear nuevo registro activo
        return $this->insert([
            'id_beneficiario' => $id_beneficiario,
            'nombre_escuela'  => $nuevosData['nombre_escuela'] ?? null,
            'grado'           => $nuevosData['grado'] ?? null,
            'seccion'         => $nuevosData['seccion'] ?? null,
            'turno'           => $nuevosData['turno'] ?? null,
            'status_esc'      => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => $usuario_id,
        ]);
    }
}
<?php
/**
 * =====================================================
 * ARCHIVO: app/Models/JornadaModel.php
 * REEMPLAZAR COMPLETO
 * =====================================================
 * FIX: getJornadaConDireccion() ahora incluye municipio, parroquia, detalle
 */

namespace App\Models;

use CodeIgniter\Model;

class JornadaModel extends Model
{
    protected $table            = 'jornadas';
    protected $primaryKey       = 'id_jornada';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'nombre_jornada',
        'fecha_inicio',
        'institucion_id',
        'organizacion_id',
        'status_jor',
        'creado_en',
        'creado_por',
        'modificado_en',
        'modificado_por',
    ];

    /**
     * Obtener jornada con datos de institución y dirección completa
     * FIX: Ahora incluye municipio, parroquia y detalle
     */
    public function getJornadaConDireccion($id_jornada)
    {
        return $this
            ->select('jornadas.*, 
                      instituciones.nombre_institucion, 
                      instituciones.tipo AS tipo_jornada,
                      dir.id_direccion, 
                      dir.pais, 
                      dir.estado, 
                      dir.municipio, 
                      dir.parroquia, 
                      dir.ciudad, 
                      dir.detalle, 
                      dir.coordenadas,
                      organizaciones.nombre_org')
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
            ->join('organizacion AS organizaciones', 'organizaciones.id_organizacion = jornadas.organizacion_id', 'left')
            ->where('jornadas.id_jornada', $id_jornada)
            ->first();
    }

    /**
     * Obtener IDs de pesquisas vinculadas a una jornada
     */
    public function getPesquisasPorJornada($id_jornada)
    {
        $db = \Config\Database::connect();
        $result = $db->table('tipo_pesquisa_actividad')
            ->select('idtipo_pesquisa')
            ->where('id_jornada', $id_jornada)
            ->where('status_pesq_act', 1)
            ->get()
            ->getResultArray();

        return array_column($result, 'idtipo_pesquisa');
    }
}
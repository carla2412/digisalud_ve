<?php
/**
 * =====================================================
 * ARCHIVO: app/Models/JornadaModel.php
 * REEMPLAZAR COMPLETO
 * =====================================================
 */

namespace App\Models;

use CodeIgniter\Model;

class JornadaModel extends Model
{
    protected $table            = 'jornadas';
    protected $primaryKey       = 'id_jornada';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'nombre_jornada',
        'fecha_inicio',
        'institucion_id',
        'organizacion_id',
        'status_jor',
        'creado_en',
        'creado_por',
        'modificado_en',
        'modificado_por'
    ];

    /**
     * Retorna un array simple con los IDs de las pesquisas
     * vinculadas a una jornada.
     * Ejemplo: [1, 2, 6]
     */
    public function getPesquisasPorJornada(int $id_jornada): array
    {
        $db = \Config\Database::connect();

        $rows = $db->table('tipo_pesquisa_actividad')
                   ->select('idtipo_pesquisa')
                   ->where('id_jornada', $id_jornada)
                   ->where('status_pesq_act', 1)
                   ->get()
                   ->getResultArray();

        return array_column($rows, 'idtipo_pesquisa');
    }

    /**
     * Retorna la jornada con datos de institución y dirección
     * para precargar el formulario de edición.
     */
    public function getJornadaConDireccion(int $id_jornada): ?array
    {
        return $this->select("jornadas.*, 
                    instituciones.nombre_institucion, 
                    instituciones.tipo AS tipo_jornada,
                    dir.id_direccion, dir.pais, dir.estado, dir.ciudad, dir.coordenadas")
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
            ->where('jornadas.id_jornada', $id_jornada)
            ->first();
    }
}
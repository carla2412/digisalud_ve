<?php
/**
 * =====================================================
 * ARCHIVO: app/Models/JornadaModel.php
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
     * vinculadas a una jornada a través de tipo_pesquisa_actividad.
     *
     * Ejemplo de retorno: [1, 2, 6]
     *
     * @param int $id_jornada
     * @return array
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

        // Retornar solo un array plano de IDs: [1, 2, 6]
        return array_column($rows, 'idtipo_pesquisa');
    }
}
<?php
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
}

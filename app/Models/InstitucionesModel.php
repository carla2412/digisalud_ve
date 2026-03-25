<?php
namespace App\Models;

use CodeIgniter\Model;

class InstitucionesModel extends Model
{
    protected $table            = 'instituciones';
    protected $primaryKey       = 'id_institucion';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true; // tu tabla NO es auto incremental

    protected $allowedFields = [
        'id_institucion',
        'nombre_institucion',
        'tipo',
        'direccion_id'
    ];
}

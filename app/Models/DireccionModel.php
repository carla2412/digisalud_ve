<?php
namespace App\Models;
use CodeIgniter\Model;

class DireccionModel extends Model
{
    protected $table = 'direcciones';
    protected $primaryKey = 'id_direccion';
    protected $returnType = 'array';
    protected $allowedFields = [
        'pais', 'estado', 'municipio', 'parroquia', 'ciudad', 'detalle', 'coordenadas'
    ];
}

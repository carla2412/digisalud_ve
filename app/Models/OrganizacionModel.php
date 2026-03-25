<?php
namespace App\Models;
use CodeIgniter\Model;

class OrganizacionModel extends Model
{
    protected $table = 'organizacion';
    protected $primaryKey = 'id_organizacion';
    protected $returnType = 'array';
      protected $allowedFields = [
        'nombre_org',
        'tipo',
        'categoria',      
        'descripcion',
        'telefono',
        'correo',
        'nombre_responsable',
        'direccion_id',
        'status_org',
        'creado_por'
    ];
}

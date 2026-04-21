<?php
namespace App\Models;
use CodeIgniter\Model;

class FamiliaresModel extends Model
{
    protected $table      = 'familiares';
    protected $primaryKey = 'id_familiar';
    protected $returnType = 'array';
    protected $allowedFields = [
        'beneficiario_id',
        'beneficiario_id_representante',
        'relacion',
        'telefono',
    ];
}
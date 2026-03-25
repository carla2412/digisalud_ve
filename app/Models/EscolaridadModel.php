<?php
namespace App\Models;
use CodeIgniter\Model;

class EscolaridadModel extends Model
{
    protected $table='escolaridad';

    protected $allowedFields=[
        'id_beneficiario',
        'nombre_escuela',
        'grado',
        'seccion',
        'turno'
    ];
}
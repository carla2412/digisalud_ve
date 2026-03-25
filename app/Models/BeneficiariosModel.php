<?php

namespace App\Models;

use CodeIgniter\Model;

class BeneficiariosModel extends Model
{
    protected $table = 'beneficiarios';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'pais_nacimiento',
        'telefono',
        'correo',
        'id_digisalud'
    ];

    protected $validationRules = [
        'nombres' => 'required|min_length[2]',
        'apellidos' => 'required|min_length[2]',
        'fecha_nacimiento' => 'required|valid_date',
        'sexo' => 'required',
        'pais_nacimiento' => 'required'
    ];
}
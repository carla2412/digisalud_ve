<?php

namespace App\Models;

use CodeIgniter\Model;

class JornadaBeneficiariosModel extends Model
{
    protected $table = 'beneficiarios_jornadas';

    protected $primaryKey = 'id_benef_jor';

    protected $allowedFields = [
        'id_beneficiario',
        'jornada_id',
        'status_bc',
        'creado_en',
        'creado_por'
    ];
}
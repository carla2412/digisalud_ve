<?php
namespace App\Models;
use CodeIgniter\Model;

class AntecedentesBeneficiariosModel extends Model
{
    protected $table      = 'antecedentes_beneficiarios';
    protected $primaryKey = 'id_ant_benef';
    protected $returnType = 'array';
    protected $allowedFields = [
        'id_beneficiario', 'id_antecedente', 'jornada_id',
        'valor', 'observacion', 'creado_en', 'creado_por',
    ];
}
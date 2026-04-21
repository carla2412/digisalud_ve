<?php
// ========================================================
// ARCHIVO: app/Models/BeneficiariosModel.php
// REEMPLAZAR COMPLETO — corrige primaryKey y allowedFields
// ========================================================

namespace App\Models;

use CodeIgniter\Model;

class BeneficiariosModel extends Model
{
    protected $table      = 'beneficiarios';
    protected $primaryKey = 'id_beneficiario'; // ← CORREGIDO: era 'id'
    protected $returnType = 'array';

    protected $allowedFields = [
        'id_digisalud',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'sexo',
        'pais_nacimiento',
        'telefono',
        'correo',
        'direccion_id',     // ← FALTABA: FK a direcciones
        'creado_en',        // ← FALTABA
        'creado_por',       // ← FALTABA
        'modificado_en',
        'modificado_por',
    ];

    protected $validationRules = [
        'nombres'          => 'required|min_length[2]',
        'apellidos'        => 'required|min_length[2]',
        'fecha_nacimiento' => 'required|valid_date',
        'sexo'             => 'required',
        'pais_nacimiento'  => 'required',
    ];
}
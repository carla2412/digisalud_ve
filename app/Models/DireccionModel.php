<?php
/**
 * =====================================================
 * ARCHIVO: app/Models/DireccionModel.php
 * REEMPLAZAR COMPLETO
 * =====================================================
 * FIX: allowedFields incluye 'detalle'
 */

namespace App\Models;

use CodeIgniter\Model;

class DireccionModel extends Model
{
    protected $table            = 'direcciones';
    protected $primaryKey       = 'id_direccion';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $useTimestamps    = false;

    protected $allowedFields = [
        'pais',
        'estado',
        'municipio',
        'parroquia',
        'ciudad',
        'detalle',
        'coordenadas',
    ];
}
<?php
namespace App\Models;

use CodeIgniter\Model;

class InstitucionActividadModel extends Model
{
    protected $table            = 'institucion_actividad';
    protected $primaryKey       = 'id_inst_act';
    protected $returnType       = 'array';
  
     protected $allowedFields = ['id_inst_act','id_institucion','id_jornada','id_centro','status_act'];
}

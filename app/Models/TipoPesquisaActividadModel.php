<?php
namespace App\Models;

use CodeIgniter\Model;
 

class TipoPesquisaActividadModel extends Model {
    protected $table = 'tipo_pesquisa_actividad';
    protected $primaryKey = 'id_pesq_act';
    protected $allowedFields = ['idtipo_pesquisa','id_jornada','id_centro','status_pesq_act'];
}

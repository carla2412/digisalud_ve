<?php
namespace App\Models;

use CodeIgniter\Model;

class TipoPesquisaModel extends Model
{
    protected $table = 'tipo_pesquisa';
    protected $primaryKey = 'idtipo_pesquisa';
    protected $returnType = 'array';

    protected $allowedFields = ['nombre_tipo','descripcion_view'];
 

   
}

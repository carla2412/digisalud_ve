<?php
class CentrosUsuariosModel extends Model
{
    protected $table = 'centros_usuarios'; // CAMBIAR CUANDO TENGAS LA TABLA CORRECTA
    protected $primaryKey = 'id';
    protected $allowedFields = ['id_usuario', 'id_centro'];

    public function contarCentros($id_usuario)
    {
        return $this->where('id_usuario', $id_usuario)->countAllResults();
    }
}

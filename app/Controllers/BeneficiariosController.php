<?php

namespace App\Controllers;

use App\Models\BeneficiariosModel;
use App\Models\DireccionModel;
use App\Models\EscolaridadModel;
use App\Models\JornadaBeneficiariosModel;

class BeneficiariosController extends BaseController
{

    public function buscar()
    {
        return view('beneficiarios/buscar');
    }

    public function buscarAjax()
    {
        $model = new BeneficiariosModel();

        $term = $this->request->getGet('q');

        $data = $model
            ->like('numero_documento', $term)
            ->orLike('nombres', $term)
            ->orLike('apellidos', $term)
            ->orLike('id_digisalud', $term)
            ->findAll();

        return $this->response->setJSON($data);
    }

    public function create($jornada_id)
    {
        return view('beneficiarios/create',[
            'jornada_id'=>$jornada_id
        ]);
    }

    public function store($jornada_id)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel = new DireccionModel();
        $escModel = new EscolaridadModel();
        $jorModel = new JornadaBeneficiariosModel();

        $data = $this->request->getPost();

        $data['id_digisalud'] = $this->generarID($data);

        $id_beneficiario = $benefModel->insert($data);

        if($this->request->getPost('direccion_activa')){

            $dirModel->insert([
                'id_beneficiario'=>$id_beneficiario,
                'pais'=>$data['pais'],
                'estado'=>$data['estado'],
                'municipio'=>$data['municipio'],
                'parroquia'=>$data['parroquia'],
                'ciudad'=>$data['ciudad']
            ]);
        }

        if($this->request->getPost('escolaridad_activa')){

            $escModel->insert([
                'id_beneficiario'=>$id_beneficiario,
                'nombre_escuela'=>$data['nombre_escuela'],
                'grado'=>$data['grado'],
                'seccion'=>$data['seccion'],
                'turno'=>$data['turno']
            ]);
        }

        $jorModel->insert([
            'id_beneficiario'=>$id_beneficiario,
            'jornada_id'=>$jornada_id,
            'status_bc'=>1,
            'creado_en'=>date('Y-m-d H:i:s'),
            'creado_por'=>1
        ]);

        return redirect()->to("/jornadas/$jornada_id/beneficiarios");
    }

    private function generarID($data)
    {

        $pais = strtoupper(substr($data['pais_nacimiento'],0,2));
        $sexo = strtoupper($data['sexo']);

        $nombres = explode(" ",$data['nombres']);
        $apellidos = explode(" ",$data['apellidos']);

        $p1 = strtoupper(substr($nombres[0],0,3));
        $p2 = isset($nombres[1]) ? strtoupper(substr($nombres[1],0,1)) : "";

        $a1 = strtoupper(substr($apellidos[0],0,3));
        $a2 = isset($apellidos[1]) ? strtoupper(substr($apellidos[1],0,1)) : "";

        $fecha = str_replace("-","",$data['fecha_nacimiento']);

        return $pais.$sexo.$p1.$p2.$a1.$a2.$fecha;
    }
}
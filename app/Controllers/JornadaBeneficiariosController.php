<?php

namespace App\Controllers;

use App\Models\JornadaBeneficiariosModel;
use App\Models\BeneficiariosModel;

class JornadaBeneficiariosController extends BaseController
{

    public function index($jornada_id)
    {
        $model = new JornadaBeneficiariosModel();

        $data['beneficiarios'] = $model
            ->select('beneficiarios.*')
            ->join('beneficiarios','beneficiarios.id_beneficiario = beneficiarios_jornadas.id_beneficiario')
            ->where('jornada_id',$jornada_id)
            ->findAll();

        $data['jornada_id'] = $jornada_id;

        return view('jornadas/beneficiarios',$data);
    }

    public function asociar($jornada,$beneficiario)
    {

        $model = new JornadaBeneficiariosModel();

        $model->insert([
            'id_beneficiario'=>$beneficiario,
            'jornada_id'=>$jornada,
            'status_bc'=>1,
            'creado_en'=>date('Y-m-d H:i:s'),
            'creado_por'=>1
        ]);

        return redirect()->back();
    }

}
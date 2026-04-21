<?php
// ========================================================
// ARCHIVO: app/Controllers/JornadaBeneficiariosController.php
// REEMPLAZAR COMPLETO
// ========================================================

namespace App\Controllers;

use App\Models\JornadaBeneficiariosModel;
use App\Models\BeneficiariosModel;
use App\Models\JornadaModel;
use App\Models\FamiliaresModel;

class JornadaBeneficiariosController extends BaseController
{
    public function index($jornada_id)
    {
        $jorBenefModel = new JornadaBeneficiariosModel();
        $jornadaModel  = new JornadaModel();

        // Datos de la jornada con pesquisas asociadas
        $jornada = $jornadaModel
            ->select("jornadas.*, instituciones.nombre_institucion, dir.ciudad,
                      GROUP_CONCAT(DISTINCT tpa.idtipo_pesquisa ORDER BY tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->where('jornadas.id_jornada', $jornada_id)
            ->groupBy('jornadas.id_jornada')
            ->first();

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada');
        }

        // Pesquisas activas de esta jornada
        $pesquisas_jornada = [];
        if (!empty($jornada['pesquisas'])) {
            $pesquisas_jornada = array_map('trim', explode(',', $jornada['pesquisas']));
        }

        // Beneficiarios con representante
        $beneficiarios = $jorBenefModel
            ->select('beneficiarios.*, beneficiarios_jornadas.id_benef_jor, beneficiarios_jornadas.status_bc,
                      fam.relacion AS rep_relacion,
                      rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos')
            ->join('beneficiarios', 'beneficiarios.id_beneficiario = beneficiarios_jornadas.id_beneficiario')
            ->join('familiares AS fam', 'fam.beneficiario_id = beneficiarios.id_beneficiario', 'left')
            ->join('beneficiarios AS rep', 'rep.id_beneficiario = fam.beneficiario_id_representante', 'left')
            ->where('beneficiarios_jornadas.jornada_id', $jornada_id)
            ->where('beneficiarios_jornadas.status_bc', 1)
            ->findAll();

        // ═══ CONSULTAR EVALUACIONES POR BENEFICIARIO ═══
        // Esto determinará qué íconos van en color y cuáles en gris
        // TODO: cuando desarrolles el módulo de evaluaciones, aquí consultarás
        //       la tabla de resultados de pesquisa por beneficiario.
        //       Por ahora dejamos un array vacío — todos los íconos serán gris.
        //
        // Estructura esperada: $evaluaciones[id_beneficiario] = ['1','3','6'] (IDs de pesquisas evaluadas)
        //
        // Ejemplo futuro:
        // $db = \Config\Database::connect();
        // $evals = $db->table('evaluaciones_pesquisa')
        //     ->select('id_beneficiario, idtipo_pesquisa')
        //     ->where('jornada_id', $jornada_id)
        //     ->get()->getResultArray();
        // foreach ($evals as $e) {
        //     $evaluaciones[$e['id_beneficiario']][] = $e['idtipo_pesquisa'];
        // }

        $evaluaciones = []; // vacío por ahora

        return view('jornadas/beneficiarios', [
            'beneficiarios'     => $beneficiarios,
            'jornada'           => $jornada,
            'jornada_id'        => $jornada_id,
            'total'             => count($beneficiarios),
            'pesquisas_jornada' => $pesquisas_jornada,
            'evaluaciones'      => $evaluaciones,
        ]);
    }

    public function asociar($jornada_id, $beneficiario_id)
    {
        $model = new JornadaBeneficiariosModel();

        $existe = $model->where('id_beneficiario', $beneficiario_id)
                        ->where('jornada_id', $jornada_id)->first();
        if ($existe) {
            return redirect()->back()->with('error', 'El beneficiario ya está asociado a esta jornada');
        }

        $model->insert([
            'id_beneficiario' => $beneficiario_id,
            'jornada_id'      => $jornada_id,
            'status_bc'       => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => session('id_usuario') ?? 1
        ]);

        return redirect()->to("/jornadas/$jornada_id/beneficiarios")
                         ->with('success', 'Beneficiario asociado correctamente');
    }

    public function desasociar($jornada_id, $beneficiario_id)
    {
        $model = new JornadaBeneficiariosModel();
        $model->where('id_beneficiario', $beneficiario_id)
              ->where('jornada_id', $jornada_id)
              ->set(['status_bc' => 0])->update();

        return redirect()->to("/jornadas/$jornada_id/beneficiarios")
                         ->with('success', 'Beneficiario removido de la jornada');
    }
}
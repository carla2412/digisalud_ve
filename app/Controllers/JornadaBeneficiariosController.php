<?php
// ========================================================
// ARCHIVO: app/Controllers/JornadaBeneficiariosController.php
// ========================================================

namespace App\Controllers;

use App\Models\JornadaBeneficiariosModel;
use App\Models\BeneficiariosModel;
use App\Models\JornadaModel;
use App\Models\FamiliaresModel;
use App\Models\PesquisaEvaluacionModel;

class JornadaBeneficiariosController extends BaseController
{
    public function index($jornada_id)
    {
        $jorBenefModel = new JornadaBeneficiariosModel();
        $jornadaModel  = new JornadaModel();

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

        $pesquisas_jornada = [];
        if (!empty($jornada['pesquisas'])) {
            $pesquisas_jornada = array_map('trim', explode(',', $jornada['pesquisas']));
        }

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

        if (count($beneficiarios) === 0) {
            return redirect()->to(base_url("jornadas/$jornada_id/beneficiarios/buscar"))
                ->with('info', 'Esta jornada aún no tiene beneficiarios. Use el buscador para añadir uno.');
        }

        // ─── Obtener evaluaciones realizadas por beneficiario en esta jornada ───
        $evaluaciones = [];

        $evalRows = db_connect()
            ->table('pesquisa_evaluaciones')
            ->select('beneficiario_id, tipo_pesquisa_id')
            ->where('jornada_id', $jornada_id)
            ->where('status_eval', 1)
            ->get()
            ->getResultArray();

        foreach ($evalRows as $row) {
            $bid = $row['beneficiario_id'];
            if (!isset($evaluaciones[$bid])) {
                $evaluaciones[$bid] = [];
            }
            $evaluaciones[$bid][] = $row['tipo_pesquisa_id'];
        }

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
        $evalModel = new PesquisaEvaluacionModel();

        $tieneEvaluaciones = $evalModel
            ->where('beneficiario_id', (int) $beneficiario_id)
            ->where('jornada_id', (int) $jornada_id)
            ->where('status_eval', 1)
            ->countAllResults();

        if ($tieneEvaluaciones > 0) {
            return redirect()->to("/jornadas/$jornada_id/beneficiarios")
                ->with('error', 'No se puede retirar el beneficiario de la jornada porque ya tiene evaluaciones registradas.');
        }

        $model = new JornadaBeneficiariosModel();
        $model->where('id_beneficiario', $beneficiario_id)
            ->where('jornada_id', $jornada_id)
            ->set(['status_bc' => 0])
            ->update();

        return redirect()->to("/jornadas/$jornada_id/beneficiarios")
            ->with('success', 'Beneficiario removido de la jornada');
    }


    public function buscar($jornada_id)
    {
        $jorBenefModel = new JornadaBeneficiariosModel();
        $jornadaModel  = new JornadaModel();

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

        $pesquisas_jornada = [];

        if (!empty($jornada['pesquisas'])) {
            $pesquisas_jornada = array_map('trim', explode(',', $jornada['pesquisas']));
        }

        $beneficiariosAsignados = $jorBenefModel
            ->select('beneficiarios.*, beneficiarios_jornadas.id_benef_jor, beneficiarios_jornadas.status_bc')
            ->join('beneficiarios', 'beneficiarios.id_beneficiario = beneficiarios_jornadas.id_beneficiario')
            ->where('beneficiarios_jornadas.jornada_id', $jornada_id)
            ->where('beneficiarios_jornadas.status_bc', 1)
            ->findAll();

        return view('beneficiarios/buscar', [
            'jornada_id'                  => $jornada_id,
            'jornada'                     => $jornada,
            'beneficiariosAsignados'      => $beneficiariosAsignados,
            'totalBeneficiariosAsignados' => count($beneficiariosAsignados),
            'pesquisas_jornada'           => $pesquisas_jornada,
        ]);
    }

    public function fichaRapida($jornada_id, $beneficiario_id)
    {
        $db = db_connect();

        $beneficiario = $db->table('beneficiarios')
            ->select("
            beneficiarios.*,
            beneficiarios_jornadas.id_benef_jor,
            beneficiarios_jornadas.status_bc,
            fam.relacion AS rep_relacion,
            rep.nombres AS rep_nombres,
            rep.apellidos AS rep_apellidos
        ")
            ->join(
                'beneficiarios_jornadas',
                'beneficiarios_jornadas.id_beneficiario = beneficiarios.id_beneficiario'
            )
            ->join(
                'familiares AS fam',
                'fam.beneficiario_id = beneficiarios.id_beneficiario',
                'left'
            )
            ->join(
                'beneficiarios AS rep',
                'rep.id_beneficiario = fam.beneficiario_id_representante',
                'left'
            )
            ->where('beneficiarios.id_beneficiario', $beneficiario_id)
            ->where('beneficiarios_jornadas.jornada_id', $jornada_id)
            ->where('beneficiarios_jornadas.status_bc', 1)
            ->get()
            ->getRowArray();

        if (!$beneficiario) {
            return $this->response->setStatusCode(404)->setJSON([
                'ok'      => false,
                'message' => 'Beneficiario no encontrado en esta jornada.',
            ]);
        }

        $jornada = $db->table('jornadas')
            ->select("
            jornadas.*,
            instituciones.nombre_institucion,
            GROUP_CONCAT(
                DISTINCT tpa.idtipo_pesquisa
                ORDER BY tpa.idtipo_pesquisa
                SEPARATOR ','
            ) AS pesquisas
        ")
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->where('jornadas.id_jornada', $jornada_id)
            ->groupBy('jornadas.id_jornada')
            ->get()
            ->getRowArray();

        $pesquisasJornada = [];

        if (!empty($jornada['pesquisas'])) {
            $pesquisasJornada = array_map('trim', explode(',', $jornada['pesquisas']));
        }

        $evaluadasRows = $db->table('pesquisa_evaluaciones')
            ->select('tipo_pesquisa_id')
            ->where('jornada_id', $jornada_id)
            ->where('beneficiario_id', $beneficiario_id)
            ->where('status_eval', 1)
            ->get()
            ->getResultArray();

        $evaluadas = array_map(
            static fn($row) => (string) $row['tipo_pesquisa_id'],
            $evaluadasRows
        );

        $catalogoPesquisas = [
            '1' => [
                'nombre' => 'Antropometría',
                'icono'  => 'antropometria2.svg',
                'desc'   => 'Peso, talla, IMC',
            ],
            '2' => [
                'nombre' => 'Laboratorio',
                'icono'  => 'sanguinea2.svg',
                'desc'   => 'Hemoglobina, glucosa',
            ],
            '3' => [
                'nombre' => 'Visual',
                'icono'  => 'visual2.svg',
                'desc'   => 'Agudeza visual',
            ],
            '4' => [
                'nombre' => 'Signos vitales',
                'icono'  => 'signosVitales2.svg',
                'desc'   => 'Tensión, temperatura, FC',
            ],
            '5' => [
                'nombre' => 'Medicina general',
                'icono'  => 'medicinaGeneral2.svg',
                'desc'   => 'Evaluación clínica',
            ],
            '6' => [
                'nombre' => 'Vacunación',
                'icono'  => 'vacunacion2.svg',
                'desc'   => 'Control de vacunas',
            ],
        ];

        $pesquisas = [];

        foreach ($pesquisasJornada as $pesquisaId) {
            $pesquisaId = (string) $pesquisaId;

            if (!isset($catalogoPesquisas[$pesquisaId])) {
                continue;
            }

            $evaluado = in_array($pesquisaId, $evaluadas, true);

            $pesquisas[] = [
                'id'       => $pesquisaId,
                'nombre'   => $catalogoPesquisas[$pesquisaId]['nombre'],
                'desc'     => $catalogoPesquisas[$pesquisaId]['desc'],
                'icono'    => base_url('img/' . $catalogoPesquisas[$pesquisaId]['icono']),
                'evaluado' => $evaluado,
                'estado'   => $evaluado ? 'Evaluado' : 'Pendiente',
            ];
        }

        $fechaNacimiento = $beneficiario['fecha_nacimiento'] ?? null;
        $edad = '—';

        if (!empty($fechaNacimiento)) {
            $nac  = new \DateTime($fechaNacimiento);
            $diff = (new \DateTime())->diff($nac);

            $edad = $diff->y . ' año' . ($diff->y !== 1 ? 's' : '')
                . ', ' . $diff->m . ' mes(es) y '
                . $diff->d . ' días';
        }

        $totalPesquisas = count($pesquisas);
        $totalEvaluadas = count(array_filter($pesquisas, static fn($p) => $p['evaluado']));
        $totalPendientes = max(0, $totalPesquisas - $totalEvaluadas);

        return $this->response->setJSON([
            'ok' => true,
            'beneficiario' => [
                'id'              => (int) $beneficiario['id_beneficiario'],
                'id_digisalud'    => $beneficiario['id_digisalud'] ?? '—',
                'nombres'         => $beneficiario['nombres'] ?? '',
                'apellidos'       => $beneficiario['apellidos'] ?? '',
                'nombre_completo' => trim(($beneficiario['nombres'] ?? '') . ' ' . ($beneficiario['apellidos'] ?? '')),
                'fecha_nacimiento' => !empty($fechaNacimiento) ? date('d-m-Y', strtotime($fechaNacimiento)) : '—',
                'edad'            => $edad,
                'foto_url'        => !empty($beneficiario['foto_url'])
                    ? base_url($beneficiario['foto_url'])
                    : null,
                'representante'   => !empty($beneficiario['rep_nombres'])
                    ? trim(($beneficiario['rep_nombres'] ?? '') . ' ' . ($beneficiario['rep_apellidos'] ?? ''))
                    : '—',
            ],
            'jornada' => [
                'id'     => (int) $jornada_id,
                'nombre' => $jornada['nombre_jornada'] ?? 'Jornada',
                'fecha'  => !empty($jornada['fecha_inicio'])
                    ? date('d-m-Y', strtotime($jornada['fecha_inicio']))
                    : '—',
            ],
            'resumen' => [
                'total_pesquisas'   => $totalPesquisas,
                'total_evaluadas'   => $totalEvaluadas,
                'total_pendientes'  => $totalPendientes,
                'porcentaje_avance' => $totalPesquisas > 0
                    ? round(($totalEvaluadas / $totalPesquisas) * 100)
                    : 0,
            ],
            'pesquisas' => $pesquisas,
            'acciones' => [
                'editar_url'    => base_url("beneficiarios/editar/{$beneficiario_id}"),
                'historial_url' => base_url("beneficiarios/{$beneficiario_id}/historial"),
            ],
        ]);
    }
}

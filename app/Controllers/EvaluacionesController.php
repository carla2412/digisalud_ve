<?php

namespace App\Controllers;

use App\Models\PesquisaItemModel;
use App\Models\PesquisaEvaluacionModel;
use App\Models\PesquisaResultadoModel;
use CodeIgniter\HTTP\ResponseInterface;

class EvaluacionesController extends BaseController
{
    protected PesquisaItemModel       $itemModel;
    protected PesquisaEvaluacionModel $evalModel;
    protected PesquisaResultadoModel  $resultModel;

    public function __construct()
    {
        $this->itemModel   = new PesquisaItemModel();
        $this->evalModel   = new PesquisaEvaluacionModel();
        $this->resultModel = new PesquisaResultadoModel();
    }

    /**
     * GET /evaluaciones/formulario/{beneficiarioId}/{tipoPesquisaId}?jornada_id=X
     * Página completa del formulario de evaluación.
     */
    public function formulario(int $beneficiarioId, int $tipoPesquisaId)
    {
        $session   = session();
        $jornadaId = (int) $this->request->getGet('jornada_id');
        $centroId  = (int) $this->request->getGet('centro_id');

        // Obtener datos del beneficiario
        $db = \Config\Database::connect();
        $beneficiario = $db->table('beneficiarios')
            ->where('id_beneficiario', $beneficiarioId)
            ->get()->getRowArray();

        if (! $beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado.');
        }

        // Obtener info de la pesquisa
        $tipoPesquisa = $db->table('tipo_pesquisa')
            ->where('idtipo_pesquisa', $tipoPesquisaId)
            ->get()->getRowArray();

        if (! $tipoPesquisa) {
            return redirect()->back()->with('error', 'Tipo de pesquisa no encontrado.');
        }

        if ($jornadaId) {
            $pesquisaHabilitada = $db->table('tipo_pesquisa_actividad')
                ->where('id_jornada', $jornadaId)
                ->where('idtipo_pesquisa', $tipoPesquisaId)
                ->where('status_pesq_act', 1)
                ->countAllResults();

            if (! $pesquisaHabilitada) {
                return redirect()->back()->with('error', 'La pesquisa seleccionada no está habilitada para esta jornada.');
            }
        }

        // Obtener items agrupados por sección
        $itemsAgrupados = $this->itemModel->getItemsAgrupados($tipoPesquisaId);

        // Decodificar opciones_json de cada item
        foreach ($itemsAgrupados as $seccion => &$items) {
            foreach ($items as &$item) {
                if (! empty($item['opciones_json'])) {
                    $item['opciones'] = json_decode($item['opciones_json'], true);
                } else {
                    $item['opciones'] = [];
                }
            }
        }
        unset($items, $item);

        // Pesquisas de la jornada/centro (para sidebar)
        $pesquisasActividad = [];
        if ($jornadaId) {
            $pesquisasActividad = $db->table('tipo_pesquisa_actividad')
                ->select('idtipo_pesquisa')
                ->where('id_jornada', $jornadaId)
                ->where('status_pesq_act', 1)
                ->orderBy('idtipo_pesquisa', 'ASC')
                ->get()->getResultArray();
            $pesquisasActividad = array_column($pesquisasActividad, 'idtipo_pesquisa');
        }

        // Verificar si ya existe evaluación (para edición)
        $evaluacionExistente = null;
        $valoresExistentes   = [];
        if ($jornadaId) {
            $evaluacionExistente = $this->evalModel->existeEnJornada($beneficiarioId, $tipoPesquisaId, $jornadaId);
        }

        if ($evaluacionExistente) {
            $resultados = $this->resultModel->getResultadosConItems($evaluacionExistente['id_evaluacion']);
            foreach ($resultados as $r) {
                switch ($r['tipo_dato']) {
                    case 'number':
                        $valoresExistentes[$r['codigo']] = $r['valor_numero'];
                        break;
                    case 'boolean':
                        $valoresExistentes[$r['codigo']] = $r['valor_booleano'];
                        break;
                    case 'date':
                        $valoresExistentes[$r['codigo']] = $r['valor_fecha'];
                        break;
                    default:
                        $valoresExistentes[$r['codigo']] = $r['valor_texto'];
                        break;
                }
            }
        }

        // Evaluaciones ya realizadas por este beneficiario en esta jornada
        $pesquisasEvaluadas = [];
        if ($jornadaId) {
            $pesquisasEvaluadas = $this->evalModel->getPesquisasEvaluadas($beneficiarioId, $jornadaId);
        }

        // Mapa de nombres de secciones
        $nombresSecciones = $this->getNombresSecciones();

        // Info de pesquisas para sidebar
        $infoPesquisas = [
            1 => ['nombre' => 'Antropometría',   'img' => 'antropometria2.svg',     'gris' => 'antropometria-color.svg'],
            2 => ['nombre' => 'Laboratorio',      'img' => 'sanguinea2.svg',         'gris' => 'sanguinea-color.svg'],
            3 => ['nombre' => 'Visual',           'img' => 'visual2.svg',            'gris' => 'visual-color.svg'],
            4 => ['nombre' => 'Signos vitales',   'img' => 'signosVitales2.svg',     'gris' => 'signos-vitales-color.svg'],
            5 => ['nombre' => 'Medicina general', 'img' => 'medicinaGeneral2.svg',   'gris' => 'medicina-general-color.svg'],
            6 => ['nombre' => 'Vacunación',       'img' => 'vacunacion2.svg',        'gris' => 'vacunacion-color.svg'],
        ];

        return view('evaluaciones/rutas/formulario', [
            'beneficiario'         => $beneficiario,
            'tipoPesquisa'         => $tipoPesquisa,
            'tipoPesquisaId'       => $tipoPesquisaId,
            'jornadaId'            => $jornadaId,
            'centroId'             => $centroId,
            'itemsAgrupados'       => $itemsAgrupados,
            'nombresSecciones'     => $nombresSecciones,
            'evaluacionExistente'  => $evaluacionExistente,
            'valoresExistentes'    => $valoresExistentes,
            'pesquisasActividad'   => $pesquisasActividad,
            'pesquisasEvaluadas'   => $pesquisasEvaluadas,
            'infoPesquisas'        => $infoPesquisas,
        ]);
    }

    /**
     * POST /evaluaciones/guardar
     */
    public function guardar(): ResponseInterface
    {
        $session = session();

        $beneficiarioId = (int) $this->request->getPost('beneficiario_id');
        $tipoPesquisaId = (int) $this->request->getPost('tipo_pesquisa_id');
        $jornadaId      = $this->request->getPost('jornada_id');
        $centroId       = $this->request->getPost('centro_id');
        $evaluacionId   = $this->request->getPost('evaluacion_id');
        $observaciones  = $this->request->getPost('observaciones');
        $campos         = $this->request->getPost('campos') ?? [];

        $usuarioId = $session->get('id_usuario');

        if (! $beneficiarioId || ! $tipoPesquisaId || ! $usuarioId) {
            return $this->response->setJSON([
                'ok'      => false,
                'mensaje' => 'Datos incompletos para guardar la evaluación.',
            ]);
        }

        // Obtener items del catálogo
        $itemsCatalogo = $this->itemModel->getItemsPorPesquisa($tipoPesquisaId);
        $mapaCodigo = [];
        foreach ($itemsCatalogo as $item) {
            $mapaCodigo[$item['codigo']] = $item;
        }

        // Validar campos obligatorios
        foreach ($mapaCodigo as $codigo => $item) {
            if ($item['obligatorio'] && (! isset($campos[$codigo]) || $campos[$codigo] === '')) {
                // Verificar si el campo está oculto por dependencia
                if (! empty($item['depende_de'])) {
                    $valorPadre = $campos[$item['depende_de']] ?? '';
                    if ($valorPadre !== $item['depende_valor']) {
                        continue; // El campo está oculto, no validar
                    }
                }
                return $this->response->setJSON([
                    'ok'      => false,
                    'mensaje' => "El campo '{$item['nombre']}' es obligatorio.",
                    'campo'   => $codigo,
                ]);
            }
        }

        // Validar rangos numéricos
        foreach ($campos as $codigo => $valor) {
            if (! isset($mapaCodigo[$codigo]) || $valor === '' || $valor === null) {
                continue;
            }
            $item = $mapaCodigo[$codigo];
            if ($item['tipo_dato'] === 'number' && is_numeric($valor)) {
                if ($item['valor_min'] !== null && (float) $valor < (float) $item['valor_min']) {
                    return $this->response->setJSON([
                        'ok'      => false,
                        'mensaje' => "{$item['nombre']}: valor mínimo es {$item['valor_min']} {$item['unidad']}.",
                        'campo'   => $codigo,
                    ]);
                }
                if ($item['valor_max'] !== null && (float) $valor > (float) $item['valor_max']) {
                    return $this->response->setJSON([
                        'ok'      => false,
                        'mensaje' => "{$item['nombre']}: valor máximo es {$item['valor_max']} {$item['unidad']}.",
                        'campo'   => $codigo,
                    ]);
                }
            }
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            if (! empty($evaluacionId)) {
                // Editar existente
                $this->evalModel->update($evaluacionId, [
                    'observaciones'  => $observaciones,
                    'modificado_en'  => date('Y-m-d H:i:s'),
                    'modificado_por' => $usuarioId,
                ]);
                $this->resultModel->eliminarPorEvaluacion((int) $evaluacionId);
            } else {
                // Nueva evaluación — verificar unicidad en jornada
                if (! empty($jornadaId)) {
                    $existente = $this->evalModel->existeEnJornada($beneficiarioId, $tipoPesquisaId, (int) $jornadaId);
                    if ($existente) {
                        $evaluacionId = $existente['id_evaluacion'];
                        $this->evalModel->update($evaluacionId, [
                            'observaciones'  => $observaciones,
                            'modificado_en'  => date('Y-m-d H:i:s'),
                            'modificado_por' => $usuarioId,
                        ]);
                        $this->resultModel->eliminarPorEvaluacion((int) $evaluacionId);
                    }
                }

                if (empty($evaluacionId)) {
                    $evaluacionId = $this->evalModel->insert([
                        'beneficiario_id'  => $beneficiarioId,
                        'tipo_pesquisa_id' => $tipoPesquisaId,
                        'jornada_id'       => ! empty($jornadaId) ? (int) $jornadaId : null,
                        'centro_id'        => ! empty($centroId) ? (int) $centroId : null,
                        'fecha_evaluacion' => date('Y-m-d H:i:s'),
                        'observaciones'    => $observaciones,
                        'evaluado_por'     => $usuarioId,
                        'creado_en'        => date('Y-m-d H:i:s'),
                        'status_eval'      => 1,
                    ]);
                }
            }

            // Guardar resultados
            $datosResultados = [];
            foreach ($campos as $codigo => $valor) {
                if (! isset($mapaCodigo[$codigo]) || $valor === '' || $valor === null) {
                    continue;
                }
                $datosResultados[] = [
                    'item_id'   => $mapaCodigo[$codigo]['id_item'],
                    'valor'     => $valor,
                    'tipo_dato' => $mapaCodigo[$codigo]['tipo_dato'],
                ];
            }

            $this->resultModel->guardarLote((int) $evaluacionId, $datosResultados);

            $db->transComplete();

            if ($db->transStatus() === false) {
                return $this->response->setJSON(['ok' => false, 'mensaje' => 'Error al guardar.']);
            }

            // URL de retorno
            $urlRetorno = '';
            if (! empty($jornadaId)) {
                $urlRetorno = base_url("jornadas/{$jornadaId}/beneficiarios");
            } elseif (! empty($centroId)) {
                $urlRetorno = base_url("centros/{$centroId}/beneficiarios");
            }

            return $this->response->setJSON([
                'ok'            => true,
                'mensaje'       => 'Evaluación guardada correctamente.',
                'evaluacion_id' => $evaluacionId,
                'url_retorno'   => $urlRetorno,
            ]);

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error guardando evaluación: ' . $e->getMessage());
            return $this->response->setJSON(['ok' => false, 'mensaje' => 'Error interno al guardar.']);
        }
    }

    /**
     * Mapa de nombres legibles por sección.
     */
    private function getNombresSecciones(): array
    {
        return [
            'mediciones_basicas'  => 'Mediciones básicas',
            'circunferencias'     => 'Circunferencias',
            'pliegues'            => 'Pliegues cutáneos',
            'percentiles'         => 'Percentiles',
            'condiciones'         => 'Condiciones especiales',
            'estimacion_talla'    => 'Estimación de talla',
            'tratamiento'         => 'Tratamiento / Seguimiento',
            'hematologia'         => 'Hematología',
            'quimica_sanguinea'   => 'Química sanguínea',
            'perfil_hepatico'     => 'Perfil hepático',
            'electrolitos'        => 'Electrolitos',
            'coagulacion'         => 'Coagulación',
            'serologia'           => 'Serología',
            'orina'               => 'Examen de orina',
            'heces'               => 'Examen de heces',
            'parasitos'           => 'Parásitos',
            'observaciones_lab'   => 'Observaciones',
            'evaluacion_visual'   => 'Evaluación visual',
            'agudeza'             => 'Agudeza visual',
            'hallazgos'           => 'Hallazgos',
            'optica'              => 'Óptica / Refracción',
            'seguimiento_visual'  => 'Seguimiento',
            'signos'              => 'Signos vitales',
            'seguimiento_vitales' => 'Seguimiento',
            'motivo'              => 'Motivo de consulta',
            'antecedentes'        => 'Antecedentes',
            'habitos'             => 'Hábitos psicobiológicos',
            'sintomas'            => 'Enfermedad actual',
            'eval_sistemas'       => 'Evaluación por sistemas',
            'diagnostico'         => 'Diagnóstico y plan',
            'paraclinicos'        => 'Paraclínicos',
            'especialista'        => 'Especialista',
            'vacunas'             => 'Estado de vacunas',
            'aplicacion'          => 'Aplicación actual',
            'control'             => 'Control',
        ];
    }
}
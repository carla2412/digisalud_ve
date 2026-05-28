<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JornadaModel;
use App\Models\TipoPesquisaActividadModel;

class Reportes extends BaseController
{
    public function home($jornadaId)
{
    $jornadaModel = new JornadaModel();

    $jornada = $jornadaModel->find($jornadaId);

    if (!$jornada) {
        return redirect()
            ->to(site_url('jornadas'))
            ->with('error', 'La jornada seleccionada no existe.');
    }

    /*
     * Primero intenta tomar las pesquisas desde la URL.
     * Esto viene desde jornadas/index.php:
     * ?pesquisas=1,2,3
     */
    $pesquisasParam = $this->request->getGet('pesquisas');

    if (!empty($pesquisasParam)) {
        $pesquisasActivas = array_filter(array_map('trim', explode(',', $pesquisasParam)));
        $pesquisasActivas = array_values(array_unique(array_map('strval', $pesquisasActivas)));
    } else {
        $pesquisasActivas = $this->obtenerPesquisasActivas((int) $jornadaId);
    }

    $reportesDisponibles = $this->obtenerReportesDisponibles((int) $jornadaId, $pesquisasActivas);

    return view('reportes/home_detallados', [
        'jornada' => $jornada,
        'jornadaId' => $jornadaId,
        'pesquisasActivas' => $pesquisasActivas,
        'reportesDisponibles' => $reportesDisponibles,
    ]);
}

    private function obtenerPesquisasActivas(int $jornadaId): array
    {
        $db = db_connect();

        $pesquisasActivas = [];

        /*
         * Primero intenta leer desde la tabla de asociación:
         * tipo_pesquisa_actividad
         */
        if ($db->tableExists('tipo_pesquisa_actividad')) {
            $campos = $db->getFieldNames('tipo_pesquisa_actividad');

            $columnaJornada = $this->primeraColumnaExistente($campos, [
                'id_jornada',
                'jornada_id',
                'id_actividad',
                'actividad_id',
            ]);

            $columnaTipoPesquisa = $this->primeraColumnaExistente($campos, [
                'id_tipo_pesquisa',
                'tipo_pesquisa_id',
                'id_pesquisa',
                'pesquisa_id',
            ]);

            if ($columnaJornada && $columnaTipoPesquisa) {
                $rows = $db->table('tipo_pesquisa_actividad')
                    ->select($columnaTipoPesquisa)
                    ->where($columnaJornada, $jornadaId)
                    ->get()
                    ->getResultArray();

                foreach ($rows as $row) {
                    if (isset($row[$columnaTipoPesquisa])) {
                        $pesquisasActivas[] = (string) $row[$columnaTipoPesquisa];
                    }
                }
            }
        }

        /*
         * Respaldo:
         * Si no encuentra pesquisas asociadas, detecta por evaluaciones existentes.
         */
        if (empty($pesquisasActivas)) {
            if ($this->existeEvaluacionAntropometria($jornadaId)) {
                $pesquisasActivas[] = '1';
            }

            if ($this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_sanguineo_lab',
                'evaluacion_sanguineo_lab',
                'evaluaciones_sanguineo',
                'evaluacion_sanguineo',
                'evaluaciones_laboratorio',
                'evaluacion_laboratorio',
            ])) {
                $pesquisasActivas[] = '2';
            }

            if ($this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_visual',
                'evaluacion_visual',
            ])) {
                $pesquisasActivas[] = '3';
            }

            if ($this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_signos_vitales',
                'evaluacion_signos_vitales',
                'signos_vitales',
            ])) {
                $pesquisasActivas[] = '4';
            }

            if ($this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_medicina_general',
                'evaluacion_medicina_general',
                'medicina_general',
            ])) {
                $pesquisasActivas[] = '5';
            }

            if ($this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_vacunacion',
                'evaluacion_vacunacion',
                'vacunacion',
            ])) {
                $pesquisasActivas[] = '6';
            }
        }

        return array_values(array_unique(array_map('strval', $pesquisasActivas)));
    }

    private function obtenerReportesDisponibles(int $jornadaId, array $pesquisasActivas): array
    {
        $disponibles = [];

        if (in_array('1', $pesquisasActivas, true)) {
            $disponibles['antropometria'] = $this->obtenerSubReportesAntropometria($jornadaId);
        }

        if (in_array('2', $pesquisasActivas, true)) {
            $disponibles['sanguineo'] = $this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_sanguineo_lab',
                'evaluacion_sanguineo_lab',
                'evaluaciones_sanguineo',
                'evaluacion_sanguineo',
                'evaluaciones_laboratorio',
                'evaluacion_laboratorio',
            ]);
        }

        if (in_array('3', $pesquisasActivas, true)) {
            $disponibles['visual'] = $this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_visual',
                'evaluacion_visual',
            ]);
        }

        if (in_array('4', $pesquisasActivas, true)) {
            $disponibles['signos_vitales'] = $this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_signos_vitales',
                'evaluacion_signos_vitales',
                'signos_vitales',
            ]);
        }

        if (in_array('5', $pesquisasActivas, true)) {
            $disponibles['medicina_general'] = $this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_medicina_general',
                'evaluacion_medicina_general',
                'medicina_general',
            ]);
        }

        if (in_array('6', $pesquisasActivas, true)) {
            $disponibles['vacunacion'] = $this->existeEvaluacionEnTablas($jornadaId, [
                'evaluaciones_vacunacion',
                'evaluacion_vacunacion',
                'vacunacion',
            ]);
        }

        return $disponibles;
    }

    private function obtenerSubReportesAntropometria(int $jornadaId): array
{
    $db = db_connect();

    $tabla = $this->primeraTablaExistente([
        'evaluaciones_antropometria',
        'evaluacion_antropometria',
        'evaluaciones_antropometricas',
        'evaluacion_antropometrica',
        'antropometria',
        'evaluaciones',
        'evaluacion',
    ]);

    if (!$tabla) {
        return [
            'menores_19' => false,
            'adultos' => false,
            'embarazadas' => false,
        ];
    }

    $campos = $db->getFieldNames($tabla);

    $columnaJornada = $this->primeraColumnaExistente($campos, [
        'id_jornada',
        'jornada_id',
        'id_actividad',
        'actividad_id',
    ]);

    if (!$columnaJornada) {
        return [
            'menores_19' => false,
            'adultos' => false,
            'embarazadas' => false,
        ];
    }

    $builderBase = $db->table($tabla)
        ->where($columnaJornada, $jornadaId);

    /*
     * Si la tabla es genérica y tiene id_tipo_pesquisa,
     * filtramos solo Antropometría.
     */
    if (in_array('id_tipo_pesquisa', $campos, true)) {
        $builderBase->where('id_tipo_pesquisa', 1);
    }

    if (in_array('tipo_pesquisa_id', $campos, true)) {
        $builderBase->where('tipo_pesquisa_id', 1);
    }

    /*
     * Caso ideal: existe grupo_reporte.
     */
    $columnaGrupo = $this->primeraColumnaExistente($campos, [
        'grupo_reporte',
        'grupo',
        'grupo_antropometria',
        'tipo_grupo',
    ]);

    if ($columnaGrupo) {
        return [
            'menores_19' => $this->existePorTexto($tabla, $columnaJornada, $jornadaId, $columnaGrupo, [
                'menor',
                'menores',
                'niño',
                'nino',
                'niña',
                'nina',
                'adolescente',
            ]),

            'adultos' => $this->existePorTexto($tabla, $columnaJornada, $jornadaId, $columnaGrupo, [
                'adulto',
                'adultos',
            ]),

            'embarazadas' => $this->existePorTexto($tabla, $columnaJornada, $jornadaId, $columnaGrupo, [
                'embarazada',
                'embarazadas',
                'embarazo',
                'gestante',
            ]),
        ];
    }

    /*
     * Fallback temporal:
     * Si hay registros de antropometría pero aún no detectamos el grupo,
     * mostramos al menos Menores de 19 para no ocultar el reporte.
     */
    $total = $builderBase->countAllResults();

    return [
        'menores_19' => $total > 0,
        'adultos' => false,
        'embarazadas' => false,
    ];
}
    private function existeEvaluacionAntropometria(int $jornadaId): bool
{
    return $this->existeEvaluacionEnTablas($jornadaId, [
        'evaluaciones_antropometria',
        'evaluacion_antropometria',
        'evaluaciones_antropometricas',
        'evaluacion_antropometrica',
        'antropometria',
        'evaluaciones',
        'evaluacion',
    ]);
}

    private function existeEvaluacionEnTablas(int $jornadaId, array $tablas): bool
    {
        $db = db_connect();

        foreach ($tablas as $tabla) {
            if (!$db->tableExists($tabla)) {
                continue;
            }

            $campos = $db->getFieldNames($tabla);

            $columnaJornada = $this->primeraColumnaExistente($campos, [
    'id_jornada',
    'jornada_id',
    'id_actividad',
    'actividad_id',
    'jornada',
]);

            if (!$columnaJornada) {
                continue;
            }

            $total = $db->table($tabla)
                ->where($columnaJornada, $jornadaId)
                ->countAllResults();

            if ($total > 0) {
                return true;
            }
        }

        return false;
    }

    private function existePorTexto(
        string $tabla,
        string $columnaJornada,
        int $jornadaId,
        string $columnaTexto,
        array $textos
    ): bool {
        $db = db_connect();

        $builder = $db->table($tabla)
            ->where($columnaJornada, $jornadaId)
            ->groupStart();

        foreach ($textos as $index => $texto) {
            if ($index === 0) {
                $builder->like($columnaTexto, $texto);
            } else {
                $builder->orLike($columnaTexto, $texto);
            }
        }

        $builder->groupEnd();

        return $builder->countAllResults() > 0;
    }

    private function primeraTablaExistente(array $tablas): ?string
    {
        $db = db_connect();

        foreach ($tablas as $tabla) {
            if ($db->tableExists($tabla)) {
                return $tabla;
            }
        }

        return null;
    }

    private function primeraColumnaExistente(array $campos, array $candidatas): ?string
    {
        foreach ($candidatas as $columna) {
            if (in_array($columna, $campos, true)) {
                return $columna;
            }
        }

        return null;
    }
}
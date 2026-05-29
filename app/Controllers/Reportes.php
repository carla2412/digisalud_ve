<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JornadaModel;

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
            'jornada'          => $jornada,
            'jornadaId'        => $jornadaId,
            'pesquisasActivas' => $pesquisasActivas,
            'reportesDisponibles' => $reportesDisponibles,
        ]);
    }

    // =========================================================================
    // Obtener pesquisas activas de la jornada
    // =========================================================================

    private function obtenerPesquisasActivas(int $jornadaId): array
    {
        $db = db_connect();

        $pesquisasActivas = [];

        /*
         * Lee desde tipo_pesquisa_actividad
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
                'idtipo_pesquisa',   // <-- nombre real en tu BD
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
         * Respaldo: detecta pesquisas por evaluaciones existentes en pesquisa_evaluaciones
         */
        if (empty($pesquisasActivas) && $db->tableExists('pesquisa_evaluaciones')) {
            $rows = $db->table('pesquisa_evaluaciones')
                ->select('tipo_pesquisa_id')
                ->where('jornada_id', $jornadaId)
                ->where('status_eval', 1)
                ->groupBy('tipo_pesquisa_id')
                ->get()
                ->getResultArray();

            foreach ($rows as $row) {
                $pesquisasActivas[] = (string) $row['tipo_pesquisa_id'];
            }
        }

        return array_values(array_unique(array_map('strval', $pesquisasActivas)));
    }

    // =========================================================================
    // Verificar qué pesquisas tienen datos reales
    // =========================================================================

    private function obtenerReportesDisponibles(int $jornadaId, array $pesquisasActivas): array
    {
        $disponibles = [];

        if (in_array('1', $pesquisasActivas, true)) {
            $disponibles['antropometria'] = $this->obtenerSubReportesAntropometria($jornadaId);
        }

        if (in_array('2', $pesquisasActivas, true)) {
            $disponibles['sanguineo'] = $this->existeEvaluacionConTipo($jornadaId, 2);
        }

        if (in_array('3', $pesquisasActivas, true)) {
            $disponibles['visual'] = $this->existeEvaluacionConTipo($jornadaId, 3);
        }

        if (in_array('4', $pesquisasActivas, true)) {
            $disponibles['signos_vitales'] = $this->existeEvaluacionConTipo($jornadaId, 4);
        }

        if (in_array('5', $pesquisasActivas, true)) {
            $disponibles['medicina_general'] = $this->existeEvaluacionConTipo($jornadaId, 5);
        }

        if (in_array('6', $pesquisasActivas, true)) {
            $disponibles['vacunacion'] = $this->existeEvaluacionConTipo($jornadaId, 6);
        }

        return $disponibles;
    }

    // =========================================================================
    // Verifica si existen evaluaciones para un tipo_pesquisa_id dado
    // Busca primero en pesquisa_evaluaciones (tabla real del proyecto),
    // luego en tablas legacy si existieran.
    // =========================================================================

    private function existeEvaluacionConTipo(int $jornadaId, int $tipoPesquisaId): bool
    {
        $db = db_connect();

        // ── Tabla principal del proyecto ──────────────────────────────────────
        if ($db->tableExists('pesquisa_evaluaciones')) {
            $total = $db->table('pesquisa_evaluaciones')
                ->where('jornada_id', $jornadaId)
                ->where('tipo_pesquisa_id', $tipoPesquisaId)
                ->where('status_eval', 1)
                ->countAllResults();

            if ($total > 0) {
                return true;
            }
        }

        // ── Tabla genérica "evaluaciones" (arquitectura nueva / centros) ──────
        if ($db->tableExists('evaluaciones')) {
            $campos = $db->getFieldNames('evaluaciones');

            $colJornada = $this->primeraColumnaExistente($campos, [
                'jornada_id', 'id_jornada',
            ]);

            $colTipo = $this->primeraColumnaExistente($campos, [
                'tipo_pesquisa_id', 'id_tipo_pesquisa',
            ]);

            if ($colJornada && $colTipo) {
                $total = $db->table('evaluaciones')
                    ->where($colJornada, $jornadaId)
                    ->where($colTipo, $tipoPesquisaId)
                    ->countAllResults();

                if ($total > 0) {
                    return true;
                }
            }
        }

        return false;
    }

    // =========================================================================
    // Sub-reportes de Antropometría (menores_19 / adultos / embarazadas)
    // =========================================================================

    private function obtenerSubReportesAntropometria(int $jornadaId): array
    {
        $db = db_connect();

        // ── 1. pesquisa_evaluaciones (tabla real) ─────────────────────────────
        if ($db->tableExists('pesquisa_evaluaciones')) {
            $total = $db->table('pesquisa_evaluaciones')
                ->where('jornada_id', $jornadaId)
                ->where('tipo_pesquisa_id', 1)
                ->where('status_eval', 1)
                ->countAllResults();

            if ($total > 0) {
                /*
                 * La tabla pesquisa_resultados almacena los ítems.
                 * Intentamos distinguir grupos por edad/condición.
                 * Si no hay suficiente información, mostramos los tres grupos
                 * como disponibles (el reporte filtrará internamente).
                 */
                return $this->detectarGruposAntropometria($jornadaId);
            }
        }

        // ── 2. Tabla genérica "evaluaciones" ─────────────────────────────────
        if ($db->tableExists('evaluaciones')) {
            $campos = $db->getFieldNames('evaluaciones');

            $colJornada = $this->primeraColumnaExistente($campos, [
                'jornada_id', 'id_jornada',
            ]);
            $colTipo = $this->primeraColumnaExistente($campos, [
                'tipo_pesquisa_id', 'id_tipo_pesquisa',
            ]);

            if ($colJornada && $colTipo) {
                $total = $db->table('evaluaciones')
                    ->where($colJornada, $jornadaId)
                    ->where($colTipo, 1)
                    ->countAllResults();

                if ($total > 0) {
                    return $this->detectarGruposAntropometria($jornadaId);
                }
            }
        }

        return [
            'menores_19'  => false,
            'adultos'     => false,
            'embarazadas' => false,
        ];
    }

    /**
     * Intenta determinar qué grupos antropométricos existen.
     *
     * Si la tabla pesquisa_resultados tiene datos suficientes para distinguir
     * grupos (por edad del beneficiario), separa. Si no, activa los tres para
     * que el controlador de reporte detallado filtre.
     */
    private function detectarGruposAntropometria(int $jornadaId): array
    {
        $db = db_connect();

        /*
         * Unimos pesquisa_evaluaciones → beneficiarios para obtener la edad
         * y así separar menores / adultos / embarazadas.
         */
        if ($db->tableExists('pesquisa_evaluaciones') && $db->tableExists('beneficiarios')) {
            $camposBenef = $db->getFieldNames('beneficiarios');

            // Columna de fecha de nacimiento
            $colFechaNac = $this->primeraColumnaExistente($camposBenef, [
                'fecha_nacimiento',
                'fechaNacimiento',
                'fecha_nac',
                'nacimiento',
            ]);

            // Columna de condición de embarazo
            $colEmbarazo = $this->primeraColumnaExistente($camposBenef, [
                'embarazada',
                'gestante',
                'condicion_embarazo',
                'es_embarazada',
            ]);

            if ($colFechaNac) {
                // Menores de 19 años
                $menores = $db->query("
                    SELECT COUNT(*) AS total
                    FROM pesquisa_evaluaciones pe
                    INNER JOIN beneficiarios b ON b.id_beneficiario = pe.beneficiario_id
                    WHERE pe.jornada_id       = {$jornadaId}
                      AND pe.tipo_pesquisa_id = 1
                      AND pe.status_eval      = 1
                      AND TIMESTAMPDIFF(YEAR, b.{$colFechaNac}, CURDATE()) < 19
                ")->getRowArray();

                // Adultos (≥ 19 años y no embarazadas)
                $adultos = $db->query("
                    SELECT COUNT(*) AS total
                    FROM pesquisa_evaluaciones pe
                    INNER JOIN beneficiarios b ON b.id_beneficiario = pe.beneficiario_id
                    WHERE pe.jornada_id       = {$jornadaId}
                      AND pe.tipo_pesquisa_id = 1
                      AND pe.status_eval      = 1
                      AND TIMESTAMPDIFF(YEAR, b.{$colFechaNac}, CURDATE()) >= 19
                      " . ($colEmbarazo ? "AND (b.{$colEmbarazo} IS NULL OR b.{$colEmbarazo} = 0)" : '') . "
                ")->getRowArray();

                // Embarazadas
                $embarazadas = ['total' => 0];
                if ($colEmbarazo) {
                    $embarazadas = $db->query("
                        SELECT COUNT(*) AS total
                        FROM pesquisa_evaluaciones pe
                        INNER JOIN beneficiarios b ON b.id_beneficiario = pe.beneficiario_id
                        WHERE pe.jornada_id       = {$jornadaId}
                          AND pe.tipo_pesquisa_id = 1
                          AND pe.status_eval      = 1
                          AND b.{$colEmbarazo}    = 1
                    ")->getRowArray();
                }

                return [
                    'menores_19'  => (int) ($menores['total']    ?? 0) > 0,
                    'adultos'     => (int) ($adultos['total']     ?? 0) > 0,
                    'embarazadas' => (int) ($embarazadas['total'] ?? 0) > 0,
                ];
            }
        }

        /*
         * Fallback: si no podemos distinguir grupos, activamos los tres.
         * El controlador de cada sub-reporte filtrará internamente.
         */
        return [
            'menores_19'  => true,
            'adultos'     => true,
            'embarazadas' => false, // embarazadas requiere campo explícito
        ];
    }

    // =========================================================================
    // Helpers
    // =========================================================================

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
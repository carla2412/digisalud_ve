<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JornadaModel;
use App\Models\TipoPesquisaActividadModel;
use App\Models\EvaluacionAntropometriaModel;

class Reportes extends BaseController
{
    public function home($jornadaId)
    {
        $jornadaModel = new JornadaModel();
        $tipoPesquisaActividadModel = new TipoPesquisaActividadModel();

        $jornada = $jornadaModel->find($jornadaId);

        if (!$jornada) {
            return redirect()
                ->to(site_url('jornadas'))
                ->with('error', 'La jornada seleccionada no existe.');
        }

        /*
         * 1. Buscar pesquisas asociadas a la jornada.
         * Ajustado a tu columna real: id_jornada.
         */
        $pesquisasJornada = $tipoPesquisaActividadModel
            ->where('id_jornada', $jornadaId)
            ->findAll();

        $pesquisasActivas = [];

        foreach ($pesquisasJornada as $pesquisa) {
            $idTipoPesquisa = $pesquisa['id_tipo_pesquisa']
                ?? $pesquisa['tipo_pesquisa_id']
                ?? null;

            if ($idTipoPesquisa !== null) {
                $pesquisasActivas[] = (string) $idTipoPesquisa;
            }
        }

        $pesquisasActivas = array_values(array_unique($pesquisasActivas));

        /*
         * 2. Detectar qué reportes tienen data evaluada.
         */
        $reportesDisponibles = $this->obtenerReportesDisponibles($jornadaId, $pesquisasActivas);

        return view('reportes/home_detallados', [
            'jornada' => $jornada,
            'jornadaId' => $jornadaId,
            'pesquisasActivas' => $pesquisasActivas,
            'reportesDisponibles' => $reportesDisponibles,
        ]);
    }

    private function obtenerReportesDisponibles(int $jornadaId, array $pesquisasActivas): array
    {
        $disponibles = [];

        /*
         * IDs actuales:
         * 1 = Antropometría
         * 2 = Laboratorio sanguíneo
         * 3 = Visual
         * 4 = Signos vitales
         * 5 = Medicina general
         * 6 = Vacunación
         */

        if (in_array('1', $pesquisasActivas, true)) {
            $disponibles['antropometria'] = $this->obtenerSubReportesAntropometria($jornadaId);
        }

        if (in_array('2', $pesquisasActivas, true)) {
            $disponibles['sanguineo'] = $this->existeEvaluacionEnTabla(
                'evaluaciones_sanguineo_lab',
                $jornadaId
            );
        }

        if (in_array('3', $pesquisasActivas, true)) {
            $disponibles['visual'] = $this->existeEvaluacionEnTabla(
                'evaluaciones_visual',
                $jornadaId
            );
        }

        if (in_array('4', $pesquisasActivas, true)) {
            $disponibles['signos_vitales'] = $this->existeEvaluacionEnTabla(
                'evaluaciones_signos_vitales',
                $jornadaId
            );
        }

        if (in_array('5', $pesquisasActivas, true)) {
            $disponibles['medicina_general'] = $this->existeEvaluacionEnTabla(
                'evaluaciones_medicina_general',
                $jornadaId
            );
        }

        if (in_array('6', $pesquisasActivas, true)) {
            $disponibles['vacunacion'] = $this->existeEvaluacionEnTabla(
                'evaluaciones_vacunacion',
                $jornadaId
            );
        }

        return $disponibles;
    }

    private function existeEvaluacionEnTabla(string $tabla, int $jornadaId): bool
    {
        return db_connect()
            ->table($tabla)
            ->where('id_jornada', $jornadaId)
            ->countAllResults() > 0;
    }

    private function obtenerSubReportesAntropometria(int $jornadaId): array
    {
        /*
         * IMPORTANTE:
         * Aquí debes ajustar los nombres de tabla y columnas según tu BD real.
         *
         * La lógica buscada:
         * - menores_19: edad < 19 años y no embarazada
         * - adultos: edad >= 19 años y no embarazada
         * - embarazadas: embarazada = 1 / Si
         */

        $db = db_connect();

        $base = $db->table('evaluaciones_antropometria ea')
            ->join('beneficiarios b', 'b.id_beneficiario = ea.id_beneficiario')
            ->where('ea.id_jornada', $jornadaId);

        $menores19 = clone $base;
        $adultos = clone $base;
        $embarazadas = clone $base;

        return [
            'menores_19' => $menores19
                ->where('TIMESTAMPDIFF(YEAR, b.fecha_nacimiento, CURDATE()) <', 19)
                ->groupStart()
                    ->where('ea.embarazada', 0)
                    ->orWhere('ea.embarazada IS NULL', null, false)
                ->groupEnd()
                ->countAllResults() > 0,

            'adultos' => $adultos
                ->where('TIMESTAMPDIFF(YEAR, b.fecha_nacimiento, CURDATE()) >=', 19)
                ->groupStart()
                    ->where('ea.embarazada', 0)
                    ->orWhere('ea.embarazada IS NULL', null, false)
                ->groupEnd()
                ->countAllResults() > 0,

            'embarazadas' => $embarazadas
                ->groupStart()
                    ->where('ea.embarazada', 1)
                    ->orWhere('ea.embarazada', 'Si')
                    ->orWhere('ea.embarazada', 'SI')
                    ->orWhere('ea.embarazada', 'sí')
                    ->orWhere('ea.embarazada', 'SÍ')
                ->groupEnd()
                ->countAllResults() > 0,
        ];
    }
}
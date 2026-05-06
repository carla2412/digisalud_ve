<?php

namespace App\Models;

use CodeIgniter\Model;

class PesquisaEvaluacionModel extends Model
{
    protected $table            = 'pesquisa_evaluaciones';
    protected $primaryKey       = 'id_evaluacion';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'beneficiario_id', 'tipo_pesquisa_id', 'jornada_id', 'centro_id',
        'fecha_evaluacion', 'observaciones', 'evaluado_por',
        'creado_en', 'modificado_en', 'modificado_por', 'status_eval',
    ];

    /**
     * Verificar si ya existe evaluación activa para un beneficiario
     * en una pesquisa dentro de una jornada.
     */
    public function existeEnJornada(int $beneficiarioId, int $tipoPesquisaId, int $jornadaId): ?array
    {
        return $this->where('beneficiario_id', $beneficiarioId)
                    ->where('tipo_pesquisa_id', $tipoPesquisaId)
                    ->where('jornada_id', $jornadaId)
                    ->where('status_eval', 1)
                    ->first();
    }

    /**
     * Obtener todas las evaluaciones activas de un beneficiario en una jornada.
     * Retorna array de tipo_pesquisa_id evaluados.
     */
    public function getPesquisasEvaluadas(int $beneficiarioId, int $jornadaId): array
    {
        $rows = $this->select('tipo_pesquisa_id')
                     ->where('beneficiario_id', $beneficiarioId)
                     ->where('jornada_id', $jornadaId)
                     ->where('status_eval', 1)
                     ->findAll();

        return array_column($rows, 'tipo_pesquisa_id');
    }

    /**
     * Obtener evaluaciones de un beneficiario en una pesquisa (historial).
     */
    public function getHistorial(int $beneficiarioId, int $tipoPesquisaId): array
    {
        return $this->where('beneficiario_id', $beneficiarioId)
                    ->where('tipo_pesquisa_id', $tipoPesquisaId)
                    ->where('status_eval', 1)
                    ->orderBy('fecha_evaluacion', 'DESC')
                    ->findAll();
    }

    /**
     * Obtener evaluaciones por jornada con conteo por pesquisa.
     */
    public function getConteosPorJornada(int $jornadaId): array
    {
        return $this->select('tipo_pesquisa_id, COUNT(*) as total')
                    ->where('jornada_id', $jornadaId)
                    ->where('status_eval', 1)
                    ->groupBy('tipo_pesquisa_id')
                    ->findAll();
    }
}
<?php

namespace App\Models;

use CodeIgniter\Model;

class PesquisaResultadoModel extends Model
{
    protected $table            = 'pesquisa_resultados';
    protected $primaryKey       = 'id_resultado';
    protected $returnType       = 'array';
    protected $useTimestamps    = false;
    protected $allowedFields    = [
        'evaluacion_id', 'item_id',
        'valor_texto', 'valor_numero', 'valor_booleano', 'valor_fecha',
    ];

    /**
     * Obtener todos los resultados de una evaluación con datos del item.
     */
    public function getResultadosConItems(int $evaluacionId): array
    {
        return $this->select('pesquisa_resultados.*, pi.codigo, pi.nombre, pi.seccion, pi.tipo_dato, pi.unidad, pi.orden')
                    ->join('pesquisa_items pi', 'pi.id_item = pesquisa_resultados.item_id')
                    ->where('pesquisa_resultados.evaluacion_id', $evaluacionId)
                    ->orderBy('pi.seccion, pi.orden', 'ASC')
                    ->findAll();
    }

    /**
     * Guardar resultados en lote para una evaluación.
     * $datos = [ ['item_id' => X, 'valor' => Y, 'tipo_dato' => 'number'], ... ]
     */
    public function guardarLote(int $evaluacionId, array $datos): bool
    {
        $batch = [];

        foreach ($datos as $d) {
            $fila = [
                'evaluacion_id'  => $evaluacionId,
                'item_id'        => $d['item_id'],
                'valor_texto'    => null,
                'valor_numero'   => null,
                'valor_booleano' => null,
                'valor_fecha'    => null,
            ];

            $valor = $d['valor'];
            if ($valor === '' || $valor === null) {
                continue;
            }

            switch ($d['tipo_dato']) {
                case 'number':
                    $fila['valor_numero'] = (float) $valor;
                    break;
                case 'boolean':
                    $fila['valor_booleano'] = ($valor === 's' || $valor === '1' || $valor === true) ? 1 : 0;
                    break;
                case 'date':
                    $fila['valor_fecha'] = $valor;
                    break;
                default: // text, textarea, select
                    $fila['valor_texto'] = (string) $valor;
                    break;
            }

            $batch[] = $fila;
        }

        if (empty($batch)) {
            return true;
        }

        return $this->insertBatch($batch) !== false;
    }

    /**
     * Eliminar resultados de una evaluación (para re-guardar).
     */
    public function eliminarPorEvaluacion(int $evaluacionId): bool
    {
        return $this->where('evaluacion_id', $evaluacionId)->delete();
    }
}
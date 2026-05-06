<?php

namespace App\Models;

use CodeIgniter\Model;

class PesquisaItemModel extends Model
{
    protected $table            = 'pesquisa_items';
    protected $primaryKey       = 'id_item';
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'tipo_pesquisa_id', 'codigo', 'nombre', 'seccion',
        'tipo_dato', 'opciones_json', 'obligatorio', 'orden',
        'unidad', 'valor_min', 'valor_max', 'placeholder',
        'depende_de', 'depende_valor', 'ancho_col', 'status_item',
    ];

    /**
     * Obtener todos los items activos de una pesquisa,
     * agrupados por sección y ordenados.
     */
    public function getItemsPorPesquisa(int $tipoPesquisaId): array
    {
        return $this->where('tipo_pesquisa_id', $tipoPesquisaId)
                    ->where('status_item', 1)
                    ->orderBy('seccion, orden', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener items agrupados por sección (para renderizar el form).
     * Retorna: ['hematologia' => [...items], 'orina' => [...items], ...]
     */
    public function getItemsAgrupados(int $tipoPesquisaId): array
    {
        $items = $this->getItemsPorPesquisa($tipoPesquisaId);
        $agrupados = [];

        foreach ($items as $item) {
            $seccion = $item['seccion'] ?? 'general';
            $agrupados[$seccion][] = $item;
        }

        return $agrupados;
    }

    /**
     * Obtener un item por su código y pesquisa.
     */
    public function getItemPorCodigo(int $tipoPesquisaId, string $codigo): ?array
    {
        return $this->where('tipo_pesquisa_id', $tipoPesquisaId)
                    ->where('codigo', $codigo)
                    ->where('status_item', 1)
                    ->first();
    }

    /**
     * Obtener mapa id_item => item para validación rápida.
     */
    public function getMapaItems(int $tipoPesquisaId): array
    {
        $items = $this->getItemsPorPesquisa($tipoPesquisaId);
        $mapa = [];

        foreach ($items as $item) {
            $mapa[$item['id_item']] = $item;
        }

        return $mapa;
    }
}
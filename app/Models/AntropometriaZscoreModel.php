<?php

namespace App\Models;

/**
 * Fuente de archivos JSON para cálculos antropométricos.
 * No usa tabla: el usuario pidió trabajar z-score como JSON, un archivo por indicador.
 */
class AntropometriaZscoreModel
{
    public function getManifest(): array
    {
        return [
            'zpe_dias'          => base_url('data/antro/zpe_dias.json'),
            'zpe_meses'         => base_url('data/antro/zpe_meses.json'),
            'zte_dias'          => base_url('data/antro/zte_dias.json'),
            'zte_meses_m'       => base_url('data/antro/zte_meses.json'),
            'zte_meses_f'       => base_url('data/antro/zte_meses_parte2.json'),
            'zimce_dias'        => base_url('data/antro/zimce_dias.json'),
            'zimce_meses'       => base_url('data/antro/zimce_meses.json'),
            'zpt_65_120'        => base_url('data/antro/zpeso_talla.json'),
            'zpt_45_110'        => base_url('data/antro/zpeso_talla2.json'),
            'zcc_dias'          => base_url('data/antro/zcc_dias.json'),
            'zcbi_dias'         => base_url('data/antro/zcbi_dias.json'),
            'ztricipital_dias'  => base_url('data/antro/ztricipital_dias.json'),
            'zsubescapular_dias'=> base_url('data/antro/zsubescapular_dias.json'),
            'percentiles'       => base_url('data/antro/percentiles.json'),
        ];
    }
}

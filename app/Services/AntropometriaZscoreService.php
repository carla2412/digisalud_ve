<?php

namespace App\Services;

use DateTime;
use Throwable;

class AntropometriaZscoreService
{
    protected string $basePath;

    protected array $cache = [];

    public function __construct(?string $basePath = null)
    {
        $this->basePath = rtrim($basePath ?? FCPATH . 'data/antro/', DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    public function calcular(array $datos): array
    {
        return $this->calcularMenor($datos);
    }

    public function calcularMenor(array $datos): array
    {
        $sexo = strtoupper(trim((string)($datos['sexo'] ?? '')));
        $sexo = in_array($sexo, ['M', 'F'], true) ? $sexo : null;

        $peso = $this->toFloat($datos['peso'] ?? null);
        $talla = $this->toFloat($datos['talla'] ?? null);
        $circCefalica = $this->toFloat($datos['circ_cefalica'] ?? null);
        $circBrazoIzq = $this->toFloat($datos['circ_brazo_izq'] ?? null);
        $pliegueTricipital = $this->toFloat($datos['pliegue_tricipital'] ?? null);
        $pliegueSubescapular = $this->toFloat($datos['pliegue_subescapular'] ?? null);
        $metodoMedicionTalla = $this->normalizarMetodoMedicionTalla($datos['metodo_medicion_talla'] ?? null);
        $fechaNacimiento = $datos['fecha_nacimiento'] ?? null;
        $fechaEvaluacion = $datos['fecha_evaluacion'] ?? null;

        $edadDias = $this->calcularEdadDias($fechaNacimiento, $fechaEvaluacion);
        $edemaRaw = strtolower(trim((string)($datos['edema'] ?? '')));
        $tieneEdema = in_array($edemaRaw, ['1', 'si', 'sí', 's', 'true'], true);

        $imc = null;

        if ($peso !== null && $peso > 0 && $talla !== null && $talla > 0) {
            $tallaMetros = $talla / 100;

            if ($tallaMetros > 0) {
                $imc = round($peso / ($tallaMetros * $tallaMetros), 2);
            }
        }

        $grupoEdadReporte = $this->grupoEdadReporte($edadDias);

        $resultado = [
            'edad_dias_medicion'          => $edadDias,
            'grupo_edad_reporte'          => $grupoEdadReporte,
            'imc'                         => $imc,

            'zpe'                         => null,
            'zpe_percentil'               => null,
            'zte'                         => null,
            'zte_percentil'               => null,
            'zimce'                       => null,
            'zimce_percentil'             => null,
            'zpt'                         => null,
            'zpt_percentil'               => null,
            'zcc'                         => null,
            'zcc_percentil'               => null,
            'zcbi'                        => null,
            'zcbi_percentil'              => null,
            'zptri'                       => null,
            'zptri_percentil'             => null,
            'zpsub'                       => null,
            'zpsub_percentil'             => null,

            'clasificacion_imc_talla'     => null,
            'estado_nutricional_agregado' => null,
            'semaforo_nutricional'        => null,
            'interpretacion_zimce_zte'    => null,
            'interpretacion_zpt_zte'      => null,
        ];

        if (
            $edadDias === null ||
            $edadDias <= 0 ||
            $edadDias > 6939 ||
            $sexo === null ||
            $peso === null ||
            $peso <= 0 ||
            $talla === null ||
            $talla <= 0 ||
            $tieneEdema
        ) {
            return $resultado;
        }

        $this->setZscoreConPercentil($resultado, 'zpe', $this->zEdadPeso($sexo, $edadDias, $peso));
        $this->setZscoreConPercentil($resultado, 'zte', $this->zEdadTalla($sexo, $edadDias, $talla));

        if ($imc !== null) {
            $this->setZscoreConPercentil($resultado, 'zimce', $this->zEdadImc($sexo, $edadDias, $imc));
        }

        $this->setZscoreConPercentil($resultado, 'zpt', $this->zPesoTalla($sexo, $edadDias, $talla, $peso));

        if ($edadDias >= 1 && $edadDias <= 1856) {
            if ($circCefalica !== null) {
                $this->setZscoreConPercentil(
                    $resultado,
                    'zcc',
                    $this->zGenericByDiasCampos(
                        'zcc_dias.json',
                        $sexo,
                        $edadDias,
                        $circCefalica,
                        'ccdias_indicador_genero',
                        'ccdias_indicador_denominador',
                        'ccdias_sd0',
                        'ccdias_indicador_coeficiente_l',
                        'ccdias_indicador_coeficiente_s'
                    )
                );
            }

            if ($circBrazoIzq !== null) {
                $this->setZscoreConPercentil(
                    $resultado,
                    'zcbi',
                    $this->zGenericByDiasCampos(
                        'zcbi_dias.json',
                        $sexo,
                        $edadDias,
                        $circBrazoIzq,
                        'cbidias_indicador_genero',
                        'cbidias_indicador_denominador',
                        'cbidias_sd0',
                        'cbidias_indicador_coeficiente_l',
                        'cbidias_indicador_coeficiente_s'
                    )
                );
            }

            if ($pliegueTricipital !== null) {
                $this->setZscoreConPercentil(
                    $resultado,
                    'zptri',
                    $this->zGenericByDiasCampos(
                        'ztricipital_dias.json',
                        $sexo,
                        $edadDias,
                        $pliegueTricipital,
                        'tridias_indicador_genero',
                        'tridias_indicador_denominador',
                        'tridias_sd0',
                        'tridias_indicador_coeficiente_l',
                        'tridias_indicador_coeficiente_s'
                    )
                );
            }

            if ($pliegueSubescapular !== null) {
                $this->setZscoreConPercentil(
                    $resultado,
                    'zpsub',
                    $this->zGenericByDiasCampos(
                        'zsubescapular_dias.json',
                        $sexo,
                        $edadDias,
                        $pliegueSubescapular,
                        'subdias_indicador_genero',
                        'subdias_indicador_denominador',
                        'subdias_sd0',
                        'subdias_indicador_coeficiente_l',
                        'subdias_indicador_coeficiente_s'
                    )
                );
            }
        }

        $resultado['interpretacion_zimce_zte'] = $this->interpretacionZimceZte(
            $resultado['zimce'],
            $resultado['zte']
        );

        if ($edadDias <= 1856) {
            $resultado['interpretacion_zpt_zte'] = $this->interpretacionZptZte(
                $resultado['zpt'],
                $resultado['zte']
            );
        }

        $resultado['clasificacion_imc_talla'] = $this->clasificacionImcTalla(
            $resultado['zpt'],
            $resultado['zimce'],
            $resultado['zte']
        );

        $resultado['estado_nutricional_agregado'] = $this->estadoNutricionalAgregado(
            $resultado['zpt'],
            $resultado['zimce'],
            $resultado['zte']
        );

        $resultado['semaforo_nutricional'] = $this->semaforoMenor(
            $resultado['zimce'],
            $resultado['zte']
        );

        return $resultado;
    }

    protected function zEdadPeso(string $sexo, int $edadDias, float $peso): ?float
    {
        if ($edadDias >= 1 && $edadDias <= 1856) {
            return $this->zGenericByDiasCampos(
                'zpe_dias.json',
                $sexo,
                $edadDias,
                $peso,
                'pdias_indicador_genero',
                'pdias_indicador_denominador',
                'pdias_sd0_mediana',
                'pdias_indicador_coeficiente_l',
                'pdias_indicador_coeficiente_s'
            );
        }

        if ($edadDias > 1856 && $edadDias <= 3653) {
            return $this->zGenericByMesesCampos(
                'zpe_meses.json',
                $sexo,
                $edadDias,
                $peso,
                'p_indicador_genero',
                'p_indicador_denominador',
                'p_indicador_coeficiente_m',
                'p_indicador_coeficiente_l',
                'p_indicador_coeficiente_s'
            );
        }

        return null;
    }

    protected function zEdadTalla(string $sexo, int $edadDias, float $talla): ?float
    {
        if ($edadDias >= 1 && $edadDias <= 1856) {
            return $this->zGenericByDiasCampos(
                'zte_dias.json',
                $sexo,
                $edadDias,
                $talla,
                'tdias_indicador_genero',
                'tdias_indicador_denominador',
                'tdias_sd0_mediana',
                'tdias_indicador_coeficiente_l',
                'tdias_indicador_coeficiente_s'
            );
        }

        if ($edadDias > 1856 && $edadDias <= 6939) {
            $archivo = $sexo === 'M'
                ? 'zte_meses.json'
                : 'zte_meses_parte2.json';

            return $this->zGenericByMesesCampos(
                $archivo,
                $sexo,
                $edadDias,
                $talla,
                't_indicador_genero',
                't_indicador_denominador',
                't_indicador_coeficiente_m',
                't_indicador_coeficiente_l',
                't_indicador_coeficiente_s'
            );
        }

        return null;
    }

    protected function zEdadImc(string $sexo, int $edadDias, float $imc): ?float
    {
        if ($edadDias >= 1 && $edadDias <= 1856) {
            return $this->zGenericByDiasCampos(
                'zimce_dias.json',
                $sexo,
                $edadDias,
                $imc,
                'idias_indicador_genero',
                'idias_indicador_denominador',
                'idias_sd0_mediana',
                'idias_indicador_coeficiente_l',
                'idias_indicador_coeficiente_s'
            );
        }

        if ($edadDias > 1856 && $edadDias <= 6939) {
            return $this->zGenericByMesesCampos(
                'zimce_meses.json',
                $sexo,
                $edadDias,
                $imc,
                'i_indicador_genero',
                'i_indicador_denominador',
                'i_indicador_coeficiente_m',
                'i_indicador_coeficiente_l',
                'i_indicador_coeficiente_s'
            );
        }

        return null;
    }

    protected function zPesoTalla(string $sexo, int $edadDias, float $talla, float $peso): ?float
    {
        if ($edadDias < 1 || $edadDias > 1856) {
            return null;
        }

        $archivo = $edadDias <= 730
            ? 'zpeso_talla2.json'
            : 'zpeso_talla.json';

        $rows = $this->filtrarPorSexoCampo(
            $this->loadJson($archivo),
            $sexo,
            'petadias_indicador_genero'
        );

        if (empty($rows)) {
            return null;
        }

        $denominador = round($talla, 1);
        $row = $this->nearestCampo($rows, $denominador, 'petadias_indicador_denominador');

        if (!$row) {
            return null;
        }

        return $this->calcZLms(
            $peso,
            $row['petadias_sd0_mediana'] ?? null,
            $row['petadias_indicador_coeficiente_l'] ?? null,
            $row['petadias_indicador_coeficiente_s'] ?? null
        );
    }
    protected function zGenericByDias(string $archivo, string $sexo, int $edadDias, float $valor): ?float
    {
        $rows = $this->filtrarPorSexo($this->loadJson($archivo), $sexo);

        if (empty($rows)) {
            return null;
        }

        $row = $this->nearest($rows, $edadDias, ['dias', 'dia', 'edad_dias', 'day', 'days']);

        if (!$row) {
            return null;
        }

        return $this->calcZ($valor, $row);
    }

    protected function zGenericByMeses(string $archivo, string $sexo, int $edadDias, float $valor): ?float
    {
        $meses = $this->edadMesesAprox($edadDias);

        $rows = $this->filtrarPorSexo($this->loadJson($archivo), $sexo);

        if (empty($rows)) {
            return null;
        }

        $row = $this->nearest($rows, $meses, ['meses', 'mes', 'edad_meses', 'month', 'months']);

        if (!$row) {
            return null;
        }

        return $this->calcZ($valor, $row);
    }

    protected function calcZ(float $valor, array $row): ?float
    {
        $l = $this->getValue($row, ['l', 'L']);
        $m = $this->getValue($row, ['m', 'M']);
        $s = $this->getValue($row, ['s', 'S']);

        if ($l !== null && $m !== null && $s !== null && $m > 0 && $s > 0) {
            if (abs($l) < 0.0000001) {
                $z = log($valor / $m) / $s;
            } else {
                $z = (pow($valor / $m, $l) - 1) / ($l * $s);
            }

            return $this->valoresExtremos(round($z, 2));
        }

        $sd0 = $this->getValue($row, ['sd0', 'SD0', 'p50', 'P50', 'mediana', 'median']);
        $sd1 = $this->getValue($row, ['sd1', 'SD1', '+1sd', '1sd']);
        $sd_1 = $this->getValue($row, ['sd_1', 'SD_1', 'sd-1', '-1sd']);

        if ($sd0 !== null && $sd1 !== null && $sd_1 !== null) {
            if ($valor >= $sd0) {
                $den = $sd1 - $sd0;
                if ($den == 0) {
                    return null;
                }

                return $this->valoresExtremos(round(($valor - $sd0) / $den, 2));
            }

            $den = $sd0 - $sd_1;
            if ($den == 0) {
                return null;
            }

            return $this->valoresExtremos(round(($valor - $sd0) / $den, 2));
        }

        $minus3 = $this->getValue($row, ['sd3neg', 'SD3neg', 'sd_3_neg', 'sd-3', '-3sd', 'SD-3']);
        $minus2 = $this->getValue($row, ['sd2neg', 'SD2neg', 'sd_2_neg', 'sd-2', '-2sd', 'SD-2']);
        $minus1 = $this->getValue($row, ['sd1neg', 'SD1neg', 'sd_1_neg', 'sd-1', '-1sd', 'SD-1']);
        $zero   = $this->getValue($row, ['sd0', 'SD0', '0sd', 'SD0']);
        $plus1  = $this->getValue($row, ['sd1', 'SD1', '+1sd', 'SD1']);
        $plus2  = $this->getValue($row, ['sd2', 'SD2', '+2sd', 'SD2']);
        $plus3  = $this->getValue($row, ['sd3', 'SD3', '+3sd', 'SD3']);

        $points = [
            -3 => $minus3,
            -2 => $minus2,
            -1 => $minus1,
            0 => $zero,
            1 => $plus1,
            2 => $plus2,
            3 => $plus3,
        ];

        $valid = [];

        foreach ($points as $z => $v) {
            if ($v !== null) {
                $valid[$z] = $v;
            }
        }

        if (count($valid) < 2) {
            return null;
        }

        ksort($valid);

        $z = $this->interp($valor, $valid);

        return $z !== null ? $this->valoresExtremos(round($z, 2)) : null;
    }

    protected function interp(float $valor, array $points): ?float
    {
        $prevZ = null;
        $prevValue = null;

        foreach ($points as $z => $pointValue) {
            if ($prevZ === null) {
                $prevZ = (float)$z;
                $prevValue = (float)$pointValue;
                continue;
            }

            $currentZ = (float)$z;
            $currentValue = (float)$pointValue;

            if (
                ($valor >= $prevValue && $valor <= $currentValue) ||
                ($valor <= $prevValue && $valor >= $currentValue)
            ) {
                $den = $currentValue - $prevValue;

                if ($den == 0) {
                    return null;
                }

                return $prevZ + (($valor - $prevValue) / $den) * ($currentZ - $prevZ);
            }

            $prevZ = $currentZ;
            $prevValue = $currentValue;
        }

        $keys = array_keys($points);
        $firstZ = (float)$keys[0];
        $lastZ = (float)$keys[count($keys) - 1];

        $firstValue = (float)$points[$keys[0]];
        $lastValue = (float)$points[$keys[count($keys) - 1]];

        if ($valor < $firstValue) {
            return $firstZ;
        }

        if ($valor > $lastValue) {
            return $lastZ;
        }

        return null;
    }

    protected function nearest(array $rows, float|int $target, array $possibleKeys): ?array
    {
        $best = null;
        $bestDistance = null;

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $value = $this->getValue($row, $possibleKeys);

            if ($value === null) {
                continue;
            }

            $distance = abs($value - $target);

            if ($best === null || $distance < $bestDistance) {
                $best = $row;
                $bestDistance = $distance;
            }
        }

        return $best;
    }

    protected function filtrarPorSexo(array $rows, string $sexo): array
    {
        $filtrados = [];

        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }

            $sexoRow = strtoupper(trim((string)(
                $row['sexo']
                ?? $row['sex']
                ?? $row['genero']
                ?? $row['gender']
                ?? ''
            )));

            if ($sexoRow === '' || $sexoRow === $sexo) {
                $filtrados[] = $row;
                continue;
            }

            if ($sexo === 'M' && in_array($sexoRow, ['1', 'NIÑO', 'NINO', 'MASCULINO', 'MALE', 'BOY'], true)) {
                $filtrados[] = $row;
                continue;
            }

            if ($sexo === 'F' && in_array($sexoRow, ['2', 'NIÑA', 'NINA', 'FEMENINO', 'FEMALE', 'GIRL'], true)) {
                $filtrados[] = $row;
            }
        }

        return $filtrados;
    }

    protected function loadJson(string $archivo): array
    {
        if (isset($this->cache[$archivo])) {
            return $this->cache[$archivo];
        }

        $path = $this->basePath . $archivo;

        if (!is_file($path)) {
            return $this->cache[$archivo] = [];
        }

        $json = file_get_contents($path);
        $data = json_decode($json, true);

        if (!is_array($data)) {
            return $this->cache[$archivo] = [];
        }

        if (isset($data['data']) && is_array($data['data'])) {
            return $this->cache[$archivo] = $data['data'];
        }

        if (isset($data['rows']) && is_array($data['rows'])) {
            return $this->cache[$archivo] = $data['rows'];
        }

        return $this->cache[$archivo] = $data;
    }

    protected function calcularEdadDias($fechaNacimiento, $fechaEvaluacion): ?int
    {
        try {
            if (empty($fechaNacimiento) || empty($fechaEvaluacion)) {
                return null;
            }

            $nacimiento = new DateTime((string)$fechaNacimiento);
            $evaluacion = new DateTime((string)$fechaEvaluacion);

            $diff = $nacimiento->diff($evaluacion);

            if ($diff->invert === 1) {
                return null;
            }

            return (int)$diff->days;
        } catch (Throwable) {
            return null;
        }
    }

    protected function edadMesesAprox(int $edadDias): int
    {
        return (int)floor($edadDias / 30.4375);
    }

    protected function grupoEdadReporte(?int $edadDias): ?string
    {
        if ($edadDias === null || $edadDias <= 0) {
            return null;
        }

        if ($edadDias < 1826) {
            return '< de 5 años';
        }

        if ($edadDias <= 6939) {
            return '5 a 19 años';
        }

        return '> de 19 años';
    }

    protected function interpretacionZimceZte(?float $zimce, ?float $zte): ?string
    {
        if ($zimce === null && $zte === null) {
            return null;
        }

        $catTalla = $this->categoriaTallaZte($zte);
        $catImc = $this->categoriaZimce($zimce);

        if ($catImc === null && $catTalla === null) {
            return null;
        }

        return trim(($catImc ?? 'Sin IMC/Edad') . ' / ' . ($catTalla ?? 'Sin Talla/Edad'));
    }

    protected function interpretacionZptZte(?float $zpt, ?float $zte): ?string
    {
        if ($zpt === null && $zte === null) {
            return null;
        }

        $catPt = $this->categoriaZpt($zpt);
        $catTalla = $this->categoriaTallaZteMinuscula($zte);

        if ($catPt === null && $catTalla === null) {
            return null;
        }

        return trim(($catPt ?? 'Sin Peso/Talla') . ' / ' . ($catTalla ?? 'sin talla/edad'));
    }

    protected function categoriaZimce(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'Desnutrición severa';
        if ($z < -2) return 'Desnutrición';
        if ($z < -1) return 'Riesgo de desnutrición';
        if ($z <= 1) return 'Peso adecuado';
        if ($z <= 2) return 'Riesgo de sobrepeso';
        if ($z <= 3) return 'Sobrepeso';
        return 'Obesidad';
    }

    protected function categoriaZpt(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'Emaciación severa';
        if ($z < -2) return 'Emaciación';
        if ($z < -1) return 'Riesgo de emaciación';
        if ($z <= 1) return 'Peso adecuado/talla';
        if ($z <= 2) return 'Posible riesgo sobrepeso';
        return 'Sobrepeso/talla';
    }

    protected function categoriaTallaZte(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'Talla muy baja';
        if ($z < -2) return 'Talla baja';
        if ($z <= 3) return 'Talla adecuada';
        return 'Talla alta';
    }

    protected function categoriaTallaZteMinuscula(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'talla muy baja';
        if ($z < -2) return 'talla baja';
        if ($z <= 3) return 'talla adecuada';
        return 'talla alta';
    }

    protected function clasificacionImcTalla(?float $zpt, ?float $zimce, ?float $zte): ?string
    {
        $ponderal = $zpt !== null ? $zpt : $zimce;

        if ($ponderal === null && $zte === null) {
            return null;
        }

        $clasePeso = $this->clasePeso($ponderal);
        $claseTalla = $this->claseTalla($zte);

        if ($clasePeso === null && $claseTalla === null) {
            return null;
        }

        return ($clasePeso ?? 'Sin clasificación ponderal') . ' con ' . ($claseTalla ?? 'Sin clasificación de talla');
    }

    protected function clasePeso(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'Delgadez severa';
        if ($z < -2) return 'Delgadez';
        if ($z < -1) return 'Riesgo de delgadez';
        if ($z <= 1) return 'Peso adecuado';
        if ($z <= 2) return 'Sobrepeso';
        if ($z <= 3) return 'Obesidad';
        return 'Obesidad severa';
    }

    protected function claseTalla(?float $z): ?string
    {
        if ($z === null) return null;
        if ($z < -3) return 'Talla muy baja';
        if ($z < -2) return 'Talla baja';
        if ($z <= 2) return 'Talla adecuada';
        return 'Talla alta';
    }

    protected function estadoNutricionalAgregado(?float $zpt, ?float $zimce, ?float $zte): ?string
    {
        $ponderal = $zpt !== null ? $zpt : $zimce;

        if ($ponderal === null && $zte === null) {
            return null;
        }

        if ($ponderal !== null && $ponderal > 1) {
            return 'malnutrición por exceso';
        }

        if ($ponderal !== null && $ponderal < -1 && $zte !== null && $zte < -2) {
            return 'déficit agudo más crónico';
        }

        if ($ponderal !== null && $ponderal < -1 && ($zte === null || $zte >= -2)) {
            return 'déficit agudo';
        }

        if ($zte !== null && $zte < -2 && $ponderal !== null && $ponderal >= -1 && $ponderal <= 1) {
            return 'déficit crónico';
        }

        return 'sin malnutrición agregada';
    }

    protected function semaforoMenor(?float $zimce, ?float $zte): string
    {
        if ($zimce === null) {
            return 'gris';
        }

        if ($zimce < -3 || ($zte !== null && $zte < -3)) {
            return 'rojo';
        }

        if ($zimce < -2 || ($zte !== null && $zte < -2)) {
            return 'naranja';
        }

        if ($zimce < -1) {
            return 'amarillo';
        }

        if ($zimce > 3) {
            return 'rojo';
        }

        if ($zimce > 2) {
            return 'naranja';
        }

        if ($zimce > 1) {
            return 'amarillo';
        }

        return 'verde';
    }

    protected function valoresExtremos(?float $z): ?float
    {
        if ($z === null) {
            return null;
        }

        if ($z > 6) {
            return 6.0;
        }

        if ($z < -6) {
            return -6.0;
        }

        return $z;
    }

    protected function getValue(array $row, array $keys): ?float
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== '' && $row[$key] !== null) {
                return (float)$row[$key];
            }
        }

        return null;
    }

    protected function toFloat($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', trim($value));
        }

        return is_numeric($value) ? (float)$value : null;
    }

    protected function setZscoreConPercentil(array &$resultado, string $codigo, ?float $zscore): void
    {
        if ($zscore === null || ! is_finite($zscore)) {
            return;
        }

        $resultado[$codigo] = round($zscore, 2);
        $resultado[$codigo . '_percentil'] = $this->zscoreAPercentil($zscore);
    }

    protected function zscoreAPercentil(float $z): string
    {
        $p = $this->normalCdf($z) * 100;

        if (! is_finite($p)) {
            return '';
        }

        if ($p < 0.01) {
            return '<0.01';
        }

        if ($p > 99.99) {
            return '>99.99';
        }

        return number_format($p, 2, '.', '');
    }

    protected function normalCdf(float $x): float
    {
        return 0.5 * (1 + $this->erf($x / sqrt(2)));
    }

    protected function erf(float $x): float
    {
        $sign = $x >= 0 ? 1 : -1;
        $x = abs($x);

        $a1 = 0.254829592;
        $a2 = -0.284496736;
        $a3 = 1.421413741;
        $a4 = -1.453152027;
        $a5 = 1.061405429;
        $p = 0.3275911;

        $t = 1 / (1 + $p * $x);
        $y = 1 - (((((($a5 * $t + $a4) * $t) + $a3) * $t + $a2) * $t + $a1) * $t * exp(-$x * $x));

        return $sign * $y;
    }


    protected function zGenericByDiasCampos(
        string $archivo,
        string $sexo,
        int $edadDias,
        float $valor,
        string $campoSexo,
        string $campoDenominador,
        string $campoMediana,
        string $campoL,
        string $campoS
    ): ?float {
        $rows = $this->filtrarPorSexoCampo($this->loadJson($archivo), $sexo, $campoSexo);

        if (empty($rows)) {
            return null;
        }

        $row = $this->buscarFilaDenominadorEntero($rows, $campoDenominador, $edadDias)
            ?? $this->nearestCampo($rows, $edadDias, $campoDenominador);

        if (!$row) {
            return null;
        }

        return $this->calcZLms(
            $valor,
            $row[$campoMediana] ?? null,
            $row[$campoL] ?? null,
            $row[$campoS] ?? null
        );
    }

    protected function zGenericByMesesCampos(
        string $archivo,
        string $sexo,
        int $edadDias,
        float $valor,
        string $campoSexo,
        string $campoDenominador,
        string $campoMediana,
        string $campoL,
        string $campoS
    ): ?float {
        $rows = $this->filtrarPorSexoCampo($this->loadJson($archivo), $sexo, $campoSexo);

        if (empty($rows)) {
            return null;
        }

        $mesesExactos = $edadDias / 30.4375;
        $mes = (int) floor($mesesExactos);
        $fraccion = $mesesExactos - $mes;

        $row1 = $this->buscarFilaDenominadorEntero($rows, $campoDenominador, $mes)
            ?? $this->nearestCampo($rows, $mes, $campoDenominador);

        if (!$row1) {
            return null;
        }

        $row2 = $this->buscarFilaDenominadorEntero($rows, $campoDenominador, $mes + 1) ?? $row1;

        $mediana = $this->interpolar(
            $this->numeroZscore($row1[$campoMediana] ?? null),
            $this->numeroZscore($row2[$campoMediana] ?? null),
            $fraccion
        );

        $l = $this->interpolar(
            $this->numeroZscore($row1[$campoL] ?? null),
            $this->numeroZscore($row2[$campoL] ?? null),
            $fraccion
        );

        $s = $this->interpolar(
            $this->numeroZscore($row1[$campoS] ?? null),
            $this->numeroZscore($row2[$campoS] ?? null),
            $fraccion
        );

        return $this->calcZLms($valor, $mediana, $l, $s);
    }

    protected function filtrarPorSexoCampo(array $rows, string $sexo, string $campoSexo): array
    {
        return array_values(array_filter($rows, function ($row) use ($sexo, $campoSexo) {
            if (! is_array($row)) {
                return false;
            }

            return strtoupper(trim((string)($row[$campoSexo] ?? ''))) === $sexo;
        }));
    }

    protected function buscarFilaDenominadorEntero(array $rows, string $campo, int $valor): ?array
    {
        foreach ($rows as $row) {
            $denominador = $this->numeroZscore($row[$campo] ?? null);

            if ($denominador !== null && (int) $denominador === $valor) {
                return $row;
            }
        }

        return null;
    }

    protected function nearestCampo(array $rows, $target, string $campo): ?array
    {
        $best = null;
        $bestDistance = null;

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $value = $this->numeroZscore($row[$campo] ?? null);

            if ($value === null) {
                continue;
            }

            $distance = abs($value - (float) $target);

            if ($best === null || $distance < $bestDistance) {
                $best = $row;
                $bestDistance = $distance;
            }
        }

        return $best;
    }

    protected function calcZLms(float $valor, $mediana, $l, $s): ?float
    {
        $mediana = $this->numeroZscore($mediana);
        $l = $this->numeroZscore($l);
        $s = $this->numeroZscore($s);

        if ($valor <= 0 || $mediana === null || $mediana <= 0 || $l === null || $s === null || $s == 0.0) {
            return null;
        }

        if (abs($l) < 0.0000001) {
            $z = log($valor / $mediana) / $s;
        } else {
            $z = (pow($valor / $mediana, $l) - 1) / ($l * $s);
        }

        return $this->valoresExtremos(round($z, 2));
    }

    protected function numeroZscore($valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        $valor = str_replace(',', '.', trim((string) $valor));

        return is_numeric($valor) ? (float) $valor : null;
    }

    protected function interpolar(?float $a, ?float $b, float $fraccion): ?float
    {
        if ($a === null && $b === null) {
            return null;
        }

        if ($a === null) {
            return $b;
        }

        if ($b === null) {
            return $a;
        }

        return $a + (($b - $a) * $fraccion);
    }




    protected function normalizarMetodoMedicionTalla($valor): ?string
    {
        $valor = strtolower(trim((string) $valor));

        $valor = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $valor
        );

        $valor = str_replace([' ', '-'], '_', $valor);

        // Regla solicitada: si no viene informado, por defecto es de_pie.
        if ($valor === '') {
            return 'de_pie';
        }

        if (in_array($valor, ['de_pie', 'pie', 'parado', 'bipedestacion'], true)) {
            return 'de_pie';
        }

        if (in_array($valor, ['acostado', 'acostada', 'decubito', 'longitud'], true)) {
            return 'acostado';
        }

        return null;
    }

    protected function ajustarTallaPorMetodoMedicion(?float $talla, ?int $edadDias, ?string $metodoMedicionTalla): ?float
    {
        if ($talla === null || $edadDias === null || $metodoMedicionTalla === null) {
            return $talla;
        }

        if ($edadDias <= 730 && $metodoMedicionTalla === 'de_pie') {
            return round($talla + 0.7, 1);
        }

        if ($edadDias > 730 && $metodoMedicionTalla === 'acostado') {
            return round($talla - 0.7, 1);
        }

        return $talla;
    }
} // Fin de la clase AntropometriaZscoreService

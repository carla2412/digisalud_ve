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
            'zte'                         => null,
            'zimce'                       => null,
            'zpt'                         => null,
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

        $resultado['zpe'] = $this->zEdadPeso($sexo, $edadDias, $peso);
        $resultado['zte'] = $this->zEdadTalla($sexo, $edadDias, $talla);

        if ($imc !== null) {
            $resultado['zimce'] = $this->zEdadImc($sexo, $edadDias, $imc);
        }

        if ($edadDias <= 1856) {
            $resultado['zpt'] = $this->zPesoTalla($sexo, $talla, $peso);
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
        if ($edadDias <= 1856) {
            return $this->zGenericByDias('zpe_dias.json', $sexo, $edadDias, $peso);
        }

        if ($edadDias <= 3653) {
            return $this->zGenericByMeses('zpe_meses.json', $sexo, $edadDias, $peso);
        }

        return null;
    }

    protected function zEdadTalla(string $sexo, int $edadDias, float $talla): ?float
    {
        if ($edadDias <= 1856) {
            return $this->zGenericByDias('zte_dias.json', $sexo, $edadDias, $talla);
        }

        if ($edadDias <= 6939) {
            $archivo = $sexo === 'M'
                ? 'zte_meses.json'
                : 'zte_meses_parte2.json';

            return $this->zGenericByMeses($archivo, $sexo, $edadDias, $talla);
        }

        return null;
    }

    protected function zEdadImc(string $sexo, int $edadDias, float $imc): ?float
    {
        if ($edadDias <= 1856) {
            return $this->zGenericByDias('zimce_dias.json', $sexo, $edadDias, $imc);
        }

        if ($edadDias <= 6939) {
            return $this->zGenericByMeses('zimce_meses.json', $sexo, $edadDias, $imc);
        }

        return null;
    }

    protected function zPesoTalla(string $sexo, float $talla, float $peso): ?float
    {
        $archivo = $talla >= 65 ? 'zpeso_talla.json' : 'zpeso_talla2.json';

        $rows = $this->filtrarPorSexo($this->loadJson($archivo), $sexo);

        if (empty($rows)) {
            return null;
        }

        $row = $this->nearest($rows, $talla, ['talla', 'length', 'height', 'cm']);

        if (!$row) {
            return null;
        }

        return $this->calcZ($peso, $row);
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
}
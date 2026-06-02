<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\JornadaModel;

/**
 * ReportesAntropometriaController
 *
 * Maneja los 3 reportes detallados de antropometría:
 *   - adultos        → jornadas/{id}/reportes/antropometria/adultos
 *   - menores-19     → jornadas/{id}/reportes/antropometria/menores-19
 *   - embarazadas    → jornadas/{id}/reportes/antropometria/embarazadas
 *
 * Fuente de datos: pesquisa_evaluaciones + pesquisa_resultados + pesquisa_items
 * La interpretación combinada se calcula en PHP usando los mismos umbrales
 * del sistema legado (lista_resul_antropo2.php, lista_resul_antropo.php,
 * lista_resul_antropo3.php).
 */
class ReportesAntropometriaController extends BaseController
{
    // =========================================================================
    // IDs de ítems en pesquisa_items (tipo_pesquisa_id = 1)
    // Códigos según AntropometriaItemsSeeder y dump SQL
    // =========================================================================
    private const ITEM_CODIGOS = [
        'peso'                       => 'peso',
        'talla'                      => 'talla',
        'imc'                        => 'imc',
        'circ_cintura'               => 'circ_cintura',
        'edema'                      => 'edema',
        'zpe'                        => 'zpe',
        'zte'                        => 'zte',
        'zpt'                        => 'zpt',
        'zimce'                      => 'zimce',
        'circ_cefalica'              => 'circ_cefalica',
        'circ_brazo_izq'             => 'circ_brazo_izq',
        'pliegue_tricipital'         => 'pliegue_tricipital',
        'pliegue_subescapular'       => 'pliegue_subescapular',
        'grupo_edad_reporte'         => 'grupo_edad_reporte',
        'clasificacion_imc_talla'    => 'clasificacion_imc_talla',
        'estado_nutricional_agregado' => 'estado_nutricional_agregado',
        'edad_dias_medicion'         => 'edad_dias_medicion',
        'embarazada'                 => 'embarazada',
        'embarazo_fum'               => 'embarazo_fum',
        'embarazo_fecha_eco'         => 'embarazo_fecha_eco',
        'embarazo_semanas_eco'       => 'embarazo_semanas_eco',
        'embarazo_semanas'           => 'embarazo_semanas',
        'embarazo_imc_pregestacional' => 'embarazo_imc_pregestacional',
        'embarazo_ganancia_kg'       => 'embarazo_ganancia_kg',
        'remision'                   => 'remision',
        'metodo_medicion_talla'      => 'metodo_medicion_talla',
    ];

    // =========================================================================
    // ADULTOS  (≥ 19 años, no embarazadas)
    // =========================================================================

    public function adultos(int $jornadaId): string
    {
        $jornada = $this->obtenerJornada($jornadaId);
        $itemMap  = $this->obtenerMapaItems();
        $filas    = $this->obtenerFilasAntropometria($jornadaId, $itemMap);

        $datos = [];
        $semaforo = ['verde' => 0, 'amarillo' => 0, 'naranja' => 0, 'rojo' => 0, 'gris' => 0];
        $contadores = ['total' => 0, 'masculinos' => 0, 'femeninas' => 0, 'adulto_19_60' => 0, 'adulto_mayor' => 0];

        foreach ($filas as $fila) {
            $edad_dias = (float) ($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));

            // Solo adultos ≥ 19 años (6939 días), no embarazadas
            if ($edad_dias <= 6939 || $embarazada === '1' || $embarazada === 's') {
                continue;
            }

            $imc      = (float) ($fila['imc'] ?? 0);
            $cintura  = (float) ($fila['circ_cintura'] ?? 0);
            $talla    = (float) ($fila['talla'] ?? 0);
            $sexo     = strtoupper(trim((string)($fila['sexo'] ?? 'M')));

            [$interp, $clase] = $this->interpretarAdulto($imc, $cintura, $talla, $sexo, $edad_dias);

            $semaforo[$clase]++;
            $contadores['total']++;
            if ($sexo === 'M') $contadores['masculinos']++;
            if ($sexo === 'F') $contadores['femeninas']++;
            if ($edad_dias > 6939 && $edad_dias <= 21914) $contadores['adulto_19_60']++;
            if ($edad_dias > 21914) $contadores['adulto_mayor']++;

            $datos[] = array_merge($fila, [
                '_interpretacion' => $interp,
                '_clase'          => $clase,
                '_sexo'           => $sexo,
            ]);
        }

        return view('reportes/antropometria/adultos', [
            'jornada'     => $jornada,
            'jornadaId'   => $jornadaId,
            'datos'       => $datos,
            'semaforo'    => $semaforo,
            'contadores'  => $contadores,
        ]);
    }

    // =========================================================================
    // MENORES DE 19 AÑOS
    // =========================================================================

    public function menores19(int $jornadaId): string
    {
        $jornada = $this->obtenerJornada($jornadaId);
        $itemMap  = $this->obtenerMapaItems();
        $filas    = $this->obtenerFilasAntropometria($jornadaId, $itemMap);

        $datos = [];
        $semaforo = ['verde' => 0, 'amarillo' => 0, 'naranja' => 0, 'rojo' => 0, 'gris' => 0];
        $contadores = ['total' => 0, 'masculinos' => 0, 'femeninas' => 0, 'menores_5' => 0, 'entre_5_19' => 0];

        foreach ($filas as $fila) {
            $edad_dias = (float) ($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));

            // Solo menores de 19 años (≤ 6939 días), no embarazadas
            if ($edad_dias <= 0 || $edad_dias > 6939 || $embarazada === '1' || $embarazada === 's') {
                continue;
            }

            $sexo    = strtoupper(trim((string)($fila['sexo'] ?? 'M')));
            $zpt   = ($fila['zpt'] ?? null) !== null && $fila['zpt'] !== '' ? (float)$fila['zpt'] : null;
            $zte   = ($fila['zte'] ?? null) !== null && $fila['zte'] !== '' ? (float)$fila['zte'] : null;
            $zimce = ($fila['zimce'] ?? null) !== null && $fila['zimce'] !== '' ? (float)$fila['zimce'] : null;
            $zpe   = ($fila['zpe'] ?? null) !== null && $fila['zpe'] !== '' ? (float)$fila['zpe'] : null;

            [$interp_zimce_zte, $interp_zpt_zte, $clase] = $this->interpretarMenor(
                $edad_dias,
                $zpt,
                $zte,
                $zimce,
                $zpe,
                (float)($fila['peso'] ?? 0),
                (float)($fila['talla'] ?? 0)
            );

            $semaforo[$clase]++;
            $contadores['total']++;
            if ($sexo === 'M') $contadores['masculinos']++;
            if ($sexo === 'F') $contadores['femeninas']++;
            if ($edad_dias > 0 && $edad_dias <= 1856)  $contadores['menores_5']++;
            if ($edad_dias > 1856 && $edad_dias <= 6939) $contadores['entre_5_19']++;

            $datos[] = array_merge($fila, [
                '_interp_zimce_zte' => $interp_zimce_zte,
                '_interp_zpt_zte'   => $interp_zpt_zte,
                '_clase'            => $clase,
                '_sexo'             => $sexo,
            ]);
        }

        return view('reportes/antropometria/menores_19', [
            'jornada'    => $jornada,
            'jornadaId'  => $jornadaId,
            'datos'      => $datos,
            'semaforo'   => $semaforo,
            'contadores' => $contadores,
        ]);
    }

    // =========================================================================
    // EMBARAZADAS
    // =========================================================================

    public function embarazadas(int $jornadaId): string
    {
        $jornada = $this->obtenerJornada($jornadaId);
        $itemMap  = $this->obtenerMapaItems();
        $filas    = $this->obtenerFilasAntropometria($jornadaId, $itemMap);

        $datos = [];
        $semaforo = ['verde' => 0, 'rojo' => 0, 'gris' => 0];
        $contadores = ['total' => 0];

        foreach ($filas as $fila) {
            $edad_dias  = (float)($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));
            $fum        = trim((string)($fila['embarazo_fum'] ?? ''));
            $fecha_eco  = trim((string)($fila['embarazo_fecha_eco'] ?? ''));
            $sem_eco    = (float)($fila['embarazo_semanas_eco'] ?? 0);

            // Solo mujeres con datos de embarazo
            $sexo = strtoupper(trim((string)($fila['sexo'] ?? 'M')));
            $tieneEmbarazo = ($embarazada === '1' || $embarazada === 's')
                || $fum !== '' || $fecha_eco !== '' || $sem_eco > 0;

            if ($sexo !== 'F' || !$tieneEmbarazo || $edad_dias > 18263) {
                continue;
            }

            $imc_preg   = (float)($fila['embarazo_imc_pregestacional'] ?? 0);
            $ganancia   = (float)($fila['embarazo_ganancia_kg'] ?? 0);
            $fecha_eval = trim((string)($fila['fecha_evaluacion'] ?? ''));

            // Calcular semanas gestación
            $semanas = $this->calcularSemanasGestacion($fum, $fecha_eco, $sem_eco, $fecha_eval);

            [$interp, $clase] = $this->interpretarEmbarazada($imc_preg, $ganancia, $semanas);

            $semaforo[$clase]++;
            $contadores['total']++;

            $datos[] = array_merge($fila, [
                '_interpretacion' => $interp,
                '_clase'          => $clase,
                '_sexo'           => $sexo,
                '_semanas_calc'   => $semanas,
                '_imc_preg'       => $imc_preg > 0 ? number_format($imc_preg, 1) : '—',
                '_ganancia'       => $ganancia != 0 ? number_format($ganancia, 1) : '—',
            ]);
        }

        return view('reportes/antropometria/embarazadas', [
            'jornada'    => $jornada,
            'jornadaId'  => $jornadaId,
            'datos'      => $datos,
            'semaforo'   => $semaforo,
            'contadores' => $contadores,
        ]);
    }

    // =========================================================================
    // EXPORTAR EXCEL — los 3 tipos usan PhpSpreadsheet
    // URL: jornadas/{id}/reportes/antropometria/{tipo}/excel
    // =========================================================================

    public function exportarExcel(int $jornadaId, string $tipo): void
    {
        $metodo = match ($tipo) {
            'adultos'     => 'adultos',
            'menores-19'  => 'menores19',
            'embarazadas' => 'embarazadas',
            default       => null,
        };

        if (!$metodo) {
            redirect()->to(site_url("jornadas/{$jornadaId}/reportes"))->send();
            return;
        }

        $jornada  = $this->obtenerJornada($jornadaId);
        $itemMap  = $this->obtenerMapaItems();
        $filas    = $this->obtenerFilasAntropometria($jornadaId, $itemMap);
        $nombre   = $jornada['nombre_jornada'] ?? "Jornada_{$jornadaId}";

        match ($tipo) {
            'adultos'     => $this->generarExcelAdultos($filas, $nombre, $jornadaId),
            'menores-19'  => $this->generarExcelMenores($filas, $nombre, $jornadaId),
            'embarazadas' => $this->generarExcelEmbarazadas($filas, $nombre, $jornadaId),
        };
    }

    // =========================================================================
    // LÓGICA DE INTERPRETACIÓN — ADULTOS
    // Basada en lista_resul_antropo2.php (sistema legado)
    // Combina IMC + Circunferencia de Cintura según sexo y grupo etario
    // =========================================================================

    private function interpretarAdulto(
        float $imc,
        float $cintura,
        float $talla,
        string $sexo,
        float $edad_dias
    ): array {
        if ($imc <= 0 || $cintura <= 0 || $talla <= 0) {
            return ['Revisar datos', 'gris'];
        }

        $es_adulto_mayor = $edad_dias > 21914; // > 60 años

        if ($es_adulto_mayor) {
            return $this->interpretarAdultoMayor($imc, $cintura, $sexo);
        }

        // Adulto 19-60 años
        if ($sexo === 'M') {
            return $this->interpretarAdultoMasculino($imc, $cintura, $talla);
        }

        return $this->interpretarAdultoFemenino($imc, $cintura, $talla);
    }

    private function interpretarAdultoMasculino(float $imc, float $cintura, float $talla): array
    {
        if ($talla < 124.0 || $talla > 202.05) return ['Revisar datos de talla', 'gris'];
        // Umbrales masculinos (cm): riesgo < 94, alto ≥ 94 ≤ 101.9, muy alto > 101.9
        if ($imc < 12.00)                                     return ['Revisar datos (IMC)', 'gris'];
        if ($imc >= 12.00 && $imc <= 16.00)                   return ['Desnutrición severa con ' . $this->riesgoCintMasc($cintura), 'rojo'];
        if ($imc > 16.00 && $imc <= 16.99)                    return ['Desnutrición moderada con ' . $this->riesgoCintMasc($cintura), $cintura >= 94 ? 'rojo' : 'naranja'];
        if ($imc > 16.99 && $imc <= 18.49)                    return ['Peso bajo con ' . $this->riesgoCintMasc($cintura), $cintura > 101.9 ? 'rojo' : ($cintura >= 94 ? 'naranja' : 'amarillo')];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura < 94)   return ['Peso adecuado con riesgo de ECNT bajo', 'verde'];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura >= 94 && $cintura <= 101.9) return ['Peso adecuado con riesgo de ECNT moderado', 'naranja'];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura > 101.9) return ['Peso adecuado con riesgo de ECNT alto', 'rojo'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura < 94)   return ['Sobrepeso con riesgo de ECNT bajo', 'amarillo'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura >= 94 && $cintura <= 101.9) return ['Sobrepeso con riesgo de ECNT moderado', 'naranja'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura > 101.9) return ['Sobrepeso con riesgo de ECNT alto', 'rojo'];
        if ($imc > 29.99 && $imc <= 39.99 && $cintura <= 101.9) return ['Obesidad con riesgo incrementado', 'naranja'];
        if ($imc > 29.99 && $imc <= 39.99 && $cintura > 101.9) return ['Obesidad con riesgo incrementado sustancialmente', 'rojo'];
        if ($imc > 39.99 && $imc <= 80.00)                    return ['Obesidad severa con riesgo incrementado sustancialmente', 'rojo'];
        return ['Revisar datos', 'gris'];
    }

    private function riesgoCintMasc(float $c): string
    {
        if ($c < 94)              return 'riesgo de ECNT bajo';
        if ($c <= 101.9)          return 'riesgo de ECNT moderado';
        if ($c <= 151.8)          return 'riesgo de ECNT alto';
        return 'revisar circunferencia';
    }

    private function interpretarAdultoFemenino(float $imc, float $cintura, float $talla): array
    {
        if ($talla < 124.0 || $talla > 202.05) return ['Revisar datos de talla', 'gris'];
        // Umbrales femeninos (cm): riesgo < 80, alto ≥ 80 ≤ 87.9, muy alto > 87.9
        if ($imc < 12.00)                                      return ['Revisar datos (IMC)', 'gris'];
        if ($imc >= 12.00 && $imc <= 16.00)                    return ['Desnutrición severa con ' . $this->riesgoCintFem($cintura), 'rojo'];
        if ($imc > 16.00 && $imc <= 16.99)                     return ['Desnutrición moderada con ' . $this->riesgoCintFem($cintura), $cintura > 87.9 ? 'rojo' : 'naranja'];
        if ($imc > 16.99 && $imc <= 18.49 && $cintura < 80)   return ['Peso bajo con riesgo de ECNT bajo', 'amarillo'];
        if ($imc > 16.99 && $imc <= 18.49 && $cintura >= 80 && $cintura <= 87.9) return ['Peso bajo con riesgo de ECNT moderado', 'naranja'];
        if ($imc > 16.99 && $imc <= 18.49 && $cintura > 87.9) return ['Peso bajo con riesgo de ECNT alto', 'rojo'];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura < 80)   return ['Peso adecuado con riesgo de ECNT bajo', 'verde'];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura >= 80 && $cintura <= 87.9) return ['Peso adecuado con riesgo de ECNT moderado', 'naranja'];
        if ($imc > 18.49 && $imc <= 24.99 && $cintura > 87.9) return ['Peso adecuado con riesgo de ECNT alto', 'rojo'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura < 80)   return ['Sobrepeso con riesgo de ECNT bajo', 'amarillo'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura >= 80 && $cintura <= 87.9) return ['Sobrepeso con riesgo de ECNT moderado', 'naranja'];
        if ($imc > 24.99 && $imc <= 29.99 && $cintura > 87.9) return ['Sobrepeso con riesgo de ECNT alto', 'rojo'];
        if ($imc > 29.99 && $imc <= 39.99 && $cintura <= 87.9) return ['Obesidad con riesgo incrementado', 'naranja'];
        if ($imc > 29.99 && $imc <= 39.99 && $cintura > 87.9) return ['Obesidad con riesgo incrementado sustancialmente', 'rojo'];
        if ($imc > 39.99 && $imc <= 80.00)                     return ['Obesidad severa con riesgo incrementado sustancialmente', 'rojo'];
        return ['Revisar datos', 'gris'];
    }

    private function riesgoCintFem(float $c): string
    {
        if ($c < 80)    return 'riesgo de ECNT bajo';
        if ($c <= 87.9) return 'riesgo de ECNT moderado';
        if ($c <= 147.3) return 'riesgo de ECNT alto';
        return 'revisar circunferencia';
    }

    private function interpretarAdultoMayor(float $imc, float $cintura, string $sexo): array
    {
        // Adulto mayor (> 60 años): criterios OPS/OMS ajustados
        $umbralCint = $sexo === 'M' ? 94.0 : 80.0;
        $riesgoCint = $cintura >= $umbralCint ? 'riesgo de ECNT elevado' : 'riesgo de ECNT bajo';

        if ($imc < 19.0)              return ["Desnutrido con {$riesgoCint}", 'rojo'];
        if ($imc >= 19.0 && $imc <= 22.99) return ["Delgado con {$riesgoCint}", $cintura >= $umbralCint ? 'naranja' : 'amarillo'];
        if ($imc >= 23.0 && $imc <= 27.99) return ["Peso adecuado con {$riesgoCint}", $cintura >= $umbralCint ? 'naranja' : 'verde'];
        if ($imc >= 28.0 && $imc <= 31.99) return ["Sobrepeso con {$riesgoCint}", $cintura >= $umbralCint ? 'rojo' : 'amarillo'];
        if ($imc >= 32.0)             return ["Obesidad con {$riesgoCint}", 'rojo'];
        return ['Revisar datos', 'gris'];
    }

    // =========================================================================
    // LÓGICA DE INTERPRETACIÓN — MENORES DE 19 AÑOS
    // Basada en lista_resul_antropo.php e incl_reporte_detallado/
    // =========================================================================

    private function interpretarMenor(
        float $edad_dias,
        ?float $zpt,
        ?float $zte,
        ?float $zimce,
        ?float $zpe,
        float $peso,
        float $talla
    ): array {
        // Interpretación ZIMCE/ZTE (combinada 1)
        $interp1 = $this->interpretarZimceZte($zimce, $zte);
        // Interpretación ZPT/ZTE para < 5 años (combinada 2)
        $interp2 = '';
        if ($edad_dias <= 1856 && $zpt !== null && $zte !== null && $peso > 0 && $talla > 0) {
            $interp2 = $this->interpretarZptZte($zpt, $zte);
        }

        // Clase semáforo basada en la peor clasificación
        $clase = $this->claseParaMenor($zimce, $zte, $zpt, $zpe, $edad_dias);

        return [$interp1, $interp2, $clase];
    }

    private function interpretarZimceZte(?float $zimce, ?float $zte): string
    {
        if ($zimce === null || $zte === null) return 'Revisar datos';

        // Talla
        $talla_cat = match (true) {
            $zte < -3.0  => 'Talla muy baja',
            $zte < -2.0  => 'Talla baja',
            $zte <= 3.0  => 'Talla adecuada',
            default      => 'Talla alta',
        };

        // IMC/Edad
        $imc_cat = match (true) {
            $zimce < -3.0  => 'Desnutrición severa',
            $zimce < -2.0  => 'Desnutrición',
            $zimce < -1.0  => 'Riesgo de desnutrición',
            $zimce <= 1.0  => 'Peso adecuado',
            $zimce <= 2.0  => 'Riesgo de sobrepeso',
            $zimce <= 3.0  => 'Sobrepeso',
            default        => 'Obesidad',
        };

        return "{$imc_cat} / {$talla_cat}";
    }

    private function interpretarZptZte(?float $zpt, ?float $zte): string
    {
        if ($zpt === null || $zte === null) return '';

        $peso_cat = match (true) {
            $zpt < -3.0  => 'Emaciación severa',
            $zpt < -2.0  => 'Emaciación',
            $zpt < -1.0  => 'Riesgo de emaciación',
            $zpt <= 1.0  => 'Peso adecuado/talla',
            $zpt <= 2.0  => 'Posible riesgo sobrepeso',
            default      => 'Sobrepeso/talla',
        };

        $talla_cat = match (true) {
            $zte < -3.0 => 'talla muy baja',
            $zte < -2.0 => 'talla baja',
            $zte <= 3.0 => 'talla adecuada',
            default     => 'talla alta',
        };

        return "{$peso_cat} / {$talla_cat}";
    }

    private function claseParaMenor(?float $zimce, ?float $zte, ?float $zpt, ?float $zpe, float $edad_dias): string
    {
        // Usar ZIMCE como eje principal
        if ($zimce === null) return 'gris';

        if ($zimce < -3.0 || ($zte !== null && $zte < -3.0)) return 'rojo';
        if ($zimce < -2.0 || ($zte !== null && $zte < -2.0)) return 'naranja';
        if ($zimce < -1.0 || ($zte !== null && $zte < -2.0)) return 'amarillo';
        if ($zimce > 3.0)  return 'rojo';
        if ($zimce > 2.0)  return 'naranja';
        if ($zimce > 1.0)  return 'amarillo';
        return 'verde';
    }

    // =========================================================================
    // LÓGICA DE INTERPRETACIÓN — EMBARAZADAS
    // Basada en lista_resul_antropo3.php — IOM/OMS ganancia de peso gestacional
    // =========================================================================

    private function interpretarEmbarazada(float $imc_preg, float $ganancia, int $semanas): array
    {
        if ($imc_preg <= 0 || $ganancia == 0 || $semanas <= 3) {
            return ['Revisar datos o datos insuficientes', 'gris'];
        }

        // Rangos de ganancia de peso según IMC pregestacional (IOM 2009)
        // [límite inferior, límite superior] por semana o total a término
        // Se usan los límites de la tabla gestacional del sistema legado
        [$cat_imc, $inf, $sup] = $this->tablaGananciaGestacional($imc_preg, $semanas);

        if ($inf === null) {
            return ["Revisar datos de IMC pregestacional ({$imc_preg})", 'gris'];
        }

        if ($ganancia < $inf) {
            return ["{$cat_imc} con ganancia de peso insuficiente", 'rojo'];
        }
        if ($ganancia <= $sup) {
            return ["{$cat_imc} con ganancia de peso adecuada", 'verde'];
        }
        return ["{$cat_imc} con ganancia de peso excesiva", 'naranja'];
    }

    /**
     * Retorna [categoría_imc, límite_inferior, límite_superior]
     * Los límites son acumulados según semanas gestación (IOM 2009 simplificado).
     */
    private function tablaGananciaGestacional(float $imc_preg, int $semanas): array
    {
        // Ganancias totales recomendadas al término (IOM 2009)
        // Se interpolan linealmente según semanas
        $tablas = [
            'Bajo peso'      => ['cat' => 'Bajo peso',     'total_inf' => 12.5, 'total_sup' => 18.0],
            'Peso adecuado'  => ['cat' => 'Peso adecuado', 'total_inf' => 11.5, 'total_sup' => 16.0],
            'Sobrepeso'      => ['cat' => 'Sobrepeso',     'total_inf' =>  7.0, 'total_sup' => 11.5],
            'Obesidad'       => ['cat' => 'Obesidad',      'total_inf' =>  5.0, 'total_sup' =>  9.0],
        ];

        $clave = match (true) {
            $imc_preg < 18.5                         => 'Bajo peso',
            $imc_preg >= 18.5 && $imc_preg <= 24.9   => 'Peso adecuado',
            $imc_preg > 24.9  && $imc_preg <= 29.9   => 'Sobrepeso',
            $imc_preg > 29.9  && $imc_preg <= 80.0   => 'Obesidad',
            default                                   => null,
        };

        if (!$clave) return ['Revisar IMC', null, null];

        $t = $tablas[$clave];
        $sem_referencia = 40;
        $factor = max(0, min($semanas, $sem_referencia)) / $sem_referencia;

        return [
            $t['cat'],
            round($t['total_inf'] * $factor, 1),
            round($t['total_sup'] * $factor, 1),
        ];
    }

    private function calcularSemanasGestacion(
        string $fum,
        string $fecha_eco,
        float $sem_eco,
        string $fecha_eval
    ): int {
        if ($fum !== '' && $fecha_eval !== '') {
            try {
                $d1 = new \DateTime($fum);
                $d2 = new \DateTime($fecha_eval);
                return (int) floor($d1->diff($d2)->days / 7);
            } catch (\Exception $e) {
            }
        }

        if ($fecha_eco !== '' && $sem_eco > 0 && $fecha_eval !== '') {
            try {
                $d1 = new \DateTime($fecha_eco);
                $d2 = new \DateTime($fecha_eval);
                $semDesdeEco = (int) floor($d1->diff($d2)->days / 7);
                return (int) ($sem_eco + $semDesdeEco);
            } catch (\Exception $e) {
            }
        }

        return 0;
    }

    // =========================================================================
    // GENERADORES EXCEL (PhpSpreadsheet)
    // =========================================================================

    private function generarExcelAdultos(array $filas, string $nombreJornada, int $jornadaId): void
    {
        $datos = [];
        foreach ($filas as $fila) {
            $edad_dias  = (float)($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));
            if ($edad_dias <= 6939 || $embarazada === '1' || $embarazada === 's') continue;

            $imc     = (float)($fila['imc'] ?? 0);
            $cintura = (float)($fila['circ_cintura'] ?? 0);
            $talla   = (float)($fila['talla'] ?? 0);
            $sexo    = strtoupper(trim((string)($fila['sexo'] ?? 'M')));
            [$interp,] = $this->interpretarAdulto($imc, $cintura, $talla, $sexo, $edad_dias);
            $datos[] = array_merge($fila, ['_interpretacion' => $interp, '_sexo' => $sexo]);
        }

        $cabeceras = [
            'Semáforo',
            'Nombre',
            'Cédula',
            'Sexo',
            'Fecha Nacimiento',
            'Edad',
            'Interpretación Combinada',
            'Peso (kg)',
            'Peso (lb)',
            'Talla (cm)',
            'IMC',
            'C. Cintura (cm)',
            'Edema',
            'Remisión',
            'Observaciones',
            'Fecha Evaluación',
            'Jornada',
        ];

        $filas_xls = [['REPORTE ANTROPOMÉTRICO — ADULTOS'], ['Jornada: ' . $nombreJornada], [], $cabeceras];

        foreach ($datos as $d) {
            $filas_xls[] = [
                $this->etiquetaSemaforo($d['_clase'] ?? 'gris'),
                $d['nombre_completo'] ?? '',
                $d['id_digisalud'] ?? '',
                $d['_sexo'],
                $d['fecha_nacimiento'] ?? '',
                $this->formatearEdad((float)($d['edad_dias_medicion'] ?? 0)),
                $d['_interpretacion'],
                number_format((float)($d['peso'] ?? 0), 2),
                number_format((float)($d['peso'] ?? 0) * 2.20462, 1),
                number_format((float)($d['talla'] ?? 0), 1),
                number_format((float)($d['imc'] ?? 0), 2),
                number_format((float)($d['circ_cintura'] ?? 0), 1),
                ($d['edema'] ?? 0) ? 'Sí' : 'No',
                $d['remision'] ?? '',
                $d['observaciones'] ?? '',
                $d['fecha_evaluacion'] ?? '',
                $d['nombre_jornada'] ?? '',
            ];
        }

        $this->enviarExcel($filas_xls, "Reporte_Antro_Adultos_{$nombreJornada}");
    }

    private function generarExcelMenores(array $filas, string $nombreJornada, int $jornadaId): void
    {
        $datos = [];
        foreach ($filas as $fila) {
            $edad_dias  = (float)($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));
            if ($edad_dias <= 0 || $edad_dias > 6939 || $embarazada === '1' || $embarazada === 's') continue;

            $zpt   = $fila['zpt']   !== null ? (float)$fila['zpt']   : null;
            $zte   = $fila['zte']   !== null ? (float)$fila['zte']   : null;
            $zimce = $fila['zimce'] !== null ? (float)$fila['zimce'] : null;
            $zpe   = $fila['zpe']   !== null ? (float)$fila['zpe']   : null;
            [$i1, $i2,] = $this->interpretarMenor($edad_dias, $zpt, $zte, $zimce, $zpe, (float)($fila['peso'] ?? 0), (float)($fila['talla'] ?? 0));
            $datos[] = array_merge($fila, ['_interp_zimce_zte' => $i1, '_interp_zpt_zte' => $i2]);
        }

        $cabeceras = [
            'Semáforo',
            'Nombre',
            'Cédula',
            'Sexo',
            'Fecha Nacimiento',
            'Edad antro.',
            'Interp. ZIMCE/ZTE',
            'Interp. ZPT/ZTE',
            'Peso (kg)',
            'Peso (lb)',
            'Talla (cm)',
            'IMC',
            'ZP/T',
            'ZP/E',
            'ZT/E',
            'ZIMC/E',
            'Circ. Cefálica',
            'Circ. Brazo Izq',
            'Pliegue Tricipital',
            'Pliegue Subescapular',
            'Edema',
            'Remisión',
            'Observaciones',
            'Fecha Evaluación',
        ];

        $filas_xls = [['REPORTE ANTROPOMÉTRICO — MENORES DE 19 AÑOS'], ['Jornada: ' . $nombreJornada], [], $cabeceras];

        foreach ($datos as $d) {
            $filas_xls[] = [
                $this->etiquetaSemaforo($d['_clase'] ?? 'gris'),
                $d['nombre_completo'] ?? '',
                $d['id_digisalud'] ?? '',
                $d['sexo'] ?? '',
                $d['fecha_nacimiento'] ?? '',
                $this->formatearEdad((float)($d['edad_dias_medicion'] ?? 0)),
                $d['_interp_zimce_zte'],
                $d['_interp_zpt_zte'],
                number_format((float)($d['peso'] ?? 0), 2),
                number_format((float)($d['peso'] ?? 0) * 2.20462, 1),
                number_format((float)($d['talla'] ?? 0), 1),
                number_format((float)($d['imc'] ?? 0), 2),
                $d['zpt'] ?? '',
                $d['zpe'] ?? '',
                $d['zte'] ?? '',
                $d['zimce'] ?? '',
                $d['circ_cefalica'] ?? '',
                $d['circ_brazo_izq'] ?? '',
                $d['pliegue_tricipital'] ?? '',
                $d['pliegue_subescapular'] ?? '',
                ($d['edema'] ?? 0) ? 'Sí' : 'No',
                $d['remision'] ?? '',
                $d['observaciones'] ?? '',
                $d['fecha_evaluacion'] ?? '',
            ];
        }

        $this->enviarExcel($filas_xls, "Reporte_Antro_Menores19_{$nombreJornada}");
    }

    private function generarExcelEmbarazadas(array $filas, string $nombreJornada, int $jornadaId): void
    {
        $datos = [];
        foreach ($filas as $fila) {
            $edad_dias  = (float)($fila['edad_dias_medicion'] ?? 0);
            $embarazada = strtolower(trim((string)($fila['embarazada'] ?? '')));
            $fum        = trim((string)($fila['embarazo_fum'] ?? ''));
            $fecha_eco  = trim((string)($fila['embarazo_fecha_eco'] ?? ''));
            $sem_eco    = (float)($fila['embarazo_semanas_eco'] ?? 0);
            $sexo       = strtoupper(trim((string)($fila['sexo'] ?? 'M')));
            $tieneEmbarazo = ($embarazada === '1' || $embarazada === 's') || $fum !== '' || $fecha_eco !== '' || $sem_eco > 0;
            if ($sexo !== 'F' || !$tieneEmbarazo || $edad_dias > 18263) continue;

            $imc_preg = (float)($fila['embarazo_imc_pregestacional'] ?? 0);
            $ganancia = (float)($fila['embarazo_ganancia_kg'] ?? 0);
            $semanas  = $this->calcularSemanasGestacion($fum, $fecha_eco, $sem_eco, $fila['fecha_evaluacion'] ?? '');
            [$interp,] = $this->interpretarEmbarazada($imc_preg, $ganancia, $semanas);
            $datos[] = array_merge($fila, ['_interpretacion' => $interp, '_semanas' => $semanas]);
        }

        $cabeceras = [
            'Semáforo',
            'Nombre',
            'Cédula',
            'Sexo',
            'Fecha Nacimiento',
            'Edad',
            'Interpretación Combinada',
            'Peso (kg)',
            'Peso (lb)',
            'Talla (cm)',
            'Peso Pregestacional',
            'FUM',
            'Fecha Eco',
            'Semanas Gestación',
            'Ganancia Peso (kg)',
            'IMC Pregestacional',
            'Especialista',
            'Edema',
            'Observaciones',
            'Fecha Evaluación',
        ];

        $filas_xls = [['REPORTE ANTROPOMÉTRICO — EMBARAZADAS'], ['Jornada: ' . $nombreJornada], [], $cabeceras];

        foreach ($datos as $d) {
            $peso_preg = (float)($d['embarazo_imc_pregestacional'] ?? 0);
            $filas_xls[] = [
                $this->etiquetaSemaforo($d['_clase'] ?? 'gris'),
                $d['nombre_completo'] ?? '',
                $d['id_digisalud'] ?? '',
                $d['sexo'] ?? '',
                $d['fecha_nacimiento'] ?? '',
                $this->formatearEdad((float)($d['edad_dias_medicion'] ?? 0)),
                $d['_interpretacion'],
                number_format((float)($d['peso'] ?? 0), 2),
                number_format((float)($d['peso'] ?? 0) * 2.20462, 1),
                number_format((float)($d['talla'] ?? 0), 1),
                $d['embarazo_imc_pregestacional'] ?? '',
                $d['embarazo_fum'] ?? '',
                $d['embarazo_fecha_eco'] ?? '',
                $d['_semanas'] ?? '',
                number_format((float)($d['embarazo_ganancia_kg'] ?? 0), 1),
                $d['embarazo_imc_pregestacional'] ?? '',
                ($d['remision'] ?? '') !== '' ? 'Sí' : 'No',
                ($d['edema'] ?? 0) ? 'Sí' : 'No',
                $d['observaciones'] ?? '',
                $d['fecha_evaluacion'] ?? '',
            ];
        }

        $this->enviarExcel($filas_xls, "Reporte_Antro_Embarazadas_{$nombreJornada}");
    }

    private function enviarExcel(array $filas, string $nombreArchivo): void
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Colores semáforo
        $colores = [
            'Verde'    => 'FF00B140',
            'Amarillo' => 'FFFFC609',
            'Naranja'  => 'FFFF8724',
            'Rojo'     => 'FFE43312',
            'Gris'     => 'FFB1B0B0',
        ];

        $fila = 1;
        foreach ($filas as $row) {
            $col = 1;
            foreach ($row as $celda) {
                $cell = $sheet->getCellByColumnAndRow($col, $fila);
                $cell->setValue($celda);

                // Título
                if ($fila === 1 && $col === 1) {
                    $cell->getStyle()->getFont()->setBold(true)->setSize(13);
                }
                // Cabeceras
                $totalCols = count($filas[3] ?? []);
                if ($fila === 4) {
                    $cell->getStyle()->getFont()->setBold(true)->setColor(
                        (new \PhpOffice\PhpSpreadsheet\Style\Color(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE))
                    );
                    $cell->getStyle()->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB('FF101A61');
                }
                // Colorear col semáforo
                if ($col === 1 && $fila > 4 && isset($colores[$celda])) {
                    $cell->getStyle()->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()->setARGB($colores[$celda]);
                    $cell->getStyle()->getFont()->setBold(true);
                }
                $col++;
            }
            $fila++;
        }

        // Autowidth
        foreach (range(1, $sheet->getHighestColumn()) as $col) {
            $sheet->getColumnDimensionByColumn($col)->setAutoSize(true);
        }

        $nombre = preg_replace('/[^A-Za-z0-9_\-]/', '_', $nombreArchivo);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment; filename=\"{$nombre}.xlsx\"");
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // QUERY PRINCIPAL — obtiene todas las evaluaciones de antropometría
    // con sus resultados y datos del beneficiario
    // =========================================================================

    private function obtenerFilasAntropometria(int $jornadaId, array $itemMap): array
    {
        $db = db_connect();

        // Obtener evaluaciones de la jornada
        $evaluaciones = $db->table('pesquisa_evaluaciones AS pe')
            ->select('
                pe.id_evaluacion,
                pe.beneficiario_id,
                pe.fecha_evaluacion,
                pe.observaciones,
                b.nombres,
                b.apellidos,
                CONCAT(b.nombres, " ", b.apellidos) AS nombre_completo,
                b.id_digisalud,
                b.fecha_nacimiento,
                b.sexo,
                j.nombre_jornada
            ')
            ->join('beneficiarios AS b', 'b.id_beneficiario = pe.beneficiario_id')
            ->join('jornadas AS j', 'j.id_jornada = pe.jornada_id', 'left')
            ->where('pe.jornada_id', $jornadaId)
            ->where('pe.tipo_pesquisa_id', 1)
            ->where('pe.status_eval', 1)
            ->orderBy('nombre_completo', 'ASC')
            ->get()->getResultArray();

        if (empty($evaluaciones)) return [];

        // IDs de evaluaciones
        $evalIds = array_column($evaluaciones, 'id_evaluacion');

        // Obtener TODOS los resultados de una vez
        $resultados = $db->table('pesquisa_resultados AS pr')
            ->select('pr.evaluacion_id, pr.item_id, pr.valor_texto, pr.valor_numero, pr.valor_booleano, pr.valor_fecha, pi.codigo')
            ->join('pesquisa_items AS pi', 'pi.id_item = pr.item_id')
            ->whereIn('pr.evaluacion_id', $evalIds)
            ->get()->getResultArray();

        // Indexar por evaluacion_id → codigo → valor
        $resultadosMap = [];
        foreach ($resultados as $r) {
            $evalId = $r['evaluacion_id'];
            $codigo = $r['codigo'];
            $valor  = $r['valor_texto'] ?? $r['valor_numero'] ?? $r['valor_booleano'] ?? $r['valor_fecha'];
            $resultadosMap[$evalId][$codigo] = $valor;
        }

        // Combinar
        // Combinar
        $filas = [];

        $defaults = array_fill_keys(array_values(self::ITEM_CODIGOS), null);

        foreach ($evaluaciones as $eval) {
            $evalId = $eval['id_evaluacion'];

            // Garantiza que todas las claves esperadas existan,
            // aunque la evaluación no tenga ese resultado guardado.
            $vals = array_merge($defaults, $resultadosMap[$evalId] ?? []);

            // Calcular edad_dias_medicion si no está en resultados
            $edadDias = isset($vals['edad_dias_medicion']) && $vals['edad_dias_medicion'] !== null
                ? (float)$vals['edad_dias_medicion']
                : $this->calcularEdadDias($eval['fecha_nacimiento'] ?? '', $eval['fecha_evaluacion'] ?? '');

            $filas[] = array_merge($eval, $vals, [
                'edad_dias_medicion' => $edadDias,
            ]);
        }

        return $filas;
    }

    private function calcularEdadDias(string $fechaNac, string $fechaEval): float
    {
        if (!$fechaNac || !$fechaEval) return 0;
        try {
            $d1 = new \DateTime($fechaNac);
            $d2 = new \DateTime($fechaEval);
            return (float) $d1->diff($d2)->days;
        } catch (\Exception $e) {
            return 0;
        }
    }

    // =========================================================================
    // HELPERS
    // =========================================================================

    private function obtenerJornada(int $jornadaId): array
    {
        $model = new JornadaModel();
        $jornada = $model->find($jornadaId);
        if (!$jornada) {
            redirect()->to(site_url('jornadas'))->send();
            exit;
        }
        return $jornada;
    }

    private function obtenerMapaItems(): array
    {
        $db = db_connect();
        $items = $db->table('pesquisa_items')
            ->where('tipo_pesquisa_id', 1)
            ->where('status_item', 1)
            ->get()->getResultArray();
        $mapa = [];
        foreach ($items as $item) {
            $mapa[$item['id_item']] = $item;
        }
        return $mapa;
    }

    private function etiquetaSemaforo(string $clase): string
    {
        return match ($clase) {
            'verde'   => 'Verde',
            'amarillo' => 'Amarillo',
            'naranja' => 'Naranja',
            'rojo'    => 'Rojo',
            default   => 'Gris',
        };
    }

    private function formatearEdad(float $dias): string
    {
        if ($dias <= 0) return '—';
        $anios = floor($dias / 365.25);
        $meses = floor(($dias % 365.25) / 30.44);
        return "{$anios} a. {$meses} m.";
    }
}

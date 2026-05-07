<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;

/**
 * ═══════════════════════════════════════════════════════════════════
 * LabInterpretacionController
 * ═══════════════════════════════════════════════════════════════════
 *
 * Endpoint AJAX para el semáforo de anemia.
 *
 * GET /lab/semaforo?jornada_id=X  (o centro_id=X)
 *
 * Consulta pesquisa_resultados + pesquisa_evaluaciones + beneficiarios
 * para clasificar la hemoglobina de cada beneficiario evaluado en la
 * jornada/centro y retornar los conteos por categoría.
 *
 * ARCHIVO NUEVO — no modifica código existente.
 */
class LabInterpretacionController extends BaseController
{
    /**
     * GET /lab/semaforo
     *
     * Retorna JSON:
     * {
     *   ok: true,
     *   total_evaluados: N,
     *   semaforo: { verde: N, amarillo: N, naranja: N, rojo: N, gris: N }
     * }
     */
    public function semaforo(): ResponseInterface
    {
        $session = session();

        if (! $session->has('id_usuario') || ! $session->get('id_usuario')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['ok' => false, 'mensaje' => 'No autenticado.']);
        }

        $jornadaId = (int) $this->request->getGet('jornada_id');
        $centroId  = (int) $this->request->getGet('centro_id');

        if (! $jornadaId && ! $centroId) {
            return $this->response->setJSON([
                'ok' => false,
                'mensaje' => 'Se requiere jornada_id o centro_id.',
            ]);
        }

        $db = \Config\Database::connect();

        // ─── Obtener hemoglobina de cada beneficiario evaluado ───
        // pesquisa_items id_item para hemoglobina (tipo_pesquisa_id = 2, codigo = 'hemoglobina')
        $itemHb = $db->table('pesquisa_items')
            ->select('id_item')
            ->where('tipo_pesquisa_id', 2)
            ->where('codigo', 'hemoglobina')
            ->where('status_item', 1)
            ->get()->getRowArray();

        if (! $itemHb) {
            return $this->response->setJSON([
                'ok' => false,
                'mensaje' => 'No se encontró el item de hemoglobina en el catálogo.',
            ]);
        }

        $itemIdHb = (int) $itemHb['id_item'];

        // ─── Query: obtener evaluaciones de laboratorio de la jornada/centro ───
        $builder = $db->table('pesquisa_evaluaciones AS pe')
            ->select('
                pe.id_evaluacion,
                pe.beneficiario_id,
                b.fecha_nacimiento,
                b.sexo,
                pr.valor_numero AS hemoglobina,
                pr_emb.valor_texto AS embarazada
            ')
            ->join('beneficiarios AS b', 'b.id_beneficiario = pe.beneficiario_id')
            ->join('pesquisa_resultados AS pr', 'pr.evaluacion_id = pe.id_evaluacion AND pr.item_id = ' . $itemIdHb, 'left')
            ->where('pe.tipo_pesquisa_id', 2)
            ->where('pe.status_eval', 1);

        // Join para obtener el campo embarazada_lab
        $itemEmb = $db->table('pesquisa_items')
            ->select('id_item')
            ->where('tipo_pesquisa_id', 2)
            ->where('codigo', 'embarazada_lab')
            ->where('status_item', 1)
            ->get()->getRowArray();

        if ($itemEmb) {
            $builder->join(
                'pesquisa_resultados AS pr_emb',
                'pr_emb.evaluacion_id = pe.id_evaluacion AND pr_emb.item_id = ' . (int) $itemEmb['id_item'],
                'left'
            );
        } else {
            // Si no existe el campo, usar LEFT JOIN con tabla falsa para mantener el alias
            $builder->select('NULL AS embarazada', false);
        }

        if ($jornadaId) {
            $builder->where('pe.jornada_id', $jornadaId);
        } else {
            $builder->where('pe.centro_id', $centroId);
        }

        $evaluaciones = $builder->get()->getResultArray();

        // ─── Clasificar cada evaluación ───
        $contadores = [
            'verde'    => 0,
            'amarillo' => 0,
            'naranja'  => 0,
            'rojo'     => 0,
            'gris'     => 0,
        ];

        $hoy = new \DateTime();

        foreach ($evaluaciones as $eval) {
            $hb = $eval['hemoglobina'];
            $fechaNac   = $eval['fecha_nacimiento'] ?? null;
            $sexo       = strtoupper($eval['sexo'] ?? 'M');
            $embarazada = strtolower($eval['embarazada'] ?? 'n');

            // Calcular edad en días
            $edadDias = 0;
            if ($fechaNac) {
                $nacimiento = new \DateTime($fechaNac);
                $diff = $nacimiento->diff($hoy);
                $edadDias = (int) $diff->days;
            }

            $clasificacion = $this->clasificarAnemiaServer($hb, $edadDias, $sexo, $embarazada);

            switch ($clasificacion) {
                case 'normal':
                    $contadores['verde']++;
                    break;
                case 'leve':
                    $contadores['amarillo']++;
                    break;
                case 'moderada':
                    $contadores['naranja']++;
                    break;
                case 'severa':
                    $contadores['rojo']++;
                    break;
                default:
                    $contadores['gris']++;
                    break;
            }
        }

        return $this->response->setJSON([
            'ok'              => true,
            'total_evaluados' => count($evaluaciones),
            'semaforo'        => $contadores,
        ]);
    }

    // ══════════════════════════════════════════════════════════════
    // Clasificación server-side (réplica de la lógica JS)
    // ══════════════════════════════════════════════════════════════

    /**
     * Clasifica anemia server-side.
     *
     * @param mixed  $hb         Valor de hemoglobina (puede ser null)
     * @param int    $edadDias   Edad en días
     * @param string $sexo       'M' o 'F'
     * @param string $embarazada 's' o 'n'
     * @return string normal|leve|moderada|severa|revisar|sin_dato
     */
    private function clasificarAnemiaServer($hb, int $edadDias, string $sexo, string $embarazada): string
    {
        if ($hb === null || $hb === '') {
            return 'sin_dato';
        }

        $hb = (float) $hb;

        if ($edadDias <= 0) {
            return 'revisar';
        }

        if ($hb > 20 || ($hb > 0 && $hb < 3)) {
            return 'revisar';
        }

        // A. 183–1825 días
        if ($edadDias >= 183 && $edadDias <= 1825) {
            if ($hb >= 11.0 && $hb <= 20)  return 'normal';
            if ($hb >= 10.0 && $hb <= 10.9) return 'leve';
            if ($hb >= 7.0  && $hb <= 9.9)  return 'moderada';
            if ($hb >= 3.0  && $hb < 7.0)   return 'severa';
            return 'revisar';
        }

        // B. 1826–4382 días
        if ($edadDias >= 1826 && $edadDias <= 4382) {
            if ($hb >= 11.5 && $hb <= 20)   return 'normal';
            if ($hb >= 11.0 && $hb <= 11.4)  return 'leve';
            if ($hb >= 8.0  && $hb <= 10.9)  return 'moderada';
            if ($hb >= 3.0  && $hb < 8.0)    return 'severa';
            return 'revisar';
        }

        // C. 4383–5478 días
        if ($edadDias >= 4383 && $edadDias <= 5478) {
            if ($hb >= 12.0 && $hb <= 20)   return 'normal';
            if ($hb >= 11.0 && $hb <= 11.9)  return 'leve';
            if ($hb >= 8.0  && $hb <= 10.9)  return 'moderada';
            if ($hb >= 3.0  && $hb < 8.0)    return 'severa';
            return 'revisar';
        }

        // D. >= 5479 días
        if ($edadDias >= 5479) {
            // D.1 Masculino
            if ($sexo === 'M') {
                if ($hb >= 13.0 && $hb <= 20)   return 'normal';
                if ($hb >= 11.0 && $hb <= 12.9)  return 'leve';
                if ($hb >= 8.0  && $hb <= 10.9)  return 'moderada';
                if ($hb >= 3.0  && $hb < 8.0)    return 'severa';
                return 'revisar';
            }

            // D.2 Femenino embarazada
            if ($sexo === 'F' && $embarazada === 's') {
                if ($hb >= 11.0 && $hb <= 20)   return 'normal';
                if ($hb >= 10.0 && $hb <= 10.9)  return 'leve';
                if ($hb >= 7.0  && $hb <= 9.9)   return 'moderada';
                if ($hb >= 3.0  && $hb < 7.0)    return 'severa';
                return 'revisar';
            }

            // D.3 Femenino no embarazada
            if ($hb >= 12.0 && $hb <= 20)   return 'normal';
            if ($hb >= 11.0 && $hb <= 11.9)  return 'leve';
            if ($hb >= 8.0  && $hb <= 10.9)  return 'moderada';
            if ($hb >= 3.0  && $hb < 8.0)    return 'severa';
            return 'revisar';
        }

        // Menor de 6 meses
        return 'revisar';
    }
}
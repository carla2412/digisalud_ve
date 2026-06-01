<?php

namespace App\Controllers;

use App\Models\BeneficiariosModel;
use App\Models\JornadaBeneficiariosModel;
use App\Models\JornadaModel;
use App\Models\PesquisaEvaluacionModel;
use App\Models\PesquisaItemModel;
use App\Models\PesquisaResultadoModel;
use App\Models\RolesUsuariosContextoModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Modulo centralizado de cargas masivas.
 *
 * Permite gestionar plantillas y cargas por jornada. Las cargas de evaluaciones
 * se habilitan segun las pesquisas asociadas a la jornada seleccionada.
 *
 * Roles permitidos: 1, 2, 3 y 4.
 * - Roles 1 y 2: pueden ver jornadas de todas las organizaciones.
 * - Roles 3 y 4: solo jornadas de su organizacion y/o jornadas asignadas por contexto.
 */
class CargaMasivaController extends BaseController
{
    private const ROLES_PERMITIDOS = [1, 2, 3, 4];
    private const TIPO_ANTROPOMETRIA = 1;
    private const MAX_REGISTROS = 500;
    private const EDAD_DIAS_ADULTO = 6939;

    public function index()
    {
        if (! $this->usuarioPuedeGestionar()) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permisos para gestionar cargas masivas.');
        }

        return view('cargas_masivas/index', [
            'jornadas' => $this->obtenerJornadasPermitidas(),
            'plantillas' => $this->obtenerPlantillasDisponibles(),
            'tiposCarga' => $this->obtenerTiposCarga(),
        ]);
    }

    public function descargarPlantilla(string $codigo)
    {
        if (! $this->usuarioPuedeGestionar()) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permisos para descargar plantillas.');
        }

        $plantillas = $this->obtenerPlantillasDisponibles();

        if (! isset($plantillas[$codigo])) {
            return redirect()->back()->with('error', 'Plantilla no disponible.');
        }

        $archivo = $plantillas[$codigo]['archivo'];
        $path = FCPATH . 'assets/templates/' . $archivo;

        if (! file_exists($path)) {
            return redirect()->back()->with('error', 'Plantilla no encontrada en assets/templates.');
        }

        return $this->response->download($path, null)->setFileName($archivo);
    }

    public function jornadaInfo(int $jornadaId)
    {
        if (! $this->usuarioPuedeGestionar()) {
            return $this->response->setJSON([
                'ok' => false,
                'mensaje' => 'No tienes permisos para consultar esta informacion.',
            ]);
        }

        $jornada = $this->obtenerJornadaPermitida($jornadaId);

        if (! $jornada) {
            return $this->response->setJSON([
                'ok' => false,
                'mensaje' => 'La jornada no existe o no pertenece a tu organizacion.',
            ]);
        }

        return $this->response->setJSON([
            'ok' => true,
            'jornada' => $jornada,
            'pesquisas' => $this->normalizarPesquisas($jornada['pesquisas'] ?? ''),
            'tipos_carga' => $this->obtenerTiposCarga(),
        ]);
    }

    public function procesar(int $jornadaId, string $tipo)
    {
        if (! $this->usuarioPuedeGestionar()) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'No tienes permisos para gestionar cargas masivas.',
            ]);
        }

        $jornada = $this->obtenerJornadaPermitida($jornadaId);

        if (! $jornada) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'La jornada no existe o no pertenece a tu organizacion.',
            ]);
        }

        if ((int) ($jornada['status_jor'] ?? 0) !== 1) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'La jornada no esta activa. Solo se permiten cargas sobre jornadas activas.',
            ]);
        }

        $tiposCarga = $this->obtenerTiposCarga();

        if (! isset($tiposCarga[$tipo])) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Tipo de carga no soportado.',
            ]);
        }

        $pesquisas = $this->normalizarPesquisas($jornada['pesquisas'] ?? '');
        $tipoPesquisaRequerido = (int) $tiposCarga[$tipo]['tipo_pesquisa_id'];

        if ($tipoPesquisaRequerido > 0 && ! in_array($tipoPesquisaRequerido, $pesquisas, true)) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'La jornada seleccionada no tiene asociada la pesquisa requerida para esta carga.',
            ]);
        }

        return match ($tipo) {
            'antropometria' => $this->procesarAntropometria($jornadaId),
            default => $this->response->setJSON([
                'ok' => false,
                'error' => 'Esta carga todavia no esta implementada.',
            ]),
        };
    }

    private function obtenerPlantillasDisponibles(): array
    {
        return [
            'beneficiarios' => [
                'codigo' => 'beneficiarios',
                'nombre' => 'Beneficiarios',
                'descripcion' => 'Registro masivo de beneficiarios con direccion opcional.',
                'archivo' => 'plantilla_beneficiarios_digisalud.xlsx',
                'icono' => 'bi-people',
            ],
            'antropometria' => [
                'codigo' => 'antropometria',
                'nombre' => 'Antropometria',
                'descripcion' => 'Carga masiva de peso, talla, cintura y medidas opcionales.',
                'archivo' => 'plantilla_antropometria_digisalud.xlsx',
                'icono' => 'bi-person-standing',
            ],
        ];
    }

    private function obtenerTiposCarga(): array
    {
        return [
            'antropometria' => [
                'codigo' => 'antropometria',
                'nombre' => 'Antropometria',
                'descripcion' => 'Peso, talla, IMC, cintura y circunferencias.',
                'tipo_pesquisa_id' => self::TIPO_ANTROPOMETRIA,
                'plantilla' => 'antropometria',
                'icono' => 'antropometria2.svg',
            ],
            // Futuras integraciones:
            // 'laboratorio' => ['tipo_pesquisa_id' => 2, ...],
            // 'visual' => ['tipo_pesquisa_id' => 3, ...],
            // 'signos_vitales' => ['tipo_pesquisa_id' => 4, ...],
            // 'medicina_general' => ['tipo_pesquisa_id' => 5, ...],
            // 'vacunacion' => ['tipo_pesquisa_id' => 6, ...],
        ];
    }

    private function usuarioPuedeGestionar(): bool
    {
        return in_array((int) session('id_rol'), self::ROLES_PERMITIDOS, true);
    }

    private function obtenerIdsJornadasAsignadas(int $idUsuario): array
    {
        if ($idUsuario <= 0) {
            return [];
        }

        $rolesContextoModel = new RolesUsuariosContextoModel();
        $rows = $rolesContextoModel
            ->select('jornada_id')
            ->where('id_usuario', $idUsuario)
            ->where('tipo_contexto', 'JORNADA')
            ->where('status_urc', 1)
            ->where('jornada_id IS NOT NULL')
            ->findAll();

        $ids = array_map('intval', array_column($rows, 'jornada_id'));
        return array_values(array_unique(array_filter($ids)));
    }

    private function obtenerJornadasPermitidas(): array
    {
        $idUsuario = (int) session('id_usuario');
        $rol = (int) session('id_rol');
        $orgSesion = (int) session('organizacion_id');

        $model = new JornadaModel();
        $builder = $model
            ->select("jornadas.id_jornada, jornadas.nombre_jornada, jornadas.fecha_inicio, jornadas.status_jor, jornadas.organizacion_id,
                      org.nombre_org, inst.nombre_institucion,
                      GROUP_CONCAT(DISTINCT tpa.idtipo_pesquisa ORDER BY tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
            ->join('organizacion AS org', 'org.id_organizacion = jornadas.organizacion_id', 'left')
            ->join('instituciones AS inst', 'inst.id_institucion = jornadas.institucion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->where('jornadas.status_jor !=', 0)
            ->groupBy('jornadas.id_jornada')
            ->orderBy('jornadas.fecha_inicio', 'DESC');

        if (! in_array($rol, [1, 2], true)) {
            $idsJornadasAsignadas = $this->obtenerIdsJornadasAsignadas($idUsuario);

            $builder->groupStart()
                ->where('jornadas.organizacion_id', $orgSesion);

            if (! empty($idsJornadasAsignadas)) {
                $builder->orWhereIn('jornadas.id_jornada', $idsJornadasAsignadas);
            }

            $builder->groupEnd();
        }

        return $builder->findAll();
    }

    private function obtenerJornadaPermitida(int $jornadaId): ?array
    {
        foreach ($this->obtenerJornadasPermitidas() as $jornada) {
            if ((int) $jornada['id_jornada'] === $jornadaId) {
                return $jornada;
            }
        }

        return null;
    }

    private function normalizarPesquisas($pesquisas): array
    {
        if (is_array($pesquisas)) {
            return array_values(array_unique(array_map('intval', $pesquisas)));
        }

        if ($pesquisas === null || trim((string) $pesquisas) === '') {
            return [];
        }

        return array_values(array_unique(array_filter(array_map('intval', explode(',', (string) $pesquisas)))));
    }

    private function procesarAntropometria(int $jornadaId)
    {
        $archivo = $this->request->getFile('archivo_excel');

        if (! $archivo || ! $archivo->isValid()) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'No se recibio un archivo valido.',
            ]);
        }

        $ext = strtolower((string) $archivo->getClientExtension());
        if (! in_array($ext, ['xlsx', 'xls'], true)) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Solo se permiten archivos .xlsx o .xls.',
            ]);
        }

        if ($archivo->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'El archivo no debe superar 5 MB.',
            ]);
        }

        $tempPath = WRITEPATH . 'uploads/' . $archivo->getRandomName();
        $archivo->move(WRITEPATH . 'uploads/', basename($tempPath));

        try {
            $spreadsheet = IOFactory::load($tempPath);
        } catch (\Throwable $e) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'No se pudo leer el archivo Excel. Verifique el formato.',
            ]);
        }

        $sheet = $spreadsheet->getSheetByName('Antropometria')
            ?: $spreadsheet->getSheetByName('Antropometría')
            ?: $spreadsheet->getSheet(0);

        if (strtolower(trim($sheet->getTitle())) === 'instrucciones' && $spreadsheet->getSheetCount() > 1) {
            $sheet = $spreadsheet->getSheet(1);
        }

        $highestRow = $sheet->getHighestRow();

        if ($highestRow < 2) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'El archivo no contiene datos, solo encabezados.',
            ]);
        }

        if ($highestRow > (self::MAX_REGISTROS + 2)) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Maximo ' . self::MAX_REGISTROS . ' registros por carga.',
            ]);
        }

        $benefModel = new BeneficiariosModel();
        $jorBenModel = new JornadaBeneficiariosModel();
        $evalModel = new PesquisaEvaluacionModel();
        $itemModel = new PesquisaItemModel();
        $resultadoModel = new PesquisaResultadoModel();

        $items = $itemModel->getItemsPorPesquisa(self::TIPO_ANTROPOMETRIA);
        $itemsPorCodigo = [];
        foreach ($items as $item) {
            $itemsPorCodigo[$item['codigo']] = $item;
        }

        $codigosNecesarios = [
            'peso',
            'talla',
            'edema',
            'circ_cintura',
            'circ_brazo_izq',
            'circ_cefalica',
            'imc',
            'edad_dias_medicion',
            'edad_meses_medicion',
            'grupo_edad_reporte',
        ];

        $faltantes = array_values(array_filter($codigosNecesarios, static fn ($codigo) => ! isset($itemsPorCodigo[$codigo])));

        if (! empty($faltantes)) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'Faltan items de antropometria en pesquisa_items: ' . implode(', ', $faltantes),
            ]);
        }

        $usuarioId = session('id_usuario') ?? 1;
        $ahora = date('Y-m-d H:i:s');

        $guardados = 0;
        $yaEvaluados = 0;
        $noExisten = [];
        $noAsociados = [];
        $errores = [];

        $db = \Config\Database::connect();

        for ($fila = 2; $fila <= $highestRow; $fila++) {
            $idDigi = trim((string) $sheet->getCell("A{$fila}")->getValue());
            $nombresExcel = trim((string) $sheet->getCell("B{$fila}")->getValue());
            $apellidosExcel = trim((string) $sheet->getCell("C{$fila}")->getValue());
            $pesoRaw = $sheet->getCell("D{$fila}")->getValue();
            $tallaRaw = $sheet->getCell("E{$fila}")->getValue();
            $fechaRaw = $sheet->getCell("F{$fila}")->getValue();
            $cinturaRaw = $sheet->getCell("G{$fila}")->getValue();
            $observacion = trim((string) $sheet->getCell("H{$fila}")->getValue());
            $brazoRaw = $sheet->getCell("I{$fila}")->getValue();
            $cefalicaRaw = $sheet->getCell("J{$fila}")->getValue();

            if ($idDigi === '' && $nombresExcel === '' && $apellidosExcel === '' && $pesoRaw === null && $tallaRaw === null) {
                continue;
            }

            $errFila = [];

            if ($idDigi === '') {
                $errFila[] = 'id_digisalud obligatorio';
            }
            if (strlen($nombresExcel) < 2) {
                $errFila[] = 'nombres obligatorio o muy corto';
            }
            if (strlen($apellidosExcel) < 2) {
                $errFila[] = 'apellidos obligatorio o muy corto';
            }

            $fechaEvaluacion = $this->parsearFechaExcel($fechaRaw);
            if (! $fechaEvaluacion) {
                $errFila[] = 'fecha_evaluacion invalida';
            } elseif (strtotime($fechaEvaluacion) > strtotime(date('Y-m-d'))) {
                $errFila[] = 'fecha_evaluacion no puede ser futura';
            }

            $peso = $this->parsearNumero($pesoRaw);
            $talla = $this->parsearNumero($tallaRaw);
            if ($peso === null) {
                $errFila[] = 'peso obligatorio o invalido';
            }
            if ($talla === null) {
                $errFila[] = 'talla obligatoria o invalida';
            }
            if ($peso !== null) {
                $this->validarRangoItem($errFila, 'peso', $peso, $itemsPorCodigo['peso']);
            }
            if ($talla !== null) {
                $this->validarRangoItem($errFila, 'talla', $talla, $itemsPorCodigo['talla']);
            }

            $cintura = $this->parsearNumero($cinturaRaw);
            $brazo = $this->parsearNumero($brazoRaw);
            $cefalica = $this->parsearNumero($cefalicaRaw);

            if ($cinturaRaw !== null && trim((string) $cinturaRaw) !== '' && $cintura === null) {
                $errFila[] = 'circunferencia de cintura invalida';
            }
            if ($brazoRaw !== null && trim((string) $brazoRaw) !== '' && $brazo === null) {
                $errFila[] = 'circunferencia brazo izquierdo invalida';
            }
            if ($cefalicaRaw !== null && trim((string) $cefalicaRaw) !== '' && $cefalica === null) {
                $errFila[] = 'circunferencia cefalica invalida';
            }

            if ($cintura !== null) {
                $this->validarRangoItem($errFila, 'circunferencia de cintura', $cintura, $itemsPorCodigo['circ_cintura']);
            }
            if ($brazo !== null) {
                $this->validarRangoItem($errFila, 'circunferencia brazo izquierdo', $brazo, $itemsPorCodigo['circ_brazo_izq']);
            }
            if ($cefalica !== null) {
                $this->validarRangoItem($errFila, 'circunferencia cefalica', $cefalica, $itemsPorCodigo['circ_cefalica']);
            }

            $beneficiario = null;
            if ($idDigi !== '') {
                $beneficiario = $benefModel->where('id_digisalud', $idDigi)->first();
                if (! $beneficiario) {
                    $noExisten[] = [
                        'fila' => $fila,
                        'id_digisalud' => $idDigi,
                        'nombre' => trim($nombresExcel . ' ' . $apellidosExcel),
                        'mensaje' => 'El id_digisalud no existe. Debe registrar al beneficiario y agregarlo a la jornada.',
                    ];
                    continue;
                }
            }

            if ($beneficiario && $fechaEvaluacion) {
                $edadDias = $this->calcularEdadDias($beneficiario['fecha_nacimiento'] ?? null, $fechaEvaluacion);
                if ($edadDias === null) {
                    $errFila[] = 'no se pudo calcular edad con fecha_nacimiento y fecha_evaluacion';
                } elseif ($edadDias < 0) {
                    $errFila[] = 'fecha_evaluacion no puede ser anterior a fecha_nacimiento';
                } elseif ($edadDias > self::EDAD_DIAS_ADULTO && $cintura === null) {
                    $errFila[] = 'circunferencia de cintura obligatoria para adultos';
                }
            }

            if (! empty($errFila)) {
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim($nombresExcel . ' ' . $apellidosExcel),
                    'errores' => $errFila,
                ];
                continue;
            }

            $idBeneficiario = (int) $beneficiario['id_beneficiario'];

            $asociado = $jorBenModel
                ->where('id_beneficiario', $idBeneficiario)
                ->where('jornada_id', $jornadaId)
                ->where('status_bc', 1)
                ->first();

            if (! $asociado) {
                $noAsociados[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim(($beneficiario['nombres'] ?? $nombresExcel) . ' ' . ($beneficiario['apellidos'] ?? $apellidosExcel)),
                    'mensaje' => 'Existe en el sistema, pero no esta asociado a esta jornada. Debe agregarlo a la jornada antes de cargar antropometria.',
                ];
                continue;
            }

            $existente = $evalModel->existeEnJornada($idBeneficiario, self::TIPO_ANTROPOMETRIA, $jornadaId);
            if ($existente) {
                $yaEvaluados++;
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim(($beneficiario['nombres'] ?? '') . ' ' . ($beneficiario['apellidos'] ?? '')),
                    'errores' => ['Antropometria ya fue evaluada para este beneficiario en esta jornada. Use Editar evaluacion.'],
                ];
                continue;
            }

            $edadDias = $this->calcularEdadDias($beneficiario['fecha_nacimiento'] ?? null, $fechaEvaluacion);
            $edadMeses = $edadDias !== null ? round($edadDias / 30.4375, 2) : null;
            $imc = ($peso !== null && $talla !== null && $talla > 0)
                ? round($peso / pow($talla / 100, 2), 2)
                : null;

            $campos = [
                'peso' => $peso,
                'talla' => $talla,
                'edema' => 0,
                'imc' => $imc,
                'edad_dias_medicion' => $edadDias,
                'edad_meses_medicion' => $edadMeses,
                'grupo_edad_reporte' => ($edadDias !== null && $edadDias > self::EDAD_DIAS_ADULTO) ? 'adultos' : 'menores-19',
            ];

            if ($cintura !== null) {
                $campos['circ_cintura'] = $cintura;
            }
            if ($brazo !== null) {
                $campos['circ_brazo_izq'] = $brazo;
            }
            if ($cefalica !== null) {
                $campos['circ_cefalica'] = $cefalica;
            }

            $db->transBegin();
            try {
                $evaluacionId = $evalModel->insert([
                    'beneficiario_id' => $idBeneficiario,
                    'tipo_pesquisa_id' => self::TIPO_ANTROPOMETRIA,
                    'jornada_id' => $jornadaId,
                    'centro_id' => null,
                    'fecha_evaluacion' => $fechaEvaluacion,
                    'observaciones' => $observacion !== '' ? $observacion : null,
                    'evaluado_por' => $usuarioId,
                    'creado_en' => $ahora,
                    'status_eval' => 1,
                ], true);

                if (! $evaluacionId) {
                    throw new \RuntimeException('No se pudo crear la evaluacion.');
                }

                $datosResultados = [];
                foreach ($campos as $codigo => $valor) {
                    if ($valor === null || $valor === '' || ! isset($itemsPorCodigo[$codigo])) {
                        continue;
                    }

                    $datosResultados[] = [
                        'item_id' => (int) $itemsPorCodigo[$codigo]['id_item'],
                        'valor' => $valor,
                        'tipo_dato' => $itemsPorCodigo[$codigo]['tipo_dato'],
                    ];
                }

                $resultadoModel->guardarLote((int) $evaluacionId, $datosResultados);
                $db->transCommit();
                $guardados++;
            } catch (\Throwable $e) {
                $db->transRollback();
                log_message('error', 'Error en carga masiva antropometria fila ' . $fila . ': ' . $e->getMessage());
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim($nombresExcel . ' ' . $apellidosExcel),
                    'errores' => ['Error interno al guardar la evaluacion.'],
                ];
            }
        }

        @unlink($tempPath);

        return $this->response->setJSON([
            'ok' => true,
            'guardados' => $guardados,
            'ya_evaluados' => $yaEvaluados,
            'no_existen' => $noExisten,
            'no_asociados' => $noAsociados,
            'errores' => $errores,
            'total_procesados' => $guardados + $yaEvaluados + count($noExisten) + count($noAsociados) + count($errores),
        ]);
    }

    private function parsearFechaExcel($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            try {
                return \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $valor)->format('Y-m-d');
            } catch (\Throwable $e) {
                return null;
            }
        }

        $valor = trim((string) $valor);

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            $dt = \DateTime::createFromFormat('Y-m-d', $valor);
            return $dt && $dt->format('Y-m-d') === $valor ? $valor : null;
        }

        if (preg_match('#^(\d{1,2})[/\-](\d{1,2})[/\-](\d{4})$#', $valor, $m)) {
            $fecha = sprintf('%04d-%02d-%02d', (int) $m[3], (int) $m[2], (int) $m[1]);
            $dt = \DateTime::createFromFormat('Y-m-d', $fecha);
            return $dt && $dt->format('Y-m-d') === $fecha ? $fecha : null;
        }

        return null;
    }

    private function parsearNumero($valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        $valor = str_replace(',', '.', trim((string) $valor));
        return is_numeric($valor) ? (float) $valor : null;
    }

    private function validarRangoItem(array &$errores, string $label, float $valor, array $item): void
    {
        if ($valor <= 0) {
            $errores[] = $label . ' debe ser mayor a cero';
            return;
        }

        if ($item['valor_min'] !== null && $valor < (float) $item['valor_min']) {
            $errores[] = $label . ': valor minimo es ' . $item['valor_min'] . ' ' . ($item['unidad'] ?? '');
        }

        if ($item['valor_max'] !== null && $valor > (float) $item['valor_max']) {
            $errores[] = $label . ': valor maximo es ' . $item['valor_max'] . ' ' . ($item['unidad'] ?? '');
        }
    }

    private function calcularEdadDias(?string $fechaNacimiento, string $fechaEvaluacion): ?int
    {
        if (empty($fechaNacimiento)) {
            return null;
        }

        try {
            $nacimiento = new \DateTimeImmutable($fechaNacimiento);
            $evaluacion = new \DateTimeImmutable($fechaEvaluacion);
            return $evaluacion < $nacimiento ? -1 : (int) $nacimiento->diff($evaluacion)->days;
        } catch (\Throwable $e) {
            return null;
        }
    }
}

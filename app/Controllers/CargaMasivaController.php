<?php

namespace App\Controllers;

use App\Services\AntropometriaZscoreService;

use App\Models\BeneficiariosModel;
use App\Models\DireccionModel;
use App\Models\JornadaBeneficiariosModel;
use App\Models\JornadaModel;
use App\Models\PesquisaEvaluacionModel;
use App\Models\PesquisaItemModel;
use App\Models\PesquisaResultadoModel;
use App\Models\RolesUsuariosContextoModel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;

/**
 * Modulo centralizado de cargas masivas.
 *
 * Permite gestionar plantillas y cargas por jornada. Las cargas de evaluaciones
 * se habilitan segun las pesquisas asociadas a la jornada seleccionada.
 */
class CargaMasivaController extends BaseController
{
    private const ROLES_PERMITIDOS = [1, 2, 3, 4];
    private const TIPO_ANTROPOMETRIA = 1;
    private const MAX_REGISTROS = 500;
    private const EDAD_DIAS_ADULTO = 6939;
    private array $antroZscoreCache = [];



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
            'beneficiarios' => $this->procesarBeneficiarios($jornadaId),
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
            'beneficiarios' => [
                'codigo' => 'beneficiarios',
                'nombre' => 'Beneficiarios',
                'descripcion' => 'Crea beneficiarios, evita duplicados por ID DigiSalud y los asocia a la jornada.',
                'tipo_pesquisa_id' => 0,
                'plantilla' => 'beneficiarios',
                'icono' => null,
                'bootstrap_icon' => 'bi-people',
            ],
            'antropometria' => [
                'codigo' => 'antropometria',
                'nombre' => 'Antropometria',
                'descripcion' => 'Peso, talla, IMC, cintura y circunferencias.',
                'tipo_pesquisa_id' => self::TIPO_ANTROPOMETRIA,
                'plantilla' => 'antropometria',
                'icono' => 'antropometria2.svg',
                'bootstrap_icon' => null,
            ],
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

    private function validarYLeerExcel()
    {
        $archivo = $this->request->getFile('archivo_excel');

        if (! $archivo || ! $archivo->isValid()) {
            return ['error' => 'No se recibio un archivo valido.'];
        }

        $ext = strtolower((string) $archivo->getClientExtension());
        if (! in_array($ext, ['xlsx', 'xls'], true)) {
            return ['error' => 'Solo se permiten archivos .xlsx o .xls.'];
        }

        if ($archivo->getSize() > 5 * 1024 * 1024) {
            return ['error' => 'El archivo no debe superar 5 MB.'];
        }

        $tempPath = WRITEPATH . 'uploads/' . $archivo->getRandomName();
        $archivo->move(WRITEPATH . 'uploads/', basename($tempPath));

        try {
            $spreadsheet = IOFactory::load($tempPath);
        } catch (\Throwable $e) {
            @unlink($tempPath);
            return ['error' => 'No se pudo leer el archivo Excel. Verifique el formato.'];
        }

        return ['spreadsheet' => $spreadsheet, 'tempPath' => $tempPath];
    }

    private function obtenerHoja($spreadsheet, array $nombres)
    {
        foreach ($nombres as $nombre) {
            $sheet = $spreadsheet->getSheetByName($nombre);
            if ($sheet) {
                return $sheet;
            }
        }

        $sheet = $spreadsheet->getSheet(0);
        if (strtolower(trim($sheet->getTitle())) === 'instrucciones' && $spreadsheet->getSheetCount() > 1) {
            return $spreadsheet->getSheet(1);
        }

        return $sheet;
    }

    private function limpiarTextoBasico(string $texto): string
    {
        return str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'],
            $texto
        );
    }

    private function normalizarClaveColumna(string $texto): string
    {
        $texto = strtolower($this->limpiarTextoBasico($texto));

        // Elimina aclaratorias entre paréntesis:
        // "sexo * (F o M)" => "sexo *"
        // "fecha_nacimiento * (01/02/1991)" => "fecha_nacimiento *"
        $texto = preg_replace('/\s*\([^)]*\)/', '', $texto);

        // Quita asteriscos y saltos de línea
        $texto = str_replace(['*', "\r", "\n", "\t"], ' ', $texto);

        // Normaliza separadores
        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto);

        return trim((string) $texto, '_');
    }



    private function buscarColumnaPorAlias(array $headers, array $aliases): ?int
    {
        foreach ($aliases as $alias) {
            $aliasKey = $this->normalizarClaveColumna($alias);

            foreach ($headers as $headerKey => $col) {
                if (
                    $headerKey === $aliasKey ||
                    str_starts_with($headerKey, $aliasKey . '_') ||
                    str_contains($headerKey, '_' . $aliasKey)
                ) {
                    return (int) $col;
                }
            }
        }

        return null;
    }




    private function filaVaciaBeneficiario(array $data): bool
    {
        foreach ($data as $valor) {
            if (trim((string) $valor) !== '') {
                return false;
            }
        }

        return true;
    }
    private function normalizarNombrePersona(?string $texto): string
    {
        $texto = trim((string) $texto);
        $texto = preg_replace('/\s+/', ' ', $texto);

        return mb_strtoupper($texto, 'UTF-8');
    }

    private function normalizarTextoParaIdDigi(?string $texto): string
    {
        $texto = trim((string) $texto);

        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'U', 'N'],
            $texto
        );

        $texto = preg_replace('/[^A-Za-z]/', '', $texto);

        return strtoupper($texto);
    }
    private function procesarBeneficiarios(int $jornadaId)
    {
        $excel = $this->validarYLeerExcel();
        if (isset($excel['error'])) {
            return $this->response->setJSON(['ok' => false, 'error' => $excel['error']]);
        }

        $spreadsheet = $excel['spreadsheet'];
        $tempPath = $excel['tempPath'];
        $sheet = $this->obtenerHoja($spreadsheet, ['Beneficiarios', 'beneficiarios']);
        $highestRow = $sheet->getHighestRow();
        $headers = $this->mapearEncabezados($sheet);
        if ($highestRow < 2) {
            @unlink($tempPath);
            return $this->response->setJSON(['ok' => false, 'error' => 'El archivo no contiene datos, solo encabezados.']);
        }

        if ($highestRow > (self::MAX_REGISTROS + 2)) {
            @unlink($tempPath);
            return $this->response->setJSON(['ok' => false, 'error' => 'Maximo ' . self::MAX_REGISTROS . ' registros por carga.']);
        }

        $headers = $this->mapearEncabezados($sheet);
        $requeridos = [
            'nombres' => ['nombres', 'nombre'],
            'apellidos' => ['apellidos', 'apellido'],
            'sexo' => ['sexo', 'genero'],
            'fecha_nacimiento' => ['fecha_nacimiento', 'fecha de nacimiento', 'fecha nac', 'nacimiento'],
            'pais_nacimiento' => ['pais_nacimiento', 'pais de nacimiento', 'pais nacimiento'],
        ];

        $faltantes = [];

        foreach ($requeridos as $campo => $aliases) {
            if ($this->buscarColumnaPorAlias($headers, $aliases) === null) {
                $faltantes[] = $campo;
            }
        }

        if (! empty($faltantes)) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok' => false,
                'error' => 'La plantilla no contiene las columnas obligatorias: ' . implode(', ', $faltantes),
            ]);
        }

        $benefModel = new BeneficiariosModel();
        $dirModel = new DireccionModel();
        $jorBenModel = new JornadaBeneficiariosModel();
        $db = \Config\Database::connect();

        $usuarioId = session('id_usuario') ?? 1;
        $ahora = date('Y-m-d H:i:s');

        $guardados = 0;
        $creados = 0;
        $existentesAsociados = 0;
        $yaAsociados = 0;
        $errores = [];
        $logs = [];
        $totalProcesados = 0;

        for ($fila = 2; $fila <= $highestRow; $fila++) {
            $data = [
                'nombres' => $this->valorPorAlias($sheet, $headers, $fila, $requeridos['nombres']),
                'apellidos' => $this->valorPorAlias($sheet, $headers, $fila, $requeridos['apellidos']),
                'sexo' => $this->valorPorAlias($sheet, $headers, $fila, $requeridos['sexo']),
                'fecha_nacimiento' => $this->valorPorAlias($sheet, $headers, $fila, $requeridos['fecha_nacimiento']),
                'pais_nacimiento' => $this->valorPorAlias($sheet, $headers, $fila, $requeridos['pais_nacimiento']),
                'telefono' => $this->valorPorAlias($sheet, $headers, $fila, ['telefono', 'teléfono', 'celular']),
                'correo' => $this->valorPorAlias($sheet, $headers, $fila, ['correo', 'email', 'correo electronico']),
                'pais_residencia' => $this->valorPorAlias($sheet, $headers, $fila, [
                    'pais_residencia',
                    'pais residencia',
                    'pais_residencia_opcional',
                    'pais residencia opcional',
                    'pais donde vive',
                ]),
                'estado' => $this->valorPorAlias($sheet, $headers, $fila, ['estado']),
                'municipio' => $this->valorPorAlias($sheet, $headers, $fila, ['municipio']),
                'parroquia' => $this->valorPorAlias($sheet, $headers, $fila, ['parroquia']),
                'ciudad' => $this->valorPorAlias($sheet, $headers, $fila, ['ciudad_localidad', 'ciudad localidad', 'ciudad', 'localidad']),
                'detalle' => $this->valorPorAlias($sheet, $headers, $fila, ['detalle', 'detalle sector casa punto de referencia', 'sector casa punto de referencia', 'direccion detalle']),
            ];
            $data['nombres'] = $this->normalizarNombrePersona($data['nombres']);
            $data['apellidos'] = $this->normalizarNombrePersona($data['apellidos']);

            if ($this->filaVaciaBeneficiario($data)) {
                continue;
            }

            $totalProcesados++;
            $errFila = [];

            if (mb_strlen($data['nombres']) < 2) {
                $errFila[] = 'nombres obligatorio o muy corto';
            }
            if (mb_strlen($data['apellidos']) < 2) {
                $errFila[] = 'apellidos obligatorio o muy corto';
            }

            $sexo = strtoupper(substr($data['sexo'], 0, 1));
            if (! in_array($sexo, ['M', 'F'], true)) {
                $errFila[] = 'sexo obligatorio o invalido. Use M/F o Masculino/Femenino';
            }

            $fechaNacimiento = $this->parsearFechaExcel($data['fecha_nacimiento']);
            if (! $fechaNacimiento) {
                $errFila[] = 'fecha_nacimiento obligatoria o invalida';
            } elseif (strtotime($fechaNacimiento) > strtotime(date('Y-m-d'))) {
                $errFila[] = 'fecha_nacimiento no puede ser futura';
            }

            if ($data['pais_nacimiento'] === '') {
                $errFila[] = 'pais_nacimiento obligatorio';
            }

            $idDigi = $fechaNacimiento
                ? $this->construirIdDigi($data['pais_nacimiento'], $sexo, $data['nombres'], $data['apellidos'], $fechaNacimiento)
                : '';

            if (! empty($errFila)) {
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim($data['nombres'] . ' ' . $data['apellidos']),
                    'errores' => $errFila,
                ];
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $data['nombres'], $data['apellidos'], implode('; ', $errFila));
                continue;
            }

            $db->transBegin();
            try {
                $beneficiario = $benefModel->where('id_digisalud', $idDigi)->first();
                $idBeneficiario = $beneficiario ? (int) $beneficiario['id_beneficiario'] : 0;
                $creadoEnFila = false;

                if (! $beneficiario) {
                    $direccionId = null;
                    $tieneDireccion = $data['pais_residencia'] !== ''
                        || $data['estado'] !== ''
                        || $data['municipio'] !== ''
                        || $data['parroquia'] !== ''
                        || $data['ciudad'] !== ''
                        || $data['detalle'] !== '';

                    if ($tieneDireccion) {
                        $direccionId = $dirModel->insert([
                            'pais'      => $data['pais_residencia'] !== '' ? $data['pais_residencia'] : null,
                            'estado'    => $data['estado'] !== '' ? $data['estado'] : null,
                            'municipio' => $data['municipio'] !== '' ? $data['municipio'] : null,
                            'parroquia' => $data['parroquia'] !== '' ? $data['parroquia'] : null,
                            'ciudad'    => $data['ciudad'] !== '' ? $data['ciudad'] : null,
                            'detalle'   => $data['detalle'] !== '' ? $data['detalle'] : null,
                        ], true);
                    }

                    $idBeneficiario = (int) $benefModel->insert([
                        'id_digisalud' => $idDigi,
                        'nombres' => $data['nombres'],
                        'apellidos' => $data['apellidos'],
                        'fecha_nacimiento' => $fechaNacimiento,
                        'sexo' => $sexo,
                        'pais_nacimiento' => $data['pais_nacimiento'],
                        'telefono' => $data['telefono'] !== '' ? $data['telefono'] : null,
                        'correo' => $data['correo'] !== '' ? $data['correo'] : null,
                        'direccion_id' => $direccionId,
                        'creado_en' => $ahora,
                        'creado_por' => $usuarioId,
                    ], true);

                    if (! $idBeneficiario) {
                        throw new \RuntimeException('No se pudo crear el beneficiario.');
                    }

                    $creadoEnFila = true;
                }

                $asociado = $jorBenModel
                    ->where('id_beneficiario', $idBeneficiario)
                    ->where('jornada_id', $jornadaId)
                    ->where('status_bc', 1)
                    ->first();

                if ($asociado) {
                    $db->transCommit();
                    $yaAsociados++;
                    $mensaje = 'El beneficiario ya existia y ya estaba asociado a esta jornada.';
                    $errores[] = [
                        'fila' => $fila,
                        'id_digisalud' => $idDigi,
                        'nombre' => trim($data['nombres'] . ' ' . $data['apellidos']),
                        'errores' => [$mensaje],
                    ];
                    $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $data['nombres'], $data['apellidos'], $mensaje);
                    continue;
                }

                $jorBenModel->insert([
                    'id_beneficiario' => $idBeneficiario,
                    'jornada_id' => $jornadaId,
                    'status_bc' => 1,
                    'creado_en' => $ahora,
                    'creado_por' => $usuarioId,
                ]);

                $db->transCommit();
                $guardados++;

                if ($creadoEnFila) {
                    $creados++;
                    $logs[] = $this->crearLog($fila, 'GUARDADO', $idDigi, $data['nombres'], $data['apellidos'], 'Beneficiario creado y asociado a la jornada.');
                } else {
                    $existentesAsociados++;
                    $logs[] = $this->crearLog($fila, 'GUARDADO', $idDigi, $data['nombres'], $data['apellidos'], 'Beneficiario existente asociado a la jornada.');
                }
            } catch (\Throwable $e) {
                $db->transRollback();
                log_message('error', 'Error en carga masiva beneficiarios fila ' . $fila . ': ' . $e->getMessage());
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim($data['nombres'] . ' ' . $data['apellidos']),
                    'errores' => ['Error interno al guardar el beneficiario.'],
                ];
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $data['nombres'], $data['apellidos'], 'Error interno al guardar el beneficiario.');
            }
        }

        @unlink($tempPath);

        return $this->response->setJSON([
            'ok' => true,
            'tipo' => 'beneficiarios',
            'guardados' => $guardados,
            'creados' => $creados,
            'existentes_asociados' => $existentesAsociados,
            'ya_asociados' => $yaAsociados,
            'no_guardados' => count($errores),
            'errores' => $errores,
            'logs' => $logs,
            'total_procesados' => $totalProcesados,
        ]);
    }

    private function crearLog(int $fila, string $estado, string $idDigi, string $nombres, string $apellidos, string $detalle): array
    {
        return [
            'fila' => $fila,
            'estado' => $estado,
            'id_digisalud' => $idDigi,
            'nombres' => $nombres,
            'apellidos' => $apellidos,
            'detalle' => $detalle,
        ];
    }

    private function limpiarPartesNombre(?string $texto): array
    {
        $texto = trim((string) $texto);
        if ($texto === '') {
            return [];
        }

        return array_values(array_filter(preg_split('/\s+/', $texto)));
    }

    private function normalizarTextoId(?string $texto): string
    {
        $texto = trim((string) $texto);
        $texto = str_replace(['á', 'é', 'í', 'ó', 'ú', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ñ'], ['a', 'e', 'i', 'o', 'u', 'n', 'A', 'E', 'I', 'O', 'U', 'N'], $texto);
        $texto = preg_replace('/[^A-Za-z]/', '', $texto);
        return strtoupper($texto);
    }

    private function normalizarFechaIdDigi(?string $fecha): string
    {
        $fecha = trim((string) $fecha);
        if ($fecha === '') {
            return '20000101';
        }

        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return str_replace('-', '', $fecha);
        }

        if (preg_match('/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/', $fecha, $m)) {
            return $m[3] . $m[2] . $m[1];
        }

        return preg_replace('/\D/', '', $fecha);
    }

    private function construirIdDigi(
        ?string $paisNacimiento,
        ?string $sexo,
        ?string $nombres,
        ?string $apellidos,
        ?string $fechaNacimiento
    ): string {
        $pais = $this->normalizarTextoParaIdDigi($paisNacimiento ?: 'VE');
        $pais = substr($pais !== '' ? $pais : 'VE', 0, 2);

        $sexo = strtoupper(substr($sexo ?: 'M', 0, 1));

        $partesNombres = $this->limpiarPartesNombre($nombres);
        $partesApellidos = $this->limpiarPartesNombre($apellidos);

        $primerNombre = substr($this->normalizarTextoParaIdDigi($partesNombres[0] ?? ''), 0, 3);
        $segundoNombre = isset($partesNombres[1])
            ? substr($this->normalizarTextoParaIdDigi($partesNombres[1]), 0, 1)
            : '';

        $primerApellido = substr($this->normalizarTextoParaIdDigi($partesApellidos[0] ?? ''), 0, 3);
        $segundoApellido = isset($partesApellidos[1])
            ? substr($this->normalizarTextoParaIdDigi($partesApellidos[1]), 0, 1)
            : '';

        $fecha = $this->normalizarFechaIdDigi($fechaNacimiento ?: '2000-01-01');

        return $pais
            . $sexo
            . $primerNombre
            . $segundoNombre
            . $primerApellido
            . $segundoApellido
            . $fecha;
    }

    private function procesarAntropometria(int $jornadaId)
    {
        $excel = $this->validarYLeerExcel();
        if (isset($excel['error'])) {
            return $this->response->setJSON(['ok' => false, 'error' => $excel['error']]);
        }

        $spreadsheet = $excel['spreadsheet'];
        $tempPath = $excel['tempPath'];
        $sheet = $this->obtenerHoja($spreadsheet, ['Antropometria', 'Antropometría']);
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
            'metodo_medicion_talla',
            'edema',
            'circ_cintura',
            'circ_brazo_izq',
            'circ_cefalica',
            'pliegue_tricipital',
            'pliegue_subescapular',
            'imc',
            'edad_dias_medicion',
            'edad_meses_medicion',
            'grupo_edad_reporte',

            'zpe',
            'zpe_percentil',
            'zte',
            'zte_percentil',
            'zpt',
            'zpt_percentil',
            'zimce',
            'zimce_percentil',
            'zcc',
            'zcc_percentil',
            'zcbi',
            'zcbi_percentil',
            'zptri',
            'zptri_percentil',
            'zpsub',
            'zpsub_percentil',

            'clasificacion_imc_talla',
            'estado_nutricional_agregado',
        ];

        $faltantes = array_values(array_filter($codigosNecesarios, static fn($codigo) => ! isset($itemsPorCodigo[$codigo])));

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
        $logs = [];

        $db = \Config\Database::connect();
        $antroService = new AntropometriaZscoreService();
        $headers = $this->mapearEncabezados($sheet);
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

            $metodoMedicionTallaRaw = $this->valorItemAntroPorCodigo(
                $sheet,
                $headers,
                $itemsPorCodigo,
                $fila,
                'metodo_medicion_talla'
            );

            $tricipitalRaw = $this->valorItemAntroPorCodigo(
                $sheet,
                $headers,
                $itemsPorCodigo,
                $fila,
                'pliegue_tricipital'
            );

            $subescapularRaw = $this->valorItemAntroPorCodigo(
                $sheet,
                $headers,
                $itemsPorCodigo,
                $fila,
                'pliegue_subescapular'
            );

            if (
                $idDigi === ''
                && $nombresExcel === ''
                && $apellidosExcel === ''
                && $pesoRaw === null
                && $tallaRaw === null
                && $metodoMedicionTallaRaw === ''
                && $tricipitalRaw === ''
                && $subescapularRaw === ''
            ) {
                continue;
            }

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
            $metodoMedicionTalla = $this->normalizarMetodoMedicionTalla($metodoMedicionTallaRaw);
            $tricipital = $this->parsearNumero($tricipitalRaw);
            $subescapular = $this->parsearNumero($subescapularRaw);

            if ($metodoMedicionTalla === null) {
                $errFila[] = 'metodo_medicion_talla invalido. Use de_pie o acostado';
            }

            if ($tricipitalRaw !== '' && $tricipital === null) {
                $errFila[] = 'pliegue tricipital invalido';
            }

            if ($subescapularRaw !== '' && $subescapular === null) {
                $errFila[] = 'pliegue subescapular invalido';
            }
            if ($cinturaRaw !== null && trim((string) $cinturaRaw) !== '' && $cintura === null) {
                $errFila[] = 'circunferencia de cintura invalida';
            }
            if ($brazoRaw !== null && trim((string) $brazoRaw) !== '' && $brazo === null) {
                $errFila[] = 'circunferencia brazo izquierdo invalida';
            }
            if ($cefalicaRaw !== null && trim((string) $cefalicaRaw) !== '' && $cefalica === null) {
                $errFila[] = 'circunferencia cefalica invalida';
            }
            if ($tricipital !== null) {
                $this->validarRangoItem($errFila, 'pliegue tricipital', $tricipital, $itemsPorCodigo['pliegue_tricipital']);
            }

            if ($subescapular !== null) {
                $this->validarRangoItem($errFila, 'pliegue subescapular', $subescapular, $itemsPorCodigo['pliegue_subescapular']);
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
            if ($tricipital !== null) {
                $this->validarRangoItem($errFila, 'pliegue tricipital', $tricipital, $itemsPorCodigo['pliegue_tricipital']);
            }

            if ($subescapular !== null) {
                $this->validarRangoItem($errFila, 'pliegue subescapular', $subescapular, $itemsPorCodigo['pliegue_subescapular']);
            }

            $beneficiario = null;
            if ($idDigi !== '') {
                $beneficiario = $benefModel->where('id_digisalud', $idDigi)->first();
                if (! $beneficiario) {
                    $mensaje = 'El id_digisalud no existe. Debe registrar al beneficiario y agregarlo a la jornada.';
                    $noExisten[] = [
                        'fila' => $fila,
                        'id_digisalud' => $idDigi,
                        'nombre' => trim($nombresExcel . ' ' . $apellidosExcel),
                        'mensaje' => $mensaje,
                    ];
                    $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, $mensaje);
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
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, implode('; ', $errFila));
                continue;
            }

            $idBeneficiario = (int) $beneficiario['id_beneficiario'];

            $asociado = $jorBenModel
                ->where('id_beneficiario', $idBeneficiario)
                ->where('jornada_id', $jornadaId)
                ->where('status_bc', 1)
                ->first();

            if (! $asociado) {
                $mensaje = 'Existe en el sistema, pero no esta asociado a esta jornada. Debe agregarlo a la jornada antes de cargar antropometria.';
                $noAsociados[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim(($beneficiario['nombres'] ?? $nombresExcel) . ' ' . ($beneficiario['apellidos'] ?? $apellidosExcel)),
                    'mensaje' => $mensaje,
                ];
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, $mensaje);
                continue;
            }

            $existente = $evalModel->existeEnJornada($idBeneficiario, self::TIPO_ANTROPOMETRIA, $jornadaId);
            if ($existente) {
                $yaEvaluados++;
                $mensaje = 'Antropometria ya fue evaluada para este beneficiario en esta jornada. Use Editar evaluacion.';
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim(($beneficiario['nombres'] ?? '') . ' ' . ($beneficiario['apellidos'] ?? '')),
                    'errores' => [$mensaje],
                ];
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, $mensaje);
                continue;
            }

            $edadDias = $this->calcularEdadDias($beneficiario['fecha_nacimiento'] ?? null, $fechaEvaluacion);
            $edadMeses = $edadDias !== null ? round($edadDias / 30.4375, 2) : null;

            $tallaParaCalculo = $this->ajustarTallaPorMetodoMedicion(
                $talla,
                $edadDias,
                $metodoMedicionTalla
            );

            $calculadosAntro = $antroService->calcularMenor([
                'sexo'                  => $beneficiario['sexo'] ?? null,
                'fecha_nacimiento'      => $beneficiario['fecha_nacimiento'] ?? null,
                'fecha_evaluacion'      => $fechaEvaluacion,
                'peso'                  => $peso,
                'talla'                 => $tallaParaCalculo,
                'metodo_medicion_talla' => $metodoMedicionTalla,
                'circ_cefalica'         => $cefalica,
                'circ_brazo_izq'        => $brazo,
                'pliegue_tricipital'    => $tricipital,
                'pliegue_subescapular'  => $subescapular,
                'edema'                 => 0,
            ]);
            $imc = $calculadosAntro['imc'] ?? (
                ($peso !== null && $tallaParaCalculo !== null && $tallaParaCalculo > 0)
                ? round($peso / pow($tallaParaCalculo / 100, 2), 2)
                : null
            );

            $campos = [
                'peso' => $peso,
                'talla' => $tallaParaCalculo,
                'metodo_medicion_talla' => $metodoMedicionTalla,
                'edema' => 0,
                'imc' => $imc,
                'edad_dias_medicion' => $calculadosAntro['edad_dias_medicion'] ?? $edadDias,
                'edad_meses_medicion' => $edadMeses,
                'grupo_edad_reporte' => ($edadDias !== null && $edadDias > self::EDAD_DIAS_ADULTO) ? 'adultos' : 'menores-19',
            ];
            if ($tricipital !== null) {
                $campos['pliegue_tricipital'] = $tricipital;
            }

            if ($subescapular !== null) {
                $campos['pliegue_subescapular'] = $subescapular;
            }
            if ($edadDias !== null && $edadDias <= self::EDAD_DIAS_ADULTO) {
                foreach (
                    [
                        'zpe',
                        'zpe_percentil',
                        'zte',
                        'zte_percentil',
                        'zimce',
                        'zimce_percentil',
                        'zpt',
                        'zpt_percentil',
                        'zcc',
                        'zcc_percentil',
                        'zcbi',
                        'zcbi_percentil',
                        'zptri',
                        'zptri_percentil',
                        'zpsub',
                        'zpsub_percentil',
                        'clasificacion_imc_talla',
                        'estado_nutricional_agregado',
                    ] as $codigoCalculado
                ) {
                    if (array_key_exists($codigoCalculado, $calculadosAntro)) {
                        $campos[$codigoCalculado] = $calculadosAntro[$codigoCalculado];
                    }
                }
            }

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
                $logs[] = $this->crearLog($fila, 'GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, 'Evaluacion de antropometria guardada.');
            } catch (\Throwable $e) {
                $db->transRollback();
                log_message('error', 'Error en carga masiva antropometria fila ' . $fila . ': ' . $e->getMessage());
                $errores[] = [
                    'fila' => $fila,
                    'id_digisalud' => $idDigi,
                    'nombre' => trim($nombresExcel . ' ' . $apellidosExcel),
                    'errores' => ['Error interno al guardar la evaluacion.'],
                ];
                $logs[] = $this->crearLog($fila, 'NO GUARDADO', $idDigi, $nombresExcel, $apellidosExcel, 'Error interno al guardar la evaluacion.');
            }
        }

        @unlink($tempPath);

        return $this->response->setJSON([
            'ok' => true,
            'tipo' => 'antropometria',
            'guardados' => $guardados,
            'ya_evaluados' => $yaEvaluados,
            'no_existen' => $noExisten,
            'no_asociados' => $noAsociados,
            'errores' => $errores,
            'logs' => $logs,
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
    private function calcularZscoresAntropometriaMasiva(
        ?float $peso,
        ?float $talla,
        ?float $imc,
        int $edadDias,
        ?string $sexo,
        ?float $circCefalica = null,
        ?float $circBrazoIzq = null
    ): array {
        $sexo = $this->normalizarSexoZscore($sexo);
        if ($sexo === null) {
            return [];
        }

        $resultados = [];

        // Peso / edad: hasta 3653 días
        if ($peso !== null && $edadDias <= 3653) {
            $zpe = $this->calcularZPesoEdad($peso, $edadDias, $sexo);
            $this->agregarZscoreConPercentil($resultados, 'zpe', $zpe);
        }

        // Talla / edad: hasta 6939 días
        if ($talla !== null && $edadDias <= self::EDAD_DIAS_ADULTO) {
            $zte = $this->calcularZTallaEdad($talla, $edadDias, $sexo);
            $this->agregarZscoreConPercentil($resultados, 'zte', $zte);
        }

        // IMC / edad: hasta 6939 días
        if ($imc !== null && $edadDias <= self::EDAD_DIAS_ADULTO) {
            $zimce = $this->calcularZImcEdad($imc, $edadDias, $sexo);
            $this->agregarZscoreConPercentil($resultados, 'zimce', $zimce);
        }

        // Peso / talla: hasta 1856 días
        if ($peso !== null && $talla !== null && $edadDias <= 1856) {
            $zpt = $this->calcularZPesoTalla($peso, $talla, $sexo);
            $this->agregarZscoreConPercentil($resultados, 'zpt', $zpt);
        }

        // Circunferencia cefálica: hasta 1856 días
        if ($circCefalica !== null && $edadDias <= 1856) {
            $zcc = $this->calcularZGenericoPorDias(
                'zcc_dias',
                $circCefalica,
                $edadDias,
                $sexo,
                'ccdias_indicador_genero',
                'ccdias_indicador_denominador',
                'ccdias_sd0',
                'ccdias_indicador_coeficiente_l',
                'ccdias_indicador_coeficiente_s'
            );
            $this->agregarZscoreConPercentil($resultados, 'zcc', $zcc);
        }

        // Circunferencia brazo izquierdo: de 91 a 1856 días
        if ($circBrazoIzq !== null && $edadDias >= 91 && $edadDias <= 1856) {
            $zcbi = $this->calcularZGenericoPorDias(
                'zcbi_dias',
                $circBrazoIzq,
                $edadDias,
                $sexo,
                'cbidias_indicador_genero',
                'cbidias_indicador_denominador',
                'cbidias_sd0',
                'cbidias_indicador_coeficiente_l',
                'cbidias_indicador_coeficiente_s'
            );
            $this->agregarZscoreConPercentil($resultados, 'zcbi', $zcbi);
        }

        return $resultados;
    }

    private function agregarZscoreConPercentil(array &$resultados, string $codigo, ?float $zscore): void
    {
        if ($zscore === null || ! is_finite($zscore)) {
            return;
        }

        $resultados[$codigo] = round($zscore, 2);
        $resultados[$codigo . '_percentil'] = $this->zscoreAPercentil($zscore);
    }

    private function calcularZPesoEdad(float $peso, int $edadDias, string $sexo): ?float
    {
        if ($edadDias <= 1856) {
            return $this->calcularZGenericoPorDias(
                'zpe_dias',
                $peso,
                $edadDias,
                $sexo,
                'pdias_indicador_genero',
                'pdias_indicador_denominador',
                'pdias_sd0_mediana',
                'pdias_indicador_coeficiente_l',
                'pdias_indicador_coeficiente_s'
            );
        }

        return $this->calcularZGenericoPorMeses(
            'zpe_meses',
            $peso,
            $edadDias,
            $sexo,
            'p_indicador_genero',
            'p_indicador_denominador',
            'p_indicador_coeficiente_m',
            'p_indicador_coeficiente_l',
            'p_indicador_coeficiente_s'
        );
    }

    private function calcularZTallaEdad(float $talla, int $edadDias, string $sexo): ?float
    {
        if ($edadDias <= 1856) {
            return $this->calcularZGenericoPorDias(
                'zte_dias',
                $talla,
                $edadDias,
                $sexo,
                'tdias_indicador_genero',
                'tdias_indicador_denominador',
                'tdias_sd0_mediana',
                'tdias_indicador_coeficiente_l',
                'tdias_indicador_coeficiente_s'
            );
        }

        $archivo = $sexo === 'F' ? 'zte_meses_f' : 'zte_meses_m';

        return $this->calcularZGenericoPorMeses(
            $archivo,
            $talla,
            $edadDias,
            $sexo,
            't_indicador_genero',
            't_indicador_denominador',
            't_indicador_coeficiente_m',
            't_indicador_coeficiente_l',
            't_indicador_coeficiente_s'
        );
    }

    private function calcularZImcEdad(float $imc, int $edadDias, string $sexo): ?float
    {
        if ($edadDias <= 1856) {
            return $this->calcularZGenericoPorDias(
                'zimce_dias',
                $imc,
                $edadDias,
                $sexo,
                'idias_indicador_genero',
                'idias_indicador_denominador',
                'idias_sd0_mediana',
                'idias_indicador_coeficiente_l',
                'idias_indicador_coeficiente_s'
            );
        }

        return $this->calcularZGenericoPorMeses(
            'zimce_meses',
            $imc,
            $edadDias,
            $sexo,
            'i_indicador_genero',
            'i_indicador_denominador',
            'i_indicador_coeficiente_m',
            'i_indicador_coeficiente_l',
            'i_indicador_coeficiente_s'
        );
    }

    private function calcularZPesoTalla(float $peso, float $talla, string $sexo): ?float
    {
        $archivo = $talla >= 65 ? 'zpt_65_120' : 'zpt_45_110';
        $filas = $this->obtenerDataZscore($archivo);

        $denominador = round($talla, 1);

        $filasSexo = array_values(array_filter($filas, function ($fila) use ($sexo) {
            return $this->normalizarTextoZscore($fila['petadias_indicador_genero'] ?? null) === $sexo;
        }));

        $fila = null;
        foreach ($filasSexo as $f) {
            $valorDenominador = $this->numeroZscore($f['petadias_indicador_denominador'] ?? null);
            if ($valorDenominador !== null && abs($valorDenominador - $denominador) < 0.051) {
                $fila = $f;
                break;
            }
        }

        if ($fila === null) {
            $fila = $this->buscarFilaMasCercana($filasSexo, 'petadias_indicador_denominador', $denominador);
        }

        if ($fila === null) {
            return null;
        }

        return $this->calcularZLms(
            $peso,
            $fila['petadias_sd0_mediana'] ?? null,
            $fila['petadias_indicador_coeficiente_l'] ?? null,
            $fila['petadias_indicador_coeficiente_s'] ?? null
        );
    }

    private function calcularZGenericoPorDias(
        string $archivo,
        float $valor,
        int $edadDias,
        string $sexo,
        string $campoSexo,
        string $campoDenominador,
        string $campoMediana,
        string $campoL,
        string $campoS
    ): ?float {
        $filas = $this->obtenerDataZscore($archivo);

        $filasSexo = array_values(array_filter($filas, function ($fila) use ($sexo, $campoSexo) {
            return $this->normalizarTextoZscore($fila[$campoSexo] ?? null) === $sexo;
        }));

        $fila = null;
        foreach ($filasSexo as $f) {
            $denominador = $this->numeroZscore($f[$campoDenominador] ?? null);
            if ($denominador !== null && (int) $denominador === $edadDias) {
                $fila = $f;
                break;
            }
        }

        if ($fila === null) {
            $fila = $this->buscarFilaMasCercana($filasSexo, $campoDenominador, $edadDias);
        }

        if ($fila === null) {
            return null;
        }

        return $this->calcularZLms(
            $valor,
            $fila[$campoMediana] ?? null,
            $fila[$campoL] ?? null,
            $fila[$campoS] ?? null
        );
    }

    private function calcularZGenericoPorMeses(
        string $archivo,
        float $valor,
        int $edadDias,
        string $sexo,
        string $campoSexo,
        string $campoDenominador,
        string $campoMediana,
        string $campoL,
        string $campoS
    ): ?float {
        $filas = $this->obtenerDataZscore($archivo);

        $filasSexo = array_values(array_filter($filas, function ($fila) use ($sexo, $campoSexo) {
            return $this->normalizarTextoZscore($fila[$campoSexo] ?? null) === $sexo;
        }));

        $edadMesesExacta = $edadDias / 30.4375;
        $mes = (int) floor($edadMesesExacta);
        $fraccion = $edadMesesExacta - $mes;

        $fila1 = $this->buscarFilaPorDenominadorEntero($filasSexo, $campoDenominador, $mes)
            ?? $this->buscarFilaMasCercana($filasSexo, $campoDenominador, $mes);

        if ($fila1 === null) {
            return null;
        }

        $fila2 = $this->buscarFilaPorDenominadorEntero($filasSexo, $campoDenominador, $mes + 1) ?? $fila1;

        $mediana = $this->interpolarZscore(
            $this->numeroZscore($fila1[$campoMediana] ?? null),
            $this->numeroZscore($fila2[$campoMediana] ?? null),
            $fraccion
        );

        $l = $this->interpolarZscore(
            $this->numeroZscore($fila1[$campoL] ?? null),
            $this->numeroZscore($fila2[$campoL] ?? null),
            $fraccion
        );

        $s = $this->interpolarZscore(
            $this->numeroZscore($fila1[$campoS] ?? null),
            $this->numeroZscore($fila2[$campoS] ?? null),
            $fraccion
        );

        return $this->calcularZLms($valor, $mediana, $l, $s);
    }

    private function calcularZLms(float $valor, $mediana, $l, $s): ?float
    {
        $mediana = $this->numeroZscore($mediana);
        $l = $this->numeroZscore($l);
        $s = $this->numeroZscore($s);

        if ($valor <= 0 || $mediana === null || $mediana <= 0 || $l === null || $s === null || $s == 0.0) {
            return null;
        }

        $z = (pow($valor / $mediana, $l) - 1) / ($l * $s);

        if ($z < -3 || $z > 3) {
            $z = $this->ajustarZscoreExtremo($z, $valor, $mediana, $l, $s);
        }

        return $z;
    }

    private function ajustarZscoreExtremo(float $z, float $valor, float $mediana, float $l, float $s): float
    {
        $exp = 1 / $l;

        if ($z < -3) {
            $sd2 = $mediana * pow(1 + $l * $s * -2, $exp);
            $sd3 = $mediana * pow(1 + $l * $s * -3, $exp);

            return -3 + (($valor - $sd3) / ($sd2 - $sd3));
        }

        if ($z > 3) {
            $sd2 = $mediana * pow(1 + $l * $s * 2, $exp);
            $sd3 = $mediana * pow(1 + $l * $s * 3, $exp);

            return 3 + (($valor - $sd3) / ($sd3 - $sd2));
        }

        return $z;
    }

    private function zscoreAPercentil(float $z): string
    {
        $p = $this->normalCdfZscore($z) * 100;

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

    private function normalCdfZscore(float $x): float
    {
        return 0.5 * (1 + $this->erfZscore($x / sqrt(2)));
    }

    private function erfZscore(float $x): float
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

    private function obtenerDataZscore(string $clave): array
    {
        if (isset($this->antroZscoreCache[$clave])) {
            return $this->antroZscoreCache[$clave];
        }

        $archivos = [
            'zpe_dias'     => 'zpe_dias.json',
            'zpe_meses'    => 'zpe_meses.json',
            'zte_dias'     => 'zte_dias.json',
            'zte_meses_m'  => 'zte_meses.json',
            'zte_meses_f'  => 'zte_meses_parte2.json',
            'zimce_dias'   => 'zimce_dias.json',
            'zimce_meses'  => 'zimce_meses.json',
            'zpt_65_120'   => 'zpeso_talla.json',
            'zpt_45_110'   => 'zpeso_talla2.json',
            'zcc_dias'     => 'zcc_dias.json',
            'zcbi_dias'    => 'zcbi_dias.json',
        ];

        if (! isset($archivos[$clave])) {
            return $this->antroZscoreCache[$clave] = [];
        }

        $path = FCPATH . 'data/antro/' . $archivos[$clave];

        if (! is_file($path)) {
            log_message('error', 'Archivo z-score no encontrado: ' . $path);
            return $this->antroZscoreCache[$clave] = [];
        }

        $contenido = file_get_contents($path);
        $data = json_decode($contenido, true);

        if (! is_array($data)) {
            log_message('error', 'Archivo z-score invalido: ' . $path);
            return $this->antroZscoreCache[$clave] = [];
        }

        return $this->antroZscoreCache[$clave] = $data;
    }

    private function buscarFilaPorDenominadorEntero(array $filas, string $campo, int $valor): ?array
    {
        foreach ($filas as $fila) {
            $denominador = $this->numeroZscore($fila[$campo] ?? null);
            if ($denominador !== null && (int) $denominador === $valor) {
                return $fila;
            }
        }

        return null;
    }

    private function buscarFilaMasCercana(array $filas, string $campo, float $objetivo): ?array
    {
        $mejor = null;
        $mejorDistancia = null;

        foreach ($filas as $fila) {
            $valor = $this->numeroZscore($fila[$campo] ?? null);
            if ($valor === null) {
                continue;
            }

            $distancia = abs($valor - $objetivo);
            if ($mejor === null || $distancia < $mejorDistancia) {
                $mejor = $fila;
                $mejorDistancia = $distancia;
            }
        }

        return $mejor;
    }

    private function interpolarZscore(?float $a, ?float $b, float $fraccion): ?float
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

    private function numeroZscore($valor): ?float
    {
        if ($valor === null || trim((string) $valor) === '') {
            return null;
        }

        $valor = str_replace(',', '.', trim((string) $valor));

        return is_numeric($valor) ? (float) $valor : null;
    }

    private function normalizarSexoZscore(?string $sexo): ?string
    {
        $sexo = strtoupper(trim((string) $sexo));

        if ($sexo === '') {
            return null;
        }

        $sexo = substr($sexo, 0, 1);

        return in_array($sexo, ['M', 'F'], true) ? $sexo : null;
    }

    private function normalizarTextoZscore($valor): string
    {
        return strtoupper(trim((string) $valor));
    }


    private function valorItemAntroPorCodigo($sheet, array $headers, array $itemsPorCodigo, int $fila, string $codigo): string
    {
        $aliases = [$codigo];

        if (isset($itemsPorCodigo[$codigo]['nombre'])) {
            $aliases[] = (string) $itemsPorCodigo[$codigo]['nombre'];
        }

        return $this->valorPorAlias($sheet, $headers, $fila, $aliases);
    }

    private function valorPorAlias($sheet, array $headers, int $fila, array $aliases): string
    {
        foreach ($aliases as $alias) {
            $key = $this->normalizarHeaderExcel((string) $alias);

            if (isset($headers[$key])) {
                return trim((string) $sheet->getCell($headers[$key] . $fila)->getValue());
            }
        }

        return '';
    }

    private function mapearEncabezados($sheet): array
    {
        $highestColumn = $sheet->getHighestColumn();
        $highestColumnIndex = Coordinate::columnIndexFromString($highestColumn);

        $headers = [];

        for ($col = 1; $col <= $highestColumnIndex; $col++) {
            $letra = Coordinate::stringFromColumnIndex($col);
            $valor = trim((string) $sheet->getCell($letra . '1')->getValue());

            if ($valor === '') {
                continue;
            }

            $headers[$this->normalizarHeaderExcel($valor)] = $letra;
        }

        return $headers;
    }

    private function normalizarHeaderExcel(string $texto): string
    {
        $texto = strtolower(trim($texto));
        $texto = str_replace(
            ['á', 'é', 'í', 'ó', 'ú', 'ü', 'ñ', 'Á', 'É', 'Í', 'Ó', 'Ú', 'Ü', 'Ñ'],
            ['a', 'e', 'i', 'o', 'u', 'u', 'n', 'a', 'e', 'i', 'o', 'u', 'u', 'n'],
            $texto
        );

        $texto = preg_replace('/[^a-z0-9]+/', '_', $texto);
        return trim($texto, '_');
    }

    private function normalizarMetodoMedicionTalla(?string $valor): ?string
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

    private function ajustarTallaPorMetodoMedicion(?float $talla, ?int $edadDias, ?string $metodoMedicionTalla): ?float
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
} // fin clase

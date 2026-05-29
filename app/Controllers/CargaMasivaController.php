<?php
// ========================================================
// ARCHIVO: app/Controllers/CargaMasivaController.php
// Modulo de carga masiva de beneficiarios desde Excel
// ========================================================

namespace App\Controllers;

use App\Models\BeneficiariosModel;
use App\Models\DireccionModel;
use App\Models\EscolaridadModel;
use App\Models\JornadaBeneficiariosModel;
use App\Models\JornadaModel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CargaMasivaController extends BaseController
{
    /**
     * Descargar plantilla Excel para carga masiva
     */
    public function descargarPlantilla()
    {
        $path = FCPATH . 'assets/templates/plantilla_beneficiarios_digisalud.xlsx';

        if (!file_exists($path)) {
            return redirect()->back()->with('error', 'Plantilla no encontrada.');
        }

        return $this->response->download($path, null)->setFileName('plantilla_beneficiarios_digisalud.xlsx');
    }

    /**
     * Procesar carga masiva (AJAX POST)
     * Recibe: archivo Excel + jornada_id
     * Retorna: JSON con resultado
     */
    public function procesar($jornada_id)
    {
        $jornada_id = (int) $jornada_id;

        // Verificar que la jornada existe y esta activa
        $jornadaModel = new JornadaModel();
        $jornada = $jornadaModel->find($jornada_id);

        if (!$jornada || (int)($jornada['status_jor'] ?? 0) !== 1) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'La jornada no existe o no está activa.',
            ]);
        }

        // Validar archivo
        $archivo = $this->request->getFile('archivo_excel');

        if (!$archivo || !$archivo->isValid()) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'No se recibió un archivo válido.',
            ]);
        }

        // Validar extension
        $ext = strtolower($archivo->getClientExtension());
        if (!in_array($ext, ['xlsx', 'xls'], true)) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Solo se permiten archivos .xlsx o .xls',
            ]);
        }

        // Validar tamano (max 5MB)
        if ($archivo->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'El archivo no debe superar 5 MB.',
            ]);
        }

        // Mover a temporal
        $tempPath = WRITEPATH . 'uploads/' . $archivo->getRandomName();
        $archivo->move(WRITEPATH . 'uploads/', basename($tempPath));

        try {
            $spreadsheet = IOFactory::load($tempPath);
        } catch (\Exception $e) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'No se pudo leer el archivo Excel. Verifique el formato.',
            ]);
        }

        // Buscar la hoja "Beneficiarios"
        $sheet = null;
        foreach ($spreadsheet->getSheetNames() as $name) {
            if (strtolower(trim($name)) === 'beneficiarios') {
                $sheet = $spreadsheet->getSheetByName($name);
                break;
            }
        }

        if (!$sheet) {
            // Si no hay hoja "Beneficiarios", usar la primera hoja de datos
            $sheet = $spreadsheet->getSheet(0);
            if (strtolower(trim($sheet->getTitle())) === 'instrucciones' && $spreadsheet->getSheetCount() > 1) {
                $sheet = $spreadsheet->getSheet(1);
            }
        }

        $highestRow = $sheet->getHighestRow();

        if ($highestRow < 2) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'El archivo no contiene datos (solo encabezados).',
            ]);
        }

        if ($highestRow > 502) {
            @unlink($tempPath);
            return $this->response->setJSON([
                'ok'    => false,
                'error' => 'Máximo 500 registros por carga.',
            ]);
        }

        // Procesar filas
        $benefModel  = new BeneficiariosModel();
        $dirModel    = new DireccionModel();
        $escModel    = new EscolaridadModel();
        $jorBenModel = new JornadaBeneficiariosModel();

        $usuarioId = session('id_usuario') ?? 1;
        $ahora     = date('Y-m-d H:i:s');

        $insertados  = 0;
        $asociados   = 0; // ya existian, solo se asociaron
        $errores     = [];
        $duplicadosJornada = 0;

        for ($fila = 2; $fila <= $highestRow; $fila++) {
            $nombres         = trim((string) $sheet->getCell("A{$fila}")->getValue());
            $apellidos       = trim((string) $sheet->getCell("B{$fila}")->getValue());
            $sexo            = strtoupper(trim((string) $sheet->getCell("C{$fila}")->getValue()));
            $fechaNacRaw     = $sheet->getCell("D{$fila}")->getValue();
            $paisNacimiento  = trim((string) $sheet->getCell("E{$fila}")->getValue());
            $escuela         = trim((string) $sheet->getCell("F{$fila}")->getValue());
            $grado           = trim((string) $sheet->getCell("G{$fila}")->getValue());
            $seccion         = trim((string) $sheet->getCell("H{$fila}")->getValue());
            $turno           = trim((string) $sheet->getCell("I{$fila}")->getValue());

            // Direccion de residencia opcional. No confundir con pais_nacimiento.
            $paisResidencia  = trim((string) $sheet->getCell("J{$fila}")->getValue());
            $estado          = trim((string) $sheet->getCell("K{$fila}")->getValue());
            $municipio       = trim((string) $sheet->getCell("L{$fila}")->getValue());
            $parroquia       = trim((string) $sheet->getCell("M{$fila}")->getValue());
            $ciudadLocalidad = trim((string) $sheet->getCell("N{$fila}")->getValue());
            $detalleDireccion = trim((string) $sheet->getCell("O{$fila}")->getValue());

            // Fila vacia -> saltar
            if ($nombres === '' && $apellidos === '') {
                continue;
            }

            // -- Validaciones --
            $errFila = [];

            if (strlen($nombres) < 2) {
                $errFila[] = 'nombres vacío o muy corto';
            }
            if (strlen($apellidos) < 2) {
                $errFila[] = 'apellidos vacío o muy corto';
            }
            if (!in_array($sexo, ['F', 'M'], true)) {
                $errFila[] = "sexo inválido ('{$sexo}')";
            }

            // Parsear fecha
            $fechaNac = $this->parsearFechaExcel($fechaNacRaw);
            if (!$fechaNac) {
                $errFila[] = 'fecha_nacimiento inválida';
            } elseif (strtotime($fechaNac) > time()) {
                $errFila[] = 'fecha_nacimiento es futura';
            }

            if ($paisNacimiento === '') {
                $errFila[] = 'pais_nacimiento vacío';
            }

            // Validar turno si viene
            if ($turno !== '' && !in_array($turno, ['Mañana', 'Tarde', 'Completo', 'mañana', 'tarde', 'completo'], true)) {
                $errFila[] = "turno inválido ('{$turno}')";
            }

            if (!empty($errFila)) {
                $errores[] = [
                    'fila'    => $fila,
                    'nombre'  => "{$nombres} {$apellidos}",
                    'errores' => $errFila,
                ];
                continue;
            }

            // Normalizar turno
            $turno = $turno !== '' ? ucfirst(strtolower($turno)) : '';

            // Crear direccion solo si alguno de sus campos viene informado.
            $direccionId = $this->crearDireccionResidencia(
                $dirModel,
                $paisResidencia,
                $estado,
                $municipio,
                $parroquia,
                $ciudadLocalidad,
                $detalleDireccion
            );

            // -- Verificar si ya existe (mismo nombre + apellido + fecha_nacimiento) --
            $existente = $benefModel
                ->where('LOWER(nombres)', strtolower($nombres))
                ->where('LOWER(apellidos)', strtolower($apellidos))
                ->where('fecha_nacimiento', $fechaNac)
                ->first();

            if ($existente) {
                $idBeneficiario = $existente['id_beneficiario'];

                // Si el Excel trae direccion, vincularla al beneficiario existente.
                if ($direccionId) {
                    $benefModel->update($idBeneficiario, [
                        'direccion_id'   => $direccionId,
                        'modificado_en'  => $ahora,
                        'modificado_por' => $usuarioId,
                    ]);
                }

                // Ya esta asociado a esta jornada?
                $yaAsociado = $jorBenModel
                    ->where('id_beneficiario', $idBeneficiario)
                    ->where('jornada_id', $jornada_id)
                    ->first();

                if ($yaAsociado) {
                    if ((int)$yaAsociado['status_bc'] === 0) {
                        // Reactivar
                        $jorBenModel->update($yaAsociado['id_benef_jor'], ['status_bc' => 1]);
                        $asociados++;
                    } else {
                        $duplicadosJornada++;
                    }
                } else {
                    $jorBenModel->insert([
                        'id_beneficiario' => $idBeneficiario,
                        'jornada_id'      => $jornada_id,
                        'status_bc'       => 1,
                        'creado_en'       => $ahora,
                        'creado_por'      => $usuarioId,
                    ]);
                    $asociados++;
                }

                continue;
            }

            // -- Crear beneficiario nuevo --
            $idDigi = $this->construirIdDigi($paisNacimiento, $sexo, $nombres, $apellidos, $fechaNac);

            $idBeneficiario = $benefModel->insert([
                'id_digisalud'     => $idDigi,
                'nombres'          => $nombres,
                'apellidos'        => $apellidos,
                'fecha_nacimiento' => $fechaNac,
                'sexo'             => $sexo,
                'pais_nacimiento'  => $paisNacimiento,
                'direccion_id'     => $direccionId,
                'creado_en'        => $ahora,
                'creado_por'       => $usuarioId,
            ]);

            if (!$idBeneficiario) {
                $errores[] = [
                    'fila'    => $fila,
                    'nombre'  => "{$nombres} {$apellidos}",
                    'errores' => ['Error al insertar en la base de datos'],
                ];
                continue;
            }

            // Escolaridad (opcional)
            if ($escuela !== '') {
                $escModel->insert([
                    'id_beneficiario' => $idBeneficiario,
                    'nombre_escuela'  => $escuela,
                    'grado'           => $grado !== '' ? $grado : null,
                    'seccion'         => $seccion !== '' ? $seccion : null,
                    'turno'           => $turno !== '' ? $turno : null,
                    'status_esc'      => 1,
                    'creado_en'       => $ahora,
                    'creado_por'      => $usuarioId,
                ]);
            }

            // Asociar a la jornada
            $jorBenModel->insert([
                'id_beneficiario' => $idBeneficiario,
                'jornada_id'      => $jornada_id,
                'status_bc'       => 1,
                'creado_en'       => $ahora,
                'creado_por'      => $usuarioId,
            ]);

            $insertados++;
        }

        @unlink($tempPath);

        return $this->response->setJSON([
            'ok'                 => true,
            'insertados'         => $insertados,
            'asociados'          => $asociados,
            'duplicados_jornada' => $duplicadosJornada,
            'errores'            => $errores,
            'total_procesados'   => $insertados + $asociados + $duplicadosJornada + count($errores),
        ]);
    }

    // ================================================
    // HELPERS (misma logica que BeneficiariosController)
    // ================================================

    /**
     * Parsear fecha desde Excel (puede ser serial numerico o string)
     */
    private function parsearFechaExcel($valor): ?string
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        // Si es numerico -> serial de Excel
        if (is_numeric($valor)) {
            try {
                $dateTime = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject((float) $valor);
                return $dateTime->format('Y-m-d');
            } catch (\Exception $e) {
                return null;
            }
        }

        $valor = trim((string) $valor);

        // Formato AAAA-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $valor)) {
            $ts = strtotime($valor);
            return $ts !== false ? date('Y-m-d', $ts) : null;
        }

        // Formato DD/MM/AAAA o DD-MM-AAAA
        if (preg_match('#^(\d{1,2})[/\-](\d{1,2})[/\-](\d{4})$#', $valor, $m)) {
            $ts = strtotime("{$m[3]}-{$m[2]}-{$m[1]}");
            return $ts !== false ? date('Y-m-d', $ts) : null;
        }

        return null;
    }

    private function crearDireccionResidencia(
        DireccionModel $dirModel,
        string $paisResidencia,
        string $estado,
        string $municipio,
        string $parroquia,
        string $ciudadLocalidad,
        string $detalleDireccion
    ): ?int {
        $hayDireccion = $paisResidencia !== ''
            || $estado !== ''
            || $municipio !== ''
            || $parroquia !== ''
            || $ciudadLocalidad !== ''
            || $detalleDireccion !== '';

        if (!$hayDireccion) {
            return null;
        }

        $id = $dirModel->insert([
            'pais'      => $paisResidencia !== '' ? $paisResidencia : null,
            'estado'    => $estado !== '' ? $estado : null,
            'municipio' => $municipio !== '' ? $municipio : null,
            'parroquia' => $parroquia !== '' ? $parroquia : null,
            'ciudad'    => $ciudadLocalidad !== '' ? $ciudadLocalidad : null,
            'detalle'   => $detalleDireccion !== '' ? $detalleDireccion : null,
        ]);

        return $id ? (int) $id : null;
    }

    private function limpiarPartesNombre(?string $nombre): array
    {
        $nombre = trim($nombre ?? '');
        $nombre = preg_replace('/\s+/', ' ', $nombre);
        $partes = explode(' ', $nombre);
        return array_values(array_filter($partes, fn($p) => strlen(trim($p)) > 0));
    }

    private function normalizarFechaIdDigi(string $fecha): string
    {
        $fecha = trim($fecha);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return str_replace('-', '', $fecha);
        }
        if (preg_match('#^(\d{2})/(\d{2})/(\d{4})$#', $fecha, $m)) {
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
        $pais = strtoupper(substr($paisNacimiento ?: 'VE', 0, 2));
        $sexo = strtoupper(substr($sexo ?: 'M', 0, 1));

        $partesNombres   = $this->limpiarPartesNombre($nombres);
        $partesApellidos = $this->limpiarPartesNombre($apellidos);

        $primerNombre    = strtoupper(substr($partesNombres[0] ?? '', 0, 3));
        $segundoNombre   = isset($partesNombres[1]) ? strtoupper(substr($partesNombres[1], 0, 1)) : '';

        $primerApellido  = strtoupper(substr($partesApellidos[0] ?? '', 0, 3));
        $segundoApellido = isset($partesApellidos[1]) ? strtoupper(substr($partesApellidos[1], 0, 1)) : '';

        $fecha = $this->normalizarFechaIdDigi($fechaNacimiento ?: '2000-01-01');

        return $pais
            . $sexo
            . $primerNombre
            . $segundoNombre
            . $primerApellido
            . $segundoApellido
            . $fecha;
    }
}

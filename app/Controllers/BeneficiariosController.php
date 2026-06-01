<?php
// ========================================================
// ARCHIVO: app/Controllers/BeneficiariosController.php
// REEMPLAZAR COMPLETO
// ========================================================

namespace App\Controllers;

use App\Models\BeneficiariosModel;
use App\Models\DireccionModel;
use App\Models\EscolaridadModel;
use App\Models\FamiliaresModel;
use App\Models\AntecedentesBeneficiariosModel;
use App\Models\JornadaBeneficiariosModel;
use App\Models\OrganizacionModel;
use App\Models\JornadaModel;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class BeneficiariosController extends BaseController
{
    // ════════════════════════════════════════
    // INDEX — Lista principal desde sidebar
    // ════════════════════════════════════════
    public function index()
    {
        $rolActual      = (int) session()->get('id_rol');
        $orgSesion      = (int) session()->get('organizacion_id');
        $q              = trim($this->request->getGet('q') ?? '');
        $organizacionId = $this->request->getGet('organizacion_id');
        $page           = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage        = 15;

        // Solo roles 1, 2, 3 pueden acceder
        if (!in_array($rolActual, [1, 2, 3], true)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permisos para acceder a beneficiarios.');
        }

        // ═══ ORGANIZACIONES PARA EL FILTRO (solo rol 1 y 2) ═══
        $organizaciones = [];
        if (in_array($rolActual, [1, 2], true)) {
            $orgModel       = new OrganizacionModel();
            $organizaciones = $orgModel
                ->select('id_organizacion, nombre_org')
                ->where('status_org', 1)
                ->orderBy('nombre_org', 'ASC')
                ->findAll();
        }

        // ═══ CONSTRUIR QUERY DE BENEFICIARIOS ═══
        $db      = \Config\Database::connect();
        $builder = $db->table('beneficiarios AS b');

        $builder->select('
            b.id_beneficiario,
            b.id_digisalud,
            b.nombres,
            b.apellidos,
            b.sexo,
            b.fecha_nacimiento,
            b.pais_nacimiento,
            org.nombre_org,
            j.nombre_jornada,
            inst.nombre_institucion,
            esc.nombre_escuela,
            esc.grado,
            esc.seccion,
            esc.turno,
            dir.estado,
            dir.municipio,
            dir.parroquia
        ');

        // JOIN a jornadas para obtener organización
        $builder->join('beneficiarios_jornadas AS bj', 'bj.id_beneficiario = b.id_beneficiario AND bj.status_bc = 1', 'left');
        $builder->join('jornadas AS j', 'j.id_jornada = bj.jornada_id', 'left');
        $builder->join('organizacion AS org', 'org.id_organizacion = j.organizacion_id', 'left');
        $builder->join('instituciones AS inst', 'inst.id_institucion = j.institucion_id', 'left');
        $builder->join('escolaridad AS esc', 'esc.id_beneficiario = b.id_beneficiario AND esc.status_esc = 1', 'left');
        $builder->join('direcciones AS dir', 'dir.id_direccion = b.direccion_id', 'left');

        // ═══ FILTRO POR ROL ═══
        if ($rolActual === 3) {
            // Rol 3: solo beneficiarios de jornadas de SU organización
            $builder->where('j.organizacion_id', $orgSesion);
        } elseif (in_array($rolActual, [1, 2], true) && !empty($organizacionId)) {
            // Rol 1/2 con filtro de organización seleccionada
            $builder->where('j.organizacion_id', (int) $organizacionId);
        }

        // ═══ FILTRO DE BÚSQUEDA ═══
        if ($q !== '') {
            $builder->groupStart()
                ->like('b.nombres', $q)
                ->orLike('b.apellidos', $q)
                ->orLike('b.id_digisalud', $q)
                ->groupEnd();
        }

        // Agrupar por beneficiario para evitar duplicados
        $builder->groupBy('b.id_beneficiario');
        $builder->orderBy('b.apellidos', 'ASC');

        // ═══ CONTAR TOTAL ═══
        $builderCount = clone $builder;
        $totalBeneficiarios = $builderCount->countAllResults(false);

        // ═══ PAGINACIÓN ═══
        $offset = ($page - 1) * $perPage;
        $builder->limit($perPage, $offset);

        $beneficiarios = $builder->get()->getResultArray();

        // Calcular datos de paginación
        $totalPages = max(1, (int) ceil($totalBeneficiarios / $perPage));

        // En BeneficiariosController::index(), en el array $data al final:

        $data = [
            'beneficiarios'      => $beneficiarios,
            'organizaciones'     => $organizaciones,
            'totalBeneficiarios' => $totalBeneficiarios,
            'q'                  => $q,
            'organizacion_id'    => $organizacionId,
            'page'               => $page,
            'perPage'            => $perPage,
            'totalPages'         => $totalPages,
            'rolActual'          => $rolActual,   // ← AGREGAR ESTA LÍNEA
        ];

        return view('beneficiarios/index', $data);
    }

    // ════════════════════════════════════════
    // EXPORTAR A EXCEL (.xlsx)
    // ════════════════════════════════════════
    public function exportar()
    {
        $rolActual      = (int) session()->get('id_rol');
        $orgSesion      = (int) session()->get('organizacion_id');
        $q              = trim($this->request->getGet('q') ?? '');
        $organizacionId = $this->request->getGet('organizacion_id');

        if (!in_array($rolActual, [1, 2, 3], true)) {
            return redirect()->to(site_url('dashboard'))
                ->with('error', 'No tienes permisos.');
        }

        $db      = \Config\Database::connect();
        $builder = $db->table('beneficiarios AS b');

        $builder->select('
            b.id_digisalud AS IDENTIFICACION,
            b.nombres AS NOMBRES,
            b.apellidos AS APELLIDOS,
            b.sexo AS GENERO,
            b.fecha_nacimiento AS FECHA_DE_NACIMIENTO,
            b.pais_nacimiento AS PAIS_DE_NACIMIENTO,
            org.nombre_org AS ORGANIZACION,
            CONCAT(inst.nombre_institucion, " - ", j.nombre_jornada) AS CENTRO_JORNADA,
            esc.nombre_escuela AS NOMBRE_ESCUELA,
            esc.grado AS GRADO,
            esc.seccion AS SECCION,
            esc.turno AS TURNO,
            dir.estado AS ESTADO,
            dir.municipio AS MUNICIPIO,
            dir.parroquia AS PARROQUIA
        ');

        $builder->join('beneficiarios_jornadas AS bj', 'bj.id_beneficiario = b.id_beneficiario AND bj.status_bc = 1', 'left');
        $builder->join('jornadas AS j', 'j.id_jornada = bj.jornada_id', 'left');
        $builder->join('organizacion AS org', 'org.id_organizacion = j.organizacion_id', 'left');
        $builder->join('instituciones AS inst', 'inst.id_institucion = j.institucion_id', 'left');
        $builder->join('escolaridad AS esc', 'esc.id_beneficiario = b.id_beneficiario AND esc.status_esc = 1', 'left');
        $builder->join('direcciones AS dir', 'dir.id_direccion = b.direccion_id', 'left');

        // Filtro por rol
        if ($rolActual === 3) {
            $builder->where('j.organizacion_id', $orgSesion);
        } elseif (in_array($rolActual, [1, 2], true) && !empty($organizacionId)) {
            $builder->where('j.organizacion_id', (int) $organizacionId);
        }

        // Filtro búsqueda
        if ($q !== '') {
            $builder->groupStart()
                ->like('b.nombres', $q)
                ->orLike('b.apellidos', $q)
                ->orLike('b.id_digisalud', $q)
                ->groupEnd();
        }

        $builder->groupBy('b.id_beneficiario');
        $builder->orderBy('b.apellidos', 'ASC');

        $rows = $builder->get()->getResultArray();

        // ═══ GENERAR EXCEL CON PhpSpreadsheet ═══
        // Si PhpSpreadsheet no está instalado, exportar como CSV con BOM para Excel
        $filename = 'beneficiarios_' . date('Ymd_His');

        // Intentar usar PhpSpreadsheet
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            return $this->exportarXlsx($rows, $filename);
        }

        // Fallback: CSV con BOM (abre bien en Excel)
        return $this->exportarCsv($rows, $filename);
    }

    private function exportarXlsx(array $rows, string $filename)
    {
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Beneficiarios');

        // Encabezados
        $headers = [
            'IDENTIFICACION',
            'NOMBRES',
            'APELLIDOS',
            'GENERO',
            'FECHA DE NACIMIENTO',
            'PAIS DE NACIMIENTO',
            'ORGANIZACION',
            'CENTRO - JORNADA',
            'NOMBRE ESCUELA',
            'GRADO',
            'SECCION',
            'TURNO',
            'ESTADO',
            'MUNICIPIO',
            'PARROQUIA'
        ];

        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getColumnDimension($col)->setAutoSize(true);
            $col++;
        }

        // Datos
        $rowNum = 2;
        foreach ($rows as $r) {
            $sheet->setCellValue('A' . $rowNum, $r['IDENTIFICACION'] ?? '');
            $sheet->setCellValue('B' . $rowNum, $r['NOMBRES'] ?? '');
            $sheet->setCellValue('C' . $rowNum, $r['APELLIDOS'] ?? '');
            $sheet->setCellValue('D' . $rowNum, $r['GENERO'] ?? '');
            $sheet->setCellValue('E' . $rowNum, $r['FECHA_DE_NACIMIENTO'] ?? '');
            $sheet->setCellValue('F' . $rowNum, $r['PAIS_DE_NACIMIENTO'] ?? '');
            $sheet->setCellValue('G' . $rowNum, $r['ORGANIZACION'] ?? '');
            $sheet->setCellValue('H' . $rowNum, $r['CENTRO_JORNADA'] ?? '');
            $sheet->setCellValue('I' . $rowNum, $r['NOMBRE_ESCUELA'] ?? '');
            $sheet->setCellValue('J' . $rowNum, $r['GRADO'] ?? '');
            $sheet->setCellValue('K' . $rowNum, $r['SECCION'] ?? '');
            $sheet->setCellValue('L' . $rowNum, $r['TURNO'] ?? '');
            $sheet->setCellValue('M' . $rowNum, $r['ESTADO'] ?? '');
            $sheet->setCellValue('N' . $rowNum, $r['MUNICIPIO'] ?? '');
            $sheet->setCellValue('O' . $rowNum, $r['PARROQUIA'] ?? '');
            $rowNum++;
        }

        $writer = new Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    private function exportarCsv(array $rows, string $filename)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '.csv"');

        $output = fopen('php://output', 'w');

        // BOM para que Excel reconozca UTF-8
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // Encabezados
        fputcsv($output, [
            'IDENTIFICACION',
            'NOMBRES',
            'APELLIDOS',
            'GENERO',
            'FECHA DE NACIMIENTO',
            'PAIS DE NACIMIENTO',
            'ORGANIZACION',
            'CENTRO - JORNADA',
            'NOMBRE ESCUELA',
            'GRADO',
            'SECCION',
            'TURNO',
            'ESTADO',
            'MUNICIPIO',
            'PARROQUIA'
        ], ';');

        foreach ($rows as $r) {
            fputcsv($output, [
                $r['IDENTIFICACION'] ?? '',
                $r['NOMBRES'] ?? '',
                $r['APELLIDOS'] ?? '',
                $r['GENERO'] ?? '',
                $r['FECHA_DE_NACIMIENTO'] ?? '',
                $r['PAIS_DE_NACIMIENTO'] ?? '',
                $r['ORGANIZACION'] ?? '',
                $r['CENTRO_JORNADA'] ?? '',
                $r['NOMBRE_ESCUELA'] ?? '',
                $r['GRADO'] ?? '',
                $r['SECCION'] ?? '',
                $r['TURNO'] ?? '',
                $r['ESTADO'] ?? '',
                $r['MUNICIPIO'] ?? '',
                $r['PARROQUIA'] ?? '',
            ], ';');
        }

        fclose($output);
        exit;
    }

    // ════════════════════════════════════════
    // BUSCAR — para asociar a jornada
    // ════════════════════════════════════════
    public function buscar($jornada_id)
    {
        $jornadaModel  = new JornadaModel();
        $jorBenefModel = new JornadaBeneficiariosModel();

        // Cargar jornada con datos completos que necesita buscar.php
        $jornada = $jornadaModel
            ->select("jornadas.*, 
                  instituciones.nombre_institucion,
                  dir.ciudad,
                  GROUP_CONCAT(DISTINCT tpa.idtipo_pesquisa 
                      ORDER BY tpa.idtipo_pesquisa 
                      SEPARATOR ',') AS pesquisas")
            ->join(
                'instituciones',
                'instituciones.id_institucion = jornadas.institucion_id',
                'left'
            )
            ->join(
                'direcciones AS dir',
                'dir.id_direccion = instituciones.direccion_id',
                'left'
            )
            ->join(
                'tipo_pesquisa_actividad AS tpa',
                'tpa.id_jornada = jornadas.id_jornada',
                'left'
            )
            ->where('jornadas.id_jornada', $jornada_id)
            ->groupBy('jornadas.id_jornada')
            ->first();

        if (!$jornada) {
            return redirect()->to(site_url('jornadas'))
                ->with('error', 'Jornada no encontrada.');
        }

        // Contar beneficiarios ya asignados
        $totalAsignados = $jorBenefModel
            ->where('jornada_id', $jornada_id)
            ->where('status_bc', 1)
            ->countAllResults();

        return view('beneficiarios/buscar', [
            'jornada_id'                  => (int) $jornada_id,
            'jornada'                     => $jornada,
            'beneficiariosAsignados'      => [],
            'totalBeneficiariosAsignados' => $totalAsignados,
            'pesquisas_jornada'           => [],
        ]);
    }

    public function buscarAjax()
    {
        $model = new BeneficiariosModel();
        $term  = $this->request->getGet('q');

        if (strlen($term) < 2) return $this->response->setJSON([]);

        $data = $model
            ->select('id_beneficiario, id_digisalud, nombres, apellidos, fecha_nacimiento, sexo, pais_nacimiento')
            ->groupStart()
            ->like('nombres', $term)
            ->orLike('apellidos', $term)
            ->orLike('id_digisalud', $term)
            ->groupEnd()
            ->limit(15)->findAll();

        $famModel = new FamiliaresModel();

        foreach ($data as &$b) {
            $b['edad'] = $this->calcularEdadTexto($b['fecha_nacimiento']);

            $fam = $famModel
                ->select('familiares.relacion, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos')
                ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
                ->where('familiares.beneficiario_id', $b['id_beneficiario'])
                ->first();

            if ($fam) {
                $b['relacion_texto'] = $fam['relacion'];
                $b['parentesco'] = $fam['relacion'] . ': ' . $fam['rep_nombres'] . ' ' . $fam['rep_apellidos'];
            } else {
                $b['relacion_texto'] = '';
                $b['parentesco'] = 'Sin representante';
            }
        }

        return $this->response->setJSON($data);
    }
    public function antecedentesAjax()
    {
        $q    = $this->request->getGet('q');
        $tipo = $this->request->getGet('tipo');

        $db      = \Config\Database::connect();
        $builder = $db->table('antecedentes')
            ->select('id_antecedente, nombre, tipo, descripcion');

        // Filtrar por tipo si viene (Antecedentes Clínicos / Datos Socioeconómicos)
        if (!empty($tipo)) {
            $builder->where('tipo', $tipo);
        }

        // Filtrar por texto de búsqueda
        if (!empty($q) && strlen($q) >= 2) {
            $builder->groupStart()
                ->like('descripcion', $q)
                ->orLike('nombre', $q)
                ->groupEnd();
        }

        $data = $builder->orderBy('descripcion', 'ASC')
            ->limit(20)
            ->get()
            ->getResultArray();

        return $this->response->setJSON($data);
    }
    public function buscarAntecedentesAjax()
    {
        $beneficiarioId = $this->request->getGet('id');
        if (empty($beneficiarioId)) return $this->response->setJSON([]);

        $antModel = new AntecedentesBeneficiariosModel();
        $data = $antModel
            ->select('antecedentes_beneficiarios.*, antecedentes.nombre, antecedentes.tipo, antecedentes.descripcion')
            ->join('antecedentes', 'antecedentes.id_antecedente = antecedentes_beneficiarios.id_antecedente')
            ->where('antecedentes_beneficiarios.id_beneficiario', $beneficiarioId)
            ->findAll();

        return $this->response->setJSON($data);
    }

    // ════════════════════════════════════════
    // CREAR BENEFICIARIO
    // ════════════════════════════════════════
    public function create($jornada_id)
    {
        return view('beneficiarios/create', [
            'jornada_id' => $jornada_id,
            'errors'     => session('errors') ?? [],
        ]);
    }
    private function limpiarPartesNombre(?string $texto): array
    {
        $texto = trim((string) $texto);

        if ($texto === '') {
            return [];
        }

        // Divide por uno o más espacios y elimina valores vacíos
        return array_values(array_filter(preg_split('/\s+/', $texto)));
    }

    private function normalizarFechaIdDigi(?string $fecha): string
    {
        $fecha = trim((string) $fecha);

        if ($fecha === '') {
            return '20000101';
        }

        // Si viene desde input type="date": YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha)) {
            return str_replace('-', '', $fecha);
        }

        // Si llega como DD-MM-YYYY o DD/MM/YYYY
        if (preg_match('/^(\d{2})[-\/](\d{2})[-\/](\d{4})$/', $fecha, $m)) {
            return $m[3] . $m[2] . $m[1];
        }

        // Fallback: deja solo números
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

        $primerNombre   = $this->normalizarTextoParaIdDigi(substr($partesNombres[0] ?? '', 0, 3));
        $segundoNombre  = isset($partesNombres[1]) ? $this->normalizarTextoParaIdDigi(substr($partesNombres[1], 0, 1)) : '';

        $primerApellido  = $this->normalizarTextoParaIdDigi(substr($partesApellidos[0] ?? '', 0, 3));
        $segundoApellido = isset($partesApellidos[1]) ? $this->normalizarTextoParaIdDigi(substr($partesApellidos[1], 0, 1)) : '';

        $fecha = $this->normalizarFechaIdDigi($fechaNacimiento ?: '2000-01-01');

        return $pais
            . $sexo
            . $primerNombre
            . $segundoNombre
            . $primerApellido
            . $segundoApellido
            . $fecha;
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
    /**
     * ════════════════════════════════════════════════════════════════
     * STORE — Guardar nuevo beneficiario y asociarlo a la jornada
     * ════════════════════════════════════════════════════════════════
     *
     * CORREGIDO:
     *  - Escenario A: Representante existente (seleccionado del buscador)
     *  - Escenario B: Representante nuevo (formulario repNuevoBox)
     *  - Checkbox "Evaluar representante en esta jornada"
     *  - Antecedentes clínicos y socioeconómicos unificados
     *  - Usa lentes (id_antecedente = 38) sin duplicar
     *  - Observación general (id_antecedente = 15) sin duplicar
     */
    public function store($jornada_id)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();
        $jorModel   = new JornadaBeneficiariosModel();

        $post      = $this->request->getPost();
        $usuarioId = session('id_usuario');
        $ahora     = date('Y-m-d H:i:s');

        // ══════════════════════════════════════
        // 1) DIRECCIÓN (opcional)
        // ══════════════════════════════════════
        $direccion_id = null;
        if (!empty($post['estado'])) {
            $direccion_id = $dirModel->insert([
                'pais'      => $post['pais'] ?? 'Venezuela',
                'estado'    => $post['estado'] ?? null,
                'municipio' => $post['municipio'] ?? null,
                'parroquia' => $post['parroquia'] ?? null,
            ]);
        }

        // ══════════════════════════════════════
        // 2) BENEFICIARIO
        // ══════════════════════════════════════
        $sexo = strtoupper(substr($post['sexo'] ?? 'M', 0, 1));
        $fn   = $post['fecha_nacimiento'] ?? '2000-01-01';
        $post['nombres'] = $this->normalizarNombrePersona($post['nombres'] ?? '');
        $post['apellidos'] = $this->normalizarNombrePersona($post['apellidos'] ?? '');
        $idDigi = $this->construirIdDigi(
            $post['pais_nacimiento'] ?? 'VE',
            $sexo,
            $post['nombres'] ?? '',
            $post['apellidos'] ?? '',
            $fn
        );

        $id_beneficiario = $benefModel->insert([
            'id_digisalud'     => $idDigi,
            'nombres'          => $post['nombres'],
            'apellidos'        => $post['apellidos'],
            'fecha_nacimiento' => $fn,
            'sexo'             => $sexo === 'F' ? 'F' : 'M',
            'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
            'telefono'         => $post['telefono'] ?? null,
            'correo'           => $post['correo'] ?? null,
            'direccion_id'     => $direccion_id,
            'creado_en'        => $ahora,
            'creado_por'       => $usuarioId ?? 1,
        ]);

        // ══════════════════════════════════════
        // 3) ESCOLARIDAD (opcional)
        // ══════════════════════════════════════
        if (!empty($post['nombre_escuela'])) {
            $escModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'nombre_escuela'  => $post['nombre_escuela'],
                'grado'           => $post['grado'] ?? null,
                'seccion'         => $post['seccion'] ?? null,
                'turno'           => $post['turno'] ?? null,
                'status_esc'      => 1,
                'creado_en'       => $ahora,
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        // ══════════════════════════════════════
        // 4) REPRESENTANTE (FAMILIAR)
        //    Escenario A: existente → representante_id viene lleno
        //    Escenario B: nuevo    → representante_id vacío,
        //                            pero rep_nombres + rep_apellidos vienen llenos
        // ══════════════════════════════════════
        $repId = $post['representante_id'] ?? null;

        // ── Escenario B: crear representante como beneficiario nuevo ──
        if (empty($repId) && !empty(trim($post['rep_nombres'] ?? '')) && !empty(trim($post['rep_apellidos'] ?? ''))) {

            $repSexo = strtoupper(substr($post['rep_sexo'] ?? 'M', 0, 1));
            $repFn   = !empty($post['rep_fecha_nacimiento']) ? $post['rep_fecha_nacimiento'] : '1980-01-01';

            $repIdDigi = $this->construirIdDigi(
                $post['pais_nacimiento'] ?? 'VE',
                $repSexo,
                $post['rep_nombres'] ?? '',
                $post['rep_apellidos'] ?? '',
                $repFn
            );

            $repId = $benefModel->insert([
                'id_digisalud'     => $repIdDigi,
                'nombres'          => trim($post['rep_nombres']),
                'apellidos'        => trim($post['rep_apellidos']),
                'fecha_nacimiento' => $repFn,
                'sexo'             => $repSexo === 'F' ? 'F' : 'M',
                'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
                'telefono'         => $post['rep_telefono_nuevo'] ?? null,
                'direccion_id'     => $direccion_id, // misma dirección del beneficiario
                'creado_en'        => $ahora,
                'creado_por'       => $usuarioId ?? 1,
            ]);

            // Si marcó "Evaluar al representante en esta jornada"
            if (!empty($post['evaluar_representante']) && !empty($repId)) {
                $jorModel->insert([
                    'id_beneficiario' => $repId,
                    'jornada_id'      => $jornada_id,
                    'status_bc'       => 1,
                    'creado_en'       => $ahora,
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        // ── Insertar relación familiar (aplica a ambos escenarios) ──
        if (!empty($repId)) {
            $famModel->insert([
                'beneficiario_id'               => $id_beneficiario,
                'beneficiario_id_representante'  => $repId,
                'relacion'                       => $post['relacion'] ?? '',
                'telefono'                       => $post['telefono_representante'] ?? '',
            ]);
        }

        // ══════════════════════════════════════
        // 5) ANTECEDENTES CLÍNICOS Y SOCIOECONÓMICOS
        //    Ambos llegan en el mismo array antecedentes[]
        // ══════════════════════════════════════
        $antecedentes  = $post['antecedentes'] ?? [];
        $idsInsertados = []; // Para evitar duplicados con usa_lentes (38) y observación (15)

        if (is_array($antecedentes)) {
            foreach ($antecedentes as $idAnt) {
                $idAnt = (int) $idAnt;
                if ($idAnt <= 0 || in_array($idAnt, $idsInsertados, true)) {
                    continue;
                }

                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $idAnt,
                    'jornada_id'      => $jornada_id,
                    'creado_en'       => $ahora,
                    'creado_por'      => $usuarioId ?? 1,
                ]);

                $idsInsertados[] = $idAnt;
            }
        }

        // ── Checkbox "Usa lentes" (id_antecedente = 38) ──
        if (!empty($post['usa_lentes']) && !in_array(38, $idsInsertados, true)) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 38,
                'jornada_id'      => $jornada_id,
                'creado_en'       => $ahora,
                'creado_por'      => $usuarioId ?? 1,
            ]);
            $idsInsertados[] = 38;
        }

        // ── Observación general (id_antecedente = 15 "OTRO") ──
        $obs = trim($post['observacion_antecedentes'] ?? '');
        if ($obs !== '') {
            // Si ya se insertó el 15 desde el array de antecedentes,
            // actualizar ese registro con la observación
            if (in_array(15, $idsInsertados, true)) {
                $antModel
                    ->where('id_beneficiario', $id_beneficiario)
                    ->where('id_antecedente', 15)
                    ->where('jornada_id', $jornada_id)
                    ->set(['observacion' => $obs])
                    ->update();
            } else {
                // Si no estaba en el array, crear registro nuevo
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => 15,
                    'jornada_id'      => $jornada_id,
                    'observacion'     => $obs,
                    'creado_en'       => $ahora,
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        // ══════════════════════════════════════
        // 6) ASOCIAR BENEFICIARIO A JORNADA
        // ══════════════════════════════════════
        $jorModel->insert([
            'id_beneficiario' => $id_beneficiario,
            'jornada_id'      => $jornada_id,
            'status_bc'       => 1,
            'creado_en'       => $ahora,
            'creado_por'      => $usuarioId ?? 1,
        ]);

        return redirect()
            ->to(base_url("jornadas/$jornada_id/beneficiarios"))
            ->with('success', 'Beneficiario registrado y asociado correctamente.');
    }

    // ════════════════════════════════════════
    // EDITAR — Cargar datos del beneficiario
    // ════════════════════════════════════════
    public function edit($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();

        // ── Beneficiario ──
        $beneficiario = $benefModel->find($id_beneficiario);
        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        // ── Dirección ──
        $direccion = null;
        if (!empty($beneficiario['direccion_id'])) {
            $direccion = $dirModel->find($beneficiario['direccion_id']);
        }

        // ── Escolaridad activa ──
        $escolaridad = $escModel
            ->where('id_beneficiario', $id_beneficiario)
            ->where('status_esc', 1)
            ->first();

        // ── Familiar / Representante ──
        $familiar = $famModel
            ->select('familiares.*, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos, rep.id_digisalud AS rep_id_digisalud')
            ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
            ->where('familiares.beneficiario_id', $id_beneficiario)
            ->first();

        // ── Antecedentes (todos) ──
        $antecedentes = $antModel
            ->select('antecedentes_beneficiarios.*, antecedentes.nombre, antecedentes.tipo, antecedentes.descripcion')
            ->join('antecedentes', 'antecedentes.id_antecedente = antecedentes_beneficiarios.id_antecedente')
            ->where('antecedentes_beneficiarios.id_beneficiario', $id_beneficiario)
            ->findAll();

        // ══════════════════════════════════════════════════
        // Clasificar antecedentes en las variables que
        // la vista edit.php necesita:
        //   $antClinico  → tipo "Antecedentes Clínicos" (sin id 38)
        //   $antSocio    → tipo "Datos Socioeconómicos"
        //   $usaLentes   → true si existe id_antecedente = 38
        //   $observacion → texto del id_antecedente = 15
        // ══════════════════════════════════════════════════
        $antClinico  = [];
        $antSocio    = [];
        $usaLentes   = false;
        $observacion = '';

        foreach ($antecedentes as $a) {
            $idAnt = (int) $a['id_antecedente'];

            if ($idAnt === 38) {
                $usaLentes = true;
                continue;
            }

            if ($idAnt === 15) {
                $observacion = $a['observacion'] ?? '';
                continue;
            }

            if (($a['tipo'] ?? '') === 'Datos Socioeconómicos') {
                $antSocio[] = $a;
            } else {
                $antClinico[] = $a;
            }
        }

        // ── Enviar a la vista ──
        return view('beneficiarios/edit', [
            'beneficiario' => $beneficiario,
            'direccion'    => $direccion,
            'escolaridad'  => $escolaridad,
            'familiar'     => $familiar,
            'antecedentes' => $antecedentes,   // array completo (por si se necesita)
            'antClinico'   => $antClinico,      // ← NUEVO: vista lo necesita
            'antSocio'     => $antSocio,        // ← NUEVO: vista lo necesita
            'usaLentes'    => $usaLentes,       // ← NUEVO: vista lo necesita
            'observacion'  => $observacion,     // ← NUEVO: vista lo necesita
        ]);
    }
    public function update($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();

        $post      = $this->request->getPost();
        $usuarioId = session('id_usuario');

        $beneficiario = $benefModel->find($id_beneficiario);
        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        // Actualizar dirección
        if (!empty($post['estado'])) {
            if (!empty($beneficiario['direccion_id'])) {
                $dirModel->update($beneficiario['direccion_id'], [
                    'pais'      => $post['pais'] ?? 'Venezuela',
                    'estado'    => $post['estado'] ?? null,
                    'municipio' => $post['municipio'] ?? null,
                    'parroquia' => $post['parroquia'] ?? null,
                ]);
            } else {
                $newDirId = $dirModel->insert([
                    'pais'      => $post['pais'] ?? 'Venezuela',
                    'estado'    => $post['estado'] ?? null,
                    'municipio' => $post['municipio'] ?? null,
                    'parroquia' => $post['parroquia'] ?? null,
                ]);
                $post['direccion_id'] = $newDirId;
            }
        }

        // Actualizar beneficiario
        // Recalcular ID DigiSalud cuando se edita perfil
        $sexoUpdate = strtoupper(substr($post['sexo'] ?? $beneficiario['sexo'] ?? 'M', 0, 1));
        $fechaUpdate = $post['fecha_nacimiento'] ?? $beneficiario['fecha_nacimiento'] ?? '2000-01-01';
        $paisUpdate = $post['pais_nacimiento'] ?? $beneficiario['pais_nacimiento'] ?? 'Venezuela';

        $idDigiUpdate = $this->construirIdDigi(
            $paisUpdate,
            $sexoUpdate,
            $post['nombres'] ?? $beneficiario['nombres'] ?? '',
            $post['apellidos'] ?? $beneficiario['apellidos'] ?? '',
            $fechaUpdate
        );

        // Actualizar beneficiario
        $benefModel->update($id_beneficiario, [
            'id_digisalud'     => $idDigiUpdate,
            'nombres'          => $post['nombres'],
            'apellidos'        => $post['apellidos'],
            'fecha_nacimiento' => $fechaUpdate,
            'sexo'             => $sexoUpdate === 'F' ? 'F' : 'M',
            'pais_nacimiento'  => $paisUpdate,
            'telefono'         => $post['telefono'] ?? null,
            'correo'           => $post['correo'] ?? null,
            'direccion_id'     => $post['direccion_id'] ?? $beneficiario['direccion_id'],
            'modificado_en'    => date('Y-m-d H:i:s'),
            'modificado_por'   => $usuarioId ?? 1,
        ]);

        // Escolaridad
        if (!empty($post['nombre_escuela'])) {
            $escActiva = $escModel
                ->where('id_beneficiario', $id_beneficiario)
                ->where('status_esc', 1)
                ->first();

            $escData = [
                'id_beneficiario' => $id_beneficiario,
                'nombre_escuela'  => $post['nombre_escuela'],
                'grado'           => $post['grado'] ?? null,
                'seccion'         => $post['seccion'] ?? null,
                'turno'           => $post['turno'] ?? null,
                'status_esc'      => 1,
            ];

            if ($escActiva) {
                $escData['modificado_en']  = date('Y-m-d H:i:s');
                $escData['modificado_por'] = $usuarioId ?? 1;
                $escModel->update($escActiva['escolaridad_id'], $escData);
            } else {
                $escData['creado_en']  = date('Y-m-d H:i:s');
                $escData['creado_por'] = $usuarioId ?? 1;
                $escModel->insert($escData);
            }
        }

        // Antecedentes: eliminar anteriores sin jornada y reinsertar
        $antModel->where('id_beneficiario', $id_beneficiario)
            ->where('jornada_id', null)
            ->delete();

        $antecedentes = $post['antecedentes'] ?? [];
        if (is_array($antecedentes)) {
            foreach ($antecedentes as $idAnt) {
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $idAnt,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        if (!empty($post['usa_lentes'])) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 38,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        $obs = $post['observacion_antecedentes'] ?? '';
        if (!empty($obs)) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 15,
                'observacion'     => $obs,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        return redirect()->back()->with('success', 'Beneficiario actualizado correctamente');
    }

    // ════════════════════════════════════════
    // HISTORIAL
    // ════════════════════════════════════════
    public function historial($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $beneficiario = $benefModel->find($id_beneficiario);

        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        // Jornadas asociadas
        $db = \Config\Database::connect();
        $jornadas = $db->table('beneficiarios_jornadas AS bj')
            ->select('bj.*, j.nombre_jornada, j.fecha_inicio, org.nombre_org, inst.nombre_institucion')
            ->join('jornadas AS j', 'j.id_jornada = bj.jornada_id')
            ->join('organizacion AS org', 'org.id_organizacion = j.organizacion_id', 'left')
            ->join('instituciones AS inst', 'inst.id_institucion = j.institucion_id', 'left')
            ->where('bj.id_beneficiario', $id_beneficiario)
            ->get()->getResultArray();

        return view('beneficiarios/historial', [
            'beneficiario' => $beneficiario,
            'jornadas'     => $jornadas,
        ]);
    }

    // ════════════════════════════════════════
    // UTILIDADES
    // ════════════════════════════════════════
    private function calcularEdadTexto($fechaNac): string
    {
        if (empty($fechaNac)) return '—';
        try {
            $nac  = new \DateTime($fechaNac);
            $hoy  = new \DateTime();
            $diff = $hoy->diff($nac);
            if ($diff->y > 0) return $diff->y . ' año' . ($diff->y > 1 ? 's' : '');
            if ($diff->m > 0) return $diff->m . ' mes' . ($diff->m > 1 ? 'es' : '');
            return $diff->d . ' día' . ($diff->d > 1 ? 's' : '');
        } catch (\Exception $e) {
            return '—';
        }
    }
}

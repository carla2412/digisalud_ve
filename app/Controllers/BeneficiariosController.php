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

        $data = [
            'beneficiarios'       => $beneficiarios,
            'organizaciones'      => $organizaciones,
            'totalBeneficiarios'  => $totalBeneficiarios,
            'q'                   => $q,
            'organizacion_id'     => $organizacionId,
            'page'                => $page,
            'perPage'             => $perPage,
            'totalPages'          => $totalPages,
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
            'IDENTIFICACION', 'NOMBRES', 'APELLIDOS', 'GENERO',
            'FECHA DE NACIMIENTO', 'PAIS DE NACIMIENTO', 'ORGANIZACION',
            'CENTRO - JORNADA', 'NOMBRE ESCUELA', 'GRADO', 'SECCION',
            'TURNO', 'ESTADO', 'MUNICIPIO', 'PARROQUIA'
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
            'IDENTIFICACION', 'NOMBRES', 'APELLIDOS', 'GENERO',
            'FECHA DE NACIMIENTO', 'PAIS DE NACIMIENTO', 'ORGANIZACION',
            'CENTRO - JORNADA', 'NOMBRE ESCUELA', 'GRADO', 'SECCION',
            'TURNO', 'ESTADO', 'MUNICIPIO', 'PARROQUIA'
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
        return view('beneficiarios/buscar', ['jornada_id' => $jornada_id]);
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
        return view('beneficiarios/create', ['jornada_id' => $jornada_id]);
    }

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

        // ══ 1) DIRECCIÓN ══
        $direccion_id = null;
        if (!empty($post['estado'])) {
            $direccion_id = $dirModel->insert([
                'pais'      => $post['pais'] ?? 'Venezuela',
                'estado'    => $post['estado'] ?? null,
                'municipio' => $post['municipio'] ?? null,
                'parroquia' => $post['parroquia'] ?? null,
            ]);
        }

        // ══ 2) BENEFICIARIO ══
        $sexo   = strtoupper(substr($post['sexo'] ?? 'M', 0, 1));
        $nombre = strtoupper(substr($post['nombres'] ?? '', 0, 3));
        $apell  = strtoupper(substr($post['apellidos'] ?? '', 0, 3));
        $fn     = $post['fecha_nacimiento'] ?? '2000-01-01';
        $pais   = strtoupper(substr($post['pais_nacimiento'] ?? 'VE', 0, 2));
        $idDigi = $pais . $sexo . $nombre . $apell . str_replace('-', '', $fn);

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
            'creado_en'        => date('Y-m-d H:i:s'),
            'creado_por'       => $usuarioId ?? 1,
        ]);

        // ══ 3) ESCOLARIDAD ══
        if (!empty($post['nombre_escuela'])) {
            $escModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'nombre_escuela'  => $post['nombre_escuela'],
                'grado'           => $post['grado'] ?? null,
                'seccion'         => $post['seccion'] ?? null,
                'turno'           => $post['turno'] ?? null,
                'status_esc'      => 1,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        // ══ 4) REPRESENTANTE (FAMILIAR) ══
        $repId = $post['representante_id'] ?? null;
        if (!empty($repId)) {
            $famModel->insert([
                'beneficiario_id'              => $id_beneficiario,
                'beneficiario_id_representante' => $repId,
                'relacion'                      => $post['relacion'] ?? '',
                'telefono'                      => $post['telefono_representante'] ?? '',
            ]);
        }

        // ══ 5) ANTECEDENTES CLÍNICOS ══
        $antecedentes = $post['antecedentes'] ?? [];
        if (is_array($antecedentes)) {
            foreach ($antecedentes as $idAnt) {
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $idAnt,
                    'jornada_id'      => $jornada_id,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        // Checkbox "Usa lentes"
        if (!empty($post['usa_lentes'])) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 38,
                'jornada_id'      => $jornada_id,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        // Observación general
        $obs = $post['observacion_antecedentes'] ?? '';
        if (!empty($obs)) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 15,
                'jornada_id'      => $jornada_id,
                'observacion'     => $obs,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        // ══ 6) ASOCIAR A JORNADA ══
        $jorModel->insert([
            'id_beneficiario' => $id_beneficiario,
            'jornada_id'      => $jornada_id,
            'status_bc'       => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => $usuarioId ?? 1,
        ]);

        return redirect()->to("/jornadas/$jornada_id/beneficiarios")
                         ->with('success', 'Beneficiario registrado y asociado correctamente');
    }

    // ════════════════════════════════════════
    // EDITAR
    // ════════════════════════════════════════
    public function edit($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();

        $beneficiario = $benefModel->find($id_beneficiario);
        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        $direccion = null;
        if (!empty($beneficiario['direccion_id'])) {
            $direccion = $dirModel->find($beneficiario['direccion_id']);
        }

        $escolaridad = $escModel
            ->where('id_beneficiario', $id_beneficiario)
            ->where('status_esc', 1)
            ->first();

        $familiar = $famModel
            ->select('familiares.*, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos, rep.id_digisalud AS rep_id_digisalud')
            ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
            ->where('familiares.beneficiario_id', $id_beneficiario)
            ->first();

        $antecedentes = $antModel
            ->select('antecedentes_beneficiarios.*, antecedentes.nombre, antecedentes.tipo, antecedentes.descripcion')
            ->join('antecedentes', 'antecedentes.id_antecedente = antecedentes_beneficiarios.id_antecedente')
            ->where('antecedentes_beneficiarios.id_beneficiario', $id_beneficiario)
            ->findAll();

        return view('beneficiarios/edit', [
            'beneficiario' => $beneficiario,
            'direccion'    => $direccion,
            'escolaridad'  => $escolaridad,
            'familiar'     => $familiar,
            'antecedentes' => $antecedentes,
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
        $benefModel->update($id_beneficiario, [
            'nombres'          => $post['nombres'],
            'apellidos'        => $post['apellidos'],
            'fecha_nacimiento' => $post['fecha_nacimiento'],
            'sexo'             => $post['sexo'] ?? $beneficiario['sexo'],
            'pais_nacimiento'  => $post['pais_nacimiento'] ?? $beneficiario['pais_nacimiento'],
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
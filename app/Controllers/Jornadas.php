<?php
/**
 * =====================================================
 * ARCHIVO: app/Controllers/Jornadas.php
 * REEMPLAZAR COMPLETO
 * =====================================================
 */

namespace App\Controllers;

use App\Models\JornadaModel;
use App\Models\OrganizacionModel;
use App\Models\InstitucionesModel;

class Jornadas extends BaseController
{
    protected $jornadaModel;

    public function __construct()
    {
        helper(['url', 'form']);
        $this->jornadaModel = new JornadaModel();
    }

    // ================================
    // INDEX — Listado con filtros y paginación
    // ================================
    public function index()
    {
        $rol       = (int) session('id_rol');
        $orgSesion = (int) session('organizacion_id');

        // Parámetros de filtros (GET)
        $busqueda = trim($this->request->getGet('q') ?? '');
        $status   = $this->request->getGet('status');
        $orden    = $this->request->getGet('orden');
        $page     = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage  = 5;

        $builder = $this->jornadaModel
            ->select("jornadas.*, 
                      organizaciones.nombre_org,
                      instituciones.nombre_institucion, dir.ciudad,
                      GROUP_CONCAT(tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
            ->join('organizacion AS organizaciones', 'organizaciones.id_organizacion = jornadas.organizacion_id', 'left')
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->join('tipo_pesquisa AS tp', 'tp.idtipo_pesquisa = tpa.idtipo_pesquisa', 'left')
            ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
            ->where('jornadas.status_jor !=', 0)
            ->groupBy('jornadas.id_jornada');

        // FIX: Roles 3,4,5,6,7 solo su organización
        if (!in_array($rol, [1, 2])) {
            $builder->where('jornadas.organizacion_id', $orgSesion);
        }

        // Filtro búsqueda
        if ($busqueda !== '') {
            $builder->groupStart()
                ->like('jornadas.nombre_jornada', $busqueda)
                ->orLike('organizaciones.nombre_org', $busqueda)
                ->orLike('instituciones.nombre_institucion', $busqueda)
                ->orLike('dir.ciudad', $busqueda)
            ->groupEnd();
        }

        // Filtro status
        if ($status !== null && $status !== '') {
            $builder->where('jornadas.status_jor', (int) $status);
        }

        // Orden
        $ordenDir = ($orden === 'asc') ? 'ASC' : 'DESC';
        $builder->orderBy('jornadas.fecha_inicio', $ordenDir);

        // Contar total
        $builderCount  = clone $builder;
        $totalJornadas = $builderCount->countAllResults(false);

        // Paginar de 5 en 5
        $offset   = ($page - 1) * $perPage;
        $jornadas = $builder->limit($perPage, $offset)->findAll();

        $totalPages    = max(1, (int) ceil($totalJornadas / $perPage));
        $instituciones = (new InstitucionesModel())->findAll();

        return view('jornadas/index', [
            'jornadas'       => $jornadas,
            'instituciones'  => $instituciones,
            'busqueda'       => $busqueda,
            'status'         => $status,
            'orden'          => $orden ?? 'desc',
            'page'           => $page,
            'perPage'        => $perPage,
            'totalJornadas'  => $totalJornadas,
            'totalPages'     => $totalPages,
        ]);
    }

    // ================================
    // LISTAR (AJAX) — mantener compatibilidad
    // ================================
    public function listar()
    {
        $rol       = (int) session('id_rol');
        $orgSesion = (int) session('organizacion_id');

        $builder = $this->jornadaModel
            ->select("jornadas.*, 
                      organizaciones.nombre_org,
                      instituciones.nombre_institucion,
                      GROUP_CONCAT(tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
            ->join('organizacion AS organizaciones', 'organizaciones.id_organizacion = jornadas.organizacion_id', 'left')
            ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
            ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
            ->join('tipo_pesquisa AS tp', 'tp.idtipo_pesquisa = tpa.idtipo_pesquisa', 'left')
            ->where('jornadas.status_jor !=', 0)
            ->groupBy('jornadas.id_jornada');

        if (!in_array($rol, [1, 2])) {
            $builder->where('jornadas.organizacion_id', $orgSesion);
        }

        $jornadas = $builder->findAll();
        return $this->response->setJSON(['data' => $jornadas]);
    }

    // ================================
    // CAMBIAR STATUS
    // ================================
    public function cambiarStatus()
    {
        $id     = $this->request->getPost('id');
        $status = $this->request->getPost('status');

        $this->jornadaModel->update($id, [
            'status_jor'     => $status,
            'modificado_en'  => date('Y-m-d H:i:s'),
            'modificado_por' => session('id_usuario'),
        ]);

        return $this->response->setJSON(['success' => true]);
    }

    // ================================
    // CREAR — Mostrar formulario
    // ================================
    public function crear()
    {
        $orgModel          = new \App\Models\OrganizacionModel();
        $tipoPesquisaModel = new \App\Models\TipoPesquisaModel();

        $rol       = (int) session('id_rol');
        $orgSesion = (int) session('organizacion_id');

        $pesquisas = $tipoPesquisaModel->findAll();

        // FIX: Roles 3,4,5,6,7 solo su organización
        if (in_array($rol, [1, 2])) {
            $organizaciones = $orgModel->findAll();
            $soloLectura    = false;
        } else {
            $organizaciones = $orgModel->where('id_organizacion', $orgSesion)->findAll();
            $soloLectura    = true;
        }

        return view('jornadas/crear', [
            'organizaciones' => $organizaciones,
            'soloLectura'    => $soloLectura,
            'orgSesion'      => $orgSesion,
            'pesquisas'      => $pesquisas,
        ]);
    }

    // ================================
    // GUARDAR — Procesar creación
    // ================================
    public function guardar()
    {
        $db = \Config\Database::connect();

        $direccionModel   = new \App\Models\DireccionModel();
        $institucionModel = new \App\Models\InstitucionesModel();
        $jornadaModel     = new \App\Models\JornadaModel();
        $instActModel     = new \App\Models\InstitucionActividadModel();

        // ========= 1) GUARDAR DIRECCIÓN =========
        // FIX: Limpiar prefijo "Estado " que devuelve Nominatim
        $estadoRaw = $this->request->getPost('estado') ?? '';
        $estado    = preg_replace('/^Estado\s+/i', '', trim($estadoRaw));

        $direccionData = [
            'pais'        => $this->request->getPost('pais') ?: 'Venezuela',
            'estado'      => $estado,
            'municipio'   => $this->request->getPost('municipio'),   // FIX: ahora se guarda
            'parroquia'   => $this->request->getPost('parroquia'),   // FIX: ahora se guarda
            'ciudad'      => $this->request->getPost('ciudad'),
            'detalle'     => $this->request->getPost('detalle'),     // FIX: campo detalle
            'coordenadas' => $this->request->getPost('coords'),
        ];
        $direccion_id = $direccionModel->insert($direccionData, true);

        // ========= 2) GUARDAR INSTITUCIÓN =========
        // FIX: Select con sugerencias — puede ser existente o nueva
        $institucion_id_existente = $this->request->getPost('institucion_id');
        $nombre_institucion       = trim($this->request->getPost('nombre_institucion') ?? '');

        if (!empty($institucion_id_existente) && is_numeric($institucion_id_existente)) {
            // Usar existente, actualizar dirección y tipo
            $newInstId = (int) $institucion_id_existente;
            $db->table('instituciones')
               ->where('id_institucion', $newInstId)
               ->update([
                   'tipo'         => $this->request->getPost('tipo_jornada'),
                   'direccion_id' => $direccion_id,
               ]);
        } else {
            // Crear nueva institución
            $lastId    = $institucionModel->selectMax('id_institucion')->first()['id_institucion'] ?? 0;
            $newInstId = $lastId + 1;

            $institucionData = [
                'id_institucion'     => $newInstId,
                'nombre_institucion' => $nombre_institucion,
                'tipo'               => $this->request->getPost('tipo_jornada'),
                'direccion_id'       => $direccion_id,
            ];
            $institucionModel->insert($institucionData);
        }

        // ========= 3) GUARDAR JORNADA =========
        // FIX: Si el select está disabled, tomar del hidden
        $organizacion_id = $this->request->getPost('organizacion_id')
                        ?: $this->request->getPost('organizacion_id_hidden');

        $jornadaData = [
            'nombre_jornada'  => $this->request->getPost('nombre_jornada'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'organizacion_id' => $organizacion_id,
            'institucion_id'  => $newInstId,
            'status_jor'      => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => session('id_usuario'),
        ];
        $jornadaModel->insert($jornadaData);
        $id_jornada = $jornadaModel->insertID();

        // ========= 4) RELACIONAR INSTITUCIÓN ↔ JORNADA =========
        $instActModel->insert([
            'id_institucion' => $newInstId,
            'id_jornada'     => $id_jornada,
            'id_centro'      => null,
            'status_act'     => 1,
        ]);

        // ========= 5) GUARDAR PESQUISAS =========
        $pesquisasSeleccionadas     = $this->request->getPost('pesquisas') ?? [];
        $tipoPesquisaActividadModel = new \App\Models\TipoPesquisaActividadModel();

        foreach ($pesquisasSeleccionadas as $idPesquisa) {
            $tipoPesquisaActividadModel->insert([
                'idtipo_pesquisa' => $idPesquisa,
                'id_jornada'      => $id_jornada,
                'status_pesq_act' => 1,
            ]);
        }

        return redirect()->to('/jornadas')->with('success', 'Jornada creada exitosamente.');
    }

    // ════════════════════════════════════════════════
    // EDITAR — Mostrar formulario (GET)
    // ════════════════════════════════════════════════
    public function editar($id_jornada)
    {
        $rol = (int) session('id_rol');
        if (!in_array($rol, [1, 2, 3])) {
            return redirect()->to('/jornadas')->with('error', 'No tienes permiso para editar jornadas.');
        }

        $jornada = $this->jornadaModel->getJornadaConDireccion($id_jornada);

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada.');
        }

        $pesquisasSeleccionadas = $this->jornadaModel->getPesquisasPorJornada($id_jornada);

        $orgModel          = new \App\Models\OrganizacionModel();
        $tipoPesquisaModel = new \App\Models\TipoPesquisaModel();
        $orgSesion         = (int) session('organizacion_id');

        $pesquisas = $tipoPesquisaModel->findAll();

        if (in_array($rol, [1, 2])) {
            $organizaciones = $orgModel->findAll();
            $soloLectura    = false;
        } else {
            $organizaciones = $orgModel->where('id_organizacion', $orgSesion)->findAll();
            $soloLectura    = true;
        }

        return view('jornadas/editar', [
            'jornada'                => $jornada,
            'organizaciones'         => $organizaciones,
            'soloLectura'            => $soloLectura,
            'orgSesion'              => $orgSesion,
            'pesquisas'              => $pesquisas,
            'pesquisasSeleccionadas' => $pesquisasSeleccionadas,
        ]);
    }

    // ════════════════════════════════════════════════
    // ACTUALIZAR — Procesar edición (POST)
    // ════════════════════════════════════════════════
    public function actualizar()
    {
        $rol = (int) session('id_rol');
        if (!in_array($rol, [1, 2, 3])) {
            return redirect()->to('/jornadas')->with('error', 'No tienes permiso para editar jornadas.');
        }

        $id_jornada = $this->request->getPost('id_jornada');

        $rules = [
            'id_jornada'      => 'required|integer',
            'nombre_jornada'  => 'required|min_length[3]|max_length[45]',
            'fecha_inicio'    => 'required|valid_date[Y-m-d]',
            'organizacion_id' => 'required|integer',
            'status_jor'      => 'required|in_list[1,2]',
            'pesquisas'       => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                             ->withInput()
                             ->with('errors', $this->validator->getErrors());
        }

        $jornada = $this->jornadaModel->getJornadaConDireccion($id_jornada);

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada.');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // ═══ 1) ACTUALIZAR DIRECCIÓN ═══
        // FIX: Limpiar "Estado " + guardar municipio/parroquia/detalle
        $estadoRaw = $this->request->getPost('estado') ?? '';
        $estado    = preg_replace('/^Estado\s+/i', '', trim($estadoRaw));

        $direccionModel = new \App\Models\DireccionModel();

        $dirData = [
            'pais'        => $this->request->getPost('pais') ?: 'Venezuela',
            'estado'      => $estado,
            'municipio'   => $this->request->getPost('municipio'),
            'parroquia'   => $this->request->getPost('parroquia'),
            'ciudad'      => $this->request->getPost('ciudad'),
            'detalle'     => $this->request->getPost('detalle'),
            'coordenadas' => $this->request->getPost('coords'),
        ];

        if (!empty($jornada['id_direccion'])) {
            $direccionModel->update($jornada['id_direccion'], $dirData);
        } else {
            $nuevaDirId = $direccionModel->insert($dirData, true);
            if (!empty($jornada['institucion_id'])) {
                $db->table('instituciones')
                   ->where('id_institucion', $jornada['institucion_id'])
                   ->update(['direccion_id' => $nuevaDirId]);
            }
        }

        // ═══ 2) ACTUALIZAR INSTITUCIÓN ═══
        if (!empty($jornada['institucion_id'])) {
            $nombre_institucion = trim($this->request->getPost('nombre_institucion') ?? '');
            $db->table('instituciones')
               ->where('id_institucion', $jornada['institucion_id'])
               ->update([
                   'nombre_institucion' => $nombre_institucion,
                   'tipo'               => $this->request->getPost('tipo_jornada'),
               ]);
        }

        // ═══ 3) ACTUALIZAR JORNADA ═══
        $this->jornadaModel->update($id_jornada, [
            'nombre_jornada'  => $this->request->getPost('nombre_jornada'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'organizacion_id' => $this->request->getPost('organizacion_id'),
            'status_jor'      => $this->request->getPost('status_jor'),
            'modificado_en'   => date('Y-m-d H:i:s'),
            'modificado_por'  => session('id_usuario'),
        ]);

        // ═══ 4) ACTUALIZAR PESQUISAS ═══
        $tpaModel = new \App\Models\TipoPesquisaActividadModel();
        $tpaModel->where('id_jornada', $id_jornada)->delete();

        $pesquisasSeleccionadas = $this->request->getPost('pesquisas') ?? [];
        foreach ($pesquisasSeleccionadas as $idPesquisa) {
            $tpaModel->insert([
                'idtipo_pesquisa' => $idPesquisa,
                'id_jornada'      => $id_jornada,
                'status_pesq_act' => 1,
            ]);
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Error al actualizar la jornada.');
        }

        return redirect()->to('/jornadas')->with('success', 'Jornada actualizada correctamente.');
    }

    // ════════════════════════════════════════════════
    // AJAX: Buscar instituciones para select con sugerencias
    // ════════════════════════════════════════════════
    public function buscarInstituciones()
    {
        $term = trim($this->request->getGet('q') ?? '');

        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $institucionModel = new InstitucionesModel();

        $resultados = $institucionModel
            ->select('id_institucion, nombre_institucion')
            ->like('nombre_institucion', $term)
            ->orderBy('nombre_institucion', 'ASC')
            ->limit(10)
            ->findAll();

        return $this->response->setJSON($resultados);
    }
}
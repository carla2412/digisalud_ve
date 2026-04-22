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
    // INDEX — Listado de jornadas
    // ================================
    public function index()
    {
        $orgSesion = session('organizacion_id');

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
            ->groupBy('jornadas.id_jornada')
            ->orderBy('jornadas.fecha_inicio', 'DESC');

        if ($orgSesion != 2) {
            $builder->where('jornadas.organizacion_id', $orgSesion);
        }

        $jornadas       = $builder->findAll();
        $instituciones  = (new InstitucionesModel())->findAll();

        return view('jornadas/index', [
            'jornadas'      => $jornadas,
            'instituciones' => $instituciones
        ]);
    }

    // ================================
    // LISTADO PARA DATATABLE (AJAX)
    // ================================
    public function listar()
    {
        $orgSesion = session('organizacion_id');

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

        if ($orgSesion != 2) {
            $builder->where('jornadas.organizacion_id', $orgSesion);
        }

        $jornadas = $builder->findAll();
        return $this->response->setJSON(['data' => $jornadas]);
    }

    // ================================
    // CAMBIAR STATUS (0/1/2)
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

        $rol       = session('id_rol');
        $orgSesion = session('organizacion_id');

        $pesquisas = $tipoPesquisaModel->findAll();

        if ($rol == 1 || $rol == 2) {
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
            'pesquisas'      => $pesquisas
        ]);
    }

    // ================================
    // GUARDAR — Procesar creación
    // ================================
    public function guardar()
    {
        $db = \Config\Database::connect();

        $direccionModel     = new \App\Models\DireccionModel();
        $institucionModel   = new \App\Models\InstitucionesModel();
        $jornadaModel       = new \App\Models\JornadaModel();
        $instActModel       = new \App\Models\InstitucionActividadModel();
        $tipoActividadModel = new \App\Models\TipoPesquisaActividadModel();

        // ========= 1) GUARDAR DIRECCIÓN =========
        $direccionData = [
            'pais'        => $this->request->getPost('pais'),
            'estado'      => $this->request->getPost('estado'),
            'ciudad'      => $this->request->getPost('ciudad'),
            'municipio'   => null,
            'parroquia'   => null,
            'detalle'     => $this->request->getPost('localidad'),
            'coordenadas' => $this->request->getPost('coords'),
        ];
        $direccion_id = $direccionModel->insert($direccionData, true);

        // ========= 2) GUARDAR INSTITUCIÓN =========
        $institucionModel = new \App\Models\InstitucionesModel();
        $lastId    = $institucionModel->selectMax('id_institucion')->first()['id_institucion'] ?? 0;
        $newInstId = $lastId + 1;

        $institucionData = [
            'id_institucion'     => $newInstId,
            'nombre_institucion' => $this->request->getPost('localidad'),
            'tipo'               => $this->request->getPost('tipo_jornada'),
            'direccion_id'       => $direccion_id
        ];
        $institucionModel->insert($institucionData);

        // ========= 3) GUARDAR JORNADA =========
        $jornadaData = [
            'nombre_jornada'  => $this->request->getPost('nombre_jornada'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'organizacion_id' => $this->request->getPost('organizacion_id'),
            'institucion_id'  => $newInstId,
            'status_jor'      => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => session('id_usuario')
        ];
        $jornadaModel->insert($jornadaData);
        $id_jornada = $jornadaModel->insertID();

        // ========= 4) RELACIONAR INSTITUCIÓN ↔ JORNADA =========
        $instActModel->insert([
            'id_institucion' => $newInstId,
            'id_jornada'     => $id_jornada,
            'id_centro'      => null,
            'status_act'     => 1
        ]);

        // ========= 5) GUARDAR PESQUISAS =========
        $pesquisasSeleccionadas     = $this->request->getPost('pesquisas');
        $tipoPesquisaActividadModel = new \App\Models\TipoPesquisaActividadModel();

        foreach ($pesquisasSeleccionadas as $idPesquisa) {
            $tipoPesquisaActividadModel->insert([
                'idtipo_pesquisa' => $idPesquisa,
                'id_jornada'      => $id_jornada,
                'status_pesq_act' => 1
            ]);
        }

        return redirect()->back()->with('success', true);
    }

    // ════════════════════════════════════════════════
    // EDITAR — Mostrar formulario de edición (GET)
    // ════════════════════════════════════════════════
    public function editar($id_jornada)
    {
        // ── Validar rol: solo 1 (Super Admin), 2 (Admin), 3 (Coordinador) ──
        $rol = session('id_rol');
        if (!in_array($rol, [1, 2, 3])) {
            return redirect()->to('/jornadas')->with('error', 'No tienes permiso para editar jornadas.');
        }

        // ── Obtener jornada CON datos de institución y dirección ──
        $jornada = $this->jornadaModel->getJornadaConDireccion($id_jornada);

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada.');
        }

        // ── Pesquisas vinculadas (array de IDs) ──
        $pesquisasSeleccionadas = $this->jornadaModel->getPesquisasPorJornada($id_jornada);

        // ── Catálogos ──
        $orgModel          = new \App\Models\OrganizacionModel();
        $tipoPesquisaModel = new \App\Models\TipoPesquisaModel();
        $orgSesion         = session('organizacion_id');

        $pesquisas = $tipoPesquisaModel->findAll();

        if ($rol == 1 || $rol == 2) {
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
        // ── Validar rol ──
        $rol = session('id_rol');
        if (!in_array($rol, [1, 2, 3])) {
            return redirect()->to('/jornadas')->with('error', 'No tienes permiso para editar jornadas.');
        }

        $id_jornada = $this->request->getPost('id_jornada');

        // ── Validación del formulario ──
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

        // ── Verificar que la jornada existe ──
        $jornada = $this->jornadaModel->getJornadaConDireccion($id_jornada);

        if (!$jornada) {
            return redirect()->to('/jornadas')->with('error', 'Jornada no encontrada.');
        }

        // ── Iniciar transacción ──
        $db = \Config\Database::connect();
        $db->transStart();

        // ═══ 1) ACTUALIZAR DIRECCIÓN ═══
        $direccionModel = new \App\Models\DireccionModel();

        if (!empty($jornada['id_direccion'])) {
            // Actualizar dirección existente
            $direccionModel->update($jornada['id_direccion'], [
                'pais'        => $this->request->getPost('pais'),
                'estado'      => $this->request->getPost('estado'),
                'ciudad'      => $this->request->getPost('ciudad'),
                'coordenadas' => $this->request->getPost('coords'),
                'detalle'     => $this->request->getPost('localidad'),
            ]);
        } else {
            // Crear dirección nueva si no tenía
            $nuevaDirId = $direccionModel->insert([
                'pais'        => $this->request->getPost('pais'),
                'estado'      => $this->request->getPost('estado'),
                'ciudad'      => $this->request->getPost('ciudad'),
                'municipio'   => null,
                'parroquia'   => null,
                'detalle'     => $this->request->getPost('localidad'),
                'coordenadas' => $this->request->getPost('coords'),
            ], true);

            // Vincular a la institución
            if (!empty($jornada['institucion_id'])) {
                $db->table('instituciones')
                   ->where('id_institucion', $jornada['institucion_id'])
                   ->update(['direccion_id' => $nuevaDirId]);
            }
        }

        // ═══ 2) ACTUALIZAR INSTITUCIÓN (localidad + tipo público/privado) ═══
        if (!empty($jornada['institucion_id'])) {
            $db->table('instituciones')
               ->where('id_institucion', $jornada['institucion_id'])
               ->update([
                   'nombre_institucion' => $this->request->getPost('localidad'),
                   'tipo'               => $this->request->getPost('tipo_jornada'),
               ]);
        }

        // ═══ 3) ACTUALIZAR JORNADA (nombre, fecha, org, status, auditoría) ═══
        $this->jornadaModel->update($id_jornada, [
            'nombre_jornada'  => $this->request->getPost('nombre_jornada'),
            'fecha_inicio'    => $this->request->getPost('fecha_inicio'),
            'organizacion_id' => $this->request->getPost('organizacion_id'),
            'status_jor'      => $this->request->getPost('status_jor'),
            'modificado_en'   => date('Y-m-d H:i:s'),
            'modificado_por'  => session('id_usuario'),
        ]);

        // ═══ 4) SINCRONIZAR PESQUISAS: limpiar y reponer ═══
        $db->table('tipo_pesquisa_actividad')
           ->where('id_jornada', $id_jornada)
           ->delete();

        $pesquisasNuevas = $this->request->getPost('pesquisas');
        $batch = [];
        foreach ($pesquisasNuevas as $idPesquisa) {
            $batch[] = [
                'idtipo_pesquisa' => $idPesquisa,
                'id_jornada'      => $id_jornada,
                'id_centro'       => null,
                'status_pesq_act' => 1,
            ];
        }

        if (!empty($batch)) {
            $db->table('tipo_pesquisa_actividad')->insertBatch($batch);
        }

        // ═══ 5) Completar transacción ═══
        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()
                             ->withInput()
                             ->with('error', 'Error al actualizar la jornada. Intente nuevamente.');
        }

        return redirect()->to('/jornadas')->with('success', 'Jornada actualizada correctamente.');
    }
}
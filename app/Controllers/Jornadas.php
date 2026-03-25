<?php
namespace App\Controllers;

use App\Models\JornadaModel;
use App\Models\OrganizacionModel;
use App\Models\InstitucionesModel;

class Jornadas extends BaseController
{
    protected $jornadaModel;

    public function __construct()
    {
        helper(['url','form']);
        $this->jornadaModel = new JornadaModel();
    }

public function index()
{
    $orgSesion = session('organizacion_id');

   $builder = $this->jornadaModel
    ->select("jornadas.*, 
              organizaciones.nombre_org,
              instituciones.nombre_institucion,dir.ciudad,
              GROUP_CONCAT(tpa.idtipo_pesquisa SEPARATOR ',') AS pesquisas")
    ->join('organizacion AS organizaciones', 'organizaciones.id_organizacion = jornadas.organizacion_id', 'left')
    ->join('instituciones', 'instituciones.id_institucion = jornadas.institucion_id', 'left')
    ->join('tipo_pesquisa_actividad AS tpa', 'tpa.id_jornada = jornadas.id_jornada', 'left')
    ->join('tipo_pesquisa AS tp', 'tp.idtipo_pesquisa = tpa.idtipo_pesquisa', 'left')
    ->join('direcciones AS dir', 'dir.id_direccion = instituciones.direccion_id', 'left')
    ->where('jornadas.status_jor !=', 0)
    ->groupBy('jornadas.id_jornada');




    // SOLO filtrar cuando NO ES DIGISALUD
    if ($orgSesion != 2) {
        $builder->where('jornadas.organizacion_id', $orgSesion);
    }

    $jornadas = $builder->findAll();

    $instituciones = (new InstitucionesModel())->findAll();

    return view('jornadas/index', [
        'jornadas'      => $jornadas,
        'instituciones' => $instituciones
    ]);
}

    // ================================
    // LISTADO PARA DATATABLE
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



            // Solo filtrar por organizacion cuando NO sea Digisalud
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
            $id  = $this->request->getPost('id');
            $status = $this->request->getPost('status');

            $this->jornadaModel->update($id, [
                'status_jor' => $status,
                'modificado_en' => date('Y-m-d H:i:s'),
                'modificado_por' => session('id_usuario'),
            ]);

            return $this->response->setJSON(['success' => true]);
        }

   public function crear()
{
    $orgModel = new \App\Models\OrganizacionModel();
    $tipoPesquisaModel = new \App\Models\TipoPesquisaModel();

    $rol = session('id_rol');
    $orgSesion = session('organizacion_id');

    // Obtener tipos de pesquisa
    $pesquisas = $tipoPesquisaModel->findAll();

    if ($rol == 1 || $rol == 2) {
        // Admin y superadmin → ven todas las organizaciones
        $organizaciones = $orgModel->findAll();
        $soloLectura = false;
    } else {
        // Otros roles → solo su organización
        $organizaciones = $orgModel->where('id_organizacion', $orgSesion)->findAll();
        $soloLectura = true;
    }

    return view('jornadas/crear', [
        'organizaciones' => $organizaciones,
        'soloLectura'    => $soloLectura,
        'orgSesion'      => $orgSesion,
        'pesquisas'      => $pesquisas   // 👈 NECESARIO
    ]);
}




        public function guardar()
        {
            $db = \Config\Database::connect();
         
            // Instanciar modelos
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

            $direccion_id = $direccionModel->insert($direccionData, true); // true = return insert ID


            // ========= 2) GUARDAR INSTITUCIÓN =========
            $institucionModel = new \App\Models\InstitucionesModel();

            // OJO: tu tabla NO usa AUTO_INCREMENT → generamos ID manual
            $lastId = $institucionModel->selectMax('id_institucion')->first()['id_institucion'] ?? 0;
            $newInstId = $lastId + 1;

            $institucionData = [
                'id_institucion'    => $newInstId,
                'nombre_institucion'=> $this->request->getPost('localidad'), // nombre desde modal
                'tipo'              => $this->request->getPost('tipo_jornada'), // pública / privada
                'direccion_id'      => $direccion_id
            ];

            $institucionModel->insert($institucionData);


             // ========= 3) DEFINIR ID DE JORNADA Y GUARDAR =========
       
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

            // ✔ ID correcto autogenerado por MySQL
            $id_jornada = $jornadaModel->insertID();


            // ========= 4) RELACIONAR INSTITUCIÓN ↔ JORNADA =========
            $instActModel->insert([
                'id_institucion' => $newInstId,
                'id_jornada'     => $id_jornada, // 👈 YA DEFINIDO
                'id_centro'      => null,
                'status_act'     => 1
            ]);


            // ========= 5) GUARDAR PESQUISAS =========
            $pesquisasSeleccionadas = $this->request->getPost('pesquisas'); // array de IDs

            $tipoPesquisaActividadModel = new \App\Models\TipoPesquisaActividadModel();

            foreach ($pesquisasSeleccionadas as $idPesquisa) {
                $tipoPesquisaActividadModel->insert([
                    'idtipo_pesquisa' => $idPesquisa,
                    'id_jornada'      => $id_jornada,
                    'status_pesq_act' => 1
                ]);
            }



            // ========= 6) LISTO → MENSAJE SWEET ALERT =========
            return redirect()->back()->with('success', true);
        }


}

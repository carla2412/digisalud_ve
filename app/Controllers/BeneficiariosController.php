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

class BeneficiariosController extends BaseController
{

    // ════════════════════════════════════════
    // VISTA: Búsqueda de beneficiarios
    // ════════════════════════════════════════
    public function buscar($jornada_id)
    {
        return view('beneficiarios/buscar', [
            'jornada_id' => $jornada_id
        ]);
    }

    // ════════════════════════════════════════
    // AJAX: Buscar beneficiarios
    // ════════════════════════════════════════
    public function buscarAjax()
    {
        $model = new BeneficiariosModel();
        $term  = $this->request->getGet('q');

        if (strlen($term) < 2) {
            return $this->response->setJSON([]);
        }

        $data = $model
            ->select('id_beneficiario, id_digisalud, nombres, apellidos, fecha_nacimiento, sexo, pais_nacimiento')
            ->groupStart()
                ->like('nombres', $term)
                ->orLike('apellidos', $term)
                ->orLike('id_digisalud', $term)
            ->groupEnd()
            ->limit(15)
            ->findAll();

        foreach ($data as &$b) {
            $b['edad'] = $this->calcularEdadTexto($b['fecha_nacimiento']);

            // Buscar parentesco
            $fam = (new FamiliaresModel())
                ->select('familiares.relacion, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos')
                ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
                ->where('familiares.beneficiario_id', $b['id_beneficiario'])
                ->first();

            $b['parentesco'] = '';
            if ($fam && !empty($fam['rep_nombres'])) {
                $b['parentesco'] = ($fam['relacion'] ?? '') . ': ' . $fam['rep_nombres'] . ' ' . $fam['rep_apellidos'];
            }
        }

        return $this->response->setJSON($data);
    }

    // ════════════════════════════════════════
    // AJAX: Buscar antecedentes desde tabla BD
    // ════════════════════════════════════════
    public function buscarAntecedentesAjax()
    {
        $db   = \Config\Database::connect();
        $term = $this->request->getGet('q');
        $tipo = $this->request->getGet('tipo');

        $builder = $db->table('antecedentes')
            ->select('id_antecedente, nombre, tipo, descripcion');

        if (!empty($term)) {
            $builder->groupStart()
                ->like('descripcion', $term)
                ->orLike('nombre', $term)
            ->groupEnd();
        }

        if (!empty($tipo)) {
            $builder->where('tipo', $tipo);
        }

        $data = $builder->orderBy('tipo')->orderBy('descripcion')->get()->getResultArray();
        return $this->response->setJSON($data);
    }

    // ════════════════════════════════════════
    // VISTA: Formulario de creación
    // ════════════════════════════════════════
    public function create($jornada_id)
    {
        return view('beneficiarios/create', [
            'jornada_id' => $jornada_id
        ]);
    }

    // ════════════════════════════════════════
    // GUARDAR: Nuevo beneficiario + jornada
    // ════════════════════════════════════════
    public function store($jornada_id)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();
        $jorModel   = new JornadaBeneficiariosModel();

        $post = $this->request->getPost();

        // ══════════════════════════════════════════════
        // 1) DIRECCIÓN — guardar PRIMERO para obtener ID
        // ══════════════════════════════════════════════
        $direccion_id = null;
        if (!empty($post['direccion_activa'])) {
            $dirModel->insert([
                'pais'      => $post['pais'] ?? 'Venezuela',
                'estado'    => $post['estado'] ?? null,
                'municipio' => $post['municipio'] ?? null,
                'parroquia' => $post['parroquia'] ?? null,
                'ciudad'    => $post['ciudad'] ?? null,
            ]);
            $direccion_id = $dirModel->getInsertID();
        }

        // ══════════════════════════════════════════════
        // 2) BENEFICIARIO — campos EXPLÍCITOS, nada de getPost() crudo
        //    Esto evita que 'pais','estado','ciudad' etc. se filtren
        //    al modelo y corrompan campos como modificado_por
        // ══════════════════════════════════════════════
        $benefData = [
            'id_digisalud'     => $this->generarID($post),
            'nombres'          => $post['nombres'] ?? '',
            'apellidos'        => $post['apellidos'] ?? '',
            'fecha_nacimiento' => $post['fecha_nacimiento'] ?? null,
            'sexo'             => $post['sexo'] ?? 'M',
            'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
            'telefono'         => $post['telefono'] ?? null,
            'correo'           => $post['correo'] ?? null,
            'direccion_id'     => $direccion_id,       // ← ASOCIAR dirección
            'creado_en'        => date('Y-m-d H:i:s'), // ← FECHA creación
            'creado_por'       => session('id_usuario') ?? null, // ← USUARIO sesión
        ];

        $id_beneficiario = $benefModel->insert($benefData);

        // ══════════════════════════════════════════════
        // 3) ESCOLARIDAD (opcional)
        // ══════════════════════════════════════════════
        if (!empty($post['escolaridad_activa'])) {
            $escModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'nombre_escuela'  => $post['nombre_escuela'] ?? null,
                'grado'           => $post['grado'] ?? null,
                'seccion'         => $post['seccion'] ?? null,
                'turno'           => $post['turno'] ?? null,
            ]);
        }

        // ══════════════════════════════════════════════
        // 4) FAMILIAR / REPRESENTANTE (opcional)
        // ══════════════════════════════════════════════
        if (!empty($post['familiar_activo'])) {
            $repId = $post['representante_id'] ?? null;

            // Si no existe representante seleccionado, crear uno nuevo
            if (empty($repId) && !empty($post['rep_nombres'])) {
                $repData = [
                    'id_digisalud'     => $this->generarID([
                        'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
                        'sexo'             => $post['rep_sexo'] ?? 'M',
                        'nombres'          => $post['rep_nombres'] ?? '',
                        'apellidos'        => $post['rep_apellidos'] ?? '',
                        'fecha_nacimiento' => $post['rep_fecha_nacimiento'] ?? date('Y-m-d'),
                    ]),
                    'nombres'          => $post['rep_nombres'],
                    'apellidos'        => $post['rep_apellidos'] ?? '',
                    'fecha_nacimiento' => $post['rep_fecha_nacimiento'] ?? date('Y-m-d'),
                    'sexo'             => $post['rep_sexo'] ?? 'M',
                    'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
                    'creado_en'        => date('Y-m-d H:i:s'),
                    'creado_por'       => session('id_usuario') ?? null,
                ];
                $repId = $benefModel->insert($repData);

                // Si marcó "evaluar representante", también asociar a la jornada
                if (!empty($post['evaluar_representante'])) {
                    $jorModel->insert([
                        'id_beneficiario' => $repId,
                        'jornada_id'      => $jornada_id,
                        'status_bc'       => 1,
                        'creado_en'       => date('Y-m-d H:i:s'),
                        'creado_por'      => session('id_usuario') ?? 1,
                    ]);
                }
            }

            // También evaluar representante existente si marcó checkbox
            if (!empty($repId) && !empty($post['evaluar_representante']) && !empty($post['representante_id'])) {
                // Verificar que no esté ya asociado
                $yaAsociado = $jorModel->where('id_beneficiario', $repId)
                                       ->where('jornada_id', $jornada_id)->first();
                if (!$yaAsociado) {
                    $jorModel->insert([
                        'id_beneficiario' => $repId,
                        'jornada_id'      => $jornada_id,
                        'status_bc'       => 1,
                        'creado_en'       => date('Y-m-d H:i:s'),
                        'creado_por'      => session('id_usuario') ?? 1,
                    ]);
                }
            }

            if ($repId) {
                $famModel->insert([
                    'beneficiario_id'               => $id_beneficiario,
                    'beneficiario_id_representante'  => $repId,
                    'relacion'                       => $post['relacion'] ?? null,
                    'telefono'                       => $post['telefono_representante'] ?? null,
                ]);
            }
        }

        // ══════════════════════════════════════════════
        // 5) ANTECEDENTES (opcional)
        // ══════════════════════════════════════════════
        $antecedentes = $post['antecedentes'] ?? [];
        if (!empty($antecedentes) && is_array($antecedentes)) {
            foreach ($antecedentes as $id_antecedente) {
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $id_antecedente,
                    'jornada_id'      => $jornada_id,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => session('id_usuario') ?? 1,
                ]);
            }
        }

        $obs = $post['observacion_antecedentes'] ?? '';
        if (!empty($obs)) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 15, // "OTRO"
                'jornada_id'      => $jornada_id,
                'observacion'     => $obs,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => session('id_usuario') ?? 1,
            ]);
        }

        // ══════════════════════════════════════════════
        // 6) ASOCIAR BENEFICIARIO A LA JORNADA
        // ══════════════════════════════════════════════
        $jorModel->insert([
            'id_beneficiario' => $id_beneficiario,
            'jornada_id'      => $jornada_id,
            'status_bc'       => 1,
            'creado_en'       => date('Y-m-d H:i:s'),
            'creado_por'      => session('id_usuario') ?? 1,
        ]);

        return redirect()->to("/jornadas/$jornada_id/beneficiarios")
                         ->with('success', 'Beneficiario registrado y asociado correctamente');
    }

    // ════════════════════════════════════════
    // GENERAR ID DIGISALUD
    // ════════════════════════════════════════
    private function generarID($data)
    {
        $pais  = strtoupper(substr($data['pais_nacimiento'] ?? 'VE', 0, 2));
        $sexo  = strtoupper($data['sexo'] ?? 'M');

        $nombres   = explode(" ", trim($data['nombres'] ?? ''));
        $apellidos = explode(" ", trim($data['apellidos'] ?? ''));

        $p1 = strtoupper(substr($nombres[0] ?? '', 0, 3));
        $p2 = isset($nombres[1]) ? strtoupper(substr($nombres[1], 0, 1)) : "";
        $a1 = strtoupper(substr($apellidos[0] ?? '', 0, 3));
        $a2 = isset($apellidos[1]) ? strtoupper(substr($apellidos[1], 0, 1)) : "";

        $fecha = str_replace("-", "", $data['fecha_nacimiento'] ?? '');

        return $pais . $sexo . $p1 . $p2 . $a1 . $a2 . $fecha;
    }

    private function calcularEdadTexto($fecha_nacimiento)
    {
        $nac  = new \DateTime($fecha_nacimiento);
        $diff = (new \DateTime())->diff($nac);
        if ($diff->y > 0) {
            return $diff->y . ' año' . ($diff->y > 1 ? 's' : '') .
                   ' con ' . $diff->m . ' mes(es) y ' . $diff->d . ' dias';
        }
        return $diff->m . ' mes(es) y ' . $diff->d . ' días';
    }
}
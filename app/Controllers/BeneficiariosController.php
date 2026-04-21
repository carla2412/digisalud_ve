<?php
// ========================================================
// ARCHIVO: app/Controllers/BeneficiariosController.php
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

    public function buscar($jornada_id)
    {
        return view('beneficiarios/buscar', ['jornada_id' => $jornada_id]);
    }

    public function buscarAjax()
{
    $model = new BeneficiariosModel();
    $term  = $this->request->getGet('q');
    
    if (strlen($term) < 2) return $this->response->setJSON([]);

    // 1. Buscamos los beneficiarios
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
        
        // 2. Corregimos la búsqueda de la relación
        // Buscamos si este beneficiario tiene un representante asignado
        $fam = $famModel
            ->select('familiares.relacion, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos')
            ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
            ->where('familiares.beneficiario_id', $b['id_beneficiario'])
            ->first();

        // 3. Normalizamos la respuesta para el Dropdown
        if ($fam) {
            // Guardamos el texto plano de la relación
            $b['relacion_texto'] = $fam['relacion']; 
            $b['parentesco'] = $fam['relacion'] . ': ' . $fam['rep_nombres'] . ' ' . $fam['rep_apellidos'];
        } else {
            $b['relacion_texto'] = 'Ninguna';
            $b['parentesco'] = 'Sin representante';
        }
        
        // IMPORTANTE: Para que muchos plugins de Dropdown (como Select2) funcionen, 
        // necesitan un campo llamado "id" y "text".
        $b['id'] = $b['id_beneficiario'];
        $b['text'] = $b['nombres'] . ' ' . $b['apellidos'] . ' (' . $b['id_digisalud'] . ')';
    }

    return $this->response->setJSON($data);
}

    public function buscarAntecedentesAjax()
    {
        $db   = \Config\Database::connect();
        $term = $this->request->getGet('q');
        $tipo = $this->request->getGet('tipo');

        $builder = $db->table('antecedentes')->select('id_antecedente, nombre, tipo, descripcion');
        if (!empty($term)) {
            $builder->groupStart()->like('descripcion', $term)->orLike('nombre', $term)->groupEnd();
        }
        if (!empty($tipo)) $builder->where('tipo', $tipo);

        return $this->response->setJSON(
            $builder->orderBy('tipo')->orderBy('descripcion')->get()->getResultArray()
        );
    }

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
        $usuarioId = session('id_usuario') ?? null;

        // ══ 1) DIRECCIÓN ══
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

        // ══ 2) BENEFICIARIO (campos explícitos) ══
        $benefData = [
            'id_digisalud'     => $this->generarID($post),
            'nombres'          => $post['nombres'] ?? '',
            'apellidos'        => $post['apellidos'] ?? '',
            'fecha_nacimiento' => $post['fecha_nacimiento'] ?? null,
            'sexo'             => $post['sexo'] ?? 'M',
            'pais_nacimiento'  => $post['pais_nacimiento'] ?? 'Venezuela',
            'telefono'         => $post['telefono'] ?? null,
            'correo'           => $post['correo'] ?? null,
            'direccion_id'     => $direccion_id,
            'creado_en'        => date('Y-m-d H:i:s'),
            'creado_por'       => $usuarioId,
        ];
        $id_beneficiario = $benefModel->insert($benefData);

        // ══ 3) ESCOLARIDAD (con auditoría) ══
        if (!empty($post['escolaridad_activa'])) {
            $escModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'nombre_escuela'  => $post['nombre_escuela'] ?? null,
                'grado'           => $post['grado'] ?? null,
                'seccion'         => $post['seccion'] ?? null,
                'turno'           => $post['turno'] ?? null,
                'status_esc'      => 1, // activo
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId,
            ]);
        }

        // ══ 4) FAMILIAR / REPRESENTANTE ══
        if (!empty($post['familiar_activo'])) {
            $repId = $post['representante_id'] ?? null;

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
                    'creado_por'       => $usuarioId,
                ];
                $repId = $benefModel->insert($repData);
            }

            // Evaluar representante en jornada si checkbox marcado
            if (!empty($repId) && !empty($post['evaluar_representante'])) {
                $yaAsociado = $jorModel->where('id_beneficiario', $repId)
                                       ->where('jornada_id', $jornada_id)->first();
                if (!$yaAsociado) {
                    $jorModel->insert([
                        'id_beneficiario' => $repId,
                        'jornada_id'      => $jornada_id,
                        'status_bc'       => 1,
                        'creado_en'       => date('Y-m-d H:i:s'),
                        'creado_por'      => $usuarioId ?? 1,
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

        // ══ 5) ANTECEDENTES ══
        $antecedentes = $post['antecedentes'] ?? [];
        if (!empty($antecedentes) && is_array($antecedentes)) {
            foreach ($antecedentes as $id_antecedente) {
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $id_antecedente,
                    'jornada_id'      => $jornada_id,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        // Checkbox "Usa lentes" → id_antecedente 38 (USA_LENTES)
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
    // EDITAR: Cargar datos del beneficiario
    // ════════════════════════════════════════
    public function edit($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();

        // Beneficiario
        $beneficiario = $benefModel->find($id_beneficiario);
        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        // Dirección (si tiene)
        $direccion = null;
        if (!empty($beneficiario['direccion_id'])) {
            $direccion = $dirModel->find($beneficiario['direccion_id']);
        }

        // Escolaridad activa
        $escolaridad = $escModel->getActiva($id_beneficiario);

        // Familiar/representante
        $familiar = $famModel
            ->select('familiares.*, rep.nombres AS rep_nombres, rep.apellidos AS rep_apellidos, rep.id_digisalud AS rep_id_digisalud')
            ->join('beneficiarios AS rep', 'rep.id_beneficiario = familiares.beneficiario_id_representante', 'left')
            ->where('familiares.beneficiario_id', $id_beneficiario)
            ->first();

        // Antecedentes ya asociados
        $antecedentes = $antModel
            ->select('antecedentes_beneficiarios.*, antecedentes.descripcion, antecedentes.tipo, antecedentes.nombre')
            ->join('antecedentes', 'antecedentes.id_antecedente = antecedentes_beneficiarios.id_antecedente')
            ->where('antecedentes_beneficiarios.id_beneficiario', $id_beneficiario)
            ->findAll();

        // Separar antecedentes por tipo
        $antClinico = [];
        $antSocio   = [];
        $usaLentes  = false;
        $observacion = '';

        foreach ($antecedentes as $a) {
            if ($a['id_antecedente'] == 38) {
                $usaLentes = true;
            } elseif ($a['id_antecedente'] == 15 && !empty($a['observacion'])) {
                $observacion = $a['observacion'];
            } elseif ($a['tipo'] === 'Antecedentes Clínicos') {
                $antClinico[] = $a;
            } elseif ($a['tipo'] === 'Datos Socioeconómicos') {
                $antSocio[] = $a;
            }
        }

        return view('beneficiarios/edit', [
            'beneficiario' => $beneficiario,
            'direccion'    => $direccion,
            'escolaridad'  => $escolaridad,
            'familiar'     => $familiar,
            'antClinico'   => $antClinico,
            'antSocio'     => $antSocio,
            'usaLentes'    => $usaLentes,
            'observacion'  => $observacion,
        ]);
    }

    // ════════════════════════════════════════
    // ACTUALIZAR: Guardar cambios del beneficiario
    // ════════════════════════════════════════
    public function update($id_beneficiario)
    {
        $benefModel = new BeneficiariosModel();
        $dirModel   = new DireccionModel();
        $escModel   = new EscolaridadModel();
        $famModel   = new FamiliaresModel();
        $antModel   = new AntecedentesBeneficiariosModel();

        $post      = $this->request->getPost();
        $usuarioId = session('id_usuario') ?? null;

        $beneficiario = $benefModel->find($id_beneficiario);
        if (!$beneficiario) {
            return redirect()->back()->with('error', 'Beneficiario no encontrado');
        }

        // ══ 1) ACTUALIZAR DIRECCIÓN ══
        if (!empty($post['direccion_activa'])) {
            $dirData = [
                'pais'      => $post['pais'] ?? 'Venezuela',
                'estado'    => $post['estado'] ?? null,
                'municipio' => $post['municipio'] ?? null,
                'parroquia' => $post['parroquia'] ?? null,
                'ciudad'    => $post['ciudad'] ?? null,
            ];

            if (!empty($beneficiario['direccion_id'])) {
                // Actualizar dirección existente
                $dirModel->update($beneficiario['direccion_id'], $dirData);
            } else {
                // Crear nueva dirección y asociar
                $dirModel->insert($dirData);
                $newDirId = $dirModel->getInsertID();
                $benefModel->update($id_beneficiario, ['direccion_id' => $newDirId]);
            }
        }

        // ══ 2) ACTUALIZAR BENEFICIARIO ══
        $benefData = [
            'nombres'          => $post['nombres'] ?? $beneficiario['nombres'],
            'apellidos'        => $post['apellidos'] ?? $beneficiario['apellidos'],
            'fecha_nacimiento' => $post['fecha_nacimiento'] ?? $beneficiario['fecha_nacimiento'],
            'sexo'             => $post['sexo'] ?? $beneficiario['sexo'],
            'pais_nacimiento'  => $post['pais_nacimiento'] ?? $beneficiario['pais_nacimiento'],
            'telefono'         => $post['telefono'] ?? null,
            'correo'           => $post['correo'] ?? null,
            'modificado_en'    => date('Y-m-d H:i:s'),
            'modificado_por'   => $usuarioId,
        ];

        // Regenerar ID Digisalud si cambiaron datos clave
        $nuevoId = $this->generarID($benefData);
        if ($nuevoId !== $beneficiario['id_digisalud']) {
            $benefData['id_digisalud'] = $nuevoId;
        }

        $benefModel->update($id_beneficiario, $benefData);

        // ══ 3) ESCOLARIDAD ══
        if (!empty($post['escolaridad_activa'])) {
            $escActual = $escModel->getActiva($id_beneficiario);
            $escNueva = [
                'nombre_escuela' => $post['nombre_escuela'] ?? null,
                'grado'          => $post['grado'] ?? null,
                'seccion'        => $post['seccion'] ?? null,
                'turno'          => $post['turno'] ?? null,
            ];

            if ($escActual) {
                // Si cambió el grado → usar cambiarAnio (mantiene historial)
                if (($escActual['grado'] ?? '') !== ($escNueva['grado'] ?? '')) {
                    $escModel->cambiarAnio($id_beneficiario, $escNueva, $usuarioId);
                } else {
                    // Solo actualizar datos sin cambiar grado
                    $escModel->update($escActual['escolaridad_id'], array_merge($escNueva, [
                        'modificado_en'  => date('Y-m-d H:i:s'),
                        'modificado_por' => $usuarioId,
                    ]));
                }
            } else {
                // Crear nueva escolaridad
                $escModel->insert(array_merge($escNueva, [
                    'id_beneficiario' => $id_beneficiario,
                    'status_esc'      => 1,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => $usuarioId,
                ]));
            }
        }

        // ══ 4) FAMILIAR / REPRESENTANTE ══
        if (!empty($post['familiar_activo'])) {
            $repId = $post['representante_id'] ?? null;

            // Si no hay representante seleccionado pero hay datos nuevos
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
                    'creado_por'       => $usuarioId,
                ];
                $repId = $benefModel->insert($repData);
            }

            if ($repId) {
                // Buscar si ya existe relación familiar
                $famExiste = $famModel->where('beneficiario_id', $id_beneficiario)->first();
                $famData = [
                    'beneficiario_id'               => $id_beneficiario,
                    'beneficiario_id_representante'  => $repId,
                    'relacion'                       => $post['relacion'] ?? null,
                    'telefono'                       => $post['telefono_representante'] ?? null,
                ];

                if ($famExiste) {
                    $famModel->update($famExiste['id_familiar'], $famData);
                } else {
                    $famModel->insert($famData);
                }
            }
        }

        // ══ 5) ANTECEDENTES — borrar los anteriores y reinsertar ══
        // Eliminar antecedentes previos de este beneficiario
        $db = \Config\Database::connect();
        $db->table('antecedentes_beneficiarios')
           ->where('id_beneficiario', $id_beneficiario)
           ->delete();

        // Reinsertar los seleccionados
        $antecedentes = $post['antecedentes'] ?? [];
        if (!empty($antecedentes) && is_array($antecedentes)) {
            foreach ($antecedentes as $id_ant) {
                $antModel->insert([
                    'id_beneficiario' => $id_beneficiario,
                    'id_antecedente'  => $id_ant,
                    'creado_en'       => date('Y-m-d H:i:s'),
                    'creado_por'      => $usuarioId ?? 1,
                ]);
            }
        }

        // Usa lentes
        if (!empty($post['usa_lentes'])) {
            $antModel->insert([
                'id_beneficiario' => $id_beneficiario,
                'id_antecedente'  => 38,
                'creado_en'       => date('Y-m-d H:i:s'),
                'creado_por'      => $usuarioId ?? 1,
            ]);
        }

        // Observación
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
    private function generarID($data)
    {
        $pais = strtoupper(substr($data['pais_nacimiento'] ?? 'VE', 0, 2));
        $sexo = strtoupper($data['sexo'] ?? 'M');
        $nombres = explode(" ", trim($data['nombres'] ?? ''));
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
        $diff = (new \DateTime())->diff(new \DateTime($fecha_nacimiento));
        if ($diff->y > 0) return $diff->y . ' año' . ($diff->y > 1 ? 's' : '') . ' con ' . $diff->m . ' mes(es) y ' . $diff->d . ' dias';
        return $diff->m . ' mes(es) y ' . $diff->d . ' días';
    }

    
}
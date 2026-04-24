<?php
namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\OrganizacionModel;
use App\Models\DireccionModel;

class Registro extends BaseController
{
    public function __construct()
    {
        helper('url');  
    }
    public function individual()
    {
        return view('registro/individual');
    }

    public function organizacion()
    {
        
        return view('registro/organizacion');
        
    }

 public function guardarIndividual()
    {
        $usuarios       = new UsuarioModel();
        $direcciones    = new DireccionModel();
        $rolesContexto  = new \App\Models\RolesUsuariosContextoModel(); // 👈 Modelo nuevo

        $db = \Config\Database::connect();
        $db->transStart(); // 🔒 Iniciar transacción segura

        // 1️⃣ Guardar dirección
        $direccionData = [
            'pais'        => $this->request->getPost('pais'),
            'estado'      => $this->request->getPost('estado'),
            'municipio'   => $this->request->getPost('municipio'),
            'parroquia'   => $this->request->getPost('parroquia'),
            'ciudad'      => $this->request->getPost('ciudad'),
            'detalle'     => $this->request->getPost('detalle'),
            'coordenadas' => null
        ];

        if (!$direcciones->insert($direccionData)) {
            dd('❌ Error insertando dirección:', $direcciones->errors());
        }

        $direccionId = $direcciones->getInsertID();

        // 2️⃣ Guardar usuario — AHORA INCLUYE PROFESIÓN
        $data = [
            'nombres'        => $this->request->getPost('nombres'),
            'apellidos'      => $this->request->getPost('apellidos'),
            'genero'         => $this->request->getPost('genero'),
            'email'          => $this->request->getPost('email'),
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
            'username'       => explode('@', $this->request->getPost('email'))[0],
            'password_hash'  => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'telefono'       => $this->request->getPost('telefono'),
            'profesion'      => $this->request->getPost('profesion'),    // 👈 NUEVO
            'direccion_id'   => $direccionId,
            'organizacion_id'=> 1,
            'status_usu'     => '1'
        ];

        if (!$usuarios->insert($data)) {
            dd('❌ Error insertando usuario:', $usuarios->errors());
        }

        $usuarioId = $usuarios->getInsertID();

        // 3️⃣ ASIGNAR ROL POR DEFECTO (id_rol = 6)
        $rolesContexto->insert([
            'usuario_id'      => $usuarioId,
            'id_rol'          => 6,   // 👈 Rol por defecto
            'organizacion_id' => 1,   // 👈 Independiente
            'status_urc'      => '1',
        ]);

        $db->transComplete(); // 🔓 Ejecutar transacción

        if (!$db->transStatus()) {
            dd('❌ Error en la transacción completa');
        }

        return redirect()
            ->back()
            ->with('success', 'Usuario individual registrado correctamente.');
    }


    public function guardarOrganizacion()
{
    helper('url');

    // Modelos
    $organizacionModel = new \App\Models\OrganizacionModel();
    $usuarioModel      = new \App\Models\UsuarioModel();
    $direccionModel    = new \App\Models\DireccionModel();
    $rolesContexto     = new \App\Models\RolesUsuariosContextoModel();

    $db = \Config\Database::connect();
    $db->transStart(); // 🔒 Iniciar transacción segura

    // ====== 1️⃣ Insertar la dirección ======
    $direccionData = [
        'pais'        => $this->request->getPost('pais'),
        'estado'      => $this->request->getPost('estado'),
        'municipio'   => $this->request->getPost('municipio'),
        'parroquia'   => $this->request->getPost('parroquia'),
        'ciudad'      => $this->request->getPost('ciudad'),
        'detalle'     => $this->request->getPost('detalle'),
        'coordenadas' => null,
    ];

    if (!$direccionModel->insert($direccionData)) {
        dd('❌ Error insertando dirección:', $direccionModel->errors());
    }

    $direccionId = $direccionModel->getInsertID();

    // ====== 2️⃣ Insertar la organización ======
    $orgData = [
        'nombre_org'         => $this->request->getPost('nombreOrg'),
        'tipo'               => $this->request->getPost('tipoOrg'),
        'categoria'          => $this->request->getPost('categoriaOrg'),
        'telefono'           => $this->request->getPost('telefono'),
        'email'             => $this->request->getPost('email'),
        'nombre_responsable' => $this->request->getPost('nombres') . ' ' . $this->request->getPost('apellidos'),
        'direccion_id'       => $direccionId,
        'status_org'         => '1'
    ];

    if (!$organizacionModel->insert($orgData)) {
        dd('❌ Error insertando organización:', $organizacionModel->errors());
    }

    $organizacionId = $organizacionModel->getInsertID();

    // ====== 3️⃣ Insertar el usuario vinculado ======
    $userData = [
        'nombres'        => $this->request->getPost('nombres'),
        'apellidos'      => $this->request->getPost('apellidos'),
        'genero'         => $this->request->getPost('genero'),
        'email'          => $this->request->getPost('email'),
        'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
        'username'       => explode('@', $this->request->getPost('email'))[0],
        'password_hash'  => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
        'telefono'       => $this->request->getPost('telefono'),
        'profesion'      => $this->request->getPost('profesion'),
        'direccion_id'   => $direccionId,  
        'organizacion_id'=> $organizacionId,
        'status_usu'     => '1'
    ];

    if (!$usuarioModel->insert($userData)) {
        dd('❌ Error insertando usuario:', $usuarioModel->errors());
    }

    $usuarioId = $usuarioModel->getInsertID();

    // ====== 4️⃣ Asignar rol por defecto (3) al usuario ======
    $rolesContexto->insert([
        'id_usuario'      => $usuarioId,
        'id_rol'          => 3,             // Rol administrativo de la organización
        'organizacion_id' => $organizacionId,
        'status_urc'      => '1'
    ]);

    $db->transComplete(); // 🔓 Ejecutar transacción

    if (!$db->transStatus()) {
        dd('❌ Error en la transacción completa');
    }

    return redirect()
        ->back()
        ->with('success', '✅ Organización y usuario creados correctamente.');
}



}

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

    public function validarEmailIndividual()
    {
        $usuarios = new UsuarioModel();
        $email = $this->normalizarEmail((string) $this->request->getGet('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        if ($email === '') {
            return $this->response->setJSON([
                'valid' => true,
                'message' => ''
            ]);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Ingresa un correo electrónico válido.'
            ]);
        }

        $errores = $this->validarUsuarioDisponible($usuarios, $email, $username);

        if ($errores !== []) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => reset($errores)
            ]);
        }

        return $this->response->setJSON([
            'valid' => true,
            'message' => ''
        ]);
    }

    public function validarEmailOrganizacion()
    {
        $usuarios = new UsuarioModel();
        $email = $this->normalizarEmail((string) $this->request->getGet('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        if ($email === '') {
            return $this->response->setJSON([
                'valid' => true,
                'message' => ''
            ]);
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Ingresa un correo electrónico válido.'
            ]);
        }

        $errores = $this->validarUsuarioDisponible($usuarios, $email, $username);

        if ($errores !== []) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => reset($errores)
            ]);
        }

        return $this->response->setJSON([
            'valid' => true,
            'message' => ''
        ]);
    }

    public function guardarIndividual()
    {
        $usuarios       = new UsuarioModel();
        $direcciones    = new DireccionModel();
        $rolesContexto  = new \App\Models\RolesUsuariosContextoModel();

        $email = $this->normalizarEmail((string) $this->request->getPost('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        $errores = $this->validarUsuarioDisponible($usuarios, $email, $username);
        if ($errores !== []) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $errores);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        // 1 Guardar dirección
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

        // 2 Guardar usuario — AHORA INCLUYE PROFESIÓN
        $data = [
            'nombres'        => $this->request->getPost('nombres'),
            'apellidos'      => $this->request->getPost('apellidos'),
            'genero'         => $this->request->getPost('genero'),
            'email'          => $email,
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
            'username'       => $username,
            'password_hash'  => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'telefono'       => $this->request->getPost('telefono'),
            'profesion'      => $this->request->getPost('profesion'),
            'direccion_id'   => $direccionId,
            'organizacion_id' => 1,
            'status_usu'     => '1'
        ];

        if (!$usuarios->insert($data)) {
            dd('❌ Error insertando usuario:', $usuarios->errors());
        }

        $usuarioId = $usuarios->getInsertID();

        // 3 ASIGNAR ROL POR DEFECTO (id_rol = 6)
        $rolesContexto->insert([
            'id_usuario'      => $usuarioId,
            'id_rol'          => 8,   //   INDIVIDUAL
            'organizacion_id' => 1,   //   Independiente
            'tipo_contexto'    => 'GLOBAL',
            'status_urc'      => '1',
        ]);

        $db->transComplete();

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

        $email = $this->normalizarEmail((string) $this->request->getPost('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        $errores = $this->validarUsuarioDisponible($usuarioModel, $email, $username);
        if ($errores !== []) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $errores);
        }

        $db = \Config\Database::connect();
        $db->transStart(); //   Iniciar transacción segura

        // ======  1 Insertar la dirección ======
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

        // ====== 2 Insertar la organización ======
        $orgData = [
            'nombre_org'         => $this->request->getPost('nombreOrg'),
            'tipo'               => $this->request->getPost('tipoOrg'),
            'categoria'          => $this->request->getPost('categoriaOrg'),
            'telefono'           => $this->request->getPost('telefono'),
            'email'              => $email,
            'nombre_responsable' => $this->request->getPost('nombres') . ' ' . $this->request->getPost('apellidos'),
            'direccion_id'       => $direccionId,
            'status_org'         => '1'
        ];

        if (!$organizacionModel->insert($orgData)) {
            dd('❌ Error insertando organización:', $organizacionModel->errors());
        }

        $organizacionId = $organizacionModel->getInsertID();

        // ====== 3 Insertar el usuario vinculado ======
        $userData = [
            'nombres'        => $this->request->getPost('nombres'),
            'apellidos'      => $this->request->getPost('apellidos'),
            'genero'         => $this->request->getPost('genero'),
            'email'          => $email,
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
            'username'       => $username,
            'password_hash'  => password_hash($this->request->getPost('contrasena'), PASSWORD_DEFAULT),
            'telefono'       => $this->request->getPost('telefono'),
            'profesion'      => $this->request->getPost('profesion'),
            'direccion_id'   => $direccionId,
            'organizacion_id' => $organizacionId,
            'status_usu'     => '1'
        ];

        if (!$usuarioModel->insert($userData)) {
            dd('❌ Error insertando usuario:', $usuarioModel->errors());
        }

        $usuarioId = $usuarioModel->getInsertID();

        // ====== 4 Asignar rol por defecto (3) al usuario ======
        $rolesContexto->insert([
            'id_usuario'      => $usuarioId,
            'id_rol'          => 3,             // Rol administrativo de la organización
            'organizacion_id' => $organizacionId,
            'tipo_contexto'    => 'GLOBAL',
            'status_urc'      => '1'
        ]);

        $db->transComplete(); //  Ejecutar transacción

        if (!$db->transStatus()) {
            dd('❌ Error en la transacción completa');
        }

        return redirect()
            ->back()
            ->with('success', 'Organización y usuario creados correctamente.');
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function generarUsernameDesdeEmail(string $email): string
    {
        return strtolower(trim((string) explode('@', $email)[0]));
    }

    private function validarUsuarioDisponible(UsuarioModel $usuarios, string $email, string $username): array
    {
        $errores = [];
        $organizaciones = new OrganizacionModel();

        if ($usuarios->existeEmail($email)) {
            $errores['email'] = 'El correo ya está registrado por otro usuario.';
        }

        if ($organizaciones->existeEmail($email)) {
            $errores['email'] = 'El correo ya está registrado en una organización.';
        }

        if ($usuarios->existeUsername($username)) {
            $errores['username'] = 'El username generado a partir del correo ya está registrado. Usa un correo diferente.';
        }

        return $errores;
    }
}

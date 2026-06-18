<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\RolesUsuariosContextoModel;
use CodeIgniter\Database\Exceptions\DatabaseException;

class PerfilController extends BaseController
{
    public function index()
    {
        $id = session('id_usuario');

        $usuarioModel = new UsuarioModel();
        $rolesModel   = new RolesUsuariosContextoModel();

        $perfil = $usuarioModel->obtenerPerfil($id);

        // Obtener rol del usuario
        $rolData = $rolesModel
            ->select('roles.nombre_rol, roles.descripcion_rol')
            ->join('roles', 'roles.id_rol = roles_usuarios_contexto.id_rol')
            ->where('roles_usuarios_contexto.id_usuario', $id)
            ->first();

        $perfil['rol']              = $rolData['nombre_rol']      ?? 'Sin rol asignado';
        $perfil['descripcion_rol']  = $rolData['descripcion_rol'] ?? '';

        $estadisticas = [
            'jornadas'          => $rolesModel->contarJornadas($id),
            'centros'           => $rolesModel->contarCentros($id),
            'detalle_jornadas'  => $rolesModel->obtenerJornadasUsuario($id),
            'detalle_centros'   => $rolesModel->obtenerCentrosUsuario($id),
        ];

        return view('perfil/index', compact('perfil', 'estadisticas'));
    }

    public function validarEmail()
    {
        $id = (int) session('id_usuario');
        $email = strtolower(trim((string) $this->request->getGet('email')));
        $username = explode('@', $email)[0] ?? '';

        if (!$id) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Tu sesión no es válida. Inicia sesión nuevamente.'
            ]);
        }

        if ($email === '') {
            return $this->response->setJSON([
                'valid' => true,
                'message' => ''
            ]);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Ingresa un correo electrónico válido.'
            ]);
        }

        $usuarioModel = new UsuarioModel();

        if ($usuarioModel->existeEmail($email, $id)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Este correo electrónico ya está registrado por otro usuario.'
            ]);
        }

        if ($usuarioModel->existeUsername($username, $id)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'El usuario generado por este correo ya existe.'
            ]);
        }

        return $this->response->setJSON([
            'valid' => true,
            'message' => ''
        ]);
    }

    public function actualizar()
    {
        $id = (int) session('id_usuario');

        if (!$id) {
            return redirect()->to('/login')->with('error', 'Tu sesión no es válida. Inicia sesión nuevamente.');
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $username = explode('@', $email)[0] ?? '';

        $data = [
            'nombres'          => trim((string) $this->request->getPost('nombres')),
            'apellidos'        => trim((string) $this->request->getPost('apellidos')),
            'genero'           => $this->request->getPost('genero'),
            'fecha_nacimiento' => $this->request->getPost('fecha_nacimiento'),
            'email'            => $email,
            'username'         => $username,
            'profesion'        => $this->request->getPost('profesion'),
            'telefono'         => $this->request->getPost('telefono'),
        ];

        $usuarioModel = new UsuarioModel();

        if ($usuarioModel->existeEmail($email, $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El correo electrónico ingresado ya está registrado por otro usuario.');
        }

        if ($usuarioModel->existeUsername($username, $id)) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'El nombre de usuario generado por el correo ya está registrado por otro usuario.');
        }

        try {
            $usuarioModel->update($id, $data);
        } catch (DatabaseException $e) {
            if ((int) $e->getCode() === 1062) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', 'No se pudo actualizar el perfil porque el correo o usuario ya existe.');
            }

            throw $e;
        }

        // Actualizar sesión para que el header refleje el cambio
        session()->set([
            'nombres'         => $data['nombres'],
            'apellidos'       => $data['apellidos'],
            'nombre_completo' => $data['nombres'] . ' ' . $data['apellidos'],
        ]);

        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente');
    }

    /**
     * Subir foto de perfil vía AJAX
     */
    public function subirFoto()
    {
        $id = session('id_usuario');

        if (!$id) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Sesión no válida.'
            ]);
        }

        $foto = $this->request->getFile('foto');

        if (!$foto || !$foto->isValid() || $foto->hasMoved()) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'No se recibió un archivo válido.'
            ]);
        }

        // Validar tipo
        $validTypes = ['image/jpeg', 'image/png', 'image/webp'];
        if (!in_array($foto->getMimeType(), $validTypes)) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Solo se permiten imágenes JPG, PNG o WEBP.'
            ]);
        }

        // Validar tamaño (máx 2MB)
        if ($foto->getSizeByUnit('mb') > 2) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'La imagen no debe superar los 2 MB.'
            ]);
        }

        // Crear carpeta si no existe
        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'fotos';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0775, true);
        }

        // Generar nombre único y mover
        $nuevoNombre = 'usuario_' . $id . '_' . time() . '.' . $foto->getExtension();
        $foto->move($uploadPath, $nuevoNombre);

        $rutaFoto = 'uploads/fotos/' . $nuevoNombre;

        // Borrar foto anterior si existe
        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->find($id);

        if (!empty($usuario['foto_url']) && file_exists(FCPATH . $usuario['foto_url'])) {
            @unlink(FCPATH . $usuario['foto_url']);
        }

        // Guardar ruta en BD
        $usuarioModel->update($id, ['foto_url' => $rutaFoto]);

        return $this->response->setJSON([
            'success'  => true,
            'foto_url' => base_url($rutaFoto) . '?v=' . time()
        ]);
    }
}

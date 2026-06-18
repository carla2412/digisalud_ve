<?php

namespace App\Controllers;

use App\Models\OrganizacionModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class OrganizacionesEmailValidation extends BaseController
{
    private const ROLES_PERMITIDOS = [1, 2, 3, 4, 5, 6, 7];

    public function validar(int $organizacionId): ResponseInterface
    {
        if (! $this->tieneAcceso()) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Tu sesión no es válida o no tienes permisos para validar este correo.'
            ]);
        }

        $email = strtolower(trim((string) $this->request->getGet('email')));

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

        $organizacionModel = new OrganizacionModel();
        $usuarioModel = new UsuarioModel();

        $organizacion = $organizacionModel->find($organizacionId);

        if (! $organizacion) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Organización no encontrada.'
            ]);
        }

        $responsable = $this->buscarResponsableOrganizacion($usuarioModel, $organizacionId);
        $responsableId = $responsable ? (int) $responsable['id_usuario'] : null;
        $username = strtolower(trim((string) explode('@', $email)[0]));

        if ($organizacionModel->existeEmail($email, $organizacionId)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Este correo electrónico ya está registrado en otra organización.'
            ]);
        }

        if ($usuarioModel->existeEmail($email, $responsableId)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'Este correo electrónico ya está registrado por otro usuario.'
            ]);
        }

        if ($usuarioModel->existeUsername($username, $responsableId)) {
            return $this->response->setJSON([
                'valid' => false,
                'message' => 'El usuario generado por este correo ya existe. Usa un correo diferente.'
            ]);
        }

        return $this->response->setJSON([
            'valid' => true,
            'message' => ''
        ]);
    }

    private function tieneAcceso(): bool
    {
        $session = session();

        if (! $session->has('id_usuario') || ! $session->get('id_usuario')) {
            return false;
        }

        return in_array((int) $session->get('id_rol'), self::ROLES_PERMITIDOS, true);
    }

    private function buscarResponsableOrganizacion(UsuarioModel $usuarioModel, int $organizacionId): ?array
    {
        return $usuarioModel
            ->select('usuarios.*')
            ->join('roles_usuarios_contexto ruc', 'ruc.id_usuario = usuarios.id_usuario', 'left')
            ->where('usuarios.organizacion_id', $organizacionId)
            ->where('usuarios.status_usu', 1)
            ->where('ruc.id_rol', 3)
            ->where('ruc.status_urc', 1)
            ->orderBy('usuarios.id_usuario', 'ASC')
            ->first();
    }
}

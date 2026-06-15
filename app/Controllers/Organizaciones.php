<?php

namespace App\Controllers;

use App\Models\OrganizacionModel;
use App\Models\RolesUsuariosContextoModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class Organizaciones extends BaseController
{
    private const ROLES_PERMITIDOS = [1, 2, 3, 4, 5, 6, 7];
    private const LOGO_DIR = FCPATH . 'uploads/logos/';
    private const LOGO_EXTENSIONES = ['png', 'jpg', 'jpeg'];
    private const LOGO_MIMES = [
        'image/png'  => 'png',
        'image/jpeg' => 'jpg',
    ];

    private OrganizacionModel $model;

    public function initController(
        \CodeIgniter\HTTP\RequestInterface $request,
        \CodeIgniter\HTTP\ResponseInterface $response,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
        $this->model = new OrganizacionModel();
    }

    private function verificarAcceso(): bool
    {
        $session = session();

        if (! $session->has('id_usuario') || ! $session->get('id_usuario')) {
            return redirect()->to(base_url('login'))->send() && false;
        }

        $idRol = (int) $session->get('id_rol');

        if (! in_array($idRol, self::ROLES_PERMITIDOS, true)) {
            $session->setFlashdata('error', 'No tienes permisos para acceder a este módulo.');
            redirect()->to(base_url('dashboard'))->send();
            return false;
        }

        return true;
    }

    private function esAdminGeneral(): bool
    {
        return in_array((int) session()->get('id_rol'), [1, 2], true);
    }

    public function index(): string|ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $idRol = (int) session()->get('id_rol');
        $organizacionSesion = (int) session()->get('organizacion_id');

        $builder = $this->model
            ->select('organizacion.*, direcciones.estado, direcciones.ciudad')
            ->join('direcciones', 'direcciones.id_direccion = organizacion.direccion_id', 'left')
            ->where('organizacion.status_org', 1);

        if (in_array($idRol, [3, 4, 5, 6, 7], true)) {
            $builder->where('organizacion.id_organizacion', $organizacionSesion);
        }

        $organizaciones = $builder
            ->orderBy('organizacion.nombre_org', 'ASC')
            ->findAll();

        return view('organizaciones/index', [
            'titulo'         => 'Organizaciones',
            'organizaciones' => $organizaciones,
        ]);
    }

    public function create(): string|ResponseInterface
    {
        if (! $this->esAdminGeneral()) {
            session()->setFlashdata('error', 'No tienes permisos para crear organizaciones.');
            return redirect()->to(base_url('organizaciones'));
        }

        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        return view('organizaciones/create_org', [
            'titulo' => 'Nueva Organización',
        ]);
    }

    public function store(): ResponseInterface
    {
        if (! $this->esAdminGeneral()) {
            session()->setFlashdata('error', 'No tienes permisos para crear organizaciones.');
            return redirect()->to(base_url('organizaciones'));
        }

        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $rules = [
            'nombre_org'                   => 'required|max_length[120]',
            'tipo'                         => 'required|max_length[50]',
            'categoria'                    => 'required|max_length[80]',
            'telefono'                     => 'required|max_length[30]',
            'email'                        => 'required|valid_email|max_length[120]',
            'responsable_nombres'          => 'required|max_length[80]',
            'responsable_apellidos'        => 'required|max_length[80]',
            'responsable_fecha_nacimiento' => 'required|valid_date[Y-m-d]',
            'responsable_genero'           => 'required|in_list[M,F]',
            'password'                     => 'required|min_length[6]|max_length[255]',
            'confirmar_password'           => 'required|matches[password]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();
        $email = $this->normalizarEmail((string) $this->request->getPost('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        $erroresUnicidad = $this->validarEmailYUsernameDisponibles($usuarioModel, $email, $username);
        if ($erroresUnicidad !== []) {
            return redirect()->back()->withInput()->with('errors', $erroresUnicidad);
        }

        $db = \Config\Database::connect();
        $db->transStart();

        $logoUrl = null;
        $archivo = $this->request->getFile('logo');

        if ($archivo && $archivo->isValid() && ! $archivo->hasMoved()) {
            $resultado = $this->procesarLogo($archivo);

            if ($resultado['error']) {
                return redirect()->back()->withInput()->with('errors', ['logo' => $resultado['mensaje']]);
            }

            $logoUrl = $resultado['filename'];
        }

        $direccionId = $this->procesarDireccion();
        $responsableNombres = trim((string) $this->request->getPost('responsable_nombres'));
        $responsableApellidos = trim((string) $this->request->getPost('responsable_apellidos'));
        $nombreResponsable = trim($responsableNombres . ' ' . $responsableApellidos);

        $this->model->skipValidation(true)->insert([
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'email'              => $email,
            'nombre_responsable' => $nombreResponsable,
            'direccion_id'       => $direccionId,
            'logo_url'           => $logoUrl,
            'status_org'         => 1,
            'creado_en'          => date('Y-m-d H:i:s'),
            'creado_por'         => (int) session()->get('id_usuario'),
        ]);

        $organizacionId = $this->model->getInsertID();
        $rolesContexto = new RolesUsuariosContextoModel();

        $usuarioModel->insert([
            'nombres'          => $responsableNombres,
            'apellidos'        => $responsableApellidos,
            'genero'           => $this->request->getPost('responsable_genero'),
            'fecha_nacimiento' => $this->request->getPost('responsable_fecha_nacimiento'),
            'email'            => $email,
            'username'         => $username,
            'password_hash'    => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'telefono'         => $this->request->getPost('telefono'),
            'direccion_id'     => $direccionId,
            'organizacion_id'  => $organizacionId,
            'status_usu'       => 1,
            'creado_en'        => date('Y-m-d H:i:s'),
            'creado_por'       => (int) session()->get('id_usuario'),
        ]);

        $usuarioId = $usuarioModel->getInsertID();

        $rolesContexto->insert([
            'id_usuario'      => $usuarioId,
            'id_rol'          => 3,
            'organizacion_id' => $organizacionId,
            'tipo_contexto'   => 'GLOBAL',
            'status_urc'      => 1,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            session()->setFlashdata('error', 'No se pudo crear la organización.');
            return redirect()->back()->withInput();
        }

        session()->setFlashdata('success', 'Organización creada exitosamente.');
        return redirect()->to(base_url('organizaciones'));
    }

    public function edit(int $id): string|ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $organizacion = $this->model->find($id);

        if (! $organizacion) {
            session()->setFlashdata('error', 'Organización no encontrada.');
            return redirect()->to(base_url('organizaciones'));
        }

        $direccion = null;
        if (! empty($organizacion['direccion_id'])) {
            $db = \Config\Database::connect();
            $direccion = $db->table('direcciones')
                ->where('id_direccion', $organizacion['direccion_id'])
                ->get()
                ->getRowArray();
        }

        $usuarioModel = new UsuarioModel();
        $responsable = $this->buscarResponsableOrganizacion($usuarioModel, $id);

        return view('organizaciones/editar_org', [
            'titulo'       => 'Editar Organización',
            'organizacion' => $organizacion,
            'direccion'    => $direccion,
            'responsable'  => $responsable,
        ]);
    }

    public function update(int $id): ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $organizacion = $this->model->find($id);

        if (! $organizacion) {
            session()->setFlashdata('error', 'Organización no encontrada.');
            return redirect()->to(base_url('organizaciones'));
        }

        $rules = [
            'nombre_org'                   => 'required|max_length[120]',
            'tipo'                         => 'required|max_length[50]',
            'categoria'                    => 'required|max_length[80]',
            'telefono'                     => 'required|max_length[30]',
            'email'                        => 'required|valid_email|max_length[120]',
            'responsable_nombres'          => 'required|max_length[80]',
            'responsable_apellidos'        => 'required|max_length[80]',
            'password'                     => 'permit_empty|min_length[6]|max_length[255]',
            'confirmar_password'           => 'matches[password]',
            'responsable_fecha_nacimiento' => 'required|valid_date[Y-m-d]',
            'responsable_genero'           => 'required|in_list[M,F]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $usuarioModel = new UsuarioModel();
        $responsable = $this->buscarResponsableOrganizacion($usuarioModel, $id);
        $responsableId = $responsable ? (int) $responsable['id_usuario'] : null;
        $email = $this->normalizarEmail((string) $this->request->getPost('email'));
        $username = $this->generarUsernameDesdeEmail($email);

        $erroresUnicidad = $this->validarEmailYUsernameDisponibles($usuarioModel, $email, $username, $responsableId, $id);
        if ($erroresUnicidad !== []) {
            return redirect()->back()->withInput()->with('errors', $erroresUnicidad);
        }

        $logoUrl = $organizacion['logo_url'];
        $archivo = $this->request->getFile('logo');

        if ($archivo && $archivo->isValid() && ! $archivo->hasMoved()) {
            $resultado = $this->procesarLogo($archivo);

            if ($resultado['error']) {
                return redirect()->back()->withInput()->with('errors', ['logo' => $resultado['mensaje']]);
            }

            if (! empty($organizacion['logo_url'])) {
                $this->eliminarLogo($organizacion['logo_url']);
            }

            $logoUrl = $resultado['filename'];
        }

        $direccionId = $this->procesarDireccion($organizacion['direccion_id']);
        $responsableNombres = trim((string) $this->request->getPost('responsable_nombres'));
        $responsableApellidos = trim((string) $this->request->getPost('responsable_apellidos'));
        $nombreResponsable = trim($responsableNombres . ' ' . $responsableApellidos);

        $this->model->skipValidation(true)->update($id, [
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'email'              => $email,
            'nombre_responsable' => $nombreResponsable,
            'direccion_id'       => $direccionId,
            'logo_url'           => $logoUrl,
        ]);

        if ($responsable) {
            $userData = [
                'nombres'          => $responsableNombres,
                'apellidos'        => $responsableApellidos,
                'email'            => $email,
                'username'         => $username,
                'telefono'         => $this->request->getPost('telefono'),
                'genero'           => $this->request->getPost('responsable_genero'),
                'fecha_nacimiento' => $this->request->getPost('responsable_fecha_nacimiento'),
            ];

            if ($this->request->getPost('password')) {
                $userData['password_hash'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
            }

            $usuarioModel->update($responsable['id_usuario'], $userData);
        }

        session()->setFlashdata('success', 'Organización actualizada correctamente.');
        return redirect()->to(base_url('organizaciones'));
    }

    public function logo(string $filename): ResponseInterface
    {
        if (! session()->has('id_usuario') || ! session()->get('id_usuario')) {
            return $this->response->setStatusCode(403)->setBody('Acceso denegado.');
        }

        $filename = basename($filename);
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (! in_array($ext, self::LOGO_EXTENSIONES, true)) {
            return $this->response->setStatusCode(400)->setBody('Tipo de archivo no permitido.');
        }

        $ruta = self::LOGO_DIR . $filename;
        if (! is_file($ruta)) {
            return $this->response->setStatusCode(404)->setBody('Logo no encontrado.');
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($ruta);
        if (! array_key_exists($mimeReal, self::LOGO_MIMES)) {
            return $this->response->setStatusCode(415)->setBody('Tipo MIME no permitido.');
        }

        return $this->response
            ->setHeader('Content-Type', $mimeReal)
            ->setHeader('Content-Disposition', 'inline; filename="' . $filename . '"')
            ->setHeader('X-Content-Type-Options', 'nosniff')
            ->setHeader('Cache-Control', 'private, max-age=3600')
            ->setBody(file_get_contents($ruta));
    }

    private function procesarDireccion(?int $direccionIdExistente = null): ?int
    {
        if (! $this->request->getPost('direccion_activa')) {
            return $direccionIdExistente;
        }

        $estado = $this->request->getPost('estado');
        if (empty($estado)) {
            return $direccionIdExistente;
        }

        $datosDireccion = [
            'pais'      => $this->request->getPost('pais') ?: 'Venezuela',
            'estado'    => $estado,
            'municipio' => $this->request->getPost('municipio'),
            'parroquia' => $this->request->getPost('parroquia'),
            'ciudad'    => $this->request->getPost('ciudad'),
            'detalle'   => $this->request->getPost('detalle'),
        ];

        $db = \Config\Database::connect();

        if ($direccionIdExistente) {
            $db->table('direcciones')->where('id_direccion', $direccionIdExistente)->update($datosDireccion);
            return $direccionIdExistente;
        }

        $db->table('direcciones')->insert($datosDireccion);
        return $db->insertID();
    }

    private function procesarLogo(\CodeIgniter\HTTP\Files\UploadedFile $archivo): array
    {
        if (! $archivo->isValid()) {
            return ['error' => true, 'mensaje' => 'El archivo no se cargó correctamente.', 'filename' => ''];
        }

        $extOriginal = strtolower($archivo->getClientExtension());
        if (! in_array($extOriginal, self::LOGO_EXTENSIONES, true)) {
            return ['error' => true, 'mensaje' => 'Solo se permiten imágenes PNG, JPG o JPEG.', 'filename' => ''];
        }

        $mimeCliente = $archivo->getClientMimeType();
        if (! array_key_exists($mimeCliente, self::LOGO_MIMES)) {
            return ['error' => true, 'mensaje' => 'El tipo de archivo no está permitido.', 'filename' => ''];
        }

        $tmpPath = $archivo->getTempName();
        if (@getimagesize($tmpPath) === false) {
            return ['error' => true, 'mensaje' => 'El archivo no es una imagen válida.', 'filename' => ''];
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($tmpPath);
        if (! array_key_exists($mimeReal, self::LOGO_MIMES)) {
            return ['error' => true, 'mensaje' => 'El contenido del archivo no corresponde a una imagen permitida.', 'filename' => ''];
        }

        if (! is_dir(self::LOGO_DIR)) {
            mkdir(self::LOGO_DIR, 0755, true);
        }

        $ext = self::LOGO_MIMES[$mimeReal];
        $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;

        while (file_exists(self::LOGO_DIR . $nuevoNombre)) {
            $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;
        }

        if (! $archivo->move(self::LOGO_DIR, $nuevoNombre)) {
            return ['error' => true, 'mensaje' => 'Error al guardar el archivo en el servidor.', 'filename' => ''];
        }

        return ['error' => false, 'mensaje' => '', 'filename' => $nuevoNombre];
    }

    private function eliminarLogo(string $filename): void
    {
        $filename = basename($filename);
        $ruta = self::LOGO_DIR . $filename;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }

    private function normalizarEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function generarUsernameDesdeEmail(string $email): string
    {
        return strtolower(trim((string) explode('@', $email)[0]));
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

    private function existeEmailOrganizacion(string $email, ?int $exceptoOrganizacionId = null): bool
    {
        $builder = $this->model->where('email', $email);

        if ($exceptoOrganizacionId !== null) {
            $builder->where('id_organizacion !=', $exceptoOrganizacionId);
        }

        return $builder->first() !== null;
    }

    private function validarEmailYUsernameDisponibles(
        UsuarioModel $usuarioModel,
        string $email,
        string $username,
        ?int $exceptoUsuarioId = null,
        ?int $exceptoOrganizacionId = null
    ): array {
        $errores = [];

        if ($this->existeEmailOrganizacion($email, $exceptoOrganizacionId)) {
            $errores['email'] = 'El correo ya está registrado en otra organización.';
        }

        if ($usuarioModel->existeEmail($email, $exceptoUsuarioId)) {
            $errores['email'] = 'El correo ya está registrado en otro usuario.';
        }

        if ($usuarioModel->existeUsername($username, $exceptoUsuarioId)) {
            $errores['username'] = 'El username generado a partir del correo ya está registrado. Usa un correo diferente.';
        }

        return $errores;
    }
}

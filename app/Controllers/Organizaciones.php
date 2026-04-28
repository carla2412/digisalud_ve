<?php

namespace App\Controllers;

use App\Models\OrganizacionModel;
use CodeIgniter\Controller;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Controlador Organizaciones
 *
 * Gestiona el CRUD de la tabla `organizacion`.
 * Acceso restringido a roles: 1 (Admin TI), 2 (Admin Digisalud), 3 (Admin Org).
 *
 * Vistas separadas:
 *  - organizaciones/create_org  → formulario de creación
 *  - organizaciones/editar_org  → formulario de edición
 *
 * Gestión de logos:
 *  - Almacenamiento en FCPATH . 'uploads/logos/'
 *  - Servido mediante método logo() con verificación de sesión
 *  - Renombrado aleatorio con bin2hex(random_bytes(16))
 *  - Validación MIME real via getClientMimeType() + getImageType()
 */
class Organizaciones extends BaseController
{
    // Roles con acceso al módulo
    private const ROLES_PERMITIDOS = [1, 2, 3];

    // Directorio de logos
    private const LOGO_DIR = FCPATH . 'uploads/logos/';

    // Extensiones permitidas (minúsculas)
    private const LOGO_EXTENSIONES = ['png', 'jpg', 'jpeg'];

    // MIME types permitidos mapeados
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

    // ----------------------------------------------------------------
    // Control de acceso centralizado
    // ----------------------------------------------------------------

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

    // ----------------------------------------------------------------
    // index — Listar organizaciones
    // ----------------------------------------------------------------

    public function index(): string|ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $organizaciones = $this->model
            ->select('organizacion.*, direcciones.estado, direcciones.ciudad')
            ->join('direcciones', 'direcciones.id_direccion = organizacion.direccion_id', 'left')
            ->where('organizacion.status_org', 1)
            ->orderBy('organizacion.nombre_org', 'ASC')
            ->findAll();

        return view('organizaciones/index', [
            'titulo'         => 'Organizaciones',
            'organizaciones' => $organizaciones,
        ]);
    }

    // ----------------------------------------------------------------
    // create — Mostrar formulario de creación
    // ----------------------------------------------------------------

    public function create(): string|ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        return view('organizaciones/create_org', [
            'titulo' => 'Nueva Organización',
        ]);
    }

    // ----------------------------------------------------------------
    // store — Guardar nueva organización
    // ----------------------------------------------------------------

    public function store(): ResponseInterface
    {
        if (! $this->verificarAcceso()) {
            return $this->response;
        }

        $rules = [
            'nombre_org'         => 'required|max_length[120]',
            'tipo'               => 'required|max_length[50]',
            'categoria'          => 'required|max_length[80]',
            'telefono'           => 'required|max_length[30]',
            'email'             => 'required|valid_email|max_length[120]',
            'nombre_responsable' => 'permit_empty|max_length[120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Procesar logo si fue cargado
        $logoUrl = null;
        $archivo = $this->request->getFile('logo');

        if ($archivo && $archivo->isValid() && ! $archivo->hasMoved()) {
            $resultado = $this->procesarLogo($archivo);

            if ($resultado['error']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', ['logo' => $resultado['mensaje']]);
            }

            $logoUrl = $resultado['filename'];
        }

        // Procesar dirección si la sección fue activada
        $direccionId = $this->procesarDireccion();

        // Preparar datos para inserción
        $datos = [
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'email'             => $this->request->getPost('email'),
            'nombre_responsable' => $this->request->getPost('nombre_responsable'),
            'direccion_id'       => $direccionId,
            'logo_url'           => $logoUrl,
            'status_org'         => 1,
            'creado_en'          => date('Y-m-d H:i:s'),
            'creado_por'         => (int) session()->get('id_usuario'),
        ];

        $this->model->skipValidation(true)->insert($datos);

        session()->setFlashdata('success', 'Organización creada exitosamente.');
        return redirect()->to(base_url('organizaciones'));
    }

    // ----------------------------------------------------------------
    // edit — Mostrar formulario de edición
    // ----------------------------------------------------------------

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

        // Obtener datos de dirección si existe
        $direccion = null;
        if (! empty($organizacion['direccion_id'])) {
            $db = \Config\Database::connect();
            $direccion = $db->table('direcciones')
                ->where('id_direccion', $organizacion['direccion_id'])
                ->get()
                ->getRowArray();
        }

        return view('organizaciones/editar_org', [
            'titulo'       => 'Editar Organización',
            'organizacion' => $organizacion,
            'direccion'    => $direccion,
        ]);
    }

    // ----------------------------------------------------------------
    // update — Actualizar organización existente
    // ----------------------------------------------------------------

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
            'nombre_org'         => 'required|max_length[120]',
            'tipo'               => 'required|max_length[50]',
            'categoria'          => 'required|max_length[80]',
            'telefono'           => 'required|max_length[30]',
            'email'             => 'required|valid_email|max_length[120]',
            'nombre_responsable' => 'permit_empty|max_length[120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Manejar el logo
        $logoUrl = $organizacion['logo_url'];
        $archivo = $this->request->getFile('logo');

        if ($archivo && $archivo->isValid() && ! $archivo->hasMoved()) {
            $resultado = $this->procesarLogo($archivo);

            if ($resultado['error']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', ['logo' => $resultado['mensaje']]);
            }

            if (! empty($organizacion['logo_url'])) {
                $this->eliminarLogo($organizacion['logo_url']);
            }

            $logoUrl = $resultado['filename'];
        }

        // Procesar dirección
        $direccionId = $this->procesarDireccion($organizacion['direccion_id']);

        $datos = [
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'email'             => $this->request->getPost('email'),
            'nombre_responsable' => $this->request->getPost('nombre_responsable'),
            'direccion_id'       => $direccionId,
            'logo_url'           => $logoUrl,
        ];

        $this->model->skipValidation(true)->update($id, $datos);

        session()->setFlashdata('success', 'Organización actualizada correctamente.');
        return redirect()->to(base_url('organizaciones'));
    }

    // ----------------------------------------------------------------
    // logo — Servir el archivo de logo
    // ----------------------------------------------------------------

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

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
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

    // ----------------------------------------------------------------
    // Métodos privados
    // ----------------------------------------------------------------

    /**
     * Procesa la sección de dirección del formulario.
     * Inserta o actualiza en la tabla `direcciones`.
     *
     * @param int|null $direccionIdExistente ID de dirección existente (para update)
     * @return int|null ID de la dirección o null si no se activó la sección
     */
    private function procesarDireccion(?int $direccionIdExistente = null): ?int
    {
        // Si la sección de dirección no fue activada, conservar valor anterior
        if (! $this->request->getPost('direccion_activa')) {
            return $direccionIdExistente;
        }

        $estado    = $this->request->getPost('estado');
        $municipio = $this->request->getPost('municipio');

        // Si no hay estado seleccionado, no guardar dirección
        if (empty($estado)) {
            return $direccionIdExistente;
        }

        $datosDireccion = [
            'pais'      => $this->request->getPost('pais') ?: 'Venezuela',
            'estado'    => $estado,
            'municipio' => $municipio,
            'parroquia' => $this->request->getPost('parroquia'),
            'ciudad'    => $this->request->getPost('ciudad'),
        ];

        $db = \Config\Database::connect();

        if ($direccionIdExistente) {
            // Actualizar dirección existente
            $db->table('direcciones')
                ->where('id_direccion', $direccionIdExistente)
                ->update($datosDireccion);
            return $direccionIdExistente;
        }

        // Insertar nueva dirección
        $db->table('direcciones')->insert($datosDireccion);
        return $db->insertID();
    }

    /**
     * Procesa y guarda el archivo de logo de forma segura.
     */
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
        $imgInfo = @getimagesize($tmpPath);
        if ($imgInfo === false) {
            return ['error' => true, 'mensaje' => 'El archivo no es una imagen válida.', 'filename' => ''];
        }

        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($tmpPath);
        if (! array_key_exists($mimeReal, self::LOGO_MIMES)) {
            return ['error' => true, 'mensaje' => 'El contenido del archivo no corresponde a una imagen permitida.', 'filename' => ''];
        }

        if (! is_dir(self::LOGO_DIR)) {
            mkdir(self::LOGO_DIR, 0755, true);
        }

        $ext         = self::LOGO_MIMES[$mimeReal];
        $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;

        while (file_exists(self::LOGO_DIR . $nuevoNombre)) {
            $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;
        }

        if (! $archivo->move(self::LOGO_DIR, $nuevoNombre)) {
            return ['error' => true, 'mensaje' => 'Error al guardar el archivo en el servidor.', 'filename' => ''];
        }

        return ['error' => false, 'mensaje' => '', 'filename' => $nuevoNombre];
    }

    /**
     * Elimina el archivo de logo del servidor si existe.
     */
    private function eliminarLogo(string $filename): void
    {
        $filename = basename($filename);
        $ruta     = self::LOGO_DIR . $filename;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}
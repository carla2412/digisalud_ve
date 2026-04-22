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
 * Gestión de logos:
 *  - Almacenamiento en WRITABLE_PATH . 'uploads/logos/' (fuera del public root)
 *  - Servido mediante método logo() con verificación de rol
 *  - Renombrado aleatorio con bin2hex(random_bytes(16))
 *  - Validación MIME real via getClientMimeType() + getImageType()
 */
class Organizaciones extends BaseController
{
    // Roles con acceso al módulo
    private const ROLES_PERMITIDOS = [1, 2, 3];

    // Directorio de logos fuera del public root
    private const LOGO_DIR = WRITEPATH . 'uploads/logos/';

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

    /**
     * Verifica que el usuario tenga sesión activa y rol autorizado.
     * Redirige al login si no está autenticado.
     * Redirige al dashboard con error si el rol no está permitido.
     */
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

        return view('organizaciones/form', [
            'titulo'       => 'Nueva Organización',
            'organizacion' => null,
            'accion'       => 'store',
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

        // Validar campos del formulario
        $rules = [
            'nombre_org'        => 'required|max_length[120]',
            'tipo'              => 'required|max_length[50]',
            'categoria'         => 'required|max_length[80]',
            'telefono'          => 'required|max_length[30]',
            'correo'            => 'required|valid_email|max_length[120]',
            'nombre_responsable'=> 'permit_empty|max_length[120]',
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

        // Preparar datos para inserción
        $datos = [
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'correo'             => $this->request->getPost('correo'),
            'nombre_responsable' => $this->request->getPost('nombre_responsable'),
            'direccion_id'       => $this->request->getPost('direccion_id') ?: null,
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

        return view('organizaciones/form', [
            'titulo'       => 'Editar Organización',
            'organizacion' => $organizacion,
            'accion'       => 'update/' . $id,
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

        // Validar campos del formulario
        $rules = [
            'nombre_org'         => 'required|max_length[120]',
            'tipo'               => 'required|max_length[50]',
            'categoria'          => 'required|max_length[80]',
            'telefono'           => 'required|max_length[30]',
            'correo'             => 'required|valid_email|max_length[120]',
            'nombre_responsable' => 'permit_empty|max_length[120]',
        ];

        if (! $this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Manejar el logo — conservar el anterior si no se sube uno nuevo
        $logoUrl = $organizacion['logo_url']; // valor actual por defecto
        $archivo = $this->request->getFile('logo');

        if ($archivo && $archivo->isValid() && ! $archivo->hasMoved()) {
            $resultado = $this->procesarLogo($archivo);

            if ($resultado['error']) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('errors', ['logo' => $resultado['mensaje']]);
            }

            // Eliminar logo anterior del servidor si existe
            if (! empty($organizacion['logo_url'])) {
                $this->eliminarLogo($organizacion['logo_url']);
            }

            $logoUrl = $resultado['filename'];
        }

        $datos = [
            'nombre_org'         => $this->request->getPost('nombre_org'),
            'tipo'               => $this->request->getPost('tipo'),
            'categoria'          => $this->request->getPost('categoria'),
            'telefono'           => $this->request->getPost('telefono'),
            'correo'             => $this->request->getPost('correo'),
            'nombre_responsable' => $this->request->getPost('nombre_responsable'),
            'direccion_id'       => $this->request->getPost('direccion_id') ?: null,
            'logo_url'           => $logoUrl,
        ];

        $this->model->skipValidation(true)->update($id, $datos);

        session()->setFlashdata('success', 'Organización actualizada correctamente.');
        return redirect()->to(base_url('organizaciones'));
    }

    // ----------------------------------------------------------------
    // logo — Servir el archivo de logo fuera del public root
    // ----------------------------------------------------------------

    /**
     * Endpoint seguro para visualizar logos.
     * URL: /organizaciones/logo/{filename}
     *
     * Verifica sesión activa antes de servir el archivo.
     * No expone rutas absolutas del servidor.
     */
    public function logo(string $filename): ResponseInterface
    {
        // Solo usuarios autenticados pueden ver logos
        if (! session()->has('id_usuario') || ! session()->get('id_usuario')) {
            return $this->response->setStatusCode(403)->setBody('Acceso denegado.');
        }

        // Sanitizar: solo nombre de archivo, sin path traversal
        $filename = basename($filename);

        // Validar extensión
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        if (! in_array($ext, self::LOGO_EXTENSIONES, true)) {
            return $this->response->setStatusCode(400)->setBody('Tipo de archivo no permitido.');
        }

        $ruta = self::LOGO_DIR . $filename;

        if (! is_file($ruta)) {
            return $this->response->setStatusCode(404)->setBody('Logo no encontrado.');
        }

        // Determinar Content-Type real desde el archivo
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
    // Métodos privados de gestión de archivos
    // ----------------------------------------------------------------

    /**
     * Procesa y guarda el archivo de logo de forma segura.
     *
     * @return array{error: bool, mensaje: string, filename: string}
     */
    private function procesarLogo(\CodeIgniter\HTTP\Files\UploadedFile $archivo): array
    {
        // 1. Verificar que fue cargado correctamente
        if (! $archivo->isValid()) {
            return ['error' => true, 'mensaje' => 'El archivo no se cargó correctamente.', 'filename' => ''];
        }

        // 2. Validar extensión del nombre original
        $extOriginal = strtolower($archivo->getClientExtension());
        if (! in_array($extOriginal, self::LOGO_EXTENSIONES, true)) {
            return ['error' => true, 'mensaje' => 'Solo se permiten imágenes PNG, JPG o JPEG.', 'filename' => ''];
        }

        // 3. Validar MIME real reportado por el cliente
        $mimeCliente = $archivo->getClientMimeType();
        if (! array_key_exists($mimeCliente, self::LOGO_MIMES)) {
            return ['error' => true, 'mensaje' => 'El tipo de archivo no está permitido.', 'filename' => ''];
        }

        // 4. Validar que sea realmente una imagen con getimagesize()
        $tmpPath = $archivo->getTempName();
        $imgInfo = @getimagesize($tmpPath);
        if ($imgInfo === false) {
            return ['error' => true, 'mensaje' => 'El archivo no es una imagen válida.', 'filename' => ''];
        }

        // 5. Validar MIME real del binario (segunda capa)
        $finfo    = new \finfo(FILEINFO_MIME_TYPE);
        $mimeReal = $finfo->file($tmpPath);
        if (! array_key_exists($mimeReal, self::LOGO_MIMES)) {
            return ['error' => true, 'mensaje' => 'El contenido del archivo no corresponde a una imagen permitida.', 'filename' => ''];
        }

        // 6. Crear directorio si no existe
        if (! is_dir(self::LOGO_DIR)) {
            mkdir(self::LOGO_DIR, 0755, true);
        }

        // 7. Generar nombre aleatorio seguro (sin colisiones)
        $ext         = self::LOGO_MIMES[$mimeReal];
        $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;

        // Evitar colisión (improbable pero seguro)
        while (file_exists(self::LOGO_DIR . $nuevoNombre)) {
            $nuevoNombre = bin2hex(random_bytes(16)) . '.' . $ext;
        }

        // 8. Mover el archivo al directorio seguro
        if (! $archivo->move(self::LOGO_DIR, $nuevoNombre)) {
            return ['error' => true, 'mensaje' => 'Error al guardar el archivo en el servidor.', 'filename' => ''];
        }

        return ['error' => false, 'mensaje' => '', 'filename' => $nuevoNombre];
    }

    /**
     * Elimina el archivo de logo del servidor si existe.
     * Valida que sea un nombre de archivo simple (sin path traversal).
     */
    private function eliminarLogo(string $filename): void
    {
        $filename = basename($filename); // prevenir path traversal
        $ruta     = self::LOGO_DIR . $filename;

        if (is_file($ruta)) {
            unlink($ruta);
        }
    }
}
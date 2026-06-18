<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * OrganizacionModel
 *
 * Modelo para la tabla `organizacion` de digisalud_ci.
 * Motor: MyISAM — no soporta transacciones ni FK nativas.
 *
 * Columnas reales confirmadas en el dump SQL:
 *   id_organizacion, nombre_org, tipo, categoria, telefono, email,
 *   nombre_responsable, direccion_id, logo_url (agregada vía migración),
 *   status_org, creado_en, creado_por
 */
class OrganizacionModel extends Model
{
    protected $table            = 'organizacion';
    protected $primaryKey       = 'id_organizacion';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    // Campos que el modelo puede insertar/actualizar
    protected $allowedFields = [
        'nombre_org',
        'tipo',
        'categoria',
        'telefono',
        'email',
        'nombre_responsable',
        'direccion_id',
        'logo_url',
        'status_org',
        'creado_en',
        'creado_por',
    ];

    // Timestamps automáticos — desactivados porque la tabla usa creado_en manual
    protected $useTimestamps = false;

    // ----------------------------------------------------------------
    // Reglas de validación del modelo
    // NOTA: La validación del archivo (logo) se hace en el Controlador
    // para evitar que el campo "logo" requerido bloquee el UPDATE
    // cuando el usuario no sube un archivo nuevo.
    // ----------------------------------------------------------------
    protected $validationRules = [
        'nombre_org' => [
            'label' => 'Nombre de la organización',
            'rules' => 'required|max_length[120]',
        ],
        'tipo' => [
            'label' => 'Tipo',
            'rules' => 'required|max_length[50]',
        ],
        'categoria' => [
            'label' => 'Categoría',
            'rules' => 'required|max_length[80]',
        ],
        'telefono' => [
            'label' => 'Teléfono',
            'rules' => 'required|max_length[30]|regex_match[/^\+?[\d\s\-\(\)]{7,30}$/]',
        ],
        'email' => [
            'label' => 'email electrónico',
            'rules' => 'required|valid_email|max_length[120]',
        ],
        'nombre_responsable' => [
            'label' => 'Nombre del responsable',
            'rules' => 'permit_empty|max_length[120]',
        ],
    ];

    protected $validationMessages = [
        'nombre_org' => [
            'required'   => 'El nombre de la organización es obligatorio.',
            'max_length' => 'El nombre no puede exceder 120 caracteres.',
        ],
        'email' => [
            'required'    => 'El email electrónico es obligatorio.',
            'valid_email' => 'El email electrónico no tiene un formato válido.',
        ],
        'telefono' => [
            'required'     => 'El teléfono es obligatorio.',
            'regex_match'  => 'El teléfono no tiene un formato válido (ej: +58 412 0000000).',
        ],
        'categoria' => [
            'required' => 'La categoría es obligatoria.',
        ],
        'tipo' => [
            'required' => 'El tipo de organización es obligatorio.',
        ],
    ];

    protected $skipValidation = false;

    // ----------------------------------------------------------------
    // Queries auxiliares
    // ----------------------------------------------------------------

    public function existeEmail(string $email, ?int $exceptoOrganizacionId = null): bool
    {
        $builder = $this->where('email', strtolower(trim($email)));

        if ($exceptoOrganizacionId !== null) {
            $builder->where($this->primaryKey . ' !=', $exceptoOrganizacionId);
        }

        return $builder->first() !== null;
    }

    /**
     * Retorna todas las organizaciones activas ordenadas por nombre.
     */
    public function getActivas(): array
    {
        return $this->where('status_org', 1)
                    ->orderBy('nombre_org', 'ASC')
                    ->findAll();
    }

    /**
     * Retorna organizacion con datos de dirección via JOIN.
     * Útil para el index con más detalle.
     */
    public function getConDireccion(int $id): ?array
    {
        return $this->db->table('organizacion o')
            ->select('o.*, d.estado, d.municipio, d.ciudad')
            ->join('direcciones d', 'd.id_direccion = o.direccion_id', 'left')
            ->where('o.id_organizacion', $id)
            ->get()
            ->getRowArray();
    }
}

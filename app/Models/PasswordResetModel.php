<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table            = 'password_resets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id_usuario',
        'token_hash',
        'expires_at',
        'used_at',
        'created_at',
        'ip_address',
        'user_agent',
    ];

    protected $useTimestamps = false;

    public function invalidarTokensActivos(int $usuarioId): void
    {
        $this->where('id_usuario', $usuarioId)
            ->where('used_at', null)
            ->set(['used_at' => date('Y-m-d H:i:s')])
            ->update();
    }

    public function buscarTokenValido(string $tokenHash): ?array
    {
        return $this->where('token_hash', $tokenHash)
            ->where('used_at', null)
            ->where('expires_at >=', date('Y-m-d H:i:s'))
            ->first();
    }
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table      = 'password_resets';
    protected $primaryKey = 'id';

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;

    protected $allowedFields = [
        'user_id',
        'token_hash',
        'expires_at',
    ];

    public function createTokenForUser(int $userId): array
    {
        // Token real (se enviará por correo)
        $token = bin2hex(random_bytes(32));

        // Hash para guardar en la BD
        $tokenHash = password_hash($token, PASSWORD_DEFAULT);

        $expiresAt = date('Y-m-d H:i:s', time() + 3600); // 1 hora expira el token

        $id = $this->insert([
            'user_id'    => $userId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
        ]);

        return [
            'id'    => $id,
            'token' => $token,
        ];
    }

    public function verifyToken(int $id, string $token): ?array
    {
        $reset = $this->find($id);

        if (!$reset) {
            return null;
        }

        // ¿Expiró?
        if (strtotime($reset['expires_at']) < time()) {
            return null;
        }

        // ¿Coincide el hash?
        if (!password_verify($token, $reset['token_hash'])) {
            return null;
        }

        return $reset;
    }
}

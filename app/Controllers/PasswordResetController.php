<?php

namespace App\Controllers;

use App\Models\PasswordResetModel;
use App\Models\UsuarioModel;
use CodeIgniter\HTTP\ResponseInterface;

class PasswordResetController extends BaseController
{
    private const TOKEN_TTL_MINUTES = 30;
    private const MENSAJE_SOLICITUD = 'Si el correo está registrado, recibirás instrucciones para restablecer tu contraseña.';

    public function solicitar(): string
    {
        return view('auth/recuperar_password');
    }

    public function enviar(): ResponseInterface
    {
        $email = strtolower(trim((string) $this->request->getPost('email')));

        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->withInput()->with('success', self::MENSAJE_SOLICITUD);
        }

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->where('email', $email)->where('status_usu', 1)->first();

        if ($usuario) {
            $this->crearYEnviarToken($usuario);
        }

        return redirect()->back()->with('success', self::MENSAJE_SOLICITUD);
    }

    public function form(string $token): string|ResponseInterface
    {
        $reset = $this->buscarResetValido($token);

        if (! $reset) {
            return redirect()->to(site_url('recuperar-password'))
                ->with('error', 'El enlace para restablecer la contraseña no es válido o expiró.');
        }

        return view('auth/reset_password', [
            'token' => $token,
        ]);
    }

    public function actualizar(): ResponseInterface
    {
        $token = (string) $this->request->getPost('token');
        $password = (string) $this->request->getPost('password');
        $confirmarPassword = (string) $this->request->getPost('confirmar_password');

        $reset = $this->buscarResetValido($token);

        if (! $reset) {
            return redirect()->to(site_url('recuperar-password'))
                ->with('error', 'El enlace para restablecer la contraseña no es válido o expiró.');
        }

        if (strlen($password) < 8) {
            return redirect()->back()->withInput()->with('error', 'La contraseña debe tener al menos 8 caracteres.');
        }

        if ($password !== $confirmarPassword) {
            return redirect()->back()->withInput()->with('error', 'Las contraseñas no coinciden.');
        }

        $usuarioModel = new UsuarioModel();
        $resetModel = new PasswordResetModel();

        $usuarioModel->update((int) $reset['id_usuario'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
        ]);

        $resetModel->update((int) $reset['id'], [
            'used_at' => date('Y-m-d H:i:s'),
        ]);

        $resetModel->invalidarTokensActivos((int) $reset['id_usuario']);
        session()->destroy();

        return redirect()->to(site_url('login'))
            ->with('success', 'Tu contraseña fue actualizada correctamente. Inicia sesión con tu nueva contraseña.');
    }

    private function crearYEnviarToken(array $usuario): void
    {
        $resetModel = new PasswordResetModel();
        $usuarioId = (int) $usuario['id_usuario'];

        $resetModel->invalidarTokensActivos($usuarioId);

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + (self::TOKEN_TTL_MINUTES * 60));

        $resetModel->insert([
            'id_usuario' => $usuarioId,
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt,
            'used_at'    => null,
            'created_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress(),
            'user_agent' => substr((string) $this->request->getUserAgent(), 0, 255),
        ]);

        $link = site_url('reset-password/' . $token);
        $nombre = trim(($usuario['nombres'] ?? '') . ' ' . ($usuario['apellidos'] ?? '')) ?: 'usuario';

        $email = service('email');
        $email->setTo($usuario['email']);
        $email->setSubject('Restablecer contraseña - Digisalud');
        $email->setMessage(view('emails/reset_password', [
            'nombre' => $nombre,
            'link' => $link,
            'minutos' => self::TOKEN_TTL_MINUTES,
        ]));

        if (! $email->send()) {
            log_message('error', 'No se pudo enviar correo de recuperación a usuario ID ' . $usuarioId . ': ' . print_r($email->printDebugger(['headers']), true));
        }
    }

    private function buscarResetValido(string $token): ?array
    {
        if (! preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }

        $resetModel = new PasswordResetModel();
        return $resetModel->buscarTokenValido(hash('sha256', $token));
    }
}

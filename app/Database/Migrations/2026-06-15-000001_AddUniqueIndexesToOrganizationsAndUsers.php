<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use RuntimeException;

class AddUniqueIndexesToOrganizationsAndUsers extends Migration
{
    public function up(): void
    {
        $this->asegurarSinDuplicados('organizacion', 'email', 'id_organizacion');
        $this->asegurarSinDuplicados('usuarios', 'email', 'id_usuario');
        $this->asegurarSinDuplicados('usuarios', 'username', 'id_usuario');

        $this->db->query('ALTER TABLE `organizacion` ADD UNIQUE INDEX `ux_organizacion_email` (`email`)');
        $this->db->query('ALTER TABLE `usuarios` ADD UNIQUE INDEX `ux_usuarios_email` (`email`)');
        $this->db->query('ALTER TABLE `usuarios` ADD UNIQUE INDEX `ux_usuarios_username` (`username`)');
    }

    public function down(): void
    {
        $this->db->query('ALTER TABLE `usuarios` DROP INDEX `ux_usuarios_username`');
        $this->db->query('ALTER TABLE `usuarios` DROP INDEX `ux_usuarios_email`');
        $this->db->query('ALTER TABLE `organizacion` DROP INDEX `ux_organizacion_email`');
    }

    private function asegurarSinDuplicados(string $tabla, string $columna, string $idColumna): void
    {
        $sql = sprintf(
            'SELECT LOWER(TRIM(`%2$s`)) AS valor, GROUP_CONCAT(`%3$s` ORDER BY `%3$s`) AS ids, COUNT(*) AS total
             FROM `%1$s`
             WHERE `%2$s` IS NOT NULL AND TRIM(`%2$s`) <> ""
             GROUP BY LOWER(TRIM(`%2$s`))
             HAVING COUNT(*) > 1
             LIMIT 5',
            $tabla,
            $columna,
            $idColumna
        );

        $duplicados = $this->db->query($sql)->getResultArray();

        if ($duplicados === []) {
            return;
        }

        $detalle = array_map(
            static fn (array $row): string => sprintf('%s=%s ids=[%s]', $columna, $row['valor'], $row['ids']),
            $duplicados
        );

        throw new RuntimeException(
            sprintf(
                'No se pueden crear índices únicos porque existen duplicados en %s.%s: %s',
                $tabla,
                $columna,
                implode('; ', $detalle)
            )
        );
    }
}

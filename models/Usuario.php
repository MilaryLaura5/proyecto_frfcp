<?php

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    public static function validar($usuario, $contraseña)
    {
        global $pdo;

        $sql = "SELECT 
                u.id_usuario, 
                u.usuario, 
                u.rol, 
                u.estado,
                u.contraseña as hash_contraseña,
                CASE 
                    WHEN u.rol = 'Administrador' THEN a.nombre
                    WHEN u.rol = 'Jurado' THEN c.nombre  -- Nombre del criterio asignado
                    WHEN u.rol = 'Presidente' THEN p.nombre
                    ELSE u.usuario
                END AS nombre
            FROM Usuario u
            LEFT JOIN Administrador a ON u.id_usuario = a.id_admin
            LEFT JOIN Jurado j ON u.id_usuario = j.id_jurado
            LEFT JOIN Presidente p ON u.id_usuario = p.id_presidente
            LEFT JOIN JuradoCriterioConcurso jcc ON j.id_jurado = jcc.id_jurado
            LEFT JOIN CriterioConcurso cc ON jcc.id_criterio_concurso = cc.id_criterio_concurso
            LEFT JOIN Criterio c ON cc.id_criterio = c.id_criterio
            WHERE u.usuario = ?
            LIMIT 1";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario && $usuario['estado'] == 1 && password_verify($contraseña, $usuario['hash_contraseña'])) {
            return $usuario;
        }
        return false;
    }
}

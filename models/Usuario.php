<?php

require_once __DIR__ . '/../config/database.php';

class Usuario {
    
    // Validar credenciales del usuario
    public static function validar($correo, $contraseña) {
        global $pdo;
        
        $sql = "SELECT 
                    u.id_usuario, 
                    u.correo, 
                    u.rol, 
                    u.estado,
                    u.contraseña as hash_contraseña,
                    CASE 
                        WHEN u.rol = 'Administrador' THEN a.nombre
                        WHEN u.rol = 'Jurado' THEN j.especialidad
                        WHEN u.rol = 'Presidente' THEN p.nombre
                        ELSE 'Sin nombre'
                    END AS nombre
                FROM Usuario u
                LEFT JOIN Administrador a ON u.id_usuario = a.id_admin
                LEFT JOIN Jurado j ON u.id_usuario = j.id_jurado
                LEFT JOIN Presidente p ON u.id_usuario = p.id_presidente
                WHERE u.correo = ?";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([$correo]);
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verificar si existe y si la contraseña es correcta
        if ($usuario && $usuario['estado'] == 1 && password_verify($contraseña, $usuario['hash_contraseña'])) {
            return $usuario;
        }
        return false;
    }
}
?>
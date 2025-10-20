<?php
// models/Jurado.php
require_once __DIR__ . '/../config/database.php';

class Jurado
{


    public static function crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contrasena)
    {
        global $pdo;

        try {
            // Primero crear en Usuario
            $sql_usuario = "INSERT INTO Usuario (usuario, contraseña, rol, estado) VALUES (?, ?, 'Jurado', 1)";
            $stmt_usuario = $pdo->prepare($sql_usuario);
            $stmt_usuario->execute([$usuario, password_hash($contrasena, PASSWORD_DEFAULT)]);

            $id_usuario = $pdo->lastInsertId();

            // Luego crear en Jurado SIN especialidad
            $sql_jurado = "INSERT INTO Jurado (dni, nombre, años_experiencia, id_jurado) VALUES (?, ?, ?, ?)";
            $stmt_jurado = $pdo->prepare($sql_jurado);

            if (!$stmt_jurado->execute([$dni, $nombre, $años_experiencia, $id_usuario])) {
                // Si falla, elimina el usuario creado
                $pdo->rollback();
                return false;
            }

            return true;
        } catch (Exception $e) {
            error_log("Error al crear jurado: " . $e->getMessage());
            return false;
        }
    }
    public static function listar()
    {
        global $pdo;
        $sql = "SELECT j.*, u.usuario, u.estado 
            FROM Jurado j
            JOIN Usuario u ON j.id_jurado = u.id_usuario
            ORDER BY j.dni";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function porConcurso($id_concurso)
    {
        global $pdo;
        $sql = "SELECT j.*, u.usuario, t.token, t.fecha_expiracion
                FROM Jurado j
                JOIN Usuario u ON j.id_jurado = u.id_usuario
                JOIN TokenAcceso t ON j.id_jurado = t.id_jurado
                WHERE t.id_concurso = ?
                ORDER BY j.dni";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function porDNI($dni)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT j.*, u.usuario 
            FROM Jurado j 
            JOIN Usuario u ON j.id_jurado = u.id_usuario 
            WHERE j.dni = ?
        ");
        $stmt->execute([$dni]);
        return $stmt->fetch();
    }

    public static function existeDNI($dni)
    {
        return self::porDNI($dni) !== false;
    }
}

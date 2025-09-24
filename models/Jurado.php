<?php
// models/Jurado.php
require_once __DIR__ . '/../config/database.php';

class Jurado
{
    public static function listar()
    {
        global $pdo;
        $sql = "SELECT j.*, u.usuario, u.estado 
                FROM Jurado j
                JOIN Usuario u ON j.id_jurado = u.id_jurado
                ORDER BY j.especialidad, j.dni";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contraseña)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Insertar en Jurado (id_jurado es AUTO_INCREMENT)
            $sql_jurado = "INSERT INTO Jurado (dni, nombre, especialidad, años_experiencia) 
                           VALUES (?, ?, ?, ?)";
            $stmt_jurado = $pdo->prepare($sql_jurado);
            $stmt_jurado->execute([$dni, $nombre, $especialidad, $años_experiencia]);
            $id_jurado = $pdo->lastInsertId();

            // Insertar en Usuario con id_jurado
            $hash = password_hash($contraseña, PASSWORD_DEFAULT);
            $sql_usuario = "INSERT INTO Usuario (usuario, contraseña, rol, id_jurado) 
                            VALUES (?, ?, 'Jurado', ?)";
            $stmt_usuario = $pdo->prepare($sql_usuario);
            $stmt_usuario->execute([$usuario, $hash, $id_jurado]);

            $pdo->commit();
            return $id_jurado; // Éxito: devuelve el ID del jurado
        } catch (Exception $e) {
            $pdo->rollback();
            error_log("Error en Jurado::crear: " . $e->getMessage());
            return false;
        }
    }

    public static function porConcurso($id_concurso)
    {
        global $pdo;
        $sql = "SELECT j.*, u.usuario, t.token, t.usado, t.fecha_expiracion
                FROM Jurado j
                JOIN Usuario u ON j.id_jurado = u.id_jurado
                JOIN TokenAcceso t ON j.id_jurado = t.id_jurado
                WHERE t.id_concurso = ?
                ORDER BY j.especialidad, u.usuario";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

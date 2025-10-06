<?php
// models/Jurado.php
require_once __DIR__ . '/../config/database.php';

class Jurado
{


    public static function crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contraseña)
    {
        global $pdo;

        try {
            $pdo->beginTransaction();

            // Verificar si el usuario ya existe
            $check = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $check->execute([$usuario]);
            if ($check->rowCount() > 0) {
                throw new Exception("Usuario ya registrado");
            }

            // Insertar en Usuario → usa 'usuario', no 'correo'
            $hash = password_hash($contraseña, PASSWORD_DEFAULT);
            $sql_user = "INSERT INTO Usuario (usuario, contraseña, rol, estado) VALUES (?, ?, 'Jurado', 1)";
            $stmt_user = $pdo->prepare($sql_user);
            $stmt_user->execute([$usuario, $hash]);
            $id_usuario = $pdo->lastInsertId();

            // Insertar en Jurado
            $sql_jurado = "INSERT INTO Jurado (id_jurado, dni, especialidad, años_experiencia) 
                       VALUES (?, ?, ?, ?)";
            $stmt_jurado = $pdo->prepare($sql_jurado);
            $stmt_jurado->execute([$id_usuario, $dni, $especialidad, $años_experiencia]);

            $pdo->commit();
            return true;
        } catch (Exception $e) {
            $pdo->rollback();
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
                ORDER BY j.especialidad, j.dni";
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
                ORDER BY j.especialidad, u.usuario";
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

<?php
// models/TipoDanza.php
require_once __DIR__ . '/../config/database.php';

class TipoDanza
{
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tipodanza ORDER BY nombre_tipo");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM tipodanza WHERE id_tipo = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function crear($nombre)
    {
        global $pdo;
        $sql = "INSERT INTO TipoDanza (nombre_tipo) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre]);
    }
    public static function actualizar($id, $nombre)
    {
        global $pdo;
        $sql = "UPDATE TipoDanza SET nombre_tipo = ? WHERE id_tipo = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $id]);
    }

    public static function eliminar($id)
    {
        global $pdo;
        // Verificar si hay series asociadas
        $check = $pdo->prepare("SELECT COUNT(*) FROM Serie WHERE id_tipo = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false; // No se puede eliminar
        }
        $sql = "DELETE FROM TipoDanza WHERE id_tipo = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    public static function tieneSeries($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Serie WHERE id_tipo = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
    public static function existeNombre($nombre)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM TipoDanza WHERE LOWER(nombre_tipo) = LOWER(?)");
        $stmt->execute([trim($nombre)]);
        return $stmt->fetchColumn() > 0;
    }
}

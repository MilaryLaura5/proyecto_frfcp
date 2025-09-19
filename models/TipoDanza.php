<?php
// models/TipoDanza.php
require_once __DIR__ . '/../config/database.php';

class TipoDanza
{
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM TipoDanza ORDER BY nombre_tipo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($nombre_tipo)
    {
        global $pdo;
        $sql = "INSERT INTO TipoDanza (nombre_tipo) VALUES (?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre_tipo]);
    }

    public static function actualizar($id, $nombre_tipo)
    {
        global $pdo;
        $sql = "UPDATE TipoDanza SET nombre_tipo = ? WHERE id_tipo = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre_tipo, $id]);
    }

    public static function eliminar($id)
    {
        global $pdo;
        // Verificar si tiene series asociadas
        $check = $pdo->prepare("SELECT COUNT(*) FROM Serie WHERE id_tipo = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false; // No se puede eliminar
        }
        $sql = "DELETE FROM TipoDanza WHERE id_tipo = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}

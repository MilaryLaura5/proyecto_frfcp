<?php
class Criterio
{
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Criterio ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function porId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Criterio WHERE id_criterio = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function existe($nombre)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Criterio WHERE LOWER(nombre) = LOWER(?)");
        $stmt->execute([trim($nombre)]);
        return $stmt->fetchColumn() > 0;
    }

    public static function crear($nombre)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT IGNORE INTO Criterio (nombre) VALUES (?)");
        return $stmt->execute([$nombre]);
    }

    public static function todos()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Criterio ORDER BY nombre");
        return $stmt->fetchAll();
    }
}

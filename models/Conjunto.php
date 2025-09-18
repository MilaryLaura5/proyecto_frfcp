<?php
// models/Conjunto.php
require_once __DIR__ . '/../config/database.php';

class Conjunto
{
    public static function listarPorConcurso($id_concurso)
    {
        global $pdo;
        $sql = "SELECT c.*, s.nombre_serie, td.nombre_tipo 
            FROM Conjunto c
            JOIN Serie s ON c.id_serie = s.id_serie
            JOIN TipoDanza td ON s.id_tipo = td.id_tipo
            WHERE c.id_concurso = ?
            ORDER BY c.orden_presentacion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function crear($nombre, $id_serie, $id_concurso, $orden_presentacion, $numero_oficial)
    {
        global $pdo;
        $sql = "INSERT INTO Conjunto (nombre, id_serie, id_concurso, estado_activo, orden_presentacion, numero_oficial) 
            VALUES (?, ?, ?, 1, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $id_serie, $id_concurso, $orden_presentacion, $numero_oficial]);
    }

    public static function obtenerPorId($id)
    {
        global $pdo;
        $sql = "SELECT * FROM Conjunto WHERE id_conjunto = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function editar($id, $nombre, $id_serie, $orden_presentacion, $numero_oficial)
    {
        global $pdo;
        $sql = "UPDATE Conjunto SET nombre = ?, id_serie = ?, orden_presentacion = ?, numero_oficial = ? 
            WHERE id_conjunto = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $id_serie, $orden_presentacion, $numero_oficial, $id]);
    }

    public static function eliminar($id)
    {
        global $pdo;
        // Verificar si ya fue calificado
        $check = $pdo->prepare("SELECT COUNT(*) FROM Calificacion WHERE id_conjunto = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false; // No se puede eliminar
        }
        $sql = "DELETE FROM Conjunto WHERE id_conjunto = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function listarPorSerie($id_serie)
    {
        global $pdo;
        $sql = "SELECT c.* FROM Conjunto c WHERE c.id_serie = ? ORDER BY c.orden_presentacion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_serie]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
// models/Concurso.php
require_once __DIR__ . '/../config/database.php';

class Concurso {
    
    public static function crear($nombre, $fecha_inicio, $fecha_fin) {
        global $pdo;
        $sql = "INSERT INTO Concurso (nombre, fecha_inicio, fecha_fin, estado) 
                VALUES (?, ?, ?, 'Pendiente')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $fecha_inicio, $fecha_fin]);
    }

    public static function listar() {
        global $pdo;
        $sql = "SELECT * FROM Concurso ORDER BY fecha_inicio DESC";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id) {
        global $pdo;
        $sql = "SELECT * FROM Concurso WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function editar($id, $nombre, $fecha_inicio, $fecha_fin) {
        global $pdo;
        $sql = "UPDATE Concurso SET nombre = ?, fecha_inicio = ?, fecha_fin = ?, estado = 'Pendiente' 
                WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $fecha_inicio, $fecha_fin, $id]);
    }

    public static function eliminar($id) {
        global $pdo;
        // Primero verifica que no tenga calificaciones asociadas
        $check = $pdo->prepare("SELECT COUNT(*) FROM Calificacion WHERE id_concurso = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            return false; // No se puede eliminar si ya hay evaluaciones
        }
        $sql = "DELETE FROM Concurso WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
}
?>
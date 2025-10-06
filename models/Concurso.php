<?php
// models/Concurso.php
require_once __DIR__ . '/../config/database.php';

class Concurso
{

    public static function crear($nombre, $fecha_inicio, $fecha_fin)
    {
        global $pdo;
        $sql = "INSERT INTO Concurso (nombre, fecha_inicio, fecha_fin, estado) 
                VALUES (?, ?, ?, 'Pendiente')";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $fecha_inicio, $fecha_fin]);
    }

    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Concurso ORDER BY fecha_inicio DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public static function editar($id, $nombre, $fecha_inicio, $fecha_fin)
    {
        global $pdo;
        $sql = "UPDATE Concurso SET nombre = ?, fecha_inicio = ?, fecha_fin = ?, estado = 'Pendiente' 
                WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $fecha_inicio, $fecha_fin, $id]);
    }

    public static function eliminar($id)
    {
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

    public static function activar($id)
    {
        global $pdo;
        $sql = "UPDATE Concurso SET estado = 'Activo' WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    public static function cerrar($id)
    {
        global $pdo;
        $sql = "UPDATE Concurso SET estado = 'Cerrado' WHERE id_concurso = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    public static function todos()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM Concurso ORDER BY nombre");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function tieneEvaluaciones($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Calificacion WHERE id_concurso = ?");
        $stmt->execute([$id]);
        return $stmt->fetchColumn() > 0;
    }
}

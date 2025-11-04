<?php
class Conjunto
{
    // Listar todos los conjuntos activos
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT c.*, s.numero_serie, s.nombre_serie, td.nombre_tipo 
        FROM Conjunto c
        JOIN Serie s ON c.id_serie = s.id_serie
        JOIN tipodanza td ON s.id_tipo = td.id_tipo   -- ✅ minúsculas
        WHERE c.estado_activo = 1
        ORDER BY c.nombre
    ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($nombre, $id_serie)
    {
        global $pdo;

        // Verificar si ya existe un conjunto con ese nombre Y serie (ignorando mayúsculas)
        $check = $pdo->prepare("
        SELECT COUNT(*) 
        FROM Conjunto 
        WHERE LOWER(nombre) = LOWER(?) AND id_serie = ?
    ");
        $check->execute([$nombre, $id_serie]);

        if ($check->fetchColumn() > 0) {
            return false; // Ya existe
        }

        // Si no existe, proceder con la inserción
        $sql = "INSERT INTO Conjunto (nombre, id_serie) VALUES (?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $id_serie]);
    }

    // Editar conjunto (incluyendo serie)
    public static function editar($id, $nombre, $id_serie)
    {
        global $pdo;
        $sql = "UPDATE Conjunto SET nombre = ?, id_serie = ? WHERE id_conjunto = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $id_serie, $id]);
    }

    // Verificar si fue evaluado
    public static function fueEvaluado($id_conjunto)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM Calificacion c 
                               JOIN ParticipacionConjunto pc ON c.id_participacion = pc.id_participacion
                               WHERE pc.id_conjunto = ?");
        $stmt->execute([$id_conjunto]);
        return $stmt->fetchColumn() > 0;
    }

    // Eliminar conjunto (solo si no fue evaluado)
    public static function eliminar($id_conjunto)
    {
        if (self::fueEvaluado($id_conjunto)) {
            return false;
        }
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM Conjunto WHERE id_conjunto = ?");
        return $stmt->execute([$id_conjunto]);
    }

    // Obtener conjunto por ID
    // Obtener un conjunto por su ID (con datos de serie y tipo)
    public static function obtenerPorId($id)
    {
        global $pdo;
        $sql = "
        SELECT c.*, 
               s.numero_serie,
               s.nombre_serie,
               td.nombre_tipo 
        FROM Conjunto c
        JOIN Serie s ON c.id_serie = s.id_serie
        JOIN tipodanza td ON s.id_tipo = td.id_tipo   -- ✅ minúsculas
        WHERE c.id_conjunto = ?
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

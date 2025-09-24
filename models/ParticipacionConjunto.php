<?php
class ParticipacionConjunto
{
    public static function listarPorConcurso($id_concurso)
    {
        global $pdo;
        $sql = "SELECT pc.*, c.nombre as nombre_conjunto, s.nombre_serie, td.nombre_tipo 
                FROM ParticipacionConjunto pc
                JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
                JOIN Serie s ON c.id_serie = s.id_serie
                JOIN TipoDanza td ON s.id_tipo = td.id_tipo
                WHERE pc.id_concurso = ?
                ORDER BY pc.orden_presentacion";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function agregar($id_conjunto, $id_concurso, $orden_presentacion)
    {
        global $pdo;
        $sql = "INSERT INTO ParticipacionConjunto (id_conjunto, id_concurso, orden_presentacion)
                VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_conjunto, $id_concurso, $orden_presentacion]);
    }

    public static function eliminar($id_participacion)
    {
        global $pdo;
        $sql = "DELETE FROM ParticipacionConjunto WHERE id_participacion = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id_participacion]);
    }
}

<?php
class ParticipacionConjunto
{
    public static function listarPorConcurso($id_concurso, $id_jurado)
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT 
            pc.id_participacion,
            pc.orden_presentacion,
            c.nombre AS nombre_conjunto,
            s.numero_serie,
            COALESCE(cal.estado, 'pendiente') AS estado_calificacion
        FROM ParticipacionConjunto pc
        JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
        LEFT JOIN Serie s ON c.id_serie = s.id_serie
        LEFT JOIN Calificacion cal ON pc.id_participacion = cal.id_participacion 
            AND cal.id_jurado = ?
        WHERE pc.id_concurso = ?
        ORDER BY pc.orden_presentacion
    ");
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function yaAsignado($id_conjunto, $id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM ParticipacionConjunto WHERE id_conjunto = ? AND id_concurso = ?");
        $stmt->execute([$id_conjunto, $id_concurso]);
        return $stmt->fetchColumn() > 0;
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
    public static function obtenerPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT 
                pc.id_participacion, 
                pc.orden_presentacion, 
                c.nombre AS nombre_conjunto, 
                s.numero_serie
            FROM participacionconjunto pc
            JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
            JOIN serie s ON c.id_serie = s.id_serie
            WHERE pc.id_participacion = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function porConcurso($id_concurso, $id_jurado = null)
    {
        global $pdo;
        $sql = "SELECT 
            p.id_participacion,
            p.orden_presentacion,
            c.nombre AS nombre_conjunto,
            s.numero_serie,
            COALESCE(cal.estado, 'pendiente') AS estado_calificacion
        FROM ParticipacionConjunto p
        JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
        JOIN Serie s ON c.id_serie = s.id_serie
        LEFT JOIN Calificacion cal ON p.id_participacion = cal.id_participacion AND cal.id_jurado = ?
        WHERE p.id_concurso = ?
        ORDER BY p.orden_presentacion
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function porConcursoConDetalles($id_concurso, $id_jurado)
    {
        global $pdo;
        $sql = "SELECT 
            p.id_participacion,
            p.orden_presentacion,
            c.nombre AS nombre_conjunto,
            s.numero_serie,
            COALESCE(cal.estado, 'pendiente') AS estado_calificacion,
            dc.puntaje,
            cr.nombre AS nombre_criterio
        FROM ParticipacionConjunto p
        JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
        JOIN Serie s ON c.id_serie = s.id_serie
        LEFT JOIN Calificacion cal ON p.id_participacion = cal.id_participacion AND cal.id_jurado = ?
        LEFT JOIN detallecalificacion dc ON cal.id_calificacion = dc.id_calificacion
        LEFT JOIN CriterioConcurso cc ON dc.id_criterio_concurso = cc.id_criterio_concurso
        LEFT JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
        WHERE p.id_concurso = ?
        ORDER BY p.orden_presentacion, cr.nombre
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_jurado, $id_concurso]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Agrupar resultados por conjunto
        $conjuntos = [];
        foreach ($resultados as $row) {
            $id = $row['id_participacion'];
            if (!isset($conjuntos[$id])) {
                $conjuntos[$id] = [
                    'id_participacion' => $id,
                    'orden_presentacion' => $row['orden_presentacion'],
                    'nombre_conjunto' => $row['nombre_conjunto'],
                    'numero_serie' => $row['numero_serie'],
                    'estado_calificacion' => $row['estado_calificacion'],
                    'detalles' => []
                ];
            }
            if ($row['puntaje'] !== null) {
                $conjuntos[$id]['detalles'][] = [
                    'nombre_criterio' => $row['nombre_criterio'],
                    'puntaje' => $row['puntaje']
                ];
            }
        }

        return array_values($conjuntos);
    }
    public static function porConcursoConEstado($id_concurso, $id_jurado)
    {
        global $pdo;
        $sql = "
        SELECT 
            p.id_participacion,
            p.orden_presentacion,
            c.nombre AS nombre_conjunto,
            s.numero_serie,
            COALESCE(cal.estado, 'pendiente') AS estado_calificacion
        FROM ParticipacionConjunto p
        JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
        JOIN Serie s ON c.id_serie = s.id_serie
        LEFT JOIN Calificacion cal ON p.id_participacion = cal.id_participacion AND cal.id_jurado = ?
        WHERE p.id_concurso = ?
        ORDER BY p.orden_presentacion
    ";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function listarPorConcursoAdmin($id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT 
            pc.id_participacion,
            pc.orden_presentacion,
            c.nombre AS nombre_conjunto,
            s.numero_serie,
            s.nombre_serie,          -- ✅ EXISTE en tu BD
            td.nombre_tipo AS nombre_tipo
        FROM ParticipacionConjunto pc
        JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
        LEFT JOIN Serie s ON c.id_serie = s.id_serie
        LEFT JOIN TipoDanza td ON s.id_tipo = td.id_tipo
        WHERE pc.id_concurso = ?
        ORDER BY pc.orden_presentacion
    ");
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

<?php
// models/Presidente.php - Modelo del Presidente

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';

class Presidente
{
    private $pdo;

    public function __construct($db = null)
    {
        $this->pdo = $db ?? $GLOBALS['pdo'];
    }

    // Obtener presidente por ID
    public function getById($id_presidente)
    {
        $sql = "SELECT p.*, u.usuario 
                FROM Presidente p
                JOIN Usuario u ON p.id_presidente = u.id_usuario
                WHERE p.id_presidente = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_presidente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los concursos activos o cerrados
    public function getAllConcursos()
    {
        $sql = "SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado
                FROM Concurso
                ORDER BY fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener resultados finales por concurso
    public function getResultadosFinales($id_concurso)
    {
        $sql = "
        SELECT 
            c.nombre AS conjunto,
            pc.orden_presentacion,
            s.numero_serie,
            td.nombre_tipo AS tipo_danza,
            COALESCE(SUM(dc.puntaje), 0) AS puntaje_total,
            cal.estado
        FROM ParticipacionConjunto pc
        JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
        JOIN Serie s ON c.id_serie = s.id_serie
        JOIN TipoDanza td ON s.id_tipo = td.id_tipo
        LEFT JOIN Calificacion cal ON pc.id_participacion = cal.id_participacion
        LEFT JOIN detallecalificacion dc ON cal.id_calificacion = dc.id_calificacion
        WHERE pc.id_concurso = ? 
          AND (cal.estado IS NOT NULL) -- Incluye 'enviado', 'calificado', 'descalificado'
        GROUP BY pc.id_participacion, c.id_conjunto, cal.estado
        ORDER BY 
            CASE WHEN cal.estado = 'descalificado' THEN 1 ELSE 0 END ASC,
            puntaje_total DESC
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener criterios usados en un concurso
    public function getCriteriosByConcurso($id_concurso)
    {
        $sql = "
            SELECT cr.nombre AS nombre_criterio, cc.puntaje_maximo
            FROM CriterioConcurso cc
            JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
            WHERE cc.id_concurso = ?
            ORDER BY cr.nombre
        ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener resultados por serie
    public function getResultadosPorSerie($id_concurso)
    {
        $sql = "
        SELECT 
            s.id_serie,
            s.numero_serie,
            td.nombre_tipo,
            c.nombre AS conjunto,
            pc.orden_presentacion,
            COALESCE(SUM(dc.puntaje), 0) AS puntaje_total,
            cal.estado
        FROM ParticipacionConjunto pc
        JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
        JOIN Serie s ON c.id_serie = s.id_serie
        JOIN TipoDanza td ON s.id_tipo = td.id_tipo
        LEFT JOIN Calificacion cal ON pc.id_participacion = cal.id_participacion
        LEFT JOIN detallecalificacion dc ON cal.id_calificacion = dc.id_calificacion
        WHERE pc.id_concurso = ? AND cal.estado IS NOT NULL
        GROUP BY s.id_serie, pc.id_participacion, c.id_conjunto, cal.estado
        ORDER BY s.numero_serie, puntaje_total DESC
    ";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

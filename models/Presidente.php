<?php
// models/Presidente.php - VERSIÓN DEFINITIVA PARA TU ESTRUCTURA

class Presidente
{
    private $pdo;

    public function __construct($db)
    {
        $this->pdo = $db;
    }

    // Obtener todos los concursos
    public function getAllConcursos()
    {
        $sql = "SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado 
                FROM Concurso 
                ORDER BY fecha_inicio DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    // Obtener resultados finales - CONSULTA CORRECTA
    public function getResultadosFinales($id_concurso)
    {
        $sql = "SELECT 
                    c.id_conjunto,
                    c.nombre AS conjunto,
                    s.nombre_serie AS categoria,
                    td.nombre_tipo AS tipo_danza,
                    pc.orden_presentacion,
                    pc.id_participacion,
                    COALESCE(ROUND(AVG(dc.puntaje), 2), 0) AS promedio_final,
                    COUNT(DISTINCT ca.id_calificacion) AS calificaciones_count
                FROM participacionconjunto pc
                JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
                JOIN serie s ON c.id_serie = s.id_serie
                JOIN tipodanza td ON s.id_tipo = td.id_tipo
                LEFT JOIN calificacion ca ON pc.id_participacion = ca.id_participacion AND ca.estado = 'enviado'
                LEFT JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
                WHERE pc.id_concurso = ?
                GROUP BY c.id_conjunto, c.nombre, s.nombre_serie, td.nombre_tipo, pc.orden_presentacion, pc.id_participacion
                ORDER BY promedio_final DESC, pc.orden_presentacion ASC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // LÓGICA MEJORADA PARA EMPATES
        $posicion = 1;
        $puntaje_anterior = null;
        $posicion_real = 0;

        foreach ($resultados as &$resultado) {
            // Si el puntaje es diferente al anterior, avanzamos posición
            if ($puntaje_anterior === null || $resultado['promedio_final'] != $puntaje_anterior) {
                $posicion_real = $posicion;
            }

            $resultado['posicion'] = $posicion_real;
            $puntaje_anterior = $resultado['promedio_final'];
            $posicion++;
        }

        return $resultados;
    }

    // Obtener criterios del concurso
    public function getCriteriosByConcurso($id_concurso)
    {
        $sql = "SELECT cr.nombre, cc.puntaje_maximo
                FROM criterioconcurso cc
                JOIN criterio cr ON cc.id_criterio = cr.id_criterio
                WHERE cc.id_concurso = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si tiene conjuntos participantes
    public function tieneConjuntosParticipantes($id_concurso)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM participacionconjunto 
                WHERE id_concurso = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }

    // Verificar si tiene calificaciones
    public function tieneCalificaciones($id_concurso)
    {
        $sql = "SELECT COUNT(*) as total 
                FROM calificacion 
                WHERE id_concurso = ? AND estado = 'enviado'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] > 0;
    }
}

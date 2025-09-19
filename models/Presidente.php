<?php
// models/Presidente.php

// ✅ Ruta corregida: con barra / después de __DIR__
require_once __DIR__ . '/../config/database.php';

class Presidente {
    private $pdo;

    public function __construct($db) {
        $this->pdo = $db;
    }

    // Obtener presidente por ID
    public function getById($id_presidente) {
        $stmt = $this->pdo->prepare("SELECT p.*, u.correo FROM Presidente p 
                                     JOIN Usuario u ON p.id_presidente = u.id_usuario 
                                     WHERE p.id_presidente = ?");
        $stmt->execute([$id_presidente]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener todos los concursos
    public function getAllConcursos() {
        $stmt = $this->pdo->prepare("SELECT * FROM Concurso ORDER BY fecha_inicio DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener resultados finales por concurso
    public function getResultadosFinales($id_concurso) {
        $sql = "SELECT 
                    c.nombre as conjunto,
                    s.nombre_serie,
                    s.tipo_danza,
                    AVG(dc.puntaje) as promedio_final
                FROM Calificacion ca
                JOIN DetalleCalificacion dc ON ca.id_calificacion = dc.id_calificacion
                JOIN Conjunto c ON ca.id_conjunto = c.id_conjunto
                JOIN Serie s ON c.id_serie = s.id_serie
                WHERE ca.id_concurso = ? AND ca.estado != 'descalificado'
                GROUP BY ca.id_conjunto, s.id_serie
                ORDER BY promedio_final DESC";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener criterios usados en un concurso
    public function getCriteriosByConcurso($id_concurso) {
        $stmt = $this->pdo->prepare("SELECT cr.nombre, cc.peso_actual 
                                     FROM CriterioConcurso cc 
                                     JOIN Criterio cr ON cc.id_criterio = cr.id_criterio 
                                     WHERE cc.id_concurso = ?");
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
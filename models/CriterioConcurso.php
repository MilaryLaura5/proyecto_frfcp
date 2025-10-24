<?php
// models/CriterioConcurso.php
class CriterioConcurso
{
    public static function porConcurso($id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT 
                cc.id_criterio_concurso,
                cr.nombre,
                cc.puntaje_maximo
            FROM CriterioConcurso cc
            JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
            WHERE cc.id_concurso = ?
            ORDER BY cc.puntaje_maximo DESC
        ");
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function disponiblesParaAsignar($id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT cr.*
            FROM Criterio cr
            WHERE cr.id_criterio NOT IN (
                SELECT cc.id_criterio FROM CriterioConcurso cc WHERE cc.id_concurso = ?
            )
            ORDER BY cr.nombre
        ");
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public static function asignar($id_criterio, $id_concurso, $puntaje_maximo)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO CriterioConcurso (id_criterio, id_concurso, puntaje_maximo)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE puntaje_maximo = ?
    ");
        return $stmt->execute([$id_criterio, $id_concurso, $puntaje_maximo, $puntaje_maximo]);
    }
}

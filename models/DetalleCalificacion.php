<?php
// models/DetalleCalificacion.php

class DetalleCalificacion
{
    public static function eliminarPorCalificacion($id_calificacion)
    {
        global $pdo;
        $stmt = $pdo->prepare("DELETE FROM detallecalificacion WHERE id_calificacion = ?");
        return $stmt->execute([$id_calificacion]);
    }

    public static function insertar($id_calificacion, $id_criterio_concurso, $puntaje)
    {
        global $pdo;
        $stmt = $pdo->prepare("INSERT INTO detallecalificacion (id_calificacion, id_criterio_concurso, puntaje)
            VALUES (?, ?, ?)
        ");
        return $stmt->execute([$id_calificacion, $id_criterio_concurso, $puntaje]);
    }
}

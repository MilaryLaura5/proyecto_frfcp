<?php
// models/Calificacion.php

class Calificacion
{
    public static function porJuradoYParticipacion($id_jurado, $id_participacion)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT *
            FROM Calificacion
            WHERE id_jurado = ? AND id_participacion = ?
        ");
        $stmt->execute([$id_jurado, $id_participacion]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function crear($id_jurado, $id_participacion, $estado = 'enviado')
    {
        global $pdo;
        $stmt = $pdo->prepare("
            INSERT INTO Calificacion (id_jurado, id_participacion, estado, fecha_hora)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$id_jurado, $id_participacion, $estado]);
        return $pdo->lastInsertId();
    }

    public static function actualizarEstado($id_calificacion, $estado)
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE Calificacion SET estado = ? WHERE id_calificacion = ?");
        return $stmt->execute([$estado, $id_calificacion]);
    }
}

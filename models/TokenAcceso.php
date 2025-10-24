<?php
// models/Concurso.php
require_once __DIR__ . '/../config/database.php';

class TokenAcceso
{
    public static function obtenerPorJurado($id_jurado)
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT * FROM tokenacceso 
        WHERE id_jurado = ? AND fecha_expiracion > NOW()
        ORDER BY fecha_generacion DESC
        LIMIT 1
    ");
        $stmt->execute([$id_jurado]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

<?php
// models/TipoDanza.php
require_once __DIR__ . '/../config/database.php';

class TipoDanza
{
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->query("SELECT * FROM TipoDanza ORDER BY id_tipo");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

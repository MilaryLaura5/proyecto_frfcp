<?php
// models/Serie.php
require_once __DIR__ . '/../config/database.php';

class Serie
{
    public static function listar()
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT s.*, td.nombre_tipo 
            FROM Serie s 
            JOIN TipoDanza td ON s.id_tipo = td.id_tipo 
            ORDER BY td.nombre_tipo, s.numero_serie
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function crear($numero_serie, $nombre_serie, $id_tipo)
    {
        global $pdo;
        $sql = "INSERT INTO Serie (numero_serie, nombre_serie, id_tipo) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$numero_serie, $nombre_serie, $id_tipo]);
    }

    public static function obtenerPorId($id)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM Serie WHERE id_serie = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function editar($id, $numero_serie, $nombre_serie, $id_tipo)
    {
        global $pdo;
        $sql = "UPDATE Serie SET numero_serie = ?, nombre_serie = ?, id_tipo = ? WHERE id_serie = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$numero_serie, $nombre_serie, $id_tipo, $id]);
    }

    public static function eliminar($id)
    {
        global $pdo;
        $sql = "DELETE FROM Serie WHERE id_serie = ?";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$id]);
    }
    public static function listarPorTipo($id_tipo)
    {
        global $pdo;
        $sql = "SELECT s.*, td.nombre_tipo 
            FROM Serie s
            JOIN TipoDanza td ON s.id_tipo = td.id_tipo
            WHERE s.id_tipo = ?
            ORDER BY s.numero_serie";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarConTipo()
    {
        global $pdo;
        $stmt = $pdo->prepare(" SELECT s.*, td.nombre_tipo 
            FROM Serie s 
            JOIN TipoDanza td ON s.id_tipo = td.id_tipo 
            ORDER BY s.numero_serie
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    /*public static function porTipo($id_tipo)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT * FROM Serie WHERE id_tipo = ? ORDER BY numero_serie
        ");
        $stmt->execute([$id_tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/

    public static function porTipo($id_tipo)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT s.*, c.nombre AS concurso_nombre 
            FROM Serie s
            LEFT JOIN Concurso c ON s.id_concurso = c.id_concurso
            WHERE s.id_tipo = ?
            ORDER BY s.numero_serie
        ");
        $stmt->execute([$id_tipo]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function listarConTipoYConcurso()
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT s.*, 
                   td.nombre_tipo,
                   c.nombre AS concurso_nombre 
            FROM Serie s
            JOIN TipoDanza td ON s.id_tipo = td.id_tipo
            LEFT JOIN Concurso c ON s.id_concurso = c.id_concurso
            ORDER BY td.nombre_tipo, s.numero_serie
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

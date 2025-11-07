<!-- models/JuradoCriterioConcurso.php -->
<?php
class JuradoCriterioConcurso
{
    /**
     * Asigna un jurado a un criterio específico en un concurso
     */
    public static function asignar($id_jurado, $id_criterio_concurso)
    {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                INSERT INTO JuradoCriterioConcurso (id_jurado, id_criterio_concurso) 
                VALUES (?, ?)
                ON DUPLICATE KEY UPDATE id_jurado = id_jurado
            ");
            return $stmt->execute([$id_jurado, $id_criterio_concurso]);
        } catch (Exception $e) {
            error_log("Error al asignar jurado a criterio: " . $e->getMessage());
            return false;
        }
    }
    /**
     * Obtiene el nombre del criterio que califica un jurado en un concurso específico
     */
    public static function getCriterioPorJuradoYConcurso($id_jurado, $id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT c.nombre 
            FROM JuradoCriterioConcurso jcc 
            JOIN CriterioConcurso cc ON jcc.id_criterio_concurso = cc.id_criterio_concurso 
            JOIN Criterio c ON cc.id_criterio = c.id_criterio 
            WHERE jcc.id_jurado = ? AND cc.id_concurso = ?
            LIMIT 1
        ");
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetch(PDO::FETCH_COLUMN); // Devuelve solo el nombre o false
    }

    /**
     * Obtiene todos los jurados con sus criterios por concurso
     */
    /**
     * Obtiene todos los jurados con sus criterios por concurso
     */
    public static function listarPorConcurso($id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT 
            j.dni,
            j.nombre, -- ← Añadido
            u.usuario,
            j.años_experiencia,
            j.id_jurado,
            c.nombre AS criterio_calificado,
            t.token,
            t.fecha_expiracion
        FROM Jurado j
        JOIN Usuario u ON j.id_jurado = u.id_usuario
        LEFT JOIN JuradoCriterioConcurso jcc ON j.id_jurado = jcc.id_jurado
        LEFT JOIN CriterioConcurso cc ON jcc.id_criterio_concurso = cc.id_criterio_concurso
        LEFT JOIN Criterio c ON cc.id_criterio = c.id_criterio
        LEFT JOIN TokenAcceso t ON j.id_jurado = t.id_jurado AND cc.id_concurso = t.id_concurso
        WHERE cc.id_concurso = ?
        ORDER BY j.dni
    ");
        $stmt->execute([$id_concurso]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Verifica si un jurado ya está asignado a un criterio en un concurso
     */
    public static function yaAsignado($id_jurado, $id_criterio_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
            SELECT 1 FROM JuradoCriterioConcurso 
            WHERE id_jurado = ? AND id_criterio_concurso = ?
        ");
        $stmt->execute([$id_jurado, $id_criterio_concurso]);
        return $stmt->fetch() !== false;
    }
    /**
     * Obtiene el criterio completo (con puntaje_maximo) que califica un jurado en un concurso
     */
    public static function getCriterioCompletoPorJuradoYConcurso($id_jurado, $id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
    SELECT 
        cr.nombre AS nombre_criterio,  -- ✅ Añadido alias
        cr.id_criterio,
        cc.puntaje_maximo,
        cc.id_criterio_concurso
    FROM JuradoCriterioConcurso jcc
    JOIN CriterioConcurso cc ON jcc.id_criterio_concurso = cc.id_criterio_concurso
    JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
    WHERE jcc.id_jurado = ? AND cc.id_concurso = ?
    LIMIT 1
");
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public static function getCriterioConcursoPorJuradoYConcurso($id_jurado, $id_concurso)
    {
        global $pdo;
        $stmt = $pdo->prepare("
        SELECT jcc.id_criterio_concurso
        FROM JuradoCriterioConcurso jcc
        JOIN CriterioConcurso cc ON jcc.id_criterio_concurso = cc.id_criterio_concurso
        WHERE jcc.id_jurado = ? AND cc.id_concurso = ?
        LIMIT 1
    ");
        $stmt->execute([$id_jurado, $id_concurso]);
        return $stmt->fetchColumn(); // Devuelve el ID o false
    }
}

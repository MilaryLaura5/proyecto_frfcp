<?php
// controllers/JuradoController.php

// ✅ Incluir autenticación y funciones necesarias
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Calificacion.php';
require_once __DIR__ . '/../models/DetalleCalificacion.php';


class JuradoController
{
    public function calificar()
    {
        redirect_if_not_jurado();

        $user = $_SESSION['user'];
        $id_participacion = $_GET['id'] ?? null;

        if (!$id_participacion || !is_numeric($id_participacion)) {
            die("Conjunto no especificado o inválido.");
        }

        // Cargar modelos
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';
        require_once __DIR__ . '/../models/CriterioConcurso.php';
        require_once __DIR__ . '/../models/Calificacion.php';

        // Obtener datos del conjunto
        $conjunto = ParticipacionConjunto::obtenerPorId($id_participacion);
        if (!$conjunto) {
            die("Conjunto no encontrado en esta participación.");
        }

        // Verificar si ya fue calificado
        $calificacion = Calificacion::porJuradoYParticipacion($id_participacion, $user['id']);

        // Obtener criterios del concurso
        $criterios = CriterioConcurso::porConcurso($user['id_concurso']);
        if (empty($criterios)) {
            die("No hay criterios definidos para este concurso.");
        }

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/jurado/calificar.php';
    }

    public function evaluar()
    {
        require_once __DIR__ . '/../helpers/auth.php';
        redirect_if_not_jurado();

        $user = $_SESSION['user'];
        $id_concurso = $user['id_concurso'];
        $id_jurado = $user['id'];

        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        global $pdo;

        // Obtener conjuntos con estado
        $stmt = $pdo->prepare("
    SELECT 
        p.id_participacion,
        p.orden_presentacion,
        c.nombre AS nombre_conjunto,
        s.numero_serie,
        COALESCE(cal.estado, 'pendiente') AS estado_calificacion
    FROM ParticipacionConjunto p
    JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
    JOIN Serie s ON c.id_serie = s.id_serie
    LEFT JOIN Calificacion cal ON p.id_participacion = cal.id_participacion AND cal.id_jurado = ?
    WHERE p.id_concurso = ?
    ORDER BY p.orden_presentacion
");
        $stmt->execute([$id_jurado, $id_concurso]);
        $conjuntos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Cargar detalles de puntajes
        foreach ($conjuntos as &$c) {
            $c['detalles'] = [];

            // Solo si ya fue calificado
            if ($c['estado_calificacion'] === 'enviado' || $c['estado_calificacion'] === 'calificado') {
                $stmt_det = $pdo->prepare("
            SELECT cr.nombre AS nombre_criterio, dc.puntaje
            FROM detallecalificacion dc
            JOIN CriterioConcurso cc ON dc.id_criterio_concurso = cc.id_criterio_concurso
            JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
            JOIN Calificacion cal ON dc.id_calificacion = cal.id_calificacion
            WHERE cal.id_jurado = ? AND cal.id_participacion = ?
            ORDER BY cr.nombre
        ");
                $stmt_det->execute([$id_jurado, $c['id_participacion']]);
                $c['detalles'] = $stmt_det->fetchAll(PDO::FETCH_ASSOC);
            }
        }
        require_once __DIR__ . '/../views/jurado/evaluar.php';
    }

    public function juradoGuardarCalificacion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $user = auth();
        if (!$user || $user['rol'] !== 'Jurado') {
            header('Location: index.php?page=login');
            exit;
        }

        global $pdo;

        $id_participacion = (int)$_POST['id_participacion'];
        $id_concurso = (int)$_POST['id_concurso'];
        $descalificar = isset($_POST['descalificar']);

        // Validar acceso
        $stmt_token = $pdo->prepare("
        SELECT token FROM TokenAcceso 
        WHERE id_jurado = ? AND id_concurso = ? AND fecha_expiracion > NOW()
    ");
        $stmt_token->execute([$user['id'], $id_concurso]);
        if (!$stmt_token->fetchColumn()) {
            header('Location: index.php?page=jurado_evaluar&error=no_permiso');
            exit;
        }

        // Verificar participación
        $stmt_part = $pdo->prepare("
        SELECT p.id_participacion, c.id_conjunto
        FROM ParticipacionConjunto p
        JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
        WHERE p.id_participacion = ? AND p.id_concurso = ?
    ");
        $stmt_part->execute([$id_participacion, $id_concurso]);
        if (!$stmt_part->fetch()) {
            header('Location: index.php?page=jurado_evaluar&error=no_existe');
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Obtener o crear calificación principal
            $stmt_check = $pdo->prepare("
            SELECT id_calificacion, estado 
            FROM Calificacion 
            WHERE id_jurado = ? AND id_participacion = ?
        ");
            $stmt_check->execute([$user['id'], $id_participacion]);
            $calif = $stmt_check->fetch();

            if ($descalificar) {
                if ($calif) {
                    $pdo->prepare("UPDATE Calificacion SET estado = 'descalificado' WHERE id_calificacion = ?")
                        ->execute([$calif['id_calificacion']]);
                    $pdo->prepare("DELETE FROM detallecalificacion WHERE id_calificacion = ?")
                        ->execute([$calif['id_calificacion']]);
                } else {
                    $stmt_ins = $pdo->prepare("
                    INSERT INTO Calificacion (id_jurado, id_participacion, estado, id_concurso, fecha_hora)
                    VALUES (?, ?, 'descalificado', ?, NOW())
                ");
                    $stmt_ins->execute([$user['id'], $id_participacion, $id_concurso]);
                }
            } else {
                if (!$calif) {
                    $stmt_ins = $pdo->prepare("
                    INSERT INTO Calificacion (id_jurado, id_participacion, estado, id_concurso, fecha_hora)
                    VALUES (?, ?, 'enviado', ?, NOW())
                ");
                    $stmt_ins->execute([$user['id'], $id_participacion, $id_concurso]);
                    $id_calificacion = $pdo->lastInsertId();
                } else {
                    $id_calificacion = $calif['id_calificacion'];
                    $pdo->prepare("DELETE FROM detallecalificacion WHERE id_calificacion = ?")
                        ->execute([$id_calificacion]);
                }

                // Obtener criterios del concurso
                $stmt_crit = $pdo->prepare("
                SELECT cc.id_criterio_concurso, cr.nombre, cc.puntaje_maximo
                FROM CriterioConcurso cc
                JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
                WHERE cc.id_concurso = ?
            ");
                $stmt_crit->execute([$id_concurso]);
                $criterios = $stmt_crit->fetchAll(PDO::FETCH_ASSOC);

                foreach ($criterios as $c) {
                    $campo = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['_', 'a', 'e', 'i', 'o', 'u'], $c['nombre']));
                    $puntaje = round((float)($_POST["puntaje_$campo"] ?? 0), 2);
                    $puntaje = max(0, min($puntaje, $c['puntaje_maximo']));

                    $stmt_det = $pdo->prepare("
                    INSERT INTO detallecalificacion (id_calificacion, id_criterio_concurso, puntaje)
                    VALUES (?, ?, ?)
                ");
                    $stmt_det->execute([$id_calificacion, $c['id_criterio_concurso'], $puntaje]);
                }
            }

            $pdo->commit();

            header("Location: index.php?page=jurado_evaluar&success=guardado");
            exit;
        } catch (Exception $e) {
            $pdo->rollback();
            error_log("Error al guardar calificación: " . $e->getMessage());
            header("Location: index.php?page=jurado_evaluar&error=db");
            exit;
        }
    }
}

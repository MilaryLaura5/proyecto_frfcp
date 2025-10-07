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
        $id_jurado = $user['id']; // Asegúrate de que esto sea un entero

        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        $conjuntos = ParticipacionConjunto::porConcurso($id_concurso, (int)$id_jurado);

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
        $stmt_token = $pdo->prepare("SELECT token FROM TokenAcceso 
        WHERE id_jurado = ? AND id_concurso = ? AND fecha_expiracion > NOW()
    ");
        $stmt_token->execute([$user['id'], $id_concurso]);
        $token_db = $stmt_token->fetchColumn();

        if (!$token_db) {
            header('Location: index.php?page=jurado_evaluar&error=no_permiso');
            exit;
        }

        // Verificar participación
        $stmt_part = $pdo->prepare("SELECT p.id_participacion, c.id_conjunto
        FROM ParticipacionConjunto p
        JOIN Conjunto c ON p.id_conjunto = c.id_conjunto
        WHERE p.id_participacion = ? AND p.id_concurso = ?
    ");
        $stmt_part->execute([$id_participacion, $id_concurso]);
        $participacion = $stmt_part->fetch();

        if (!$participacion) {
            header('Location: index.php?page=jurado_evaluar&error=no_existe');
            exit;
        }

        try {
            $pdo->beginTransaction();

            // Obtener o crear calificación principal
            $calificacion = Calificacion::porJuradoYParticipacion($user['id'], $id_participacion);

            if ($descalificar) {
                if ($calificacion) {
                    Calificacion::actualizarEstado($calificacion['id_calificacion'], 'descalificado');
                } else {
                    Calificacion::crear($user['id'], $id_participacion, 'descalificado');
                }
            } else {
                // Obtener criterios del concurso
                $stmt_crit = $pdo->prepare("SELECT cc.id_criterio_concurso, cc.id_criterio, cr.nombre, cc.puntaje_maximo
                FROM CriterioConcurso cc
                JOIN Criterio cr ON cc.id_criterio = cr.id_criterio
                WHERE cc.id_concurso = ?
            ");
                $stmt_crit->execute([$id_concurso]);
                $criterios = $stmt_crit->fetchAll(PDO::FETCH_ASSOC);

                if (empty($criterios)) {
                    throw new Exception("No hay criterios definidos");
                }

                // Crear o actualizar cabecera
                if (!$calificacion) {
                    $id_calificacion = Calificacion::crear($user['id'], $id_participacion, 'enviado');
                } else {
                    $id_calificacion = $calificacion['id_calificacion'];
                    // Eliminar detalles anteriores
                    DetalleCalificacion::eliminarPorCalificacion($id_calificacion);
                }

                // Insertar cada puntaje
                foreach ($criterios as $c) {
                    $campo = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['_', 'a', 'e', 'i', 'o', 'u'], $c['nombre']));
                    $puntaje = round((float)($_POST["puntaje_$campo"] ?? 0), 2);
                    $puntaje = max(0, min($puntaje, $c['puntaje_maximo']));

                    DetalleCalificacion::insertar($id_calificacion, $c['id_criterio_concurso'], $puntaje);
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

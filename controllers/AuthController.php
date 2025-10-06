<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/auth.php';


class AuthController
{
    // Mostrar formulario de login
    public function showLogin()
    {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesar el inicio de sesión
    public function login()
    {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }


        if ($_POST) {
            $usuario = trim($_POST['usuario']);
            $contraseña = $_POST['contraseña'];

            // Validar campos vacíos
            if (empty($usuario) || empty($contraseña)) {
                header('Location: index.php?page=login&error=vacios');
                exit;
            }

            // Validar en la base de datos
            $usuario = Usuario::validar($usuario, $contraseña);

            if ($usuario) {
                // Limpiar cualquier sesión previa
                session_destroy();
                session_start();

                // Guardar datos del usuario
                $_SESSION['user'] = [
                    'id' => $usuario['id_usuario'],
                    'usuario' => $usuario['usuario'],
                    'rol' => $usuario['rol'],
                    'nombre' => $usuario['nombre']
                ];

                // Redirigir según rol
                switch ($usuario['rol']) {
                    case 'Administrador':
                        header('Location: index.php?page=admin_dashboard');
                        break;

                    case 'Jurado':
                        header('Location: index.php?page=jurado_evaluar');
                        break;

                    case 'Presidente':
                        // ✅ CORREGIDO: Cambiado a una página que SÍ existe
                        header('Location: index.php?page=presidente_seleccionar_concurso');
                        break;

                    default:
                        header('Location: index.php?page=login&error=rol');
                        break;
                }
                exit; // ← Muy importante: detener ejecución después de redirigir
            } else {
                header('Location: index.php?page=login&error=invalido');
                exit;
            }
        } else {
            // Si no es POST, redirigir al login
            header('Location: index.php?page=login');
            exit;
        }
    }
    // En AuthController.php → método para login con token
    public function loginConToken()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            die("Token no proporcionado");
        }

        global $pdo;
        $stmt = $pdo->prepare("
        SELECT u.id_usuario, u.correo, t.fecha_expiracion 
        FROM TokenAcceso t
        JOIN Usuario u ON t.id_jurado = u.id_usuario
        WHERE t.token = ? AND t.usado = 0 AND t.fecha_expiracion > NOW()
    ");
        $stmt->execute([$token]);
        $usuario = $stmt->fetch();

        if (!$usuario) {
            die("❌ Token inválido, ya usado o expirado.");
        }

        // Marcar como usado inmediatamente
        $pdo->prepare("UPDATE TokenAcceso SET usado = 1, fecha_uso = NOW() WHERE token = ?")->execute([$token]);

        // Iniciar sesión temporal
        session_start();
        $_SESSION['user'] = [
            'id' => $usuario['id_usuario'],
            'correo' => $usuario['correo'],
            'rol' => 'Jurado'
        ];

        // Guardar token en sesión para usarlo después
        $_SESSION['current_token'] = $token;

        header('Location: index.php?page=jurado_evaluar');
        exit;
    }

    public function mostrarLoginConToken()
    {
        $token = $_GET['token'] ?? null;

        if (!$token) {
            die("Acceso denegado: falta token.");
        }

        // Asegurar sesión
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        global $pdo;
        try {
            $stmt = $pdo->prepare("
            SELECT t.token, u.usuario, u.contraseña
            FROM TokenAcceso t
            JOIN Usuario u ON t.id_jurado = u.id_usuario
            JOIN Concurso c ON t.id_concurso = c.id_concurso
            WHERE t.token = ? 
            AND c.estado = 'Activo' 
            AND c.fecha_fin > NOW()
            ");
            $stmt->execute([$token]);
            $datos = $stmt->fetch();

            if (!$datos) {
                require_once __DIR__ . '/../views/jurado/login_invalido.php';
                exit;
            }

            $_SESSION['pending_token'] = $token;
            require_once __DIR__ . '/../views/jurado/login.php';
        } catch (Exception $e) {
            error_log("Error en mostrarLoginConToken: " . $e->getMessage());
            require_once __DIR__ . '/../views/jurado/login_invalido.php';
        }
    }

    public function loginConTokenSubmit()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_POST) {
            $usuario = trim($_POST['usuario']);
            $contrasena = $_POST['contrasena'];
            $token = $_SESSION['pending_token'] ?? null;

            if (!$token) {
                header('Location: index.php?page=login');
                exit;
            }

            global $pdo;
            try {
                $check_token = $pdo->prepare("
                SELECT t.id_jurado, t.id_concurso, u.id_usuario, u.contraseña, u.estado 
                FROM TokenAcceso t
                JOIN Usuario u ON t.id_jurado = u.id_usuario
                JOIN Concurso c ON t.id_concurso = c.id_concurso
                WHERE t.token = ? 
                  AND u.usuario = ?
                  AND c.estado = 'Activo'
                  AND c.fecha_fin > NOW()
            ");
                $check_token->execute([$token, $usuario]);
                $datos = $check_token->fetch();

                if (!$datos) {
                    require_once __DIR__ . '/../views/jurado/login_invalido.php';
                    exit;
                }

                if (!password_verify($contrasena, $datos['contraseña'])) {
                    require_once __DIR__ . '/../views/jurado/login_invalido.php';
                    exit;
                }

                // ✅ Guardar id_concurso en la sesión
                $_SESSION['user'] = [
                    'id' => $datos['id_usuario'],
                    'usuario' => $usuario,
                    'rol' => 'Jurado',
                    'id_concurso' => $datos['id_concurso'] // ✅ Añadido
                ];
                unset($_SESSION['pending_token']);

                header('Location: index.php?page=jurado_evaluar');
                exit;
            } catch (Exception $e) {
                error_log("Error en loginConTokenSubmit: " . $e->getMessage());
                require_once __DIR__ . '/../views/jurado/login_invalido.php';
                exit;
            }
        }
    }

    public function guardarCalificacion()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        redirect_if_not_jurado();
        $user = $_SESSION['user'];

        if ($_POST) {
            $id_participacion = (int)$_POST['id_participacion'];
            $id_concurso = (int)$_POST['id_concurso'];
            $descalificado = isset($_POST['descalificar']) ? 1 : 0;
            $estado = $descalificado ? 'descalificado' : 'enviado';

            // Recoger todos los puntajes
            $puntajes = [];
            foreach ($_POST as $key => $value) {
                if (strpos($key, 'puntaje_') === 0) {
                    $float_value = filter_var($value, FILTER_VALIDATE_FLOAT);
                    if ($float_value === false || $float_value < 0 || $float_value > 10) {
                        header("Location: index.php?page=jurado_calificar&id=$id_participacion&error=rango");
                        exit;
                    }
                    $puntajes[$key] = number_format($float_value, 2, '.', '');
                }
            }

            // Validar que haya al menos un criterio
            if (empty($puntajes)) {
                header("Location: index.php?page=jurado_calificar&id=$id_participacion&error=sin_puntajes");
                exit;
            }

            global $pdo;

            try {
                $pdo->beginTransaction();

                // Verificar si ya existe una calificación
                $stmt_check = $pdo->prepare("
                SELECT id_calificacion FROM Calificacion 
                WHERE id_participacion = ? AND id_jurado = ?
            ");
                $stmt_check->execute([$id_participacion, $user['id']]);
                $existe = $stmt_check->fetch();

                // Preparar campos dinámicamente
                $campos = '';
                $valores = [];

                foreach ($puntajes as $campo => $valor) {
                    $campos .= "$campo = ?, ";
                    $valores[] = $valor;
                }

                $valores[] = $estado;
                $valores[] = $descalificado;
                $valores[] = $id_participacion;
                $valores[] = $user['id'];

                if ($existe) {
                    // Actualizar
                    $sql = "UPDATE Calificacion SET " . rtrim($campos, ', ') . ", estado = ?, descalificado = ? WHERE id_participacion = ? AND id_jurado = ?";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute($valores);
                } else {
                    // Insertar nueva calificación
                    $campos_insert = rtrim($campos, ', ') . ", estado, descalificado, id_participacion, id_jurado, id_concurso";
                    $placeholders = str_repeat('?, ', count($valores)) . '?, ?, ?';
                    $sql = "INSERT INTO Calificacion SET $campos_insert";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute(array_merge($valores, [$user['id_concurso']]));
                }

                $pdo->commit();

                // Redirigir con éxito
                header("Location: index.php?page=jurado_evaluar&success=calificado");
                exit;
            } catch (Exception $e) {
                $pdo->rollback();
                error_log("Error al guardar calificación: " . $e->getMessage());
                header("Location: index.php?page=jurado_calificar&id=$id_participacion&error=db");
                exit;
            }
        } else {
            header('Location: index.php?page=jurado_evaluar');
            exit;
        }
    }


    // Cerrar sesión
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}

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

        global $pdo;
        // Verificar que el token exista, no esté usado y el concurso esté activo
        $stmt = $pdo->prepare("
        SELECT t.token, u.usuario, u.contraseña, u.estado 
        FROM TokenAcceso t
        JOIN Usuario u ON t.id_jurado = u.id_usuario
        JOIN Concurso c ON t.id_concurso = c.id_concurso
        WHERE t.token = ? AND t.usado = 0 AND c.estado = 'Activo' AND c.fecha_fin > NOW()
    ");
        $stmt->execute([$token]);
        $datos = $stmt->fetch();

        if (!$datos) {
            echo "<div class='alert alert-danger text-center'>
                ❌ Token inválido, ya usado o el concurso no está activo.
              </div>";
            exit;
        }

        // Guardar token temporalmente (para validar después)
        session_start();
        $_SESSION['pending_token'] = $token;

        // Mostrar formulario de login
        require_once __DIR__ . '/../views/jurado/login.php'; // Vista arriba
    }

    public function loginConTokenSubmit()
    {
        session_start();

        if ($_POST) {
            $usuario = trim($_POST['correo']); // ← sigue siendo 'correo' en name, pero es 'usuario'
            $contrasena = $_POST['contrasena'];
            $token = $_SESSION['pending_token'] ?? null;

            if (!$token) {
                header('Location: index.php?page=login');
                exit;
            }

            global $pdo;

            // Verificar que el token sea válido y no usado
            $check_token = $pdo->prepare("
            SELECT t.id_jurado, u.id_usuario, u.contraseña, u.estado 
            FROM TokenAcceso t
            JOIN Usuario u ON t.id_jurado = u.id_usuario
            WHERE t.token = ? AND t.usado = 0 AND u.usuario = ?
        ");
            $check_token->execute([$token, $usuario]);
            $datos = $check_token->fetch();

            if (!$datos) {
                echo "<div class='alert alert-danger text-center'>❌ Token inválido o usuario incorrecto.</div>";
                exit;
            }

            // Verificar contraseña
            if (!password_verify($contrasena, $datos['contraseña'])) {
                echo "<div class='alert alert-danger text-center'>❌ Contraseña incorrecta.</div>";
                exit;
            }

            // ✅ Autenticado correctamente
            // Marcar token como usado
            $pdo->prepare("UPDATE TokenAcceso SET usado = 1, fecha_uso = NOW() WHERE token = ?")
                ->execute([$token]);

            // Iniciar sesión
            $_SESSION['user'] = [
                'id' => $datos['id_usuario'],
                'usuario' => $usuario,
                'rol' => 'Jurado'
            ];

            // Limpiar
            unset($_SESSION['pending_token']);

            // Redirigir a evaluación
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

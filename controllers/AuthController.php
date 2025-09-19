<?php
// controllers/AuthController.php

require_once __DIR__ . '/../models/Usuario.php';

class AuthController {

    // Mostrar formulario de login
    public function showLogin() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesar el inicio de sesión
    public function login() {
        // Asegurar que la sesión esté iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_POST) {
            $correo = trim($_POST['correo']);
            $contraseña = $_POST['contraseña'];

            // Validar campos vacíos
            if (empty($correo) || empty($contraseña)) {
                header('Location: index.php?page=login&error=vacios');
                exit;
            }

            // Validar en la base de datos
            $usuario = Usuario::validar($correo, $contraseña);

            if ($usuario) {
                // Limpiar cualquier sesión previa
                session_destroy();
                session_start();

                // Guardar datos del usuario
                $_SESSION['user'] = [
                    'id' => $usuario['id_usuario'],
                    'correo' => $usuario['correo'],
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

    // Cerrar sesión
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
?>
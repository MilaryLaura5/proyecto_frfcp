<?php

require_once __DIR__ . '/../models/Usuario.php';


class AuthController {

    // Mostrar formulario de login
    public function showLogin() {
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Procesar el inicio de sesión
    public function login() {
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
                // Iniciar sesión
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
                        header('Location: index.php?page=presidente_resultados');
                        break;
                    default:
                        header('Location: index.php?page=login&error=rol');
                        break;
                }
                exit;
            } else {
                header('Location: index.php?page=login&error=invalido');
                exit;
            }
        }
    }

    // Cerrar sesión
    public function logout() {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
?>
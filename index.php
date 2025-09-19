<?php
// index.php - Punto de entrada del sistema

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    require_once __DIR__ . '/config/database.php';
}

// Forzar que la cookie de sesión se envíe
if (!isset($_COOKIE[session_name()]) && !headers_sent()) {
    setcookie(
        session_name(),
        session_id(),
        [
            'expires' => 0,
            'path' => '/',
            'domain' => '',
            'secure' => false,   // true si usas HTTPS
            'httponly' => true,
            'samesite' => 'Lax'  // Permite envío en formularios
        ]
    );
}

// Verificar si se solicita una acción o vista
$page = $_GET['page'] ?? 'login';

// =============================
// RUTAS PÚBLICAS (sin login)
// =============================
if ($page === 'login') {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->showLogin();

} elseif ($page === 'login_submit') {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->login();

} elseif ($page === 'logout') {
    require_once __DIR__ . '/controllers/AuthController.php';
    $controller = new AuthController();
    $controller->logout();

// =============================
// RUTAS PROTEGIDAS (con login)
// =============================
} else {
    // Verificar autenticación
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    switch ($page) {
        // --- ADMINISTRADOR ---
        case 'admin_dashboard':
            require_once __DIR__ . '/views/admin/dashboard.php';
            break;

        case 'admin_gestion_concursos':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->mostrarFormularioCrearConcurso();
            break;

        case 'admin_crear_concurso_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConcurso();
            break;

        case 'admin_editar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->mostrarFormularioEditarConcurso();
            break;

        case 'admin_actualizar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarConcurso();
            break;

        case 'admin_eliminar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarConcurso();
            break;

        case 'admin_activar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->activarConcurso();
            break;

        case 'admin_cerrar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->cerrarConcurso();
            break;

        // --- JURADO ---
        case 'jurado_evaluar':
            echo "<h3>Evaluación - Jurado " . htmlspecialchars($_SESSION['user']['nombre']) . "</h3>";
            echo "<a href='index.php?page=logout' class='btn btn-danger'>Cerrar sesión</a>";
            // Aquí luego irá el formulario de evaluación
            break;

        // --- PRESIDENTE ---
        case 'presidente_dashboard':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->dashboard();
            break;

        case 'presidente_seleccionar_concurso':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->seleccionarConcurso();
            break;

        case 'presidente_revisar_resultados':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->revisarResultados();
            break;

        case 'presidente_generar_reporte':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->generarReporte();
            break;

        // --- DEFAULT ---
        default:
            header('Location: index.php?page=login');
            exit;
    }
}
?>

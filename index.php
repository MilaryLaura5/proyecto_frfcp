<?php

session_start();
require_once __DIR__ . '/controllers/AuthController.php';

$page = $_GET['page'] ?? 'login';

// Rutas públicas
if ($page === 'login') {
    $controller = new AuthController();
    $controller->showLogin();

} elseif ($page === 'login_submit') {
    $controller = new AuthController();
    $controller->login();

} elseif ($page === 'logout') {
    $controller = new AuthController();
    $controller->logout();

// Rutas protegidas (requieren login)
} else {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }

    // Aquí irán las vistas protegidas (dashboard, evaluación, etc.)
    switch ($page) {
        case 'admin_dashboard':
            require __DIR__ . '/views/admin/dashboard.php';
            echo "<h3>Bienvenido, Administrador " . $_SESSION['user']['nombre'] . "</h3>";
            echo "<a href='index.php?page=logout'>Cerrar sesión</a>";
            break;
        case 'jurado_evaluar':
            echo "<h3>Evaluación - Jurado " . $_SESSION['user']['nombre'] . "</h3>";
            echo "<a href='index.php?page=logout'>Cerrar sesión</a>";
            break;
        case 'presidente_resultados':
            echo "<h3>Resultados Oficiales - Presidente " . $_SESSION['user']['nombre'] . "</h3>";
            echo "<a href='index.php?page=logout'>Cerrar sesión</a>";
            break;
        default:
            header('Location: index.php?page=login');
            exit;
    }
}
?>
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

    // Rutas protegidas (requieren inicio de sesión)

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

        case 'jurado_evaluar':
            // Aquí irá la vista del jurado
            echo "<h3>Evaluación - Jurado " . htmlspecialchars($_SESSION['user']['nombre']) . "</h3>";
            echo "<a href='index.php?page=logout' class='btn btn-danger'>Cerrar sesión</a>";
            // Más adelante: formulario de evaluación
            break;

        case 'presidente_resultados':
            echo "<h3>Resultados Oficiales - Presidente " . htmlspecialchars($_SESSION['user']['nombre']) . "</h3>";
            echo "<a href='index.php?page=logout' class='btn btn-danger'>Cerrar sesión</a>";
            break;

        //CONCURSOS

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
        case 'admin_tipos_danza':
            require_once __DIR__ . '/views/admin/gestion_tipos_danza.php';
            break;

        case 'admin_seleccionar_concurso':
            require_once __DIR__ . '/views/admin/seleccionar_concurso.php';
            break;

        //SERIES
        case 'admin_gestion_series':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarSeries();
            break;

        case 'admin_crear_serie_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearSerie();
            break;

        case 'admin_editar_serie':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->mostrarFormularioEditarSerie();
            break;

        case 'admin_actualizar_serie':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarSerie();
            break;

        case 'admin_eliminar_serie':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarSerie();
            break;


        //TIPOS DE DANZA Y SERIES
        // Tipos y Series
        case 'admin_gestion_tipos_series':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarTiposYSeries();
            break;

        case 'admin_crear_tipo_danza':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearTipoDanza();
            break;

        case 'admin_actualizar_tipo_danza':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarTipoDanza();
            break;

        case 'admin_eliminar_tipo_danza':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarTipoDanza();
            break;

        //CONJUNTOS
        case 'admin_gestion_conjuntos':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntos();
            break;

        case 'admin_crear_conjunto_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConjunto();
            break;

        case 'admin_editar_conjunto':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->mostrarFormularioEditarConjunto();
            break;

        case 'admin_actualizar_conjunto':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarConjunto();
            break;

        case 'admin_eliminar_conjunto':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarConjunto();
            break;

        case 'descargar_plantilla_conjuntos':
            // Solo accesible si está logueado como admin
            if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Administrador') {
                header('Location: index.php?page=login');
                exit;
            }

            $id_concurso = $_GET['id_concurso'] ?? null;
            if (!$id_concurso) {
                die("Error: No se especificó un concurso.");
            }

            // Nombre del archivo descargado
            $filename = "plantilla_conjuntos_concurso_$id_concurso.csv";

            // Encabezados para forzar descarga
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');

            // Abrir output stream
            $output = fopen('php://output', 'w');

            // Escribir BOM para UTF-8 (evita problemas con tildes en Excel)
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Encabezados de la plantilla
            fputcsv($output, ['nombre', 'id_serie', 'orden_presentacion', 'numero_oficial'], ',', '"');

            // Ejemplos opcionales (puedes quitarlos si prefieres vacío)
            fputcsv($output, ['Morenada Laykakota', '7', '14', '14'], ',', '"');
            // fputcsv($output, ['Diablada Fuego Andino', '6', '2', '63'], ',', '"');

            fclose($output);
            exit;

        case 'admin_importar_conjuntos_csv':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->importarConjuntosCSV();
            break;


        //JURADOS
        case 'admin_gestion_jurados':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarJurados();
            break;

        case 'admin_crear_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearFormularioJurado();
            break;

        case 'admin_guardar_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarJurado();
            break;

        // Mostrar login con token
        case 'jurado_login':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->mostrarLoginConToken();
            break;

        // Procesar login con credenciales
        case 'jurado_login_submit':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->loginConTokenSubmit();
            break;

        default:
            header('Location: index.php?page=login');
            exit;
    }
}

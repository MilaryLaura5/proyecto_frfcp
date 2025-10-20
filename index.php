<?php
session_start();
// index.php - Punto de entrada del sistema

// Iniciar sesión solo si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
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
            'secure' => false,   // Cambia a true si usas HTTPS
            'httponly' => true,
            'samesite' => 'Lax'
        ]
    );
}

// Cargar conexión a la base de datos
require_once __DIR__ . '/config/database.php';

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
    // RUTAS PROTEGIDAS (requieren login)
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

        //PRESIDENTEEE
        case 'presidente_seleccionar_concurso':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->seleccionarConcursos();
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

        // CONCURSOS
        case 'admin_crear_concurso_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConcurso();
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

        // TIPOS DE DANZA Y SERIES (UNIFICADOS)
        case 'admin_gestion_series':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarSeriesYTpos(); // Muestra tipos + series
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

        // TIPOS DE DANZA (CRUD)
        case 'admin_crear_tipo_danza':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearTipoDanza();
            break;

        case 'admin_editar_tipo_danza':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->editarTipoDanza();
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

        // --- GESTIÓN GLOBAL DE CONJUNTOS ---
        case 'admin_gestionar_conjuntos_globales':
            require_once __DIR__ . '/views/admin/gestion_conjuntos_globales.php';
            break;

        case 'admin_crear_conjunto_global_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConjuntoGlobal();
            break;

        case 'admin_actualizar_conjunto_global':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarConjuntoGlobal();
            break;

        case 'admin_eliminar_conjunto_global':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarConjuntoGlobal();
            break;

        case 'admin_importar_conjuntos_csv_global':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->importarConjuntosCSVGlobal();
            break;
        //CONJUNTOS EN UN CONCURSO (solo asignación)
        case 'admin_gestion_conjuntos':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntos();
            break;
        case 'admin_seleccionar_concurso':
            require_once __DIR__ . '/views/admin/seleccionar_concurso.php';
            break;

        // Asignar un conjunto existente al concurso (con orden_presentacion)
        case 'admin_asignar_conjunto_a_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->asignarConjuntoAConcurso();
            break;

        // Eliminar solo la participación (no elimina el conjunto global)
        case 'admin_eliminar_participacion':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarParticipacion();
            break;

        // DESCARGAR PLANTILLA CSV
        case 'descargar_plantilla_conjuntos':
            if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Administrador') {
                header('Location: index.php?page=login');
                exit;
            }

            $id_concurso = $_GET['id_concurso'] ?? null;
            if (!$id_concurso) {
                die("Error: No se especificó un concurso.");
            }

            $filename = "plantilla_conjuntos_concurso_$id_concurso.csv";
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Pragma: no-cache');

            $output = fopen('php://output', 'w');
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM UTF-8
            fputcsv($output, ['nombre', 'id_serie', 'orden_presentacion', 'numero_oficial'], ',', '"');
            fclose($output);
            exit;

            // IMPORTAR CSV
        case 'admin_importar_conjuntos_csv':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->importarConjuntosCSV();
            break;

        // JURADOS
        case 'admin_gestion_jurados':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarJurados();
            break;

        case 'admin_guardar_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarJurado();
            break;

        case 'admin_verificar_usuario':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->verificarUsuario();
            break;
        case 'admin_buscar_jurado_por_dni':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->buscarJuradoPorDni();
            break;
        // Criterios de evaluación
        case 'admin_agregar_criterios':
            require_once __DIR__ . '/views/admin/agregar_criterios.php';
            break;

        case 'admin_guardar_criterios':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriterios();
            break;

        case 'admin_guardar_criterios_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriteriosConcurso();
            break;
        // Gestionar criterios globales
        case 'admin_gestionar_criterios':
            require_once __DIR__ . '/views/admin/gestion_criterios.php';
            break;
        //RESULTADOS PARA ADMIN RESULTADO EN VIVO
        case 'admin_resultados':
            require_once __DIR__ . '/views/admin/admin_resultados.php';
            break;
        // Asignar criterios a concurso
        case 'admin_asignar_criterios_concurso':
            require_once __DIR__ . '/views/admin/asignar_criterios_concurso.php';
            break;

        case 'admin_configurar_criterios':
            require_once __DIR__ . '/views/admin/asignar_criterios_concurso.php'; // mismo archivo
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

        case 'jurado_evaluar':
            require_once __DIR__ . '/views/jurado/evaluar.php';
            break;

        case 'jurado_calificar':
            require_once __DIR__ . '/views/jurado/calificar.php';
            break;

        case 'jurado_guardar_calificacion':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->guardarCalificacion();
            break;
        // RESULTADOS EN VIVO DE ADMIN
        case 'admin_ver_resultados':
            require_once __DIR__ . '/views/admin/admin_ver_resultados.php';
            break;
        // --- PRESIDENTE ---

        case 'presidente_seleccionar_concurso':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->seleccionarConcursos();
            break;

        case 'presidente_revisar_resultados':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->revisarResultados();
            break;

        case 'presidente_generar_reporte':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();

            break;

        // --- DEFAULT ---
        default:
            header('Location: index.php?page=admin_dashboard');
            exit;
    }
}

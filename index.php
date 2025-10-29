<?php
session_start();
// index.php - Punto de entrada del sistema

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
    // Asegurar que la sesión esté iniciada
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si no hay usuario logueado y la página no es de jurado, redirigir
    if (!isset($_SESSION['user']) && !in_array($_GET['page'] ?? '', ['jurado_login', 'jurado_login_submit'])) {
        header('Location: index.php?page=login');
        exit;
    }

    // Solo asignar $user si existe
    $user = $_SESSION['user'] ?? null;
    $rol = $user['rol'] ?? null;


    // Redirigir según rol si intenta acceder a ruta no permitida
    function redirigir_no_autorizado()
    {
        $user = $_SESSION['user'] ?? null;
        if ($user && $user['rol'] === 'Jurado') {
            header('Location: index.php?page=jurado_evaluar');
        } elseif ($user && $user['rol'] === 'Presidente') {
            header('Location: index.php?page=presidente_seleccionar_concurso');
        } else {
            header('Location: index.php?page=admin_dashboard');
        }
        exit;
    }

    switch ($page) {
        // --- ADMINISTRADOR ---
        case 'admin_dashboard':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/dashboard.php';
            break;

        //PRESIDENTE (no requiere rol admin, así que no se valida aquí)
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
        case 'admin_editar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->editarConcurso();
            break;

        case 'admin_crear_concurso_submit':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConcurso();
            break;

        case 'admin_actualizar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarConcurso();
            break;

        case 'admin_eliminar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarConcurso();
            break;

        case 'admin_activar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->activarConcurso();
            break;

        case 'admin_cerrar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->cerrarConcurso();
            break;

        case 'admin_gestion_concursos':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConcursos();
            break;

        // TIPOS DE DANZA Y SERIES
        case 'admin_gestion_series':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarSeriesYTpos();
            break;

        case 'admin_crear_serie_submit':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearSerie();
            break;

        case 'admin_editar_serie':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->mostrarFormularioEditarSerie();
            break;

        case 'admin_actualizar_serie':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarSerie();
            break;

        case 'admin_eliminar_serie':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarSerie();
            break;

        // TIPOS DE DANZA (CRUD)
        case 'admin_crear_tipo_danza':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearTipoDanza();
            break;

        case 'admin_editar_tipo_danza':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->editarTipoDanza();
            break;

        case 'admin_actualizar_tipo_danza':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarTipoDanza();
            break;

        case 'admin_eliminar_tipo_danza':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarTipoDanza();
            break;

        // GESTIÓN GLOBAL DE CONJUNTOS
        case 'admin_gestionar_conjuntos_globales':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntosGlobales();
            break;

        case 'admin_editar_conjunto_global':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->editarConjuntoGlobal();
            break;

        case 'admin_crear_conjunto_global_submit':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConjuntoGlobal();
            break;

        case 'admin_actualizar_conjunto_global':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->actualizarConjuntoGlobal();
            break;

        case 'admin_eliminar_conjunto_global':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarConjuntoGlobal();
            break;

        case 'admin_importar_conjuntos_csv_global':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->importarConjuntosCSVGlobal();
            break;

        case 'admin_editar_orden_participacion':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->editarOrdenParticipacion();
            break;

        // CONJUNTOS EN UN CONCURSO
        case 'admin_gestion_conjuntos':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntos();
            break;

        case 'admin_seleccionar_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->seleccionarConcurso();
            break;

        case 'admin_asignar_conjunto_a_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->asignarConjuntoAConcurso();
            break;

        case 'admin_eliminar_participacion':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarParticipacion();
            break;

        // DESCARGAR PLANTILLA CSV
        case 'descargar_plantilla_conjuntos':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
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
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($output, ['nombre', 'id_serie', 'orden_presentacion', 'numero_oficial'], ',', '"');
            fclose($output);
            exit;

        case 'admin_importar_conjuntos_csv':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->importarConjuntosCSV();
            break;

        // JURADOS
        case 'admin_gestion_jurados':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarJurados();
            break;

        case 'admin_guardar_jurado':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarJurado();
            break;

        case 'admin_verificar_usuario':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->verificarUsuario();
            break;

        case 'admin_buscar_jurado_por_dni':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->buscarJuradoPorDni();
            break;

        case 'admin_crear_jurado':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearJurado();
            break;

        // CRITERIOS
        case 'admin_agregar_criterios':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/agregar_criterios.php';
            break;

        case 'admin_guardar_criterios':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriterios();
            break;

        case 'admin_guardar_criterios_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriteriosConcurso();
            break;

        case 'admin_gestionar_criterios':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarCriterios();
            break;

        case 'admin_resultados':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/admin_resultados.php';
            break;

        case 'admin_asignar_criterios_concurso':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/asignar_criterios_concurso.php';
            break;

        case 'admin_configurar_criterios':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/asignar_criterios_concurso.php';
            break;

        // --- JURADO ---
        case 'jurado_login':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->mostrarLoginConToken();
            break;

        case 'jurado_login_submit':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->loginConTokenSubmit();
            break;

        case 'jurado_evaluar':
            require_once __DIR__ . '/controllers/JuradoController.php';
            $controller = new JuradoController();
            $controller->evaluar();
            break;

        case 'jurado_calificar':
            require_once __DIR__ . '/controllers/JuradoController.php';
            $controller = new JuradoController();
            $controller->calificar();
            break;

        case 'jurado_guardar_calificacion':
            require_once __DIR__ . '/controllers/JuradoController.php';
            $controller = new JuradoController();
            $controller->juradoGuardarCalificacion();
            break;

        case 'admin_ver_resultados':
            if ($rol !== 'Administrador') {
                redirigir_no_autorizado();
            }
            require_once __DIR__ . '/views/admin/admin_ver_resultados.php';
            break;

        // --- DEFAULT ---
        default:
            header('Location: index.php?page=admin_dashboard');
            exit;
    }
}

<?php
// index.php - Punto de entrada del sistema
date_default_timezone_set('America/Lima'); // Para Perú, Colombia, Ecuador, México, etc.
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

        // CONCURSOS
        case 'admin_gestion_concursos':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConcursos();
            break;

        case 'admin_crear_concurso_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConcurso();
            break;

        case 'admin_editar_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConcursos(); // ✅ Usa el método completo
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
        case 'admin_gestion_tipos_series':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarTiposSeries();
            break;
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
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntosGlobales();
            break;

        case 'admin_crear_conjunto_global_submit':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearConjuntoGlobal();
            break;

        case 'admin_editar_conjunto_global':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarConjuntosGlobales(); // ✅ Reutiliza el mismo método
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
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->seleccionarConcursoParaGestionarConjuntos();
            break;

        case 'admin_seleccionarS_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->seleccionarConcurso();
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

        case 'admin_crear_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->crearJurado();
            break;

        case 'admin_guardar_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarJurado();
            break;

        case 'admin_buscar_jurado':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->buscarJuradoPorDni();
            break;

        case 'admin_verificar_usuario':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->verificarUsuario();
            break;

        // Criterios de evaluación
        case 'admin_gestionar_criterios':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->gestionarCriterios();
            break;

        case 'admin_agregar_criterios':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->agregarCriterios();
            break;

        case 'admin_guardar_criterios':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriterios();
            break;

        case 'admin_guardar_criterio_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->guardarCriterioConcurso();
            break;

        // Asignar criterios a concurso
        case 'admin_asignar_criterios_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->asignarCriteriosConcurso();
            break;

        case 'admin_configurar_criterios':
            require_once __DIR__ . '/views/admin/asignar_criterios_concurso.php'; // mismo archivo
            break;

        case 'admin_eliminar_criterio_concurso':
            require_once __DIR__ . '/controllers/AdminController.php';
            $controller = new AdminController();
            $controller->eliminarCriterioConcurso();
            break;

        // ===============
        //   JURADO
        // ===============

        // Mostrar login con token
        case 'jurado_login':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->mostrarLoginConToken();
            break;

        case 'jurado_login_submit':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->loginConTokenSubmit();
            exit;

        case 'logout':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            $controller->logout();
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

        // --- PRESIDENTE ---
        case 'presidente_seleccionar_concurso':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->seleccionarConcurso();
            break;

        case 'presidente_ver_resultados':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->verResultados();
            break;

        case 'presidente_ver_resultados_por_serie':
            require_once __DIR__ . '/controllers/PresidenteController.php';
            $controller = new PresidenteController();
            $controller->verResultadosPorSerie();
            break;

        case 'resultados_en_vivo':
            require_once __DIR__ . '/controllers/ResultadosController.php';
            $controller = new ResultadosController();
            $controller->panelEnVivo();
            break;

        case 'api_resultados':
            require_once __DIR__ . '/controllers/ResultadosController.php';
            $controller = new ResultadosController();
            $controller->obtenerResultadosAPI();
            break;

        // --- DEFAULT ---
        default:
            header('Location: index.php?page=admin_dashboard');
            exit;
    }
}

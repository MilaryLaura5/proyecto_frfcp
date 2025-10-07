<?php
// controllers/AdminController.php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require_once __DIR__ . '/../models/Concurso.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../models/TipoDanza.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Concurso.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

global $pdo;
class AdminController
{
    // =============================
    // CONCURSO
    // =============================

    public function crearConcurso()
    {
        redirect_if_not_admin();
        if ($_POST) {
            $nombre = trim($_POST['nombre']);
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];

            if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                header('Location: index.php?page=admin_gestion_concursos&error=vacios');
                exit;
            }
            if ($fecha_inicio >= $fecha_fin) {
                header('Location: index.php?page=admin_gestion_concursos&error=fechas');
                exit;
            }

            if (Concurso::crear($nombre, $fecha_inicio, $fecha_fin)) {
                header('Location: index.php?page=admin_gestion_concursos&success=1');
            } else {
                header('Location: index.php?page=admin_gestion_concursos&error=db');
            }
            exit;
        }
    }
    public function actualizarConcurso()
    {
        redirect_if_not_admin();
        if ($_POST) {
            $id = (int)$_POST['id_concurso'];
            $nombre = trim($_POST['nombre']);
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];

            if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                header("Location: index.php?page=admin_editar_concurso&id=$id&error=vacios");
                exit;
            }
            if ($fecha_inicio >= $fecha_fin) {
                header("Location: index.php?page=admin_editar_concurso&id=$id&error=fechas");
                exit;
            }

            if (Concurso::editar($id, $nombre, $fecha_inicio, $fecha_fin)) {
                header('Location: index.php?page=admin_gestion_concursos&success=editado');
            } else {
                header("Location: index.php?page=admin_editar_concurso&id=$id&error=db");
            }
            exit;
        }
    }
    public function eliminarConcurso()
    {
        redirect_if_not_admin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }

        if (Concurso::eliminar($id)) {
            header('Location: index.php?page=admin_gestion_concursos&success=eliminado');
        } else {
            header('Location: index.php?page=admin_gestion_concursos&error=tiene_evaluaciones');
        }
        exit;
    }
    public function activarConcurso()
    {
        redirect_if_not_admin();
        $id = $_GET['id'] ?? null;
        if ($id && Concurso::activar($id)) {
            header('Location: index.php?page=admin_gestion_concursos&success=activado');
        } else {
            header('Location: index.php?page=admin_gestion_concursos&error=no_activado');
        }
        exit;
    }
    public function cerrarConcurso()
    {
        redirect_if_not_admin();
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_id');
            exit;
        }

        if (Concurso::cerrar($id)) {
            header('Location: index.php?page=admin_gestion_concursos&success=cerrado');
        } else {
            header('Location: index.php?page=admin_gestion_concursos&error=db');
        }
        exit;
    }
    public function gestionarConcursos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        $editando = false;
        $concurso_a_editar = null;
        $page = $_GET['page'] ?? 'admin_gestion_concursos';

        // Cargar concurso si se está editando
        if (isset($_GET['id']) && $page === 'admin_editar_concurso') {
            $concurso_a_editar = Concurso::obtenerPorId($_GET['id']);
            if ($concurso_a_editar) {
                $editando = true;
            } else {
                header('Location: index.php?page=admin_gestion_concursos&error=no_existe');
                exit;
            }
        }

        // Listar todos los concursos
        $concursos = Concurso::listar();

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestion_concursos.php';
    }

    // =============================
    // TIPOS DE DANZA + SERIES
    // =============================

    public function gestionarSeries()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        // Cargar modelos
        require_once __DIR__ . '/../models/TipoDanza.php';
        require_once __DIR__ . '/../models/Serie.php';

        $tipos = TipoDanza::listar();
        $series = Serie::listar();

        // Variables para edición
        $editando_serie = false;
        $serie_edit = null;

        if (isset($_GET['id']) && $_GET['page'] === 'admin_editar_serie') {
            $serie_edit = Serie::obtenerPorId($_GET['id']);
            if ($serie_edit) {
                $editando_serie = true;
            }
        }

        $editando_tipo = false;
        $tipo_edit = null;

        if (isset($_GET['id']) && $_GET['page'] === 'admin_editar_tipo_danza') {
            $tipo_edit = TipoDanza::obtenerPorId($_GET['id']);
            if ($tipo_edit) {
                $editando_tipo = true;
            }
        }

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestionar_series.php';
    }
    public function gestionarSeriesYTpos()
    {
        redirect_if_not_admin();
        global $pdo;
        require_once __DIR__ . '/../views/admin/gestion_series.php';
    }
    public function crearTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        if ($_POST) {
            $nombre = trim($_POST['nombre_tipo']);

            if (empty($nombre)) {
                header('Location: index.php?page=admin_gestion_series&error=tipo_vacio');
                exit;
            }

            if (TipoDanza::crear($nombre)) {
                header('Location: index.php?page=admin_gestion_series&success=tipo_creado');
            } else {
                header('Location: index.php?page=admin_gestion_series&error=tipo_db');
            }
            exit;
        }
    }
    public function editarTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        $id = $_GET['id'] ?? null;
        $tipo = $id ? TipoDanza::obtenerPorId($id) : null;

        if (!$tipo) {
            header('Location: index.php?page=admin_gestion_series&error=tipo_no_existe');
            exit;
        }

        require_once __DIR__ . '/../views/admin/gestion_series.php';
    }
    public function actualizarTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        if ($_POST) {
            $id = (int)$_POST['id_tipo'];
            $nombre = trim($_POST['nombre_tipo']);

            if (TipoDanza::actualizar($id, $nombre)) {
                header('Location: index.php?page=admin_gestion_series&success=tipo_editado');
            } else {
                header('Location: index.php?page=admin_gestion_series&error=tipo_db');
            }
            exit;
        }
    }
    public function eliminarTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        $id = $_GET['id'] ?? null;
        if ($id && TipoDanza::eliminar($id)) {
            header('Location: index.php?page=admin_gestion_series&success=tipo_eliminado');
        } else {
            header('Location: index.php?page=admin_gestion_series&error=tipo_usado');
        }
        exit;
    }
    public function crearSerie()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Serie.php';

        if ($_POST) {
            $numero_serie = (int)$_POST['numero_serie'];
            $nombre_serie = trim($_POST['nombre_serie']);
            $id_tipo = (int)$_POST['id_tipo'];

            if (empty($nombre_serie) || $numero_serie <= 0 || $id_tipo <= 0) {
                header('Location: index.php?page=admin_gestion_series&error=vacios');
                exit;
            }

            if (Serie::crear($numero_serie, $nombre_serie, $id_tipo)) {
                header('Location: index.php?page=admin_gestion_series&success=1');
            } else {
                header('Location: index.php?page=admin_gestion_series&error=db');
            }
            exit;
        }
    }
    public function mostrarFormularioEditarSerie()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_series.php';
    }
    public function actualizarSerie()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Serie.php';

        if ($_POST) {
            $id = (int)$_POST['id_serie'];
            $numero_serie = (int)$_POST['numero_serie'];
            $nombre_serie = trim($_POST['nombre_serie']);
            $id_tipo = (int)$_POST['id_tipo'];

            if (Serie::editar($id, $numero_serie, $nombre_serie, $id_tipo)) {
                header('Location: index.php?page=admin_gestion_series&success=editado');
            } else {
                header('Location: index.php?page=admin_gestion_series&error=db');
            }
            exit;
        }
    }
    public function eliminarSerie()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Serie.php';

        $id = $_GET['id'] ?? null;
        if ($id && Serie::eliminar($id)) {
            header('Location: index.php?page=admin_gestion_series&success=eliminado');
        } else {
            header('Location: index.php?page=admin_gestion_series&error=db');
        }
        exit;
    }
    public function gestionarTiposSeries()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        // Cargar modelos
        require_once __DIR__ . '/../models/TipoDanza.php';
        require_once __DIR__ . '/../models/Serie.php';

        // Listar tipos y series
        $tipos = TipoDanza::listar();

        // Agrupar series por tipo
        $series_por_tipo = [];
        foreach ($tipos as $t) {
            $series_por_tipo[$t['id_tipo']] = Serie::porTipo($t['id_tipo']);
        }

        // Editar tipo
        $editando_tipo = false;
        $tipo_edit = null;

        if (isset($_GET['editar_tipo']) && is_numeric($_GET['editar_tipo'])) {
            $id_tipo = (int)$_GET['editar_tipo'];
            $tipo_edit = array_filter($tipos, fn($t) => $t['id_tipo'] == $id_tipo);
            $tipo_edit = reset($tipo_edit);
            if ($tipo_edit) {
                $editando_tipo = true;
            }
        }

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestionar_tipos_series.php';
    }

    // =============================
    // CONJUNTOS
    // =============================

    public function gestionarConjuntos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();

        // Obtener id_concurso desde la URL
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
            exit;
        }

        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso || $concurso['estado'] === 'Cerrado') {
            header('Location: index.php?page=admin_gestion_concursos&error=invalido');
            exit;
        }

        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        // Cargar conjuntos globales
        require_once __DIR__ . '/../models/Conjunto.php';
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        $conjuntos_globales = Conjunto::listar();

        // Ordenar alfabéticamente
        usort($conjuntos_globales, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        // Participaciones en este concurso
        $participaciones = ParticipacionConjunto::listarPorConcurso($id_concurso);

        // Normalizar nombres para búsqueda (opcional: hacerlo en JS)
        foreach ($conjuntos_globales as &$c) {
            $c['nombre_normalizado'] = strtolower(
                preg_replace('/[\x{0300}-\x{036f}]/u', '', $c['nombre'])
            );
        }

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestionar_conjuntos.php';
    }
    public function asignarConjuntoAConcurso()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        if ($_POST) {
            $id_conjunto = (int)$_POST['id_conjunto'];
            $id_concurso = (int)$_POST['id_concurso'];
            $orden_presentacion = (int)$_POST['orden_presentacion'];

            if ($id_conjunto <= 0 || $id_concurso <= 0 || $orden_presentacion <= 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=vacios");
                exit;
            }

            global $pdo;
            $check = $pdo->prepare("SELECT COUNT(*) FROM ParticipacionConjunto 
                                WHERE id_concurso = ? AND orden_presentacion = ?");
            $check->execute([$id_concurso, $orden_presentacion]);
            if ($check->fetchColumn() > 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=duplicado");
                exit;
            }

            if (ParticipacionConjunto::agregar($id_conjunto, $id_concurso, $orden_presentacion)) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=asignado");
            } else {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
            }
            exit;
        }
    }
    public function eliminarParticipacion()
    {
        redirect_if_not_admin();

        $id_participacion = $_GET['id'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso || !$id_participacion) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_datos');
            exit;
        }

        global $pdo;
        $stmt = $pdo->prepare("SELECT id_participacion FROM ParticipacionConjunto 
                           WHERE id_participacion = ? AND id_concurso = ?");
        $stmt->execute([$id_participacion, $id_concurso]);

        if ($stmt->rowCount() == 0) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=no_permiso");
            exit;
        }

        $delete = $pdo->prepare("DELETE FROM ParticipacionConjunto WHERE id_participacion = ?");
        if ($delete->execute([$id_participacion])) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=eliminado");
        } else {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
        }
        exit;
    }
    public function importarConjuntosCSV()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        $id_concurso = $_POST['id_concurso'] ?? null;
        if (!$id_concurso) {
            header('Location: index.php?page=admin_seleccionar_concurso&error=no_concurso');
            exit;
        }

        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=archivo");
            exit;
        }

        $file = $_FILES['archivo_csv']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=lectura");
            exit;
        }

        fgetcsv($handle); // Saltar encabezado

        $errores = [];
        $importados = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $nombre = trim($data[0] ?? '');
            $id_serie = (int)($data[1] ?? 0);
            $orden_presentacion = (int)($data[2] ?? 0);

            if (empty($nombre) || $id_serie <= 0 || $orden_presentacion <= 0) {
                $errores[] = "Datos inválidos: $nombre";
                continue;
            }

            global $pdo;

            $stmt = $pdo->prepare("SELECT id_conjunto FROM Conjunto WHERE nombre = ? AND id_serie = ?");
            $stmt->execute([$nombre, $id_serie]);
            $row = $stmt->fetch();

            if ($row) {
                $id_conjunto = $row['id_conjunto'];
            } else {
                $insert = $pdo->prepare("INSERT INTO Conjunto (nombre, id_serie) VALUES (?, ?)");
                if ($insert->execute([$nombre, $id_serie])) {
                    $id_conjunto = $pdo->lastInsertId();
                } else {
                    $errores[] = "Error al crear conjunto: $nombre";
                    continue;
                }
            }

            $check = $pdo->prepare("SELECT COUNT(*) FROM ParticipacionConjunto 
                                WHERE id_concurso = ? AND orden_presentacion = ?");
            $check->execute([$id_concurso, $orden_presentacion]);
            if ($check->fetchColumn() > 0) {
                $errores[] = "Orden duplicado: $orden_presentacion - $nombre";
                continue;
            }

            if (ParticipacionConjunto::agregar($id_conjunto, $id_concurso, $orden_presentacion)) {
                $importados++;
            } else {
                $errores[] = "Error al asignar: $nombre";
            }
        }

        fclose($handle);

        $params = "id_concurso=$id_concurso&success=csv&importados=$importados";
        if (count($errores) > 0) {
            $params .= "&errores=" . count($errores);
        }

        header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&$params");
        exit;
    }
    public function crearConjuntoGlobal()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        if ($_POST) {
            $nombre = trim($_POST['nombre']);
            $id_serie = (int)$_POST['id_serie'];

            if (empty($nombre) || $id_serie <= 0) {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=vacios');
                exit;
            }

            if (Conjunto::crear($nombre, $id_serie)) {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&success=1');
            } else {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=duplicado');
            }
            exit;
        }
    }
    public function actualizarConjuntoGlobal()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        if ($_POST) {
            $id = (int)$_POST['id_conjunto'];
            $nombre = trim($_POST['nombre']);
            $id_serie = (int)$_POST['id_serie'];

            if (Conjunto::editar($id, $nombre, $id_serie)) {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&success=editado');
            } else {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=db');
            }
            exit;
        }
    }
    public function eliminarConjuntoGlobal()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        $id = $_GET['id'] ?? null;
        if ($id && Conjunto::eliminar($id)) {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&success=eliminado');
        } else {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=evaluado');
        }
        exit;
    }
    public function importarConjuntosCSVGlobal()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        if (!isset($_FILES['archivo_csv']) || $_FILES['archivo_csv']['error'] !== UPLOAD_ERR_OK) {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=archivo');
            exit;
        }

        $file = $_FILES['archivo_csv']['tmp_name'];
        $handle = fopen($file, 'r');
        if (!$handle) {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=lectura');
            exit;
        }

        fgetcsv($handle); // Saltar encabezado

        $errores = [];
        $importados = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $nombre = trim($data[0] ?? '');
            $id_serie = (int)($data[1] ?? 0);

            if (empty($nombre) || $id_serie <= 0) {
                $errores[] = "Datos inválidos: '$nombre'";
                continue;
            }

            global $pdo;

            $check = $pdo->prepare("SELECT id_conjunto FROM Conjunto WHERE LOWER(nombre) = LOWER(?) AND id_serie = ?");
            $check->execute([$nombre, $id_serie]);
            if ($check->fetch()) {
                $errores[] = "Ya existe: '$nombre' en esa serie";
                continue;
            }

            if (Conjunto::crear($nombre, $id_serie)) {
                $importados++;
            } else {
                $errores[] = "Error al crear: '$nombre'";
            }
        }

        fclose($handle);

        $params = "success=csv&importados=$importados";
        if (count($errores) > 0) {
            $params .= "&errores=" . count($errores);
        }

        header("Location: index.php?page=admin_gestionar_conjuntos_globales&$params");
        exit;
    }
    public function gestionarConjuntosGlobales()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();

        // Detectar si estamos editando
        $editando = false;
        $conjunto_edit = null;

        if (isset($_GET['id']) && $_GET['page'] === 'admin_editar_conjunto_global') {
            require_once __DIR__ . '/../models/Conjunto.php';
            $id = (int)$_GET['id'];
            $conjunto_edit = Conjunto::obtenerPorId($id);
            if ($conjunto_edit) {
                $editando = true;
            } else {
                header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=no_existe');
                exit;
            }
        }

        // Listar todos los conjuntos
        require_once __DIR__ . '/../models/Conjunto.php';
        $conjuntos = Conjunto::listar();

        // Listar series para el select
        require_once __DIR__ . '/../models/Serie.php';
        $series = Serie::listarConTipo();

        // Mensajes
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestionar_conjuntos_globales.php';
    }
    public function seleccionarConcursoParaGestionarConjuntos()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        global $pdo;

        // Obtener todos los concursos
        $stmt = $pdo->query("SELECT * FROM Concurso ORDER BY fecha_inicio DESC");
        $concursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $error = $_GET['error'] ?? null;

        // Cargar la vista correcta
        require_once __DIR__ . '/../views/admin/seleccionar_concurso.php';
    }

    // =============================
    // JURADOS
    // =============================

    public function crearJurado()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
            exit;
        }

        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=invalido');
            exit;
        }

        // Pasar datos a la vista
        require_once __DIR__ . '/../views/admin/crear_jurado.php';
    }
    public function gestionarJurados()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();

        // ✅ Reemplazado por mensaje completo con credenciales
        $mostrarCredenciales = false;
        $credenciales = [];

        if (isset($_SESSION['mensaje_jurado'])) {
            $credenciales = $_SESSION['mensaje_jurado'];
            unset($_SESSION['mensaje_jurado']);
            $mostrarCredenciales = true;
        }

        $error = $_GET['error'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null;

        // ✅ Listar concursos para el filtro
        require_once __DIR__ . '/../models/Concurso.php';
        require_once __DIR__ . '/../models/Jurado.php';

        $concursos = Concurso::listar();

        // ✅ Obtener jurados
        if ($id_concurso) {
            $jurados = Jurado::porConcurso($id_concurso);
        } else {
            $jurados = Jurado::listar();
        }

        // ✅ Pasar todo a la vista
        require_once __DIR__ . '/../views/admin/gestionar_jurados.php';
    }
    public function crearFormularioJurado()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/crear_jurado.php';
    }
    public function guardarJurado()
    {
        // Mostrar todos los errores
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        redirect_if_not_admin();

        require_once __DIR__ . '/../helpers/functions.php';
        require_once __DIR__ . '/../models/Jurado.php';
        require_once __DIR__ . '/../config/database.php';

        global $pdo;

        // Limpiar buffer de salida
        if (ob_get_level()) ob_clean();

        error_log("🔧 Inicio: guardarJurado");

        if (!$_POST) {
            error_log("❌ No es POST");
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }

        $id_concurso = (int)$_POST['id_concurso'];
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $especialidad = trim($_POST['especialidad']);
        $años_experiencia = (int)$_POST['años_experiencia'];

        error_log("📄 Datos recibidos: DNI=$dni, Nombre=$nombre, Concurso=$id_concurso");

        // Validaciones
        if (!preg_match('/^\d{8}$/', $dni)) {
            error_log("❌ DNI inválido: $dni");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=dni");
            exit;
        }

        if (empty($nombre)) {
            error_log("❌ Nombre vacío");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=datos");
            exit;
        }

        // Verificar DNI duplicado
        try {
            $stmt_check = $pdo->prepare("
            SELECT j.dni 
            FROM Jurado j 
            JOIN Usuario u ON j.id_jurado = u.id_usuario 
            WHERE j.dni = ?
        ");
            $stmt_check->execute([$dni]);
            if ($stmt_check->fetch()) {
                error_log("❌ DNI ya existe: $dni");
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=dni_duplicado");
                exit;
            }
        } catch (Exception $e) {
            error_log("❌ Error al verificar DNI: " . $e->getMessage());
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=db");
            exit;
        }

        // Generar usuario y contraseña
        $usuario = generarUsuario($nombre);
        $contraseña = generarContrasenaSegura(10);
        error_log("🔐 Usuario: $usuario, Contraseña: $contraseña");

        // Crear jurado
        if (Jurado::crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contraseña)) {
            error_log("✅ Jurado creado correctamente");

            $stmt = $pdo->prepare("SELECT id_jurado FROM Jurado WHERE dni = ?");
            $stmt->execute([$dni]);
            $jurado = $stmt->fetch();

            if (!$jurado) {
                error_log("❌ No se encontró jurado por DNI: $dni");
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=no_encontrado");
                exit;
            }

            $jurado_id = $jurado['id_jurado'];
            error_log("🆔 ID del jurado: $jurado_id");

            // Duración personalizada
            $dias = (int)($_POST['dias'] ?? 0);
            $horas = (int)($_POST['horas'] ?? 0);
            $minutos = (int)($_POST['minutos'] ?? 0);
            $total_minutos = $dias * 1440 + $horas * 60 + $minutos;

            if ($total_minutos <= 0) {
                error_log("❌ Duración inválida: $total_minutos minutos");
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=duracion");
                exit;
            }

            $fecha_expiracion = date('Y-m-d H:i:s', time() + $total_minutos * 60);
            error_log("⏰ Fecha expiración: $fecha_expiracion");

            $token = bin2hex(random_bytes(16));
            error_log("🔑 Token generado: $token");

            // Insertar token
            $sql_token = "INSERT INTO TokenAcceso 
                      (token, id_concurso, id_jurado, generado_por, fecha_generacion, fecha_expiracion)
                      VALUES (?, ?, ?, ?, NOW(), ?)";

            try {
                $stmt_token = $pdo->prepare($sql_token);
                $resultado = $stmt_token->execute([
                    $token,
                    $id_concurso,
                    $jurado_id,
                    $_SESSION['user']['id'],
                    $fecha_expiracion
                ]);

                if ($resultado) {
                    error_log("✅ Token insertado correctamente");

                    // Guardar en sesión
                    $_SESSION['mensaje_jurado'] = [
                        'usuario' => $usuario,
                        'contrasena' => $contraseña,
                        'token' => $token
                    ];
                    error_log("✅ Sesión guardada: mensaje_jurado");

                    // Redirigir
                    header("Location: index.php?page=admin_gestion_jurados&id_concurso=$id_concurso");
                    exit;
                } else {
                    $errorInfo = $stmt_token->errorInfo();
                    error_log("❌ Error al ejecutar INSERT Token: " . print_r($errorInfo, true));
                    header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=token_db");
                    exit;
                }
            } catch (Exception $e) {
                error_log("❌ Excepción al insertar token: " . $e->getMessage());
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=token_db");
                exit;
            }
        } else {
            error_log("❌ Fallo al crear jurado o usuario");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=db");
            exit;
        }
    }
    public function verificarUsuario()
    {
        redirect_if_not_admin();
        $usuario = $_GET['usuario'] ?? '';
        $suffix = '';
        global $pdo;

        while (true) {
            $check = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $check->execute([$usuario . $suffix]);
            if ($check->rowCount() == 0) {
                echo json_encode(['usuario' => $usuario . $suffix]);
                exit;
            }
            $suffix = rand(1, 99);
        }
    }

    // --- BÚSQUEDA DE JURADO POR DNI ---
    public function buscarJuradoPorDni()
    {
        redirect_if_not_admin();
        $dni = $_GET['dni'] ?? '';

        if (!preg_match('/^\d{8}$/', $dni)) {
            echo json_encode(['existe' => false]);
            exit;
        }

        global $pdo;
        $stmt = $pdo->prepare("
            SELECT j.nombre, j.especialidad 
            FROM Jurado j
            JOIN Usuario u ON j.id_jurado = u.id_usuario
            WHERE j.dni = ?
        ");
        $stmt->execute([$dni]);
        $jurado = $stmt->fetch();

        if ($jurado) {
            echo json_encode([
                'existe' => true,
                'nombre' => $jurado['nombre'],
                'especialidad' => $jurado['especialidad']
            ]);
        } else {
            echo json_encode(['existe' => false]);
        }
        exit;
    }

    // =============================
    // CRITERIOS
    // =============================

    public function gestionarCriterios()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        require_once __DIR__ . '/../models/Criterio.php';
        require_once __DIR__ . '/../models/Concurso.php';
        require_once __DIR__ . '/../models/CriterioConcurso.php';

        global $pdo;

        // Crear nuevo criterio
        if ($_POST['nombre'] ?? null) {
            $nombre = trim($_POST['nombre']);
            if ($nombre && !Criterio::existe($nombre)) {
                Criterio::crear($nombre);
            } else if (!$nombre) {
                header('Location: index.php?page=admin_gestionar_criterios&error=vacio');
                exit;
            }
            header('Location: index.php?page=admin_gestionar_criterios');
            exit;
        }

        // Datos principales
        $criterios = Criterio::listar();
        $concursos = Concurso::listar();

        // Si viene id_concurso, cargar criterios asignados
        $id_concurso = $_GET['id_concurso'] ?? null;
        $criterios_asignados = [];

        if ($id_concurso) {
            $criterios_asignados = CriterioConcurso::porConcurso($id_concurso);
        }

        // Cargar vista unificada
        require_once __DIR__ . '/../views/admin/gestionar_criterios_completo.php';
    }
    public function agregarCriterios()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $id_concurso = $_GET['id_concurso'] ?? null;
        if (!$id_concurso) {
            die("Concurso no especificado.");
        }

        global $pdo;

        // ✅ Incluir modelos necesarios
        require_once __DIR__ . '/../models/Concurso.php';
        require_once __DIR__ . '/../models/CriterioConcurso.php';

        // Obtener datos del concurso
        $stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso) {
            die("Concurso no encontrado.");
        }

        // ✅ Obtener criterios asignados y disponibles
        $criterios_asignados = CriterioConcurso::porConcurso($id_concurso);
        $criterios_disponibles = CriterioConcurso::disponiblesParaAsignar($id_concurso);

        // Pasar a la vista
        require_once __DIR__ . '/../views/admin/agregar_criterios.php';
    }
    public function guardarCriterios()
    {
        redirect_if_not_admin();
        $id_concurso = (int)$_POST['id_concurso'];
        $nombres = $_POST['nombre'] ?? [];
        $pesos = $_POST['peso'] ?? [];

        global $pdo;

        foreach ($nombres as $i => $nombre) {
            $nombre = trim($nombre);
            $peso = (float)$pesos[$i];

            if (!empty($nombre) && $peso > 0) {
                $stmt = $pdo->prepare("INSERT INTO Criterio (nombre, peso, id_concurso) VALUES (?, ?, ?)");
                $stmt->execute([$nombre, $peso, $id_concurso]);
            }
        }

        header("Location: index.php?page=admin_agregar_criterios&id_concurso=$id_concurso&success=criterios");
        exit;
    }
    public function guardarCriterioConcurso()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/CriterioConcurso.php';

        if (!$_POST) {
            header('Location: index.php?page=admin_gestionar_criterios');
            exit;
        }

        $id_concurso = (int)$_POST['id_concurso'];
        $id_criterio = (int)$_POST['id_criterio'];
        $puntaje = (float)$_POST['puntaje_maximo'];

        // Validaciones
        if ($id_concurso <= 0 || $id_criterio <= 0 || $puntaje <= 0) {
            header("Location: index.php?page=admin_gestionar_criterios&error=dato_invalido");
            exit;
        }

        try {
            if (CriterioConcurso::asignar($id_criterio, $id_concurso, $puntaje)) {
                header("Location: index.php?page=admin_gestionar_criterios&success=asignado&id_concurso=$id_concurso");
            } else {
                header("Location: index.php?page=admin_gestionar_criterios&error=no_guardado&id_concurso=$id_concurso");
            }
            exit;
        } catch (Exception $e) {
            error_log("Error al guardar criterio-concurso: " . $e->getMessage());
            header("Location: index.php?page=admin_gestionar_criterios&error=db&id_concurso=$id_concurso");
            exit;
        }
    }
    public function asignarCriteriosConcurso()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $id_criterio = $_GET['id_criterio'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null;

        // ✅ Incluir modelos necesarios
        require_once __DIR__ . '/../models/Criterio.php';
        require_once __DIR__ . '/../models/Concurso.php';
        require_once __DIR__ . '/../models/CriterioConcurso.php'; // Importante para los otros casos

        global $pdo;

        // Caso 1: Solo viene id_criterio → elegir concurso
        if ($id_criterio && !$id_concurso) {
            // Incluir modelos necesarios
            require_once __DIR__ . '/../models/Criterio.php';
            require_once __DIR__ . '/../models/Concurso.php';

            global $pdo;

            // Validar existencia del criterio
            $criterio = Criterio::porId($id_criterio);
            if (!$criterio) {
                header('Location: index.php?page=admin_gestionar_criterios&error=no_existe');
                exit;
            }

            // Listar concursos
            $concursos = Concurso::listar(); // Usa listar(), no todos()
            if (empty($concursos)) {
                header('Location: index.php?page=admin_gestion_concursos&error=sin_concursos');
                exit;
            }

            // ✅ Asegurar que $id_criterio sea una variable segura y esté disponible
            $id_criterio = (int)$id_criterio; // Convertir a entero para seguridad

            // Pasar todas las variables a la vista
            require_once __DIR__ . '/../views/admin/seleccionar_concurso.php';
            exit;
        }
        // Caso 2: Viene id_criterio e id_concurso → asignar puntaje
        if ($id_criterio && $id_concurso) {
            $criterio = Criterio::porId($id_criterio);

            if (!$criterio) {
                header("Location: index.php?page=admin_agregar_criterios&id_concurso=$id_concurso&error=criterio_no_existe");
                exit;
            }

            if (isset($_POST['puntaje'])) {
                $puntaje = (float)$_POST['puntaje'];

                if ($puntaje <= 0 || $puntaje > 100) {
                    header("Location: index.php?page=admin_asignar_criterios_concurso&id_concurso=$id_concurso&id_criterio=$id_criterio&error=puntaje_invalido");
                    exit;
                }

                try {
                    $stmt_check = $pdo->prepare("SELECT * FROM CriterioConcurso WHERE id_criterio = ? AND id_concurso = ?");
                    $stmt_check->execute([$id_criterio, $id_concurso]);

                    if ($stmt_check->rowCount() > 0) {
                        $pdo->prepare("UPDATE CriterioConcurso SET puntaje_maximo = ? WHERE id_criterio = ? AND id_concurso = ?")
                            ->execute([$puntaje, $id_criterio, $id_concurso]);
                    } else {
                        $pdo->prepare("INSERT INTO CriterioConcurso (id_criterio, id_concurso, puntaje_maximo) VALUES (?, ?, ?)")
                            ->execute([$id_criterio, $id_concurso, $puntaje]);
                    }

                    header("Location: index.php?page=admin_agregar_criterios&id_concurso=$id_concurso&success=guardado");
                    exit;
                } catch (Exception $e) {
                    error_log("Error al guardar criterio-concurso: " . $e->getMessage());
                    header("Location: index.php?page=admin_asignar_criterios_concurso&id_concurso=$id_concurso&id_criterio=$id_criterio&error=db");
                    exit;
                }
            }

            require_once __DIR__ . '/../views/admin/asignar_puntaje.php';
            exit;
        }

        // Caso 3: Solo id_concurso → mostrar gestión completa
        $id_concurso = $_GET['id_concurso'] ?? null;
        if (!$id_concurso) {
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }

        $concurso = Concurso::obtenerPorId($id_concurso);
        if (!$concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=invalido');
            exit;
        }

        $criterios_asignados = CriterioConcurso::porConcurso($id_concurso);
        $criterios_disponibles = CriterioConcurso::disponiblesParaAsignar($id_concurso);

        require_once __DIR__ . '/../views/admin/gestionar_criterios_concurso.php';
    }
    public function seleccionarConcurso()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        require_once __DIR__ . '/../models/Concurso.php';

        // Obtener todos los concursos
        $concursos = Concurso::listar();

        // Pasar a la vista
        require_once __DIR__ . '/../views/admin/seleccionar_concurso.php';
    }
    public function eliminarCriterioConcurso()
    {
        redirect_if_not_admin();
        $id_criterio = $_GET['id'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_criterio || !$id_concurso) {
            header('Location: index.php?page=admin_gestionar_criterios&error=no_datos');
            exit;
        }

        global $pdo;

        try {
            $stmt = $pdo->prepare("DELETE FROM CriterioConcurso WHERE id_criterio = ? AND id_concurso = ?");
            $stmt->execute([$id_criterio, $id_concurso]);

            header("Location: index.php?page=admin_gestionar_criterios&id_concurso=$id_concurso&success=eliminado");
            exit;
        } catch (Exception $e) {
            error_log("Error al eliminar criterio del concurso: " . $e->getMessage());
            header("Location: index.php?page=admin_gestionar_criterios&id_concurso=$id_concurso&error=db");
            exit;
        }
    }
}

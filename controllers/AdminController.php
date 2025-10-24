<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../models/Concurso.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../models/TipoDanza.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../helpers/functions.php';
require_once __DIR__ . '/../models/Concurso.php';



global $pdo;
class AdminController
{
    public function algunaVista()
    {
        $user = auth(); // Asegúrate de que esta función exista y devuelva los datos del usuario
        require_once __DIR__ . '/../views/admin/alguna_vista.php';
    }

    public function admin_dashboard()
    {
        $user = auth(); // Asegúrate de que esta función exista
        require_once __DIR__ . '/../views/admin/dashboard.php';
    }
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
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        // Inicializar variables para evitar "Undefined variable"
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;
        $editando_tipo = false;
        $editando_serie = false;
        $tipo_actual = null;
        $serie_actual = null;

        // Si se está editando un tipo
        if (isset($_GET['editar_tipo'])) {
            $id_tipo = (int)$_GET['editar_tipo'];
            require_once __DIR__ . '/../models/TipoDanza.php';
            $tipo_actual = TipoDanza::obtenerPorId($id_tipo);
            $editando_tipo = $tipo_actual !== false;
        }

        // Si se está editando una serie
        if (isset($_GET['editar_serie'])) {
            $id_serie = (int)$_GET['editar_serie'];
            require_once __DIR__ . '/../models/Serie.php';
            $serie_actual = Serie::obtenerPorId($id_serie);
            $editando_serie = $serie_actual !== false;
        }

        // Obtener listas
        require_once __DIR__ . '/../models/TipoDanza.php';
        require_once __DIR__ . '/../models/Serie.php';
        $tipos = TipoDanza::listar();
        $series = Serie::listarConTipo(); // Asegúrate de tener este método

        // Cargar vista
        require_once __DIR__ . '/../views/admin/gestionar_series.php';
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

        usort($conjuntos_globales, function ($a, $b) {
            return strcasecmp($a['nombre'], $b['nombre']);
        });

        // ✅ Usar el nuevo método que incluye serie y tipo
        $participaciones = ParticipacionConjunto::listarPorConcursoAdmin($id_concurso);
        foreach ($conjuntos_globales as &$c) {
            $c['nombre_normalizado'] = strtolower(
                preg_replace('/[\x{0300}-\x{036f}]/u', '', $c['nombre'])
            );
        }

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

        // Inicializar variables para evitar "Undefined variable"
        $error = $_GET['error'] ?? null;
        $success = $_GET['success'] ?? null;
        $editando = false;
        $conjunto_edit = null;

        // Si se está editando
        if (isset($_GET['editar']) && is_numeric($_GET['editar'])) {
            $editando = true;
            $id_conjunto = (int)$_GET['editar'];
            require_once __DIR__ . '/../models/Conjunto.php';
            $conjunto_edit = Conjunto::obtenerPorId($id_conjunto);
            if (!$conjunto_edit) {
                header("Location: index.php?page=admin_gestionar_conjuntos_globales&error=no_existe");
                exit;
            }
        }

        // Obtener listas
        require_once __DIR__ . '/../models/Serie.php';
        require_once __DIR__ . '/../models/Conjunto.php';
        $series = Serie::listar();
        $conjuntos = Conjunto::listar(); // ← ¡Debe devolver un array, incluso si está vacío!

        // Cargar vista
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

    private function normalizarTexto($str)
    {
        return strtolower(trim(preg_replace('/[\x{0300}-\x{036f}]/u', '', $str)));
    }


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

        require_once __DIR__ . '/../views/admin/crear_jurado.php';
    }
    public function gestionarJurados()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        $user = auth();

        $mostrarCredenciales = false;
        $credenciales = [];

        if (isset($_SESSION['mensaje_jurado'])) {
            $credenciales = $_SESSION['mensaje_jurado'];
            unset($_SESSION['mensaje_jurado']);
            $mostrarCredenciales = true;
        }

        $error = $_GET['error'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null;

        require_once __DIR__ . '/../models/Concurso.php';
        require_once __DIR__ . '/../models/Jurado.php';

        $concursos = Concurso::listar();

        if ($id_concurso) {
            // Usar el nuevo método del modelo para obtener jurados con criterios
            require_once __DIR__ . '/../models/JuradoCriterioConcurso.php';
            $jurados = JuradoCriterioConcurso::listarPorConcurso($id_concurso);
        } else {
            $jurados = Jurado::listar();
        }
        global $pdo;

        require_once __DIR__ . '/../views/admin/gestionar_jurados.php';
    }
    public function guardarJurado()
    {
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        redirect_if_not_admin();

        require_once __DIR__ . '/../helpers/functions.php';
        require_once __DIR__ . '/../models/Jurado.php';
        require_once __DIR__ . '/../config/database.php';

        // Asegúrate de incluir el modelo para la asignación de criterios
        require_once __DIR__ . '/../models/JuradoCriterioConcurso.php';

        global $pdo;

        if (ob_get_level()) ob_clean();

        error_log("🔧 Inicio: guardarJurado");

        if (!$_POST) {
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }

        $id_concurso = (int)$_POST['id_concurso'];
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $especialidad = trim($_POST['especialidad']);
        $años_experiencia = (int)$_POST['años_experiencia'];
        $usuario = trim($_POST['usuario']);
        $contrasena = trim($_POST['contrasena']); // Solo si se va a actualizar

        // Validaciones básicas
        if (!preg_match('/^\d{8}$/', $dni)) {
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=dni");
            exit;
        }

        if (empty($nombre) || empty($usuario)) {
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=datos");
            exit;
        }

        // --- CASO 1: JURADO NUEVO ---
        $stmt_check = $pdo->prepare("SELECT id_jurado FROM Jurado WHERE dni = ?");
        $stmt_check->execute([$dni]);
        $jurado = $stmt_check->fetch();

        if (!$jurado) {
            // Crear jurado + usuario + token
            if (Jurado::crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contrasena)) {
                $stmt = $pdo->prepare("SELECT id_jurado FROM Jurado WHERE dni = ?");
                $stmt->execute([$dni]);
                $jurado = $stmt->fetch();
            } else {
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=db");
                exit;
            }
        } else {
            // --- CASO 2: JURADO EXISTE ---
            // Actualizar datos (opcional)
            $update_jurado = $pdo->prepare("UPDATE Jurado SET nombre = ?, especialidad = ?, años_experiencia = ? WHERE dni = ?");
            $update_jurado->execute([$nombre, $especialidad, $años_experiencia, $dni]);

            // Opcional: actualizar contraseña si se proporcionó
            if (!empty($contrasena)) {
                $nueva_contra_hash = password_hash($contrasena, PASSWORD_DEFAULT);
                $update_user = $pdo->prepare("UPDATE Usuario SET contraseña = ? WHERE id_usuario = ?");
                $update_user->execute([$nueva_contra_hash, $jurado['id_jurado']]);
            }
        }

        $jurado_id = $jurado['id_jurado'];

        // === ASIGNAR JURADO AL CRITERIO DEL CONCURSO ===
        $id_criterio_concurso = (int)$_POST['id_criterio_concurso'];

        // Validar que el criterio pertenece al concurso
        $stmt_check = $pdo->prepare("
        SELECT id_criterio_concurso 
        FROM CriterioConcurso 
        WHERE id_criterio_concurso = ? AND id_concurso = ?
    ");
        $stmt_check->execute([$id_criterio_concurso, $id_concurso]);
        if ($stmt_check->rowCount() == 0) {
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=criterio_invalido");
            exit;
        }

        // Asignar jurado al criterio
        if (!JuradoCriterioConcurso::asignar($jurado_id, $id_criterio_concurso)) {
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=asignacion");
            exit;
        }

        // --- GENERAR TOKEN PARA ESTE CONCURSO ---
        // Verificar si ya tiene token para este concurso
        $check_token = $pdo->prepare("SELECT token FROM TokenAcceso WHERE id_concurso = ? AND id_jurado = ?");
        $check_token->execute([$id_concurso, $jurado_id]);
        $token_existente = $check_token->fetch();

        if ($token_existente) {
            // Reutilizar token existente
            $token = $token_existente['token'];
        } else {
            // Generar nuevo token
            $token = bin2hex(random_bytes(16));
        }

        // Duración personalizada
        $dias = (int)($_POST['dias'] ?? 0);
        $horas = (int)($_POST['horas'] ?? 0);
        $minutos = (int)($_POST['minutos'] ?? 0);
        $total_minutos = $dias * 1440 + $horas * 60 + $minutos;

        if ($total_minutos <= 0) {
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=duracion");
            exit;
        }

        $fecha_expiracion = date('Y-m-d H:i:s', time() + $total_minutos * 60);

        // Insertar o actualizar token para este concurso
        try {
            if (!$token_existente) {
                $sql_token = "INSERT INTO TokenAcceso (token, id_concurso, id_jurado, generado_por, fecha_generacion, fecha_expiracion) VALUES (?, ?, ?, ?, NOW(), ?)";
                $stmt_token = $pdo->prepare($sql_token);
                $stmt_token->execute([
                    $token,
                    $id_concurso,
                    $jurado_id,
                    $_SESSION['user']['id'],
                    $fecha_expiracion
                ]);
            } else {
                // Actualizar fecha de expiración
                $sql_update = "UPDATE TokenAcceso SET fecha_expiracion = ? WHERE id_concurso = ? AND id_jurado = ?";
                $pdo->prepare($sql_update)->execute([$fecha_expiracion, $id_concurso, $jurado_id]);
            }

            // Guardar credenciales en sesión
            $_SESSION['mensaje_jurado'] = [
                'usuario' => $usuario,
                'contrasena' => !empty($contrasena) ? $contrasena : '(no modificada)',
                'token' => $token
            ];

            // Redirigir
            header("Location: index.php?page=admin_gestion_jurados&id_concurso=$id_concurso");
            exit;
        } catch (Exception $e) {
            error_log("❌ Error al guardar token: " . $e->getMessage());
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=token_db");
            exit;
        }
    }
    public function verificarUsuario()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();
        ob_clean();

        $nombre_completo = $_GET['nombre'] ?? '';
        if (!$nombre_completo) {
            http_response_code(400);
            echo json_encode(['error' => 'Nombre requerido']);
            exit;
        }

        global $pdo;

        $partes = preg_split('/\s+/', trim($nombre_completo));
        if (count($partes) < 2) {
            echo json_encode(['usuario' => strtolower($partes[0])]);
            exit;
        }

        $normalizar = function ($str) {
            return strtolower(preg_replace('/[\x{0300}-\x{036f}]/u', '', $str));
        };

        $n = $normalizar($partes[0]);
        $a1 = $normalizar($partes[1]);
        $a2 = isset($partes[2]) ? $normalizar($partes[2]) : '';

        $intentos = [
            $n[0] . $a1,
            substr($n, 0, 2) . $a1,
            substr($n, 0, 3) . $a1,
            ($a2 ? $n[0] . $a2 : '')
        ];

        foreach ($intentos as $usuario) {
            if (!$usuario) continue;
            $check = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $check->execute([$usuario]);
            if ($check->rowCount() == 0) {
                echo json_encode(['usuario' => $usuario]);
                exit;
            }
        }

        for ($i = 1; $i <= 99; $i++) {
            $usuario = $intentos[0] . $i;
            $check = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $check->execute([$usuario]);
            if ($check->rowCount() == 0) {
                echo json_encode(['usuario' => $usuario]);
                exit;
            }
        }

        echo json_encode(['usuario' => $intentos[0] . rand(100, 999)]);
        exit;
    }
    public function buscarJuradoPorDni()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_admin();

        if (ob_get_level()) {
            ob_clean();
        }

        header('Content-Type: application/json; charset=utf-8');

        $dni = $_GET['dni'] ?? '';
        if (!preg_match('/^\d{8}$/', $dni)) {
            echo json_encode(['existe' => false]);
            exit;
        }

        global $pdo;
        $stmt = $pdo->prepare("
        SELECT 
            j.dni,
            j.nombre,
            j.años_experiencia,
            u.usuario,
            j.id_jurado
        FROM Jurado j
        JOIN Usuario u ON j.id_jurado = u.id_usuario
        WHERE j.dni = ?
    ");
        $stmt->execute([$dni]);
        $jurado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($jurado) {
            echo json_encode([
                'existe' => true,
                'dni' => $jurado['dni'],
                'nombre' => $jurado['nombre'],
                'años_experiencia' => (int)$jurado['años_experiencia'],
                'usuario' => $jurado['usuario']
                // ⚠️ Ya no incluimos 'especialidad'
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
    public function guardarCriteriosConcurso()
    {
        redirect_if_not_admin();
        $id_concurso = (int)($_POST['id_concurso'] ?? 0);
        if (!$id_concurso) {
            header("Location: index.php?page=admin_gestionar_criterios&error=dato_invalido");
            exit;
        }

        $puntajes = $_POST['puntajes'] ?? [];
        if (empty($puntajes)) {
            header("Location: index.php?page=admin_gestionar_criterios&id_concurso=$id_concurso&error=datos");
            exit;
        }

        require_once __DIR__ . '/../models/CriterioConcurso.php';
        $todos_guardados = true;

        foreach ($puntajes as $id_criterio => $puntaje) {
            $puntaje = (float)$puntaje;
            if ($puntaje <= 0 || $puntaje > 100) continue;

            if (!CriterioConcurso::asignar($id_criterio, $id_concurso, $puntaje)) {
                $todos_guardados = false;
            }
        }

        $mensaje = $todos_guardados ? 'asignado' : 'no_guardado';
        header("Location: index.php?page=admin_gestionar_criterios&id_concurso=$id_concurso&success=$mensaje");
        exit;
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

        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../models/Concurso.php';
        $concursos = Concurso::listar(); // Asegúrate de que esto devuelve un array

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

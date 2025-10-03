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

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
class AdminController
{
    // =============================
    // CONCURSO
    // =============================

    public function mostrarFormularioCrearConcurso()
    {
        redirect_if_not_admin();
        $editando = false;
        $page = 'admin_crear_concurso';
        require_once __DIR__ . '/../views/admin/gestion_concursos.php';
    }

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

    public function mostrarFormularioEditarConcurso()
    {
        redirect_if_not_admin();
        $id = $_GET['id'] ?? null;
        $concurso = Concurso::obtenerPorId($id);
        if (!$concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_existe');
            exit;
        }
        $editando = true;
        $page = 'admin_editar_concurso';
        require_once __DIR__ . '/../views/admin/gestion_concursos.php';
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

    // =============================
    // TIPOS DE DANZA + SERIES
    // =============================

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

    public function gestionarSeries()
    {
        redirect_if_not_admin();
        $this->gestionarSeriesYTpos();
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

    // =============================
    // CONJUNTOS
    // =============================

    public function gestionarConjuntos()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_conjuntos.php';
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

    public function mostrarFormularioEditarConjuntoGlobal()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        $id = $_GET['id'] ?? null;

        if (!$id) {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=no_id');
            exit;
        }

        $conjunto = Conjunto::obtenerPorId($id);

        if (!$conjunto) {
            header('Location: index.php?page=admin_gestionar_conjuntos_globales&error=no_existe');
            exit;
        }

        require_once __DIR__ . '/../views/admin/gestion_conjuntos_globales.php';
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

    // =============================
    // JURADOS
    // =============================

    public function gestionarJurados()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_jurados.php';
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

    // --- FUNCIONES AUXILIARES ---
    private function generarUsuario($nombre_completo)
    {
        $nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre_completo);
        $partes = preg_split('/\s+/', trim($nombre));

        if (count($partes) < 2) {
            return strtolower($partes[0]);
        }

        $inicial = mb_strtolower($partes[0][0]);
        $apellido = mb_strtolower($partes[1]);

        $usuario = $inicial . $apellido;

        global $pdo;
        $i = '';
        while (true) {
            $stmt = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
            $stmt->execute([$usuario . $i]);
            if ($stmt->rowCount() == 0) {
                return $usuario . $i;
            }
            $i++;
        }
    }

    private function generarContrasenaSegura($longitud = 10)
    {
        $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $minusculas = 'abcdefghijklmnopqrstuvwxyz';
        $numeros = '0123456789';
        $especiales = '!@#$%&*';

        $password = '';
        $password .= $mayusculas[random_int(0, strlen($mayusculas) - 1)];
        $password .= $minusculas[random_int(0, strlen($minusculas) - 1)];
        $password .= $numeros[random_int(0, strlen($numeros) - 1)];
        $password .= $especiales[random_int(0, strlen($especiales) - 1)];

        $todos = $mayusculas . $minusculas . $numeros . $especiales;
        for ($i = 4; $i < $longitud; $i++) {
            $password .= $todos[random_int(0, strlen($todos) - 1)];
        }

        return str_shuffle($password);
    }

    // --- CRITERIOS ---
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
        $id_concurso = (int)$_POST['id_concurso'];
        $id_criterio = (int)$_POST['id_criterio'];
        $puntaje = (float)$_POST['puntaje_maximo'];

        global $pdo;

        try {
            $stmt = $pdo->prepare("
            INSERT INTO CriterioConcurso (id_criterio, id_concurso, puntaje_maximo)
            VALUES (?, ?, ?)
            ON DUPLICATE KEY UPDATE puntaje_maximo = ?
        ");
            $stmt->execute([$id_criterio, $id_concurso, $puntaje, $puntaje]);

            header("Location: index.php?page=admin_agregar_criterios&id_concurso=$id_concurso&success=asignado");
            exit;
        } catch (Exception $e) {
            error_log("Error al asignar criterio: " . $e->getMessage());
            header("Location: index.php?page=admin_agregar_criterios&id_concurso=$id_concurso&error=db");
            exit;
        }
    }
}

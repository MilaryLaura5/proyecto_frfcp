<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/Concurso.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../models/TipoDanza.php';
require_once __DIR__ . '/../config/database.php';

class AdminController
{
    // Mostrar formulario para crear concurso
    public function mostrarFormularioCrearConcurso()
    {
        redirect_if_not_admin();
        $editando = false;
        $page = 'admin_crear_concurso';
        require_once __DIR__ . '/../views/admin/gestion_concursos.php';
    }

    // Procesar el formulario
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

    // Mostrar formulario de edición
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

    // Procesar edición
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

    // Eliminar concurso
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

    /**
     * Muestra la vista unificada de tipos de danza y series
     */
    public function gestionarSeriesYTpos()
    {
        redirect_if_not_admin();

        // Asegurar conexión
        global $pdo;

        // Cargar la vista
        require_once __DIR__ . '/../views/admin/gestion_series.php';
    }

    // --- TIPOS DE DANZA ---

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

        // Pasar a la misma vista principal
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

    // --- SERIES ---
    public function gestionarSeries()
    {
        redirect_if_not_admin();
        $this->gestionarSeriesYTpos(); // Reutiliza el método unificado
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
        require_once __DIR__ . '/../views/admin/gestion_series.php'; // Usa la misma vista unificada
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
    // CONJUNTOS (GLOBALES + PARTICIPACIONES)
    // =============================

    public function gestionarConjuntos()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_conjuntos.php';
    }

    // --- GESTIÓN DE PARTICIPACIONES EN CONCURSO ---

    /**
     * Asignar un conjunto existente al concurso
     */
    public function asignarConjuntoAConcurso()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        if ($_POST) {
            $id_conjunto = (int)$_POST['id_conjunto'];
            $id_concurso = (int)$_POST['id_concurso'];
            $orden_presentacion = (int)$_POST['orden_presentacion'];

            // Validación básica
            if ($id_conjunto <= 0 || $id_concurso <= 0 || $orden_presentacion <= 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=vacios");
                exit;
            }

            // Verificar si ya existe ese orden en el concurso
            global $pdo;
            $check = $pdo->prepare("SELECT COUNT(*) FROM ParticipacionConjunto 
                                WHERE id_concurso = ? AND orden_presentacion = ?");
            $check->execute([$id_concurso, $orden_presentacion]);
            if ($check->fetchColumn() > 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=duplicado");
                exit;
            }

            // Intentar agregar participación
            if (ParticipacionConjunto::agregar($id_conjunto, $id_concurso, $orden_presentacion)) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=asignado");
            } else {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
            }
            exit;
        }
    }

    /**
     * Eliminar una participación (no elimina el conjunto global)
     */
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

        // Verificar que pertenezca al concurso
        $stmt = $pdo->prepare("SELECT id_participacion FROM ParticipacionConjunto 
                           WHERE id_participacion = ? AND id_concurso = ?");
        $stmt->execute([$id_participacion, $id_concurso]);

        if ($stmt->rowCount() == 0) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=no_permiso");
            exit;
        }

        // Eliminar participación
        $delete = $pdo->prepare("DELETE FROM ParticipacionConjunto WHERE id_participacion = ?");
        if ($delete->execute([$id_participacion])) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=eliminado");
        } else {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
        }
        exit;
    }

    // --- IMPORTAR CSV (actualizado) ---

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

            // Buscar si ya existe el conjunto global por nombre y serie
            $stmt = $pdo->prepare("SELECT id_conjunto FROM Conjunto WHERE nombre = ? AND id_serie = ?");
            $stmt->execute([$nombre, $id_serie]);
            $row = $stmt->fetch();

            if ($row) {
                $id_conjunto = $row['id_conjunto'];
            } else {
                // Si no existe, crearlo
                $insert = $pdo->prepare("INSERT INTO Conjunto (nombre, id_serie) VALUES (?, ?)");
                if ($insert->execute([$nombre, $id_serie])) {
                    $id_conjunto = $pdo->lastInsertId();
                } else {
                    $errores[] = "Error al crear conjunto: $nombre";
                    continue;
                }
            }

            // Verificar si ya está asignado con ese orden
            $check = $pdo->prepare("SELECT COUNT(*) FROM ParticipacionConjunto 
                                WHERE id_concurso = ? AND orden_presentacion = ?");
            $check->execute([$id_concurso, $orden_presentacion]);
            if ($check->fetchColumn() > 0) {
                $errores[] = "Orden duplicado: $orden_presentacion - $nombre";
                continue;
            }

            // Asignar al concurso
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

    // --- GESTIÓN GLOBAL DE CONJUNTOS ---

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
                // Aquí capturamos el caso de duplicado
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

        // Cargar la vista principal (que ahora detectará que está en modo edición)
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

            // 🔍 Verificar si YA existe el conjunto con ese nombre (independiente de mayúsculas/minúsculas)
            $check = $pdo->prepare("SELECT id_conjunto FROM Conjunto WHERE LOWER(nombre) = LOWER(?) AND id_serie = ?");
            $check->execute([$nombre, $id_serie]);
            if ($check->fetch()) {
                $errores[] = "Ya existe: '$nombre' en esa serie";
                continue;
            }

            // ✅ Intentar crear solo si no existe
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

    //JURADOS
    public function gestionarJurados()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_jurados.php';
    }

    public function crearJurado()
    {
        redirect_if_not_admin();
        $id_concurso = $_GET['id_concurso'] ?? null;

        // Aquí puedes mostrar un formulario o generar directamente
        // Por ahora, redirigimos al formulario de creación
        // O implementamos generación automática (ver abajo)
    }

    public function crearFormularioJurado()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/crear_jurado.php';
    }

    public function guardarJurado()
    {
        // Asegurar sesión iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Jurado.php';
        require_once __DIR__ . '/../config/database.php';
        global $pdo;

        if (!$_POST) {
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }

        $id_concurso = (int)$_POST['id_concurso'];
        $dni = trim($_POST['dni']);
        $nombre = trim($_POST['nombre']);
        $usuario = trim($_POST['usuario']);
        $especialidad = trim($_POST['especialidad']);
        $años_experiencia = (int)$_POST['años_experiencia'];

        // Validaciones
        if (!preg_match('/^\d{8}$/', $dni)) {
            error_log("❌ DNI inválido: $dni");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=dni");
            exit;
        }

        if (empty($nombre) || empty($usuario)) {
            error_log("❌ Datos incompletos: nombre='$nombre', usuario='$usuario'");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=datos");
            exit;
        }

        // Contraseña temporal
        $contrasena_temporal = $_POST['contrasena_temporal'] ?? '';
        $contraseña = !empty($contrasena_temporal) ? $contrasena_temporal : 'temporal123';

        // Crear jurado (incluye Usuario)
        if (Jurado::crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contraseña)) {
            // Buscar id_jurado recién creado
            $stmt = $pdo->prepare("SELECT id_jurado FROM Jurado WHERE dni = ?");
            $stmt->execute([$dni]);
            $jurado = $stmt->fetch();

            if (!$jurado) {
                error_log("❌ Jurado creado pero no encontrado por DNI: $dni");
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=no_encontrado");
                exit;
            }

            // Obtener fecha_fin del concurso
            $stmt_concurso = $pdo->prepare("SELECT fecha_fin FROM Concurso WHERE id_concurso = ?");
            $stmt_concurso->execute([$id_concurso]);
            $concurso = $stmt_concurso->fetch();

            if (!$concurso) {
                error_log("❌ Concurso no encontrado: $id_concurso");
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=concurso");
                exit;
            }

            // Generar token único
            $token = bin2hex(random_bytes(16));
            $fecha_expiracion = $concurso['fecha_fin'];

            // Insertar token
            $sql_token = "INSERT INTO TokenAcceso 
                      (token, id_concurso, id_jurado, generado_por, fecha_generacion, fecha_expiracion, usado)
                      VALUES (?, ?, ?, ?, NOW(), ?, 0)";

            $stmt_token = $pdo->prepare($sql_token);
            $resultado = $stmt_token->execute([
                $token,
                $id_concurso,
                $jurado['id_jurado'],
                $_SESSION['user']['id'],
                $fecha_expiracion
            ]);

            if ($resultado) {
                error_log("✅ Jurado y token creados correctamente");

                // ✅ Guardar token en sesión para mostrar después
                $_SESSION['mensaje_token'] = $token;

                // Redirigir limpio (sin token en URL)
                header("Location: index.php?page=admin_gestion_jurados&id_concurso=$id_concurso");
            } else {
                $errorInfo = $stmt_token->errorInfo();
                error_log("❌ Error al insertar token: " . print_r($errorInfo, true));
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=token_db");
            }
        } else {
            error_log("❌ Fallo al crear jurado o usuario");
            header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=db");
        }
        exit;
    }
}

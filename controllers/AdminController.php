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

            // Validaciones básicas
            if (empty($nombre) || empty($fecha_inicio) || empty($fecha_fin)) {
                header('Location: index.php?page=admin_gestion_concursos&error=vacios');
                exit;
            }

            if ($fecha_inicio >= $fecha_fin) {
                header('Location: index.php?page=admin_gestion_concursos&error=fechas');
                exit;
            }

            // Intentar registrar el concurso
            if (Concurso::crear($nombre, $fecha_inicio, $fecha_fin)) {
                header('Location: index.php?page=admin_gestion_concursos&success=1');
                exit;
            } else {
                header('Location: index.php?page=admin_gestion_concursos&error=db');
                exit;
            }
        }
    }

    //Mostrar formulario de edición
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


    //Procesar edición
    public function actualizarConcurso()
    {
        redirect_if_not_admin();
        if ($_POST) {
            $id = $_POST['id_concurso'];
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
                exit;
            } else {
                header("Location: index.php?page=admin_editar_concurso&id=$id&error=db");
                exit;
            }
        }
    }

    //Eliminar concurso
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

    public function gestionarSeries()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_series.php';
    }


    // SERIES
    public function crearSerie()
    {
        redirect_if_not_admin();

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
    }

    public function actualizarSerie()
    {
        redirect_if_not_admin();

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

        $id = $_GET['id'] ?? null;

        if ($id && Serie::eliminar($id)) {
            header('Location: index.php?page=admin_gestion_series&success=eliminado');
        } else {
            header('Location: index.php?page=admin_gestion_series&error=db');
        }
        exit;
    }

    public function gestionarTiposYSeries()
    {
        redirect_if_not_admin();

        // ← Asegúrate de cargar los modelos necesarios
        require_once __DIR__ . '/../models/TipoDanza.php';
        require_once __DIR__ . '/../models/Serie.php';
        require_once __DIR__ . '/../config/database.php'; // ← Importante: conexión
        global $pdo; // ← Necesario para consultas en la vista

        // Cargar la vista
        require_once __DIR__ . '/../views/admin/gestion_tipos_y_series.php';
    }

    // TIPOS DE DANZA
    public function crearTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        if ($_POST) {
            $nombre_tipo = trim($_POST['nombre_tipo']);

            if (empty($nombre_tipo)) {
                header('Location: index.php?page=admin_gestion_tipos_series&error=nombre_vacio');
                exit;
            }

            // Validar formato (opcional)
            if (!preg_match('/^[a-z_]+$/', $nombre_tipo)) {
                header('Location: index.php?page=admin_gestion_tipos_series&error=formato_invalido');
                exit;
            }

            if (TipoDanza::crear($nombre_tipo)) {
                header('Location: index.php?page=admin_gestion_tipos_series&success=tipo_creado');
            } else {
                header('Location: index.php?page=admin_gestion_tipos_series&error=db');
            }
            exit;
        }
    }

    public function actualizarTipoDanza()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/TipoDanza.php';

        if ($_POST) {
            $id = (int)$_POST['id_tipo'];
            $nombre_tipo = trim($_POST['nombre_tipo']);

            if (TipoDanza::actualizar($id, $nombre_tipo)) {
                header('Location: index.php?page=admin_gestion_tipos_series&success=tipo_editado');
            } else {
                header('Location: index.php?page=admin_gestion_tipos_series&error=db');
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
            header('Location: index.php?page=admin_gestion_tipos_series&success=tipo_eliminado');
        } else {
            header('Location: index.php?page=admin_gestion_tipos_series&error=tiene_series');
        }
        exit;
    }

    // CONJUNTOS
    public function gestionarConjuntos()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_conjuntos.php';
    }

    public function crearConjunto()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        if ($_POST) {
            $nombre = trim($_POST['nombre']);
            $id_serie = (int)$_POST['id_serie'];
            $id_concurso = (int)$_POST['id_concurso']; // ← Viene del formulario
            $orden_presentacion = (int)$_POST['orden_presentacion'];
            $numero_oficial = (int)$_POST['numero_oficial'];

            if (empty($nombre) || $id_serie <= 0 || $id_concurso <= 0 || $orden_presentacion <= 0 || $numero_oficial <= 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=vacios");
                exit;
            }

            // Verificar duplicado por número oficial en este concurso
            global $pdo;
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Conjunto WHERE numero_oficial = ? AND id_concurso = ?");
            $stmt->execute([$numero_oficial, $id_concurso]);
            if ($stmt->fetchColumn() > 0) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=duplicado");
                exit;
            }

            if (Conjunto::crear($nombre, $id_serie, $id_concurso, $orden_presentacion, $numero_oficial)) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=1");
            } else {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
            }
            exit;
        }
    }

    public function mostrarFormularioEditarConjunto()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/gestion_conjuntos.php';
    }

    public function actualizarConjunto()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        if ($_POST) {
            $id = (int)$_POST['id_conjunto'];
            $nombre = trim($_POST['nombre']);
            $id_serie = (int)$_POST['id_serie'];
            $orden_presentacion = (int)$_POST['orden_presentacion'];
            $numero_oficial = (int)$_POST['numero_oficial'];
            $id_concurso = (int)$_POST['id_concurso']; // ← Importante: mantener el contexto

            // Opcional: verificar que el conjunto pertenezca al concurso (seguridad)
            global $pdo;
            $check = $pdo->prepare("SELECT id_concurso FROM Conjunto WHERE id_conjunto = ?");
            $check->execute([$id]);
            $conjunto = $check->fetch();

            if (!$conjunto || $conjunto['id_concurso'] != $id_concurso) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=no_permiso");
                exit;
            }

            if (Conjunto::editar($id, $nombre, $id_serie, $orden_presentacion, $numero_oficial)) {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=editado");
            } else {
                header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=db");
            }
            exit;
        }
    }

    public function eliminarConjunto()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

        $id = $_GET['id'] ?? null;
        $id_concurso = $_GET['id_concurso'] ?? null; // ← Recuperar para redirigir correctamente

        if (!$id_concurso) {
            header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
            exit;
        }

        // Verificar que el conjunto pertenezca al concurso
        global $pdo;
        $check = $pdo->prepare("SELECT id_concurso FROM Conjunto WHERE id_conjunto = ?");
        $check->execute([$id]);
        $conjunto = $check->fetch();

        if (!$conjunto || $conjunto['id_concurso'] != $id_concurso) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=no_permiso");
            exit;
        }

        if ($id && Conjunto::eliminar($id)) {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&success=eliminado");
        } else {
            header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=calificado");
        }
        exit;
    }

    public function importarConjuntosCSV()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../models/Conjunto.php';

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

        // Saltar encabezado
        fgetcsv($handle);

        $errores = [];
        $importados = 0;

        while (($data = fgetcsv($handle)) !== false) {
            $nombre = trim($data[0] ?? '');
            $id_serie = (int)($data[1] ?? 0);
            $orden_presentacion = (int)($data[2] ?? 0);
            $numero_oficial = (int)($data[3] ?? 0);

            if (empty($nombre) || $id_serie <= 0 || $orden_presentacion <= 0 || $numero_oficial <= 0) {
                $errores[] = "Datos inválidos: $nombre";
                continue;
            }

            // Verificar duplicado
            global $pdo;
            $check = $pdo->prepare("SELECT COUNT(*) FROM Conjunto WHERE numero_oficial = ? AND id_concurso = ?");
            $check->execute([$numero_oficial, $id_concurso]);
            if ($check->fetchColumn() > 0) {
                $errores[] = "Número oficial duplicado: $numero_oficial";
                continue;
            }

            if (Conjunto::crear($nombre, $id_serie, $id_concurso, $orden_presentacion, $numero_oficial)) {
                $importados++;
            } else {
                $errores[] = "Error al crear: $nombre";
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
    }

    public function crearFormularioJurado()
    {
        redirect_if_not_admin();
        require_once __DIR__ . '/../views/admin/crear_jurado.php';
    }

    public function guardarJurado()
    {
        redirect_if_not_admin();

        require_once __DIR__ . '/../models/Jurado.php';
        require_once __DIR__ . '/../config/database.php';
        global $pdo;

        if ($_POST) {
            $id_concurso = (int)$_POST['id_concurso'];
            $dni = trim($_POST['dni']);
            $nombre = trim($_POST['nombre']);
            $usuario = trim($_POST['usuario']);
            $especialidad = trim($_POST['especialidad']);
            $años_experiencia = (int)$_POST['años_experiencia'];

            // Validaciones
            if (!preg_match('/^\d{8}$/', $dni)) {
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=dni");
                exit;
            }

            if (empty($nombre) || empty($usuario) || !filter_var($usuario, FILTER_VALIDATE_EMAIL)) {
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=datos");
                exit;
            }

            // Contraseña temporal
            $contraseña = 'temporal123';

            // Crear jurado (incluye Usuario)
            if (Jurado::crear($dni, $nombre, $especialidad, $años_experiencia, $usuario, $contraseña)) {
                // Buscar id_jurado recién creado
                $stmt = $pdo->prepare("SELECT id_jurado FROM Jurado WHERE dni = ?");
                $stmt->execute([$dni]);
                $jurado = $stmt->fetch();

                if ($jurado) {
                    // Obtener fecha_fin del concurso → para expiración del token
                    $stmt_concurso = $pdo->prepare("SELECT fecha_fin FROM Concurso WHERE id_concurso = ?");
                    $stmt_concurso->execute([$id_concurso]);
                    $concurso = $stmt_concurso->fetch();

                    if (!$concurso) {
                        header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=concurso");
                        exit;
                    }

                    $fecha_expiracion = $concurso['fecha_fin']; // ✅ El token expira cuando acaba el concurso

                    // Generar token único
                    $token = bin2hex(random_bytes(16));

                    // Insertar token en TokenAcceso
                    $sql_token = "INSERT INTO TokenAcceso 
                              (token, id_concurso, id_jurado, generado_por, fecha_generacion, fecha_expiracion, usado)
                              VALUES (?, ?, ?, ?, NOW(), ?, 0)";

                    $stmt_token = $pdo->prepare($sql_token);
                    $resultado = $stmt_token->execute([
                        $token,
                        $id_concurso,
                        $jurado['id_jurado'],
                        $_SESSION['user']['id'], // ID del admin
                        $fecha_expiracion
                    ]);

                    if ($resultado) {
                        // ✅ Éxito: redirigir con mensaje y token
                        header("Location: index.php?page=admin_gestion_jurados&id_concurso=$id_concurso&success=token&token=$token");
                        exit;
                    } else {
                        // Error al guardar token
                        error_log("Error al insertar token para jurado DNI: $dni");
                        header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=token_db");
                        exit;
                    }
                } else {
                    // No se encontró el jurado después de crearlo
                    error_log("Jurado creado pero no encontrado por DNI: $dni");
                    header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=no_encontrado");
                    exit;
                }
            } else {
                // Error al crear jurado (probablemente duplicado o fallo interno)
                header("Location: index.php?page=admin_crear_jurado&id_concurso=$id_concurso&error=db");
                exit;
            }
        } else {
            // Acceso inválido (no es POST)
            header('Location: index.php?page=admin_gestion_concursos');
            exit;
        }
    }
}

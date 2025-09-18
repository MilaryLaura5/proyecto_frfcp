<?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/Concurso.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../models/Serie.php';
require_once __DIR__ . '/../models/TipoDanza.php';

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
}

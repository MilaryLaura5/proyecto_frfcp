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

    //SERIES
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
}

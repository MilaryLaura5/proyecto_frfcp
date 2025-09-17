    <?php
// controllers/AdminController.php

require_once __DIR__ . '/../models/Concurso.php';
require_once __DIR__ . '/../helpers/auth.php';

class AdminController {

    // Mostrar formulario para crear concurso
    public function mostrarFormularioCrearConcurso() {
        redirect_if_not_admin();

        $editando = false;
        $page = 'admin_crear_concurso';

        require_once __DIR__ . '/../views/admin/gestion_concursos.php';
    }

    // Procesar el formulario
    public function crearConcurso() {
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
    public function mostrarFormularioEditarConcurso() {
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
    public function actualizarConcurso() {
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
    public function eliminarConcurso() {
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
}
?>
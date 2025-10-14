<?php
// controllers/PresidenteController.php - VERSIÓN CORREGIDA CON REQUIRE

require_once __DIR__ . '/../models/Presidente.php';

class PresidenteController
{
    private $presidenteModel;

    public function __construct()
    {
        global $pdo;
        $this->presidenteModel = new Presidente($pdo);
    }

    public function seleccionarConcurso()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Presidente') {
            header('Location: index.php?page=login');
            exit;
        }

        $concursos = $this->presidenteModel->getAllConcursos();
        require_once __DIR__ . '/../views/presidente/seleccionar_concursos.php';
    }

    public function revisarResultados()
    {
        // VERIFICAR AUTENTICACIÓN
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Presidente') {
            header('Location: index.php?page=login');
            exit;
        }

        // VALIDACIÓN ROBUSTA DE ENTRADA
        $id_concurso = filter_var($_GET['id_concurso'] ?? 0, FILTER_VALIDATE_INT);

        if (!$id_concurso || $id_concurso < 1) {
            $_SESSION['error'] = "ID de concurso no válido";
            header('Location: index.php?page=presidente_seleccionar_concurso');
            exit;
        }

        // VERIFICAR ACCESO AL CONCURSO
        if (!$this->verificarAccesoConcurso($id_concurso)) {
            $_SESSION['error'] = "No tiene acceso a este concurso";
            header('Location: index.php?page=presidente_seleccionar_concurso');
            exit;
        }

        // Obtener resultados
        $resultados = $this->presidenteModel->getResultadosFinales($id_concurso);
        $criterios = $this->presidenteModel->getCriteriosByConcurso($id_concurso);

        // CALCULAR ESTADÍSTICAS MEJORADAS
        $estadisticas = [
            'total_conjuntos' => count($resultados),
            'calificados' => 0,
            'pendientes' => 0,
            'promedio_general' => 0,
            'puntaje_maximo' => 0
        ];

        $suma_puntajes = 0;
        foreach ($resultados as $resultado) {
            if ($resultado['calificaciones_count'] > 0) {
                $estadisticas['calificados']++;
                $suma_puntajes += $resultado['promedio_final'];
                if ($resultado['promedio_final'] > $estadisticas['puntaje_maximo']) {
                    $estadisticas['puntaje_maximo'] = $resultado['promedio_final'];
                }
            } else {
                $estadisticas['pendientes']++;
            }
        }

        $estadisticas['promedio_general'] = $estadisticas['calificados'] > 0 ?
            round($suma_puntajes / $estadisticas['calificados'], 2) : 0;

        // Pasar datos a la vista
        require_once __DIR__ . '/../views/presidente/revisar_resultados.php';
    }

    public function generarReporte()
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Presidente') {
            header('Location: index.php?page=login');
            exit;
        }

        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
            exit;
        }

        header("Location: generar_pdf_real.php?id_concurso={$id_concurso}");
        exit;
    }

    // MÉTODO DE VERIFICACIÓN DE ACCESO
    private function verificarAccesoConcurso($id_concurso)
    {
        // Por ahora retorna true, puedes expandir esta lógica según tus necesidades
        // Ejemplo: verificar si el presidente tiene permisos para este concurso específico
        return true;
    }
}

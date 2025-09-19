<?php
require_once __DIR__ . '/../models/Presidente.php';
require_once __DIR__ . '/../helpers/auth.php';

class PresidenteController
{

    private $presidenteModel;

    public function __construct()
    {
        global $pdo;
        $this->presidenteModel = new Presidente($pdo);
    }

    // Dashboard del presidente
    public function dashboard()
    {
        redirect_if_not_presidente();
        $user = auth();
        require_once __DIR__ . '/../views/presidente/dashboard.php';
    }

    // Seleccionar concurso
    public function seleccionarConcurso()
    {
        redirect_if_not_presidente();
        $concursos = $this->presidenteModel->getAllConcursos();
        $error = $_GET['error'] ?? null;
        require_once __DIR__ . '/../views/presidente/seleccionar_concursos.php';
    }

    // Revisar resultados finales
    public function revisarResultados()
    {
        redirect_if_not_presidente();

        $id_concurso = $_GET['id_concurso'] ?? null;
        if (!$id_concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
            exit;
        }

        $resultados = $this->presidenteModel->getResultadosFinales($id_concurso);
        $criterios = $this->presidenteModel->getCriteriosByConcurso($id_concurso);

        if (empty($resultados)) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=sin_resultados');
            exit;
        }

        require_once __DIR__ . '/../views/presidente/revisar_resultados.php';
    }

    // Generar reporte oficial
    public function generarReporte()
    {
        redirect_if_not_presidente();

        $id_concurso = $_GET['id_concurso'] ?? null;
        if (!$id_concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
            exit;
        }

        $resultados = $this->presidenteModel->getResultadosFinales($id_concurso);
        $criterios = $this->presidenteModel->getCriteriosByConcurso($id_concurso);
        $user = auth();

        if (empty($resultados)) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=sin_resultados');
            exit;
        }

        // Simular generación de PDF
        $nombre_archivo = "reporte_oficial_{$id_concurso}_" . date('Ymd_His') . ".pdf";
        $ruta_archivo = "uploads/reportes/" . $nombre_archivo;

        if (!file_exists('uploads/reportes')) {
            mkdir('uploads/reportes', 0777, true);
        }

        file_put_contents($ruta_archivo, "REPORTE OFICIAL\nConcurso ID: {$id_concurso}\nGenerado por: {$user['usuario']}");

        $_SESSION['success_reporte'] = "✅ Reporte generado correctamente: <strong>{$nombre_archivo}</strong>";
        $_SESSION['reporte_path'] = $ruta_archivo;

        require_once __DIR__ . '/../views/presidente/generar_reporte.php';
    }
}

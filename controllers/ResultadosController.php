<?php
// controllers/ResultadosController.php

require_once __DIR__ . '/../models/Presidente.php';
require_once __DIR__ . '/../helpers/auth.php';

class ResultadosController
{
    private $presidente;

    public function __construct()
    {
        global $pdo;
        $this->presidente = new Presidente($pdo);
    }

    // Mostrar panel en vivo
    public function panelEnVivo()
    {

        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            die("Concurso no especificado.");
        }

        global $pdo;

        // Obtener nombre del concurso
        $stmt = $pdo->prepare("SELECT nombre FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso) {
            die("Concurso no encontrado.");
        }

        require_once __DIR__ . '/../views/resultados/panel_en_vivo.php';
    }

    // API: Obtener resultados actualizados (para AJAX)
    public function obtenerResultadosAPI()
    {
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            http_response_code(400);
            echo json_encode(['error' => 'Falta id_concurso']);
            exit;
        }

        $resultados = $this->presidente->getResultadosFinales($id_concurso);

        header('Content-Type: application/json');
        echo json_encode([
            'timestamp' => date('H:i:s'), // Ahora usará la zona correcta
            'resultados' => $resultados
        ]);
        exit;
    }
}

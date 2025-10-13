<?php
// controllers/PresidenteController.php

require_once __DIR__ . '/../models/Presidente.php';
require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../tcpdf/tcpdf.php';

class PresidenteController
{
    private $presidente;
    private $user;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        redirect_if_not_presidente(); // Asegura que solo el presidente acceda
        $this->user = $_SESSION['user'];
        $this->presidente = new Presidente();
    }

    // Mostrar lista de concursos para seleccionar
    public function seleccionarConcurso()
    {
        $concursos = $this->presidente->getAllConcursos();

        require_once __DIR__ . '/../views/presidente/seleccionar_concurso.php';
    }

    // Ver resultados finales de un concurso
    public function verResultados()
    {
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
            exit;
        }

        // Datos del concurso
        global $pdo;
        $stmt = $pdo->prepare("SELECT nombre FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_existe');
            exit;
        }

        // Resultados finales
        $resultados = $this->presidente->getResultadosFinales($id_concurso);

        // Criterios del concurso
        $criterios = $this->presidente->getCriteriosByConcurso($id_concurso);

        require_once __DIR__ . '/../views/presidente/resultados.php';
    }

    // Ver resultados por serie
    public function verResultadosPorSerie()
    {
        $id_concurso = $_GET['id_concurso'] ?? null;

        if (!$id_concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
            exit;
        }

        global $pdo;

        // Datos del concurso
        $stmt = $pdo->prepare("SELECT nombre FROM Concurso WHERE id_concurso = ?");
        $stmt->execute([$id_concurso]);
        $concurso = $stmt->fetch();

        if (!$concurso) {
            header('Location: index.php?page=presidente_seleccionar_concurso&error=no_existe');
            exit;
        }

        // Resultados por serie
        $resultados = $this->presidente->getResultadosPorSerie($id_concurso);

        // Agrupar por serie
        $resultados_por_serie = [];
        foreach ($resultados as $r) {
            $id_serie = $r['id_serie'];
            if (!isset($resultados_por_serie[$id_serie])) {
                $resultados_por_serie[$id_serie] = [
                    'numero_serie' => $r['numero_serie'],
                    'nombre_tipo' => $r['nombre_tipo'],
                    'conjuntos' => []
                ];
            }
            $resultados_por_serie[$id_serie]['conjuntos'][] = $r;
        }

        require_once __DIR__ . '/../views/presidente/resultados_por_serie.php';
    }
}

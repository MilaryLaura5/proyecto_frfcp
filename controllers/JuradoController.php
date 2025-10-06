<?php

class JuradoController
{
    public function calificar()
    {
        require_once __DIR__ . '/../helpers/auth.php';
        redirect_if_not_jurado();

        $user = $_SESSION['user'];
        $id_participacion = $_GET['id'] ?? null;

        if (!$id_participacion || !is_numeric($id_participacion)) {
            die("Conjunto no especificado o inválido.");
        }

        // Cargar modelos
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';
        require_once __DIR__ . '/../models/CriterioConcurso.php';
        require_once __DIR__ . '/../models/Calificacion.php';

        // Obtener datos del conjunto
        $conjunto = ParticipacionConjunto::obtenerPorId($id_participacion);
        if (!$conjunto) {
            die("Conjunto no encontrado en esta participación.");
        }

        // Verificar si ya fue calificado
        $calificacion = Calificacion::porJuradoYParticipacion($id_participacion, $user['id']);

        // Obtener criterios del concurso
        $criterios = CriterioConcurso::porConcurso($user['id_concurso']);
        if (empty($criterios)) {
            die("No hay criterios definidos para este concurso.");
        }

        // Pasar todo a la vista
        require_once __DIR__ . '/../views/jurado/calificar.php';
    }
    public function evaluar()
    {
        require_once __DIR__ . '/../helpers/auth.php';
        redirect_if_not_jurado();

        $user = $_SESSION['user'];
        $id_concurso = $user['id_concurso'];

        // Cargar modelo
        require_once __DIR__ . '/../models/ParticipacionConjunto.php';

        // Obtener conjuntos asignados al concurso
        $conjuntos = ParticipacionConjunto::porConcurso($id_concurso);

        // Pasar datos a la vista
        require_once __DIR__ . '/../views/jurado/evaluar.php';
    }
}

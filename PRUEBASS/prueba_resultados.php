<?php
// prueba_resultados.php - Prueba directa del modelo
session_start();
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Presidente.php';

echo "<h1>🧪 PRUEBA DIRECTA RESULTADOS</h1>";

global $pdo;
$presidenteModel = new Presidente($pdo);

// Probar con concurso 12
$id_concurso = 12;
echo "<h3>Probando concurso ID: $id_concurso</h3>";

// Probar método getResultadosFinales
echo "<h4>1. getResultadosFinales():</h4>";
$resultados = $presidenteModel->getResultadosFinales($id_concurso);

if (!empty($resultados)) {
    echo "<p>✅ Resultados obtenidos: " . count($resultados) . "</p>";
    echo "<table border='1'>";
    echo "<tr><th>Pos</th><th>Conjunto</th><th>Orden</th><th>Puntaje</th><th>Calificaciones</th></tr>";
    foreach ($resultados as $resultado) {
        echo "<tr>";
        echo "<td>" . $resultado['posicion'] . "</td>";
        echo "<td>" . $resultado['conjunto'] . "</td>";
        echo "<td>" . $resultado['orden_presentacion'] . "</td>";
        echo "<td>" . $resultado['promedio_final'] . "</td>";
        echo "<td>" . $resultado['calificaciones_count'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ NO se obtuvieron resultados</p>";
}

// Probar método tieneConjuntosParticipantes
echo "<h4>2. tieneConjuntosParticipantes():</h4>";
$tiene_conjuntos = $presidenteModel->tieneConjuntosParticipantes($id_concurso);
echo "<p>" . ($tiene_conjuntos ? "✅ TIENE conjuntos" : "❌ NO tiene conjuntos") . "</p>";

// Probar método tieneCalificaciones
echo "<h4>3. tieneCalificaciones():</h4>";
$tiene_calificaciones = $presidenteModel->tieneCalificaciones($id_concurso);
echo "<p>" . ($tiene_calificaciones ? "✅ TIENE calificaciones" : "❌ NO tiene calificaciones") . "</p>";

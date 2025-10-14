<?php
// debug_presidente.php - Para ver qué está pasando
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h1>DEBUG PRESIDENTE</h1>";
echo "<h3>Session user:</h3>";
var_dump($_SESSION['user']);

// Verificar concursos en la base de datos
global $pdo;
$sql = "SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado FROM Concurso ORDER BY fecha_inicio DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$concursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Concursos en BD:</h3>";
echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Enlace Prueba</th></tr>";
foreach ($concursos as $concurso) {
    echo "<tr>";
    echo "<td>" . $concurso['id_concurso'] . "</td>";
    echo "<td>" . $concurso['nombre'] . "</td>";
    echo "<td>" . $concurso['estado'] . "</td>";
    echo "<td><a href='index.php?page=presidente_revisar_resultados&id_concurso=" . $concurso['id_concurso'] . "'>VER RESULTADOS</a></td>";
    echo "</tr>";
}
echo "</table>";

// Probar el modelo Presidente
echo "<h3>Probando modelo Presidente:</h3>";
require_once __DIR__ . '/models/Presidente.php';
$presidenteModel = new Presidente($pdo);

// Probar con un concurso específico
$id_concurso_test = 12; // Usa el ID 12 que está cerrado
echo "<p>Probando resultados para concurso ID: $id_concurso_test</p>";

try {
    $resultados = $presidenteModel->getResultadosFinales($id_concurso_test);
    echo "<p>Resultados obtenidos: " . count($resultados) . "</p>";
    var_dump($resultados);
} catch (Exception $e) {
    echo "<p style='color: red;'>Error en getResultadosFinales: " . $e->getMessage() . "</p>";
}

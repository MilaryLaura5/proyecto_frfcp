<?php
// verificar_concurso.php - Verificar estructura de tabla Concurso
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h1>🔍 ESTRUCTURA TABLA CONCURSO</h1>";

global $pdo;

// Verificar estructura de tabla 'concurso'
echo "<h3>Estructura de tabla 'Concurso':</h3>";
$sql = "DESCRIBE Concurso";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$estructura_concurso = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($estructura_concurso as $campo) {
    echo "<tr>";
    echo "<td>" . $campo['Field'] . "</td>";
    echo "<td>" . $campo['Type'] . "</td>";
    echo "<td>" . $campo['Null'] . "</td>";
    echo "<td>" . $campo['Key'] . "</td>";
    echo "<td>" . $campo['Default'] . "</td>";
    echo "<td>" . $campo['Extra'] . "</td>";
    echo "</tr>";
}
echo "</table>";

// Verificar datos de ejemplo
echo "<h3>Datos de ejemplo de tabla 'Concurso':</h3>";
$sql = "SELECT * FROM Concurso LIMIT 1";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ejemplo_concurso = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($ejemplo_concurso);
echo "</pre>";

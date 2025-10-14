<?php
// verificar_estructura.php - Verificar estructura real de las tablas
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h1>🔍 ESTRUCTURA REAL DE TABLAS</h1>";

global $pdo;

// Verificar estructura de tabla 'conjunto'
echo "<h3>1. Estructura de tabla 'conjunto':</h3>";
$sql = "DESCRIBE conjunto";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$estructura_conjunto = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($estructura_conjunto as $campo) {
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

// Verificar estructura de tabla 'detallecalificacion'
echo "<h3>2. Estructura de tabla 'detallecalificacion':</h3>";
$sql = "DESCRIBE detallecalificacion";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$estructura_detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Campo</th><th>Tipo</th><th>Nulo</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
foreach ($estructura_detalle as $campo) {
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

// Verificar algunos datos de ejemplo de 'conjunto'
echo "<h3>3. Datos de ejemplo de tabla 'conjunto' (primeros 3 registros):</h3>";
$sql = "SELECT * FROM conjunto LIMIT 3";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ejemplo_conjunto = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($ejemplo_conjunto);
echo "</pre>";

// Verificar algunos datos de ejemplo de 'detallecalificacion'
echo "<h3>4. Datos de ejemplo de tabla 'detallecalificacion' (primeros 3 registros):</h3>";
$sql = "SELECT * FROM detallecalificacion LIMIT 3";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$ejemplo_detalle = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<pre>";
print_r($ejemplo_detalle);
echo "</pre>";
?> --- IGNORE ---
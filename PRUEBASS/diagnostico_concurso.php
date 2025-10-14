<?php
// diagnostico_concurso.php - Verificar datos reales del concurso
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h1>🔍 DIAGNÓSTICO CONCURSO ID 12</h1>";

global $pdo;

// 1. Verificar concurso
echo "<h3>1. Información del Concurso ID 12:</h3>";
$sql = "SELECT * FROM Concurso WHERE id_concurso = 12";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$concurso = $stmt->fetch(PDO::FETCH_ASSOC);

if ($concurso) {
    echo "<pre>";
    print_r($concurso);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ Concurso no encontrado</p>";
}

// 2. Verificar conjuntos participantes
echo "<h3>2. Conjuntos participantes en concurso 12:</h3>";
$sql = "SELECT * FROM participacionconjunto WHERE id_concurso = 12";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$participaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($participaciones) {
    echo "<p>✅ Conjuntos participantes encontrados: " . count($participaciones) . "</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID Participación</th><th>ID Conjunto</th><th>Orden</th><th>Número Oficial</th></tr>";
    foreach ($participaciones as $participacion) {
        echo "<tr>";
        echo "<td>" . $participacion['id_participacion'] . "</td>";
        echo "<td>" . $participacion['id_conjunto'] . "</td>";
        echo "<td>" . $participacion['orden_presentacion'] . "</td>";
        echo "<td>" . $participacion['numero_oficial'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ NO hay conjuntos participantes en este concurso</p>";
}

// 3. Verificar calificaciones
echo "<h3>3. Calificaciones en concurso 12:</h3>";
$sql = "SELECT * FROM calificacion WHERE id_concurso = 12";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($calificaciones) {
    echo "<p>✅ Calificaciones encontradas: " . count($calificaciones) . "</p>";
    echo "<pre>";
    print_r($calificaciones);
    echo "</pre>";
} else {
    echo "<p style='color: red;'>❌ NO hay calificaciones en este concurso</p>";
}

// 4. Verificar todos los concursos y su estado
echo "<h3>4. Todos los concursos y su estado real:</h3>";
$sql = "SELECT 
            c.id_concurso, 
            c.nombre, 
            c.estado,
            COUNT(pc.id_conjunto) as conjuntos_participantes,
            COUNT(ca.id_calificacion) as calificaciones_registradas
        FROM Concurso c
        LEFT JOIN participacionconjunto pc ON c.id_concurso = pc.id_concurso
        LEFT JOIN calificacion ca ON c.id_concurso = ca.id_concurso
        GROUP BY c.id_concurso
        ORDER BY c.id_concurso";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$concursos_completos = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>ID</th><th>Nombre</th><th>Estado</th><th>Conjuntos</th><th>Calificaciones</th><th>¿Tiene Datos?</th></tr>";
foreach ($concursos_completos as $concurso) {
    $tiene_datos = ($concurso['conjuntos_participantes'] > 0) ? '✅' : '❌';
    echo "<tr>";
    echo "<td>" . $concurso['id_concurso'] . "</td>";
    echo "<td>" . $concurso['nombre'] . "</td>";
    echo "<td>" . $concurso['estado'] . "</td>";
    echo "<td>" . $concurso['conjuntos_participantes'] . "</td>";
    echo "<td>" . $concurso['calificaciones_registradas'] . "</td>";
    echo "<td>" . $tiene_datos . "</td>";
    echo "</tr>";
}
echo "</table>";

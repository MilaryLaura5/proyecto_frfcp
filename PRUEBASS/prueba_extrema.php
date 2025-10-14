<?php
// prueba_extrema.php - Prueba SIN modelos, solo consultas directas
session_start();
require_once __DIR__ . '/config/database.php';

echo "<h1>🔥 PRUEBA EXTREMA - CONSULTAS DIRECTAS</h1>";

global $pdo;

// 1. Probar consulta de conjuntos participantes
echo "<h3>1. Conjuntos participantes en concurso 12:</h3>";
$sql_conjuntos = "SELECT 
                    c.id_conjunto,
                    c.nombre AS conjunto,
                    pc.orden_presentacion,
                    pc.id_participacion
                  FROM participacionconjunto pc
                  JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
                  WHERE pc.id_concurso = 12
                  ORDER BY pc.orden_presentacion";

$stmt = $pdo->prepare($sql_conjuntos);
$stmt->execute();
$conjuntos = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($conjuntos) {
    echo "<p>✅ Conjuntos encontrados: " . count($conjuntos) . "</p>";
    echo "<table border='1'>";
    echo "<tr><th>ID Conjunto</th><th>Nombre</th><th>Orden</th><th>ID Participación</th></tr>";
    foreach ($conjuntos as $conjunto) {
        echo "<tr>";
        echo "<td>" . $conjunto['id_conjunto'] . "</td>";
        echo "<td>" . $conjunto['conjunto'] . "</td>";
        echo "<td>" . $conjunto['orden_presentacion'] . "</td>";
        echo "<td>" . $conjunto['id_participacion'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'>❌ NO hay conjuntos</p>";
}

// 2. Probar consulta de calificaciones para el primer conjunto
if (!empty($conjuntos)) {
    $primer_participacion = $conjuntos[0]['id_participacion'];

    echo "<h3>2. Calificaciones para participación ID: $primer_participacion</h3>";

    $sql_calificaciones = "SELECT 
                            ca.id_calificacion,
                            ca.estado,
                            dc.puntaje,
                            cr.nombre as criterio
                          FROM calificacion ca
                          LEFT JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
                          LEFT JOIN criterio cr ON dc.id_criterio = cr.id_criterio
                          WHERE ca.id_participacion = ?";

    $stmt = $pdo->prepare($sql_calificaciones);
    $stmt->execute([$primer_participacion]);
    $calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($calificaciones) {
        echo "<p>✅ Calificaciones encontradas: " . count($calificaciones) . "</p>";
        echo "<pre>";
        print_r($calificaciones);
        echo "</pre>";
    } else {
        echo "<p style='color: red;'>❌ NO hay calificaciones para esta participación</p>";
    }
}

// 3. Probar consulta de promedio
if (!empty($conjuntos)) {
    echo "<h3>3. Promedio para participación ID: $primer_participacion</h3>";

    $sql_promedio = "SELECT AVG(dc.puntaje) as promedio
                     FROM calificacion ca
                     JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
                     WHERE ca.id_participacion = ? AND ca.estado = 'enviado'";

    $stmt = $pdo->prepare($sql_promedio);
    $stmt->execute([$primer_participacion]);
    $promedio = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "<p>Promedio: " . ($promedio['promedio'] ? round($promedio['promedio'], 2) : '0') . "</p>";
}

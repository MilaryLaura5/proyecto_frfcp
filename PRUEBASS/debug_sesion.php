<?php
session_start();
echo "<h1>🔍 DEBUG DE SESIÓN Y RUTAS</h1>";

// Verificar sesión
echo "<h3>1. Estado de Sesión:</h3>";
echo "<pre>";
var_dump($_SESSION);
echo "</pre>";

// Verificar archivos existentes
echo "<h3>2. Archivos existentes:</h3>";
$archivos = [
    'controllers/PresidenteController.php',
    'models/Presidente.php',
    'views/presidente/revisar_resultados.php',
    'config/database.php'
];

foreach ($archivos as $archivo) {
    echo file_exists($archivo) ? "✅ $archivo<br>" : "❌ $archivo<br>";
}

// Verificar rutas
echo "<h3>3. Rutas actuales:</h3>";
echo "SCRIPT: " . $_SERVER['PHP_SELF'] . "<br>";
echo "GET: " . ($_GET['page'] ?? 'No page') . "<br>";
echo "ID Concurso: " . ($_GET['id_concurso'] ?? 'No ID') . "<br>";

<?php
session_start();
echo "<h1>🔍 DEBUG POST-MERGE</h1>";

// 1. Verificar que el controlador existe y funciona
if (file_exists('controllers/PresidenteController.php')) {
    require_once 'controllers/PresidenteController.php';

    try {
        $controller = new PresidenteController();
        echo "✅ PresidenteController CARGADO<br>";

        // Verificar métodos
        $metodos = get_class_methods($controller);
        echo "✅ Métodos disponibles: " . implode(', ', $metodos) . "<br>";

        if (in_array('revisarResultados', $metodos)) {
            echo "✅ revisarResultados() EXISTE<br>";
        } else {
            echo "❌ revisarResultados() NO EXISTE<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error en PresidenteController: " . $e->getMessage() . "<br>";
    }
} else {
    echo "❌ PresidenteController.php NO EXISTE<br>";
}

// 2. Verificar rutas en index.php
echo "<h3>Verificando rutas...</h3>";
echo "<a href='index.php?page=presidente_revisar_resultados&id_concurso=12'>Probar ruta manual</a><br>";

// 3. Verificar sesión específica
echo "<h3>Sesión actual:</h3>";
echo "Usuario: " . ($_SESSION['user']['usuario'] ?? 'NO') . "<br>";
echo "Rol: " . ($_SESSION['user']['rol'] ?? 'NO') . "<br>";

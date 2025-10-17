<?php
session_start();
echo "<h2>🔍 Estado de la sesión</h2>";

if (isset($_SESSION['user'])) {
    echo "<p style='color:green;'>🟢 Sesión activa</p>";
    echo "<pre>";
    print_r($_SESSION['user']);
    echo "</pre>";
    echo "<a href='index.php?page=logout'>Cerrar sesión</a>";
} else {
    echo "<p style='color:red;'>🔴 No hay sesión</p>";
}
echo "<br><a href='index.php?page=login'>Ir al login</a>";

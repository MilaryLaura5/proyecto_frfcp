<?php
echo "<h2>🔑 Generador de Hash - Tu sistema, tu regla</h2>";

$contraseña_normal = 'presidente123';

// PHP genera el hash usando Bcrypt (el método seguro)
$hash = password_hash($contraseña_normal, PASSWORD_BCRYPT);

echo "<p><strong>Contraseña:</strong> <code>$contraseña_normal</code></p>";
echo "<p><strong>Hash (copia TODO este texto):</strong></p>";
echo "<textarea rows='4' cols='80'>$hash</textarea>";
echo "<p>💡 Este hash FUNCIONARÁ porque fue creado por tu sistema.</p>";

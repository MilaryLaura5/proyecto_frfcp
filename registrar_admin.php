<?php
// registrar_admin.php
// Ejecuta este script una sola vez para crear el primer administrador

require_once 'config/database.php';

$nombre = 'Alexander Quispe';
$usuario = 'admin@frfcp.org';
$contraseña = 'admin123'; // Cambia esta contraseña después
$cargo = 'Presidente Ejecutivo';

// Verificar si ya existe un administrador
$sql_check = "SELECT id_usuario FROM Usuario WHERE usuario = ?";
$stmt_check = $pdo->prepare($sql_check);
$stmt_check->execute([$usuario]);

if ($stmt_check->rowCount() > 0) {
    die("❌ Ya existe un usuario con ese nombre. Este script solo se ejecuta una vez.");
}

try {
    // Iniciar transacción
    $pdo->beginTransaction();

    // Insertar en Usuario
    $sql_user = "INSERT INTO Usuario (usuario, contraseña, rol, estado) VALUES (?, ?, 'Administrador', 1)";
    $contraseña_hash = password_hash($contraseña, PASSWORD_DEFAULT);
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$usuario, $contraseña_hash]);
    $id_usuario = $pdo->lastInsertId();

    // Insertar en Administrador
    $sql_admin = "INSERT INTO Administrador (id_admin, nombre, cargo) VALUES (?, ?, ?)";
    $stmt_admin = $pdo->prepare($sql_admin);
    $stmt_admin->execute([$id_usuario, $nombre, $cargo]);

    // Confirmar transacción
    $pdo->commit();

    echo "
    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 100px auto; padding: 20px; border: 1px solid #007BFF; border-radius: 10px; background-color: #f8f9ff; text-align: center;'>
        <h3>✅ Administrador creado con éxito</h3>
        <p><strong>Nombre:</strong> $nombre</p>
        <p><strong>Usuario:</strong> $usuario</p>
        <p><strong>Contraseña:</strong> $contraseña</p>
        <p style='color: red;'><strong>⚠️ Cambia la contraseña después</strong></p>
        <a href='index.php?page=login' style='display: inline-block; margin-top: 15px; padding: 10px 20px; background-color: #007BFF; color: white; text-decoration: none; border-radius: 5px;'>Ir al login</a>
    </div>";
} catch (Exception $e) {
    $pdo->rollback();
    die("Error al crear administrador: " . $e->getMessage());
}

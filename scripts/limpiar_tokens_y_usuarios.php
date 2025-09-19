<?php
// scripts/limpiar_tokens_y_usuarios.php
require_once __DIR__ . '/../config/database.php';

global $pdo;

try {
    $pdo->beginTransaction();

    // 1. Obtener IDs de usuarios cuyo token expiró hace más de 5 minutos
    $stmt = $pdo->prepare("
        SELECT id_jurado FROM TokenAcceso 
        WHERE fecha_expiracion < DATE_SUB(NOW(), INTERVAL 5 MINUTE) 
        AND usado = 1
    ");
    $stmt->execute();
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($ids)) {
        // 2. Borrar tokens asociados
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql_tokens = "DELETE FROM TokenAcceso WHERE id_jurado IN ($placeholders)";
        $pdo->prepare($sql_tokens)->execute($ids);

        // 3. Borrar usuarios (y por cascada, Jurado)
        $sql_usuarios = "DELETE FROM Usuario WHERE id_usuario IN ($placeholders)";
        $pdo->prepare($sql_usuarios)->execute($ids);
    }

    $pdo->commit();
    echo "✅ Limpieza completada: " . count($ids) . " usuarios eliminados.\n";
} catch (Exception $e) {
    $pdo->rollback();
    error_log("Error en limpieza: " . $e->getMessage());
}

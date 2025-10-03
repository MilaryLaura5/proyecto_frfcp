<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();

global $pdo;

if ($_POST['nombre'] ?? null) {
    $nombre = trim($_POST['nombre']);
    if ($nombre) {
        $stmt = $pdo->prepare("INSERT IGNORE INTO Criterio (nombre) VALUES (?)");
        $stmt->execute([$nombre]);
    }
    header('Location: index.php?page=admin_gestionar_criterios');
    exit;
}

$stmt = $pdo->query("SELECT * FROM Criterio ORDER BY nombre");
$criterios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Criterios - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2><i class="bi bi-list-task"></i> Criterios Generales</h2>
        <p class="text-muted">Define los criterios que podrán usarse en cualquier concurso.</p>

        <!-- Formulario para agregar nuevo criterio -->
        <form method="POST" class="mb-4">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="nombre" class="form-control" placeholder="Nombre del criterio" required>
                <button type="submit" class="btn btn-success">Agregar Criterio</button>
            </div>
        </form>

        <!-- Lista de criterios -->
        <ul class="list-group">
            <?php foreach ($criterios as $c): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= htmlspecialchars($c['nombre']) ?>
                    <a href="index.php?page=admin_asignar_criterios_concurso&id_criterio=<?= $c['id_criterio'] ?>"
                        class="btn btn-sm btn-outline-primary">
                        Usar en concurso
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <hr>
        <a href="index.php?page=admin_dashboard" class="btn btn-secondary">Volver al Dashboard</a>
    </div>
</body>

</html>
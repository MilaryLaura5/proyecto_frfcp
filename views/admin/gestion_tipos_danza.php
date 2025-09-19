<?php
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../config/database.php';


redirect_if_not_admin();
$user = auth();

// Obtener tipos desde BD
global $pdo;
$stmt = $pdo->query("SELECT * FROM TipoDanza ORDER BY nombre_tipo");
$tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Danza - FRFCP Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .container-main { max-width: 700px; margin: 80px auto; }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-tags me-2 text-primary"></i>Tipos de Danza Oficiales</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <p class="text-muted">Estos son los tres tipos oficiales de danza reconocidos por la FRFCP. No se pueden modificar.</p>

        <ul class="list-group">
            <?php foreach ($tipos as $t): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?= ucfirst(str_replace('_', ' ', $t['nombre_tipo'])) ?></strong>
                    </span>
                    <span class="badge bg-dark text-white">Tipo <?= $t['id_tipo'] ?></span>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="alert alert-info mt-4">
            <small>
                <i class="bi bi-info-circle"></i> 
                Esta lista es fija y solo sirve como referencia para crear series.
            </small>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
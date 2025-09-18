<?php
// views/admin/seleccionar_concurso.php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

require_once __DIR__ . '/../../config/database.php';
global $pdo;
$stmt = $pdo->query("SELECT * FROM Concurso ORDER BY nombre");
$concursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Concurso - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 700px;
            margin: 100px auto;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5><i class="bi bi-people"></i> Gestionar Conjuntos</h5>
            </div>
            <div class="card-body">
                <p>Selecciona el concurso para gestionar sus conjuntos:</p>

                <?php if (count($concursos) > 0): ?>
                    <ul class="list-group">
                        <?php foreach ($concursos as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <strong><?= htmlspecialchars($c['nombre']) ?></strong><br>
                                    <small class="text-muted">
                                        <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?>
                                        →
                                        <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?>
                                    </small>
                                </span>
                                <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $c['id_concurso'] ?>"
                                    class="btn btn-sm btn-success">
                                    <i class="bi bi-arrow-right"></i> Gestionar
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="alert alert-info">No hay concursos registrados.</div>
                <?php endif; ?>
            </div>
        </div>

        <div class="mt-3 text-center">
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
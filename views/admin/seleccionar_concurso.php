<?php
// views/admin/seleccionar_concurso.php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

global $pdo;

// Obtener todos los concursos
$stmt = $pdo->query("SELECT * FROM Concurso ORDER BY fecha_inicio DESC");
$concursos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$error = $_GET['error'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Concurso - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 800px;
            margin: 100px auto;
        }

        .card-concurso {
            transition: transform 0.2s;
        }

        .card-concurso:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-people me-2 text-primary"></i> Gestionar Conjuntos</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <?php if ($error === 'no_concurso'): ?>
            <div class="alert alert-warning">⚠️ No hay concursos disponibles.</div>
        <?php endif; ?>

        <?php if (count($concursos) > 0): ?>
            <div class="row g-4">
                <?php foreach ($concursos as $c): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-concurso shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <p class="card-text text-muted mb-2">
                                    <small>
                                        <i class="bi bi-calendar"></i>
                                        <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?>
                                        →
                                        <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-<?=
                                                            $c['estado'] === 'Activo' ? 'success' : ($c['estado'] === 'Pendiente' ? 'warning' : 'secondary')
                                                            ?>">
                                        <?= $c['estado'] ?>
                                    </span>
                                </p>
                                <div class="mt-auto">
                                    <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $c['id_concurso'] ?>"
                                        class="btn btn-primary btn-sm w-100">
                                        <i class="bi bi-people"></i> Gestionar Conjuntos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-5">
                <i class="bi bi-emoji-frown" style="font-size: 3rem;"></i>
                <h5>No hay concursos registrados</h5>
                <p>Primero crea un concurso para poder gestionar conjuntos.</p>
                <a href="index.php?page=admin_gestion_concursos" class="btn btn-success">
                    <i class="bi bi-trophy"></i> Crear Concurso
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
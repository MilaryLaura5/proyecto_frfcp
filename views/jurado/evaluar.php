<?php
// views/jurado/evaluar.php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_jurado(); // Asegura que solo jurados accedan

$user = $_SESSION['user'];
$id_concurso = $user['id_concurso']; // ✅ Obtenido del token

global $pdo;

// Obtener conjuntos asignados a este concurso
$stmt = $pdo->prepare("
    SELECT pc.id_participacion, pc.orden_presentacion, c.nombre AS nombre_conjunto, s.numero_serie
    FROM ParticipacionConjunto pc
    JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
    JOIN Serie s ON c.id_serie = s.id_serie
    WHERE pc.id_concurso = ?
    ORDER BY pc.orden_presentacion
");
$stmt->execute([$id_concurso]);
$conjuntos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación - FRFCP Jurado</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .card-conjunto {
            border-left: 4px solid #007bff;
            margin-bottom: 16px;
            border-radius: 8px;
        }

        .btn-evaluar {
            background-color: #28a745;
            color: white;
            padding: 10px 0;
            font-size: 1.1em;
            border-radius: 8px;
        }

        .btn-evaluar:hover {
            background-color: #218838;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
    </style>
</head>

<body class="p-3">

    <div class="header">
        <h5><i class="bi bi-star-fill"></i> Evaluación de Conjuntos</h5>
        <small>Jurado: <?= htmlspecialchars($user['usuario']) ?></small>
    </div>

    <div class="bg-white p-3 rounded shadow-sm mt-3">
        <p class="text-center text-muted">
            Selecciona un conjunto para evaluarlo por número oficial.
        </p>
    </div>

    <?php if (count($conjuntos) > 0): ?>
        <div class="mt-4">
            <?php foreach ($conjuntos as $c): ?>
                <div class="card card-conjunto shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-0">N° <?= $c['orden_presentacion'] ?> - <?= htmlspecialchars($c['nombre_conjunto']) ?></h6>
                                <small class="text-muted">Serie <?= $c['numero_serie'] ?></small>
                            </div>
                            <a href="index.php?page=jurado_calificar&id=<?= $c['id_participacion'] ?>"
                                class="btn btn-evaluar">
                                <i class="bi bi-pencil-square"></i> Calificar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4 text-center">
            No hay conjuntos asignados a este concurso.
        </div>
    <?php endif; ?>

    <div class="text-center mt-4 mb-5">
        <a href="index.php?page=logout" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
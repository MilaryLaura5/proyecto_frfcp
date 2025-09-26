<?php
// views/jurado/calificar.php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_jurado();

$user = $_SESSION['user'];
$id_participacion = $_GET['id'] ?? null;

if (!$id_participacion) {
    die("Conjunto no especificado.");
}

global $pdo;

// Obtener datos del conjunto
$stmt = $pdo->prepare("
    SELECT pc.id_participacion, pc.orden_presentacion, c.nombre AS nombre_conjunto, s.numero_serie
    FROM ParticipacionConjunto pc
    JOIN Conjunto c ON pc.id_conjunto = c.id_conjunto
    JOIN Serie s ON c.id_serie = s.id_serie
    WHERE pc.id_participacion = ?
");
$stmt->execute([$id_participacion]);
$conjunto = $stmt->fetch();

if (!$conjunto) {
    die("Conjunto no encontrado.");
}

// Verificar si ya fue calificado por este jurado
$stmt_check = $pdo->prepare("
    SELECT * FROM Calificacion 
    WHERE id_participacion = ? AND id_jurado = ?
");
$stmt_check->execute([$id_participacion, $user['id']]);
$calificacion = $stmt_check->fetch();

// Criterios de evaluación (ajusta según tu sistema)
$criterios = [
    'Presentación y Vestimenta',
    'Música',
    'Coreografía',
    'Armonía',
    'Originalidad'
];
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .header {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }

        .card-conjunto {
            margin-bottom: 20px;
            border-radius: 10px;
            <?php if ($calificacion && $calificacion['descalificado']): ?>border: 3px solid #dc3545;
            /* Rojo */
            <?php elseif ($calificacion): ?>border: 3px solid #28a745;
            /* Verde */
            <?php else: ?>border: 3px solid #b3d7ff;
            /* Azul claro */
            <?php endif; ?>
        }

        .btn-calificar {
            background-color: #28a745;
            color: white;
            padding: 12px;
            font-size: 1.1em;
            border-radius: 8px;
        }

        .btn-descalificar {
            background-color: #dc3545;
            color: white;
            padding: 12px;
            font-size: 1.1em;
            border-radius: 8px;
        }

        .form-range {
            height: 10px;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body class="p-3">

    <div class="header">
        <h5><i class="bi bi-pencil-square"></i> Calificar Conjunto</h5>
        <small>Jurado: <?= htmlspecialchars($user['usuario']) ?></small>
    </div>

    <!-- Información del conjunto -->
    <div class="card card-conjunto bg-white shadow-sm mt-3">
        <div class="card-body">
            <h5 class="mb-1">N° <?= $conjunto['orden_presentacion'] ?></h5>
            <h6 class="text-primary"><?= htmlspecialchars($conjunto['nombre_conjunto']) ?></h6>
            <small class="text-muted">Serie <?= $conjunto['numero_serie'] ?></small>
        </div>
    </div>

    <!-- Formulario de calificación -->
    <form method="POST" action="index.php?page=jurado_guardar_calificacion">
        <input type="hidden" name="id_participacion" value="<?= $conjunto['id_participacion'] ?>">
        <input type="hidden" name="id_concurso" value="<?= $user['id_concurso'] ?>">

        <?php foreach ($criterios as $criterio): ?>
            <div class="mb-3">
                <label><?= $criterio ?> (0-10)</label>
                <input type="range" class="form-range" name="puntaje_<?= strtolower(str_replace(' ', '_', $criterio)) ?>"
                    min="0" max="10" step="0.5"
                    value="<?= $calificacion ? $calificacion['puntaje_' . strtolower(str_replace(' ', '_', $criterio))] : 5 ?>">
                <div class="d-flex justify-content-between">
                    <small>0</small>
                    <strong>
                        <?= $calificacion ? number_format($calificacion['puntaje_' . strtolower(str_replace(' ', '_', $criterio))], 1) : '5.0' ?>
                    </strong>
                    <small>10</small>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-calificar">
                <i class="bi bi-save"></i> Guardar Calificación
            </button>

            <?php if ($calificacion && !$calificacion['descalificado']): ?>
                <button type="submit" name="descalificar" value="1" class="btn btn-descalificar">
                    <i class="bi bi-x-circle"></i> Deshabilitar / Descalificar
                </button>
            <?php endif; ?>
        </div>
    </form>

    <div class="text-center mt-4 mb-5">
        <a href="index.php?page=jurado_evaluar" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <script>
        // Actualiza el valor mostrado al mover el slider
        document.querySelectorAll('.form-range').forEach(slider => {
            slider.addEventListener('input', function() {
                this.nextElementSibling.querySelector('strong').textContent = parseFloat(this.value).toFixed(1);
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificar - FRFCP</title>
    <!-- ✅ Corrección: espacios eliminados -->
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
            <?php if ($calificacion && $calificacion['estado'] === 'descalificado'): ?>border: 3px solid #dc3545;
            <?php elseif ($calificacion): ?>border: 3px solid #28a745;
            <?php else: ?>border: 3px solid #b3d7ff;
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

        label {
            font-weight: bold;
        }

        .form-text {
            font-size: 0.85em;
        }

        .alert-calificado {
            background-color: #d1ecf1;
            color: #0c5460;
            border-left: 4px solid #007bff;
        }
    </style>
</head>

<body class="p-3">

    <!-- Mensajes de éxito/error -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Calificación guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i>
            Error al guardar: <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="header">
        <h5><i class="bi bi-pencil-square"></i> Calificar Conjunto</h5>
        <small>Jurado: <?= htmlspecialchars($user['usuario']) ?></small>
    </div>

    <!-- Información del conjunto -->
    <div class="card card-conjunto bg-white shadow-sm mt-3">
        <div class="card-body">
            <h5 class="mb-1">N° <?= $conjunto['orden_presentacion'] ?? '?' ?></h5>
            <h6 class="text-primary"><?= htmlspecialchars($conjunto['nombre_conjunto'] ?? 'Desconocido') ?></h6>
            <small class="text-muted">Serie <?= $conjunto['numero_serie'] ?? '?' ?></small>
        </div>
    </div>

    <!-- Estado actual -->
    <?php if ($calificacion): ?>
        <div class="alert alert-calificado mb-3">
            <i class="bi bi-info-circle"></i>
            <?php if ($calificacion['estado'] === 'descalificado'): ?>
                Este conjunto fue <strong>descalificado</strong>.
            <?php else: ?>
                Ya has calificado este conjunto.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Formulario de calificación -->
    <form method="POST" action="index.php?page=jurado_guardar_calificacion">
        <input type="hidden" name="id_participacion" value="<?= $conjunto['id_participacion'] ?>">
        <input type="hidden" name="id_concurso" value="<?= $user['id_concurso'] ?>">

        <?php foreach ($criterios as $c):
            $campo = strtolower(str_replace([' ', 'á', 'é', 'í', 'ó', 'ú'], ['_', 'a', 'e', 'i', 'o', 'u'], $c['nombre_criterio']));
            $valor_guardado = '';
            if ($calificacion) {
                $valor_guardado = ''; // o cargar desde DetalleCalificacion si implementas edición
            }
        ?>
            <div class="mb-3">
                <label for="puntaje_<?= $campo ?>">
                    <?= htmlspecialchars($c['nombre_criterio']) ?> <!-- ✅ aquí también -->
                    <small class="text-muted">(0 - <?= $c['puntaje_maximo'] ?> puntos)</small>
                </label>
                <input type="number"
                    id="puntaje_<?= $campo ?>"
                    class="form-control"
                    name="puntaje_<?= $campo ?>"
                    step="0.01"
                    min="0"
                    max="<?= $c['puntaje_maximo'] ?>"
                    value="<?= $valor_guardado ?>"
                    required>
                <div class="form-text text-muted">
                    Ej: 6.3, 8.75, <?= $c['puntaje_maximo'] ?>
                </div>
            </div>
        <?php endforeach; ?>

        <div class="d-grid gap-2 mt-4">
            <button type="submit" class="btn btn-calificar">
                <i class="bi bi-save"></i> Guardar Calificación
            </button>

            <?php if ($calificacion && $calificacion['estado'] !== 'descalificado'): ?>
                <button type="submit" name="descalificar" value="1" class="btn btn-descalificar">
                    <i class="bi bi-x-circle"></i> Descalificar
                </button>
            <?php endif; ?>
        </div>
    </form>

    <div class="text-center mt-4 mb-5">
        <a href="index.php?page=jurado_evaluar" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
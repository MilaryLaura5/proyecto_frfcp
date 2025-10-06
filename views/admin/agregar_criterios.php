<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Criterios - <?= htmlspecialchars($concurso['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2><i class="bi bi-list-task"></i> Criterios para el concurso: <?= htmlspecialchars($concurso['nombre']) ?></h2>

        <!-- Asignar criterio existente -->
        <form method="POST" action="index.php?page=admin_guardar_criterio_concurso">
            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

            <div class="mb-3">
                <label><strong>Seleccionar Criterio Existente</strong></label>
                <select name="id_criterio" class="form-select" required>
                    <option value="">Selecciona un criterio</option>
                    <?php foreach ($criterios_disponibles as $c): ?>
                        <option value="<?= $c['id_criterio'] ?>">
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Puntaje Máximo</label>
                <input type="number" name="puntaje_maximo" class="form-control" step="0.5" min="1" max="100" required>
            </div>

            <button type="submit" class="btn btn-success">Asignar a este concurso</button>
        </form>

        <hr>

        <h4>📋 Criterios Asignados</h4>
        <?php if (count($criterios_asignados) > 0): ?>
            <ul class="list-group mb-3">
                <?php foreach ($criterios_asignados as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($c['nombre_criterio']) ?></strong>
                            <br>
                            <small class="text-muted">Puntaje máximo: <?= $c['puntaje_maximo'] ?> puntos</small>
                        </div>
                        <a href="index.php?page=admin_editar_criterio&id=<?= $c['id_criterio_concurso'] ?>"
                            class="btn btn-sm btn-warning">Editar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No hay criterios asignados a este concurso.</p>
        <?php endif; ?>

        <a href="index.php?page=admin_gestionar_criterios" class="btn btn-outline-secondary">Volver</a>
    </div>
</body>

</html>
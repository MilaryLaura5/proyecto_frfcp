<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Asignar Puntaje</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h3>Asignar puntaje a: <?= htmlspecialchars($criterio['nombre']) ?></h3>
        <form method="POST">
            <div class="mb-3" style="max-width: 300px;">
                <label>Puntaje Máximo (ej: 20)</label>
                <input type="number" name="puntaje" class="form-control" step="0.5" min="1" max="100" required>
            </div>
            <button type="submit" class="btn btn-success">Guardar</button>
            <a href="index.php?page=admin_configurar_criterios&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary">
                Volver
            </a>
        </form>
    </div>
</body>

</html>
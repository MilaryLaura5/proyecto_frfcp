<!-- views/admin/detalle_calificaciones.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Desglose de Calificaciones - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            margin: 20px;
        }
    </style>
</head>

<body class="p-3">
    <h3><i class="bi bi-eye"></i> Desglose de Calificaciones</h3>
    <a href="index.php?page=admin_resultados&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary mb-3">
        ← Volver a Resultados
    </a>
    <?php if (!empty($detalles)): ?>
        <div class="card">
            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Conjunto</th>
                            <th>Categoría</th>
                            <th>Jurado</th>
                            <th>Criterio</th>
                            <th>Puntaje</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($detalles as $d): ?>
                            <tr>
                                <td><?= htmlspecialchars($d['conjunto']) ?></td>
                                <td><?= htmlspecialchars($d['categoria']) ?></td>
                                <td><?= htmlspecialchars($d['jurado']) ?></td>
                                <td><?= htmlspecialchars($d['criterio']) ?></td>
                                <td><?= number_format($d['puntaje'], 2) ?></td>
                                <td>
                                    <span class="badge <?= $d['estado'] === 'enviado' ? 'bg-success' : 'bg-danger' ?>">
                                        <?= ucfirst($d['estado']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4 text-center">No hay calificaciones registradas.</div>
    <?php endif; ?>
</body>

</html>
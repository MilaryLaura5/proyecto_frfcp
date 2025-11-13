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

        body {
            background-color: #f8f0f0;
            /* Fondo suave con tono rojizo */
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .header-container {
            background: white;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #c9184a;
            /* 🔴 Rojo FRFCP */
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1e0e0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .card-header h5 i {
            color: #c9184a;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #fdf2f2;
            /* Fondo claro rojizo */
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        .estado-badge {
            font-size: 0.85em;
            font-weight: 500;
            padding: 0.5em 0.8em;
        }
    </style>
</head>
<!-- Encabezado -->
<div class="header-container">
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="page-title">
            <i class="bi bi-trophy me-2"></i> Desglose de Calificaciones
        </h2>
        <a href="index.php?page=admin_ver_resultados&id_concurso=<?= $id_concurso ?>" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>

<body class="p-3">
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
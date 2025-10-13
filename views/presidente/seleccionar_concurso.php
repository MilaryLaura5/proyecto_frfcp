<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Concurso - Presidente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .card-concurso {
            transition: transform 0.2s;
        }

        .card-concurso:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body class="p-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-trophy-fill text-warning"></i> Resultados Finales</h2>
            <a href="index.php?page=logout" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-box-arrow-right"></i> Salir
            </a>
            <a href="index.php?page=resultados_en_vivo&id_concurso=<?= $c['id_concurso'] ?>"
                class="btn btn-outline-light btn-sm ms-2">
                <i class="bi bi-tv"></i> En Vivo
            </a>
        </div>

        <div class="row">
            <?php foreach ($concursos as $c): ?>
                <div class="col-md-6 mb-4">
                    <a href="index.php?page=presidente_ver_resultados&id_concurso=<?= $c['id_concurso'] ?>"
                        class="text-decoration-none">
                        <div class="card card-concurso shadow-sm h-100">
                            <div class="card-body">
                                <h5><?= htmlspecialchars($c['nombre']) ?></h5>
                                <p class="mb-1"><strong>Inicio:</strong> <?= date('d/m/Y H:i', strtotime($c['fecha_inicio'])) ?></p>
                                <p class="mb-1"><strong>Fin:</strong> <?= date('d/m/Y H:i', strtotime($c['fecha_fin'])) ?></p>
                                <span class="badge bg-<?= $c['estado'] == 'Activo' ? 'success' : ($c['estado'] == 'Cerrado' ? 'danger' : 'warning') ?>">
                                    <?= ucfirst($c['estado']) ?>
                                </span>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>

</html>
<!-- views/presidente/resultados_por_serie.php -->

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultados por Serie - <?= htmlspecialchars($concurso['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .header {
            background-color: #ffc107;
            color: #000;
            border-radius: 8px 8px 0 0;
        }

        .serie-card {
            margin-bottom: 30px;
        }

        .medalla {
            font-size: 1.5em;
        }

        .table th {
            font-weight: 600;
        }
    </style>
</head>

<body class="p-4">
    <div class="container">

        <!-- Encabezado -->
        <div class="header p-3 text-center mb-4">
            <h3><i class="bi bi-bar-chart"></i> Resultados por Serie</h3>
            <h5><?= htmlspecialchars($concurso['nombre']) ?></h5>
        </div>

        <?php if (empty($resultados_por_serie)): ?>
            <div class="alert alert-info text-center">No hay resultados disponibles.</div>
        <?php else: ?>
            <?php foreach ($resultados_por_serie as $serie): ?>
                <div class="card serie-card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5>Serie <?= $serie['numero_serie'] ?>: <?= htmlspecialchars($serie['nombre_tipo']) ?></h5>
                        <small><?= count($serie['conjuntos']) ?> conjuntos evaluados</small>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Conjunto</th>
                                        <th>Puntaje Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($serie['conjuntos'] as $idx => $c): ?>
                                        <tr class="<?= $c['estado'] === 'descalificado' ? 'table-danger' : '' ?>">
                                            <td>
                                                <?php if ($c['estado'] !== 'descalificado'): ?>
                                                    <span class="medalla">
                                                        <?php if ($idx == 0): ?>🥇
                                                        <?php elseif ($idx == 1): ?>🥈
                                                        <?php elseif ($idx == 2): ?>🥉
                                                        <?php else: ?><?= $idx + 1 ?>.
                                                    <?php endif; ?>
                                                    </span>
                                                <?php else: ?>
                                                    🚫
                                                <?php endif; ?>
                                            </td>
                                            <td><?= htmlspecialchars($c['conjunto']) ?></td>
                                            <td>
                                                <strong>
                                                    <?php if ($c['estado'] === 'descalificado'): ?>
                                                        DESCALIFICADO
                                                    <?php else: ?>
                                                        <?= number_format($c['puntaje_total'], 2) ?>
                                                    <?php endif; ?>
                                                </strong>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php?page=presidente_ver_resultados&id_concurso=<?= $id_concurso ?>"
                class="btn btn-outline-primary me-2">
                <i class="bi bi-trophy"></i> Ver General
            </a>
            <a href="index.php?page=presidente_seleccionar_concurso" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>
</body>

</html>
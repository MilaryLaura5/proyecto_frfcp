<!-- views/admin/detalle_calificaciones.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Desglose de Calificaciones - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
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

        .jurado-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
            margin-bottom: 2rem;
        }

        .jurado-card .card-header {
            background: linear-gradient(135deg, #c9184a, #800f2f);
            color: white;
            border-bottom: none;
            padding: 1rem 1.5rem;
            font-weight: 600;
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

        .timer-container {
            background: linear-gradient(135deg, #c9184a, #800f2f);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(201, 24, 74, 0.3);
        }

        .auto-refresh-badge {
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #dee2e6;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.85rem;
        }

        .jurado-stats {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }

        .stats-item {
            font-size: 0.9rem;
            color: #6c757d;
        }

        .stats-value {
            font-weight: 600;
            color: #c9184a;
        }
    </style>
</head>

<body class="p-3">
    <!-- Encabezado -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-trophy me-2"></i> Desglose de Calificaciones por Jurado
            </h2>
            <div class="d-flex align-items-center gap-3">
                <!-- Contador de tiempo -->
                <div class="timer-container">
                    <i class="bi bi-clock me-2"></i>
                    <span id="refreshTimer">10</span>s
                </div>

                <!-- Indicador de recarga automática -->
                <div class="auto-refresh-badge">
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Auto-recarga
                </div>

                <a href="index.php?page=admin_ver_resultados&id_concurso=<?= $id_concurso ?>" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>

    <?php if (!empty($detalles)): ?>
        <?php
        // Agrupar los detalles por jurado
        $calificaciones_por_jurado = [];
        foreach ($detalles as $d) {
            $jurado_nombre = $d['jurado'];
            if (!isset($calificaciones_por_jurado[$jurado_nombre])) {
                $calificaciones_por_jurado[$jurado_nombre] = [];
            }
            $calificaciones_por_jurado[$jurado_nombre][] = $d;
        }
        ?>

        <?php foreach ($calificaciones_por_jurado as $jurado_nombre => $calificaciones_jurado): ?>
            <?php
            // Calcular estadísticas para este jurado
            $total_calificaciones = count($calificaciones_jurado);
            $calificaciones_enviadas = array_filter($calificaciones_jurado, function ($c) {
                return $c['estado'] === 'enviado';
            });
            $total_enviadas = count($calificaciones_enviadas);
            $porcentaje_completado = $total_calificaciones > 0 ? round(($total_enviadas / $total_calificaciones) * 100, 1) : 0;
            ?>

            <div class="card jurado-card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            <?= htmlspecialchars($jurado_nombre) ?>
                        </h5>
                        <span class="badge bg-light text-dark">
                            <?= $total_enviadas ?>/<?= $total_calificaciones ?> calificaciones
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Estadísticas del jurado -->
                    <div class="jurado-stats">
                        <div class="row text-center">
                            <div class="col-md-4">
                                <div class="stats-item">Total Calificaciones</div>
                                <div class="stats-value"><?= $total_calificaciones ?></div>
                            </div>
                            <div class="col-md-4">
                                <div class="stats-item">Enviadas</div>
                                <div class="stats-value text-success"><?= $total_enviadas ?></div>
                            </div>

                        </div>
                    </div>

                    <!-- Tabla de calificaciones del jurado -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Conjunto</th>
                                    <th>Serie</th>
                                    <th>Criterio</th>
                                    <th>Puntaje</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($calificaciones_jurado as $d): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($d['conjunto']) ?></td>
                                        <td><?= htmlspecialchars($d['categoria']) ?></td>
                                        <td><?= htmlspecialchars($d['criterio']) ?></td>
                                        <td>
                                            <?php if ($d['estado'] === 'enviado'): ?>
                                                <strong><?= number_format($d['puntaje'], 2) ?></strong>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge <?= $d['estado'] === 'enviado' ? 'bg-success' : 'bg-warning' ?> estado-badge">
                                                <?= ucfirst($d['estado']) ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    <?php else: ?>
        <div class="alert alert-info mt-4 text-center">
            <i class="bi bi-info-circle me-2"></i>
            No hay calificaciones registradas.
        </div>
    <?php endif; ?>

    <!-- <script>
        let timeLeft = 10;
        const timerElement = document.getElementById('refreshTimer');

        function startTimer() {
            const countdown = setInterval(() => {
                timeLeft--;
                timerElement.textContent = timeLeft;

                if (timeLeft <= 0) {
                    clearInterval(countdown);
                    location.reload();
                }
            }, 1000);
        }

        // Iniciar el temporizador cuando la página cargue
        document.addEventListener('DOMContentLoaded', function() {
            startTimer();

            // También puedes agregar una función para reiniciar el temporizador manualmente
            document.getElementById('refreshTimer').addEventListener('click', function() {
                timeLeft = 10;
                timerElement.textContent = timeLeft;
            });
        });

        // Recargar automáticamente cada 10 segundos
        setTimeout(function() {
            location.reload();
        }, 10000);
    </script>-->
</body>

</html>
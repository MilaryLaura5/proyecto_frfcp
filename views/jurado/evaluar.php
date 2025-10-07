<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Evaluación - FRFCP Jurado</title>
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
            margin-bottom: 16px;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .card-conjunto.pendiente {
            border-left: 4px solid #007bff;
            background-color: #f8f9ff;
        }

        .card-conjunto.calificado {
            border-left: 4px solid #28a745;
            background-color: #f0fff4;
        }

        .card-conjunto.descalificado {
            border-left: 4px solid #dc3545;
            background-color: #fff5f5;
        }

        .badge-status {
            font-size: 0.8em;
            padding: 0.5em 0.8em;
            border-radius: 50px;
        }

        .btn-descalificar {
            background-color: #dc3545;
            color: white;
            padding: 10px 0;
            font-size: 1.1em;
            border-radius: 8px;
        }

        .detalle-puntaje {
            display: none;
            margin-top: 15px;
            padding: 15px;
            background-color: #e9ecef;
            border-radius: 8px;
            font-size: 0.95em;
        }
    </style>
</head>

<body class="p-3">

    <!-- Mensajes -->
    <?php if (isset($_GET['success']) && $_GET['success'] === 'guardado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> Calificación guardada correctamente.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> Error: <?= htmlspecialchars($_GET['error']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="header">
        <h5><i class="bi bi-star-fill"></i> Evaluación de Conjuntos</h5>
        <small>Jurado: <?= htmlspecialchars($user['usuario']) ?> </small>
    </div>

    <div class="bg-white p-3 rounded shadow-sm mt-3">
        <p class="text-center text-muted">
            Selecciona un conjunto para evaluarlo en el concurso:
            <strong><?= htmlspecialchars($nombre_concurso) ?></strong>
        </p>
    </div>

    <?php if (count($conjuntos) > 0): ?>
        <div class="mt-4">
            <?php foreach ($conjuntos as $c): ?>
                <div class="card card-conjunto 
                    <?= $c['estado_calificacion'] === 'descalificado' ? 'descalificado' : '' ?>
                    <?= $c['estado_calificacion'] === 'enviado' || $c['estado_calificacion'] === 'calificado' ? 'calificado' : 'pendiente' ?>
                    shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-0">N° <?= $c['orden_presentacion'] ?> - <?= htmlspecialchars($c['nombre_conjunto']) ?></h6>
                                <small class="text-muted">Serie <?= $c['numero_serie'] ?></small>
                            </div>
                            <?php if ($c['estado_calificacion'] === 'descalificado'): ?>
                                <span class="badge bg-danger badge-status">Descalificado</span>
                            <?php elseif ($c['estado_calificacion'] === 'enviado' || $c['estado_calificacion'] === 'calificado'): ?>
                                <span class="badge bg-success badge-status">Calificado</span>
                            <?php else: ?>
                                <span class="badge bg-primary badge-status">Pendiente</span>
                            <?php endif; ?>
                        </div>

                        <div class="mt-2">
                            <?php if ($c['estado_calificacion'] === 'descalificado'): ?>
                                <button class="btn btn-secondary w-100" disabled>
                                    <i class="bi bi-lock"></i> Descalificado
                                </button>
                            <?php elseif ($c['estado_calificacion'] === 'enviado' || $c['estado_calificacion'] === 'calificado'): ?>
                                <button class="btn btn-warning w-100 ver-detalle-btn" data-id="<?= $c['id_participacion'] ?>">
                                    <i class="bi bi-eye"></i> Ver Puntajes
                                </button>

                                <!-- Detalle oculto -->
                                <div id="detalle_<?= $c['id_participacion'] ?>" class="detalle-puntaje mt-2 p-3 bg-light rounded">
                                    <strong>Puntajes asignados:</strong>
                                    <ul class="list-group list-group-flush mt-2">
                                        <?php foreach ($c['detalles'] as $d): ?>
                                            <li class="list-group-item d-flex justify-content-between">
                                                <?= htmlspecialchars($d['nombre_criterio']) ?>
                                                <strong><?= number_format($d['puntaje'], 2) ?> pts</strong>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <div class="text-end mt-2">
                                        <button class="btn btn-sm btn-outline-secondary cerrar-detalle">
                                            Cerrar
                                        </button>
                                    </div>
                                </div>
                            <?php else: ?>
                                <a href="index.php?page=jurado_calificar&id=<?= $c['id_participacion'] ?>" class="btn btn-primary w-100">
                                    <i class="bi bi-pencil-square"></i> Calificar
                                </a>
                                <form method="POST" action="index.php?page=jurado_guardar_calificacion" class="mt-2">
                                    <input type="hidden" name="id_participacion" value="<?= $c['id_participacion'] ?>">
                                    <input type="hidden" name="id_concurso" value="<?= $user['id_concurso'] ?>">
                                    <input type="hidden" name="descalificar" value="1">
                                    <button type="submit" class="btn btn-descalificar w-100" onclick="return confirm('¿Descalificar este conjunto? Se asignará 0 puntos.')">
                                        <i class="bi bi-x-circle"></i> Descalificar
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="alert alert-info mt-4 text-center">No hay conjuntos asignados.</div>
    <?php endif; ?>

    <div class="text-center mt-4 mb-5">
        <a href="index.php?page=logout" class="btn btn-outline-danger btn-sm">
            <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
        </a>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.querySelectorAll('.ver-detalle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const id = this.dataset.id;
                const detalle = document.getElementById('detalle_' + id);
                detalle.style.display = detalle.style.display === 'block' ? 'none' : 'block';
            });
        });

        document.querySelectorAll('.cerrar-detalle').forEach(btn => {
            btn.addEventListener('click', function() {
                this.closest('.detalle-puntaje').style.display = 'none';
            });
        });
    </script>
</body>

</html>
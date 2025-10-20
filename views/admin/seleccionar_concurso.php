<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Concurso - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            color: #0056b3;
            margin: 0;
        }

        .card-concurso {
            transition: all 0.3s ease;
            height: 100%;
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }

        .card-concurso:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.12);
        }

        .card-body {
            display: flex;
            flex-direction: column;
        }

        .card-footer {
            background: transparent;
            border-top: none;
            padding-top: 0;
        }

        .btn-primary {
            border-radius: 8px;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .row.g-4>div {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado con ancho completo -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-people me-2 text-primary"></i>
                Gestionar Conjuntos
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Contenido principal con ancho completo -->
    <div class="container-fluid px-4">

        <?php if ($error === 'no_concurso'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                ⚠️ No hay concursos disponibles.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (count($concursos) > 0): ?>
            <div class="row g-4">
                <?php foreach ($concursos as $c): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card card-concurso shadow-sm h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></h5>
                                <p class="card-text text-muted mb-2">
                                    <small>
                                        <i class="bi bi-calendar"></i>
                                        <?= date('d/m/Y', strtotime($c['fecha_inicio'])) ?>
                                        →
                                        <?= date('d/m/Y', strtotime($c['fecha_fin'])) ?>
                                    </small>
                                </p>
                                <p class="card-text">
                                    <span class="badge bg-<?= $c['estado'] === 'Activo' ? 'success' : ($c['estado'] === 'Pendiente' ? 'warning' : 'secondary') ?>">
                                        <?= ucfirst($c['estado']) ?>
                                    </span>
                                </p>
                                <div class="mt-auto">
                                    <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $c['id_concurso'] ?>"
                                        class="btn btn-primary w-100 py-2">
                                        <i class="bi bi-people me-1"></i> Gestionar Conjuntos
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-emoji-frown display-4 text-muted"></i>
                <h5 class="text-muted mt-3">No hay concursos registrados</h5>
                <p class="text-muted">Primero crea un concurso para poder gestionar conjuntos.</p>
                <a href="index.php?page=admin_gestion_concursos" class="btn btn-success btn-lg px-4">
                    <i class="bi bi-trophy me-2"></i> Crear Concurso
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
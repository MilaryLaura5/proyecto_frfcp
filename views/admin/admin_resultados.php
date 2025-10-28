<?php
// views/admin/admin_resultados.php - Vista mejorada para Admin
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin(); // Solo Admin puede ver esto
$user = auth();

global $pdo;

// Obtener todos los concursos
$stmt = $pdo->prepare("SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado FROM Concurso ORDER BY fecha_inicio DESC");
$stmt->execute();
$concursos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Concurso - Admin</title>
    <!-- Bootstrap CSS (corregido: sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
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

<body class="p-3">

    <!-- Encabezado -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-trophy me-2"></i> Seleccionar Concurso
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Mensajes de error (sin cambios de color) -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                <i class="bi bi-exclamation-triangle"></i>
                <?php
                switch ($_GET['error']) {
                    case 'no_concurso':
                        echo 'Debe seleccionar un concurso.';
                        break;
                    case 'sin_resultados':
                        echo 'No hay resultados disponibles.';
                        break;
                    default:
                        echo 'Error al cargar los datos.';
                        break;
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Card de concursos -->
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i> Concursos Disponibles
                </h5>
                <span class="badge bg-secondary"><?= count($concursos) ?> encontrados</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($concursos)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="width: 8%;">ID</th>
                                    <th style="width: 40%;">Nombre del Concurso</th>
                                    <th class="text-center" style="width: 12%;">Inicio</th>
                                    <th class="text-center" style="width: 12%;">Fin</th>
                                    <th class="text-center" style="width: 12%;">Estado</th>
                                    <th class="text-center" style="width: 16%;">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($concursos as $c): ?>
                                    <tr>
                                        <td class="text-center fw-bold"><?= $c['id_concurso'] ?></td>
                                        <td><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="text-center"><?= date('d/m/Y H:i', strtotime($c['fecha_inicio'])) ?></td>
                                        <td class="text-center"><?= date('d/m/Y H:i', strtotime($c['fecha_fin'])) ?></td>
                                        <td class="text-center">
                                            <?php
                                            $estado = $c['estado'];
                                            $badge_class = 'bg-warning text-dark';
                                            if ($estado === 'Activo') {
                                                $badge_class = 'bg-success';
                                            } elseif ($estado === 'Cerrado') {
                                                $badge_class = 'bg-danger';
                                            }
                                            ?>
                                            <span class="badge <?= $badge_class ?> estado-badge">
                                                <?= ucfirst($estado) ?>
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <a href="index.php?page=admin_ver_resultados&id_concurso=<?= $c['id_concurso'] ?>"
                                                class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye"></i> Ver Resultados
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="lead text-muted mt-3">No hay concursos registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
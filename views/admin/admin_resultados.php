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
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .main-content {
            padding: 20px;
        }

        .btn-action {
            min-width: 140px;
        }

        .concurso-row:hover {
            background-color: #f8f9fa;
        }

        .estado-badge {
            font-size: 0.8em;
            font-weight: bold;
            padding: 0.5em 0.8em;
        }

        .bg-cerrado {
            background-color: #dc3545;
            color: white;
        }

        .bg-activo {
            background-color: #28a745;
            color: white;
        }

        .bg-pendiente {
            background-color: #ffc107;
            color: black;
        }

        .info-box {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin: 20px 0;
            font-size: 0.9em;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Contenido principal -->
            <main class="col-md-12 main-content">

                <!-- Encabezado con botón alineado -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary mb-0">🏆 Seleccionar Concurso</h2>
                    <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary">
                        ← Volver
                    </a>
                </div>
            </main>


            <!-- Mensajes de error -->
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>⚠️ Error:</strong>
                    <?php
                    switch ($_GET['error']) {
                        case 'no_concurso':
                            echo 'Debe seleccionar un concurso';
                            break;
                        case 'sin_resultados':
                            echo 'No hay resultados disponibles';
                            break;
                        default:
                            echo 'Error al cargar los datos';
                            break;
                    }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Card principal -->
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">📊 Concursos Disponibles</h5>
                    <span class="badge bg-light text-primary fs-6"><?php echo count($concursos); ?> concursos</span>
                </div>
                <div class="card-body p-0">
                    <?php if (!empty($concursos)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="5%" class="text-center">ID</th>
                                        <th width="35%">Nombre del Concurso</th>
                                        <th width="12%" class="text-center">Fecha Inicio</th>
                                        <th width="12%" class="text-center">Fecha Fin</th>
                                        <th width="10%" class="text-center">Estado</th>
                                        <th width="20%" class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($concursos as $c): ?>
                                        <tr class="concurso-row">
                                            <td class="text-center fw-bold"><?php echo $c['id_concurso']; ?></td>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($c['nombre']); ?></div>
                                            </td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($c['fecha_inicio'])); ?></td>
                                            <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($c['fecha_fin'])); ?></td>
                                            <td class="text-center">
                                                <?php
                                                $badge_class = 'bg-pendiente';
                                                if ($c['estado'] == 'Activo') {
                                                    $badge_class = 'bg-activo';
                                                } elseif ($c['estado'] == 'Cerrado') {
                                                    $badge_class = 'bg-cerrado';
                                                }
                                                ?>
                                                <span class="badge <?php echo $badge_class; ?>">
                                                    <?php echo $c['estado']; ?>
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <a href="index.php?page=admin_ver_resultados&id_concurso=<?php echo $c['id_concurso']; ?>"
                                                    class="btn btn-primary btn-sm btn-action">
                                                    👁️ Ver Resultados
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Información adicional -->
                        <div class="p-3 bg-light border-top">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>💡 Estados del Concurso:</h6>
                                    <ul class="list-unstyled small">
                                        <li><span class="badge bg-success me-1">🟢</span> <strong>Activo:</strong> En proceso de calificación</li>
                                        <li><span class="badge bg-danger me-1">🔴</span> <strong>Cerrado:</strong> Finalizado con resultados</li>
                                        <li><span class="badge bg-warning text-dark me-1">🟡</span> <strong>Pendiente:</strong> Próximo a iniciar</li>
                                    </ul>

                                </div>
                                <div class="col-md-6">
                                    <h6>🚀 Acciones Disponibles:</h6>
                                    <ul class="list-unstyled small">
                                        <li>👁️ <strong>Ver Resultados:</strong> Ver ranking y descargar PDF</li>
                                        <li>📄 <strong>PDF Oficial:</strong> Documento listo para impresión</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    <?php else: ?>
                        <div class="text-center p-5">
                            <div class="text-muted">
                                <h4>📭 No hay concursos disponibles</h4>
                                <p>No se encontraron concursos en el sistema.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
// views/presidente/seleccionar_concursos.php - VERSIÓN MEJORADA
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Concurso - Presidente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-action {
            min-width: 140px;
        }

        .concurso-row:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-3 bg-dark text-white sidebar" id="sidebar">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Presidente</h4>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="toggleSidebarBtn" title="Ocultar menú">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                <div class="p-3 pt-0">
                    <p class="text-muted mb-1">Sesión activa</p>
                    <p class="fw-bold text-warning"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></p>
                    <hr class="bg-light">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=presidente_seleccionar_concurso" class="nav-link text-white">
                                Seleccionar Concursos
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="index.php?page=logout" class="nav-link text-danger">
                                🚪 Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-md-9" id="mainContent">
                <div class="p-4">
                    <button class="btn btn-outline-dark mb-3 d-none" id="showSidebarBtn" title="Mostrar menú">
                        <i class="bi bi-chevron-right"></i>
                    </button>


                    <!-- Main Content -->
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">🏆 Seleccionar Concurso</h2>
                        <span class="badge bg-primary fs-6">Presidente</span>
                    </div>

                    <!-- Mostrar mensajes -->
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
                                case 'sin_conjuntos':
                                    echo 'El concurso no tiene conjuntos participantes';
                                    break;
                                default:
                                    echo 'Error al cargar los datos';
                            }
                            ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

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
                                            <?php foreach ($concursos as $concurso): ?>
                                                <tr class="concurso-row">
                                                    <td class="text-center fw-bold"><?php echo $concurso['id_concurso']; ?></td>
                                                    <td>
                                                        <div class="fw-semibold"><?php echo htmlspecialchars($concurso['nombre']); ?></div>
                                                    </td>
                                                    <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($concurso['fecha_inicio'])); ?></td>
                                                    <td class="text-center"><?php echo date('d/m/Y H:i', strtotime($concurso['fecha_fin'])); ?></td>
                                                    <td class="text-center">
                                                        <?php
                                                        $badge_class = 'bg-secondary';
                                                        $icon = '📅';
                                                        if ($concurso['estado'] == 'Activo') {
                                                            $badge_class = 'bg-success';
                                                            $icon = '🟢';
                                                        } elseif ($concurso['estado'] == 'Pendiente') {
                                                            $badge_class = 'bg-warning';
                                                            $icon = '🟡';
                                                        } elseif ($concurso['estado'] == 'Cerrado') {
                                                            $badge_class = 'bg-danger';
                                                            $icon = '🔴';
                                                        }
                                                        ?>
                                                        <span class="badge <?php echo $badge_class; ?>">
                                                            <?php echo $icon . ' ' . $concurso['estado']; ?>
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <!-- BOTÓN PARA VER RESULTADOS -->
                                                        <a href="index.php?page=presidente_revisar_resultados&id_concurso=<?php echo $concurso['id_concurso']; ?>"
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
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<!-- Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

<script>
    const sidebar = document.getElementById('sidebar');
    const mainContent = document.getElementById('mainContent');
    const toggleBtn = document.getElementById('toggleSidebarBtn');
    const showBtn = document.getElementById('showSidebarBtn');

    toggleBtn.addEventListener('click', () => {
        sidebar.style.display = 'none';
        mainContent.classList.remove('col-md-9');
        mainContent.classList.add('col-md-12');
        showBtn.classList.remove('d-none');
    });

    showBtn.addEventListener('click', () => {
        sidebar.style.display = 'block';
        mainContent.classList.remove('col-md-12');
        mainContent.classList.add('col-md-9');
        showBtn.classList.add('d-none');
    });
</script>

<style>
    .sidebar {
        min-height: 100vh;
        transition: all 0.3s ease;
    }

    #showSidebarBtn {
        position: fixed;
        top: 20px;
        left: 10px;
        z-index: 1000;
    }

    #toggleSidebarBtn i {
        transition: transform 0.3s ease;
    }

    #toggleSidebarBtn:hover i {
        transform: translateX(-2px);
    }
</style>

</html>
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
            overflow-x: hidden;
        }

        /* === SIDEBAR === */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            width: 250px;
            background: linear-gradient(to bottom, #c9184a, #800f2f);
            color: white;
            transition: all 0.3s ease;
            padding: 0;
            box-shadow: 3px 0 15px rgba(201, 24, 74, 0.3);
        }

        /* Estado oculto */
        .sidebar-hidden {
            transform: translateX(-100%) !important;
            width: 250px !important;
        }

        .sidebar-hidden * {
            visibility: hidden;
        }

        /* Enlaces dentro del sidebar */
        .sidebar a {
            color: #ecf0f1;
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #34495e;
            color: #ffffff;
        }

        /* Detalles del sidebar */
        .sidebar hr {
            border-color: rgba(255, 255, 255, 0.3);
        }

        .sidebar .bg-light {
            background-color: rgba(255, 255, 255, 0.1) !important;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .sidebar .text-warning {
            color: #ffd166 !important;
        }

        /* Botón toggle dentro del sidebar */
        #toggleSidebarBtn {
            display: flex !important;
            visibility: visible !important;
            transition: all 0.3s ease;
        }

        #toggleSidebarBtn:hover {
            background: #34495e;
        }

        /* Overlay para móvil */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 99;
        }

        .sidebar-overlay.show {
            display: block;
        }

        /* Botón para mostrar sidebar cuando está oculto */
        #showSidebarBtn {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 98;
            background: linear-gradient(to right, #c9184a, #800f2f);
            color: white;
            border: none;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        #showSidebarBtn.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #showSidebarBtn:hover {
            transform: scale(1.05);
        }

        /* Contenido principal */
        #mainContent {
            margin-left: 250px;
            transition: all 0.3s ease;
            padding: 20px;
            min-height: 100vh;
        }

        .main-content-full {
            margin-left: 0 !important;
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

        /* Responsive del sidebar */
        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
                width: 280px !important;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .sidebar-hidden {
                transform: translateX(-100%) !important;
            }

            #mainContent {
                margin-left: 0 !important;
                padding: 15px;
            }

            #showSidebarBtn {
                display: flex;
            }

            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <?php if (!isset($user)) $user = $_SESSION['user'] ?? null; ?>
    <div class="sidebar" id="sidebar">
        <div class="p-3 d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <i class="bi bi-person-badge fs-4 me-2 text-warning"></i>
                <div>
                    <h5 class="mb-0">Administrador</h5>
                    <small class="text-light opacity-75">Panel de Control</small>
                </div>
            </div>
            <button class="btn btn-sm btn-outline-light rounded-circle" id="toggleSidebarBtn" title="Ocultar menú">
                <i class="bi bi-chevron-left"></i>
            </button>
        </div>

        <div class="p-3 pt-0">
            <div class="bg-light bg-opacity-10 rounded p-2 mb-3">
                <p class="text-light mb-1 opacity-75">Sesión activa</p>
                <p class="fw-bold text-warning mb-0">
                    <i class="bi bi-person-circle me-1"></i>
                    <?= htmlspecialchars($user['nombre'] ?? 'Admin') ?>
                </p>
            </div>

            <hr class="opacity-50">

            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="index.php?page=admin_dashboard" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-person-badge me-2"></i> Panel de Control
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_gestion_concursos" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-trophy me-2"></i> Gestionar Concursos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_gestionar_conjuntos_globales" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-people me-2"></i> Conjuntos Globales
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_gestionar_criterios" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-list-task me-2"></i> Criterios Globales
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_gestion_jurados" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-person-badge me-2"></i> Gestión de Jurados
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_resultados" class="nav-link text-white d-flex align-items-center active">
                        <i class="bi bi-graph-up-arrow me-2"></i> Resultados en Vivo
                    </a>
                </li>
                <li class="nav-item mt-2">
                    <a href="index.php?page=logout" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Botón para mostrar sidebar en móvil o cuando está oculto -->
    <button class="btn" id="showSidebarBtn">
        <i class="bi bi-list"></i>
    </button>

    <!-- Contenido principal -->
    <div id="mainContent">
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

        <div class="container-fluid px-0">

            <!-- Mensajes de error -->
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
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const showBtn = document.getElementById('showSidebarBtn');
            const overlay = document.getElementById('sidebarOverlay');
            const toggleIcon = toggleBtn?.querySelector('i');

            let sidebarVisible = true;

            // Función para actualizar visibilidad del botón show
            function updateShowButton() {
                if (window.innerWidth < 768) {
                    // En móvil, mostrar botón siempre
                    showBtn.classList.add('show');
                    if (!sidebar.classList.contains('show')) {
                        showBtn.innerHTML = '<i class="bi bi-list"></i>';
                    } else {
                        showBtn.innerHTML = '<i class="bi bi-x"></i>';
                    }
                } else {
                    // En escritorio, mostrar botón solo si sidebar está oculto
                    if (sidebarVisible) {
                        showBtn.classList.remove('show');
                    } else {
                        showBtn.classList.add('show');
                        showBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
                    }
                }
            }

            // Función para ocultar sidebar
            function hideSidebar() {
                sidebar.classList.add('sidebar-hidden');
                mainContent.classList.add('main-content-full');
                sidebarVisible = false;
                overlay.classList.remove('show');
                updateShowButton();
            }

            // Función para mostrar sidebar
            function showSidebar() {
                sidebar.classList.remove('sidebar-hidden');
                mainContent.classList.remove('main-content-full');
                sidebarVisible = true;

                if (window.innerWidth < 768) {
                    sidebar.classList.add('show');
                    overlay.classList.add('show');
                }
                updateShowButton();
            }

            // Toggle sidebar en escritorio
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    if (sidebarVisible) {
                        hideSidebar();
                    } else {
                        showSidebar();
                    }
                });
            }

            // Mostrar/ocultar sidebar cuando se hace clic en el botón show
            if (showBtn) {
                showBtn.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        // En móvil, toggle del overlay
                        if (sidebar.classList.contains('show')) {
                            hideSidebar();
                        } else {
                            showSidebar();
                        }
                    } else {
                        // En escritorio, mostrar sidebar
                        showSidebar();
                    }
                });
            }

            // Cerrar sidebar al hacer clic en el overlay (móvil)
            if (overlay) {
                overlay.addEventListener('click', function() {
                    hideSidebar();
                });
            }

            // Manejo responsive
            function handleResize() {
                if (window.innerWidth < 768) {
                    // En móvil, comportamiento overlay
                    sidebar.classList.remove('sidebar-hidden');
                    mainContent.classList.remove('main-content-full');
                    if (!sidebarVisible) {
                        hideSidebar();
                    } else {
                        showBtn.classList.add('show');
                        showBtn.innerHTML = '<i class="bi bi-list"></i>';
                    }
                } else {
                    // En escritorio, comportamiento normal
                    overlay.classList.remove('show');
                    sidebar.classList.remove('show');
                    if (sidebarVisible) {
                        showSidebar();
                    } else {
                        hideSidebar();
                    }
                }
                updateShowButton();
            }

            // Inicializar
            updateShowButton();
            handleResize();
            window.addEventListener('resize', handleResize);

            // Cerrar sidebar al hacer clic en un link (móvil)
            const sidebarLinks = sidebar.querySelectorAll('a');
            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 768) {
                        hideSidebar();
                    }
                });
            });
        });
    </script>
</body>

</html>
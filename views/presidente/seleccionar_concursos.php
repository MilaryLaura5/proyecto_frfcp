<?php
// views/presidente/seleccionar_concursos.php - VERSIÓN MEJORADA CON SIDEBAR ROJO Y TOGGLE COMPLETO
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seleccionar Concurso - Presidente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
        }

        .btn-action {
            min-width: 140px;
        }

        .concurso-row:hover {
            background-color: #fdf2f2;
            transform: scale(1.005);
            transition: all 0.2s ease;
        }

        /* SIDEBAR ROJO OSCURO MEJORADO */
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #600000, #800000, #a00000);
            color: white;
            transition: all 0.3s ease;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.3);
            border-right: 3px solid #400000;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Título principal en rojo */
        .page-title {
            color: #c9184a;
            font-weight: 700;
        }

        /* Encabezado de tarjeta */
        .card-header {
            background: linear-gradient(to right, #c9184a, #800f2f);
            color: white;
            border: none;
        }

        /* Tabla */
        .table thead th {
            background-color: #fdf2f2;
            color: #495057;
            font-weight: 600;
        }

        /* Badge de rol */
        .badge-presidente {
            background: linear-gradient(to right, #c9184a, #800f2f);
            color: white;
            font-weight: 600;
        }

        /* Botones sidebar */
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: #8b0000;
            border: none;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        #showSidebarBtn:hover {
            transform: scale(1.1);
            background: #a00000;
        }

        #toggleSidebarBtn {
            transition: all 0.3s ease;
        }

        #toggleSidebarBtn:hover {
            transform: translateX(-2px);
        }

        /* Estado cuando el sidebar está oculto */
        .sidebar-hidden #sidebar {
            display: none !important;
        }

        .sidebar-hidden #mainContent {
            width: 100% !important;
            margin-left: 0 !important;
        }

        .sidebar-hidden #showSidebarBtn {
            display: flex !important;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            #showSidebarBtn {
                display: flex;
            }

            .btn-action {
                min-width: 120px;
                font-size: 0.875rem;
            }
        }

        /* Mejoras visuales */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .alert {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        /* Transición suave para el contenido principal */
        #mainContent {
            transition: all 0.3s ease;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR ROJO MEJORADO -->
            <div class="col-md-3 col-lg-2 sidebar p-0" id="sidebar">
                <div class="p-3 d-flex justify-content-between align-items-center border-bottom border-light border-opacity-25">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge fs-4 me-2 text-warning"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Presidente</h5>
                            <small class="text-light opacity-75">Panel de Control</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="toggleSidebarBtn" title="Ocultar menú">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                <div class="p-3 pt-3">
                    <div class="bg-light bg-opacity-10 rounded p-2 mb-3">
                        <p class="text-light mb-1 opacity-75 small">Sesión activa</p>
                        <p class="fw-bold text-warning mb-0">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['user']['nombre']); ?>
                        </p>
                    </div>

                    <hr class="bg-light opacity-25 my-3">

                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=presidente_seleccionar_concurso"
                                class="nav-link text-white d-flex align-items-center active">
                                <i class="bi bi-trophy me-2"></i>
                                Seleccionar Concursos
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="index.php?page=logout"
                                class="nav-link text-white d-flex align-items-center bg-danger bg-opacity-50">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-md-9 col-lg-10" id="mainContent">
                <!-- Botón para mostrar sidebar cuando está oculto -->
                <button class="btn d-none" id="showSidebarBtn" title="Mostrar menú">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="page-title">🏆 Seleccionar Concurso</h2>
                        <span class="badge badge-presidente fs-6 px-3 py-2">Presidente</span>
                    </div>

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
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">📊 Concursos Disponibles</h5>
                            <span class="badge bg-light text-primary fs-6"><?php echo count($concursos); ?> concursos</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($concursos)): ?>
                                <div class="table-responsive">
                                    <table class="table table-hover table-striped mb-0">
                                        <thead class="table-light">
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
                                                            $badge_class = 'bg-warning text-dark';
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Elementos del DOM
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const showBtn = document.getElementById('showSidebarBtn');
        const body = document.body;

        // Estado del sidebar
        let sidebarVisible = true;

        // Función para ocultar sidebar
        function hideSidebar() {
            sidebar.classList.add('d-none');
            mainContent.classList.remove('col-md-9', 'col-lg-10');
            mainContent.classList.add('col-12');
            showBtn.classList.remove('d-none');
            body.classList.add('sidebar-hidden');
            sidebarVisible = false;

            // Cambiar el ícono del botón show
            showBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        }

        // Función para mostrar sidebar
        function showSidebar() {
            sidebar.classList.remove('d-none');
            mainContent.classList.remove('col-12');
            mainContent.classList.add('col-md-9', 'col-lg-10');
            showBtn.classList.add('d-none');
            body.classList.remove('sidebar-hidden');
            sidebarVisible = true;
        }

        // Event listeners
        toggleBtn.addEventListener('click', hideSidebar);
        showBtn.addEventListener('click', showSidebar);

        // Sidebar móvil
        function handleMobileSidebar() {
            if (window.innerWidth < 768) {
                // En móvil, usar el sistema de overlay
                showBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Cerrar sidebar al hacer clic fuera
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 768 &&
                        !sidebar.contains(event.target) &&
                        !showBtn.contains(event.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            } else {
                // En escritorio, usar el sistema de toggle normal
                sidebar.classList.remove('show');
            }
        }

        // Manejo responsive
        function handleResize() {
            if (window.innerWidth >= 768) {
                // En escritorio, asegurarse de que el sidebar esté visible
                sidebar.classList.remove('show');
                if (!sidebarVisible) {
                    showSidebar();
                }
            } else {
                // En móvil, ocultar sidebar por defecto
                if (sidebarVisible) {
                    hideSidebar();
                }
            }
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            handleMobileSidebar();
            handleResize();
        });

        window.addEventListener('resize', handleResize);
    </script>
</body>

</html>
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Administrador</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            width: 250px;
            background-color: #2c3e50;
            color: white;
            transition: all 0.3s ease;
            padding: 0;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.2);
        }

        /* ESTADO COLAPSADO - SE OCULTA TODO */
        .sidebar-collapsed {
            width: 60px !important;
        }

        .sidebar-collapsed .nav-link span,
        .sidebar-collapsed h5,
        .sidebar-collapsed p,
        .sidebar-collapsed hr,
        .sidebar-collapsed .d-flex>div:not(:has(i)),
        .sidebar-collapsed .bg-light {
            display: none !important;
        }

        .sidebar-collapsed .nav-link {
            justify-content: center !important;
            padding: 12px 5px !important;
        }

        .sidebar-collapsed .nav-link i {
            margin-right: 0 !important;
            font-size: 1.2rem;
        }

        .sidebar-collapsed .d-flex {
            justify-content: center !important;
        }

        .sidebar a {
            color: #ecf0f1;
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        @media (max-width: 767.98px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }
        }

        .main-content {
            transition: margin-left 0.3s ease;
            padding: 20px;
            min-height: 100vh;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }

            .main-content-expanded {
                margin-left: 60px !important;
            }
        }

        .card-dashboard {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            border: none;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Íconos en rojo vibrante */
        .icon-red {
            color: #c9184a;
            font-size: 2rem;
        }

        /* Badge de rol en rojo oscuro */
        .badge-admin {
            background: linear-gradient(to right, #c9184a, #800f2f);
            font-weight: 600;
        }

        /* Botón mostrar sidebar en móvil */
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: #2c3e50;
            border: 1px solid rgba(255, 255, 255, 0.3);
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
            background: #34495e;
            transform: scale(1.1);
        }

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0 !important;
                width: 100%;
            }

            #showSidebarBtn {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <!-- Botón para mostrar sidebar en móvil -->
    <button class="btn" id="showSidebarBtn" title="Mostrar menú">
        <i class="bi bi-list"></i>
    </button>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
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
                            <?php echo htmlspecialchars($user['nombre']); ?>
                        </p>
                    </div>

                    <hr class="opacity-50">

                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=admin_gestion_concursos" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-trophy me-2"></i>
                                <span>Gestionar Concursos</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=admin_gestionar_conjuntos_globales" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-people me-2"></i>
                                <span>Conjuntos Globales</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=admin_gestionar_criterios" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-list-task me-2"></i>
                                <span>Criterios Globales</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=admin_gestion_jurados" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-person-badge me-2"></i>
                                <span>Gestión de Jurados</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=admin_resultados" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-graph-up-arrow me-2"></i>
                                <span>Resultados en Vivo</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="index.php?page=admin_importar_csv" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-file-earmark-spreadsheet me-2"></i>
                                <span>Importar CSV</span>
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="index.php?page=logout" class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                <span>Cerrar sesión</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content" id="mainContent">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Centro de Control del Administrador</h2>
                    <span class="badge badge-admin fs-6 px-3 py-2">Rol: Administrador</span>
                </div>

                <div class="row g-4">
                    <!-- Gestionar Concursos -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestion_concursos" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-trophy icon-red"></i>
                                    <h5 class="mt-2">Concursos</h5>
                                    <p class="text-muted mb-0">Crear, activar y cerrar eventos</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Gestionar Conjuntos Globales -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestionar_conjuntos_globales" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-people icon-red"></i>
                                    <h5 class="mt-2">Conjuntos</h5>
                                    <p class="text-muted mb-0">Administrar agrupaciones culturales</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Criterios Globales -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestionar_criterios" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-list-task icon-red"></i>
                                    <h5 class="mt-2">Criterios</h5>
                                    <p class="text-muted mb-0">Definir criterios reutilizables</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Gestión de Jurados -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestion_jurados" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-person-badge icon-red"></i>
                                    <h5 class="mt-2">Jurados</h5>
                                    <p class="text-muted mb-0">Asignar jurados y generar credenciales</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Resultados en Vivo -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_resultados" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-graph-up-arrow icon-red"></i>
                                    <h5 class="mt-2">Resultados en Vivo</h5>
                                    <p class="text-muted mb-0">Ver resultados en tiempo real</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Importación Masiva -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_importar_csv" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-spreadsheet icon-red"></i>
                                    <h5 class="mt-2">Importar CSV</h5>
                                    <p class="text-muted mb-0">Cargar conjuntos o jurados masivamente</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Soporte / Documentación -->
                    <div class="col-xl-3 col-md-6">
                        <a href="#" class="text-decoration-none" onclick="alert('Documentación disponible bajo solicitud'); return false;">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-question-circle icon-red"></i>
                                    <h5 class="mt-2">Ayuda</h5>
                                    <p class="text-muted mb-0">Guías y soporte técnico</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Mensaje informativo -->
                <div class="alert alert-light mt-4 border">
                    <strong>💡 Consejo:</strong> Usa los <strong>criterios globales</strong> para ahorrar tiempo al configurar nuevos concursos.
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const showBtn = document.getElementById('showSidebarBtn');
            const toggleIcon = toggleBtn?.querySelector('i');

            let sidebarExpanded = true;

            // Función para expandir sidebar
            function expandSidebar() {
                sidebar.classList.remove('sidebar-collapsed');
                mainContent.classList.remove('main-content-expanded');
                if (toggleIcon) {
                    toggleIcon.classList.remove('bi-chevron-right');
                    toggleIcon.classList.add('bi-chevron-left');
                }
                sidebarExpanded = true;
            }

            // Función para colapsar sidebar
            function collapseSidebar() {
                sidebar.classList.add('sidebar-collapsed');
                mainContent.classList.add('main-content-expanded');
                if (toggleIcon) {
                    toggleIcon.classList.remove('bi-chevron-left');
                    toggleIcon.classList.add('bi-chevron-right');
                }
                sidebarExpanded = false;
            }

            // Toggle sidebar en escritorio
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function() {
                    if (sidebarExpanded) {
                        collapseSidebar();
                    } else {
                        expandSidebar();
                    }
                });
            }

            // Sidebar móvil
            if (showBtn) {
                showBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Cerrar sidebar al hacer clic fuera en móvil
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 768 &&
                        !sidebar.contains(event.target) &&
                        !showBtn.contains(event.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }

            // Manejo responsive
            function handleResize() {
                if (window.innerWidth < 768) {
                    // En móvil
                    sidebar.classList.remove('sidebar-collapsed');
                    mainContent.classList.remove('main-content-expanded');
                    showBtn.style.display = 'flex';
                } else {
                    // En escritorio
                    showBtn.style.display = 'none';
                    sidebar.classList.remove('show');
                    // Restaurar estado del toggle
                    if (!sidebarExpanded) {
                        collapseSidebar();
                    } else {
                        expandSidebar();
                    }
                }
            }

            // Inicializar
            handleResize();
            window.addEventListener('resize', handleResize);
        });
    </script>
</body>

</html>
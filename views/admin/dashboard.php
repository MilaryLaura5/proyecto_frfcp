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
            background: linear-gradient(to bottom, #c9184a, #800f2f);
            /* ROJO DEL CÓDIGO QUE ME PASASTE */
            color: white;
            transition: all 0.3s ease;
            padding: 0;
            box-shadow: 3px 0 15px rgba(201, 24, 74, 0.3);
        }

        /* ESTADO COMPLETAMENTE OCULTO */
        .sidebar-hidden {
            transform: translateX(-100%) !important;
            width: 250px !important;
        }

        .sidebar-hidden * {
            visibility: hidden;
        }

        .sidebar a {
            color: #ecf0f1;
            /* COLOR DEL TEXTO COMO EN EL CÓDIGO EJEMPLO */
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #34495e;
            /* AZUL OSCURO COMO EN EL CÓDIGO EJEMPLO */
            color: #ffffff;
        }

        /* COMPORTAMIENTO EN MÓVIL */
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
        }

        .main-content {
            transition: all 0.3s ease;
            padding: 20px;
            min-height: 100vh;
            width: 100%;
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
                width: calc(100% - 250px);
            }

            .main-content-full {
                margin-left: 0 !important;
                width: 100% !important;
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

        /* Botón mostrar sidebar - SIEMPRE VISIBLE CUANDO SEA NECESARIO */
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 99;
            background: #c9184a;
            /* MISMO ROJO DEL SIDEBAR */
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            width: 45px;
            height: 45px;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 3px 10px rgba(201, 24, 74, 0.3);
            transition: all 0.3s ease;
            display: none;
        }

        #showSidebarBtn:hover {
            background: #34495e;
            /* AZUL OSCURO COMO EN EL HOVER DEL SIDEBAR */
            transform: scale(1.1);
        }

        #showSidebarBtn.show {
            display: flex !important;
        }

        /* Botón toggle dentro del sidebar - SIEMPRE VISIBLE */
        #toggleSidebarBtn {
            display: flex !important;
            visibility: visible !important;
            transition: all 0.3s ease;
        }

        #toggleSidebarBtn:hover {
            background: #34495e;
            /* AZUL OSCURO COMO EN EL HOVER DEL SIDEBAR */
        }

        /* Estilos adicionales para el sidebar rojo */
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

        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0 !important;
                width: 100% !important;
                padding: 15px;
            }

            #showSidebarBtn {
                display: flex !important;
            }

            .sidebar-overlay.show {
                display: block;
            }
        }
    </style>
</head>

<body>
    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Botón para mostrar sidebar cuando está oculto -->
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
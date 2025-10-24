<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Dashboard - Administrador</title>
    <!-- ✅ Corrección: espacios eliminados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #2c3e50;
            color: white;
        }

        .sidebar a {
            color: #ecf0f1;
            transition: background-color 0.2s;
        }

        .sidebar a:hover {
            background-color: #34495e;
        }

        .main-content {
            padding: 20px;
        }

        .card-dashboard {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .badge {
            font-size: 0.8em;
        }

        .sidebar {
            position: fixed;
            top: 0;
            bottom: 0;
            z-index: 100;
            padding-left: 0;
            padding-right: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            width: 250px;
            transition: width 0.3s ease;
            padding: 0;
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
        }

        @media (min-width: 768px) {
            .main-content {
                margin-left: 250px;
            }

            .sidebar.collapsed {
                width: 60px;
            }

            .sidebar.collapsed .nav-link,
            .sidebar.collapsed h5,
            .sidebar.collapsed p,
            .sidebar.collapsed hr {
                display: none !important;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar reutilizable -->
            <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Centro de Control del Administrador</h2>
                    <span class="badge bg-primary fs-6">Rol: Administrador</span>
                </div>

                <div class="row g-4">
                    <!-- Gestionar Concursos -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestion_concursos" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-trophy text-warning" style="font-size: 2rem;"></i>
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
                                    <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
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
                                    <i class="bi bi-list-task text-primary" style="font-size: 2rem;"></i>
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
                                    <i class="bi bi-person-badge text-success" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2">Jurados</h5>
                                    <p class="text-muted mb-0">Asignar jurados y generar credenciales</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Resultados en Vivo -->
                    <!-- Resultados en Vivo - Entra directamente a selección de concursos -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_resultados" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-graph-up-arrow text-danger" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2">Resultados en Vivo</h5>
                                    <p class="text-muted mb-0">Ver resultados en tiempo real</p>
                                </div>
                            </div>
                        </a>
                    </div>

                    <!-- Importación Masiva -->
                    <div class="col-xl-3 col-md-6">
                        <a href="index.php?page=admin_gestionar_conjuntos_globales" class="text-decoration-none">
                            <div class="card card-dashboard shadow-sm">
                                <div class="card-body text-center">
                                    <i class="bi bi-file-earmark-spreadsheet text-secondary" style="font-size: 2rem;"></i>
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
                                    <i class="bi bi-question-circle text-dark" style="font-size: 2rem;"></i>
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

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const mainContent = document.querySelector('.main-content'); // Asegúrate de que esta clase exista en tu contenedor principal
            const icon = toggleBtn.querySelector('i');

            let isExpanded = true;

            toggleBtn.addEventListener('click', function() {
                if (isExpanded) {
                    // Colapsar
                    sidebar.style.width = '60px';
                    sidebar.querySelectorAll('.nav-link, h5, p, hr').forEach(el => el.style.display = 'none');
                    icon.classList.remove('bi-chevron-left');
                    icon.classList.add('bi-chevron-right');
                    // Cambiar el contenido principal a 12 columnas
                    mainContent.className = mainContent.className.replace(/\bcol-md-\d+\b/g, 'col-md-12');
                } else {
                    // Expandir
                    sidebar.style.width = '';
                    sidebar.querySelectorAll('.nav-link, h5, p, hr').forEach(el => el.style.display = '');
                    icon.classList.remove('bi-chevron-right');
                    icon.classList.add('bi-chevron-left');
                    // Volver a 9 columnas
                    mainContent.className = mainContent.className.replace(/\bcol-md-\d+\b/g, 'col-md-9');
                }
                isExpanded = !isExpanded;
            });
        });
    </script>
</body>

</html>
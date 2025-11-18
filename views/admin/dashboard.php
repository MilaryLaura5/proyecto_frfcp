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
        /* === NO ES DEL SIDEBAR === */
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', system-ui, sans-serif;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Contenido principal */
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

        /* Tarjetas y estilo visual del contenido */
        .card-dashboard {
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
            border: none;
        }

        .card-dashboard:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        }

        /* Íconos y badges */
        .icon-red {
            color: #c9184a;
            font-size: 2rem;
        }

        .badge-admin {
            background: linear-gradient(to right, #c9184a, #800f2f);
            font-weight: 600;
        }

        /* Botón para mostrar sidebar (fuera de la barra) */
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 99;
            background: #c9184a;
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
            transform: scale(1.1);
        }

        #showSidebarBtn.show {
            display: flex !important;
        }

        /* Ajustes responsive del contenido */
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
            <?php require_once __DIR__ . '/partials/sidebar.php'; ?>
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

</body>

</html>
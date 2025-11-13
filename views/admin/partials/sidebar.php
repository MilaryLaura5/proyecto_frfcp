<style>
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
    }
</style>

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
                <a href="index.php?page=admin_resultados" class="nav-link text-white d-flex align-items-center">
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
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Criterios - FRFCP</title>
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

        .list-group-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #fdf2f2;
        }

        .criterios-scroll {
            max-height: 500px;
            overflow-y: auto;
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

            .row>div {
                margin-bottom: 1rem;
            }

            .criterios-scroll {
                max-height: 300px;
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
                    <a href="index.php?page=admin_gestionar_criterios" class="nav-link text-white d-flex align-items-center active">
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
                    <i class="bi bi-list-task me-2"></i> Gestión de Criterios y Concursos
                </h2>
                <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="container-fluid px-0">

            <!-- Mensajes -->
            <?php if (isset($_GET['success']) && $_GET['success'] === 'asignado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle"></i>
                    <strong>¡Éxito!</strong> El criterio fue asignado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <?php switch ($_GET['error']):
                    case 'dato_invalido': ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Error:</strong> Datos inválidos.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php break; ?>
                    <?php
                    case 'no_guardado': ?>
                        <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
                            <i class="bi bi-exclamation-circle"></i>
                            <strong>Advertencia:</strong> No se pudo guardar.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php break; ?>
                    <?php
                    case 'db': ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                            <i class="bi bi-database"></i>
                            <strong>Error de base de datos.</strong> Contacta al administrador.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php break; ?>
                <?php endswitch; ?>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Panel izquierdo: Criterios Generales -->
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-tags"></i> Criterios Generales</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="text-muted">Crea y gestiona criterios globales.</p>

                            <form method="POST" action="index.php?page=admin_gestionar_criterios" class="mb-3">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Nombre del Criterio</strong></label>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        name="nombre"
                                        placeholder="Ej: Coreografía"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-success btn-lg w-100">Agregar Criterio</button>
                            </form>

                            <ul class="list-group flex-fill criterios-scroll mt-3">
                                <?php foreach ($criterios as $c): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox"
                                                value="<?= $c['id_criterio'] ?>"
                                                id="criterio_<?= $c['id_criterio'] ?>">
                                            <label class="form-check-label" for="criterio_<?= $c['id_criterio'] ?>">
                                                <?= htmlspecialchars($c['nombre']) ?>
                                            </label>
                                        </div>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Panel derecho: Asignación -->
                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h5><i class="bi bi-calendar-event"></i> Asignar Criterios al Concurso</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="mb-3">
                                <label class="form-label"><strong>Concurso</strong></label>
                                <select class="form-control form-select-lg" id="selectConcurso" onchange="cambiarConcurso()">
                                    <option value="">Selecciona un concurso</option>
                                    <?php foreach ($concursos as $c): ?>
                                        <option value="<?= $c['id_concurso'] ?>" <?= $id_concurso == $c['id_concurso'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['nombre']) ?> (<?= ucfirst($c['estado']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <?php if ($id_concurso): ?>
                                <form method="POST" action="index.php?page=admin_guardar_criterios_concurso" class="mb-4" id="formAsignacion">
                                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
                                    <div class="alert alert-light">
                                        <strong>Selecciona criterios del panel izquierdo para asignarlos:</strong>
                                    </div>

                                    <div id="criteriosAsignados" class="mb-3">
                                        <p class="text-muted">Selecciona criterios del panel izquierdo.</p>
                                    </div>

                                    <button type="submit" class="btn btn-primary btn-lg w-100" id="btnAsignar" disabled>
                                        Asignar Criterios Seleccionados
                                    </button>
                                </form>

                                <div class="mt-4">
                                    <?php if (!empty($criterios_asignados)): ?>
                                        <div class="alert alert-light">
                                            <strong>Criterios ya asignados a este concurso:</strong>
                                        </div>
                                        <ul class="list-group">
                                            <?php foreach ($criterios_asignados as $ca): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <strong><?= htmlspecialchars($ca['nombre']) ?></strong><br>
                                                        <small>Puntaje máximo: <strong><?= number_format($ca['puntaje_maximo'], 2) ?></strong> puntos</small>
                                                    </div>
                                                    <a href="#" class="btn btn-sm btn-danger"
                                                        onclick="eliminarCriterio(<?= $ca['id_criterio'] ?>, <?= $id_concurso ?>)">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <div class="alert alert-info text-center py-3">
                                            No hay criterios asignados aún.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info text-center py-3 mt-4">
                                    Selecciona un concurso para asignar criterios.
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
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

        // Script original de la página de criterios
        let criteriosSeleccionados = {};

        function actualizarVistaCriterios() {
            const contenedor = document.getElementById('criteriosAsignados');
            const btn = document.getElementById('btnAsignar');

            if (Object.keys(criteriosSeleccionados).length === 0) {
                contenedor.innerHTML = '<p class="text-muted">Selecciona criterios del panel izquierdo.</p>';
                btn.disabled = true;
                return;
            }

            let html = '<div class="row g-2">';
            for (const id in criteriosSeleccionados) {
                const c = criteriosSeleccionados[id];
                html += `
                    <div class="col-12" id="fila_${id}">
                        <div class="input-group">
                            <span class="input-group-text flex-grow-1">${c.nombre}</span>
                            <input type="number" 
                                   class="form-control" 
                                   name="puntajes[${id}]" 
                                   value="${c.puntaje || ''}" 
                                   step="0.01" min="0.01" max="100"
                                   placeholder="Puntaje" 
                                   required
                                   oninput="criteriosSeleccionados[${id}].puntaje = this.value">
                            <button class="btn btn-outline-danger" type="button" onclick="deseleccionarCriterio(${id})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
            html += '</div>';
            contenedor.innerHTML = html;
            btn.disabled = false;
        }

        function deseleccionarCriterio(id) {
            delete criteriosSeleccionados[id];
            const checkbox = document.querySelector(`input[value="${id}"]`);
            if (checkbox) checkbox.checked = false;
            actualizarVistaCriterios();
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = this.value;
                    const label = this.nextElementSibling.textContent.trim();
                    if (this.checked) {
                        criteriosSeleccionados[id] = {
                            id,
                            nombre: label,
                            puntaje: ''
                        };
                    } else {
                        delete criteriosSeleccionados[id];
                    }
                    actualizarVistaCriterios();
                });
            });
        });

        function cambiarConcurso() {
            const concursoId = document.getElementById('selectConcurso').value;
            if (concursoId) {
                window.location.href = 'index.php?page=admin_gestionar_criterios&id_concurso=' + concursoId;
            } else {
                window.location.href = 'index.php?page=admin_gestionar_criterios';
            }
        }

        function eliminarCriterio(idCriterio, idConcurso) {
            if (confirm('¿Eliminar este criterio del concurso?')) {
                window.location.href = 'index.php?page=admin_eliminar_criterio_concurso&id=' + idCriterio + '&id_concurso=' + idConcurso;
            }
        }
    </script>
</body>

</html>
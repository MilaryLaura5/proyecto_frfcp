<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Unificada: Tipos y Series - FRFCP</title>
    <!-- Bootstrap CSS (corregido: sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            /* Fondo suave con tono rojizo */
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
            transition: all 0.3s ease;
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

        /* Contenido principal */
        #mainContent {
            margin-left: 250px;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .main-content-full {
            margin-left: 0 !important;
        }

        /* Botón para mostrar sidebar cuando está oculto */
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 99;
            background: #c9184a;
            border: none;
            color: white;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: none;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        #showSidebarBtn.show {
            display: flex;
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

            #mainContent {
                margin-left: 0 !important;
            }
        }

        /* Estilos existentes de la página */
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
            /* 🔴 Rojo FRFCP */
            margin: 0;
        }

        .panel {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .panel h4 {
            color: #c9184a;
            margin-bottom: 0.75rem;
        }

        .panel h4 i {
            color: #c9184a;
        }

        .accordion-button {
            font-weight: 600;
            color: #c9184a;
        }

        .accordion-button:not(.collapsed) {
            background-color: #fdf2f2;
            color: #c9184a;
        }

        .list-group-item {
            border-left: none;
            border-right: none;
        }

        .list-group-item:first-child {
            border-top: none;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* Botones de acción en rojo */
        .btn-primary-red {
            background: linear-gradient(to right, #c9184a, #800f2f);
            border: none;
            font-weight: 600;
        }

        .btn-primary-red:hover {
            background: linear-gradient(to right, #b01545, #6a0d25);
            transform: translateY(-1px);
        }

        .btn-warning-red {
            background-color: #ff9e9e;
            color: #5a0000;
            border: none;
            font-weight: 600;
        }

        .btn-warning-red:hover {
            background-color: #ff7f7f;
            color: #3a0000;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .row.g-4>div {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>

<body>

    <!-- Botón para mostrar sidebar (oculto por defecto) -->
    <button class="btn" id="showSidebarBtn">
        <i class="bi bi-chevron-right"></i>
    </button>

    <!-- Overlay para móvil -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

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
                    <a href="index.php?page=admin_gestion_series" class="nav-link text-white d-flex align-items-center active">
                        <i class="bi bi-collection me-2"></i>Gestionar Series
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

    <!-- Contenido principal -->
    <div id="mainContent">
        <!-- Encabezado -->
        <div class="header-container">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="page-title">
                    Gestión Unificada: Tipos de Danza y Series
                </h2>
                <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="container-fluid px-4">

            <!-- Mensajes (sin cambios de color) -->
            <?php if ($error === 'vacios'): ?>
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ⚠️ Completa todos los campos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error === 'tipo_vacio'): ?>
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ⚠️ El nombre del tipo es obligatorio.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error === 'tipo_usado'): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ❌ No se puede eliminar: tiene series asociadas.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success == '1'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Serie creada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'editado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Serie actualizada.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'eliminado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Serie eliminada.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'tipo_creado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Tipo de danza creado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'tipo_editado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Tipo de danza actualizado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'tipo_eliminado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Tipo de danza eliminado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="row g-4">
                <!-- Panel izquierdo: Tipos de Danza -->
                <div class="col-md-5">
                    <div class="panel p-4">
                        <h4><i class="bi bi-tags"></i> Tipos de Danza</h4>
                        <p class="text-muted">Gestiona los tipos oficiales de danza.</p>

                        <!-- Formulario: Crear o Editar Tipo -->
                        <?php if ($editando_tipo): ?>
                            <form method="POST" action="index.php?page=admin_actualizar_tipo_danza" class="mb-4 border-bottom pb-4">
                                <input type="hidden" name="id_tipo" value="<?= $tipo_edit['id_tipo'] ?>">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Nombre</strong></label>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        name="nombre_tipo"
                                        value="<?= htmlspecialchars($tipo_edit['nombre_tipo']) ?>"
                                        required>
                                </div>
                                <div class="d-grid gap-2 d-md-flex">
                                    <button type="submit" class="btn btn-warning btn-lg">Actualizar Tipo</button>
                                    <a href="index.php?page=admin_gestion_series" class="btn btn-secondary btn-lg">Cancelar</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="index.php?page=admin_crear_tipo_danza" class="mb-4 border-bottom pb-4">
                                <div class="mb-3">
                                    <label class="form-label"><strong>Nombre del Tipo</strong></label>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        name="nombre_tipo"
                                        placeholder="Ej: Traje Originario"
                                        required>
                                </div>
                                <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Agregar Tipo</button>
                            </form>
                        <?php endif; ?>

                        <!-- Listado de tipos -->
                        <ul class=" list-group">
                            <?php foreach ($tipos as $t): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <strong><?= htmlspecialchars($t['nombre_tipo']) ?></strong>
                                    <span>
                                        <a href="index.php?page=admin_editar_tipo_danza&id=<?= $t['id_tipo'] ?>"
                                            class="btn btn-sm btn-warning me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?page=admin_eliminar_tipo_danza&id=<?= $t['id_tipo'] ?>"
                                            class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Eliminar? Solo si no tiene series.');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

                <!-- Panel derecho: Series por Tipo -->
                <div class="col-md-7">
                    <div class="panel p-4">
                        <h4><i class="bi bi-collection"></i> Series por Tipo de Danza</h4>
                        <p class="text-muted">Crea y gestiona series para cada tipo.</p>

                        <!-- Formulario: Crear/Editar Serie -->
                        <?php if ($editando_serie): ?>
                            <form method="POST" action="index.php?page=admin_actualizar_serie" class="mb-4 border-bottom pb-4">
                                <input type="hidden" name="id_serie" value="<?= $serie_edit['id_serie'] ?>">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label"><strong>Número</strong></label>
                                        <input type="number"
                                            class="form-control form-control-lg"
                                            name="numero_serie"
                                            min="1" max="99"
                                            value="<?= $serie_edit['numero_serie'] ?>"
                                            required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Nombre</strong></label>
                                        <input type="text"
                                            class="form-control form-control-lg"
                                            name="nombre_serie"
                                            value="<?= htmlspecialchars($serie_edit['nombre_serie']) ?>"
                                            required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label"><strong>Tipo</strong></label>
                                    <select class="form-control form-select-lg" name="id_tipo" required>
                                        <option value="">Selecciona un tipo</option>
                                        <?php foreach ($tipos as $t): ?>
                                            <option value="<?= $t['id_tipo'] ?>" <?= $serie_edit['id_tipo'] == $t['id_tipo'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($t['nombre_tipo']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="d-grid gap-2 d-md-flex mt-4">
                                    <button type="submit" class="btn btn-warning btn-lg">Actualizar Serie</button>
                                    <a href="index.php?page=admin_gestion_series" class="btn btn-secondary btn-lg">Cancelar</a>
                                </div>
                            </form>
                        <?php else: ?>
                            <form method="POST" action="index.php?page=admin_crear_serie_submit" class="mb-4 border-bottom pb-4">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label"><strong>Número</strong></label>
                                        <input type="number"
                                            class="form-control form-control-lg"
                                            name="numero_serie"
                                            min="1" max="99"
                                            required>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label"><strong>Nombre</strong></label>
                                        <input type="text"
                                            class="form-control form-control-lg"
                                            name="nombre_serie"
                                            placeholder="Ej: Carnavalescas Ligeras"
                                            required>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <label class="form-label"><strong>Tipo</strong></label>
                                    <select class="form-control form-select-lg" name="id_tipo" required>
                                        <option value="">Selecciona un tipo</option>
                                        <?php foreach ($tipos as $t): ?>
                                            <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre_tipo']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Crear Serie</button>
                            </form>
                        <?php endif; ?>

                        <!-- Acordeón de series por tipo -->
                        <div class="accordion" id="seriesAccordion">
                            <?php foreach ($tipos as $t): ?>
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="heading<?= $t['id_tipo'] ?>">
                                        <button class="accordion-button <?= $t['id_tipo'] > 1 ? 'collapsed' : '' ?>"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#tipo<?= $t['id_tipo'] ?>">
                                            <?= strtoupper(htmlspecialchars($t['nombre_tipo'])) ?>
                                        </button>
                                    </h2>
                                    <div id="tipo<?= $t['id_tipo'] ?>"
                                        class="accordion-collapse collapse <?= $t['id_tipo'] == 1 ? 'show' : '' ?>"
                                        data-bs-parent="#seriesAccordion">
                                        <div class="accordion-body">
                                            <?php
                                            $series_tipo = array_filter($series, fn($s) => $s['id_tipo'] == $t['id_tipo']);
                                            ?>
                                            <?php if (count($series_tipo) > 0): ?>
                                                <ul class="list-group">
                                                    <?php foreach ($series_tipo as $s): ?>
                                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <strong>SERIE <?= $s['numero_serie'] ?></strong>: <?= htmlspecialchars($s['nombre_serie']) ?>
                                                            </span>
                                                            <span>
                                                                <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                                    class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil"></i></a>
                                                                <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                                    class="btn btn-sm btn-danger"
                                                                    onclick="return confirm('¿Eliminar?');"><i class="bi bi-trash"></i></a>
                                                            </span>
                                                        </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            <?php else: ?>
                                                <p class="text-muted">No hay series.</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Script para el sidebar -->
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
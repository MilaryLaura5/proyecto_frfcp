<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Conjuntos Globales - FRFCP</title>
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

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 200px);
        }

        .table-responsive {
            max-height: 60vh;
            overflow-y: auto;
        }

        .sticky-top th {
            position: sticky;
            top: 0;
            background-color: #fdf2f2;
            z-index: 10;
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
                    <a href="index.php?page=admin_gestion_concursos" class="nav-link text-white d-flex align-items-center">
                        <i class="bi bi-trophy me-2"></i> Gestionar Concursos
                    </a>
                </li>
                <li class="nav-item">
                    <a href="index.php?page=admin_gestionar_conjuntos_globales" class="nav-link text-white d-flex align-items-center active">
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
                    <i class="bi bi-collection me-2"></i>
                    Gestionar Conjuntos Globales
                </h2>
                <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="container-fluid px-0">

            <!-- Mensajes -->
            <?php if ($error === 'vacios'): ?>
                <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ⚠️ Completa todos los campos.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error === 'duplicado'): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ❌ Ya existe un conjunto con ese nombre en esta serie.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($error === 'evaluado'): ?>
                <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ❌ No se puede eliminar: el conjunto ya fue evaluado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if ($success == '1'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Conjunto creado correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'editado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Conjunto actualizado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php elseif ($success == 'eliminado'): ?>
                <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                    ✅ Conjunto eliminado.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Fila superior: Formulario e Importar -->
            <div class="row g-4 mb-4">
                <!-- Formulario -->
                <div class="col-md-5">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h5><i class="bi <?= $editando ? 'bi-pencil' : 'bi-plus-circle' ?>"></i>
                                <?= $editando ? 'Editar Conjunto' : 'Nuevo Conjunto' ?>
                            </h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_conjunto_global' : 'admin_crear_conjunto_global_submit' ?>">
                                <?php if ($editando): ?>
                                    <input type="hidden" name="id_conjunto" value="<?= $conjunto_edit['id_conjunto'] ?>">
                                <?php endif; ?>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                                    <input type="text"
                                        class="form-control form-control-lg"
                                        name="nombre"
                                        value="<?= $editando ? htmlspecialchars($conjunto_edit['nombre']) : '' ?>"
                                        placeholder="Ej: Sikuris Huj'Maya"
                                        required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label"><strong>Serie</strong></label>
                                    <select class="form-control form-select-lg" name="id_serie" required>
                                        <option value="">Selecciona una serie</option>
                                        <?php foreach ($series as $s): ?>
                                            <option value="<?= $s['id_serie'] ?>"
                                                <?= ($editando && $conjunto_edit['id_serie'] == $s['id_serie']) ? 'selected' : '' ?>>
                                                SERIE <?= $s['numero_serie'] ?> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="mt-auto">
                                    <div class="d-grid gap-2 d-md-flex">
                                        <?php if ($editando): ?>
                                            <button type="submit" class="btn btn-warning btn-lg">Actualizar</button>
                                            <a href="index.php?page=admin_gestionar_conjuntos_globales" class="btn btn-secondary btn-lg">Cancelar</a>
                                        <?php else: ?>
                                            <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Registrar</button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Importar CSV -->
                <div class="col-md-7">
                    <div class="card shadow-sm h-100">
                        <div class="card-header">
                            <h5><i class="bi bi-file-earmark-spreadsheet"></i> Importar Múltiples Conjuntos desde CSV</h5>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <p class="text-muted">Formato del archivo CSV:</p>
                            <code>nombre,id_serie</code>
                            <p><small>Ejemplo:</small></p>
                            <code>Sikuris Huj'Maya,9</code>

                            <form method="POST" action="index.php?page=admin_importar_conjuntos_csv_global" enctype="multipart/form-data" class="mt-3">
                                <div class="mb-3">
                                    <label for="archivo_csv" class="form-label">Seleccionar archivo CSV</label>
                                    <input type="file" class="form-control form-control-lg" name="archivo_csv" accept=".csv" required>
                                </div>
                                <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Importar Conjuntos</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lista de conjuntos -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul"></i> Todos los Conjuntos Registrados
                            </h5>
                            <span class="badge bg-secondary"><?= count($conjuntos) ?> encontrados</span>
                        </div>
                        <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                            <?php if (count($conjuntos) > 0): ?>
                                <table class="table table-hover mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center" style="width: 5%;">#</th>
                                            <th style="width: 60%;">Nombre del Conjunto</th>
                                            <th class="text-center" style="width: 10%;">Serie</th>
                                            <th class="text-center" style="width: 15%;">Tipo Danza</th>
                                            <th class="text-center" style="width: 10%;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($conjuntos as $idx => $c): ?>
                                            <tr>
                                                <td class="text-center"><?= $idx + 1 ?></td>
                                                <td style="white-space: normal; word-break: break-word;">
                                                    <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                                </td>
                                                <td class="text-center">SERIE <?= $c['numero_serie'] ?? 'N/A' ?></td>
                                                <td class="text-center"><?= htmlspecialchars($c['nombre_tipo']) ?></td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center gap-1">
                                                        <a href="index.php?page=admin_editar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                            class="btn btn-sm btn-warning"
                                                            title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="index.php?page=admin_eliminar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                            class="btn btn-sm btn-danger"
                                                            title="Eliminar"
                                                            onclick="return confirm('¿Eliminar? Solo si no ha sido evaluado.');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-emoji-frown display-4 text-muted"></i>
                                    <p class="lead text-muted mt-3">No hay conjuntos registrados.</p>
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
    </script>
</body>

</html>
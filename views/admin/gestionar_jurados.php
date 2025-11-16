<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Jurados - FRFCP Admin</title>
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

        .card-header h5 i,
        .card-header h6 i {
            color: #c9184a;
        }

        .table thead th {
            background-color: #fdf2f2;
            color: #495057;
            font-weight: 600;
        }

        .badge-status {
            font-size: 0.85em;
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        /* Estilos para el modal de enlace */
        #modalEnlace {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            padding: 1rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-copy {
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
        }

        /* Botón fijo ajustado para sidebar */
        #boton-fijo {
            position: fixed;
            bottom: 0;
            left: 250px;
            right: 0;
            background: white;
            padding: 1rem;
            box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .main-content-full #boton-fijo {
            left: 0;
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

            #boton-fijo {
                left: 0;
            }

            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            #modalEnlace {
                width: 90%;
                right: 5%;
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
                    <a href="index.php?page=admin_gestion_jurados" class="nav-link text-white d-flex align-items-center active">
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
                    <i class="bi bi-person-badge me-2"></i> Gestionar Jurados
                </h2>
                <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>
        </div>

        <div class="container-fluid px-0">

            <!-- Selección de concurso -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white">
                            <h5><i class="bi bi-filter"></i> Filtrar por Concurso</h5>
                        </div>
                        <div class="card-body">
                            <form method="GET">
                                <input type="hidden" name="page" value="admin_gestion_jurados">
                                <select name="id_concurso" class="form-select form-select-lg"
                                    onchange="this.form.submit()">
                                    <option value="">-- Todos los jurados --</option>
                                    <?php foreach ($concursos as $c): ?>
                                        <option value="<?= $c['id_concurso'] ?>"
                                            <?= ($id_concurso == $c['id_concurso']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if ($mostrarCredenciales): ?>
                <div class="alert alert-success rounded-4 mb-4 border-0 shadow-sm">
                    <h5 class="mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i> ¡Jurado creado con éxito!</h5>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <strong>Usuario:</strong>
                            <div class="bg-light px-2 py-1 rounded">
                                <code><?= htmlspecialchars($credenciales['usuario']) ?></code>
                            </div>
                        </div>
                        <div class="col-6">
                            <strong>Contraseña:</strong>
                            <div class="bg-light px-2 py-1 rounded">
                                <code><?= htmlspecialchars($credenciales['contrasena']) ?></code>
                            </div>
                        </div>
                    </div>
                    <p class="fw-semibold">Enlace de acceso para el jurado:</p>
                    <?php
                    $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
                    $link = rtrim($link, '/') . "/index.php?page=jurado_login&token=" . urlencode($credenciales['token']);
                    ?>
                    <div class="input-group">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($link) ?>" id="linkToken"
                            readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="copiarNuevoEnlace()">
                            <i class="bi bi-copy"></i>
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">
                        Entrega este enlace junto con el usuario y contraseña. El acceso expira al finalizar el concurso.
                    </small>
                </div>
            <?php endif; ?>

            <!-- Errores -->
            <?php if ($error === 'dni'): ?>
                <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm">
                    ❌ DNI inválido. Debe tener 8 dígitos.
                </div>
            <?php elseif ($error === 'datos'): ?>
                <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm">
                    ❌ Completa todos los campos correctamente.
                </div>
            <?php elseif ($error === 'db'): ?>
                <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm">
                    ❌ Error al guardar el jurado. Inténtalo de nuevo.
                </div>
            <?php endif; ?>

            <!-- Listado de jurados -->
            <div class="row">
                <div class="col-12">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">
                                <i class="bi bi-list-ul"></i>
                                <?= $id_concurso ? 'Jurados asignados al concurso' : 'Todos los Jurados' ?>
                            </h5>
                            <span class="badge bg-secondary"><?= count($jurados) ?> encontrados</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (count($jurados) > 0): ?>
                                <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                                    <table class="table table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>DNI</th>
                                                <th>Nombre</th>
                                                <th>Usuario</th>
                                                <th>Años Exp.</th>
                                                <?php if ($id_concurso): ?>
                                                    <th>Criterio a Calificar</th>
                                                    <th>Token</th>
                                                    <th>Estado</th>
                                                    <th>Acciones</th>
                                                <?php endif; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($jurados as $j): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($j['dni']) ?></td>
                                                    <td><?= htmlspecialchars($j['nombre'] ?? '—') ?></td>
                                                    <td><?= htmlspecialchars($j['usuario']) ?></td>
                                                    <td><?= (int)($j['años_experiencia'] ?? 0) ?></td>

                                                    <?php if ($id_concurso): ?>
                                                        <td>
                                                            <?php if (!empty($j['criterio_calificado'])): ?>
                                                                <?= htmlspecialchars($j['criterio_calificado']) ?><br>
                                                                <small class="text-muted">
                                                                    Máx: <?= number_format($j['puntaje_maximo'] ?? 0, 2) ?> pts
                                                                </small>
                                                            <?php else: ?>
                                                                <em class="text-muted">No asignado</em>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($j['token'])): ?>
                                                                <code
                                                                    style="font-size: 0.9em;"><?= substr($j['token'], 0, 16) ?>...</code>
                                                            <?php else: ?>
                                                                <small class="text-muted">Sin token</small>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($j['token']) && !empty($j['fecha_expiracion'])): ?>
                                                                <?php
                                                                $expirado = new DateTime($j['fecha_expiracion']) < new DateTime();
                                                                ?>
                                                                <span
                                                                    class="badge <?= $expirado ? 'bg-secondary' : 'bg-warning text-dark' ?>">
                                                                    <?= $expirado ? 'Expirado' : 'Activo' ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No asignado</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($j['token'])): ?>
                                                                <!-- Botón Enlace -->
                                                                <button class="btn btn-sm btn-outline-primary me-2"
                                                                    onclick="mostrarEnlace('<?= addslashes(htmlspecialchars($j['token'])) ?>')">
                                                                    <i class="bi bi-link-45deg"></i> Enlace
                                                                </button>

                                                                <!-- Botón QR -->
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-primary qr-btn me-2"
                                                                    data-token="<?= htmlspecialchars($j['token']) ?>"
                                                                    data-nombre="<?= htmlspecialchars($j['nombre'] ?? 'Jurado') ?>">
                                                                    <i class="bi bi-qr-code"></i> QR
                                                                </button>

                                                                <!-- ✅ Botón Eliminar -->
                                                                <a href="index.php?page=admin_eliminar_jurado&id=<?= $j['id_jurado'] ?>&id_concurso=<?= $id_concurso ?>"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('¿Eliminar este jurado?\n\n⚠️ Se borrarán todas sus calificaciones, token y usuario.')">
                                                                    <i class="bi bi-trash"></i> Eliminar
                                                                </a>
                                                            <?php endif; ?>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-people display-4 text-muted"></i>
                                    <p class="lead text-muted mt-3">
                                        <?= $id_concurso ? 'No hay jurados asignados a este concurso.' : 'No hay jurados registrados.' ?>
                                    </p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Botón fijo -->
    <div id="boton-fijo" class="bg-white shadow-sm">
        <div class="container-fluid px-4">
            <div class="d-flex justify-content-end align-items-center">
                <?php if ($id_concurso): ?>
                    <a href="index.php?page=admin_crear_jurado&id_concurso=<?= $id_concurso ?>"
                        class="btn btn-success btn-lg px-4">
                        <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                    </a>
                <?php else: ?>
                    <div class="text-end">
                        <button class="btn btn-success btn-lg px-4" disabled
                            title="Primero selecciona un concurso">
                            <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                        </button>
                        <br>
                        <small class="text-muted mt-2 d-block">Para crear un jurado, primero selecciona un concurso.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Modal para enlaces existentes -->
    <div id="modalEnlace" class="alert alert-info">
        <strong>Enlace de acceso:</strong><br>
        <input type="text" id="enlaceInput" readonly class="form-control form-control-sm mb-2">
        <div class="d-grid gap-2 d-md-flex">
            <button class="btn btn-sm btn-secondary" onclick="copiarExistente()">Copiar</button>
            <button class="btn btn-sm btn-outline-danger" onclick="cerrarModal()">Cerrar</button>
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

        // Script original de la página de jurados
        function copiarNuevoEnlace() {
            const input = document.getElementById('linkToken');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado al portapapeles');
        }

        function mostrarEnlace(token) {
            const basePath = `<?= dirname($_SERVER['SCRIPT_NAME']) ?>`;
            const link = `${basePath}/index.php?page=jurado_login&token=${encodeURIComponent(token)}`;
            document.getElementById('enlaceInput').value = link;
            document.getElementById('modalEnlace').style.display = 'block';
        }

        function copiarExistente() {
            const input = document.getElementById('enlaceInput');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado');
        }

        function cerrarModal() {
            document.getElementById('modalEnlace').style.display = 'none';
        }

        // Script para botones QR
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.qr-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const token = this.getAttribute('data-token');
                    const nombre = this.getAttribute('data-nombre');
                    const url = `generar_qr.php?token=${encodeURIComponent(token)}`;
                    const popup = window.open('', '_blank', 'width=400,height=450');
                    popup.document.write(`
                        <!DOCTYPE html>
                        <html lang="es">
                        <head>
                            <meta charset="UTF-8">
                            <title>QR - ${nombre}</title>
                            <style>
                                body { font-family: Arial, sans-serif; text-align: center; padding: 20px; background: #f8f9fa; }
                                h4 { color: #333; }
                                img { max-width: 100%; height: auto; margin: 20px 0; }
                                .footer { font-size: 0.8em; color: #666; margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; }
                            </style>
                        </head>
                        <body>
                            <h4>QR para: ${nombre}</h4>
                            <img src="${url}" alt="Código QR" />
                            <div class="footer">Escanea este código para acceder como jurado.</div>
                        </body>
                        </html>
                    `);
                    popup.document.close();
                });
            });
        });
    </script>
</body>

</html>
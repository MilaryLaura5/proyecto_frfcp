<!-- views/partials/sidebar.php -->
<div class="col-md-3 col-lg-2 bg-dark text-white sidebar" id="sidebar">
    <div class="p-3 d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-shield-lock"></i> FRFCP Admin</h5>
        <button class="btn btn-sm btn-outline-light rounded-circle d-none d-md-block" id="toggleSidebarBtn" title="Ocultar menú">
            <i class="bi bi-chevron-left"></i>
        </button>
    </div>

    <div class="p-3 pt-0">
        <p class="text-muted mb-1">Sesión activa</p>
        <p class="fw-bold text-warning"><?= htmlspecialchars($user['nombre'] ?? $user['usuario']) ?></p>
        <hr class="bg-light">
        <ul class="nav nav-pills flex-column">
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_dashboard">
                    <i class="bi bi-house"></i> Inicio
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_gestion_concursos">
                    <i class="bi bi-trophy"></i> Gestionar Concursos
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_gestion_series">
                    <i class="bi bi-list-ul"></i> Tipos y Series
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_gestionar_conjuntos_globales">
                    <i class="bi bi-collection"></i> Conjuntos Globales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_seleccionar_concurso">
                    <i class="bi bi-people"></i> Asignar a Concurso
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_gestion_jurados">
                    <i class="bi bi-person-badge"></i> Gestionar Jurados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_gestionar_criterios">
                    <i class="bi bi-list-task"></i> Criterios Globales
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white" href="index.php?page=admin_resultados">
                    <i class="bi bi-graph-up"></i> Resultados en Vivo
                </a>
            </li>
            <li class="nav-item mt-3">
                <a class="nav-link text-danger" href="index.php?page=logout">
                    <i class="bi bi-box-arrow-right"></i> Cerrar sesión
                </a>
            </li>
        </ul>
    </div>
</div>
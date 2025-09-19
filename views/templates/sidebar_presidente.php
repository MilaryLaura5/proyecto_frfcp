<!-- views/templates/sidebar_presidente.php -->
<nav class="col-md-3 col-lg-2 d-flex flex-column p-0 bg-dark text-white" style="min-height: 100vh;">
    <!-- Encabezado -->
    <div class="p-3 text-center border-bottom border-secondary">
        <h5 class="mb-1"><i class="bi bi-crown-fill"></i> Presidente</h5>
        <small class="text-muted">Sesión activa</small>
        <p class="mb-0 mt-1 fw-bold"><?= htmlspecialchars($user['nombre']) ?></p>
    </div>

    <!-- Menú -->
    <ul class="nav flex-column flex-grow-1 px-2 py-4">
        <li class="nav-item mb-1">
            <a class="nav-link text-white <?= $page === 'presidente_seleccionar_concurso' ? 'active bg-primary rounded' : '' ?>" 
               href="index.php?page=presidente_seleccionar_concurso">
                <i class="bi bi-list-check me-2"></i> Seleccionar Concurso
            </a>
        </li>
        <?php if (isset($_GET['id_concurso'])): ?>
        <li class="nav-item mb-1">
            <a class="nav-link text-white <?= $page === 'presidente_revisar_resultados' ? 'active bg-primary rounded' : '' ?>" 
               href="index.php?page=presidente_revisar_resultados&id_concurso=<?= $_GET['id_concurso'] ?>">
                <i class="bi bi-graph-up me-2"></i> Revisar Resultados
            </a>
        </li>
        <li class="nav-item mb-1">
            <a class="nav-link text-white <?= $page === 'presidente_generar_reporte' ? 'active bg-primary rounded' : '' ?>" 
               href="index.php?page=presidente_generar_reporte&id_concurso=<?= $_GET['id_concurso'] ?>">
                <i class="bi bi-file-earmark-pdf me-2"></i> Generar Reporte
            </a>
        </li>
        <?php endif; ?>
    </ul>

    <!-- Cerrar sesión -->
    <div class="p-3 border-top border-secondary">
        <a href="index.php?page=logout" class="nav-link text-danger d-flex align-items-center">
            <i class="bi bi-box-arrow-right me-2"></i> Cerrar sesión
        </a>
    </div>
</nav>
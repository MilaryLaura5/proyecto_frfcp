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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .sidebar { min-height: 100vh; background-color: #2c3e50; color: white; }
        .sidebar a { color: #ecf0f1; }
        .sidebar a:hover { background-color: #34495e; }
        .main-content { padding: 20px; }
        .card-dashboard { transition: transform 0.2s; }
        .card-dashboard:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Barra lateral -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="p-3 text-center">
                    <h5><i class="bi bi-shield-lock"></i> FRFCP Admin</h5>
                    <small>¡Bienvenido, <?php echo htmlspecialchars($user['nombre']); ?>!</small>
                </div>
                <hr>
                <ul class="nav flex-column">
                    <li class="nav-item"><a class="nav-link" href="index.php?page=admin_dashboard"><i class="bi bi-house"></i> Inicio</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-trophy"></i> Crear Concurso</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-list-ul"></i> Gestionar Series</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-people"></i> Gestionar Conjuntos</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-person-badge"></i> Gestionar Jurados</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-key"></i> Generar Tokens</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-play-circle"></i> Activar Concurso</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-graph-up"></i> Resultados en Vivo</a></li>
                    <li class="nav-item"><a class="nav-link" href="#"><i class="bi bi-file-earmark-pdf"></i> Exportar Reporte</a></li>
                    <li class="nav-item mt-3"><a class="nav-link text-danger" href="index.php?page=logout"><i class="bi bi-box-arrow-right"></i> Cerrar sesión</a></li>
                </ul>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>🎯 Centro de Control del Administrador</h2>
                    <span class="badge bg-primary">Rol: Administrador</span>
                </div>

                <div class="row">
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-dashboard shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-trophy text-warning" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Crear Concurso</h5>
                                <p class="text-muted">Define nuevo evento cultural</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-dashboard shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Gestionar Conjuntos</h5>
                                <p class="text-muted">Agrega, edita o importa desde CSV</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-dashboard shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-key text-success" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Generar Tokens</h5>
                                <p class="text-muted">Acceso seguro para jurados</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card card-dashboard shadow">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up text-danger" style="font-size: 2rem;"></i>
                                <h5 class="mt-2">Resultados en Vivo</h5>
                                <p class="text-muted">Monitorea evaluaciones en tiempo real</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-4">
                    <strong>💡 Consejo:</strong> Puedes activar el concurso cuando todos los jurados tengan su token.
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
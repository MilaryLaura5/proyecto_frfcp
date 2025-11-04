<?php
// views/presidente/revisar_resultados.php
// Vista del Presidente - Resultados del Concurso

global $pdo;
$id_concurso = $_GET['id_concurso'] ?? 0;

// Obtener información del concurso actual
$stmt = $pdo->prepare("SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso_actual = $stmt->fetch();

// Verificar si hay calificaciones
$tiene_calificaciones = false;
if (!empty($resultados)) {
    foreach ($resultados as $resultado) {
        if ($resultado['promedio_final'] > 0) {
            $tiene_calificaciones = true;
            break;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados - Presidente</title>
    <!-- Bootstrap CSS (corregido: sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f8f0f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: white;
            border-radius: 12px;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            margin: 0;
            font-weight: 700;
            color: #c9184a;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .badge-status {
            font-size: 0.9rem;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .info-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            height: 100%;
        }

        .stat-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: transform 0.2s;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .table-success {
            background-color: #d1f7d1 !important;
        }

        .medal {
            font-size: 16px;
        }

        .estado-badge {
            font-size: 12px;
        }

        .sidebar {
            min-height: 100vh;
            background: linear-gradient(to bottom, #800f2f, #c9184a);
            color: white;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: white;
            transition: background-color 0.2s;
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        .info-box {
            background: #fdf2f2;
            border-radius: 8px;
            padding: 12px;
            border-left: 4px solid #c9184a;
            margin-bottom: 10px;
        }

        .pdf-card {
            background: linear-gradient(135deg, #198754, #157347);
            color: white;
            border-radius: 12px;
            height: 100%;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
        }

        .table-container table {
            margin-bottom: 0;
        }

        .table-container thead th {
            position: sticky;
            top: 0;
            background-color: #f8f9fa;
            z-index: 10;
        }

        .compact-info {
            font-size: 0.9rem;
        }

        .compact-info h5 {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .compact-info p {
            margin-bottom: 5px;
        }

        .pdf-card {
            border: 1px solid #198754;
            transition: all 0.3s ease;
        }

        .pdf-card:hover {
            box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
            transform: translateY(-2px);
        }

        .btn-outline-success {
            border-width: 1.5px;
            padding: 8px 16px;
        }

<<<<<<< HEAD


        /* === PANEL ROJO === */
        .card.border-danger {
            background-color: #dc3545;
            /* Rojo Bootstrap */
            color: white;
        }

        /* === BOTONES DE DESCARGA IGUALES === */
        .btn-descargar {
            width: 150px;
            height: 45px;
            font-size: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* PDF en rojo */
        .btn-pdf {
            background-color: #dc3545;
            color: white;
            border: none;
        }

        .btn-pdf:hover {
            background-color: #b02a37;
        }

        /* Excel en verde */
        .btn-excel {
            background-color: #198754;
            color: white;
            border: none;
        }

        .btn-excel:hover {
            background-color: #146c43;
        }
    </style>

=======
        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 10px;
            z-index: 1000;
        }

        #toggleSidebarBtn i {
            transition: transform 0.3s ease;
        }

        #toggleSidebarBtn:hover i {
            transform: translateX(-2px);
        }
>>>>>>> 5866320ea19d7a7d545a7fe056b1d2092b3d504c
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR -->
            <div class="col-md-3 bg-dark text-white sidebar" id="sidebar">
                <div class="p-3 d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Presidente</h4>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="toggleSidebarBtn" title="Ocultar menú">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                <div class="p-3 pt-0">
                    <p class="text-muted mb-1">Sesión activa</p>
                    <p class="fw-bold" style="color: #ffccd5;"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></p>
                    <hr class="opacity-50">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=presidente_seleccionar_concurso" class="nav-link text-white">
                                ← Volver a Concursos
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="index.php?page=logout" class="nav-link text-danger">
                                🚪 Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-md-9" id="mainContent">
                <div class="p-4">
                    <button class="btn btn-outline-dark mb-3 d-none" id="showSidebarBtn" title="Mostrar menú">
                        <i class="bi bi-chevron-right"></i>
                    </button>

                    <!-- ENCABEZADO -->
                    <div class="page-header">
                        <h2>🏆 Resultados del Concurso</h2>
                        <div class="d-flex align-items-center gap-3">
                            <?php if ($tiene_calificaciones): ?>
                                <span class="badge bg-success badge-status">✅ Con calificaciones</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark badge-status">⏳ Esperando calificaciones</span>
                            <?php endif; ?>

                            <a href="index.php?page=presidente_seleccionar_concurso" class="btn btn-outline-secondary">
                                ← Volver
                            </a>
                        </div>
                    </div>

                    <!-- LAYOUT COMPACTO: 3 COLUMNAS -->
                    <div class="row mb-4">
                        <!-- COLUMNA 1: INFORMACIÓN DEL CONCURSO -->
                        <div class="col-md-4 mb-3">
                            <div class="card info-card border-0 h-100">
                                <div class="card-header d-flex justify-content-between align-items-center py-2" style="background: linear-gradient(to right, #c9184a, #800f2f); color: white;">
                                    <h6 class="mb-0">📋 Información del Concurso</h6>
                                    <span class="badge estado-badge 
                    <?php
                    if ($concurso_actual['estado'] == 'Activo') echo 'bg-success';
                    elseif ($concurso_actual['estado'] == 'Cerrado') echo 'bg-danger';
                    else echo 'bg-warning text-dark';
                    ?>">
                                        <?php echo $concurso_actual['estado']; ?>
                                    </span>
                                </div>
                                <div class="card-body compact-info">
                                    <div class="info-box">
                                        <p class="mb-1 fw-semibold text-secondary">ID Concurso</p>
                                        <h5 class="mb-0 text-dark"><?php echo $concurso_actual['id_concurso']; ?></h5>
                                    </div>
                                    <div class="info-box">
                                        <p class="mb-1 fw-semibold text-secondary">Nombre</p>
                                        <h5 class="mb-0 text-dark"><?php echo htmlspecialchars($concurso_actual['nombre']); ?></h5>
                                    </div>
                                    <div class="info-box">
                                        <p class="mb-1 fw-semibold text-secondary">Fecha Inicio</p>
                                        <h5 class="mb-0 text-dark"><?php echo date('d/m/Y H:i', strtotime($concurso_actual['fecha_inicio'])); ?></h5>
                                    </div>
                                    <div class="info-box">
                                        <p class="mb-1 fw-semibold text-secondary">Fecha Fin</p>
                                        <h5 class="mb-0 text-dark"><?php echo date('d/m/Y H:i', strtotime($concurso_actual['fecha_fin'])); ?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- COLUMNA 2: ESTADÍSTICAS -->
                        <div class="col-md-4 mb-3">
                            <div class="card info-card border-0 h-100">
                                <div class="card-header bg-info text-white text-center py-2">
                                    <h6 class="mb-0">📊 Estadísticas</h6>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($resultados)): ?>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="card text-center bg-light stat-card">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title">📊 Total</h6>
                                                        <p class="card-text h4 text-primary mb-1"><?php echo $estadisticas['total_conjuntos']; ?></p>
                                                        <small class="text-muted">Conjuntos</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="card text-center bg-light stat-card">
                                                    <div class="card-body py-3">
                                                        <h6 class="card-title">✅ Calificados</h6>
                                                        <p class="card-text h4 text-success mb-1"><?php echo $estadisticas['calificados']; ?></p>
                                                        <small class="text-muted">Con calificación</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center text-muted py-4">
                                            <p>No hay datos disponibles</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!--UNIDO DE PDF Y EXCELL -->
                        <!-- Exportar Resultados (PDF y Excel unidos, recuadro completo) -->
                        <div class="col-md-4 mb-3">
<<<<<<< HEAD
                            <div class="card shadow-sm h-100 border-0 rounded-4">
                                <div class="card-header text-white text-center py-3"
                                    style="background: linear-gradient(90deg, #dc3545, #198754);">
                                    <h6 class="mb-0 fw-semibold">
                                        <i class="bi bi-file-earmark-bar-graph"></i> Exportar Resultados
                                    </h6>
=======
                            <?php if (!empty($resultados)): ?>
                                <div class="card info-card border-0 h-100">
                                    <div class="card-header bg-success text-white text-center py-2">
                                        <h6 class="mb-0">📄 Exportar Resultados</h6>
                                    </div>
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div class="mb-3">
                                            <i class="bi bi-file-earmark-pdf text-danger display-6"></i>
                                        </div>
                                        <p class="text-muted small mb-3">
                                            Documento formal PDF listo para entrega oficial
                                        </p>
                                        <a href="generar_pdf_real.php?id_concurso=<?php echo $id_concurso; ?>"
                                            class="btn btn-outline-success btn-sm fw-semibold">
                                            <i class="bi bi-download me-2"></i>Descargar PDF
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="card info-card border-0 h-100">
                                    <div class="card-header bg-secondary text-white text-center py-2">
                                        <h6 class="mb-0">📄 Exportar Resultados</h6>
                                    </div>
                                    <div class="card-body text-center d-flex flex-column justify-content-center">
                                        <div class="mb-3">
                                            <i class="bi bi-file-earmark-pdf text-muted display-6"></i>
                                        </div>
                                        <p class="text-muted small mb-0">
                                            Disponible cuando haya resultados
                                        </p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- FILTROS -->
                    <?php if (!empty($resultados)): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">🔍 Filtros de Búsqueda</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <input type="text" class="form-control" id="buscarConjunto"
                                            placeholder="🔍 Buscar por nombre de conjunto...">
                                    </div>
                                    <div class="col-md-6">
                                        <select class="form-select" id="filtroCategoria">
                                            <option value="">Todas las categorías</option>
                                            <?php
                                            $categorias = array_unique(array_column($resultados, 'categoria'));
                                            foreach ($categorias as $categoria) {
                                                echo "<option value='" . htmlspecialchars($categoria) . "'>" . htmlspecialchars($categoria) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- TABLA DE RESULTADOS CON SCROLL -->
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center" style="background: linear-gradient(to right, #c9184a, #800f2f); color: white;">
                            <h5 class="mb-0">📊 Resultados Finales</h5>
                            <span class="badge bg-light text-dark"><?php echo count($resultados); ?> conjuntos</span>
                        </div>
                        <div class="card-body p-0">
                            <?php if (!empty($resultados)): ?>
                                <div class="table-container">
                                    <table class="table table-striped table-hover mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Posición</th>
                                                <th>N° Orden</th>
                                                <th>Conjunto Folklórico</th>
                                                <th>Categoría</th>
                                                <th>Puntaje Final</th>
                                                <th>Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resultados as $resultado): ?>
                                                <tr class="<?php echo $resultado['posicion'] <= 3 && $resultado['promedio_final'] > 0 ? 'table-success' : ''; ?>">
                                                    <td>
                                                        <strong><?php echo $resultado['posicion']; ?>°</strong>
                                                        <?php if ($resultado['posicion'] == 1 && $resultado['promedio_final'] > 0): ?>
                                                            🥇
                                                        <?php elseif ($resultado['posicion'] == 2 && $resultado['promedio_final'] > 0): ?>
                                                            🥈
                                                        <?php elseif ($resultado['posicion'] == 3 && $resultado['promedio_final'] > 0): ?>
                                                            🥉
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="fw-bold"><?php echo $resultado['orden_presentacion']; ?></td>
                                                    <td class="conjunto-nombre"><?php echo htmlspecialchars($resultado['conjunto']); ?></td>
                                                    <td class="categoria-nombre"><?php echo htmlspecialchars($resultado['categoria']); ?></td>
                                                    <td>
                                                        <strong class="<?php echo $resultado['promedio_final'] > 0 ? 'text-primary' : 'text-muted'; ?>">
                                                            <?php echo $resultado['promedio_final'] > 0 ? $resultado['promedio_final'] : 'Sin calificar'; ?>
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <span class="badge <?php echo $resultado['calificaciones_count'] > 0 ? 'bg-success' : 'bg-warning'; ?>">
                                                            <?php echo $resultado['calificaciones_count'] > 0 ? 'Calificado' : 'Pendiente'; ?>
                                                        </span>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
>>>>>>> 5866320ea19d7a7d545a7fe056b1d2092b3d504c
                                </div>

                                <div class="card-body d-flex align-items-center justify-content-center p-4" style="min-height: 240px;">
                                    <?php if (!empty($resultados)): ?>
                                        <div class="row w-100 text-center">
                                            <!-- PDF -->
                                            <div class="col-6 border-end">
                                                <i class="bi bi-file-earmark-pdf display-5 text-danger"></i>
                                                <h6 class="fw-bold text-dark mt-2">PDF</h6>
                                                <p class="text-muted small mb-2">Documento formal para impresión</p>
                                                <a href="generar_pdf_real.php?id_concurso=<?= $id_concurso ?>"
                                                    class="btn btn-outline-danger btn-sm fw-semibold w-75" target="_blank">
                                                    <i class="bi bi-download me-1"></i> Descargar
                                                </a>
                                            </div>

                                            <!-- Excel -->
                                            <div class="col-6">
                                                <i class="bi bi-file-earmark-excel display-5 text-success"></i>
                                                <h6 class="fw-bold text-dark mt-2">Excel</h6>
                                                <p class="text-muted small mb-2">Editable para análisis detallado</p>
                                                <a href="generar_excel.php?id_concurso=<?= $id_concurso ?>"
                                                    class="btn btn-outline-success btn-sm fw-semibold w-75">
                                                    <i class="bi bi-download me-1"></i> Descargar
                                                </a>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center w-100">
                                            <i class="bi bi-exclamation-circle display-6 text-muted"></i>
                                            <p class="text-muted mt-3 mb-0">Disponible cuando haya resultados</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- FILTROS -->
                        <?php if (!empty($resultados)): ?>
                            <div class="card mb-3">
                                <div class="card-header bg-secondary text-white">
                                    <h6 class="mb-0">🔍 Filtros de Búsqueda</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="text" class="form-control" id="buscarConjunto"
                                                placeholder="🔍 Buscar por nombre de conjunto...">
                                        </div>
                                        <div class="col-md-6">
                                            <select class="form-select" id="filtroCategoria">
                                                <option value="">Todas las categorías</option>
                                                <?php
                                                $categorias = array_unique(array_column($resultados, 'categoria'));
                                                foreach ($categorias as $categoria) {
                                                    echo "<option value='" . htmlspecialchars($categoria) . "'>" . htmlspecialchars($categoria) . "</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- TABLA DE RESULTADOS CON SCROLL -->
                        <div class="card mb-4">
                            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">📊 Resultados Finales</h5>
                                <span class="badge bg-light text-dark"><?php echo count($resultados); ?> conjuntos</span>
                            </div>
                            <div class="card-body p-0">
                                <?php if (!empty($resultados)): ?>
                                    <div class="table-container">
                                        <table class="table table-striped table-hover mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Posición</th>
                                                    <th>N° Orden</th>
                                                    <th>Conjunto Folklórico</th>
                                                    <th>Categoría</th>
                                                    <th>Puntaje Final</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($resultados as $resultado): ?>
                                                    <tr class="<?php echo $resultado['posicion'] <= 3 && $resultado['promedio_final'] > 0 ? 'table-success' : ''; ?>">
                                                        <td>
                                                            <strong><?php echo $resultado['posicion']; ?>°</strong>
                                                            <?php if ($resultado['posicion'] == 1 && $resultado['promedio_final'] > 0): ?>
                                                                🥇
                                                            <?php elseif ($resultado['posicion'] == 2 && $resultado['promedio_final'] > 0): ?>
                                                                🥈
                                                            <?php elseif ($resultado['posicion'] == 3 && $resultado['promedio_final'] > 0): ?>
                                                                🥉
                                                            <?php endif; ?>
                                                        </td>
                                                        <td class="fw-bold"><?php echo $resultado['orden_presentacion']; ?></td>
                                                        <td class="conjunto-nombre"><?php echo htmlspecialchars($resultado['conjunto']); ?></td>
                                                        <td class="categoria-nombre"><?php echo htmlspecialchars($resultado['categoria']); ?></td>
                                                        <td>
                                                            <strong class="<?php echo $resultado['promedio_final'] > 0 ? 'text-primary' : 'text-muted'; ?>">
                                                                <?php echo $resultado['promedio_final'] > 0 ? $resultado['promedio_final'] : 'Sin calificar'; ?>
                                                            </strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?php echo $resultado['calificaciones_count'] > 0 ? 'bg-success' : 'bg-warning'; ?>">
                                                                <?php echo $resultado['calificaciones_count'] > 0 ? 'Calificado' : 'Pendiente'; ?>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- RESUMEN -->
                                    <div class="p-3 bg-light border-top">
                                        <h6>📈 Resumen Detallado:</h6>
                                        <div class="row">
                                            <div class="col-md-3"><strong>Total conjuntos:</strong> <?php echo $estadisticas['total_conjuntos']; ?></div>
                                            <div class="col-md-3"><strong>Con calificaciones:</strong> <?php echo $estadisticas['calificados']; ?></div>
                                            <div class="col-md-3"><strong>Pendientes:</strong> <?php echo $estadisticas['pendientes']; ?></div>
                                            <div class="col-md-3"><strong>Porcentaje:</strong> <?php echo round(($estadisticas['calificados'] / $estadisticas['total_conjuntos']) * 100, 1); ?>%</div>
                                        </div>
                                    </div>

                                <?php else: ?>
                                    <div class="alert alert-warning text-center m-3">
                                        <h5>📭 No hay conjuntos registrados</h5>
                                        <p>Este concurso no tiene resultados disponibles aún.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- CRITERIOS -->
                        <?php if (!empty($criterios)): ?>
                            <div class="card mb-4">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">📝 Criterios de Evaluación</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <?php foreach ($criterios as $criterio): ?>
                                            <div class="col-md-6 mb-2">
                                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                    <span class="fw-semibold"><?php echo htmlspecialchars($criterio['nombre']); ?></span>
                                                    <span class="badge bg-primary">Max: <?php echo $criterio['puntaje_maximo']; ?></span>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

<<<<<<< HEAD
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

        <script>
            // Filtros dinámicos
            document.addEventListener('DOMContentLoaded', function() {
                const buscar = document.getElementById('buscarConjunto');
                const filtro = document.getElementById('filtroCategoria');
                if (!buscar || !filtro) return;
=======
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscar = document.getElementById('buscarConjunto');
            const filtro = document.getElementById('filtroCategoria');
            if (!buscar || !filtro) return;
>>>>>>> 5866320ea19d7a7d545a7fe056b1d2092b3d504c

                function filtrar() {
                    const texto = buscar.value.toLowerCase();
                    const categoria = filtro.value;
                    const filas = document.querySelectorAll('tbody tr');
                    let visibles = 0;

                    filas.forEach(fila => {
                        const nombre = fila.querySelector('.conjunto-nombre').textContent.toLowerCase();
                        const cat = fila.querySelector('.categoria-nombre').textContent;
                        const coincide = (!categoria || cat === categoria) && nombre.includes(texto);
                        fila.style.display = coincide ? '' : 'none';
                        if (coincide) visibles++;
                    });

                    const badge = document.querySelector('.card-header .badge');
                    if (badge) badge.textContent = visibles + ' conjuntos';
                }

<<<<<<< HEAD
                buscar.addEventListener('input', filtrar);
                filtro.addEventListener('change', filtrar);
            });
        </script>
=======
            buscar.addEventListener('input', filtrar);
            filtro.addEventListener('change', filtrar);
        });

        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const showBtn = document.getElementById('showSidebarBtn');

        toggleBtn.addEventListener('click', () => {
            sidebar.style.display = 'none';
            mainContent.classList.remove('col-md-9');
            mainContent.classList.add('col-md-12');
            showBtn.classList.remove('d-none');
        });

        showBtn.addEventListener('click', () => {
            sidebar.style.display = 'block';
            mainContent.classList.remove('col-md-12');
            mainContent.classList.add('col-md-9');
            showBtn.classList.add('d-none');
        });
    </script>
>>>>>>> 5866320ea19d7a7d545a7fe056b1d2092b3d504c
</body>

</html>
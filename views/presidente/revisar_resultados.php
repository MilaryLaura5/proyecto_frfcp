<?php
// views/presidente/revisar_resultados.php
// Vista del Presidente - Resultados del Concurso

global $pdo;
$id_concurso = $_GET['id_concurso'] ?? null;
if (!$id_concurso || !is_numeric($id_concurso)) {
    header('Location: index.php?page=presidente_seleccionar_concurso&error=no_concurso');
    exit;
}

// Información del concurso
$stmt = $pdo->prepare("SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso_actual = $stmt->fetch();
if (!$concurso_actual) die("Concurso no encontrado");

// === OBTENER RESULTADOS ===
$sql = "
    SELECT 
        c.nombre AS conjunto,
        pc.orden_presentacion,
        s.nombre_serie AS categoria,
        COALESCE(ROUND(SUM(dc.puntaje), 2), 0) AS puntaje_suma,
        MAX(CASE WHEN ca.estado = 'descalificado' THEN 1 ELSE 0 END) AS es_descalificado,
        COUNT(dc.id_detalle) AS calificaciones_count
    FROM participacionconjunto pc
    JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
    JOIN serie s ON c.id_serie = s.id_serie
    LEFT JOIN calificacion ca ON pc.id_participacion = ca.id_participacion
    LEFT JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
    WHERE pc.id_concurso = ?
    GROUP BY c.id_conjunto, c.nombre, pc.orden_presentacion, s.nombre_serie
    ORDER BY 
        es_descalificado ASC,
        puntaje_suma DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_concurso]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar resultados: asignar estado y puntaje final
foreach ($resultados as &$r) {
    $es_descalificado = (bool)$r['es_descalificado'];
    $tiene_calificaciones = $r['calificaciones_count'] > 0;

    if ($es_descalificado) {
        $r['estado_final'] = 'descalificado';
        $r['puntaje_final'] = 0.00;
    } elseif ($tiene_calificaciones) {
        $r['estado_final'] = 'calificado';
        $r['puntaje_final'] = (float)$r['puntaje_suma'];
    } else {
        $r['estado_final'] = 'pendiente';
        $r['puntaje_final'] = null;
    }
}
unset($r);

// Separar calificados (para posiciones) y otros
$calificados = [];
$otros = [];
foreach ($resultados as $r) {
    if ($r['estado_final'] === 'calificado') {
        $calificados[] = $r;
    } else {
        $otros[] = $r;
    }
}

// Calcular posiciones solo para calificados
$posicion_actual = 1;
$contador = 1;
$ultimo_puntaje = null;
foreach ($calificados as &$r) {
    if ($ultimo_puntaje === null || $r['puntaje_final'] < $ultimo_puntaje) {
        $posicion_actual = $contador;
    }
    $r['posicion'] = $posicion_actual;
    $ultimo_puntaje = $r['puntaje_final'];
    $contador++;
}
unset($r);

// Combinar: primero calificados, luego otros (descalificados + pendientes)
$resultados_finales = array_merge($calificados, $otros);

// Estadísticas
$total = count($resultados_finales);
$calificados_count = count($calificados);
$descalificados_count = count(array_filter($otros, fn($r) => $r['estado_final'] === 'descalificado'));
$pendientes_count = $total - $calificados_count - $descalificados_count;

$estadisticas = [
    'total_conjuntos' => $total,
    'calificados' => $calificados_count,
    'descalificados' => $descalificados_count,
    'pendientes' => $pendientes_count,
    'porcentaje_calificados' => $total > 0 ? round(($calificados_count / $total) * 100, 1) : 0
];

$tiene_calificaciones = $calificados_count > 0;

// Criterios
$stmt_c = $pdo->prepare("SELECT cr.nombre, cc.puntaje_maximo FROM criterioconcurso cc JOIN criterio cr ON cc.id_criterio = cr.id_criterio WHERE cc.id_concurso = ?");
$stmt_c->execute([$id_concurso]);
$criterios = $stmt_c->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados del Concurso - Presidente</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        /* SIDEBAR ROJO */
        .sidebar {
            background: linear-gradient(180deg, #600000, #800000, #a00000);
            color: white;
            min-height: 100vh;
            box-shadow: 3px 0 15px rgba(0, 0, 0, 0.3);
            border-right: 3px solid #400000;
            transition: all 0.3s ease;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            margin-bottom: 8px;
            transition: all 0.3s;
            padding: 12px 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.15);
            color: white;
            transform: translateX(5px);
            border-color: rgba(255, 255, 255, 0.2);
        }

        .sidebar .nav-link.active {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
        }

        /* Botones sidebar - TRANSPARENTES */
        #toggleSidebarBtn {
            background: transparent !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            transition: all 0.3s ease;
        }

        #toggleSidebarBtn:hover {
            background: rgba(255, 255, 255, 0.1) !important;
            transform: translateX(-2px);
        }

        #showSidebarBtn {
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1000;
            background: rgba(139, 0, 0, 0.8) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            border-radius: 50%;
            width: 45px;
            height: 45px;
            display: none;
            align-items: center;
            justify-content: center;
            color: white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        #showSidebarBtn:hover {
            background: rgba(160, 0, 0, 0.9) !important;
            transform: scale(1.1);
        }

        /* Estado cuando el sidebar está oculto */
        .sidebar-hidden #sidebar {
            display: none !important;
        }

        .sidebar-hidden #mainContent {
            width: 100% !important;
            margin-left: 0 !important;
        }

        .sidebar-hidden #showSidebarBtn {
            display: flex !important;
        }

        /* CONTENIDO PRINCIPAL */
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

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #fdf2f2;
        }

        .badge-status {
            font-size: 0.85em;
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        .table-container {
            max-height: 500px;
            overflow-y: auto;
        }

        .info-box {
            background: #fdf2f2;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 10px;
        }

        .stat-card {
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        /* Transición suave para el contenido principal */
        #mainContent {
            transition: all 0.3s ease;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: fixed;
                z-index: 1000;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }

            .sidebar.show {
                transform: translateX(0);
            }

            #showSidebarBtn {
                display: flex;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- SIDEBAR ROJO MEJORADO -->
            <div class="col-md-3 col-lg-2 sidebar p-0" id="sidebar">
                <div class="p-3 d-flex justify-content-between align-items-center border-bottom border-light border-opacity-25">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge fs-4 me-2 text-warning"></i>
                        <div>
                            <h5 class="mb-0 fw-bold">Presidente</h5>
                            <small class="text-light opacity-75">Panel de Control</small>
                        </div>
                    </div>
                    <button class="btn btn-sm btn-outline-light rounded-circle" id="toggleSidebarBtn" title="Ocultar menú">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </div>

                <div class="p-3 pt-3">
                    <div class="bg-light bg-opacity-10 rounded p-2 mb-3">
                        <p class="text-light mb-1 opacity-75 small">Sesión activa</p>
                        <p class="fw-bold text-warning mb-0">
                            <i class="bi bi-person-circle me-1"></i>
                            <?php echo htmlspecialchars($_SESSION['user']['nombre'] ?? 'Usuario'); ?>
                        </p>
                    </div>

                    <hr class="bg-light opacity-25 my-3">

                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=presidente_seleccionar_concurso"
                                class="nav-link text-white d-flex align-items-center">
                                <i class="bi bi-arrow-left-circle me-2"></i>
                                Volver a Concursos
                            </a>
                        </li>
                        <li class="nav-item mt-2">
                            <a href="index.php?page=logout"
                                class="nav-link text-white d-flex align-items-center bg-danger bg-opacity-50">
                                <i class="bi bi-box-arrow-right me-2"></i>
                                Cerrar sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="col-md-9 col-lg-10" id="mainContent">
                <!-- Botón para mostrar sidebar cuando está oculto -->
                <button class="btn d-none" id="showSidebarBtn" title="Mostrar menú">
                    <i class="bi bi-chevron-right"></i>
                </button>

                <div class="p-3">
                    <!-- Encabezado -->
                    <div class="header-container">
                        <div class="d-flex justify-content-between align-items-center">
                            <h2 class="page-title">
                                <i class="bi bi-trophy me-2"></i> Resultados del Concurso
                            </h2>
                            <div class="d-flex align-items-center gap-3">
                                <?php if ($tiene_calificaciones): ?>
                                    <span class="badge bg-success badge-status">✅ Con calificaciones</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark badge-status">⏳ Esperando calificaciones</span>
                                <?php endif; ?>
                                <a href="index.php?page=presidente_seleccionar_concurso" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-arrow-left"></i> Volver
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="container-fluid px-4">
                        <!-- Layout compacto: 3 columnas -->
                        <div class="row mb-4">
                            <!-- Información del concurso -->
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <h6 class="mb-0"><i class="bi bi-info-circle"></i> Información del Concurso</h6>
                                        <span class="badge <?= $concurso_actual['estado'] == 'Activo' ? 'bg-success' : ($concurso_actual['estado'] == 'Cerrado' ? 'bg-danger' : 'bg-warning text-dark') ?> badge-status">
                                            <?= ucfirst($concurso_actual['estado']) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-box">
                                            <p class="mb-1 fw-semibold text-secondary">ID Concurso</p>
                                            <h6 class="mb-0"><?= $concurso_actual['id_concurso'] ?></h6>
                                        </div>
                                        <div class="info-box">
                                            <p class="mb-1 fw-semibold text-secondary">Nombre</p>
                                            <h6 class="mb-0"><?= htmlspecialchars($concurso_actual['nombre'], ENT_QUOTES, 'UTF-8') ?></h6>
                                        </div>
                                        <div class="info-box">
                                            <p class="mb-1 fw-semibold text-secondary">Fecha Inicio</p>
                                            <h6 class="mb-0"><?= date('d/m/Y H:i', strtotime($concurso_actual['fecha_inicio'])) ?></h6>
                                        </div>
                                        <div class="info-box">
                                            <p class="mb-1 fw-semibold text-secondary">Fecha Fin</p>
                                            <h6 class="mb-0"><?= date('d/m/Y H:i', strtotime($concurso_actual['fecha_fin'])) ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas -->
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-info text-white text-center">
                                        <h6 class="mb-0"><i class="bi bi-bar-chart"></i> Estadísticas</h6>
                                    </div>
                                    <div class="card-body">
                                        <?php if (!empty($resultados)): ?>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="card text-center bg-light stat-card">
                                                        <div class="card-body py-3">
                                                            <h6 class="card-title">📊 Total</h6>
                                                            <p class="card-text h4 text-primary mb-1"><?= $estadisticas['total_conjuntos'] ?></p>
                                                            <small class="text-muted">Conjuntos</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="card text-center bg-light stat-card">
                                                        <div class="card-body py-3">
                                                            <h6 class="card-title">✅ Calificados</h6>
                                                            <p class="card-text h4 text-success mb-1"><?= $estadisticas['calificados'] ?></p>
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

                            <!-- Exportar Resultados -->
                            <div class="col-md-4 mb-3">
                                <div class="card shadow-sm h-100 border-0 rounded-4">
                                    <div class="card-header text-white text-center py-3"
                                        style="background: linear-gradient(90deg, #dc3545, #198754);">
                                        <h6 class="mb-0 fw-semibold">
                                            <i class="bi bi-file-earmark-bar-graph"></i> Exportar Resultados
                                        </h6>
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

                            <!-- Filtros -->
                            <?php if (!empty($resultados)): ?>
                                <div class="card shadow-sm mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="bi bi-funnel"></i> Filtros de Búsqueda</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="text" class="form-control" id="buscarConjunto"
                                                    placeholder="Buscar por nombre de conjunto...">
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-select" id="filtroCategoria">
                                                    <option value="">Todas las categorías</option>
                                                    <?php
                                                    $categorias = array_unique(array_column($resultados, 'categoria'));
                                                    foreach ($categorias as $categoria) {
                                                        echo "<option value='" . htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') . "'>" . htmlspecialchars($categoria, ENT_QUOTES, 'UTF-8') . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Tabla de resultados -->
                            <div class="card shadow-sm mb-4">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Resultados Finales</h5>
                                    <span class="badge bg-secondary"><?= count($resultados_finales) ?> encontrados</span>
                                </div>
                                <div class="card-body p-0">
                                    <?php if (!empty($resultados_finales)): ?>
                                        <div class="table-responsive table-container">
                                            <table class="table table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Posición</th>
                                                        <th>N° Orden</th>
                                                        <th>Conjunto Folklórico</th>
                                                        <th>Serie</th>
                                                        <th>Puntaje Final</th>
                                                        <th>Estado</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($resultados_finales as $r): ?>
                                                        <tr class="<?= ($r['estado_final'] === 'calificado' && isset($r['posicion']) && $r['posicion'] <= 3) ? 'table-success' : '' ?>">
                                                            <td>
                                                                <?php if ($r['estado_final'] === 'calificado'): ?>
                                                                    <strong><?= $r['posicion'] ?>°</strong>
                                                                    <?php if ($r['posicion'] == 1): ?>🥇
                                                                    <?php elseif ($r['posicion'] == 2): ?>🥈
                                                                    <?php elseif ($r['posicion'] == 3): ?>🥉
                                                                <?php endif; ?>
                                                            <?php else: ?>
                                                                —
                                                            <?php endif; ?>
                                                            </td>
                                                            <td class="fw-bold"><?= $r['orden_presentacion'] ?></td>
                                                            <td class="conjunto-nombre"><?= htmlspecialchars($r['conjunto'], ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td class="categoria-nombre"><?= htmlspecialchars($r['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                                            <td>
                                                                <?php if ($r['estado_final'] === 'descalificado'): ?>
                                                                    <strong class="text-muted">0.00</strong>
                                                                <?php elseif ($r['estado_final'] === 'calificado'): ?>
                                                                    <strong class="text-primary"><?= number_format($r['puntaje_final'], 2) ?></strong>
                                                                <?php else: ?>
                                                                    <span class="text-muted">Sin calificar</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php if ($r['estado_final'] === 'descalificado'): ?>
                                                                    <span class="badge bg-danger">Descalificado</span>
                                                                <?php elseif ($r['estado_final'] === 'calificado'): ?>
                                                                    <span class="badge bg-success">Calificado</span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-warning text-dark">Pendiente</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </div>

                                        <!-- Resumen -->
                                        <div class="p-3 bg-light border-top">
                                            <h6><i class="bi bi-graph-up"></i> Resumen Detallado:</h6>
                                            <div class="row">
                                                <div class="col-md-3"><strong>Total conjuntos:</strong> <?= $estadisticas['total_conjuntos'] ?></div>
                                                <div class="col-md-3"><strong>Calificados:</strong> <?= $estadisticas['calificados'] ?></div>
                                                <div class="col-md-3"><strong>Descalificados:</strong> <?= $estadisticas['descalificados'] ?></div>
                                                <div class="col-md-3"><strong>Pendientes:</strong> <?= $estadisticas['pendientes'] ?></div>
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <div class="text-center py-5">
                                            <i class="bi bi-inbox display-4 text-muted"></i>
                                            <p class="lead text-muted mt-3">No hay conjuntos participantes en este concurso.</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Criterios -->
                            <?php if (!empty($criterios)): ?>
                                <div class="card shadow-sm mb-4">
                                    <div class="card-header">
                                        <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Criterios de Evaluación</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <?php foreach ($criterios as $c): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                                        <span class="fw-semibold"><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></span>
                                                        <span class="badge bg-primary">Max: <?= $c['puntaje_maximo'] ?></span>
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
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Elementos del DOM
        const sidebar = document.getElementById('sidebar');
        const mainContent = document.getElementById('mainContent');
        const toggleBtn = document.getElementById('toggleSidebarBtn');
        const showBtn = document.getElementById('showSidebarBtn');
        const body = document.body;

        // Estado del sidebar
        let sidebarVisible = true;

        // Función para ocultar sidebar
        function hideSidebar() {
            sidebar.classList.add('d-none');
            mainContent.classList.remove('col-md-9', 'col-lg-10');
            mainContent.classList.add('col-12');
            showBtn.classList.remove('d-none');
            body.classList.add('sidebar-hidden');
            sidebarVisible = false;

            // Cambiar el ícono del botón show
            showBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        }

        // Función para mostrar sidebar
        function showSidebar() {
            sidebar.classList.remove('d-none');
            mainContent.classList.remove('col-12');
            mainContent.classList.add('col-md-9', 'col-lg-10');
            showBtn.classList.add('d-none');
            body.classList.remove('sidebar-hidden');
            sidebarVisible = true;
        }

        // Event listeners
        toggleBtn.addEventListener('click', hideSidebar);
        showBtn.addEventListener('click', showSidebar);

        // Filtros de búsqueda
        const buscar = document.getElementById('buscarConjunto');
        const filtro = document.getElementById('filtroCategoria');

        if (buscar && filtro) {
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
                if (badge) badge.textContent = visibles + ' encontrados';
            }

            buscar.addEventListener('input', filtrar);
            filtro.addEventListener('change', filtrar);
        }

        // Sidebar móvil
        function handleMobileSidebar() {
            if (window.innerWidth < 768) {
                // En móvil, usar el sistema de overlay
                showBtn.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                });

                // Cerrar sidebar al hacer clic fuera
                document.addEventListener('click', function(event) {
                    if (window.innerWidth < 768 &&
                        !sidebar.contains(event.target) &&
                        !showBtn.contains(event.target) &&
                        sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            } else {
                // En escritorio, usar el sistema de toggle normal
                sidebar.classList.remove('show');
            }
        }

        // Manejo responsive
        function handleResize() {
            if (window.innerWidth >= 768) {
                // En escritorio, asegurarse de que el sidebar esté visible
                sidebar.classList.remove('show');
                if (!sidebarVisible) {
                    showSidebar();
                }
            } else {
                // En móvil, ocultar sidebar por defecto
                if (sidebarVisible) {
                    hideSidebar();
                }
            }
        }

        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            handleMobileSidebar();
            handleResize();
        });

        window.addEventListener('resize', handleResize);
    </script>
</body>

</html>
<?php
// views/admin/admin_ver_resultados.php - Versión CORREGIDA usando id_participacion
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
global $pdo;

$id_concurso = $_GET['id_concurso'] ?? null;
if (!$id_concurso || !is_numeric($id_concurso)) {
    header('Location: index.php?page=admin_resultados&error=no_concurso');
    exit;
}

// Información del concurso
$stmt = $pdo->prepare("SELECT id_concurso, nombre, fecha_inicio, fecha_fin, estado FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso_actual = $stmt->fetch();
if (!$concurso_actual) die("Concurso no encontrado");

// === OBTENER RESULTADOS USANDO id_participacion ===
$sql = "SELECT 
            c.nombre AS conjunto,
            pc.orden_presentacion,
            s.nombre_serie AS categoria,
            ROUND(AVG(dc.puntaje), 2) AS promedio_final,
            COUNT(dc.id_detalle) AS calificaciones_count
        FROM participacionconjunto pc
        JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
        JOIN serie s ON c.id_serie = s.id_serie
        LEFT JOIN calificacion ca ON pc.id_participacion = ca.id_participacion AND ca.estado = 'enviado'
        LEFT JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
        WHERE pc.id_concurso = ?
        GROUP BY c.id_conjunto, c.nombre, pc.orden_presentacion, s.nombre_serie
        ORDER BY promedio_final DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_concurso]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular posiciones con manejo de empates
$posicion = 1;
$ultimo_puntaje = null;
foreach ($resultados as $index => &$r) {
    if ($index === 0) {
        $r['posicion'] = 1;
    } else {
        if ($r['promedio_final'] < $ultimo_puntaje) {
            $posicion = $index + 1;
        }
        $r['posicion'] = $posicion;
    }
    $ultimo_puntaje = $r['promedio_final'];
}
unset($r);

// Estadísticas
$total = count($resultados);
$calificados = 0;
$suma = 0;
foreach ($resultados as $r) {
    if ($r['calificaciones_count'] > 0 && $r['promedio_final'] > 0) {
        $calificados++;
        $suma += $r['promedio_final'];
    }
}
$pendientes = $total - $calificados;
$promedio_general = $calificados > 0 ? round($suma / $calificados, 2) : 0;

$estadisticas = [
    'total_conjuntos' => $total,
    'calificados' => $calificados,
    'pendientes' => $pendientes,
    'promedio_general' => $promedio_general
];

$tiene_calificaciones = $calificados > 0;

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
    <title>Resultados del Concurso - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-success {
            background-color: #d1f7d1 !important;
        }

        .medal {
            font-size: 16px;
        }

        .estado-badge {
            font-size: 12px;
        }

        .stat-card {
            transition: transform 0.2s;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        /* NUEVOS ESTILOS PARA EL LAYOUT COMPACTO */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 1.5rem;
        }

        .page-header h2 {
            margin: 0;
            font-weight: 700;
            color: #0d6efd;
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
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
            height: 100%;
        }

        .stat-card {
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
        }

        .info-box {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
            border-left: 4px solid #0d6efd;
            margin-bottom: 10px;
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

        /* SCROLL PARA LA TABLA */
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
            background-color: #212529;
            z-index: 10;
        }
    </style>
</head>

<body>
    <div class="container-fluid">

        <!-- Encabezado principal -->
        <div class="page-header">
            <h2>🏆 Resultados del Concurso</h2>

            <div class="d-flex align-items-center gap-3">
                <?php if ($tiene_calificaciones): ?>
                    <span class="badge bg-success badge-status">✅ Con calificaciones</span>
                <?php else: ?>
                    <span class="badge bg-warning text-dark badge-status">⏳ Esperando calificaciones</span>
                <?php endif; ?>

                <a href="index.php?page=admin_resultados" class="btn btn-outline-secondary">
                    ← Volver a Resultados en Vivo
                </a>
            </div>
        </div>

        <!-- LAYOUT COMPACTO: 3 COLUMNAS -->
        <div class="row mb-4">
            <!-- COLUMNA 1: INFORMACIÓN DEL CONCURSO -->
            <div class="col-md-4 mb-3">
                <div class="card info-card border-primary h-100">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
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
                <div class="card info-card border-info h-100">
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

            <!-- COLUMNA 3: PDF -->
            <div class="col-md-4 mb-3">
                <?php if (!empty($resultados)): ?>
                    <div class="card info-card border-success h-100">
                        <div class="card-header bg-success text-white text-center py-2">
                            <h6 class="mb-0">📄 Exportar Resultados</h6>
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf text-danger display-6"></i>
                            </div>
                            <p class="text-muted small mb-3">
                                Documento formal listo para impresión y entrega oficial
                            </p>
                            <a href="generar_pdf_real.php?id_concurso=<?php echo $id_concurso; ?>"
                                class="btn btn-outline-success btn-sm fw-semibold" target="_blank">
                                <i class="bi bi-download me-2"></i>Descargar PDF
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card info-card border-secondary h-100">
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

        <!-- Filtros -->
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
                                <?php foreach ($resultados as $r): ?>
                                    <tr class="<?= ($r['posicion'] <= 3 && $r['promedio_final'] > 0) ? 'table-success' : '' ?>">
                                        <td>
                                            <strong><?= $r['posicion'] ?>°</strong>
                                            <?php if ($r['posicion'] == 1 && $r['promedio_final'] > 0): ?>
                                                🥇
                                            <?php elseif ($r['posicion'] == 2 && $r['promedio_final'] > 0): ?>
                                                🥈
                                            <?php elseif ($r['posicion'] == 3 && $r['promedio_final'] > 0): ?>
                                                🥉
                                            <?php endif; ?>
                                        </td>
                                        <td class="fw-bold"><?= $r['orden_presentacion'] ?></td>
                                        <td class="conjunto-nombre"><?= htmlspecialchars($r['conjunto']) ?></td>
                                        <td class="categoria-nombre"><?= htmlspecialchars($r['categoria']) ?></td>
                                        <td>
                                            <strong class="<?= $r['promedio_final'] > 0 ? 'text-primary' : 'text-muted' ?>">
                                                <?= $r['promedio_final'] > 0 ? number_format($r['promedio_final'], 2) : 'Sin calificar' ?>
                                            </strong>
                                        </td>
                                        <td>
                                            <span class="badge <?= $r['calificaciones_count'] > 0 ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $r['calificaciones_count'] > 0 ? 'Calificado' : 'Pendiente' ?>
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
                            <div class="col-md-3"><strong>Total conjuntos:</strong> <?= $estadisticas['total_conjuntos'] ?></div>
                            <div class="col-md-3"><strong>Con calificaciones:</strong> <?= $estadisticas['calificados'] ?></div>
                            <div class="col-md-3"><strong>Pendientes:</strong> <?= $estadisticas['pendientes'] ?></div>
                            <div class="col-md-3"><strong>Porcentaje:</strong> <?= $estadisticas['total_conjuntos'] > 0 ? round(($estadisticas['calificados'] / $estadisticas['total_conjuntos']) * 100, 1) : 0 ?>%</div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="alert alert-warning text-center m-3">
                        <h5>📭 No hay conjuntos participantes</h5>
                        <p>Este concurso no tiene conjuntos registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Criterios -->
        <?php if (!empty($criterios)): ?>
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">📝 Criterios de Evaluación</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach ($criterios as $c): ?>
                            <div class="col-md-6 mb-2">
                                <div class="d-flex justify-content-between align-items-center p-2 border rounded">
                                    <span class="fw-semibold"><?= htmlspecialchars($c['nombre']) ?></span>
                                    <span class="badge bg-primary">Max: <?= $c['puntaje_maximo'] ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscar = document.getElementById('buscarConjunto');
            const filtro = document.getElementById('filtroCategoria');
            if (!buscar || !filtro) return;

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

            buscar.addEventListener('input', filtrar);
            filtro.addEventListener('change', filtrar);
        });
    </script>
</body>

</html>
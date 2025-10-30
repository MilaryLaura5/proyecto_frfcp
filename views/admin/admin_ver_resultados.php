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

// === OBTENER RESULTADOS CON SUMA CORRECTA POR CRITERIO ===
$sql = "
    SELECT 
        c.nombre AS conjunto,
        pc.orden_presentacion,
        s.nombre_serie AS categoria,
        COALESCE(ROUND(SUM(promedios_criterio.promedio_criterio), 2), 0) AS promedio_final,
        COUNT(promedios_criterio.id_criterio_concurso) AS calificaciones_count
    FROM participacionconjunto pc
    JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
    JOIN serie s ON c.id_serie = s.id_serie
    LEFT JOIN (
        SELECT 
            ca.id_participacion,
            dc.id_criterio_concurso,
            AVG(dc.puntaje) AS promedio_criterio
        FROM calificacion ca
        JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
        WHERE ca.estado = 'enviado'
        GROUP BY ca.id_participacion, dc.id_criterio_concurso
    ) AS promedios_criterio ON pc.id_participacion = promedios_criterio.id_participacion
    WHERE pc.id_concurso = ?
    GROUP BY c.id_conjunto, c.nombre, pc.orden_presentacion, s.nombre_serie
    ORDER BY promedio_final DESC
";


$stmt = $pdo->prepare($sql);
$stmt->execute([$id_concurso]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular posiciones con manejo de empates correcto
$posicion_actual = 1;
$contador = 1;
$ultimo_puntaje = null;

foreach ($resultados as &$r) {
    if ($ultimo_puntaje === null || $r['promedio_final'] < $ultimo_puntaje) {
        $posicion_actual = $contador;
    }
    $r['posicion'] = $posicion_actual;

    $ultimo_puntaje = $r['promedio_final'];
    $contador++;
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
            /* 🔴 Rojo FRFCP */
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

        .card-header h6 i,
        .card-header h5 i {
            color: #c9184a;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #fdf2f2;
            /* Fondo claro rojizo */
        }

        .table td,
        .table th {
            vertical-align: middle;
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
    </style>
</head>

<body class="p-3">

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

                <a href="index.php?page=admin_resultados" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver a Resultados en Vivo
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

            <!-- PDF -->
            <div class="col-md-4 mb-3">
                <?php if (!empty($resultados)): ?>
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-success text-white text-center">
                            <h6 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Exportar Resultados</h6>
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf display-6 text-danger"></i>
                            </div>
                            <p class="text-muted small mb-3">
                                Documento formal listo para impresión y entrega oficial
                            </p>
                            <a href="generar_pdf_real.php?id_concurso=<?= $id_concurso ?>"
                                class="btn btn-outline-success btn-sm fw-semibold" target="_blank">
                                <i class="bi bi-download me-2"></i>Descargar PDF
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-secondary text-white text-center">
                            <h6 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Exportar Resultados</h6>
                        </div>
                        <div class="card-body text-center d-flex flex-column justify-content-center">
                            <div class="mb-3">
                                <i class="bi bi-file-earmark-pdf display-6 text-muted"></i>
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
                <span class="badge bg-secondary"><?= count($resultados) ?> encontrados</span>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($resultados)): ?>
                    <div class="table-responsive table-container">
                        <table class="table table-hover mb-0">
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
                                        <td class="conjunto-nombre"><?= htmlspecialchars($r['conjunto'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td class="categoria-nombre"><?= htmlspecialchars($r['categoria'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>
                                            <strong class="<?= $r['promedio_final'] > 0 ? 'text-primary' : 'text-muted' ?>">
                                                <?= !empty($r['promedio_final']) ? number_format($r['promedio_final'], 2) : 'Sin calificar' ?>

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

                    <!-- Resumen -->
                    <div class="p-3 bg-light border-top">
                        <h6><i class="bi bi-graph-up"></i> Resumen Detallado:</h6>
                        <div class="row">
                            <div class="col-md-3"><strong>Total conjuntos:</strong> <?= $estadisticas['total_conjuntos'] ?></div>
                            <div class="col-md-3"><strong>Con calificaciones:</strong> <?= $estadisticas['calificados'] ?></div>
                            <div class="col-md-3"><strong>Pendientes:</strong> <?= $estadisticas['pendientes'] ?></div>
                            <div class="col-md-3"><strong>Porcentaje:</strong> <?= $estadisticas['total_conjuntos'] > 0 ? round(($estadisticas['calificados'] / $estadisticas['total_conjuntos']) * 100, 1) : 0 ?>%</div>
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

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
                if (badge) badge.textContent = visibles + ' encontrados';
            }

            buscar.addEventListener('input', filtrar);
            filtro.addEventListener('change', filtrar);
        });
    </script>
</body>

</html>
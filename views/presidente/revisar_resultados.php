<?php
// views/presidente/revisar_resultados.php - VERSIÓN CON MEJORAS

// Obtener información del concurso actual
global $pdo;
$id_concurso = $_GET['id_concurso'] ?? 0;

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
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 bg-dark text-white min-vh-100">
                <div class="p-3">
                    <h4>👑 Presidente</h4>
                    <p class="text-muted mb-1">Sesión activa</p>
                    <p class="fw-bold text-warning"><?php echo htmlspecialchars($_SESSION['user']['nombre']); ?></p>
                    <hr class="bg-light">
                    <ul class="nav nav-pills flex-column">
                        <li class="nav-item">
                            <a href="index.php?page=presidente_seleccionar_concurso" class="nav-link">
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

            <!-- Main Content -->
            <div class="col-md-9">
                <div class="p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2 class="text-primary">🏆 Resultados del Concurso</h2>
                        <div>
                            <?php if ($tiene_calificaciones): ?>
                                <span class="badge bg-success fs-6">✅ Con calificaciones</span>
                            <?php else: ?>
                                <span class="badge bg-warning fs-6">⏳ Esperando calificaciones</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Información del Concurso -->
                    <div class="card mb-4 border-primary">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">📋 Información del Concurso</h5>
                            <span class="badge estado-badge 
                                <?php
                                if ($concurso_actual['estado'] == 'Activo') echo 'bg-success';
                                elseif ($concurso_actual['estado'] == 'Cerrado') echo 'bg-info';
                                else echo 'bg-warning';
                                ?>">
                                <?php echo $concurso_actual['estado']; ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>ID Concurso:</strong> <?php echo $concurso_actual['id_concurso']; ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($concurso_actual['nombre']); ?></p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Estado:</strong>
                                        <span class="badge 
                                            <?php
                                            if ($concurso_actual['estado'] == 'Activo') echo 'bg-success';
                                            elseif ($concurso_actual['estado'] == 'Cerrado') echo 'bg-info';
                                            else echo 'bg-warning';
                                            ?>">
                                            <?php echo $concurso_actual['estado']; ?>
                                        </span>
                                    </p>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-6">
                                    <p><strong>Fecha Inicio:</strong> <?php echo date('d/m/Y H:i', strtotime($concurso_actual['fecha_inicio'])); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Fecha Fin:</strong> <?php echo date('d/m/Y H:i', strtotime($concurso_actual['fecha_fin'])); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ESTADÍSTICAS MEJORADAS -->
                    <?php if (!empty($resultados)): ?>
                        <div class="row mb-4">
                            <div class="col-md-3">
                                <div class="card text-center bg-light stat-card">
                                    <div class="card-body">
                                        <h5 class="card-title">📊 Total</h5>
                                        <p class="card-text h4 text-primary"><?php echo $estadisticas['total_conjuntos']; ?></p>
                                        <small class="text-muted">Conjuntos</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light stat-card">
                                    <div class="card-body">
                                        <h5 class="card-title">✅ Calificados</h5>
                                        <p class="card-text h4 text-success"><?php echo $estadisticas['calificados']; ?></p>
                                        <small class="text-muted">Con calificación</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light stat-card">
                                    <div class="card-body">
                                        <h5 class="card-title">📈 Promedio</h5>
                                        <p class="card-text h4 text-info"><?php echo $estadisticas['promedio_general']; ?></p>
                                        <small class="text-muted">Puntaje general</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card text-center bg-light stat-card">
                                    <div class="card-body">
                                        <h5 class="card-title">⭐ Máximo</h5>
                                        <p class="card-text h4 text-warning"><?php echo $estadisticas['puntaje_maximo']; ?></p>
                                        <small class="text-muted">Puntaje más alto</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- FILTROS DE BÚSQUEDA -->
                    <?php if (!empty($resultados)): ?>
                        <div class="card mb-3">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="mb-0">🔍 Filtros de Búsqueda</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
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

                    <!-- Resultados -->
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">📊 Resultados Finales</h5>
                            <span class="badge bg-light text-dark"><?php echo count($resultados); ?> conjuntos</span>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($resultados)): ?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="10%">Posición</th>
                                                <th width="8%">N° Orden</th>
                                                <th width="42%">Conjunto Folklórico</th>
                                                <th width="15%">Categoría</th>
                                                <th width="15%">Puntaje Final</th>
                                                <th width="10%">Estado</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($resultados as $resultado): ?>
                                                <tr class="<?php echo $resultado['posicion'] <= 3 && $resultado['promedio_final'] > 0 ? 'table-success' : ''; ?>">
                                                    <td>
                                                        <strong><?php echo $resultado['posicion']; ?>°</strong>
                                                        <?php if ($resultado['posicion'] == 1 && $resultado['promedio_final'] > 0): ?>
                                                            <span class="medal">🥇</span>
                                                        <?php elseif ($resultado['posicion'] == 2 && $resultado['promedio_final'] > 0): ?>
                                                            <span class="medal">🥈</span>
                                                        <?php elseif ($resultado['posicion'] == 3 && $resultado['promedio_final'] > 0): ?>
                                                            <span class="medal">🥉</span>
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

                                <!-- Resumen de calificaciones -->
                                <div class="mt-3 p-3 bg-light rounded">
                                    <h6>📈 Resumen Detallado:</h6>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <strong>Total conjuntos:</strong> <?php echo $estadisticas['total_conjuntos']; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Con calificaciones:</strong> <?php echo $estadisticas['calificados']; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Pendientes:</strong> <?php echo $estadisticas['pendientes']; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <strong>Porcentaje:</strong>
                                            <?php echo round(($estadisticas['calificados'] / $estadisticas['total_conjuntos']) * 100, 1); ?>%
                                        </div>
                                    </div>
                                </div>

                            <?php else: ?>
                                <div class="alert alert-warning text-center">
                                    <h5>📭 No hay conjuntos participantes</h5>
                                    <p>Este concurso no tiene conjuntos registrados para mostrar resultados.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Criterios de Evaluación -->
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

                    <!-- Botón para Descargar PDF -->
                    <?php if (!empty($resultados)): ?>
                        <div class="card border-success">
                            <div class="card-header bg-success text-white text-center">
                                <h5 class="mb-0">📄 Exportar Resultados Oficiales</h5>
                            </div>
                            <div class="card-body text-center py-4">
                                <a href="generar_pdf_real.php?id_concurso=<?php echo $id_concurso; ?>"
                                    class="btn btn-danger btn-lg px-5">
                                    🎯 Descargar PDF de Resultados
                                </a>
                                <p class="text-muted mt-3 mb-0">
                                    Documento formal listo para impresión y entrega oficial
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SCRIPT PARA FILTROS DINÁMICOS -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const buscarInput = document.getElementById('buscarConjunto');
            const filtroSelect = document.getElementById('filtroCategoria');

            if (buscarInput) {
                buscarInput.addEventListener('input', filtrarResultados);
            }

            if (filtroSelect) {
                filtroSelect.addEventListener('change', filtrarResultados);
            }

            function filtrarResultados() {
                const buscar = document.getElementById('buscarConjunto').value.toLowerCase();
                const categoria = document.getElementById('filtroCategoria').value;
                const filas = document.querySelectorAll('tbody tr');

                let visibleCount = 0;

                filas.forEach(fila => {
                    const conjunto = fila.querySelector('.conjunto-nombre').textContent.toLowerCase();
                    const cat = fila.querySelector('.categoria-nombre').textContent;

                    const coincideBuscar = conjunto.includes(buscar);
                    const coincideCategoria = !categoria || cat === categoria;

                    if (coincideBuscar && coincideCategoria) {
                        fila.style.display = '';
                        visibleCount++;
                    } else {
                        fila.style.display = 'none';
                    }
                });

                // Actualizar contador de conjuntos visibles
                const badge = document.querySelector('.card-header .badge');
                if (badge) {
                    badge.textContent = visibleCount + ' conjuntos';
                }
            }
        });
    </script>
</body>

</html>
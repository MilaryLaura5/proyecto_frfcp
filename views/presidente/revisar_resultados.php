<!-- views/presidente/revisar_resultados.php -->
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_presidente();
$user = auth();

// Variables pasadas desde el controlador
// $resultados = array de resultados por conjunto
// $criterios = array de criterios usados
// $id_concurso = ID del concurso
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisar Resultados Finales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .main-content { padding: 20px; }
        .podium { display: flex; justify-content: center; margin: 20px 0; }
        .podium-item { text-align: center; margin: 0 15px; }
        .gold   { font-size: 2.5rem; color: #FFD700; }
        .silver { font-size: 2.2rem; color: #C0C0C0; }
        .bronze { font-size: 2rem; color: #CD7F32; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../../views/templates/sidebar_presidente.php'; ?>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-trophy"></i> Revisar Resultados Finales</h2>
                <span class="badge bg-success">Presidente</span>
            </div>

            <div class="alert alert-info">
                <strong>🔍 Validación Final:</strong> Revise los resultados antes de confirmar. Una vez publicados, no se pueden modificar.
            </div>

            <!-- Podio General -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5><i class="bi bi-star-fill"></i> Podio General del Concurso</h5>
                </div>
                <div class="card-body">
                    <div class="podium">
                        <?php 
                        // Ordenar resultados por promedio
                        usort($resultados, function($a, $b) {
                            return $b['promedio_final'] <=> $a['promedio_final'];
                        });
                        ?>
                        <!-- Oro -->
                        <?php if (isset($resultados[0])): ?>
                        <div class="podium-item">
                            <div class="gold">🥇</div>
                            <strong><?= htmlspecialchars($resultados[0]['conjunto']) ?></strong>
                            <div class="small text-muted"><?= number_format($resultados[0]['promedio_final'], 2) ?></div>
                        </div>
                        <?php endif; ?>

                        <!-- Plata -->
                        <?php if (isset($resultados[1])): ?>
                        <div class="podium-item">
                            <div class="silver">🥈</div>
                            <strong><?= htmlspecialchars($resultados[1]['conjunto']) ?></strong>
                            <div class="small text-muted"><?= number_format($resultados[1]['promedio_final'], 2) ?></div>
                        </div>
                        <?php endif; ?>

                        <!-- Bronce -->
                        <?php if (isset($resultados[2])): ?>
                        <div class="podium-item">
                            <div class="bronze">🥉</div>
                            <strong><?= htmlspecialchars($resultados[2]['conjunto']) ?></strong>
                            <div class="small text-muted"><?= number_format($resultados[2]['promedio_final'], 2) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Resultados por Serie -->
            <h3><i class="bi bi-collection"></i> Resultados por Serie</h3>
            <?php 
            $series = [];
            foreach ($resultados as $r) {
                $series[$r['nombre_serie']][] = $r;
            }

            foreach ($series as $nombre_serie => $resultados_serie):
                usort($resultados_serie, function($a, $b) {
                    return $b['promedio_final'] <=> $a['promedio_final'];
                });
            ?>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5><?= htmlspecialchars($nombre_serie) ?></h5>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Puesto</th>
                                <th>Conjunto</th>
                                <th>Tipo</th>
                                <th>Promedio</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resultados_serie as $i => $r): ?>
                            <tr>
                                <td><strong><?= $i + 1 ?>°</strong></td>
                                <td><?= htmlspecialchars($r['conjunto']) ?></td>
                                <td><?= ucfirst(str_replace('_', ' ', $r['tipo_danza'])) ?></td>
                                <td><?= number_format($r['promedio_final'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Acciones finales -->
            <div class="text-center mt-4">
                <a href="index.php?page=presidente_generar_reporte&id_concurso=<?= $id_concurso ?>" 
                   class="btn btn-success btn-lg">
                    <i class="bi bi-file-earmark-pdf"></i> Generar Reporte Oficial
                </a>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<!-- views/presidente/seleccionar_concursos.php -->
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_presidente();
$user = auth();

// Variables pasadas desde el controlador
// $concursos = array de concursos
// $error = mensaje de error
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Seleccionar Concurso</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .main-content {
            padding: 20px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <?php include __DIR__ . '/../templates/sidebar_presidente.php'; ?>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="bi bi-list-check"></i> Seleccionar Concurso</h2>
                    <span class="badge bg-warning">Presidente</span>
                </div>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">No hay concursos disponibles o aún no hay resultados.</div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <h5>Concursos Disponibles</h5>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($concursos as $c): ?>
                                    <tr>
                                        <td><?= $c['id_concurso'] ?></td>
                                        <td><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_inicio'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_fin'])) ?></td>
                                        <td>
                                            <span class="badge 
                                        <?= $c['estado'] == 'Activo' ? 'bg-primary' : ($c['estado'] == 'Cerrado' ? 'bg-success' : 'bg-warning') ?>">
                                                <?= $c['estado'] ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="index.php?page=presidente_revisar_resultados&id_concurso=<?= $c['id_concurso'] ?>"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Ver Resultados
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
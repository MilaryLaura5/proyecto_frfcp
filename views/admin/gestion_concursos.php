<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Concursos - FRFCP Admin</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
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
            color: #C1121F;
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
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #f8f9fa;
        }

        .table td,
        .table th {
            vertical-align: middle;
            white-space: nowrap;
        }

        .badge {
            font-weight: 500;
            padding: 0.5em 0.8em;
        }

        .btn-group-actions {
            gap: 0.5rem;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .d-flex-mobile-column {
                flex-direction: column;
            }

            .btn-sm-mobile {
                margin-top: 0.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado fijo con ancho completo -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <?= $editando ? 'Editar Concurso' : 'Gestionar Concursos' ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Contenido principal con ancho completo -->
    <div class="container-fluid px-4">

        <!-- Mensajes de estado -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                ⚠️ Todos los campos son obligatorios.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'fechas'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                ❌ La fecha de inicio debe ser anterior a la de fin.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'tiene_evaluaciones'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                ❌ No se puede eliminar: ya tiene evaluaciones registradas.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'no_existe'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                ❌ El concurso no existe.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Concurso creado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Concurso actualizado exitosamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Concurso eliminado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($success == 'activado'): ?>
            <div class="alert alert-info alert-dismissible fade show rounded-4 mt-3" role="alert">
                ▶️ Concurso activado y listo para evaluaciones.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Formulario: Crear o Editar -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi <?= $editando ? 'bi-pencil-fill text-warning' : 'bi-plus-circle-fill text-primary' ?>"></i>
                    <?= $editando ? 'Editar Concurso' : 'Crear Nuevo Concurso' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_concurso' : 'admin_crear_concurso_submit' ?>">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id_concurso" value="<?= (int)$concurso_a_editar['id_concurso'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Concurso</strong></label>
                        <input type="text"
                            class="form-control form-control-lg"
                            name="nombre"
                            placeholder="Ej: Fiesta de la Candelaria 2025"
                            value="<?= htmlspecialchars($editando ? $concurso_a_editar['nombre'] : '', ENT_QUOTES, 'UTF-8') ?>"
                            required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Fecha de Inicio</strong></label>
                            <input type="datetime-local"
                                class="form-control"
                                name="fecha_inicio"
                                value="<?= $editando ? date('Y-m-d\TH:i', strtotime($concurso_a_editar['fecha_inicio'])) : '' ?>"
                                required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Fecha de Fin</strong></label>
                            <input type="datetime-local"
                                class="form-control"
                                name="fecha_fin"
                                value="<?= $editando ? date('Y-m-d\TH:i', strtotime($concurso_a_editar['fecha_fin'])) : '' ?>"
                                required>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-start">
                        <?php if ($editando): ?>
                            <button type="submit" class="btn btn-warning btn-lg px-4">Actualizar Concurso</button>
                            <a href="index.php?page=admin_gestion_concursos" class="btn btn-secondary btn-lg px-4">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-primary btn-lg px-4">Registrar Concurso</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado de concursos -->
        <div class="card shadow-sm">
            <div class="card-header">
                <h5><i class="bi bi-list-ul"></i> Concursos Registrados</h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($concursos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th>Inicio</th>
                                    <th>Fin</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($concursos as $idx => $c): ?>
                                    <tr>
                                        <td><?= $idx + 1 ?></td>
                                        <td><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_inicio'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_fin'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $c['estado'] == 'Activo' ? 'success' : ($c['estado'] == 'Cerrado' ? 'danger' : 'warning') ?>">
                                                <?= ucfirst($c['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($c['estado'] === 'Cerrado'): ?>
                                                <span class="text-muted small">Finalizado</span>
                                            <?php else: ?>
                                                <div class="btn-group-actions d-flex flex-wrap">
                                                    <!-- Conjuntos -->
                                                    <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $c['id_concurso'] ?>"
                                                        class="btn btn-sm btn-info" title="Conjuntos">
                                                        <i class="bi bi-people"></i>
                                                    </a>

                                                    <!-- Editar/Eliminar/Activar -->
                                                    <?php if ($c['estado'] === 'Pendiente'): ?>
                                                        <a href="index.php?page=admin_editar_concurso&id=<?= $c['id_concurso'] ?>"
                                                            class="btn btn-sm btn-warning" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="index.php?page=admin_eliminar_concurso&id=<?= $c['id_concurso'] ?>"
                                                            class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Seguro?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                        <a href="index.php?page=admin_activar_concurso&id=<?= $c['id_concurso'] ?>"
                                                            class="btn btn-sm btn-success" title="Activar" onclick="return confirm('¿Activar este concurso?');">
                                                            <i class="bi bi-play-fill"></i>
                                                        </a>
                                                    <?php elseif ($c['estado'] === 'Activo'): ?>
                                                        <a href="index.php?page=admin_cerrar_concurso&id=<?= $c['id_concurso'] ?>"
                                                            class="btn btn-sm btn-danger" title="Cerrar" onclick="return confirm('¿Cerrar este concurso?');">
                                                            <i class="bi bi-x-circle"></i>
                                                        </a>
                                                    <?php endif; ?>

                                                    <!-- Jurados y Criterios -->
                                                    <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $c['id_concurso'] ?>"
                                                        class="btn btn-sm btn-outline-success" title="Jurados">👥</a>
                                                    <a href="index.php?page=admin_agregar_criterios&id_concurso=<?= $c['id_concurso'] ?>"
                                                        class="btn btn-sm btn-outline-primary" title="Criterios">📋</a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-emoji-frown display-4 text-muted"></i>
                        <p class="lead text-muted mt-3">Aún no hay concursos registrados.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
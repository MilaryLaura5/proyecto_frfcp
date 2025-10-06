<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Concursos - FRFCP Admin</title>
    <!-- ✅ Corrección: espacios eliminados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container-main {
            max-width: 900px;
            margin: 80px auto;
        }

        .alert {
            border-radius: 8px;
        }

        .btn-success:not(.btn-sm) {
            background-color: #198754;
        }

        .table th {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <!-- Encabezado -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-calendar-event me-2 text-primary"></i>
                <?= $editando ? 'Editar Concurso' : 'Gestionar Concursos' ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes de estado -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Todos los campos son obligatorios.</div>
        <?php elseif ($error === 'fechas'): ?>
            <div class="alert alert-danger">❌ La fecha de inicio debe ser anterior a la de fin.</div>
        <?php elseif ($error === 'tiene_evaluaciones'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: ya tiene evaluaciones registradas.</div>
        <?php elseif ($error === 'no_existe'): ?>
            <div class="alert alert-danger">❌ El concurso no existe.</div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Concurso creado correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Concurso actualizado exitosamente.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Concurso eliminado correctamente.</div>
        <?php elseif ($success == 'activado'): ?>
            <div class="alert alert-info">▶️ Concurso activado y listo para evaluaciones.</div>
        <?php endif; ?>

        <!-- Formulario: Crear o Editar -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi <?= $editando ? 'bi-pencil-fill text-warning' : 'bi-plus-circle-fill text-success' ?>"></i>
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
                            class="form-control"
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
                            <button type="submit" class="btn btn-warning px-4">Actualizar Concurso</button>
                            <a href="index.php?page=admin_gestion_concursos" class="btn btn-secondary px-4">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success px-4">Registrar Concurso</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado de concursos -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
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
                                            <span class="badge bg-<?= $c['estado'] == 'Activo' ? 'success' : ($c['estado'] == 'Cerrado' ? 'secondary' : 'warning') ?>">
                                                <?= ucfirst($c['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <!-- Primaria: Gestión básica -->
                                            <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $c['id_concurso'] ?>"
                                                class="btn btn-sm btn-info me-1" title="Gestionar conjuntos"><i class="bi bi-people"></i></a>

                                            <?php if ($c['estado'] === 'Pendiente'): ?>
                                                <!-- Edición -->
                                                <a href="index.php?page=admin_editar_concurso&id=<?= $c['id_concurso'] ?>"
                                                    class="btn btn-sm btn-warning me-1" title="Editar"><i class="bi bi-pencil"></i></a>
                                                <a href="index.php?page=admin_eliminar_concurso&id=<?= $c['id_concurso'] ?>"
                                                    class="btn btn-sm btn-danger me-1" title="Eliminar" onclick="return confirm('¿Seguro?');"><i class="bi bi-trash"></i></a>
                                                <!-- Activación -->
                                                <a href="index.php?page=admin_activar_concurso&id=<?= $c['id_concurso'] ?>"
                                                    class="btn btn-sm btn-success" title="Activar" onclick="return confirm('¿Activar este concurso?');"><i class="bi bi-play-fill"></i></a>
                                            <?php elseif ($c['estado'] === 'Activo'): ?>
                                                <!-- Cierre -->
                                                <a href="index.php?page=admin_cerrar_concurso&id=<?= $c['id_concurso'] ?>"
                                                    class="btn btn-sm btn-danger" title="Cerrar" onclick="return confirm('¿Cerrar este concurso?');"><i class="bi bi-x-circle"></i></a>
                                            <?php endif; ?>

                                            <!-- Secundarios: Jurados y criterios -->
                                            <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $c['id_concurso'] ?>"
                                                class="btn btn-sm btn-outline-success ms-1">Jurados</a>
                                            <a href="index.php?page=admin_agregar_criterios&id_concurso=<?= $c['id_concurso'] ?>"
                                                class="btn btn-sm btn-outline-warning ms-1"><i class="bi bi-pen"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 m-0">
                        <i class="bi bi-emoji-frown"></i> Aún no hay concursos registrados.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Tipos y Series - FRFCP Admin</title>
    <!-- ✅ Corrección: espacios eliminados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 1200px;
            margin: 80px auto;
        }

        .split-container {
            display: flex;
            gap: 2rem;
        }

        .panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .accordion-button:not(.collapsed) {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-tags me-2 text-primary"></i> Gestión de Tipos de Danza y Series</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($success == 'tipo_creado'): ?>
            <div class="alert alert-success">✅ Tipo de danza creado correctamente.</div>
        <?php elseif ($success == 'tipo_editado'): ?>
            <div class="alert alert-success">✅ Tipo de danza actualizado.</div>
        <?php elseif ($success == 'tipo_eliminado'): ?>
            <div class="alert alert-info">🗑️ Tipo de danza eliminado.</div>
        <?php elseif ($success == 'serie_creada'): ?>
            <div class="alert alert-success">✅ Serie creada correctamente.</div>
        <?php elseif ($success == 'serie_editada'): ?>
            <div class="alert alert-success">✅ Serie actualizada.</div>
        <?php elseif ($success == 'serie_eliminada'): ?>
            <div class="alert alert-info">🗑️ Serie eliminada.</div>
        <?php endif; ?>

        <?php if ($error === 'nombre_vacio'): ?>
            <div class="alert alert-warning">⚠️ El nombre del tipo no puede estar vacío.</div>
        <?php elseif ($error === 'tipo_existe'): ?>
            <div class="alert alert-danger">❌ Ya existe un tipo con ese nombre.</div>
        <?php elseif ($error === 'tiene_series'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: tiene series asociadas.</div>
        <?php endif; ?>

        <!-- Diseño de dos columnas -->
        <div class="split-container">

            <!-- Panel izquierdo: Tipos de Danza -->
            <div class="panel" style="flex: 1;">
                <h5><i class="bi bi-tag"></i> Tipos de Danza Oficiales</h5>
                <p class="text-muted">Gestiona los tipos de danza. Se usarán para organizar las series.</p>

                <!-- Formulario: Crear o Editar Tipo -->
                <form method="POST" action="index.php?page=<?= $editando_tipo ? 'admin_actualizar_tipo_danza' : 'admin_crear_tipo_danza' ?>" class="mb-4">
                    <?php if ($editando_tipo): ?>
                        <input type="hidden" name="id_tipo" value="<?= $tipo_edit['id_tipo'] ?>">
                        <div class="alert alert-warning">Editando: <strong><?= htmlspecialchars($tipo_edit['nombre_tipo']) ?></strong></div>
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Tipo</strong></label>
                        <input type="text"
                            class="form-control"
                            name="nombre_tipo"
                            placeholder="Ej: traje_originario"
                            value="<?= $editando_tipo ? htmlspecialchars($tipo_edit['nombre_tipo']) : '' ?>"
                            required>
                        <small class="text-muted">Usa guiones bajos, sin espacios (ej: trajes_luces)</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <?php if ($editando_tipo): ?>
                            <button type="submit" class="btn btn-warning">Actualizar Tipo</button>
                            <a href="index.php?page=admin_gestion_tipos_series" class="btn btn-secondary">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success">Agregar Tipo</button>
                        <?php endif; ?>
                    </div>
                </form>

                <!-- Listado de tipos -->
                <ul class="list-group">
                    <?php foreach ($tipos as $t): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span><?= htmlspecialchars($t['nombre_tipo']) ?></span>
                            <span>
                                <a href="index.php?page=admin_gestion_tipos_series&editar_tipo=<?= $t['id_tipo'] ?>"
                                    class="btn btn-sm btn-warning me-1" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="index.php?page=admin_eliminar_tipo_danza&id=<?= $t['id_tipo'] ?>"
                                    class="btn btn-sm btn-danger" title="Eliminar"
                                    onclick="return confirm('¿Eliminar este tipo? Solo si no tiene series.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Panel derecho: Series por Tipo -->
            <div class="panel" style="flex: 2;">
                <h5><i class="bi bi-collection"></i> Series por Tipo de Danza</h5>
                <p class="text-muted">Crea y gestiona series para cada tipo de danza.</p>

                <div class="accordion" id="seriesAccordion">
                    <?php foreach ($tipos as $t): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?= $t['id_tipo'] ?>">
                                <button class="accordion-button <?= count($series_por_tipo[$t['id_tipo']]) > 0 ? '' : 'collapsed' ?>"
                                    type="button" data-bs-toggle="collapse"
                                    data-bs-target="#tipo<?= $t['id_tipo'] ?>"
                                    aria-expanded="<?= count($series_por_tipo[$t['id_tipo']]) > 0 ? 'true' : 'false' ?>">
                                    <?= ucfirst(str_replace('_', ' ', $t['nombre_tipo'])) ?>
                                </button>
                            </h2>
                            <div id="tipo<?= $t['id_tipo'] ?>" class="accordion-collapse collapse <?= count($series_por_tipo[$t['id_tipo']]) > 0 ? 'show' : '' ?>"
                                data-bs-parent="#seriesAccordion">
                                <div class="accordion-body">
                                    <?php if (count($series_por_tipo[$t['id_tipo']]) > 0): ?>
                                        <ul class="list-group mb-3">
                                            <?php foreach ($series_por_tipo[$t['id_tipo']] as $s): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <strong>SERIE <?= $s['numero_serie'] ?></strong> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                                        <?php if (!empty($s['concurso_nombre'])): ?>
                                                            <br><small class="text-muted">Concurso: <?= htmlspecialchars($s['concurso_nombre']) ?></small>
                                                        <?php endif; ?>
                                                    </span>
                                                    <span>
                                                        <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                            class="btn btn-sm btn-warning me-1" title="Editar">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                            class="btn btn-sm btn-danger" title="Eliminar"
                                                            onclick="return confirm('¿Eliminar esta serie?');">
                                                            <i class="bi bi-trash"></i>
                                                        </a>
                                                    </span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No hay series para este tipo.</p>
                                    <?php endif; ?>

                                    <!-- Botón para agregar nueva serie -->
                                    <a href="index.php?page=admin_crear_serie&id_tipo=<?= $t['id_tipo'] ?>"
                                        class="btn btn-sm btn-success">
                                        <i class="bi bi-plus-circle"></i> Nueva Serie
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
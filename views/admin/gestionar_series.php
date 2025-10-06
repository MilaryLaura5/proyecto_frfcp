<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión Unificada: Tipos y Series - FRFCP</title>
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

        .panel {
            background: white;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-collection me-2 text-primary"></i> Gestión Unificada: Tipos de Danza y Series</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Completa todos los campos.</div>
        <?php elseif ($error === 'tipo_vacio'): ?>
            <div class="alert alert-warning">⚠️ El nombre del tipo es obligatorio.</div>
        <?php elseif ($error === 'tipo_usado'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: tiene series asociadas.</div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Serie creada correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Serie actualizada.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Serie eliminada.</div>
        <?php elseif ($success == 'tipo_creado'): ?>
            <div class="alert alert-success">✅ Tipo de danza creado.</div>
        <?php elseif ($success == 'tipo_editado'): ?>
            <div class="alert alert-success">✅ Tipo de danza actualizado.</div>
        <?php elseif ($success == 'tipo_eliminado'): ?>
            <div class="alert alert-success">✅ Tipo de danza eliminado.</div>
        <?php endif; ?>

        <div class="row g-4">
            <!-- Panel izquierdo: Tipos de Danza -->
            <div class="col-md-5">
                <div class="panel p-4">
                    <h4><i class="bi bi-tags"></i> Tipos de Danza</h4>
                    <p class="text-muted">Gestiona los tipos oficiales de danza.</p>

                    <!-- Formulario: Crear o Editar Tipo -->
                    <?php if ($editando_tipo): ?>
                        <form method="POST" action="index.php?page=admin_actualizar_tipo_danza" class="mb-4 border-bottom pb-4">
                            <input type="hidden" name="id_tipo" value="<?= $tipo_edit['id_tipo'] ?>">
                            <div class="mb-3">
                                <label class="form-label"><strong>Nombre</strong></label>
                                <input type="text"
                                    class="form-control"
                                    name="nombre_tipo"
                                    value="<?= htmlspecialchars($tipo_edit['nombre_tipo']) ?>"
                                    required>
                            </div>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-warning">Actualizar Tipo</button>
                                <a href="index.php?page=admin_gestion_series" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="index.php?page=admin_crear_tipo_danza" class="mb-4 border-bottom pb-4">
                            <div class="mb-3">
                                <label class="form-label"><strong>Nombre del Tipo</strong></label>
                                <input type="text"
                                    class="form-control"
                                    name="nombre_tipo"
                                    placeholder="Ej: Traje Originario"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success">Agregar Tipo</button>
                        </form>
                    <?php endif; ?>

                    <!-- Listado de tipos -->
                    <ul class="list-group">
                        <?php foreach ($tipos as $t): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong><?= htmlspecialchars($t['nombre_tipo']) ?></strong>
                                <span>
                                    <a href="index.php?page=admin_editar_tipo_danza&id=<?= $t['id_tipo'] ?>"
                                        class="btn btn-sm btn-warning me-1" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="index.php?page=admin_eliminar_tipo_danza&id=<?= $t['id_tipo'] ?>"
                                        class="btn btn-sm btn-danger" title="Eliminar"
                                        onclick="return confirm('¿Eliminar? Solo si no tiene series.');">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Panel derecho: Series por Tipo -->
            <div class="col-md-7">
                <div class="panel p-4">
                    <h4><i class="bi bi-collection"></i> Series por Tipo de Danza</h4>
                    <p class="text-muted">Crea y gestiona series para cada tipo.</p>

                    <!-- Formulario: Crear/Editar Serie -->
                    <?php if ($editando_serie): ?>
                        <form method="POST" action="index.php?page=admin_actualizar_serie" class="mb-4 border-bottom pb-4">
                            <input type="hidden" name="id_serie" value="<?= $serie_edit['id_serie'] ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Número</strong></label>
                                    <input type="number"
                                        class="form-control"
                                        name="numero_serie"
                                        min="1" max="99"
                                        value="<?= $serie_edit['numero_serie'] ?>"
                                        required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label"><strong>Nombre</strong></label>
                                    <input type="text"
                                        class="form-control"
                                        name="nombre_serie"
                                        value="<?= htmlspecialchars($serie_edit['nombre_serie']) ?>"
                                        required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label"><strong>Tipo</strong></label>
                                <select class="form-control" name="id_tipo" required>
                                    <option value="">Selecciona un tipo</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?= $t['id_tipo'] ?>" <?= $serie_edit['id_tipo'] == $t['id_tipo'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($t['nombre_tipo']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="d-grid gap-2 d-md-flex mt-4">
                                <button type="submit" class="btn btn-warning">Actualizar Serie</button>
                                <a href="index.php?page=admin_gestion_series" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="index.php?page=admin_crear_serie_submit" class="mb-4 border-bottom pb-4">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label"><strong>Número</strong></label>
                                    <input type="number"
                                        class="form-control"
                                        name="numero_serie"
                                        min="1" max="99"
                                        required>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label"><strong>Nombre</strong></label>
                                    <input type="text"
                                        class="form-control"
                                        name="nombre_serie"
                                        placeholder="Ej: Carnavalescas Ligeras"
                                        required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label"><strong>Tipo</strong></label>
                                <select class="form-control" name="id_tipo" required>
                                    <option value="">Selecciona un tipo</option>
                                    <?php foreach ($tipos as $t): ?>
                                        <option value="<?= $t['id_tipo'] ?>"><?= htmlspecialchars($t['nombre_tipo']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-success mt-3">Crear Serie</button>
                        </form>
                    <?php endif; ?>

                    <!-- Acordeón de series por tipo -->
                    <div class="accordion" id="seriesAccordion">
                        <?php foreach ($tipos as $t): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $t['id_tipo'] ?>">
                                    <button class="accordion-button <?= $t['id_tipo'] > 1 ? 'collapsed' : '' ?>"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#tipo<?= $t['id_tipo'] ?>">
                                        <?= strtoupper(htmlspecialchars($t['nombre_tipo'])) ?>
                                    </button>
                                </h2>
                                <div id="tipo<?= $t['id_tipo'] ?>"
                                    class="accordion-collapse collapse <?= $t['id_tipo'] == 1 ? 'show' : '' ?>"
                                    data-bs-parent="#seriesAccordion">
                                    <div class="accordion-body">
                                        <?php
                                        $series_tipo = array_filter($series, fn($s) => $s['id_tipo'] == $t['id_tipo']);
                                        ?>
                                        <?php if (count($series_tipo) > 0): ?>
                                            <ul class="list-group">
                                                <?php foreach ($series_tipo as $s): ?>
                                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                                        <span>
                                                            <strong>SERIE <?= $s['numero_serie'] ?></strong>: <?= htmlspecialchars($s['nombre_serie']) ?>
                                                        </span>
                                                        <span>
                                                            <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                                class="btn btn-sm btn-warning me-1"><i class="bi bi-pencil"></i></a>
                                                            <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('¿Eliminar?');"><i class="bi bi-trash"></i></a>
                                                        </span>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else: ?>
                                            <p class="text-muted">No hay series.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
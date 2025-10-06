<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Conjuntos Globales - FRFCP</title>
    <!-- ✅ Corrección: espacios eliminados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 900px;
            margin: 80px auto;
        }

        .search-box {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }

        .item-conjunto {
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.1s ease;
            display: none;
        }

        .item-conjunto.mostrado {
            display: block;
            opacity: 1;
        }

        #mensajeNoResultados {
            display: none;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-collection me-2 text-primary"></i> Gestionar Conjuntos Globales</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Completa todos los campos.</div>
        <?php elseif ($error === 'duplicado'): ?>
            <div class="alert alert-danger">❌ Ya existe un conjunto con ese nombre en esta serie.</div>
        <?php elseif ($error === 'evaluado'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: el conjunto ya fue evaluado.</div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Conjunto creado correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Conjunto actualizado.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Conjunto eliminado.</div>
        <?php endif; ?>

        <!-- Formulario: Crear o Editar conjunto -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi <?= $editando ? 'bi-pencil' : 'bi-plus-circle' ?>"></i>
                    <?= $editando ? 'Editar Conjunto' : 'Nuevo Conjunto' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_conjunto_global' : 'admin_crear_conjunto_global_submit' ?>">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id_conjunto" value="<?= $conjunto_edit['id_conjunto'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                        <input type="text"
                            class="form-control"
                            name="nombre"
                            value="<?= $editando ? htmlspecialchars($conjunto_edit['nombre']) : '' ?>"
                            placeholder="Ej: Sikuris Huj'Maya"
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Serie</strong></label>
                        <select class="form-control" name="id_serie" required>
                            <option value="">Selecciona una serie</option>
                            <?php foreach ($series as $s): ?>
                                <option value="<?= $s['id_serie'] ?>"
                                    <?= ($editando && $conjunto_edit['id_serie'] == $s['id_serie']) ? 'selected' : '' ?>>
                                    SERIE <?= $s['numero_serie'] ?> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <?php if ($editando): ?>
                            <button type="submit" class="btn btn-warning">Actualizar Conjunto</button>
                            <a href="index.php?page=admin_gestionar_conjuntos_globales" class="btn btn-secondary">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success">Registrar Conjunto</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sección: Importar CSV -->
        <div class="card mt-5 shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-file-earmark-spreadsheet"></i> Importar Múltiples Conjuntos desde CSV</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Formato del archivo CSV:</p>
                <code>nombre,id_serie</code>
                <p><small>Ejemplo:</small></p>
                <code>Sikuris Huj'Maya,9</code>

                <form method="POST" action="index.php?page=admin_importar_conjuntos_csv_global" enctype="multipart/form-data" class="mt-3">
                    <div class="mb-3">
                        <label for="archivo_csv" class="form-label">Seleccionar archivo CSV</label>
                        <input type="file" class="form-control" name="archivo_csv" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Importar Conjuntos</button>
                </form>
            </div>
        </div>

        <!-- Listado de conjuntos -->
        <div class="card mt-5 shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i> Todos los Conjuntos Registrados</h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($conjuntos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nombre</th>
                                    <th>Serie</th>
                                    <th>Tipo Danza</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($conjuntos as $c): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
                                        <td>SERIE <?= $c['numero_serie'] ?? 'N/A' ?></td>
                                        <td><?= htmlspecialchars($c['nombre_tipo']) ?></td>
                                        <td>
                                            <a href="index.php?page=admin_editar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                class="btn btn-sm btn-warning me-1" title="Editar">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <a href="index.php?page=admin_eliminar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                class="btn btn-sm btn-danger"
                                                title="Eliminar"
                                                onclick="return confirm('¿Eliminar? Solo si no ha sido evaluado.');">
                                                <i class="bi bi-trash"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 m-0">No hay conjuntos registrados.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
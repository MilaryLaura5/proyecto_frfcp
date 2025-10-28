<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Conjuntos Globales - FRFCP</title>
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

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 200px);
        }

        .table-responsive {
            max-height: 60vh;
            overflow-y: auto;
        }

        .sticky-top th {
            position: sticky;
            top: 0;
            background-color: #fdf2f2;
            z-index: 10;
        }

        /* Botones de acción en rojo */
        .btn-primary-red {
            background: linear-gradient(to right, #c9184a, #800f2f);
            border: none;
            font-weight: 600;
        }

        .btn-primary-red:hover {
            background: linear-gradient(to right, #b01545, #6a0d25);
            transform: translateY(-1px);
        }

        .btn-warning-red {
            background-color: #ff9e9e;
            color: #5a0000;
            border: none;
            font-weight: 600;
        }

        .btn-warning-red:hover {
            background-color: #ff7f7f;
            color: #3a0000;
        }


        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-collection me-2"></i>
                Gestionar Conjuntos Globales
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Mensajes (sin cambios de color) -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-4 mt-3" role="alert">
                ⚠️ Completa todos los campos.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'duplicado'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                ❌ Ya existe un conjunto con ese nombre en esta serie.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($error === 'evaluado'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mt-3" role="alert">
                ❌ No se puede eliminar: el conjunto ya fue evaluado.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Conjunto creado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Conjunto actualizado.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mt-3" role="alert">
                ✅ Conjunto eliminado.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Fila superior: Formulario e Importar -->
        <div class="row g-4 mb-4">
            <!-- Formulario -->
            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5><i class="bi <?= $editando ? 'bi-pencil' : 'bi-plus-circle' ?>"></i>
                            <?= $editando ? 'Editar Conjunto' : 'Nuevo Conjunto' ?>
                        </h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_conjunto_global' : 'admin_crear_conjunto_global_submit' ?>">
                            <?php if ($editando): ?>
                                <input type="hidden" name="id_conjunto" value="<?= $conjunto_edit['id_conjunto'] ?>">
                            <?php endif; ?>

                            <div class="mb-3">
                                <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                                <input type="text"
                                    class="form-control form-control-lg"
                                    name="nombre"
                                    value="<?= $editando ? htmlspecialchars($conjunto_edit['nombre']) : '' ?>"
                                    placeholder="Ej: Sikuris Huj'Maya"
                                    required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Serie</strong></label>
                                <select class="form-control form-select-lg" name="id_serie" required>
                                    <option value="">Selecciona una serie</option>
                                    <?php foreach ($series as $s): ?>
                                        <option value="<?= $s['id_serie'] ?>"
                                            <?= ($editando && $conjunto_edit['id_serie'] == $s['id_serie']) ? 'selected' : '' ?>>
                                            SERIE <?= $s['numero_serie'] ?> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mt-auto">
                                <div class="d-grid gap-2 d-md-flex">
                                    <?php if ($editando): ?>
                                        <button type="submit" class="btn btn-warning btn-lg">Actualizar</button>
                                        <a href="index.php?page=admin_gestionar_conjuntos_globales" class="btn btn-secondary btn-lg">Cancelar</a>
                                    <?php else: ?>
                                        <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Registrar</button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Importar CSV -->
            <div class="col-md-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-file-earmark-spreadsheet"></i> Importar Múltiples Conjuntos desde CSV</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted">Formato del archivo CSV:</p>
                        <code>nombre,id_serie</code>
                        <p><small>Ejemplo:</small></p>
                        <code>Sikuris Huj'Maya,9</code>

                        <form method="POST" action="index.php?page=admin_importar_conjuntos_csv_global" enctype="multipart/form-data" class="mt-3">
                            <div class="mb-3">
                                <label for="archivo_csv" class="form-label">Seleccionar archivo CSV</label>
                                <input type="file" class="form-control form-control-lg" name="archivo_csv" accept=".csv" required>
                            </div>
                            <button type="submit" class="btn btn-primary-red btn-lg px-4 text-white mt-3 w-100">Importar Conjuntos</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de conjuntos -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Todos los Conjuntos Registrados
                        </h5>
                        <span class="badge bg-secondary"><?= count($conjuntos) ?> encontrados</span>
                    </div>
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <?php if (count($conjuntos) > 0): ?>
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="width: 5%;">#</th>
                                        <th style="width: 60%;">Nombre del Conjunto</th>
                                        <th class="text-center" style="width: 10%;">Serie</th>
                                        <th class="text-center" style="width: 15%;">Tipo Danza</th>
                                        <th class="text-center" style="width: 10%;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($conjuntos as $idx => $c): ?>
                                        <tr>
                                            <td class="text-center"><?= $idx + 1 ?></td>
                                            <td style="white-space: normal; word-break: break-word;">
                                                <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                            </td>
                                            <td class="text-center">SERIE <?= $c['numero_serie'] ?? 'N/A' ?></td>
                                            <td class="text-center"><?= htmlspecialchars($c['nombre_tipo']) ?></td>
                                            <td class="text-center">
                                                <div class="d-flex justify-content-center gap-1">
                                                    <a href="index.php?page=admin_editar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                        class="btn btn-sm btn-warning"
                                                        title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="index.php?page=admin_eliminar_conjunto_global&id=<?= $c['id_conjunto'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar? Solo si no ha sido evaluado.');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-emoji-frown display-4 text-muted"></i>
                                <p class="lead text-muted mt-3">No hay conjuntos registrados.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
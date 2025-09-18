<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

// Obtener id_concurso desde la URL
$id_concurso = $_GET['id_concurso'] ?? null;

if (!$id_concurso) {
    header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
    exit;
}

// Verificar que el concurso exista y no esté cerrado
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();

if (!$concurso || $concurso['estado'] === 'Cerrado') {
    header('Location: index.php?page=admin_gestion_concursos&error=invalido');
    exit;
}

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

// Cargar modelos
require_once __DIR__ . '/../../models/Serie.php';
require_once __DIR__ . '/../../models/Conjunto.php';

$series = Serie::listar(); // Para el select
$editando = false;
$conjunto_edit = null;

if (isset($_GET['id']) && $_GET['page'] === 'admin_editar_conjunto') {
    $conjunto_edit = Conjunto::obtenerPorId($_GET['id']);
    if ($conjunto_edit && $conjunto_edit['id_concurso'] == $id_concurso) {
        $editando = true;
    } else {
        header("Location: index.php?page=admin_gestion_conjuntos&id_concurso=$id_concurso&error=no_permiso");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre']) ?></title>
    <!-- Corrección: eliminar espacios al final -->
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
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>
                <i class="bi bi-people me-2 text-primary"></i>
                Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre']) ?>
            </h2>
            <a href="index.php?page=admin_gestion_concursos" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Completa todos los campos.</div>
        <?php elseif ($error === 'duplicado'): ?>
            <div class="alert alert-danger">❌ Ya existe un conjunto con ese número oficial en este concurso.</div>
        <?php elseif ($error === 'calificado'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: el conjunto ya fue evaluado.</div>
        <?php elseif ($error === 'no_concurso'): ?>
            <div class="alert alert-danger">❌ No se especificó un concurso válido.</div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Conjunto creado correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Conjunto actualizado.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Conjunto eliminado.</div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi <?= $editando ? 'bi-pencil-fill text-warning' : 'bi-plus-circle-fill text-success' ?>"></i>
                    <?= $editando ? 'Editar Conjunto' : 'Agregar Nuevo Conjunto' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_conjunto' : 'admin_crear_conjunto_submit' ?>">
                    <!-- Campo oculto: id_concurso -->
                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

                    <?php if ($editando): ?>
                        <input type="hidden" name="id_conjunto" value="<?= $conjunto_edit['id_conjunto'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                        <input type="text"
                            class="form-control"
                            name="nombre"
                            placeholder="Ej: Morenada San Martín"
                            value="<?= $editando ? htmlspecialchars($conjunto_edit['nombre']) : '' ?>"
                            required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Serie</strong></label>
                            <select class="form-control" name="id_serie" required>
                                <option value="">Selecciona una serie</option>
                                <?php foreach ($series as $s): ?>
                                    <option value="<?= $s['id_serie'] ?>">...</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><strong>Orden</strong></label>
                            <input type="number"
                                class="form-control"
                                name="orden_presentacion"
                                min="1"
                                value="<?= $editando ? $conjunto_edit['orden_presentacion'] : '' ?>"
                                required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label"><strong>N° Oficial</strong></label>
                            <input type="number"
                                class="form-control"
                                name="numero_oficial"
                                min="1"
                                value="<?= $editando ? $conjunto_edit['numero_oficial'] : '' ?>"
                                required>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex mt-4">
                        <?php if ($editando): ?>
                            <button type="submit" class="btn btn-warning">Actualizar Conjunto</button>
                            <a href="index.php?page=admin_gestion_conjuntos&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary">Cancelar</a>
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
                <h5><i class="bi bi-file-earmark-spreadsheet"></i> Importar Conjuntos desde CSV</h5>
            </div>
            <div class="card-body">

                <!-- Botón: Descargar plantilla -->
                <p class="text-muted mb-3">
                    <strong> Paso 1:</strong>
                    <a href="index.php?page=descargar_plantilla_conjuntos&id_concurso=<?= $id_concurso ?>"
                        class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-download"></i> Descargar plantilla CSV
                    </a>
                    <br>
                    <small>Rellena los datos y luego súbelos.</small>
                </p>

                <!-- Formulario: Subir CSV -->
                <p><strong>Paso 2:</strong> Sube el archivo lleno</p>
                <form method="POST" action="index.php?page=admin_importar_conjuntos_csv" enctype="multipart/form-data">
                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
                    <div class="mb-3">
                        <label for="archivo_csv" class="form-label">Archivo CSV completado</label>
                        <input type="file" class="form-control" name="archivo_csv" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Importar conjuntos</button>
                </form>
            </div>
        </div>

        <!-- Importar desde CSV -->
        <div class="card mt-5 shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-file-earmark-spreadsheet"></i> Importar Conjuntos desde CSV</h5>
            </div>
            <div class="card-body">
                <p class="text-muted">Formato del archivo CSV:</p>
                <code>nombre,id_serie,orden_presentacion,numero_oficial</code>
                <form method="POST" action="index.php?page=admin_importar_conjuntos_csv" enctype="multipart/form-data" class="mt-3">
                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
                    <div class="mb-3">
                        <label for="archivo_csv" class="form-label">Seleccionar archivo CSV</label>
                        <input type="file" class="form-control" name="archivo_csv" accept=".csv" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Importar CSV</button>
                </form>
            </div>
        </div>
        <br>
        <!-- Listado por serie -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i> Conjuntos por Serie</h5>
            </div>
            <div class="card-body">

                <?php foreach ($series as $s): ?>
                    <h6 class="text-primary mt-4">SERIE <?= $s['numero_serie'] ?> - <?= $s['nombre_serie'] ?></h6>
                    <?php
                    // Listar solo conjuntos de esta serie Y de este concurso
                    $conjuntos_serie = $pdo->prepare("
                        SELECT c.* FROM Conjunto c 
                        WHERE c.id_serie = ? AND c.id_concurso = ? 
                        ORDER BY c.orden_presentacion
                    ");
                    $conjuntos_serie->execute([$s['id_serie'], $id_concurso]);
                    $resultados = $conjuntos_serie->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php if (count($resultados) > 0): ?>
                        <ul class="list-group mb-3">
                            <?php foreach ($resultados as $c): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>
                                        <strong><?= $c['numero_oficial'] ?></strong> - <?= htmlspecialchars($c['nombre']) ?>
                                        <br><small class="text-muted">Orden: <?= $c['orden_presentacion'] ?></small>
                                    </span>
                                    <span>
                                        <a href="index.php?page=admin_editar_conjunto&id=<?= $c['id_conjunto'] ?>&id_concurso=<?= $id_concurso ?>"
                                            class="btn btn-sm btn-warning me-1" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="index.php?page=admin_eliminar_conjunto&id=<?= $c['id_conjunto'] ?>&id_concurso=<?= $id_concurso ?>"
                                            class="btn btn-sm btn-danger" title="Eliminar"
                                            onclick="return confirm('¿Eliminar este conjunto?');">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted mb-0">No hay conjuntos registrados en esta serie para este concurso.</p>
                    <?php endif; ?>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <!-- Corrección: eliminar espacio al final -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

// Obtener tipos de danza y series
require_once $_SERVER['DOCUMENT_ROOT'] . '/FRFCP/models/TipoDanza.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/FRFCP/models/Serie.php';

$tipos = TipoDanza::listar();
$series = Serie::listar();

$editando = false;
$serie_edit = null;

if (isset($_GET['id']) && $_GET['page'] === 'admin_editar_serie') {
    $serie_edit = Serie::obtenerPorId($_GET['id']);
    if ($serie_edit) {
        $editando = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Series - FRFCP Admin</title>
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
            <h2><i class="bi bi-collection me-2 text-primary"></i>
                <?= $editando ? 'Editar Serie' : 'Gestionar Series' ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Completa todos los campos.</div>
        <?php elseif ($success == '1'): ?>
            <div class="alert alert-success">✅ Serie creada correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Serie actualizada.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Serie eliminada.</div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi <?= $editando ? 'bi-pencil-fill text-warning' : 'bi-plus-circle-fill text-success' ?>"></i>
                    <?= $editando ? 'Editar Serie' : 'Crear Nueva Serie' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_serie' : 'admin_crear_serie_submit' ?>">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id_serie" value="<?= $serie_edit['id_serie'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label"><strong>Número</strong></label>
                            <input type="number"
                                class="form-control"
                                name="numero_serie"
                                min="1" max="99"
                                value="<?= $editando ? $serie_edit['numero_serie'] : '' ?>"
                                required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label"><strong>Nombre de la Serie</strong></label>
                            <input type="text"
                                class="form-control"
                                name="nombre_serie"
                                placeholder="Ej: Carnavalescas Ligeras"
                                value="<?= $editando ? htmlspecialchars($serie_edit['nombre_serie']) : '' ?>"
                                required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label"><strong>Tipo de Danza</strong></label>
                        <select class="form-control" name="id_tipo" required>
                            <option value="">Selecciona un tipo</option>
                            <?php foreach ($tipos as $t): ?>
                                <option value="<?= $t['id_tipo'] ?>"
                                    <?= ($editando && $serie_edit['id_tipo'] == $t['id_tipo']) ? 'selected' : '' ?>>
                                    <?= ucfirst(str_replace('_', ' ', $t['nombre_tipo'])) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="d-grid gap-2 d-md-flex mt-4">
                        <?php if ($editando): ?>
                            <button type="submit" class="btn btn-warning">Actualizar Serie</button>
                            <a href="index.php?page=admin_gestion_series" class="btn btn-secondary">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success">Registrar Serie</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado -->
        <!-- Listado organizado por tipo de danza -->
        <!-- Listado con acordeones por tipo de danza -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i> Series por Tipo de Danza</h5>
            </div>
            <div class="card-body">

                <div class="accordion" id="seriesAccordion">

                    <!-- TRAJE ORIGINARIO -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOriginarios">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#originarios" aria-expanded="true" aria-controls="originarios">
                                🎭 TRAJES ORIGINARIOS
                            </button>
                        </h2>
                        <div id="originarios" class="accordion-collapse collapse show" data-bs-parent="#seriesAccordion">
                            <div class="accordion-body">
                                <?php $series = Serie::listarPorTipo(1); ?>
                                <?php if (count($series) > 0): ?>
                                    <ul class="list-group">
                                        <?php foreach ($series as $s): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong><?= $s['numero_serie'] ?></strong> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                                </span>
                                                <span>
                                                    <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-warning me-1"
                                                        title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar esta serie?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No hay series registradas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- TRAJES DE LUCES -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingLuces">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#luces" aria-expanded="false" aria-controls="luces">
                                ✨ TRAJES DE LUCES
                            </button>
                        </h2>
                        <div id="luces" class="accordion-collapse collapse" data-bs-parent="#seriesAccordion">
                            <div class="accordion-body">
                                <?php $series = Serie::listarPorTipo(2); ?>
                                <?php if (count($series) > 0): ?>
                                    <ul class="list-group">
                                        <?php foreach ($series as $s): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong><?= $s['numero_serie'] ?></strong> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                                </span>
                                                <span>
                                                    <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-warning me-1"
                                                        title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar esta serie?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No hay series registradas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- SIKURIS -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingSikuris">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sikuris" aria-expanded="false" aria-controls="sikuris">
                                🎶 SIKURIS
                            </button>
                        </h2>
                        <div id="sikuris" class="accordion-collapse collapse" data-bs-parent="#seriesAccordion">
                            <div class="accordion-body">
                                <?php $series = Serie::listarPorTipo(3); ?>
                                <?php if (count($series) > 0): ?>
                                    <ul class="list-group">
                                        <?php foreach ($series as $s): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <span>
                                                    <strong><?= $s['numero_serie'] ?></strong> - <?= htmlspecialchars($s['nombre_serie']) ?>
                                                </span>
                                                <span>
                                                    <a href="index.php?page=admin_editar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-warning me-1"
                                                        title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <a href="index.php?page=admin_eliminar_serie&id=<?= $s['id_serie'] ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar"
                                                        onclick="return confirm('¿Eliminar esta serie?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="text-muted mb-0">No hay series registradas.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                </div> <!-- Fin del acordeón -->

            </div>
        </div>



    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<!-- views/admin/gestion_concursos.php -->
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;
$editando = false;
$concurso_a_editar = null;

if (isset($_GET['id']) && $page === 'admin_editar_concurso') {
    $concurso_a_editar = Concurso::obtenerPorId($_GET['id']);
    if ($concurso_a_editar) {
        $editando = true;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Concursos - FRFCP Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .container-main { max-width: 900px; margin: 80px auto; }
        .alert { border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-calendar-event me-2 text-primary"></i>
                <?= $editando ? 'Editar Concurso' : 'Gestionar Concursos' ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Todos los campos son obligatorios.</div>
        <?php elseif ($error === 'fechas'): ?>
            <div class="alert alert-danger">❌ Fecha inicio debe ser anterior a fin.</div>
        <?php elseif ($error === 'tiene_evaluaciones'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: ya tiene evaluaciones registradas.</div>
        <?php endif; ?>

        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Concurso creado correctamente.</div>
        <?php elseif ($success == 'editado'): ?>
            <div class="alert alert-success">✅ Concurso actualizado.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Concurso eliminado.</div>
        <?php endif; ?>

        <!-- Formulario: Crear o Editar -->
        <div class="card mb-5 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi <?= $editando ? 'bi-pencil' : 'bi-plus-circle' ?> text-<?= $editando ? 'warning' : 'success' ?>"></i>
                    <?= $editando ? 'Editar Concurso' : 'Crear Nuevo Concurso' ?>
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=<?= $editando ? 'admin_actualizar_concurso' : 'admin_crear_concurso_submit' ?>">
                    <?php if ($editando): ?>
                        <input type="hidden" name="id_concurso" value="<?= $concurso_a_editar['id_concurso'] ?>">
                    <?php endif; ?>

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre</strong></label>
                        <input type="text"
                               class="form-control"
                               name="nombre"
                               value="<?= htmlspecialchars($editando ? $concurso_a_editar['nombre'] : '') ?>"
                               required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Inicio</strong></label>
                            <input type="datetime-local"
                                   class="form-control"
                                   name="fecha_inicio"
                                   value="<?= $editando ? date('Y-m-d\TH:i', strtotime($concurso_a_editar['fecha_inicio'])) : '' ?>"
                                   required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label"><strong>Fin</strong></label>
                            <input type="datetime-local"
                                   class="form-control"
                                   name="fecha_fin"
                                   value="<?= $editando ? date('Y-m-d\TH:i', strtotime($concurso_a_editar['fecha_fin'])) : '' ?>"
                                   required>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex">
                        <?php if ($editando): ?>
                            <button type="submit" class="btn btn-warning">Actualizar Concurso</button>
                            <a href="index.php?page=admin_gestion_concursos" class="btn btn-secondary">Cancelar</a>
                        <?php else: ?>
                            <button type="submit" class="btn btn-success">Registrar Concurso</button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        </div>

        <!-- Listado con Editar/Eliminar -->
        <div class="card shadow-sm">
            <div class="card-header bg-white"><h5><i class="bi bi-list-ul"></i> Concursos Registrados</h5></div>
            <div class="card-body p-0">
                <?php $concursos = Concurso::listar(); ?>
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
                                        <td><?= htmlspecialchars($c['nombre']) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_inicio'])) ?></td>
                                        <td><?= date('d/m/Y H:i', strtotime($c['fecha_fin'])) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $c['estado'] == 'Activo' ? 'success' : ($c['estado'] == 'Cerrado' ? 'secondary' : 'warning') ?>">
                                                <?= ucfirst($c['estado']) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <a href="index.php?page=admin_editar_concurso&id=<?= $c['id_concurso'] ?>" class="btn btn-sm btn-warning">✏️</a>
                                            <a href="index.php?page=admin_eliminar_concurso&id=<?= $c['id_concurso'] ?>" 
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('¿Seguro de eliminar este concurso?')">🗑️</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4">No hay concursos registrados aún.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
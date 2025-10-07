<?php
// Esta variable debe venir del controlador
$id_concurso = $_GET['id_concurso'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Criterios - FRFCP</title>
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
            padding: 20px;
        }

        .list-group-item {
            cursor: pointer;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>

<body>
    <div class="container-main">

        <!-- ✅ Mensajes de éxito/error -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'asignado'): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>¡Éxito!</strong> El criterio fue asignado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <?php switch ($_GET['error']):
                case 'dato_invalido': ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Error:</strong> Datos inválidos.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
                <?php
                case 'no_guardado': ?>
                    <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Advertencia:</strong> No se pudo guardar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
                <?php
                case 'db': ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-database"></i>
                        <strong>Error de base de datos.</strong> Contacta al administrador.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
            <?php endswitch; ?>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-list-task me-2 text-primary"></i> Gestión de Criterios y Concursos</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="row g-4">
            <!-- Panel izquierdo: Criterios -->
            <div class="col-md-5">
                <div class="panel">
                    <h5><i class="bi bi-tags"></i> Criterios Generales</h5>
                    <p class="text-muted">Crea y gestiona criterios globales.</p>

                    <!-- Formulario: Nuevo criterio -->
                    <form method="POST" action="index.php?page=admin_gestionar_criterios" class="mb-4">
                        <div class="mb-3">
                            <label class="form-label"><strong>Nombre del Criterio</strong></label>
                            <input type="text"
                                class="form-control"
                                name="nombre"
                                placeholder="Ej: Coreografía"
                                required>
                        </div>
                        <button type="submit" class="btn btn-success">Agregar Criterio</button>
                    </form>

                    <!-- Lista de criterios -->
                    <ul class="list-group">
                        <?php foreach ($criterios as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center"
                                onclick="seleccionarCriterio(
                                    <?= $c['id_criterio'] ?>,
                                    '<?= addslashes(htmlspecialchars($c['nombre'])) ?>')">
                                <?= htmlspecialchars($c['nombre']) ?>
                                <button class="btn btn-sm btn-outline-primary">Usar</button>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Panel derecho superior: Asignar criterio -->
            <div class="col-md-7">
                <div class="panel">
                    <h5><i class="bi bi-calendar-event"></i> Asignar al Concurso</h5>
                    <p class="text-muted">Selecciona un concurso para asignar el puntaje máximo del criterio.</p>

                    <div id="mensajeInicial" class="alert alert-info">
                        Selecciona un criterio para asignarlo a un concurso
                    </div>

                    <div id="formularioAsignacion" style="display: none;">
                        <div class="alert alert-light">
                            <strong>Criterio seleccionado:</strong> <span id="nombreCriterio"></span>
                        </div>
                        <form method="POST" action="index.php?page=admin_guardar_criterio_concurso">
                            <input type="hidden" name="id_criterio" id="id_criterio_input">

                            <div class="mb-3">
                                <label class="form-label"><strong>Concurso</strong></label>
                                <select class="form-control" name="id_concurso" required>
                                    <option value="">Selecciona un concurso</option>
                                    <?php foreach ($concursos as $c): ?>
                                        <option value="<?= $c['id_concurso'] ?>">
                                            <?= htmlspecialchars($c['nombre']) ?> (<?= ucfirst($c['estado']) ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><strong>Puntaje Máximo</strong></label>
                                <input type="number"
                                    class="form-control"
                                    name="puntaje_maximo"
                                    step="0.01"
                                    min="0"
                                    max="100"
                                    placeholder="Ej: 10.00"
                                    required>
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar Asignación</button>
                        </form>
                    </div>
                </div>

                <!-- Panel derecho inferior: Gestionar concursos -->
                <div class="panel mt-4">
                    <h5><i class="bi bi-calendar-check"></i> Gestionar Concurso</h5>
                    <p class="text-muted">Selecciona un concurso para ver sus criterios asignados.</p>

                    <div class="mb-3">
                        <label class="form-label"><strong>Concurso</strong></label>
                        <select class="form-control" id="selectConcurso" onchange="cambiarConcurso()">
                            <option value="">Selecciona un concurso</option>
                            <?php foreach ($concursos as $c): ?>
                                <option value="<?= $c['id_concurso'] ?>" <?= $id_concurso == $c['id_concurso'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['nombre']) ?> (<?= ucfirst($c['estado']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div id="zonaAsignacion">
                        <?php if ($id_concurso && !empty($criterios_asignados)): ?>
                            <div class="alert alert-light">
                                <strong>Criterios asignados a este concurso:</strong>
                            </div>
                            <ul class="list-group mb-3">
                                <?php foreach ($criterios_asignados as $ca): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong><?= htmlspecialchars($ca['nombre_criterio']) ?></strong><br>
                                            <small>Puntaje máximo: <strong><?= number_format($ca['puntaje_maximo'], 2) ?></strong> puntos</small>
                                        </div>
                                        <a href="#" class="btn btn-sm btn-danger"
                                            onclick="eliminarCriterio(<?= $ca['id_criterio'] ?>, <?= $id_concurso ?>)">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php elseif ($id_concurso): ?>
                            <div class="alert alert-info">No hay criterios asignados aún.</div>
                        <?php else: ?>
                            <div class="alert alert-info">Selecciona un concurso para gestionar sus criterios.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function seleccionarCriterio(id, nombre) {
            document.getElementById('id_criterio_input').value = id;
            document.getElementById('nombreCriterio').textContent = nombre;
            document.getElementById('mensajeInicial').style.display = 'none';
            document.getElementById('formularioAsignacion').style.display = 'block';
        }

        function cambiarConcurso() {
            const concursoId = document.getElementById('selectConcurso').value;
            if (concursoId) {
                window.location.href = 'index.php?page=admin_gestionar_criterios&id_concurso=' + concursoId;
            } else {
                window.location.href = 'index.php?page=admin_gestionar_criterios';
            }
        }

        function eliminarCriterio(idCriterio, idConcurso) {
            if (confirm('¿Eliminar este criterio del concurso?')) {
                window.location.href = 'index.php?page=admin_eliminar_criterio_concurso&id=' + idCriterio + '&id_concurso=' + idConcurso;
            }
        }
    </script>

    <!-- ✅ Corrección: espacio eliminado -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
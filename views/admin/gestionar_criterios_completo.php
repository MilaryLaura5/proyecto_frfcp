<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Criterios - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            color: #0056b3;
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

        .list-group-item {
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .list-group-item:hover {
            background-color: #f8f9fa;
        }

        /* Scroll en panel izquierdo */
        .criterios-scroll {
            max-height: 500px;
            overflow-y: auto;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .row>div {
                margin-bottom: 1rem;
            }

            .criterios-scroll {
                max-height: 300px;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado con ancho completo -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-list-task me-2 text-primary"></i> Gestión de Criterios y Concursos
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Contenido principal con ancho completo -->
    <div class="container-fluid px-4">

        <!-- ✅ Mensajes de éxito/error -->
        <?php if (isset($_GET['success']) && $_GET['success'] === 'asignado'): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <i class="bi bi-check-circle"></i>
                <strong>¡Éxito!</strong> El criterio fue asignado correctamente.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <?php switch ($_GET['error']):
                case 'dato_invalido': ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle"></i>
                        <strong>Error:</strong> Datos inválidos.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
                <?php
                case 'no_guardado': ?>
                    <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">
                        <i class="bi bi-exclamation-circle"></i>
                        <strong>Advertencia:</strong> No se pudo guardar.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
                <?php
                case 'db': ?>
                    <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">
                        <i class="bi bi-database"></i>
                        <strong>Error de base de datos.</strong> Contacta al administrador.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php break; ?>
            <?php endswitch; ?>
        <?php endif; ?>

        <div class="row g-4">

            <!-- Panel izquierdo: Criterios Generales con scroll -->
            <div class="col-md-5">
                <div class="card shadow-sm h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-tags"></i> Criterios Generales</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted">Crea y gestiona criterios globales.</p>

                        <!-- Formulario: Nuevo criterio -->
                        <form method="POST" action="index.php?page=admin_gestionar_criterios" class="mb-3">
                            <div class="mb-3">
                                <label class="form-label"><strong>Nombre del Criterio</strong></label>
                                <input type="text"
                                    class="form-control form-control-lg"
                                    name="nombre"
                                    placeholder="Ej: Coreografía"
                                    required>
                            </div>
                            <button type="submit" class="btn btn-success btn-lg w-100">Agregar Criterio</button>
                        </form>

                        <!-- Lista de criterios con scroll -->
                        <ul class="list-group flex-fill criterios-scroll mt-3">
                            <?php foreach ($criterios as $c): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox"
                                            value="<?= $c['id_criterio'] ?>"
                                            id="criterio_<?= $c['id_criterio'] ?>">
                                        <label class="form-check-label" for="criterio_<?= $c['id_criterio'] ?>">
                                            <?= htmlspecialchars($c['nombre']) ?>
                                        </label>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Panel derecho: Asignación y Gestión unificadas -->
            <div class="col-md-7">
                <div class="card shadow-sm h-100">
                    <div class="card-header">
                        <h5><i class="bi bi-calendar-event"></i> Asignar Criterios al Concurso</h5>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <!-- Selección de concurso -->
                        <div class="mb-3">
                            <label class="form-label"><strong>Concurso</strong></label>
                            <select class="form-control form-select-lg" id="selectConcurso" onchange="cambiarConcurso()">
                                <option value="">Selecciona un concurso</option>
                                <?php foreach ($concursos as $c): ?>
                                    <option value="<?= $c['id_concurso'] ?>" <?= $id_concurso == $c['id_concurso'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre']) ?> (<?= ucfirst($c['estado']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <?php if ($id_concurso): ?>
                            <!-- Formulario dinámico -->
                            <form method="POST" action="index.php?page=admin_guardar_criterios_concurso" class="mb-4" id="formAsignacion">
                                <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
                                <div class="alert alert-light">
                                    <strong>Selecciona criterios del panel izquierdo para asignarlos:</strong>
                                </div>

                                <!-- Contenedor dinámico -->
                                <div id="criteriosAsignados" class="mb-3">
                                    <p class="text-muted">Selecciona criterios del panel izquierdo.</p>
                                </div>

                                <button type="submit" class="btn btn-primary btn-lg w-100" id="btnAsignar" disabled>
                                    Asignar Criterios Seleccionados
                                </button>
                            </form>

                            <!-- Criterios ya asignados -->
                            <div class="mt-4">
                                <?php if (!empty($criterios_asignados)): ?>
                                    <div class="alert alert-light">
                                        <strong>Criterios ya asignados a este concurso:</strong>
                                    </div>
                                    <ul class="list-group">
                                        <?php foreach ($criterios_asignados as $ca): ?>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                <div>
                                                    <strong><?= htmlspecialchars($ca['nombre']) ?></strong><br>
                                                    <small>Puntaje máximo: <strong><?= number_format($ca['puntaje_maximo'], 2) ?></strong> puntos</small>
                                                </div>
                                                <a href="#" class="btn btn-sm btn-danger"
                                                    onclick="eliminarCriterio(<?= $ca['id_criterio'] ?>, <?= $id_concurso ?>)" <i class="bi bi-trash"></i>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <div class="alert alert-info text-center py-3">
                                        No hay criterios asignados aún.
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info text-center py-3 mt-4">
                                Selecciona un concurso para asignar criterios.
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let criteriosSeleccionados = {};

        function actualizarVistaCriterios() {
            const contenedor = document.getElementById('criteriosAsignados');
            const btn = document.getElementById('btnAsignar');

            if (Object.keys(criteriosSeleccionados).length === 0) {
                contenedor.innerHTML = '<p class="text-muted">Selecciona criterios del panel izquierdo.</p>';
                btn.disabled = true;
                return;
            }

            let html = '<div class="row g-2">';
            for (const id in criteriosSeleccionados) {
                const c = criteriosSeleccionados[id];
                html += `
                    <div class="col-12" id="fila_${id}">
                        <div class="input-group">
                            <span class="input-group-text flex-grow-1">${c.nombre}</span>
                            <input type="number" 
                                   class="form-control" 
                                   name="puntajes[${id}]" 
                                   value="${c.puntaje || ''}" 
                                   step="0.01" min="0.01" max="100"
                                   placeholder="Puntaje" 
                                   required
                                   oninput="criteriosSeleccionados[${id}].puntaje = this.value">
                            <button class="btn btn-outline-danger" type="button" onclick="deseleccionarCriterio(${id})">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                `;
            }
            html += '</div>';
            contenedor.innerHTML = html;
            btn.disabled = false;
        }

        function deseleccionarCriterio(id) {
            delete criteriosSeleccionados[id];
            // Desmarcar checkbox
            const checkbox = document.querySelector(`input[value="${id}"]`);
            if (checkbox) checkbox.checked = false;
            actualizarVistaCriterios();
        }

        // Inicializar checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.form-check-input').forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const id = this.value;
                    const label = this.nextElementSibling.textContent.trim();
                    if (this.checked) {
                        criteriosSeleccionados[id] = {
                            id,
                            nombre: label,
                            puntaje: ''
                        };
                    } else {
                        delete criteriosSeleccionados[id];
                    }
                    actualizarVistaCriterios();
                });
            });
        });

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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
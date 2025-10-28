<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre'], ENT_QUOTES, 'UTF-8') ?></title>
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

        .search-box {
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 10px;
            max-height: 200px;
            overflow-y: auto;
        }

        .search-box .list-group-item {
            border: none;
            border-bottom: 1px solid #f5eaea;
        }

        .search-box .list-group-item:last-child {
            border-bottom: none;
        }

        .item-conjunto {
            display: none;
        }

        .item-conjunto.visible {
            display: block !important;
            opacity: 1;
            pointer-events: auto;
        }

        .item-conjunto:not(.visible) {
            padding: 0 !important;
            margin: 0 !important;
            border: none !important;
            height: 0 !important;
            overflow: hidden !important;
            border-bottom: none !important;
        }

        #mensajeNoResultados {
            display: none;
        }

        .table thead th {
            background-color: #fdf2f2;
            color: #495057;
            font-weight: 600;
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-people me-2"></i>
                Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Mensajes (sin cambios de color) -->
        <?php if ($error === 'vacios'): ?>
            <div class="alert alert-warning">⚠️ Completa todos los campos.</div>
        <?php elseif ($error === 'duplicado'): ?>
            <div class="alert alert-danger">❌ Ya existe un conjunto con ese orden en este concurso.</div>
        <?php elseif ($error === 'calificado'): ?>
            <div class="alert alert-danger">❌ No se puede eliminar: el conjunto ya fue evaluado.</div>
        <?php endif; ?>

        <?php if ($success == 'asignado'): ?>
            <div class="alert alert-success">✅ Conjunto asignado correctamente.</div>
        <?php elseif ($success == 'eliminado'): ?>
            <div class="alert alert-success">✅ Conjunto eliminado del concurso.</div>
        <?php endif; ?>

        <?php if ($success == 'orden_editado'): ?>
            <div class="alert alert-success">✅ Número de orden actualizado correctamente.</div>
        <?php endif; ?>

        <!-- Buscar Conjunto -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white">
                        <h5><i class="bi bi-search"></i> Buscar Conjunto para Agregar</h5>
                    </div>
                    <div class="card-body">
                        <input type="text"
                            id="buscadorConjuntos"
                            class="form-control form-control-lg mb-3"
                            placeholder="Escribe para buscar..."
                            onkeyup="filtrarConjuntos()">

                        <div class="search-box">
                            <ul id="listaConjuntos" class="list-group">
                                <?php foreach ($conjuntos_globales as $c): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center item-conjunto"
                                        data-nombre="<?= strtolower(normalizarTexto($c['nombre'])) ?>"
                                        onclick="seleccionarConjunto(
                                            <?= $c['id_conjunto'] ?>,
                                            '<?= addslashes(htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8')) ?>',
                                            'SERIE <?= $c['numero_serie'] ?> - <?= addslashes(htmlspecialchars($c['nombre_serie'], ENT_QUOTES, 'UTF-8')) ?>'
                                        )"
                                        style="cursor: pointer;">
                                        <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                        <small class="text-muted">SERIE <?= $c['numero_serie'] ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <p id="mensajeNoResultados" class="text-muted text-center mt-3" style="display: none;">
                                No se encontraron conjuntos que coincidan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario de asignación -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="formularioAsignacion" class="card shadow-sm" style="display: none;">
                    <div class="card-header bg-white">
                        <h5><i class="bi bi-plus-circle"></i> Asignar Conjunto al Concurso</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="index.php?page=admin_asignar_conjunto_a_concurso">
                            <input type="hidden" name="id_conjunto" id="id_conjunto_input">
                            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                                    <input type="text" class="form-control" id="nombre_mostrado" readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><strong>Serie</strong></label>
                                    <input type="text" class="form-control" id="serie_mostrada" readonly>
                                </div>
                            </div>

                            <div class="mb-3 mt-3">
                                <label class="form-label"><strong>Orden / N° Oficial</strong></label>
                                <input type="number" class="form-control" name="orden_presentacion" min="1" required>
                                <small class="text-muted">Este será su número oficial en el concurso.</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex">
                                <button type="submit" class="btn btn-success">Agregar al Concurso</button>
                                <button type="button" class="btn btn-secondary" onclick="cancelar()">Cancelar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Listado de conjuntos asignados -->
        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-list-ul"></i> Conjuntos Asignados al Concurso
                        </h5>
                        <span class="badge bg-primary"><?= count($participaciones) ?> asignados</span>
                    </div>
                    <div class="card-body p-0">
                        <?php if (count($participaciones) > 0): ?>
                            <div class="table-responsive" style="max-height: 50vh; overflow-y: auto;">
                                <table class="table table-hover mb-0">
                                    <thead class="table-light sticky-top" style="top: 0; z-index: 10; background-color: #fdf2f2;">
                                        <tr>
                                            <th class="text-center" style="width: 10%;">N°</th>
                                            <th style="width: 40%;">Nombre</th>
                                            <th style="width: 20%;">Serie</th>
                                            <th style="width: 20%;">Tipo Danza</th>
                                            <th class="text-center" style="width: 10%;">Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($participaciones as $p): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <form method="POST" action="index.php?page=admin_editar_orden_participacion" style="display:inline;">
                                                        <input type="hidden" name="id_participacion" value="<?= $p['id_participacion'] ?>">
                                                        <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
                                                        <input
                                                            type="number"
                                                            name="orden_presentacion"
                                                            value="<?= $p['orden_presentacion'] ?>"
                                                            min="1"
                                                            class="form-control form-control-sm d-inline"
                                                            style="width: 70px;"
                                                            required>
                                                        <button type="submit" class="btn btn-sm btn-outline-primary ms-1" title="Guardar nuevo orden">
                                                            <i class="bi bi-check"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td><?= htmlspecialchars($p['nombre_conjunto'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($p['nombre_serie'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($p['nombre_tipo'], ENT_QUOTES, 'UTF-8') ?></td>
                                                <td class="text-center">
                                                    <a href="index.php?page=admin_eliminar_participacion&id=<?= $p['id_participacion'] ?>&id_concurso=<?= $id_concurso ?>"
                                                        class="btn btn-sm btn-danger"
                                                        title="Eliminar del concurso"
                                                        onclick="return confirm('¿Eliminar este conjunto del concurso?');">
                                                        <i class="bi bi-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="bi bi-emoji-frown display-4 text-muted"></i>
                                <p class="lead text-muted mt-3">No hay conjuntos asignados a este concurso.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function seleccionarConjunto(id, nombre, serie) {
            document.getElementById('id_conjunto_input').value = id;
            document.getElementById('nombre_mostrado').value = nombre;
            document.getElementById('serie_mostrada').value = serie;
            document.getElementById('formularioAsignacion').style.display = 'block';
        }

        function cancelar() {
            document.getElementById('formularioAsignacion').style.display = 'none';
        }

        function filtrarConjuntos() {
            const input = document.getElementById('buscadorConjuntos');
            const filtroOriginal = input?.value.trim() || '';

            if (filtroOriginal === '') {
                document.querySelectorAll('.item-conjunto').forEach(el => el.classList.remove('visible'));
                document.getElementById('mensajeNoResultados').style.display = 'none';
                return;
            }

            const filtro = filtroOriginal
                .normalize('NFD')
                .replace(/[\u0300-\u036f]/g, '')
                .toLowerCase();

            const items = document.querySelectorAll('.item-conjunto');
            let algunoVisible = false;

            items.forEach(item => {
                const nombreNormalizado = item.getAttribute('data-nombre');
                if (nombreNormalizado && nombreNormalizado.includes(filtro)) {
                    item.classList.add('visible');
                    algunoVisible = true;
                } else {
                    item.classList.remove('visible');
                }
            });

            document.getElementById('mensajeNoResultados').style.display = algunoVisible ? 'none' : 'block';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const input = document.getElementById('buscadorConjuntos');
            if (input) input.focus();
        });
    </script>
</body>

</html>
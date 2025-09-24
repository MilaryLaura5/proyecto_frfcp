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
require_once __DIR__ . '/../../models/Conjunto.php';
require_once __DIR__ . '/../../models/ParticipacionConjunto.php';

// Conjuntos globales (todos los registrados)
$conjuntos_globales = Conjunto::listar();

// Ordenar alfabéticamente por nombre
usort($conjuntos_globales, function ($a, $b) {
    return strcasecmp($a['nombre'], $b['nombre']);
});

// Participaciones en este concurso
$participaciones = ParticipacionConjunto::listarPorConcurso($id_concurso);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre'], ENT_QUOTES, 'UTF-8') ?></title>
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
            <h2>
                <i class="bi bi-people me-2 text-primary"></i>
                Gestionar Conjuntos - <?= htmlspecialchars($concurso['nombre'], ENT_QUOTES, 'UTF-8') ?>
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Mensajes -->
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

        <!-- Buscar Conjunto para Agregar -->
        <div class="card mb-4 shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-search"></i> Buscar Conjunto para Agregar</h5>
            </div>
            <div class="card-body">
                <input type="text"
                    id="buscadorConjuntos"
                    class="form-control mb-3"
                    placeholder="Escribe para buscar..."
                    onkeyup="filtrarConjuntos()">

                <div class="search-box">
                    <ul id="listaConjuntos" class="list-group">
                        <?php foreach ($conjuntos_globales as $c): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center item-conjunto"
                                data-nombre="<?= strtolower(normalizarTexto($c['nombre'])) ?>"
                                onclick="seleccionarConjunto(<?= $c['id_conjunto'] ?>, '<?= addslashes(htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8')) ?>', 'SERIE <?= $c['numero_serie'] ?> - <?= addslashes(htmlspecialchars($c['nombre_serie'], ENT_QUOTES, 'UTF-8')) ?>')"
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

        <!-- Formulario: Asignar a concurso -->
        <div id="formularioAsignacion" class="card mb-4 shadow-sm" style="display: none;">
            <div class="card-header bg-white">
                <h5><i class="bi bi-plus-circle"></i> Asignar Conjunto al Concurso</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="index.php?page=admin_asignar_conjunto_a_concurso">
                    <input type="hidden" name="id_conjunto" id="id_conjunto_input">
                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

                    <div class="mb-3">
                        <label class="form-label"><strong>Nombre del Conjunto</strong></label>
                        <input type="text" class="form-control" id="nombre_mostrado" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Serie</strong></label>
                        <input type="text" class="form-control" id="serie_mostrada" readonly>
                    </div>

                    <div class="mb-3">
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

        <!-- Listado de participaciones -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i> Conjuntos Asignados al Concurso</h5>
            </div>
            <div class="card-body">
                <?php if (count($participaciones) > 0): ?>
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>N°</th>
                                <th>Nombre</th>
                                <th>Serie</th>
                                <th>Tipo Danza</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($participaciones as $p): ?>
                                <tr>
                                    <td><strong><?= $p['orden_presentacion'] ?></strong></td>
                                    <td><?= htmlspecialchars($p['nombre_conjunto'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($p['nombre_serie'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td><?= htmlspecialchars($p['nombre_tipo'], ENT_QUOTES, 'UTF-8') ?></td>
                                    <td>
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
                <?php else: ?>
                    <p class="text-muted">No hay conjuntos asignados a este concurso.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Corrección: eliminar espacio al final -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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

        // Filtrar conjuntos mientras escribes
        function filtrarConjuntos() {
            const input = document.getElementById('buscadorConjuntos');
            const filtroOriginal = input.value.trim();

            // Normalizar texto de búsqueda (quitar acentos)
            const filtro = filtroOriginal
                .normalize('NFD') // Descomponer acentos
                .replace(/[\u0300-\u036f]/g, '') // Eliminar marcas diacríticas
                .toLowerCase();

            const items = document.getElementsByClassName('item-conjunto');
            let algunoVisible = false;

            if (filtro === '') {
                for (let i = 0; i < items.length; i++) {
                    items[i].style.display = 'none';
                }
                document.getElementById('mensajeNoResultados').style.display = 'none';
                return;
            }

            for (let i = 0; i < items.length; i++) {
                const nombreNormalizado = items[i].getAttribute('data-nombre');
                // En filtrarConjuntos()
                if (nombreNormalizado && nombreNormalizado.includes(filtro)) {
                    items[i].style.display = 'block';
                    items[i].style.opacity = '1';
                    items[i].style.pointerEvents = 'auto';
                    algunoVisible = true;
                } else {
                    items[i].style.display = 'none';
                    items[i].style.opacity = '0';
                    items[i].style.pointerEvents = 'none';
                }
            }

            document.getElementById('mensajeNoResultados').style.display = !algunoVisible ? 'block' : 'none';
        }
    </script>
</body>

</html>
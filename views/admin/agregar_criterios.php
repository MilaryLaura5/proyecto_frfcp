<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();

$id_concurso = $_GET['id_concurso'] ?? null;

if (!$id_concurso) {
    die("Concurso no especificado.");
}

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();

if (!$concurso) {
    die("Concurso no encontrado.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Agregar Criterios - FRFCP</title>
    <!-- ✅ Corrección: espacio eliminado -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2><i class="bi bi-list-task"></i> Criterios para el concurso: <?= htmlspecialchars($concurso['nombre']) ?></h2>

        <!-- Asignar criterio existente al concurso -->
        <form method="POST" action="index.php?page=admin_guardar_criterio_concurso">
            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

            <div class="mb-3">
                <label><strong>Seleccionar Criterio Existente</strong></label>
                <?php
                // Obtener criterios disponibles (no asignados aún)
                $stmt_disponibles = $pdo->prepare("
                    SELECT c.* 
                    FROM Criterio c
                    WHERE c.id_criterio NOT IN (
                        SELECT id_criterio FROM CriterioConcurso WHERE id_concurso = ?
                    )
                    ORDER BY c.nombre
                ");
                $stmt_disponibles->execute([$id_concurso]);
                $criterios_disponibles = $stmt_disponibles->fetchAll();
                ?>
                <select name="id_criterio" class="form-select" required>
                    <option value="">Selecciona un criterio</option>
                    <?php foreach ($criterios_disponibles as $c): ?>
                        <option value="<?= $c['id_criterio'] ?>">
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Puntaje Máximo (ej: 20)</label>
                <input type="number" name="puntaje_maximo" class="form-control" step="0.5" min="1" max="100" placeholder="Ej: 20" required>
            </div>

            <button type="submit" class="btn btn-success">Asignar a este concurso</button>
        </form>

        <hr>

        <!-- Lista de criterios ya asignados -->
        <h4>📋 Criterios Asignados</h4>
        <?php
        // Obtener criterios ya asignados a este concurso
        $stmt_asignados = $pdo->prepare("
            SELECT cc.*, c.nombre AS nombre_criterio
            FROM CriterioConcurso cc
            JOIN Criterio c ON cc.id_criterio = c.id_criterio
            WHERE cc.id_concurso = ?
            ORDER BY cc.puntaje_maximo DESC
        ");
        $stmt_asignados->execute([$id_concurso]);
        $criterios_asignados = $stmt_asignados->fetchAll();
        ?>

        <?php if (count($criterios_asignados) > 0): ?>
            <ul class="list-group mb-3">
                <?php foreach ($criterios_asignados as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($c['nombre_criterio']) ?></strong>
                            <br>
                            <small class="text-muted">Puntaje máximo: <?= $c['puntaje_maximo'] ?> puntos</small>
                        </div>
                        <a href="index.php?page=admin_editar_criterio&id=<?= $c['id_criterio_concurso'] ?>"
                            class="btn btn-sm btn-warning">Editar</a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p class="text-muted">No hay criterios asignados a este concurso.</p>
        <?php endif; ?>

        <hr>

        <!-- Enlace para crear nuevos criterios generales -->
        <a href="index.php?page=admin_gestionar_criterios" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-circle"></i> Crear Nuevo Criterio General
        </a>

        <br><br>
        <a href="index.php?page=admin_gestion_concursos" class="btn btn-outline-secondary">Volver a Concursos</a>
    </div>

    <script>
        function agregarCriterio() {
            const lista = document.getElementById('criterios-lista');
            const div = document.createElement('div');
            div.className = 'row g-2 mb-2';
            div.innerHTML = `
                <div class="col-6">
                    <input type="text" name="nombre[]" class="form-control" placeholder="Nombre del criterio" required>
                </div>
                <div class="col-4">
                    <input type="number" name="peso[]" class="form-control" step="0.01" min="0.01" max="1" placeholder="Peso (ej: 0.30)" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger w-100" onclick="eliminar(this)">-</button>
                </div>
            `;
            lista.appendChild(div);
        }

        function eliminar(btn) {
            btn.closest('.row').remove();
        }
    </script>
</body>

</html>
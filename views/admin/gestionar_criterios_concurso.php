<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Criterios - <?= htmlspecialchars($concurso['nombre']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2>Criterios para: <?= htmlspecialchars($concurso['nombre']) ?></h2>

        <!-- Criterios ya asignados -->
        <h4>📋 Asignados</h4>
        <table class="table bg-white">
            <tr>
                <th>Criterio</th>
                <th>Puntaje Máx.</th>
                <th>Acciones</th>
            </tr>
            <?php foreach ($criterios_asignados as $c): ?>
                <tr>
                    <td><?= htmlspecialchars($c['nombre_criterio']) ?></td>
                    <td><?= $c['puntaje_maximo'] ?> pts</td>
                    <td>
                        <a href="index.php?page=admin_asignar_criterios_concurso&id_concurso=<?= $id_concurso ?>&id_criterio=<?= $c['id_criterio'] ?>"
                            class="btn btn-sm btn-warning">Editar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>

        <!-- Criterios disponibles -->
        <h4>➕ Disponibles</h4>
        <div class="list-group">
            <?php foreach ($criterios_disponibles as $c): ?>
                <a href="index.php?page=admin_asignar_criterios_concurso&id_concurso=<?= $id_concurso ?>&id_criterio=<?= $c['id_criterio'] ?>"
                    class="list-group-item list-group-item-action">
                    <?= htmlspecialchars($c['nombre']) ?> → Asignar puntaje
                </a>
            <?php endforeach; ?>
        </div>

        <br>
        <a href="index.php?page=admin_gestion_concursos" class="btn btn-secondary">Volver</a>
    </div>
</body>

</html>
<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();

$id_criterio = $_GET['id_criterio'] ?? null;
$id_concurso = $_GET['id_concurso'] ?? null;

global $pdo;

// Si viene desde selección de criterio, pedir concurso
if ($id_criterio && !$id_concurso):
    $stmt = $pdo->prepare("SELECT nombre FROM Criterio WHERE id_criterio = ?");
    $stmt->execute([$id_criterio]);
    $criterio = $stmt->fetch();

    $stmt_concursos = $pdo->query("SELECT * FROM Concurso ORDER BY nombre");
    $concursos = $stmt_concursos->fetchAll();
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Asignar a Concurso</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="bg-light p-4">
        <div class="container">
            <h3>Asignar: <?= htmlspecialchars($criterio['nombre']) ?></h3>
            <p>Selecciona el concurso donde usarás este criterio:</p>

            <form method="GET">
                <input type="hidden" name="page" value="admin_asignar_criterios_concurso">
                <input type="hidden" name="id_criterio" value="<?= $id_criterio ?>">
                <select name="id_concurso" class="form-select mb-3" onchange="this.form.submit()" required>
                    <option value="">Selecciona un concurso</option>
                    <?php foreach ($concursos as $c): ?>
                        <option value="<?= $c['id_concurso'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
    </body>

    </html>
<?php
    exit;
endif;

// Si ya hay concurso, mostrar formulario de puntaje
if ($id_criterio && $id_concurso):
    $stmt = $pdo->prepare("SELECT nombre FROM Criterio WHERE id_criterio = ?");
    $stmt->execute([$id_criterio]);
    $criterio = $stmt->fetch();

    if ($_POST['puntaje'] ?? null) {
        $puntaje = (float)$_POST['puntaje'];
        $stmt_check = $pdo->prepare("SELECT * FROM CriterioConcurso WHERE id_criterio = ? AND id_concurso = ?");
        $stmt_check->execute([$id_criterio, $id_concurso]);

        if ($stmt_check->rowCount() > 0) {
            $pdo->prepare("UPDATE CriterioConcurso SET puntaje_maximo = ? WHERE id_criterio = ? AND id_concurso = ?")
                ->execute([$puntaje, $id_criterio, $id_concurso]);
        } else {
            $pdo->prepare("INSERT INTO CriterioConcurso (id_criterio, id_concurso, puntaje_maximo) VALUES (?, ?, ?)")
                ->execute([$id_criterio, $id_concurso, $puntaje]);
        }

        header("Location: index.php?page=admin_configurar_criterios&id_concurso=$id_concurso&success=guardado");
        exit;
    }
?>
    <!DOCTYPE html>
    <html lang="es">

    <head>
        <meta charset="UTF-8">
        <title>Asignar Puntaje</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    </head>

    <body class="bg-light p-4">
        <div class="container">
            <h3>Asignar puntaje a: <?= htmlspecialchars($criterio['nombre']) ?></h3>
            <form method="POST">
                <div class="mb-3" style="max-width: 300px;">
                    <label>Puntaje Máximo (ej: 20)</label>
                    <input type="number" name="puntaje" class="form-control" step="0.5" min="1" max="100" required>
                </div>
                <button type="submit" class="btn btn-success">Guardar</button>
                <a href="index.php?page=admin_configurar_criterios&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary">
                    Volver
                </a>
            </form>
        </div>
    </body>

    </html>
<?php
    exit;
endif;

// Configuración completa por concurso
$id_concurso = $_GET['id_concurso'] ?? null;
if (!$id_concurso) {
    header('Location: index.php?page=admin_gestion_concursos');
    exit;
}

$stmt_concurso = $pdo->prepare("SELECT nombre FROM Concurso WHERE id_concurso = ?");
$stmt_concurso->execute([$id_concurso]);
$concurso = $stmt_concurso->fetch();

$stmt = $pdo->prepare("
    SELECT cc.*, c.nombre as nombre_criterio 
    FROM CriterioConcurso cc
    JOIN Criterio c ON cc.id_criterio = c.id_criterio
    WHERE cc.id_concurso = ?
    ORDER BY cc.puntaje_maximo DESC
");
$stmt->execute([$id_concurso]);
$criterios_asignados = $stmt->fetchAll();

$stmt_disponibles = $pdo->query("SELECT * FROM Criterio WHERE id_criterio NOT IN (
    SELECT id_criterio FROM CriterioConcurso WHERE id_concurso = $id_concurso
) ORDER BY nombre");
$criterios_disponibles = $stmt_disponibles->fetchAll();
?>

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
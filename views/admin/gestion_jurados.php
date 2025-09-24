<?php
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../models/Jurado.php';

redirect_if_not_admin();
$user = auth();

// ✅ Ahora sí puedes usar $_SESSION
$mostrarToken = false;
$token = '';

if (isset($_SESSION['mensaje_token'])) {
    $token = $_SESSION['mensaje_token'];
    unset($_SESSION['mensaje_token']); // Limpiar para que no aparezca nuevamente
    $mostrarToken = true;
}

$error = $_GET['error'] ?? null;
$id_concurso = $_GET['id_concurso'] ?? null;

if ($id_concurso) {
    // Obtener jurados asignados a este concurso
    $jurados = Jurado::porConcurso($id_concurso);
} else {
    // Listar todos los jurados (opcional)
    $jurados = Jurado::listar();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Jurados - FRFCP Admin</title>
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
            <h2><i class="bi bi-person-badge me-2 text-primary"></i>
                Gestionar Jurados
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Selección de concurso -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET">
                    <input type="hidden" name="page" value="admin_gestion_jurados">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label"><strong>Filtrar por Concurso</strong></label>
                            <select name="id_concurso" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Todos los jurados --</option>
                                <?php
                                global $pdo;
                                $stmt = $pdo->query("SELECT * FROM Concurso ORDER BY nombre");
                                while ($c = $stmt->fetch()): ?>
                                    <option value="<?= $c['id_concurso'] ?>" <?= ($id_concurso == $c['id_concurso']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($mostrarToken && $token): ?>
            <div class="alert alert-success">
                <h5><i class="bi bi-check-circle"></i> ¡Jurado creado con éxito!</h5>

                <p><strong>Token generado:</strong></p>
                <code class="d-block p-2 bg-light mb-3 text-center" style="font-size: 1.1em;">
                    <?= htmlspecialchars($token) ?>
                </code>

                <p><strong>Enlace de acceso para el jurado:</strong></p>
                <?php
                $link = "http://$_SERVER[HTTP_HOST]" . dirname($_SERVER['SCRIPT_NAME']);
                $link = rtrim($link, '/') . "/index.php?page=jurado_login&token=" . urlencode($token);
                ?>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($link) ?>" id="linkToken" readonly>
                    <button class="btn btn-outline-secondary" type="button" onclick="copiarLink()">
                        <i class="bi bi-copy"></i> Copiar
                    </button>
                </div>

                <small class="text-muted">
                    Entrega este enlace al jurado. Solo funcionará hasta el final del concurso.
                </small>
            </div>
        <?php endif; ?>

        <!-- Listado de jurados -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i>
                    <?= $id_concurso ? 'Jurados asignados al concurso' : 'Todos los Jurados' ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($jurados) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>DNI</th>
                                    <th>Usuario</th>
                                    <th>Especialidad</th>
                                    <th>Años Exp.</th>
                                    <th>Token</th>
                                    <th>Estado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jurados as $j): ?>
                                    <tr>
                                        <td><?= $j['dni'] ?></td>
                                        <td><?= htmlspecialchars($j['usuario']) ?></td>
                                        <td><?= ucfirst($j['especialidad']) ?></td>
                                        <td><?= $j['años_experiencia'] ?></td>
                                        <td>
                                            <?php if (!empty($j['token'])): ?>
                                                <code style="font-size: 0.9em;"><?= $j['token'] ?></code>
                                            <?php else: ?>
                                                <small class="text-muted">Sin token</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($j['token'])): ?>
                                                <span class="badge bg-<?= $j['usado'] ? 'success' : 'warning' ?>">
                                                    <?= $j['usado'] ? 'Usado' : 'Pendiente' ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">No asignado</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 m-0">
                        No hay jurados registrados o asignados a este concurso.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón: Nuevo Jurado + Token -->
        <div class="mt-4 text-end">
            <?php if ($id_concurso): ?>
                <a href="index.php?page=admin_crear_jurado&id_concurso=<?= $id_concurso ?>"
                    class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                </a>
            <?php else: ?>
                <button class="btn btn-success" disabled title="Primero selecciona un concurso">
                    <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                </button>
                <br>
                <small class="text-muted">Para crear un jurado, primero selecciona un concurso.</small>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function copiarLink() {
            const input = document.getElementById('linkToken');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado al portapapeles');
        }
    </script>
</body>

</html>
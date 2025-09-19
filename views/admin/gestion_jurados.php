<<<<<<< HEAD
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Jurados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2><i class="fas fa-user-check"></i> Gestionar Jurados</h2>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show"><?= $_SESSION['success']; unset($_SESSION['success']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show"><?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Botón para agregar -->
    <div class="mb-4">
        <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAgregarJurado">
            <i class="fas fa-plus"></i> Nuevo Jurado
        </a>
    </div>

    <!-- Tabla de jurados -->
    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>DNI</th>
                        <th>Correo</th>
                        <th>Especialidad</th>
                        <th>Años Exp.</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jurados as $j): ?>
                    <tr>
                        <td><?= $j['id_jurado'] ?></td>
                        <td><?= htmlspecialchars($j['dni']) ?></td>
                        <td><?= htmlspecialchars($j['correo']) ?></td>
                        <td><?= htmlspecialchars($j['especialidad']) ?></td>
                        <td><?= $j['años_experiencia'] ?></td>
                        <td>
                            <a href="index.php?page=admin_editar_jurado&id=<?= $j['id_jurado'] ?>" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="index.php?page=admin_eliminar_jurado&id=<?= $j['id_jurado'] ?>" 
                               class="btn btn-sm btn-danger" 
                               onclick="return confirm('¿Eliminar jurado?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Agregar Jurado -->
<div class="modal fade" id="modalAgregarJurado" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="index.php?page=admin_agregar_jurado">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus"></i> Nuevo Jurado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>DNI</label>
                        <input type="text" name="dni" class="form-control" maxlength="8" required>
                    </div>
                    <div class="mb-3">
                        <label>Correo</label>
                        <input type="email" name="correo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Años de experiencia</label>
                        <input type="number" name="años_experiencia" class="form-control" min="1" max="50" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Registrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
=======
<?php
require_once __DIR__ . '/../../helpers/auth.php';
require_once __DIR__ . '/../../models/Jurado.php';

redirect_if_not_admin();
$user = auth();

$error = $_GET['error'] ?? null;
$success = $_GET['success'] ?? null;

$id_concurso = $_GET['id_concurso'] ?? null;

if ($id_concurso) {
    // Obtener jurados asignados a este concurso
    $jurados = Jurado::porConcurso($id_concurso);
} else {
    // Listar todos los jurados (opcional)
    $jurados = Jurado::listar();
}
?>

<?php if ($success == 'token'): ?>
    <?php $token = $_GET['token'] ?? ''; ?>
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
                                        <?= htmlspecialchars($c['nombre']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensajes -->
        <?php if ($success == '1'): ?>
            <div class="alert alert-success">✅ Jurado creado correctamente.</div>
        <?php elseif ($success == 'token'): ?>
            <div class="alert alert-success">✅ Token generado y copiado al portapapeles.</div>
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

>>>>>>> 3cb3c71ea1b3afa6213b7da48f3ff79d235a4e22
</html>
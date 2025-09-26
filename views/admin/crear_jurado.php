<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

$id_concurso = $_GET['id_concurso'] ?? null;

if (!$id_concurso) {
    header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
    exit;
}

// Verificar que el concurso exista
global $pdo;
$stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();

if (!$concurso) {
    header('Location: index.php?page=admin_gestion_concursos&error=invalido');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Jurado - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 700px;
            margin: 80px auto;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-badge me-2 text-success"></i> Nuevo Jurado</h2>
            <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>"
                class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="alert alert-info">
            <small>
                <i class="bi bi-info-circle"></i>
                Se creará un nuevo jurado y se generará un token de acceso único.
            </small>
        </div>

        <form method="POST" action="index.php?page=admin_guardar_jurado">
            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">
            <!-- Dentro del formulario -->
            <div class="alert alert-info">
                <small>
                    <strong>Este concurso termina:</strong><br>
                    <?= date('d/m/Y H:i', strtotime($concurso['fecha_fin'])) ?>
                    <br><br>
                    El token del jurado expirará automáticamente al finalizar.
                </small>
            </div>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>DNI</strong></label>
                    <input type="text" class="form-control" name="dni" maxlength="8" pattern="\d{8}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Nombre</strong></label>
                    <input type="text" class="form-control" name="nombre" required>
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label"><strong>Usuario</strong></label>
                <input type="text" class="form-control" name="usuario" required>
            </div>

            <div class="mb-3">
                <label><strong>Contraseña Temporal</strong></label>
                <input type="text" class="form-control" name="contrasena_temporal" placeholder="Opcional (por defecto: temporal123)">
                <small class="text-muted">Si no llenas, será <code>temporal123</code></small>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>Especialidad</strong></label>
                    <select class="form-control" name="especialidad" required>
                        <option value="">Selecciona...</option>
                        <option value="tradicional">Danza Tradicional</option>
                        <option value="musical">Música Andina</option>
                        <option value="vestimenta">Vestimenta y Artesanía</option>
                        <option value="coreografia">Coreografía y Armonía</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Años de Experiencia</strong></label>
                    <input type="number" class="form-control" name="años_experiencia" min="1" max="50" required>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex mt-4">
                <button type="submit" class="btn btn-success">Crear Jurado + Generar Token</button>
                <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>"
                    class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
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
</html>
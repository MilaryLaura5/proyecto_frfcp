<!-- views/admin/crear_jurado.php -->
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Jurado - FRFCP</title>
    <!-- ✅ Corrección: eliminar espacios al final -->
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
            <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <div class="alert alert-info">
            <small><i class="bi bi-info-circle"></i> Se creará un nuevo jurado y se generará un token de acceso único.</small>
        </div>

        <form method="POST" action="index.php?page=admin_guardar_jurado">
            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

            <div class="mb-3">
                <label><strong>DNI</strong></label>
                <input type="text" class="form-control" name="dni" required maxlength="8">
            </div>

            <div class="mb-3">
                <label><strong>Nombre Completo</strong></label>
                <input type="text" class="form-control" name="nombre" required>
            </div>

            <div class="mb-3">
                <label><strong>Usuario</strong></label>
                <input type="text" class="form-control" name="usuario" required>
            </div>

            <div class="mb-3">
                <label><strong>Especialidad</strong></label>
                <select class="form-control" name="especialidad" required>
                    <option value="">Selecciona una especialidad</option>
                    <option value="Presentación y Vestimenta">Presentación y Vestimenta</option>
                    <option value="Música">Música</option>
                    <option value="Coreografía">Coreografía</option>
                    <option value="Escenografía">Escenografía</option>
                    <option value="Armonía">Armonía</option>
                    <option value="Originalidad">Originalidad</option>
                </select>
            </div>

            <div class="mb-3">
                <label><strong>Años de Experiencia</strong></label>
                <input type="number" class="form-control" name="años_experiencia" min="1" required>
            </div>

            <button type="submit" class="btn btn-success">Guardar Jurado</button>
        </form>
    </div>

    <!-- ✅ Corrección: eliminar espacio al final -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
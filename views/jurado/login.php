<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Jurado - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light d-flex align-items-center py-5">
    <div class="container" style="max-width: 400px;">
        <div class="card shadow-sm">
            <div class="card-header text-center bg-primary text-white">
                <h5><i class="bi bi-person-badge"></i> Acceso Jurado</h5>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?php if ($_GET['error'] === 'invalido'): ?>
                            Token inválido o expirado.
                        <?php elseif ($_GET['error'] === 'vacio'): ?>
                            Por favor, ingresa tu token.
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=jurado_login_submit">
                    <input type="text" name="usuario" placeholder="Usuario" required>
                    <input type="password" name="contrasena" placeholder="Contraseña" required>
                    <button type="submit">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
<?php
// views/jurado/login.php
// Esta vista se muestra después de hacer clic en un enlace tipo:
// index.php?page=jurado_login&token=abc123

$token = $_GET['token'] ?? '';
if (!$token) {
    die("Error: Token no proporcionado.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Login Jurado - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }
    </style>
</head>

<body>

    <div class="card" style="max-width: 500px; margin: 100px auto;">
        <div class="card-header bg-primary text-white">
            <h5><i class="bi bi-person-check"></i> Acceso para Jurado</h5>
        </div>
        <div class="card-body">
            <p>Ingresa tus credenciales para comenzar la evaluación:</p>

            <!-- Token oculto -->
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <form method="POST" action="index.php?page=jurado_login_submit">
                <div class="mb-3">
                    <label class="form-label"><strong>Usuario)/strong></label>
                    <input type="text" class="form-control" name="usuario" placeholder="Carlos45" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"><strong>Contraseña</strong></label>
                    <input type="password" class="form-control" name="contrasena" required>
                </div>
                <button type="submit" class="btn btn-success w-100">
                    <i class="bi bi-box-arrow-in-right"></i> Iniciar Evaluación
                </button>
            </form>

            <div class="mt-3 text-center">
                <small class="text-muted">
                    Usa el usuario y contraseña entregados por el administrador.
                </small>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jurado - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', sans-serif;
        }

        .card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .btn-success {
            background-color: #28a745;
            border: none;
            padding: 12px;
            font-size: 1.1em;
        }

        .form-control {
            font-size: 1.1em;
            padding: 12px;
        }

        label {
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 500px;">
            <div class="card-header bg-primary text-white text-center">
                <h5><i class="bi bi-person-check"></i> Acceso para Jurado</h5>
            </div>
            <div class="card-body">
                <p class="text-center">Ingresa tus credenciales para evaluar:</p>

                <form method="POST" action="index.php?page=jurado_login_submit">
                    <div class="mb-3">
                        <label class="form-label"><strong>Usuario</strong></label>
                        <input type="text" class="form-control" name="usuario" placeholder="ej: jurado123" required autofocus>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
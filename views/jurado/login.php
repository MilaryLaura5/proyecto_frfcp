<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Jurado - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            /* Fondo con imagen y degradado blanco translúcido */
            background:
                linear-gradient(rgba(255, 255, 255, 0.7), rgba(255, 255, 255, 0.7)),
                url('https://www.shutterstock.com/image-photo/image-virgin-la-candelaria-paraded-600w-1300574818.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, sans-serif;
        }

        .login-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12);
            overflow: hidden;
            backdrop-filter: blur(6px);
            background-color: rgba(255, 255, 255, 0.95);
        }

        .login-header {
            background: linear-gradient(to right, #0d6efd, #0b5ed7);
            padding: 1.5rem 1.25rem;
            text-align: center;
        }

        .login-header h5 {
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .form-control:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .btn-primary {
            background: linear-gradient(to right, #0d6efd, #0b5ed7);
            border: none;
            padding: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #0b5ed7, #0a58ca);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
    </style>
</head>

<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="login-header text-white">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>Acceso para Jurado
                        </h5>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($_GET['error'])): ?>
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-exclamation-circle me-2"></i>
                                <?php if ($_GET['error'] === 'invalido'): ?>
                                    Token inválido o ha expirado.
                                <?php elseif ($_GET['error'] === 'vacio'): ?>
                                    Por favor, ingresa tu token.
                                <?php else: ?>
                                    Credenciales incorrectas. Inténtalo de nuevo.
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=jurado_login_submit">
                            <div class="mb-3">
                                <label for="usuario" class="form-label">Usuario</label>
                                <input type="text"
                                    class="form-control form-control-lg"
                                    id="usuario"
                                    name="usuario"
                                    placeholder="Ingresa tu nombre de usuario"
                                    required
                                    autocomplete="username">
                            </div>
                            <div class="mb-4">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password"
                                    class="form-control form-control-lg"
                                    id="contrasena"
                                    name="contrasena"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Ingresar
                            </button>
                        </form>

                    </div>
                </div>
                <div class="text-center mt-3 text-muted small">
                    <p class="mb-0">Federación Regional de Folclore y Cultura de Puno</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
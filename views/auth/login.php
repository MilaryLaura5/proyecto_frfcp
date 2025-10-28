<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión - FRFCP</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            /* Fondo degradado en tonos rojos suaves */
            background: linear-gradient(135deg, #f8f0f0 0%, #ffeaea 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }

        .login-card {
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            /* Degradado rojo profesional (Material Red 600 → Red 800) */
            background: linear-gradient(to right, #d32f2f, #b71c1c);
            color: white;
            padding: 1.25rem;
            text-align: center;
        }

        .card-header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .logo {
            margin-bottom: 20px;
        }

        .logo img {
            width: 70px;
            height: auto;
            border-radius: 8px;
            background: white;
            padding: 6px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }

        .form-control:focus {
            border-color: #f9a7a7;
            box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.2);
        }

        .btn-primary {
            /* Botón en tono rojo que combina */
            background: linear-gradient(to right, #d32f2f, #b71c1c);
            border: none;
            padding: 0.65rem;
            font-weight: 600;
            letter-spacing: 0.4px;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #b71c1c, #9e1b1b);
        }

        .footer-note {
            font-size: 0.85rem;
            color: #6c757d;
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>

<body class="d-flex align-items-center py-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-4">
                <div class="card login-card">
                    <div class="card-header">
                        <div class="logo mx-auto">
                            <!-- Reemplaza con tu logo real -->
                            <img src="https://iconape.com/wp-content/files/gw/144111/png/144111.png" alt="Logo FRFCP">
                        </div>
                        <h1>FRFCP Calificaciones</h1>
                    </div>
                    <div class="card-body p-4">

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger d-flex align-items-center mb-4" role="alert">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                <?php
                                switch ($error):
                                    case 'invalido':
                                        echo "Usuario o contraseña incorrectos.";
                                        break;
                                    case 'vacios':
                                        echo "Por favor, completa todos los campos.";
                                        break;
                                    case 'rol':
                                        echo "Acceso denegado: rol no autorizado.";
                                        break;
                                    default:
                                        echo "Error al iniciar sesión.";
                                endswitch;
                                ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" action="index.php?page=login_submit">
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
                                <label for="contraseña" class="form-label">Contraseña</label>
                                <input type="password"
                                    class="form-control form-control-lg"
                                    id="contraseña"
                                    name="contraseña"
                                    placeholder="••••••••"
                                    required
                                    autocomplete="current-password">
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
                            </button>
                        </form>

                    </div>
                </div>

                <p class="footer-note mt-3">
                    Solo acceso para usuarios autorizados:<br>
                    <strong>Administradores, Presidentes y Jurados</strong>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
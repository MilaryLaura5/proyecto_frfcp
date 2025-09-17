<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inicio de Sesión - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .login-container { max-width: 400px; margin: 100px auto; }
        .logo { text-align: center; margin-bottom: 30px; }
        .logo img { width: 80px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <!-- Reemplaza con tu logo real -->
            <img src="https://iconape.com/wp-content/files/gw/144111/png/144111.png" alt="Logo FRFCP">
            <h4>FRFCP Calificaciones</h4>
        </div>

        <div class="card shadow">
            <div class="card-body">
                <h5 class="card-title">Iniciar sesión</h5>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger alert-sm">
                        <?php 
                        switch ($error):
                            case 'invalido': echo "Correo o contraseña incorrectos."; break;
                            case 'vacios': echo "Por favor, completa todos los campos."; break;
                            case 'rol': echo "Rol no permitido."; break;
                        endswitch;
                        ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="index.php?page=login_submit">
                    <div class="mb-3">
                        <label for="correo" class="form-label">Correo</label>
                        <input type="email" class="form-control" id="correo" name="correo" required>
                    </div>
                    <div class="mb-3">
                        <label for="contraseña" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contraseña" name="contraseña" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Iniciar sesión</button>
                </form>
            </div>
        </div>

        <p class="text-center mt-3" style="font-size: 0.9em; color: #666;">
            Solo usuarios autorizados (admin, presidente, jurados).
        </p>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error - FRFCP</title>
    <!-- Bootstrap CSS (corregido: sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #f8f0f0 0%, #ffeaea 100%);
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
        }

        .error-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            max-width: 500px;
            text-align: center;
            border-top: 4px solid #c9184a;
        }

        .error-icon {
            font-size: 3rem;
            color: #c9184a;
            margin-bottom: 1.25rem;
        }

        .error-title {
            font-weight: 700;
            color: #c9184a;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .error-message {
            color: #555;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="error-card">
            <div class="error-icon">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <h1 class="error-title">Acceso no permitido</h1>
            <p class="error-message">
                El token es inválido, ya ha sido usado o el concurso no está activo.
            </p>
            <a href="index.php?page=login" class="btn btn-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver al inicio
            </a>
        </div>
    </div>
</body>

</html>
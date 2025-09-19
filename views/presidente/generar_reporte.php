<!-- views/presidente/generar_reporte.php --><?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_presidente();
$user = auth();

// Variables pasadas desde el controlador
// $resultados = array de resultados por conjunto
// $criterios = array de criterios usados
// $id_concurso = ID del concurso
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Oficial Generado</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f9; }
        .main-content { padding: 20px; }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include '../../views/templates/sidebar_presidente.php'; ?>

        <!-- Contenido principal -->
        <main class="col-md-9 ms-sm-auto col-lg-10 main-content">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-file-earmark-pdf"></i> Reporte Oficial Generado</h2>
                <span class="badge bg-success">Listo</span>
            </div>

            <div class="card">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 5rem; color: #dc3545;"></i>
                    <h4>Reporte Oficial Listo</h4>
                    <p class="text-muted">Puede descargarlo en formato PDF.</p>
                    
                    <a href="<?= $_SESSION['reporte_path'] ?>" 
                       class="btn btn-success btn-lg" 
                       download>
                        <i class="bi bi-download"></i> Descargar PDF
                    </a>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
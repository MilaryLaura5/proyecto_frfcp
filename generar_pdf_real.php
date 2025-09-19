<?php
// generar_pdf_real.php - Genera un PDF real con DomPDF

// Iniciar sesión
session_start();

// Incluir conexión y modelo del presidente
require_once 'config/database.php';
require_once 'models/Presidente.php';
require_once 'helpers/auth.php';

// Verificar que el usuario sea Presidente
redirect_if_not_presidente();
$user = auth();

// Obtener el ID del concurso desde la URL
$id_concurso = $_GET['id_concurso'] ?? null;
if (!$id_concurso) {
    die("❌ No se especificó el concurso.");
}

// Conectar al modelo
$model = new Presidente($pdo);

// Obtener resultados y criterios
$resultados = $model->getResultadosFinales($id_concurso);
$criterios = $model->getCriteriosByConcurso($id_concurso);

// Si no hay resultados
if (empty($resultados)) {
    die("❌ No hay resultados disponibles para este concurso.");
}

// Agrupar resultados por serie
$series = [];
foreach ($resultados as $r) {
    $series[$r['nombre_serie']][] = $r;
}

// Preparar el HTML del PDF
ob_start(); // Empieza a capturar el HTML
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reporte Oficial</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            color: #1b3269;
        }

        h2 {
            color: #d4af37;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        .podium {
            background-color: #fffbe6;
            padding: 15px;
            border-radius: 8px;
            margin-top: 30px;
        }
    </style>
</head>

<body>

    <div class="header">
        <h1>FEDERACIÓN REGIONAL DE FOLKLORE Y CULTURA POPULAR</h1>
        <h2>IV CONCURSO REGIONAL DE SIKURIS "ZAMPOÑA DE ORO"</h2>
        <p><strong>Generado por:</strong> <?= htmlspecialchars($user['nombre']) ?></p>
        <p><strong>Fecha:</strong> <?= date('d/m/Y H:i') ?></p>
    </div>

    <?php foreach ($series as $serie => $conjuntos): ?>
        <h3>SERIE: <?= strtoupper($serie) ?></h3>
        <table>
            <thead>
                <tr>
                    <th>Puesto</th>
                    <th>Conjunto</th>
                    <th>Promedio Final</th>
                    <th>Jurado 1</th>
                    <th>Jurado 2</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Ordenar por promedio (mejor primero)
                usort($conjuntos, function ($a, $b) {
                    return $b['promedio_final'] <=> $a['promedio_final'];
                });

                foreach ($conjuntos as $idx => $c):
                    $medalla = $idx == 0 ? '🥇' : ($idx == 1 ? '🥈' : ($idx == 2 ? '🥉' : ''));
                ?>
                    <tr>
                        <td><?= $medalla ?> <?= $idx + 1 ?></td>
                        <td><?= htmlspecialchars($c['nombre_conjunto']) ?></td>
                        <td><strong><?= number_format($c['promedio_final'], 2) ?></strong></td>
                        <td><?= number_format($c['promedio_jurado1'], 2) ?></td>
                        <td><?= number_format($c['promedio_jurado2'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endforeach; ?>

    <div class="podium">
        <h4>🏆 PODIO GENERAL</h4>
        <?php
        $top = array_slice($resultados, 0, 3);
        foreach ($top as $idx => $t):
            $m = $idx == 0 ? '🥇' : ($idx == 1 ? '🥈' : '🥉');
            echo "<p><strong>$m {$t['nombre_conjunto']}</strong> - " . number_format($t['promedio_final'], 2) . "</p>";
        endforeach;
        ?>
    </div>

</body>

</html>
<?php
$html = ob_get_clean(); // Captura todo el HTML generado

// Incluir DomPDF
require_once 'vendor/autoload.php'; // Esto carga DomPDF automáticamente

use Dompdf\Dompdf;

// Crear instancia de DomPDF
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape'); // Horizontal
$dompdf->render();

// Descargar el PDF
$dompdf->stream("Reporte_Concurso_{$id_concurso}.pdf", [
    "Attachment" => true // true = descarga | false = solo ver
]);
?>
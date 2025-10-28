<?php
// generar_pdf_real.php - Informe Oficial Profesional (sin emojis, sin errores)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

session_start();
if (!isset($_SESSION['user'])) {
    die("Acceso denegado");
}

$id_concurso = $_GET['id_concurso'] ?? null;
if (!$id_concurso || !is_numeric($id_concurso)) {
    die("Concurso no válido");
}

global $pdo;

// Obtener información del concurso
$stmt = $pdo->prepare("SELECT nombre, fecha_inicio, fecha_fin, estado FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();

if (!$concurso) {
    die("Concurso no encontrado");
}

// Obtener resultados con manejo de empates
require_once __DIR__ . '/models/Presidente.php';
$presidenteModel = new Presidente($pdo);
$resultados = $presidenteModel->getResultadosFinales($id_concurso);

// Obtener criterios
$sql_criterios = "SELECT DISTINCT cr.nombre, cc.puntaje_maximo
                 FROM criterioconcurso cc
                 JOIN criterio cr ON cc.id_criterio = cr.id_criterio
                 WHERE cc.id_concurso = ?";
$stmt_criterios = $pdo->prepare($sql_criterios);
$stmt_criterios->execute([$id_concurso]);
$criterios = $stmt_criterios->fetchAll(PDO::FETCH_ASSOC);

// Configurar DomPDF (seguro y compatible)
$options = new Options();
$options->set('isRemoteEnabled', false);
$options->set('isPhpEnabled', false);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// HTML DEL PDF (profesional, sin emojis, sin errores)
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Oficial - ' . htmlspecialchars($concurso['nombre']) . '</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            margin: 20px;
            color: #000;
            font-size: 12px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 3px double #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .federacion {
            font-size: 16px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 0;
            color: #8B0000;
        }
        .titulo {
            font-size: 18px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
            color: #2F4F4F;
        }
        .subtitulo {
            font-size: 14px;
            margin: 3px 0;
            font-style: italic;
        }
        .info-concurso {
            margin: 10px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 11px;
        }
        th {
            background-color: #2F4F4F;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #000;
            font-weight: bold;
        }
        td {
            padding: 6px;
            border: 1px solid #000;
            text-align: center;
        }
        .posicion-1 { background-color: #FFF2CC; font-weight: bold; }
        .posicion-2 { background-color: #E6E6E6; font-weight: bold; }
        .posicion-3 { background-color: #E6D2C3; font-weight: bold; }
        .text-left { text-align: left; }
        .sin-calificar { color: #666; font-style: italic; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ccc;
            padding-top: 10px;
        }
        .firma {
            margin-top: 50px;
            text-align: center;
        }
        .firma-linea {
            border-top: 1px solid #000;
            width: 250px;
            margin: 0 auto 5px;
        }
        .resumen-table {
            width: 60%;
            margin: 15px auto;
            font-size: 10px;
        }
        .medal {
            font-weight: bold;
            color: #8B0000;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="federacion">FEDERACIÓN REGIONAL DEL FOLCLORE Y CULTURA DE PUNO</div>
        <div class="titulo">INFORME OFICIAL DE RESULTADOS</div>
        <div class="subtitulo">Concurso: "' . htmlspecialchars($concurso['nombre']) . '"</div>
        
        <div class="info-concurso">
            <strong>Fecha del Evento:</strong> ' . date('d/m/Y', strtotime($concurso['fecha_inicio'])) . ' | 
            <strong>Estado:</strong> ' . htmlspecialchars($concurso['estado']) . ' | 
            <strong>Fecha de Emisión:</strong> ' . date('d/m/Y H:i') . '
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">Posición</th>
                <th width="8%">Orden</th>
                <th width="35%">Conjunto Folklórico</th>
                <th width="15%">Categoría</th>
                <th width="12%">Puntaje Final</th>
                <th width="10%">Estado</th>
            </tr>
        </thead>
        <tbody>';

if (!empty($resultados)) {
    foreach ($resultados as $resultado) {
        // Reemplazamos emojis por texto claro
        $medal = '';
        if ($resultado['posicion'] == 1 && $resultado['promedio_final'] > 0) $medal = ' (ORO)';
        elseif ($resultado['posicion'] == 2 && $resultado['promedio_final'] > 0) $medal = ' (PLATA)';
        elseif ($resultado['posicion'] == 3 && $resultado['promedio_final'] > 0) $medal = ' (BRONCE)';

        $puntaje_display = $resultado['promedio_final'] > 0 ? number_format($resultado['promedio_final'], 2) : '<span class="sin-calificar">N/C</span>';
        $estado = $resultado['calificaciones_count'] > 0 ? 'Calificado' : 'Pendiente';

        $html .= '
            <tr class="posicion-' . $resultado['posicion'] . '">
                <td><strong>' . $resultado['posicion'] . '°</strong>' . $medal . '</td>
                <td><strong>' . $resultado['orden_presentacion'] . '</strong></td>
                <td class="text-left">' . htmlspecialchars($resultado['conjunto']) . '</td>
                <td>' . htmlspecialchars($resultado['categoria']) . '</td>
                <td><strong>' . $puntaje_display . '</strong></td>
                <td>' . $estado . '</td>
            </tr>';
    }
} else {
    $html .= '
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">
                    No hay resultados disponibles para este concurso.
                </td>
            </tr>';
}

$html .= '
        </tbody>
    </table>

    <!-- Criterios de Evaluación -->
    <div style="margin-top: 20px;">
        <h4 style="font-size: 12px; margin-bottom: 8px; text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
            CRITERIOS DE EVALUACIÓN
        </h4>
        <table style="width: 80%; margin: 0 auto;">
            <thead>
                <tr>
                    <th width="70%">Criterio</th>
                    <th width="30%">Puntaje Máximo</th>
                </tr>
            </thead>
            <tbody>';

if (!empty($criterios)) {
    foreach ($criterios as $criterio) {
        $html .= '
                <tr>
                    <td class="text-left">' . htmlspecialchars($criterio['nombre']) . '</td>
                    <td><strong>' . $criterio['puntaje_maximo'] . '</strong></td>
                </tr>';
    }
} else {
    $html .= '
                <tr>
                    <td colspan="2" style="text-align: center;">No se definieron criterios para este concurso.</td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <!-- Resumen Estadístico -->
    <div style="margin-top: 15px;">
        <h4 style="font-size: 12px; margin-bottom: 8px; text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
            RESUMEN ESTADÍSTICO
        </h4>
        <table class="resumen-table">
            <tbody>';

if (!empty($resultados)) {
    $total_conjuntos = count($resultados);
    $conjuntos_calificados = 0;
    $suma_puntajes = 0;
    $puntaje_maximo = 0;

    foreach ($resultados as $resultado) {
        if ($resultado['calificaciones_count'] > 0) {
            $conjuntos_calificados++;
            $suma_puntajes += $resultado['promedio_final'];
            if ($resultado['promedio_final'] > $puntaje_maximo) {
                $puntaje_maximo = $resultado['promedio_final'];
            }
        }
    }

    $promedio_general = $conjuntos_calificados > 0 ? round($suma_puntajes / $conjuntos_calificados, 2) : 0;

    $html .= '
                <tr>
                    <td class="text-left"><strong>Total de Conjuntos:</strong></td>
                    <td><strong>' . $total_conjuntos . '</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Conjuntos Calificados:</strong></td>
                    <td><strong>' . $conjuntos_calificados . '</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Conjuntos Pendientes:</strong></td>
                    <td><strong>' . ($total_conjuntos - $conjuntos_calificados) . '</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Porcentaje de Avance:</strong></td>
                    <td><strong>' . round(($conjuntos_calificados / $total_conjuntos) * 100, 1) . '%</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Puntaje Promedio:</strong></td>
                    <td><strong>' . $promedio_general . '</strong></td>
                </tr>
                <tr>
                    <td class="text-left"><strong>Puntaje Máximo:</strong></td>
                    <td><strong>' . $puntaje_maximo . '</strong></td>
                </tr>';
}

$html .= '
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p><em>Documento generado automáticamente por el Sistema de Calificación de la Federación Regional del Folclore y Cultura de Puno</em></p>
        <p>Fecha de emisión: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="firma">
        <div class="firma-linea"></div>
        <p style="font-size: 11px; margin: 5px 0;"><strong>DR. ALEXANDER QUISPE HUARACHA</strong></p>
        <p style="font-size: 10px; margin: 3px 0;">PRESIDENTE</p>
        <p style="font-size: 9px; margin: 3px 0;">Federación Regional del Folclore y Cultura de Puno</p>
    </div>
</body>
</html>';

// Generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Descargar automáticamente
$filename = "Resultados_Oficiales_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $concurso['nombre']) . "_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;

<?php
// generar_pdf_real.php - PDF con suma correcta de criterios (igual que la vista web)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers/auth.php';

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

// Información del concurso
$stmt = $pdo->prepare("SELECT nombre, fecha_inicio, fecha_fin, estado FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();
if (!$concurso) die("Concurso no encontrado");

// === OBTENER RESULTADOS CON SUMA CORRECTA DE CRITERIOS (igual que la vista web) ===
$sql = "
    SELECT 
        c.nombre AS conjunto,
        pc.orden_presentacion,
        s.nombre_serie AS categoria,
        COALESCE(ROUND(SUM(dc.puntaje), 2), 0) AS puntaje_suma,
        MAX(CASE WHEN ca.estado = 'descalificado' THEN 1 ELSE 0 END) AS es_descalificado,
        COUNT(dc.id_detalle) AS calificaciones_count
    FROM participacionconjunto pc
    JOIN conjunto c ON pc.id_conjunto = c.id_conjunto
    JOIN serie s ON c.id_serie = s.id_serie
    LEFT JOIN calificacion ca ON pc.id_participacion = ca.id_participacion
    LEFT JOIN detallecalificacion dc ON ca.id_calificacion = dc.id_calificacion
    WHERE pc.id_concurso = ?
    GROUP BY c.id_conjunto, c.nombre, pc.orden_presentacion, s.nombre_serie
    ORDER BY 
        es_descalificado ASC,
        puntaje_suma DESC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([$id_concurso]);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Procesar estado y puntaje final
foreach ($resultados as &$r) {
    $es_descalificado = (bool)$r['es_descalificado'];
    $tiene_calificaciones = $r['calificaciones_count'] > 0;

    if ($es_descalificado) {
        $r['estado_pdf'] = 'descalificado';
        $r['puntaje_final'] = 0.00;
    } elseif ($tiene_calificaciones) {
        $r['estado_pdf'] = 'calificado';
        $r['puntaje_final'] = (float)$r['puntaje_suma'];
    } else {
        $r['estado_pdf'] = 'pendiente';
        $r['puntaje_final'] = null;
    }
}
unset($r);

// Separar calificados para posiciones
$calificados = array_filter($resultados, fn($r) => $r['estado_pdf'] === 'calificado');
$otros = array_filter($resultados, fn($r) => $r['estado_pdf'] !== 'calificado');

// Calcular posiciones solo para calificados
$posicion = 1;
$ultimo_puntaje = null;
foreach ($calificados as &$r) {
    if ($ultimo_puntaje === null || $r['puntaje_final'] < $ultimo_puntaje) {
        $posicion_actual = $posicion;
    }
    $r['posicion'] = $posicion_actual;
    $ultimo_puntaje = $r['puntaje_final'];
    $posicion++;
}
unset($r);

// Combinar resultados
$resultados_pdf = array_merge($calificados, $otros);

// Estadísticas
$total = count($resultados_pdf);
$calificados_count = count($calificados);
$descalificados_count = count(array_filter($otros, fn($r) => $r['estado_pdf'] === 'descalificado'));
$pendientes_count = $total - $calificados_count - $descalificados_count;
$tiene_calificaciones = $calificados_count > 0;

// Criterios
$stmt_c = $pdo->prepare("SELECT cr.nombre, cc.puntaje_maximo FROM criterioconcurso cc JOIN criterio cr ON cc.id_criterio = cr.id_criterio WHERE cc.id_concurso = ?");
$stmt_c->execute([$id_concurso]);
$criterios = $stmt_c->fetchAll(PDO::FETCH_ASSOC);

// DomPDF
$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isPhpEnabled', false);
$options->set('defaultFont', 'Arial');
$dompdf = new Dompdf($options);

// Logo
$logoPath = __DIR__ . '/logos/logo_frfcp.png';
$logoBase64 = '';
if (file_exists($logoPath)) {
    $logoBase64 = 'image/png;base64,' . base64_encode(file_get_contents($logoPath));
}

// HTML
$html = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe Oficial - ' . htmlspecialchars($concurso['nombre']) . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; color: #000; font-size: 11px; line-height: 1.4; }
        .header { text-align: center; border-bottom: 3px double #8B0000; padding-bottom: 15px; margin-bottom: 25px; }
        .logo { height: 60px; margin-bottom: 10px; }
        .federacion { font-size: 18px; font-weight: bold; text-transform: uppercase; margin: 0; color: #8B0000; }
        .titulo { font-size: 16px; font-weight: bold; margin: 8px 0; color: #2F4F4F; }
        .subtitulo { font-size: 13px; margin: 5px 0; font-style: italic; color: #555; }
        .info-concurso { background-color: #f8f8f8; padding: 10px; border-radius: 5px; margin: 15px 0; font-size: 11px; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 11px; }
        th { background-color: #2F4F4F; color: white; padding: 8px; text-align: center; border: 1px solid #000; font-weight: bold; }
        td { padding: 7px; border: 1px solid #000; text-align: center; }
        .text-left { text-align: left; padding-left: 8px; }
        .posicion-1 { background-color: #FFF9C4; font-weight: bold; }
        .posicion-2 { background-color: #E0E0E0; font-weight: bold; }
        .posicion-3 { background-color: #D7CCC8; font-weight: bold; }
        .sin-calificar { color: #777; font-style: italic; }
        .section-title { font-size: 13px; font-weight: bold; margin: 25px 0 12px 0; padding-bottom: 5px; border-bottom: 1px solid #ccc; color: #2F4F4F; text-align: center; }
        .resumen-table { width: 70%; margin: 15px auto; }
        .resumen-table td { padding: 6px 10px; }
        .footer { margin-top: 40px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ccc; padding-top: 12px; }
        .firma { margin-top: 60px; text-align: center; }
        .firma-linea { border-top: 1px solid #000; width: 250px; margin: 0 auto 8px; }
        .medal { font-weight: bold; color: #8B0000; }
    </style>
</head>
<body>

    <div class="header">
        ' . ($logoBase64 ? '<img src="' . $logoBase64 . '" alt="Logo FRFCP" class="logo">' : '') . '
        <div class="federacion">FEDERACIÓN REGIONAL DEL FOLCLORE Y CULTURA DE PUNO</div>
        <div class="titulo">INFORME OFICIAL DE RESULTADOS</div>
        <div class="subtitulo">Concurso: "' . htmlspecialchars($concurso['nombre']) . '"</div>
        <div class="info-concurso">
            <strong>Fecha del Evento:</strong> ' . date('d/m/Y', strtotime($concurso['fecha_inicio'])) . ' |
            <strong>Estado:</strong> ' . htmlspecialchars($concurso['estado']) . ' |
            <strong>Fecha de Emisión:</strong> ' . date('d/m/Y H:i') . '
        </div>
    </div>

    <div class="section-title">📊 RESULTADOS FINALES</div>
    <table>
        <thead>
            <tr>
                <th width="8%">Posición</th>
                <th width="8%">Orden</th>
                <th width="38%">Conjunto Folklórico</th>
                <th width="16%">Categoría</th>
                <th width="15%">Puntaje Final</th>
                <th width="15%">Estado</th>
            </tr>
        </thead>
        <tbody>';

foreach ($resultados_pdf as $r) {
    $posicion_texto = '—';
    $medal = '';
    if ($r['estado_pdf'] === 'calificado') {
        $posicion_texto = $r['posicion'] . '°';
        if ($r['posicion'] == 1) $medal = ' ';
        elseif ($r['posicion'] == 2) $medal = ' ';
        elseif ($r['posicion'] == 3) $medal = ' ';
    }

    $puntaje = match ($r['estado_pdf']) {
        'descalificado' => '<strong class="text-muted">0.00</strong>',
        'calificado' => '<strong class="text-primary">' . number_format($r['puntaje_final'], 2) . '</strong>',
        default => '<span class="sin-calificar">N/C</span>'
    };

    $estado = match ($r['estado_pdf']) {
        'descalificado' => '<span class="badge bg-danger">Descalificado</span>',
        'calificado' => '<span class="badge bg-success">Calificado</span>',
        default => '<span class="badge bg-warning">Pendiente</span>'
    };

    $html .= '
        <tr class="' . ($r['estado_pdf'] === 'calificado' && $r['posicion'] <= 3 ? 'posicion-' . $r['posicion'] : '') . '">
            <td><strong>' . $posicion_texto . '</strong>' . $medal . '</td>
            <td><strong>' . $r['orden_presentacion'] . '</strong></td>
            <td class="text-left">' . htmlspecialchars($r['conjunto']) . '</td>
            <td>' . htmlspecialchars($r['categoria']) . '</td>
            <td>' . $puntaje . '</td>
            <td>' . $estado . '</td>
        </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="section-title">📝 CRITERIOS DE EVALUACIÓN</div>
    <table style="width: 70%; margin: 0 auto;">
        <thead>
            <tr>
                <th width="70%">Criterio</th>
                <th width="30%">Puntaje Máximo</th>
            </tr>
        </thead>
        <tbody>';

foreach ($criterios as $c) {
    $html .= '
        <tr>
            <td class="text-left">' . htmlspecialchars($c['nombre']) . '</td>
            <td><strong>' . $c['puntaje_maximo'] . '</strong></td>
        </tr>';
}

$html .= '
        </tbody>
    </table>

    <div class="section-title">📈 RESUMEN ESTADÍSTICO</div>
    <table class="resumen-table">
        <tbody>
            <tr><td class="text-left"><strong>Total de Conjuntos:</strong></td><td><strong>' . $total . '</strong></td></tr>
            <tr><td class="text-left"><strong>Conjuntos Calificados:</strong></td><td><strong>' . $calificados_count . '</strong></td></tr>
            <tr><td class="text-left"><strong>Descalificados:</strong></td><td><strong>' . $descalificados_count . '</strong></td></tr>
            <tr><td class="text-left"><strong>Pendientes:</strong></td><td><strong>' . $pendientes_count . '</strong></td></tr>
            <tr><td class="text-left"><strong>Porcentaje de Avance:</strong></td><td><strong>' . round(($calificados_count / $total) * 100, 1) . '%</strong></td></tr>
        </tbody>
    </table>

    <div class="footer">
        <p><em>Documento generado automáticamente por el Sistema de Calificación de la Federación Regional del Folclore y Cultura de Puno</em></p>
        <p>Fecha de emisión: ' . date('d/m/Y H:i:s') . '</p>
    </div>
    
    <div class="firma">
        <div class="firma-linea"></div>
        <p style="font-size: 12px; margin: 5px 0;"><strong>DR. ALEXANDER QUISPE HUARACHA</strong></p>
        <p style="font-size: 11px; margin: 3px 0;">PRESIDENTE</p>
        <p style="font-size: 10px; margin: 3px 0;">Federación Regional del Folclore y Cultura de Puno</p>
    </div>
</body>
</html>';

// Generar PDF
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Descargar
$filename = "Resultados_Oficiales_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $concurso['nombre']) . "_" . date('Y-m-d') . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);
exit;

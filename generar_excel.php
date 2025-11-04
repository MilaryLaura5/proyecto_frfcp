<?php
// generar_excel.php - Exportar resultados a Excel (formato profesional)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers/auth.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

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

// === OBTENER RESULTADOS CON SUMA CORRECTA POR CRITERIO ===
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
        $r['estado_excel'] = 'Descalificado';
        $r['puntaje_final'] = 0.00;
    } elseif ($tiene_calificaciones) {
        $r['estado_excel'] = 'Calificado';
        $r['puntaje_final'] = (float)$r['puntaje_suma'];
    } else {
        $r['estado_excel'] = 'Pendiente';
        $r['puntaje_final'] = null;
    }
}
unset($r);

// Separar calificados para posiciones
$calificados = array_filter($resultados, fn($r) => $r['estado_excel'] === 'Calificado');
$otros = array_filter($resultados, fn($r) => $r['estado_excel'] !== 'Calificado');

// Calcular posiciones
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

$resultados_excel = array_merge($calificados, $otros);

// === CREAR ARCHIVO EXCEL ===
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Título
$sheet->setCellValue('A1', 'FEDERACIÓN REGIONAL DEL FOLCLORE Y CULTURA DE PUNO');
$sheet->setCellValue('A2', 'INFORME OFICIAL DE RESULTADOS');
$sheet->setCellValue('A3', 'Concurso: "' . $concurso['nombre'] . '"');
$sheet->setCellValue('A4', 'Fecha del Evento: ' . date('d/m/Y', strtotime($concurso['fecha_inicio'])));
$sheet->setCellValue('A5', 'Estado: ' . $concurso['estado']);
$sheet->setCellValue('A6', 'Fecha de Emisión: ' . date('d/m/Y H:i'));

// Estilo: negrita en título
$styleTitle = ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT]];
$sheet->getStyle('A1:A6')->applyFromArray($styleTitle);

// Encabezados de la tabla
$sheet->setCellValue('A8', 'Posición');
$sheet->setCellValue('B8', 'N° Orden');
$sheet->setCellValue('C8', 'Conjunto Folklórico');
$sheet->setCellValue('D8', 'Categoría');
$sheet->setCellValue('E8', 'Puntaje Final');
$sheet->setCellValue('F8', 'Estado');

// Estilo: negrita + centrado + fondo gris claro
$styleHeader = [
    'font' => ['bold' => true],
    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFEEEEEE']],
    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
];
$sheet->getStyle('A8:F8')->applyFromArray($styleHeader);

// Llenar datos
$fila = 9;
foreach ($resultados_excel as $r) {
    $posicion_texto = '—';
    if ($r['estado_excel'] === 'Calificado') {
        $posicion_texto = $r['posicion'] . '°';
    }

    $puntaje = match ($r['estado_excel']) {
        'Descalificado' => 0.00,
        'Calificado' => $r['puntaje_final'],
        default => 'N/C'
    };

    $sheet->setCellValue('A' . $fila, $posicion_texto);
    $sheet->setCellValue('B' . $fila, $r['orden_presentacion']);
    $sheet->setCellValue('C' . $fila, $r['conjunto']);
    $sheet->setCellValue('D' . $fila, $r['categoria']);
    $sheet->setCellValue('E' . $fila, $puntaje);
    $sheet->setCellValue('F' . $fila, $r['estado_excel']);

    // Estilo: centrado + bordes
    $styleCell = [
        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($styleCell);

    // Alternar color de fondo para filas pares
    if ($fila % 2 == 0) {
        $sheet->getStyle('A' . $fila . ':F' . $fila)->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
    }

    $fila++;
}

// Ajustar ancho de columnas
$sheet->getColumnDimension('A')->setAutoSize(true);
$sheet->getColumnDimension('B')->setAutoSize(true);
$sheet->getColumnDimension('C')->setAutoSize(true);
$sheet->getColumnDimension('D')->setAutoSize(true);
$sheet->getColumnDimension('E')->setAutoSize(true);
$sheet->getColumnDimension('F')->setAutoSize(true);

// Centrar contenido en todas las celdas de la tabla
$sheet->getStyle('A8:F' . ($fila - 1))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

// Descargar
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Resultados_' . preg_replace('/[^a-zA-Z0-9_-]/', '_', $concurso['nombre']) . '_' . date('Y-m-d') . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

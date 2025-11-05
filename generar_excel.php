<?php
// generar_excel.php - Exportar resultados a Excel (formato FORMAL profesional)

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/helpers/auth.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\Color;

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

// === OBTENER RESULTADOS ===
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

// === CREAR ARCHIVO EXCEL FORMAL ===
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Configuración inicial
$sheet->setTitle('Resultados Oficiales');
$sheet->getDefaultRowDimension()->setRowHeight(20);

// ========== ENCABEZADO FORMAL ==========
// Título principal
$sheet->mergeCells('A1:F1');
$sheet->setCellValue('A1', 'FEDERACIÓN REGIONAL DEL FOLCLORE Y CULTURA DE PUNO');
$sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
$sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Subtítulo
$sheet->mergeCells('A2:F2');
$sheet->setCellValue('A2', 'RESULTADOS OFICIALES DE CONCURSO');
$sheet->getStyle('A2')->getFont()->setBold(true)->setSize(12);
$sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Información del concurso
$sheet->mergeCells('A3:F3');
$sheet->setCellValue('A3', 'Concurso: "' . $concurso['nombre'] . '"');
$sheet->getStyle('A3')->getFont()->setSize(11);
$sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// Detalles del evento
$sheet->setCellValue('A4', 'Fecha de Inicio:');
$sheet->setCellValue('B4', date('d/m/Y H:i', strtotime($concurso['fecha_inicio'])));
$sheet->setCellValue('D4', 'Fecha de Fin:');
$sheet->setCellValue('E4', date('d/m/Y H:i', strtotime($concurso['fecha_fin'])));

$sheet->setCellValue('A5', 'Estado del Concurso:');
$sheet->setCellValue('B5', $concurso['estado']);
$sheet->setCellValue('D5', 'Fecha de Emisión:');
$sheet->setCellValue('E5', date('d/m/Y H:i'));

// Espacio antes de la tabla
$sheet->getRowDimension(7)->setRowHeight(10);

// ========== TABLA DE RESULTADOS ==========
$headerRow = 8;

// Encabezados de la tabla
$sheet->setCellValue('A' . $headerRow, 'POSICIÓN');
$sheet->setCellValue('B' . $headerRow, 'N° ORDEN');
$sheet->setCellValue('C' . $headerRow, 'CONJUNTO FOLKLÓRICO');
$sheet->setCellValue('D' . $headerRow, 'CATEGORÍA');
$sheet->setCellValue('E' . $headerRow, 'PUNTAJE FINAL');
$sheet->setCellValue('F' . $headerRow, 'ESTADO');

// Estilo encabezado formal - MEJORADO
$headerStyle = [
    'font' => [
        'bold' => true,
        'color' => ['argb' => Color::COLOR_WHITE],
        'size' => 10
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => ['argb' => 'FF34495E'] // Azul más oscuro y elegante
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
        'vertical' => Alignment::VERTICAL_CENTER
    ],
    'borders' => [
        'outline' => [
            'borderStyle' => Border::BORDER_MEDIUM,
            'color' => ['argb' => 'FF2C3E50']
        ],
        'inside' => [
            'borderStyle' => Border::BORDER_THIN,
            'color' => ['argb' => 'FF2C3E50']
        ]
    ]
];
$sheet->getStyle('A' . $headerRow . ':F' . $headerRow)->applyFromArray($headerStyle);
$sheet->getRowDimension($headerRow)->setRowHeight(25);

// Llenar datos
$fila = $headerRow + 1;
foreach ($resultados_excel as $r) {
    $posicion_texto = '—';
    $posicion_num = '';

    if ($r['estado_excel'] === 'Calificado') {
        $posicion_texto = $r['posicion'] . '°';
        $posicion_num = $r['posicion'];
    }

    $puntaje = match ($r['estado_excel']) {
        'Descalificado' => '0.00',
        'Calificado' => number_format($r['puntaje_final'], 2),
        default => 'N/C'
    };

    $sheet->setCellValue('A' . $fila, $posicion_texto);
    $sheet->setCellValue('B' . $fila, $r['orden_presentacion']);
    $sheet->setCellValue('C' . $fila, $r['conjunto']);
    $sheet->setCellValue('D' . $fila, $r['categoria']);
    $sheet->setCellValue('E' . $fila, $puntaje);
    $sheet->setCellValue('F' . $fila, $r['estado_excel']);

    // Estilo base para todas las filas - MEJORADO
    $rowStyle = [
        'alignment' => [
            'horizontal' => Alignment::HORIZONTAL_CENTER,
            'vertical' => Alignment::VERTICAL_CENTER
        ],
        'borders' => [
            'outline' => [
                'borderStyle' => Border::BORDER_MEDIUM,
                'color' => ['argb' => 'FFBDC3C7']
            ],
            'inside' => [
                'borderStyle' => Border::BORDER_THIN,
                'color' => ['argb' => 'FFECF0F1']
            ]
        ]
    ];

    // Destacar top 3 con colores elegantes
    if ($posicion_num <= 3 && $posicion_num != '') {
        $rowStyle['font'] = ['bold' => true];
        switch ($posicion_num) {
            case 1:
                $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFE0']]; // Amarillo muy suave
                break;
            case 2:
                $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF5F5F5']]; // Blanco humo
                break;
            case 3:
                $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F8FF']]; // Azul muy claro
                break;
        }
    }
    // Estilo para descalificados
    elseif ($r['estado_excel'] === 'Descalificado') {
        $rowStyle['font'] = ['color' => ['argb' => 'FFC0392B']]; // Rojo elegante
        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFF5F5']]; // Fondo rojo muy claro
    }
    // Estilo para pendientes
    elseif ($r['estado_excel'] === 'Pendiente') {
        $rowStyle['font'] = ['color' => ['argb' => 'FF7F8C8D']]; // Gris elegante
        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF9F9F9']]; // Fondo gris muy claro
    }
    // Efecto zebra para filas normales
    elseif ($fila % 2 == 0) {
        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF8F9F9']]; // Gris muy claro
    } else {
        $rowStyle['fill'] = ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFFFFFFF']]; // Blanco puro
    }

    $sheet->getStyle('A' . $fila . ':F' . $fila)->applyFromArray($rowStyle);
    $sheet->getRowDimension($fila)->setRowHeight(22);

    $fila++;
}

// Aplicar borde exterior completo a toda la tabla
$lastDataRow = $fila - 1;
$tableRange = 'A' . $headerRow . ':F' . $lastDataRow;
$sheet->getStyle($tableRange)->getBorders()->getOutline()->setBorderStyle(Border::BORDER_MEDIUM)->setColor(new Color('FF2C3E50'));

// ========== PIE DE PÁGINA DISCRETO ==========
$footerRow = $fila + 2;

$sheet->setCellValue('A' . $footerRow, 'Sistema de Gestión de Concursos Folklóricos - ' . date('Y'));
$sheet->getStyle('A' . $footerRow)->getFont()->setSize(9)->setItalic(true)->setColor(new Color('FF7F8C8D'));
$sheet->mergeCells('A' . $footerRow . ':F' . $footerRow);
$sheet->getStyle('A' . $footerRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

// ========== AJUSTES FINALES PERFECTOS ==========
// Ajustar ancho de columnas optimizado
$sheet->getColumnDimension('A')->setWidth(12);  // Posición
$sheet->getColumnDimension('B')->setWidth(12);  // Orden
$sheet->getColumnDimension('C')->setWidth(40);  // Conjunto
$sheet->getColumnDimension('D')->setWidth(25);  // Categoría
$sheet->getColumnDimension('E')->setWidth(15);  // Puntaje
$sheet->getColumnDimension('F')->setWidth(15);  // Estado

// Centrar todo el contenido de la tabla perfectamente
$sheet->getStyle('A' . $headerRow . ':F' . $lastDataRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER)->setVertical(Alignment::VERTICAL_CENTER);

// Congelar encabezados para mejor navegación
$sheet->freezePane('A' . ($headerRow + 1));

// Autoajustar alturas de fila del encabezado
$sheet->getRowDimension(1)->setRowHeight(25);
$sheet->getRowDimension(2)->setRowHeight(22);
$sheet->getRowDimension(3)->setRowHeight(20);

// ========== DESCARGAR ARCHIVO ==========
$filename = "Resultados_" . preg_replace('/[^a-zA-Z0-9_-]/', '_', $concurso['nombre']) . "_" . date('Y-m-d') . ".xlsx";

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '"');
header('Cache-Control: max-age=0');
header('Expires: 0');
header('Pragma: public');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;

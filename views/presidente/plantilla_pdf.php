<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Oficial - FRFCP</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; }
        .header { text-align: center; margin-bottom: 30px; }
        .logo { width: 100px; height: auto; }
        h1 { color: #1b3269; margin: 5px 0; }
        h2 { color: #d4af37; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        .podium { background-color: #fffbe6; padding: 15px; border-radius: 8px; margin-top: 30px; }
        .footer { margin-top: 50px; text-align: center; font-size: 12px; color: #555; }
        .firma { margin-top: 60px; }
        .firma div { display: inline-block; width: 30%; border-top: 1px solid #000; text-align: center; padding-top: 5px; }
    </style>
</head>
<body>

    <div class="header">
        <img src="https://via.placeholder.com/100x100.png?text=LOGO" alt="Logo FRFCP" class="logo">
        <h1>FEDERACIÓN REGIONAL DE FOLKLORE Y CULTURA POPULAR</h1>
        <h2>IV Concurso Regional de Sikuris "Zampoña de Oro"</h2>
        <p><strong>Fecha del Reporte:</strong> <?= date('d/m/Y H:i') ?></p>
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
                usort($conjuntos, function($a, $b) { return $b['promedio_final'] <=> $a['promedio_final']; });
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

    <div class="firma">
        <div>
            Jurado 1<br>
            ___________________<br>
            Firma
        </div>
        <div>
            Jurado 2<br>
            ___________________<br>
            Firma
        </div>
        <div>
            Presidente<br>
            ___________________<br>
            <?= htmlspecialchars($user['nombre']) ?>
        </div>
    </div>

    <div class="footer">
        Este reporte fue generado automáticamente por el sistema FRFCP. Todos los derechos reservados © <?= date('Y') ?>.
    </div>

</body>
</html>
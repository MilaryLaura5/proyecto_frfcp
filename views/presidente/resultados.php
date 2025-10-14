<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Resultados - <?= htmlspecialchars($concurso['nombre']) ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .header {
            background-color: #ffc107;
            color: #000;
            border-radius: 8px 8px 0 0;
        }

        .table th {
            font-weight: 600;
        }

        .medalla {
            font-size: 1.5em;
        }

        .actualizando {
            font-size: 0.9em;
            color: #6c757d;
            text-align: center;
            margin: 10px 0;
        }
    </style>
</head>

<body class="p-4">

    <div class="container">

        <!-- Encabezado -->
        <div class="header p-3 text-center mb-4">
            <h3><i class="bi bi-trophy-fill"></i> Resultados Finales</h3>
            <h5><?= htmlspecialchars($concurso['nombre']) ?></h5>
        </div>

        <!-- Criterios -->
        <div class="bg-white p-3 rounded shadow-sm mb-4">
            <h6><i class="bi bi-list-task"></i> Criterios Evaluados:</h6>
            <ul class="list-inline mb-0">
                <?php foreach ($criterios as $cr): ?>
                    <li class="list-inline-item me-3">
                        <?= htmlspecialchars($cr['nombre_criterio']) ?>
                        <small class="text-muted">(max: <?= $cr['puntaje_maximo'] ?>)</small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <!-- Zona dinámica: resultados -->
        <div id="resultados-container">
            <?php include 'resultado_tabla.php'; ?>
        </div>

        <!-- Mensaje de actualización -->
        <div class="actualizando" id="mensaje-actualizacion">
            Última actualización: <?= date('H:i:s') ?>
        </div>

        <!-- Botones de navegación -->
        <div class="text-center mt-4">
            <a href="index.php?page=presidente_seleccionar_concurso" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
            <a href="index.php?page=presidente_ver_resultados_por_serie&id_concurso=<?= $id_concurso ?>"
                class="btn btn-outline-info ms-2">
                <i class="bi bi-list-stars"></i> Por Series
            </a>
            <a href="index.php?page=exportar_resultados_pdf&id_concurso=<?= $id_concurso ?>"
                class="btn btn-primary ms-2">
                <i class="bi bi-file-earmark-pdf"></i> Exportar PDF
            </a>
        </div>

    </div>

    <!-- Script principal -->
    <script>
        // Función para actualizar resultados
        async function actualizarResultados() {
            try {
                const response = await fetch(`index.php?page=api_resultados&id_concurso=<?= $id_concurso ?>`);
                const data = await response.json();

                if (data.resultados) {
                    document.getElementById('resultados-container').innerHTML = generarTablaHTML(data.resultados);
                    document.getElementById('mensaje-actualizacion').textContent =
                        'Última actualización: ' + data.timestamp;
                }
            } catch (error) {
                console.error("Error al cargar resultados:", error);
                document.getElementById('mensaje-actualizacion').textContent =
                    "Error al actualizar. Revisa la conexión.";
            }
        }

        // Genera el HTML de la tabla
        function generarTablaHTML(resultados) {
            let html = `
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Conjunto</th>
                                <th>Serie</th>
                                <th>Tipo Danza</th>
                                <th>Puntaje Total</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

            resultados.forEach((r, idx) => {
                const esDescalificado = r.estado === 'descalificado';
                const filaClass = esDescalificado ? 'table-danger' : '';

                html += `<tr class="${filaClass}">`;

                // Posición con medalla
                html += '<td>';
                if (esDescalificado) {
                    html += '🚫';
                } else if (idx === 0) {
                    html += '🥇';
                } else if (idx === 1) {
                    html += '🥈';
                } else if (idx === 2) {
                    html += '🥉';
                } else {
                    html += (idx + 1) + '.';
                }
                html += '</td>';

                // Datos del conjunto
                html += `<td>${r.conjunto}</td>`;
                html += `<td>Serie ${r.numero_serie}</td>`;
                html += `<td>${r.tipo_danza}</td>`;
                html += `<td><strong>${esDescalificado ? 'DESCALIFICADO' : parseFloat(r.puntaje_total).toFixed(2)}</strong></td>`;
                html += '</tr>';
            });

            html += `</tbody></table></div></div></div>`;
            return html;
        }

        // Actualizar cada 10 segundos
        setInterval(actualizarResultados, 10000);

        // Primera actualización después de 10 segundos
        setTimeout(actualizarResultados, 10000);
    </script>

</body>

</html>
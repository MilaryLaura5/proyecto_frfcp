<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Jurados - FRFCP Admin</title>
    <!-- Bootstrap CSS (corregido: sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f0f0;
            /* Fondo suave con tono rojizo */
            font-family: 'Segoe UI', system-ui, sans-serif;
            min-height: 100vh;
            margin: 0;
        }

        .header-container {
            background: white;
            border-radius: 12px 12px 0 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            font-size: 1.75rem;
            font-weight: 600;
            color: #c9184a;
            /* 🔴 Rojo FRFCP */
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .card-header {
            background-color: #fff;
            border-bottom: 1px solid #f1e0e0;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .card-header h5 i {
            color: #c9184a;
        }

        .table th {
            font-weight: 600;
            color: #495057;
            background-color: #fdf2f2;
            /* Fondo claro rojizo */
        }

        .table td,
        .table th {
            vertical-align: middle;
        }

        /* Modal personalizado */
        #modalEnlace {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            z-index: 9999;
            padding: 1rem;
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-copy {
            font-size: 0.85rem;
            padding: 0.375rem 0.75rem;
        }

        @media (max-width: 768px) {
            .header-container {
                padding: 1rem;
            }

            .page-title {
                font-size: 1.5rem;
            }

            .row.g-3>div {
                margin-bottom: 0.5rem;
            }

            #modalEnlace {
                width: 90%;
                right: 5%;
                left: 5%;
            }
        }
    </style>
</head>

<body>

    <!-- Encabezado -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-person-badge me-2"></i> Gestionar Jurados
            </h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <div class="container-fluid px-4">

        <!-- Selección de concurso -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET">
                    <input type="hidden" name="page" value="admin_gestion_jurados">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label"><strong>Filtrar por Concurso</strong></label>
                            <select name="id_concurso" class="form-control form-select-lg" onchange="this.form.submit()">
                                <option value="">-- Todos los jurados --</option>
                                <?php foreach ($concursos as $c): ?>
                                    <option value="<?= $c['id_concurso'] ?>" <?= ($id_concurso == $c['id_concurso']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['nombre'], ENT_QUOTES, 'UTF-8') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Mensaje de credenciales (sin cambios de color) -->
        <?php if ($mostrarCredenciales): ?>
            <div class="alert alert-success alert-dismissible fade show rounded-4 mb-4" role="alert">
                <h5><i class="bi bi-check-circle"></i> ¡Jurado creado con éxito!</h5>
                <table class="table table-sm bg-light mb-3">
                    <tr>
                        <th>Usuario:</th>
                        <td><code><?= htmlspecialchars($credenciales['usuario']) ?></code></td>
                    </tr>
                    <tr>
                        <th>Contraseña:</th>
                        <td><code><?= htmlspecialchars($credenciales['contrasena']) ?></code></td>
                    </tr>
                </table>
                <p><strong>Enlace de acceso para el jurado:</strong></p>
                <?php
                $link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
                $link = rtrim($link, '/') . "/index.php?page=jurado_login&token=" . urlencode($credenciales['token']);
                ?>
                <div class="input-group mb-3">
                    <input type="text" class="form-control" value="<?= htmlspecialchars($link) ?>" id="linkToken" readonly>
                    <button class="btn btn-outline-secondary btn-copy" type="button" onclick="copiarNuevoEnlace()">
                        <i class="bi bi-copy"></i> Copiar
                    </button>
                </div>
                <small class="text-muted">
                    Entrega este enlace junto con el usuario y contraseña. El acceso expira al finalizar el concurso.
                </small>
                <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Errores (sin cambios) -->
        <?php if ($error === 'dni'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">❌ DNI inválido. Debe tener 8 dígitos.</div>
        <?php elseif ($error === 'datos'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">❌ Completa todos los campos correctamente.</div>
        <?php elseif ($error === 'usuario_invalido'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">❌ Usuario inválido. Usa letras, números, puntos o guiones.</div>
        <?php elseif ($error === 'db'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">❌ Error al guardar el jurado. Inténtalo de nuevo.</div>
        <?php elseif ($error === 'token_db'): ?>
            <div class="alert alert-danger alert-dismissible fade show rounded-4 mb-4" role="alert">❌ Error al generar el token. Contacta al administrador.</div>
        <?php elseif ($error === 'dni_duplicado'): ?>
            <div class="alert alert-warning alert-dismissible fade show rounded-4 mb-4" role="alert">⚠️ Ya existe un jurado con ese DNI.</div>
        <?php endif; ?>

        <!-- Listado de jurados -->
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="bi bi-list-ul"></i>
                    <?= $id_concurso ? 'Jurados asignados al concurso' : 'Todos los Jurados' ?>
                </h5>
                <span class="badge bg-secondary"><?= count($jurados) ?> encontrados</span>
            </div>
            <div class="card-body p-0">
                <?php if (count($jurados) > 0): ?>
                    <div class="table-responsive" style="max-height: 60vh; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>DNI</th>
                                    <th>Nombre</th>
                                    <th>Usuario</th>
                                    <th>Años Exp.</th>
                                    <?php if ($id_concurso): ?>
                                        <th>Criterio a Calificar</th>
                                        <th>Token</th>
                                        <th>Estado</th>
                                        <th>Acciones</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jurados as $j): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($j['dni']) ?></td>
                                        <td><?= htmlspecialchars($j['nombre'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($j['usuario']) ?></td>
                                        <td><?= (int)($j['años_experiencia'] ?? 0) ?></td>

                                        <?php if ($id_concurso): ?>
                                            <td>
                                                <?php if (!empty($j['criterio_calificado'])): ?>
                                                    <?= htmlspecialchars($j['criterio_calificado']) ?><br>
                                                    <small class="text-muted">
                                                        Máx: <?= number_format($j['puntaje_maximo'] ?? 0, 2) ?> pts
                                                    </small>
                                                <?php else: ?>
                                                    <em class="text-muted">No asignado</em>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($j['token'])): ?>
                                                    <code style="font-size: 0.9em;"><?= substr($j['token'], 0, 16) ?>...</code>
                                                <?php else: ?>
                                                    <small class="text-muted">Sin token</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($j['token']) && !empty($j['fecha_expiracion'])): ?>
                                                    <?php
                                                    $expirado = new DateTime($j['fecha_expiracion']) < new DateTime();
                                                    ?>
                                                    <span class="badge bg-<?= $expirado ? 'secondary' : 'warning' ?>">
                                                        <?= $expirado ? 'Expirado' : 'Activo' ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">No asignado</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($j['token'])): ?>
                                                    <button class="btn btn-sm btn-outline-primary"
                                                        onclick="mostrarEnlace('<?= addslashes($j['token']) ?>')">
                                                        <i class="bi bi-link-45deg"></i> Ver enlace
                                                    </button>
                                                <?php endif; ?>
                                            </td>
                                        <?php endif; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-people display-4 text-muted"></i>
                        <p class="lead text-muted mt-3">
                            <?= $id_concurso ? 'No hay jurados asignados a este concurso.' : 'No hay jurados registrados.' ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón fijo -->
        <div id="boton-fijo" class="fixed-bottom bg-white p-3 shadow-sm" style="z-index: 1000;">
            <div class="container-fluid px-4 pb-5">
                <div class="d-flex justify-content-end align-items-center">
                    <?php if ($id_concurso): ?>
                        <a href="index.php?page=admin_crear_jurado&id_concurso=<?= $id_concurso ?>"
                            class="btn btn-success btn-lg px-4">
                            <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                        </a>
                    <?php else: ?>
                        <div class="text-end">
                            <button class="btn btn-success btn-lg px-4" disabled title="Primero selecciona un concurso">
                                <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                            </button>
                            <br>
                            <small class="text-muted mt-2 d-block">Para crear un jurado, primero selecciona un concurso.</small>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para enlaces existentes -->
    <div id="modalEnlace" class="alert alert-info">
        <strong>Enlace de acceso:</strong><br>
        <input type="text" id="enlaceInput" readonly class="form-control form-control-sm mb-2">
        <div class="d-grid gap-2 d-md-flex">
            <button class="btn btn-sm btn-secondary btn-copy" onclick="copiarExistente()">Copiar</button>
            <button class="btn btn-sm btn-outline-danger" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function copiarNuevoEnlace() {
            const input = document.getElementById('linkToken');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado al portapapeles');
        }

        function mostrarEnlace(token) {
            const basePath = `<?= dirname($_SERVER['SCRIPT_NAME']) ?>`;
            const link = `${basePath}/index.php?page=jurado_login&token=${encodeURIComponent(token)}`;
            document.getElementById('enlaceInput').value = link;
            document.getElementById('modalEnlace').style.display = 'block';
        }

        function copiarExistente() {
            const input = document.getElementById('enlaceInput');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado');
        }

        function cerrarModal() {
            document.getElementById('modalEnlace').style.display = 'none';
        }
    </script>
</body>

</html>
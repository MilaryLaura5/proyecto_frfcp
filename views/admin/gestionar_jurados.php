<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestionar Jurados - FRFCP Admin</title>
    <!-- ✅ Corrección: espacios eliminados -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 900px;
            margin: 80px auto;
        }

        #modalEnlace {
            display: none;
            position: fixed;
            top: 20px;
            right: 20px;
            width: 400px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-badge me-2 text-primary"></i> Gestionar Jurados</h2>
            <a href="index.php?page=admin_dashboard" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Selección de concurso -->
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <form method="GET">
                    <input type="hidden" name="page" value="admin_gestion_jurados">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label"><strong>Filtrar por Concurso</strong></label>
                            <select name="id_concurso" class="form-control" onchange="this.form.submit()">
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

        <!-- ✅ Mensaje: Credenciales del jurado -->
        <?php if ($mostrarCredenciales): ?>
            <div class="alert alert-success">
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
                    <button class="btn btn-outline-secondary" type="button" onclick="copiarNuevoEnlace()">
                        <i class="bi bi-copy"></i> Copiar
                    </button>
                </div>

                <small class="text-muted">
                    Entrega este enlace junto con el usuario y contraseña. El acceso expira al finalizar el concurso.
                </small>
            </div>
        <?php endif; ?>

        <!-- Errores -->
        <?php if ($error === 'dni'): ?>
            <div class="alert alert-danger">❌ DNI inválido. Debe tener 8 dígitos.</div>
        <?php elseif ($error === 'datos'): ?>
            <div class="alert alert-danger">❌ Completa todos los campos correctamente.</div>
        <?php elseif ($error === 'usuario_invalido'): ?>
            <div class="alert alert-danger">❌ Usuario inválido. Usa letras, números, puntos o guiones.</div>
        <?php elseif ($error === 'db'): ?>
            <div class="alert alert-danger">❌ Error al guardar el jurado. Inténtalo de nuevo.</div>
        <?php elseif ($error === 'token_db'): ?>
            <div class="alert alert-danger">❌ Error al generar el token. Contacta al administrador.</div>
        <?php elseif ($error === 'dni_duplicado'): ?>
            <div class="alert alert-warning">⚠️ Ya existe un jurado con ese DNI.</div>
        <?php endif; ?>

        <!-- Listado de jurados -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5><i class="bi bi-list-ul"></i>
                    <?= $id_concurso ? 'Jurados asignados al concurso' : 'Todos los Jurados' ?>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (count($jurados) > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>DNI</th>
                                    <th>Usuario</th>
                                    <th>Especialidad</th>
                                    <th>Años Exp.</th>
                                    <th>Token</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($jurados as $j): ?>
                                    <tr>
                                        <td><?= $j['dni'] ?></td>
                                        <td><?= htmlspecialchars($j['usuario']) ?></td>
                                        <td><?= ucfirst($j['especialidad']) ?></td>
                                        <td><?= $j['años_experiencia'] ?></td>
                                        <td>
                                            <?php if (!empty($j['token'])): ?>
                                                <code style="font-size: 0.9em;"><?= $j['token'] ?></code>
                                            <?php else: ?>
                                                <small class="text-muted">Sin token</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($j['token'])): ?>
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
                                                    onclick="mostrarEnlace('<?= $j['token'] ?>')">
                                                    <i class="bi bi-link-45deg"></i> Ver enlace
                                                </button>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-4 m-0">
                        No hay jurados registrados o asignados a este concurso.
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Botón: Nuevo Jurado + Token -->
        <div class="mt-4 text-end">
            <?php if ($id_concurso): ?>
                <a href="index.php?page=admin_crear_jurado&id_concurso=<?= $id_concurso ?>"
                    class="btn btn-success">
                    <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                </a>
            <?php else: ?>
                <button class="btn btn-success" disabled title="Primero selecciona un concurso">
                    <i class="bi bi-plus-circle"></i> Nuevo Jurado + Token
                </button>
                <br>
                <small class="text-muted">Para crear un jurado, primero selecciona un concurso.</small>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal para mostrar enlaces existentes -->
    <div id="modalEnlace" class="alert alert-info">
        <strong>Enlace de acceso:</strong><br>
        <input type="text" id="enlaceInput" readonly class="form-control form-control-sm mb-2">
        <div class="d-grid gap-2 d-md-flex">
            <button class="btn btn-sm btn-secondary" onclick="copiarExistente()">Copiar</button>
            <button class="btn btn-sm btn-outline-danger" onclick="cerrarModal()">Cerrar</button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Para nuevo token generado
        function copiarNuevoEnlace() {
            const input = document.getElementById('linkToken');
            input.select();
            document.execCommand('copy');
            alert('✅ Enlace copiado al portapapeles');
        }

        // Para ver enlace existente
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
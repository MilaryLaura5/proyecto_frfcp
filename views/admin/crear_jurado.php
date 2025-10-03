<?php
require_once __DIR__ . '/../../helpers/auth.php';
redirect_if_not_admin();
$user = auth();

$id_concurso = $_GET['id_concurso'] ?? null;

if (!$id_concurso) {
    header('Location: index.php?page=admin_gestion_concursos&error=no_concurso');
    exit;
}

global $pdo;
$stmt = $pdo->prepare("SELECT * FROM Concurso WHERE id_concurso = ?");
$stmt->execute([$id_concurso]);
$concurso = $stmt->fetch();

if (!$concurso) {
    header('Location: index.php?page=admin_gestion_concursos&error=invalido');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Jurado - FRFCP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
        }

        .container-main {
            max-width: 700px;
            margin: 80px auto;
        }

        .alert-search {
            font-size: 0.95em;
            margin-bottom: 0;
        }
    </style>
</head>

<body>
    <div class="container-main">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-person-badge me-2 text-success"></i> Nuevo Jurado</h2>
            <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>

        <!-- Búsqueda por DNI -->
        <div class="mb-3">
            <label class="form-label"><strong>Buscar por DNI</strong></label>
            <div class="input-group">
                <input type="text" class="form-control" id="buscarDni" placeholder="12345678" maxlength="8" pattern="\d{8}">
                <button class="btn btn-outline-secondary" type="button" onclick="buscarJurado()">Buscar</button>
            </div>
            <div id="resultadoBusqueda" class="mt-2"></div>
        </div>

        <hr>

        <form method="POST" action="index.php?page=admin_guardar_jurado">
            <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>DNI</strong></label>
                    <input type="text" class="form-control" name="dni" id="dni" maxlength="8" pattern="\d{8}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Nombre Completo</strong></label>
                    <input type="text" class="form-control" name="nombre" id="nombre" required oninput="generarUsuario()">
                </div>
            </div>

            <div class="mt-3">
                <label class="form-label"><strong>Usuario (autogenerado)</strong></label>
                <input type="text" class="form-control" name="usuario" id="usuario" readonly required>
            </div>

            <div class="mb-3">
                <label><strong>Contraseña</strong></label>
                <input type="text" class="form-control" name="contrasena" placeholder="Ingresa una contraseña segura" required>
            </div>

            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <label class="form-label"><strong>Especialidad</strong></label>
                    <select class="form-control" name="especialidad" id="especialidad" required>
                        <option value="">Selecciona...</option>
                        <option value="Presentación y vestimenta">Presentación y Vestimenta</option>
                        <option value="Melodía, armonía y ritmo">Melodía, Armonía y Ritmo</option>
                        <option value="Coreografía">Coreografía</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label"><strong>Años de Experiencia</strong></label>
                    <input type="number" class="form-control" name="años_experiencia" id="años_experiencia" min="1" max="50" required>
                </div>
            </div>
            <div class="mb-3">
                <label><strong>Duración del enlace (personalizable)</strong></label>
                <div class="row g-2">
                    <div class="col-4">
                        <input type="number" name="dias" class="form-control" min="0" max="365" placeholder="Días">
                    </div>
                    <div class="col-4">
                        <input type="number" name="horas" class="form-control" min="0" max="23" placeholder="Horas">
                    </div>
                    <div class="col-4">
                        <input type="number" name="minutos" class="form-control" min="0" max="59" placeholder="Minutos">
                    </div>
                </div>
                <small class="text-muted">Ej: 0 días, 1 hora y 20 minutos → el enlace expira en 1h20m</small>
            </div>
            <div class="d-grid gap-2 d-md-flex mt-4">
                <button type="submit" class="btn btn-success">Crear Jurado + Generar Token</button>
                <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary">Cancelar</a>
            </div>

        </form>
    </div>

    <script>
        // Autogenerar usuario desde nombre
        function generarUsuario() {
            const nombre = document.getElementById('nombre').value.trim();
            if (!nombre) return;

            const partes = nombre.split(/\s+/);
            const inicial = partes[0][0]?.toLowerCase() || '';
            const apellido = partes[1]?.toLowerCase() || '';

            let usuario = inicial + apellido;

            // Eliminar tildes y caracteres extraños
            usuario = usuario.normalize("NFD").replace(/[\u0300-\u036f]/g, "");

            fetch(`index.php?page=admin_verificar_usuario&usuario=${usuario}`)
                .then(r => r.json())
                .then(data => {
                    document.getElementById('usuario').value = data.usuario;
                });
        }

        // Buscar jurado por DNI
        function buscarJurado() {
            const dni = document.getElementById('buscarDni').value;
            if (!dni || dni.length !== 8 || !/^\d+$/.test(dni)) {
                alert('DNI inválido');
                return;
            }

            fetch(`index.php?page=admin_buscar_jurado&dni=${dni}`)
                .then(r => r.json())
                .then(data => {
                    if (data.existe) {
                        document.getElementById('dni').value = dni;
                        document.getElementById('nombre').value = data.nombre;
                        document.getElementById('usuario').value = data.usuario;
                        document.getElementById('especialidad').value = data.especialidad;
                        document.getElementById('años_experiencia').value = data.años_experiencia;
                        document.getElementById('resultadoBusqueda').innerHTML = `
                            <div class="alert alert-warning alert-search">
                                ⚠️ Jurado encontrado. Puedes editarlo si es necesario.
                            </div>`;
                    } else {
                        document.getElementById('dni').value = dni;
                        document.getElementById('nombre').focus();
                        document.getElementById('resultadoBusqueda').innerHTML = `
                            <div class="alert alert-success alert-search">
                                ✅ DNI disponible. Registra al nuevo jurado.
                            </div>`;
                    }
                })
                .catch(() => {
                    document.getElementById('resultadoBusqueda').innerHTML = `
                        <div class="alert alert-danger alert-search">❌ Error al buscar.</div>`;
                });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
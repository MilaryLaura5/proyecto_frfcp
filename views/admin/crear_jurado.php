<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Nuevo Jurado - FRFCP</title>
    <!-- ✅ Bootstrap CSS (sin espacios) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
            color: #0056b3;
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
            border-bottom: 1px solid #e9ecef;
            padding: 1rem 1.5rem;
            font-weight: 600;
            color: #333;
        }

        .alert-search {
            font-size: 0.9em;
            margin-bottom: 0;
            padding: 0.5rem;
        }

        #indicadores-contrasena .badge {
            margin-right: 0.5rem;
            margin-top: 0.3rem;
            padding: 0.5em 0.8em;
            font-size: 0.85em;
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
        }
    </style>
</head>

<body>

    <!-- Encabezado con ancho completo -->
    <div class="header-container">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="page-title">
                <i class="bi bi-person-badge me-2 text-success"></i> Nuevo Jurado
            </h2>
            <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </div>

    <!-- Contenido principal con ancho completo -->
    <div class="container-fluid px-4">

        <!-- Búsqueda por DNI -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5><i class="bi bi-search"></i> Buscar por DNI</h5>
                <p class="text-muted">Verifica si ya existe un jurado antes de crear uno nuevo.</p>

                <div class="input-group mb-3">
                    <input type="text" class="form-control form-control-lg" id="buscarDni" placeholder="Ingresa 8 dígitos" maxlength="8" pattern="\d{8}">
                    <button class="btn btn-outline-secondary btn-lg" type="button" onclick="buscarJurado()">
                        <i class="bi bi-search"></i> Buscar
                    </button>
                </div>
                <div id="resultadoBusqueda"></div>
            </div>
        </div>

        <hr class="my-4">

        <!-- Formulario: Nuevo Jurado -->
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="POST" action="index.php?page=admin_guardar_jurado" onsubmit="return validarFormulario()">
                    <input type="hidden" name="id_concurso" value="<?= $id_concurso ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>DNI</strong></label>
                            <input type="text"
                                class="form-control form-control-lg"
                                name="dni"
                                id="dni"
                                maxlength="8"
                                pattern="\d{8}"
                                required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Nombre Completo</strong></label>
                            <input type="text"
                                class="form-control form-control-lg"
                                name="nombre"
                                id="nombre"
                                required
                                oninput="generarUsuario()">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label"><strong>Usuario (autogenerado)</strong></label>
                        <input type="text"
                            class="form-control form-control-lg"
                            name="usuario"
                            id="usuario"
                            readonly
                            required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Contraseña</strong></label>
                        <input type="text"
                            class="form-control form-control-lg"
                            name="contrasena"
                            id="contrasena"
                            placeholder="Ingresa una contraseña segura"
                            required>

                        <!-- Mensaje dinámico -->
                        <div class="mt-2 small" id="mensaje-contrasena"></div>
                    </div>

                    <div class="row g-3 mt-3">
                        <div class="col-md-6">
                            <label class="form-label"><strong>Criterio a Calificar</strong></label>
                            <select class="form-control form-select-lg" name="id_criterio_concurso" id="criterio" required>
                                <option value="">Selecciona un criterio...</option>
                                <?php
                                // Obtener criterios del concurso actual
                                require_once __DIR__ . '/../../models/CriterioConcurso.php';
                                $criterios = CriterioConcurso::porConcurso($id_concurso);

                                foreach ($criterios as $c):
                                ?>
                                    <option value="<?= $c['id_criterio_concurso'] ?>">
                                        <?= htmlspecialchars($c['nombre_criterio']) ?> (<?= $c['puntaje_maximo'] ?>%)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><strong>Años de Experiencia</strong></label>
                            <input type="number"
                                class="form-control form-control-lg"
                                name="años_experiencia"
                                id="años_experiencia"
                                min="1"
                                max="50"
                                required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><strong>Duración del enlace (personalizable)</strong></label>
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
                        <button type="submit" class="btn btn-success btn-lg px-4">Crear Jurado + Generar Token</button>
                        <a href="index.php?page=admin_gestion_jurados&id_concurso=<?= $id_concurso ?>" class="btn btn-secondary btn-lg px-4">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ✅ Bootstrap JS (sin espacios) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function generarUsuario() {
            const nombre = document.getElementById('nombre').value.trim();

            if (!nombre || nombre.split(/\s+/).filter(p => p).length < 2) {
                document.getElementById('usuario').value = '';
                return;
            }

            fetch(`index.php?page=admin_verificar_usuario&nombre=${encodeURIComponent(nombre)}`)
                .then(r => r.json())
                .then(data => {
                    if (data.usuario) {
                        document.getElementById('usuario').value = data.usuario;
                    } else {
                        console.error('Error del servidor:', data.error);
                        generarUsuarioLocal(nombre);
                    }
                })
                .catch(err => {
                    console.error('Error al verificar usuario:', err);
                    generarUsuarioLocal(nombre);
                });
        }

        function generarUsuarioLocal(nombreCompleto) {
            const partes = nombreCompleto.split(/\s+/).filter(p => p);
            if (partes.length < 2) {
                document.getElementById('usuario').value = partes[0].toLowerCase();
                return;
            }

            const normalizar = (str) => str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
            const n = normalizar(partes[0]);
            const a1 = normalizar(partes[1]);
            const a2 = partes[2] ? normalizar(partes[2]) : '';

            const intentos = [
                n[0] + a1,
                n.slice(0, 2) + a1,
                n.slice(0, 3) + a1,
                a2 ? n[0] + a2 : ''
            ].filter(u => u);

            for (const usuario of intentos) {
                document.getElementById('usuario').value = usuario;
                return;
            }

            document.getElementById('usuario').value = n[0] + a1;
        }

        function validarFormulario() {
            const usuario = document.getElementById('usuario').value;
            if (!usuario) {
                alert('Debe generarse un usuario válido.');
                return false;
            }
            return true;
        }

        function buscarJurado() {
            const dni = document.getElementById('buscarDni').value;
            const resultDiv = document.getElementById('resultadoBusqueda');

            if (!dni || dni.length !== 8 || !/^\d+$/.test(dni)) {
                resultDiv.innerHTML = `
                    <div class="alert alert-danger alert-search mt-2">
                        ❌ DNI inválido. Ingresa 8 dígitos.
                    </div>`;
                return;
            }

            resultDiv.innerHTML = '<div class="text-muted">Buscando...</div>';

            fetch(`index.php?page=admin_buscar_jurado_por_dni&dni=${dni}`)
                .then(r => {
                    if (!r.ok) throw new Error('Redirección o error');
                    return r.json();
                })
                .then(data => {
                    if (data.existe) {
                        document.getElementById('dni').value = dni;
                        document.getElementById('nombre').value = data.nombre;
                        document.getElementById('años_experiencia').value = data.años_experiencia;

                        if (data.usuario) {
                            document.getElementById('usuario').value = data.usuario;
                        } else {
                            generarUsuario();
                        }

                        resultDiv.innerHTML = `
                            <div class="alert alert-warning alert-search mt-2">
                                ⚠️ Jurado encontrado. Puedes editarlo si es necesario.
                            </div>`;
                    } else {
                        document.getElementById('dni').value = dni;
                        document.getElementById('nombre').value = '';
                        document.getElementById('años_experiencia').value = '';
                        document.getElementById('usuario').value = '';
                        document.getElementById('nombre').focus();

                        resultDiv.innerHTML = `
                            <div class="alert alert-success alert-search mt-2">
                                ✅ DNI disponible. Registra al nuevo jurado.
                            </div>`;
                    }
                })
                .catch(err => {
                    console.error('Error al buscar jurado:', err);
                    resultDiv.innerHTML = `
                        <div class="alert alert-danger alert-search mt-2">
                            ❌ Error: ${err.message}
                        </div>`;
                });
        }

        function validarContrasena() {
            const contrasena = document.getElementById('contrasena').value;
            const mensajeDiv = document.getElementById('mensaje-contrasena');

            // Reiniciar mensaje
            mensajeDiv.innerHTML = '';
            mensajeDiv.style.color = '#dc3545'; // Rojo por defecto

            // Criterios
            const tiene8chars = contrasena.length >= 8;
            const tieneMayuscula = /[A-Z]/.test(contrasena);
            const tieneMinuscula = /[a-z]/.test(contrasena);
            const tieneNumero = /[0-9]/.test(contrasena);
            const tieneEspecial = /[!@#$%&*]/.test(contrasena); // Puedes ajustar estos caracteres

            // Lista de faltantes
            const faltantes = [];

            if (!tiene8chars) faltantes.push("mínimo 8 caracteres");
            if (!tieneMayuscula) faltantes.push("una letra mayúscula");
            if (!tieneMinuscula) faltantes.push("una letra minúscula");
            if (!tieneNumero) faltantes.push("un número");
            if (!tieneEspecial) faltantes.push("un carácter especial (!@#$%&*)");

            // Mostrar mensaje según lo que falte
            if (faltantes.length === 0) {
                mensajeDiv.innerHTML = '<strong style="color: #198754;">✅ Contraseña segura</strong>';
                document.querySelector('[type="submit"]').disabled = false;
            } else {
                const ultimo = faltantes.pop();
                const lista = faltantes.length > 0 ? faltantes.join(', ') + ' y ' + ultimo : ultimo;
                mensajeDiv.innerHTML = `⚠️ La contraseña debe tener al menos ${lista}.`;
                document.querySelector('[type="submit"]').disabled = true;
            }
        }

        // Escuchar cambios en tiempo real
        document.getElementById('contrasena').addEventListener('input', validarContrasena);

        function actualizarBadge(id, esValido) {
            const badge = document.getElementById(id);
            badge.className = 'badge ' + (esValido ? 'bg-success' : 'bg-danger');
        }

        // Escuchar cambios en la contraseña
        document.getElementById('contrasena').addEventListener('input', validarContrasena);
    </script>
</body>

</html>
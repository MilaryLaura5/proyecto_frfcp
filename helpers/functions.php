<?php

// helpers/functions.php

function generarUsuario($nombre_completo)
{
    $nombre = iconv('UTF-8', 'ASCII//TRANSLIT', $nombre_completo);
    $partes = preg_split('/\s+/', trim($nombre));
    if (count($partes) < 2) {
        return strtolower($partes[0]);
    }
    $inicial = mb_strtolower($partes[0][0]);
    $apellido = mb_strtolower($partes[1]);
    $usuario = $inicial . $apellido;
    global $pdo;
    $i = '';
    while (true) {
        $stmt = $pdo->prepare("SELECT id_usuario FROM Usuario WHERE usuario = ?");
        $stmt->execute([$usuario . $i]);
        if ($stmt->rowCount() == 0) {
            return $usuario . $i;
        }
        $i = $i === '' ? 1 : $i + 1;
    }
}

function generarContrasenaSegura($longitud = 10)
{
    $mayusculas = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $minusculas = 'abcdefghijklmnopqrstuvwxyz';
    $numeros = '0123456789';
    $especiales = '!@#$%&*';
    $password = '';
    $password .= $mayusculas[random_int(0, strlen($mayusculas) - 1)];
    $password .= $minusculas[random_int(0, strlen($minusculas) - 1)];
    $password .= $numeros[random_int(0, strlen($numeros) - 1)];
    $password .= $especiales[random_int(0, strlen($especiales) - 1)];
    $todos = $mayusculas . $minusculas . $numeros . $especiales;
    for ($i = 4; $i < $longitud; $i++) {
        $password .= $todos[random_int(0, strlen($todos) - 1)];
    }
    return str_shuffle($password);
}

function normalizarTextos($texto)
{
    return strtolower(
        trim(
            preg_replace('/[\x{0300}-\x{036f}]/u', '', $texto)
        )
    );
}
/*
function normalizarTexto($texto)
{
    return strtolower(
        trim(
            preg_replace('/[\x{0300}-\x{036f}]/u', '', $texto)
        )
    );
}*/

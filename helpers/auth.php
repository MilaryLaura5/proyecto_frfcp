<?php

function auth()
{
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    return $_SESSION['user'];
}

function is_admin()
{
    $user = auth();
    return isset($user['rol']) && strtolower($user['rol']) === 'administrador';
}

function is_jurado()
{
    $user = auth();
    return isset($user['rol']) && strtolower($user['rol']) === 'jurado';
}


// ✅ CORREGIDO: Ahora el Admin también puede ser Presidente

function is_presidente()
{
    $user = auth();
    // ✅ Permite tanto a Presidente como a Administrador
    return isset($user['rol']) &&
        in_array(strtolower($user['rol']), ['presidente', 'administrador']);
}

function redirect_if_not_admin()
{
    if (!is_admin()) {
        header('Location: index.php?page=login&error=permiso');
        exit;
    }
}

function redirect_if_not_jurado()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Jurado') {
        header('Location: index.php?page=login');
        exit;
    }
}


// ✅ CORREGIDO: Usa is_presidente() que ahora permite al Admin



function redirect_if_not_presidente()
{
    if (!is_presidente()) {
        header('Location: index.php?page=login');
        exit;
    }
}
function normalizarTexto($string)
{
    $replacements = [
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ú' => 'u',
        'Á' => 'A',
        'É' => 'E',
        'Í' => 'I',
        'Ó' => 'O',
        'Ú' => 'U',
        'ñ' => 'n',
        'Ñ' => 'N'
    ];
    return str_replace(array_keys($replacements), array_values($replacements), $string);
}

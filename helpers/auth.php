<?php

function auth() {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?page=login');
        exit;
    }
    return $_SESSION['user'];
}

function is_admin() {
    $user = auth();
    return isset($user['rol']) && strtolower($user['rol']) === 'administrador';
}

function is_jurado() {
    $user = auth();
    return isset($user['rol']) && strtolower($user['rol']) === 'jurado';
}

function is_presidente() {
    $user = auth();
    return isset($user['rol']) && strtolower($user['rol']) === 'presidente';
}

function redirect_if_not_admin() {
    if (!is_admin()) {
        header('Location: index.php?page=login&error=permiso');
        exit;
    }
}

function redirect_if_not_jurado() {
    if (!is_jurado()) {
        header('Location: index.php?page=login&error=permiso');
        exit;
    }
}


function redirect_if_not_presidente() {
    if (!is_presidente()) {
        header('Location: index.php?page=login&error=permiso');
        exit;
    }
}
?>
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
    return $user['rol'] === 'Administrador';
}

function is_jurado() {
    $user = auth();
    return $user['rol'] === 'Jurado';
}

function is_presidente() {
    $user = auth();
    return $user['rol'] === 'Presidente';
}

function redirect_if_not_admin() {
    if (!is_admin()) {
        header('Location: index.php?page=login');
        exit;
    }
}
?>
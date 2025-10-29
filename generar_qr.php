<?php
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== 'Administrador') {
    die("Acceso denegado");
}

$token = $_GET['token'] ?? null;
if (!$token) {
    die("Token no proporcionado");
}

// En lugar de 'localhost' o 'proyecto_frfcp', usa tu IP local
$baseUrl = "http://192.168.1.8/FRFCP";
$url = "$baseUrl/index.php?page=jurado_login&token=" . urlencode($token);
// Redirigir a un servicio QR público
$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url);
header("Location: $qrUrl");
exit;

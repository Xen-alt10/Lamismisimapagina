<?php
require_once __DIR__ . '/config.php';

// Acepta GET o POST para poder usar <a href="php/logout.php">
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Si fue fetch/AJAX devolvemos JSON, si fue navegación redirigimos
$accept = $_SERVER['HTTP_ACCEPT'] ?? '';
if (str_contains($accept, 'application/json') || $_SERVER['REQUEST_METHOD'] === 'POST') {
    json_response(['ok' => true, 'msg' => 'Sesión cerrada']);
} else {
    header('Location: ../login.html');
    exit;
}

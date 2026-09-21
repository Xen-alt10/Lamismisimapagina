<?php
require_once __DIR__ . '/config.php';

require_method('POST');
$data = get_input();

// El front manda username, pero el login viejo mandaba email. Soportamos ambos.
$username = trim($data['username'] ?? $data['email'] ?? '');
$password = $data['password'] ?? $data['pass'] ?? '';

if ($username === '' || $password === '') {
    json_response(['ok' => false, 'error' => 'Faltan credenciales'], 400);
}

$stmt = $pdo->prepare("SELECT id_usuario, username, pass_hash FROM usuarios WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['pass_hash'])) {
    json_response(['ok' => false, 'error' => 'Usuario o contraseña incorrectos'], 401);
}

// Re-hash si el algoritmo cambió
if (password_needs_rehash($user['pass_hash'], PASSWORD_DEFAULT)) {
    $newHash = password_hash($password, PASSWORD_DEFAULT);
    $up = $pdo->prepare("UPDATE usuarios SET pass_hash = ? WHERE id_usuario = ?");
    $up->execute([$newHash, $user['id_usuario']]);
}

session_regenerate_id(true);
$_SESSION['id_usuario'] = (int)$user['id_usuario'];
$_SESSION['username'] = $user['username'];

// Traer XP para devolverla de una
$xpStmt = $pdo->prepare("SELECT nivel, xp_necesaria, xp_ganada FROM xp_usuario WHERE id_usuario = ? LIMIT 1");
$xpStmt->execute([$user['id_usuario']]);
$xp = $xpStmt->fetch() ?: ['nivel' => 1, 'xp_necesaria' => 500, 'xp_ganada' => 0];

json_response([
    'ok' => true,
    'msg' => 'Login OK',
    'user' => [
        'id_usuario' => (int)$user['id_usuario'],
        'username' => $user['username'],
        'nivel' => (int)$xp['nivel'],
        'xp_ganada' => (int)$xp['xp_ganada'],
        'xp_necesaria' => (int)$xp['xp_necesaria'],
    ]
]);

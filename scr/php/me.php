<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['id_usuario'])) {
    json_response(['ok' => false, 'logged' => false, 'error' => 'No logueado'], 401);
}

$id = (int)$_SESSION['id_usuario'];
$stmt = $pdo->prepare("
    SELECT u.id_usuario, u.username, x.nivel, x.xp_necesaria, x.xp_ganada
    FROM usuarios u
    LEFT JOIN xp_usuario x ON x.id_usuario = u.id_usuario
    WHERE u.id_usuario = ?
    LIMIT 1
");
$stmt->execute([$id]);
$row = $stmt->fetch();

if (!$row) {
    // sesión huérfana
    session_destroy();
    json_response(['ok' => false, 'logged' => false, 'error' => 'Usuario no encontrado'], 404);
}

json_response([
    'ok' => true,
    'logged' => true,
    'user' => [
        'id_usuario' => (int)$row['id_usuario'],
        'username' => $row['username'],
        'nivel' => (int)($row['nivel'] ?? 1),
        'xp_ganada' => (int)($row['xp_ganada'] ?? 0),
        'xp_necesaria' => (int)($row['xp_necesaria'] ?? 500),
    ]
]);

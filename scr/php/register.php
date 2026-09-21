<?php
require_once __DIR__ . '/config.php';

require_method('POST');
$data = get_input();

$username = trim($data['username'] ?? '');
$password = $data['password'] ?? $data['pass'] ?? '';
$confirm  = $data['confirm'] ?? $data['password_confirm'] ?? '';

// Validaciones
if ($username === '' || $password === '') {
    json_response(['ok' => false, 'error' => 'Usuario y contraseña son obligatorios'], 400);
}
if (strlen($username) < 3 || strlen($username) > 20) {
    json_response(['ok' => false, 'error' => 'El usuario debe tener entre 3 y 20 caracteres'], 400);
}
if (!preg_match('/^[a-zA-Z0-9_\-\.]+$/', $username)) {
    json_response(['ok' => false, 'error' => 'Usuario solo puede tener letras, números, _ - .'], 400);
}
if (strlen($password) < 4) {
    json_response(['ok' => false, 'error' => 'La contraseña debe tener al menos 4 caracteres'], 400);
}
if ($confirm !== '' && $password !== $confirm) {
    json_response(['ok' => false, 'error' => 'Las contraseñas no coinciden'], 400);
}

// ¿Usuario ya existe?
$stmt = $pdo->prepare("SELECT id_usuario FROM usuarios WHERE username = ? LIMIT 1");
$stmt->execute([$username]);
if ($stmt->fetch()) {
    json_response(['ok' => false, 'error' => 'Ese usuario ya existe'], 409);
}

// Hash seguro (bcrypt)
$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $pdo->beginTransaction();
    // FIX del FK invertido del dump original: desactivamos checks para poder insertar en orden correcto
    // Así funciona tanto si importaste el .sql original como el corregido.
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");

    $stmt = $pdo->prepare("INSERT INTO usuarios (username, pass_hash) VALUES (?, ?)");
    $stmt->execute([$username, $hash]);
    $id = (int)$pdo->lastInsertId();

    // xp inicial: nivel 1, 500 necesarios, 0 ganados
    $stmt = $pdo->prepare("INSERT INTO xp_usuario (id_usuario, nivel, xp_necesaria, xp_ganada) VALUES (?, 1, 500, 0)");
    $stmt->execute([$id]);

    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    $pdo->commit();

    // Auto-login post registro
    $_SESSION['id_usuario'] = $id;
    $_SESSION['username'] = $username;

    json_response(['ok' => true, 'msg' => 'Cuenta creada', 'id_usuario' => $id, 'username' => $username]);

} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
        // reactivar checks aunque falle
        try { $pdo->exec("SET FOREIGN_KEY_CHECKS=1"); } catch (Exception $ignored) {}
    }
    // 23000 = duplicado / FK
    if ($e->getCode() === '23000') {
        json_response(['ok' => false, 'error' => 'No se pudo crear el usuario (duplicado o FK). Revisá que la BD esté bien importada.'], 409);
    }
    json_response(['ok' => false, 'error' => 'Error en registro', 'detail' => $e->getMessage()], 500);
}

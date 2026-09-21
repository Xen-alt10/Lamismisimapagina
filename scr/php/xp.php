<?php
require_once __DIR__ . '/config.php';

if (empty($_SESSION['id_usuario'])) {
    json_response(['ok' => false, 'error' => 'Tenés que loguearte'], 401);
}
require_method('POST');
$data = get_input();
$ganada = (int)($data['xp'] ?? $data['xp_ganada'] ?? 0);

if ($ganada <= 0) {
    json_response(['ok' => false, 'error' => 'XP inválida'], 400);
}
if ($ganada > 1000) $ganada = 1000; // anti-trampa base

$id = (int)$_SESSION['id_usuario'];

// Transacción con level-up
try {
    $pdo->beginTransaction();
    // Bloquear fila
    $stmt = $pdo->prepare("SELECT nivel, xp_necesaria, xp_ganada FROM xp_usuario WHERE id_usuario = ? FOR UPDATE");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if (!$row) {
        // crear si no existe (por si FK vieja)
        $pdo->prepare("INSERT INTO xp_usuario (id_usuario, nivel, xp_necesaria, xp_ganada) VALUES (?,1,500,0)")
            ->execute([$id]);
        $row = ['nivel'=>1, 'xp_necesaria'=>500, 'xp_ganada'=>0];
    }

    $nivel = (int)$row['nivel'];
    $xpNec = (int)$row['xp_necesaria'];
    $xpGan = (int)$row['xp_ganada'] + $ganada;

    // Level up loop: si supera necesaria, sube nivel y aumenta dificultad
    $subio = 0;
    while ($xpGan >= $xpNec) {
        $xpGan -= $xpNec;
        $nivel++;
        $subio++;
        $xpNec = (int)round($xpNec * 1.2); // +20% cada nivel
        if ($xpNec > 10000) $xpNec = 10000;
    }

    $up = $pdo->prepare("UPDATE xp_usuario SET nivel=?, xp_necesaria=?, xp_ganada=? WHERE id_usuario=?");
    $up->execute([$nivel, $xpNec, $xpGan, $id]);
    $pdo->commit();

    json_response([
        'ok' => true,
        'nivel' => $nivel,
        'xp_ganada' => $xpGan,
        'xp_necesaria' => $xpNec,
        'subio_nivel' => $subio > 0,
        'ganada' => $ganada
    ]);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) $pdo->rollBack();
    json_response(['ok'=>false,'error'=>'Error al guardar XP','detail'=>$e->getMessage()], 500);
}

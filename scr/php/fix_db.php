<?php
// scr/php/fix_db.php - Corrección automática si ya importaste el SQL viejo con FK invertido
// Visitá http://localhost/.../scr/php/fix_db.php una vez y listo.
require_once __DIR__ . '/config.php';

header('Content-Type: text/plain; charset=utf-8');

try {
    // Detectar FK mala: usuarios_ibfk_1 en usuarios
    $stmt = $pdo->query("SELECT CONSTRAINT_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='usuarios' AND CONSTRAINT_NAME='usuarios_ibfk_1'");
    $mala = $stmt->fetch();
    if ($mala) {
        echo "FK invertida detectada, corrigiendo...\n";
        $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
        try { $pdo->exec("ALTER TABLE `usuarios` DROP FOREIGN KEY `usuarios_ibfk_1`"); echo "FK vieja eliminada\n"; } catch(Exception $e){ echo $e->getMessage()."\n"; }
        // xp_usuario ya tiene FK? si existe usuarios_ibfk_1 en xp, quitar
        // recrear FK correcta
        try { $pdo->exec("ALTER TABLE `xp_usuario` ADD CONSTRAINT `fk_xp_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE ON UPDATE CASCADE"); echo "FK correcta creada\n"; } catch(Exception $e){ echo $e->getMessage()."\n"; }
        $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    }

    // Asegurar defaults y unique
    $pdo->exec("ALTER TABLE `xp_usuario` MODIFY `xp_ganada` int(10) NOT NULL DEFAULT 0");
    // Crear unique username si no existe
    $chk = $pdo->query("SHOW INDEX FROM `usuarios` WHERE Key_name='username'")->fetch();
    if(!$chk){
        try{ $pdo->exec("ALTER TABLE `usuarios` ADD UNIQUE KEY `username` (`username`)"); echo "Unique username creado\n"; }catch(Exception $e){echo $e->getMessage()."\n";}
    }
    echo "OK - BD lista\n";
} catch(Exception $e){
    http_response_code(500);
    echo "Error: ".$e->getMessage();
}

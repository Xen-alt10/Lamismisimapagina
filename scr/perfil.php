<?php
session_start();
require_once __DIR__ . "/includes/db.php";

// Hay que estar logueado para ver el perfil
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: includes/login.php");
    exit;
}

$usuario_id = (int) $_SESSION["id"];
$username = (string) $_SESSION["username"];

$estado = fila_xp($usuario_id);
$nivel = (int) $estado["nivel"];
$xp_meta = max(1, (int) $estado["xp_necesaria"]);
$xp_actual = max(0, (int) $estado["xp_ganada"]);
$xp_progreso = min(100, ($xp_actual / $xp_meta) * 100);
$xp_restante = max(0, $xp_meta - $xp_actual);

$completados = niveles_completados($usuario_id);
$inicial = strtoupper(substr($username, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos/perfil.css?v=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Perfil</title>
</head>
<body>
    <header class="header">
        <section id="home">
        <h1> La mismísima página </h1>
        <nav class="nav">
            <a href="inicio.php"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="misiones.php"><i class="fa-solid fa-list-check"></i> Misiones</a>
            <a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a>
        </nav>
        </section>
    </header>

    <main id="perfil">
        <div id="perfil-cabecera" class="tarjeta">
            <div class="fpf"><?php echo htmlspecialchars($inicial, ENT_QUOTES, "UTF-8"); ?></div>
            <div class="perfil-datos">
                <h2><?php echo htmlspecialchars($username, ENT_QUOTES, "UTF-8"); ?></h2>
                <p class="perfil-nivel">Nivel <?php echo $nivel; ?></p>
            </div>
            <div class="perfil-estadistica">
                <span class="stat-numero"><?php echo count($completados); ?>/<?php echo TOTAL_NIVELES; ?></span>
                <span class="stat-texto">niveles completados</span>
            </div>
        </div>

        <div id="perfil-xp" class="tarjeta">
            <p><b>Nivel:</b> <?php echo $nivel; ?></p>
            <p><b>Experiencia:</b> <?php echo $xp_actual; ?> / <?php echo $xp_meta; ?></p>
            <div class="xp-barra" role="progressbar" aria-label="Progreso de XP" aria-valuemin="0" aria-valuemax="<?php echo $xp_meta; ?>" aria-valuenow="<?php echo min($xp_actual, $xp_meta); ?>">
                <div class="xp-barra__progreso" style="width: <?php echo $xp_progreso; ?>%"></div>
            </div>
            <p class="xp-restante">Te faltan <?php echo $xp_restante; ?> XP para el nivel <?php echo $nivel + 1; ?></p>
        </div>

        <section id="niveles" class="tarjeta">
            <h2>Niveles completados</h2>
            <p>Mate / Básico — <?php echo count($completados); ?> de <?php echo TOTAL_NIVELES; ?> completados</p>
            <div class="lista-niveles">
                <?php for ($i = 1; $i <= TOTAL_NIVELES; $i++):
                    $hecho = in_array($i, $completados, true);
                    $clase = $hecho ? "completado" : "pendiente";
                ?>
                    <a class="nivel-perfil <?php echo $clase; ?>"
                       href="juegos/matematica/Basico/nivel<?php echo $i; ?>.php"
                       title="Nivel <?php echo $i; ?><?php echo $hecho ? ' — completado' : ' — pendiente'; ?>">
                        <?php if ($hecho): ?><i class="fa-solid fa-check"></i><?php endif; ?>
                        Lvl<?php echo $i; ?>
                    </a>
                <?php endfor; ?>
            </div>
        </section>
    </main>
</body>
</html>

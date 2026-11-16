<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$logueado = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$usuario_id = $logueado ? (int) $_SESSION["id"] : 0;

$completados = $logueado ? niveles_completados($usuario_id) : [];
$cantidad_completados = count($completados);

if ($logueado) {
    $estado = fila_xp($usuario_id);
} else {
    $estado = ["nivel" => 1, "xp_ganada" => 0, "xp_necesaria" => 500];
}

$nivel = (int) $estado["nivel"];
$xp_ganada = (int) $estado["xp_ganada"];
$xp_necesaria = max(1, (int) $estado["xp_necesaria"]);

// Recompensas personales: qué desbloqueaste hasta ahora
$recompensas = [
    [
        "titulo" => "Primer nivel completado",
        "cumplida" => $cantidad_completados >= 1,
        "progreso" => porcentaje($cantidad_completados, 1),
        "detalle" => $cantidad_completados . " / 1 nivel",
    ],
    [
        "titulo" => "3 niveles completados",
        "cumplida" => $cantidad_completados >= 3,
        "progreso" => porcentaje($cantidad_completados, 3),
        "detalle" => $cantidad_completados . " / 3 niveles",
    ],
    [
        "titulo" => "Alcanza el nivel 2",
        "cumplida" => $nivel >= 2,
        "progreso" => $nivel >= 2 ? 100 : porcentaje($xp_ganada, $xp_necesaria),
        "detalle" => $nivel >= 2
            ? "Llegaste al nivel " . $nivel
            : "Nivel " . $nivel . " · " . $xp_ganada . " / " . $xp_necesaria . " XP",
    ],
    [
        "titulo" => "Completa los 6 niveles",
        "cumplida" => $cantidad_completados >= TOTAL_NIVELES,
        "progreso" => porcentaje($cantidad_completados, TOTAL_NIVELES),
        "detalle" => $cantidad_completados . " / " . TOTAL_NIVELES . " niveles",
    ],
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos/recompensas.css?v=2">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <title>Recompensas</title>
</head>
<body>
    <header class="header">
        <section id="home">
        <h1> lamismisimapagina </h1>
        <nav class="nav">
            <a href="inicio.php"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="misiones.php"><i class="fa-solid fa-list-check"></i> Misiones</a>
            <a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a>
        </nav>
        </section>
    </header>

    <h1 id="titulo">Recompensas</h1>

    <p class="aviso-sesion">Tus recompensas personales de <b><?php echo htmlspecialchars($logueado ? $_SESSION["username"] : "invitado", ENT_QUOTES, "UTF-8"); ?></b>.</p>

    <?php foreach ($recompensas as $indice => $recompensa): $n = $indice + 1; ?>
        <div id="r<?php echo $n; ?>" class="<?php echo $recompensa["cumplida"] ? "desbloqueada" : "bloqueada"; ?>">
            <h3><?php echo htmlspecialchars($recompensa["titulo"], ENT_QUOTES, "UTF-8"); ?></h3>
            <p class="barra-detalle">
                <span class="estado <?php echo $recompensa["cumplida"] ? "estado-ok" : "estado-no"; ?>">
                    <?php echo $recompensa["cumplida"] ? "Desbloqueada ✓" : "Bloqueada"; ?>
                </span>
                &middot; <?php echo htmlspecialchars($recompensa["detalle"], ENT_QUOTES, "UTF-8"); ?>
            </p>
            <div class="bc_<?php echo $n; ?>">
                <div class="bp_<?php echo $n; ?>" style="--progreso: <?php echo $recompensa["progreso"]; ?>%"></div>
            </div>
        </div>
    <?php endforeach; ?>
</body>
</html>

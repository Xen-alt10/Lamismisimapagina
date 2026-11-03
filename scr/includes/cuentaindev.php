<?php

// Initialize the session
session_start();
$conexion = new mysqli("localhost", "phpmyadmin", "RedesInformaticas", "lamismisimabasededatos");
if ($conexion ->connect_error) {
    die ("Error de conexion: " . $conexion->connect_error);
}
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$sql = "SELECT usuario_asocc, nivel, xp_necesaria, xp_ganada FROM xp_usuario WHERE usuario_asocc = $_SESSION[id]";
$result = $conexion -> query($sql);
$nivel = 1;
$xp_necesaria = 500;
$xp_ganada = 0;
if($result->num_rows > 0){
    $fila = $result->fetch_assoc();
    if ($fila) { 
        $nivel = $fila["nivel"];
        $xp_necesaria = $fila["xp_necesaria"];
        $xp_ganada = $fila["xp_ganada"];
    }
}else{

}
$xp_meta = max(1, (int) $xp_necesaria);
$xp_actual = max(0, (int) $xp_ganada);
$xp_progreso = min(100, ($xp_actual / $xp_meta) * 100);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../estilos/cuentaindev.css">
</head>
<body>
    <header class="header">
        <section id="home">
        <h1> La mismísima página </h1>
        <nav class="nav">
            <a href="../inicio.html"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="../recompensas.html"><i class="fa-solid fa-info-circle"></i> Recompensas</a>
            <a href="#historial de ejercicios"><i class="fa-solid fa-info-circle"></i> Historial de ejercicios</a>
            <a href="cuenta.php"><i class="fa-solid fa-gamepad"></i> Cuenta</a>
        </nav>
        </section>
    </header>

    <div id="userwelcome">
        <p>Bienvenido, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b></p>
    </div>

    <div id="MenuXP">
        <p>Nivel: <?php echo htmlspecialchars($nivel); ?></p>
        <p>XP: <?php echo $xp_actual; ?> / <?php echo $xp_meta; ?></p>
        <div class="xp-barra" role="progressbar" aria-label="Progreso de XP" aria-valuemin="0" aria-valuemax="<?php echo $xp_meta; ?>" aria-valuenow="<?php echo min($xp_actual, $xp_meta); ?>" aria-valuetext="<?php echo $xp_actual; ?> de <?php echo $xp_meta; ?> XP">
            <div class="xp-barra__progreso" style="width: <?php echo $xp_progreso; ?>%"></div>
        </div>
    </div>
    
    
    

</body>
</html>
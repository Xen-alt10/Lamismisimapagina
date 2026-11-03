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
if($result->num_rows > 0){
    $fila = $result->fetch_assoc();
    if ($fila) { 
        $nivel = $fila["nivel"];
        $xp_necesaria = $fila["xp_necesaria"];
        $xp_ganada = $fila["xp_ganada"];
    }
}else{
    $errormessage = "ERROR: No hay un registro de experiencia de este usuario";
}
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
            <a href="../inicio.html"><i class="fa-solid fa-house"></i> Ejercicios</a>
            <a href="../recompensas.html"><i class="fa-solid fa-info-circle"></i></a>
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
        <p>XP Actual: <?php echo htmlspecialchars($xp_necesaria); ?></p>
        <p>XP Restante: <?php echo htmlspecialchars($xp_ganada); ?></p>
    </div>
    
    
    

</body>
</html>
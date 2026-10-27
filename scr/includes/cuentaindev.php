<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
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
    
    
    

</body>
</html>
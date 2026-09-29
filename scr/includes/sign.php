<?php 

$conexion = new mysqli("localhost", "root", "", "lamismisimabasededatos");
if ($conexion ->connect_error) {
    die ("Error de conexion: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $contraseña = $_POST[password_hash("pass", PASSWORD_BCRYPT)];
    
    if ($usuario == "" || $contraseña == "") { // si el usuario y la contraseña estan vacios
        echo // añade linea de error (linea 12 a linea 15)
        '
        <p id="mensajeError"> Error: Formulario vacio, ingresar valores</p>
        ';
        exit;
    }
        if ($usuario == "" || $contraseña == "") { // si el usuario y la contraseña estan vacios
        echo // añade linea de error (linea 12 a linea 15)
        '
        <p id="mensajeError"> Error: Formulario vacio, ingresar valores</p>
        ';
        exit;
    }
    $sql = "INSERT INTO usuarios (usuario, contraseña)
            VALUES ('$username', '$contraseña')";
    $conexion->query($sql);
    
}   

?>

<!DOCTYPE html>
<html lang="en">    
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>sign in</title>
</head>
<body>
    <div menuSignIn>
        <h1>Iniciar sesion</h1>
        <p>Introducir las credenciales requeridas</p>
        <form method="post">
            <p class="elementoForm"> <!--    asignacion de username    -->
                <label>Usuario: </label>
                <input type="text" name="username">
            </p>

            <p class="elementoForm"> <!-- asignacion de contraseña -->
                <label>Contraseña: </label>
                <input type="password" name="pass">
            </p>
        
        <button type="submit">Crear Cuenta  </button>
        </form>
    </div>
    
    

</body>
</html>
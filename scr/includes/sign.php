<?php 

$conexion = new mysqli("localhost", "root", "", "lamismisimabasededatos");
if ($conexion ->connect_error) {
    die ("Error de conexion: " . $conexion->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["username"];
    $contraseña = $_POST["pass"];
    $hashpass = password_hash($contraseña, PASSWORD_BCRYPT);
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
    $sql = "INSERT INTO usuarios (username, pass_hash)
            VALUES ('$usuario', '$hashpass');";
    $conexion->query($sql);

    $last_id = $conexion->insert_id;
    $sql = "INSERT INTO xp_usuario (usuario_asocc)
            VALUES ('$last_id');";
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

<?php 
    $conexion->close();
?>
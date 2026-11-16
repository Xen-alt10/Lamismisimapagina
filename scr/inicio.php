<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos/styles.css?v=2">
    <title>La mismísima página - Aprende jugando</title>
    <meta name="description" content="Plataforma educativa para aprender matemáticas por niveles">
</head>
<body>
    <header class="header">
        <section id="home">
        <h1> La mismísima página </h1>
        <nav class="nav">
            <a href="#home"><i class="fa-solid fa-house"></i> Inicio</a>
            <a href="misiones.php"><i class="fa-solid fa-list-check"></i> Misiones</a>
            <a href="perfil.php"><i class="fa-solid fa-user"></i> Perfil</a>
        </nav>
        <div id="user-banner"></div>
        </section>
    </header>
    <main>
        <h1>¿Que estas aprendiendo ahora?</h1>
        <h1>Materia:</h1>
        <button class="btnm" onclick="activar(this)">Matematica</button>
        <button class="btnm" onclick="activar(this)">Fisica</button>
        <button class="btnm" onclick="activar(this)">Programacion</button>

        <h2>Nivel</h2>
        <button class="btns" onclick="activar2(this)">Basico</button>
        <button class="btns" onclick="activar2(this)">Intermedio</button> 
        <button class="btns" onclick="activar2(this)">Avanzado</button>
        <h1>empieza</h1>

        <div class="niveles">
        <a href="juegos/matematica/Basico/nivel1.php"><button class="nivel1">Lvl1</button></a>
        <a href="juegos/matematica/Basico/nivel2.php"><button class="nivel2">Lvl2</button></a>
        <a href="juegos/matematica/Basico/nivel3.php"><button class="nivel3">Lvl3</button></a>
         <a href="juegos/matematica/Basico/nivel4.php"><button class="nivel4">Lvl4</button></a>
         <a href="juegos/matematica/Basico/nivel5.php"><button class="nivel5">Lvl5</button></a>
         <a href="juegos/matematica/Basico/nivel6.php"><button class="nivel6">Lvl6</button></a>
        </div>
    </main>
    <footer class="pagefooter">
        <section id="contact">                                              
        <div class="contactos" id="contactos"> 
            <h2> Contactos </h2>
            <ul>
                <li><a href="mailto:lamismisimapagina@gmail.com"><i class="fa-regular fa-envelope"></i> Correo Electrónico</a></li>
                <li><a href="https://github.com/Xen-alt10/Lamismisimapagina.git" target="_blank"><i class="fa-brands fa-github"></i> GitHub</a></li>
                </ul>
        </div>
        </section>
    </footer>
    <script src="java/index.js"></script>
    <script src="java/auth.js"></script>
</body>
</html>

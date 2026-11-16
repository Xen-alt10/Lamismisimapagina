<?php
/**
 * Base de datos, reglas de XP y helpers de progreso.
 * Lo usan: perfil.php, misiones.php, recompensas.php, los niveles (nivel.php) y api_xp.php.
 */

// Misma conexión que usan login.php y sign.php
define("DB_HOST", "localhost");
define("DB_USER", "phpmyadmin");
define("DB_PASS", "RedesInformaticas");
define("DB_NAME", "lamismisimabasededatos");

// Reglas del juego
define("XP_POR_NIVEL", 100); // XP que otorga completar un nivel (sumarXP(100) en los niveles)
define("TOTAL_NIVELES", 6);  // niveles de Matemática / Básico
define("MATERIA", "matematica");
define("TEMA", "Basico");

/**
 * Conexión única a la base de datos.
 */
function db()
{
    static $conexion = null;

    if ($conexion === null) {
        $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conexion->connect_error) {
            die("Error de conexion: " . $conexion->connect_error);
        }
        $conexion->set_charset("utf8mb4");
        asegurar_tablas($conexion);
    }

    return $conexion;
}

/**
 * Crea la tabla de progreso si todavía no existe, así no hace falta
 * volver a importar lamismisimabasededatos.sql.
 */
function asegurar_tablas($conexion)
{
    $sql = "CREATE TABLE IF NOT EXISTS progreso_nivel (
        id_progreso int(10) NOT NULL AUTO_INCREMENT,
        usuario_asocc int(10) NOT NULL,
        materia varchar(50) NOT NULL DEFAULT 'matematica',
        tema varchar(50) NOT NULL DEFAULT 'Basico',
        nivel int(10) NOT NULL,
        fecha datetime NOT NULL DEFAULT current_timestamp(),
        PRIMARY KEY (id_progreso),
        UNIQUE KEY progreso_unico (usuario_asocc, materia, tema, nivel),
        KEY progreso_usuario (usuario_asocc),
        CONSTRAINT progreso_nivel_ibfk_1 FOREIGN KEY (usuario_asocc)
            REFERENCES usuarios (id_usuario) ON DELETE CASCADE ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $conexion->query($sql);
}

/**
 * Devuelve la fila de XP del usuario.
 * Si no tiene fila (usuarios viejos), la crea con los valores por defecto.
 */
function fila_xp($usuario_id)
{
    $conexion = db();
    $usuario_id = (int) $usuario_id;

    $stmt = $conexion->prepare(
        "SELECT xp_id, nivel, xp_necesaria, xp_ganada
         FROM xp_usuario
         WHERE usuario_asocc = ?
         ORDER BY xp_id ASC
         LIMIT 1"
    );
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($fila) {
        return $fila;
    }

    $stmt = $conexion->prepare("INSERT INTO xp_usuario (usuario_asocc) VALUES (?)");
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $nuevo_id = $stmt->insert_id;
    $stmt->close();

    return [
        "xp_id" => $nuevo_id,
        "nivel" => 1,
        "xp_necesaria" => 500,
        "xp_ganada" => 0,
    ];
}

/**
 * Suma XP a un usuario.
 * Si al llegar a "igual o más" que la XP que falta para subir de nivel,
 * le sube un nivel (y repite mientras le sobre XP).
 */
function sumar_xp($usuario_id, $cantidad)
{
    $fila = fila_xp($usuario_id);

    $nivel = (int) $fila["nivel"];
    $xp_necesaria = max(1, (int) $fila["xp_necesaria"]);
    $xp_ganada = max(0, (int) $fila["xp_ganada"]) + max(0, (int) $cantidad);
    $subio_de_nivel = false;

    while ($xp_ganada >= $xp_necesaria) {
        $xp_ganada -= $xp_necesaria;
        $nivel += 1;
        $subio_de_nivel = true;
    }

    $conexion = db();
    $stmt = $conexion->prepare("UPDATE xp_usuario SET nivel = ?, xp_ganada = ? WHERE xp_id = ?");
    $stmt->bind_param("iii", $nivel, $xp_ganada, $fila["xp_id"]);
    $stmt->execute();
    $stmt->close();

    return [
        "nivel" => $nivel,
        "xp_necesaria" => $xp_necesaria,
        "xp_ganada" => $xp_ganada,
        "subio_de_nivel" => $subio_de_nivel,
    ];
}

/**
 * Registra que un usuario completó un nivel.
 * Devuelve true si es la primera vez (por eso la XP de nivel solo se da una vez).
 */
function registrar_completado($usuario_id, $materia, $tema, $nivel)
{
    $conexion = db();

    $usuario_id = (int) $usuario_id;
    $materia = (string) $materia;
    $tema = (string) $tema;
    $nivel = (int) $nivel;

    $stmt = $conexion->prepare(
        "INSERT IGNORE INTO progreso_nivel (usuario_asocc, materia, tema, nivel)
         VALUES (?, ?, ?, ?)"
    );
    $stmt->bind_param("issi", $usuario_id, $materia, $tema, $nivel);
    $stmt->execute();
    $es_primera_vez = $conexion->affected_rows > 0;
    $stmt->close();

    return $es_primera_vez;
}

/**
 * Números de nivel que el usuario completó (ej: [1, 3, 4]).
 */
function niveles_completados($usuario_id, $materia = MATERIA, $tema = TEMA)
{
    $conexion = db();
    $usuario_id = (int) $usuario_id;

    $stmt = $conexion->prepare(
        "SELECT nivel FROM progreso_nivel
         WHERE usuario_asocc = ? AND materia = ? AND tema = ?"
    );
    $stmt->bind_param("iss", $usuario_id, $materia, $tema);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $niveles = [];
    while ($fila = $resultado->fetch_assoc()) {
        $niveles[] = (int) $fila["nivel"];
    }
    $stmt->close();

    sort($niveles);
    return $niveles;
}

/**
 * Quiénes completaron un nivel: usuario, nivel actual y XP de cada uno.
 */
function quien_completo($materia, $tema, $nivel)
{
    $conexion = db();
    $nivel = (int) $nivel;

    $sql = "SELECT u.id_usuario,
                   u.username,
                   COALESCE(x.nivel, 1) AS nivel,
                   COALESCE(x.xp_ganada, 0) AS xp_ganada,
                   COALESCE(x.xp_necesaria, 500) AS xp_necesaria,
                   p.fecha
            FROM progreso_nivel p
            INNER JOIN usuarios u ON u.id_usuario = p.usuario_asocc
            LEFT JOIN xp_usuario x ON x.xp_id = (
                SELECT MIN(x2.xp_id) FROM xp_usuario x2 WHERE x2.usuario_asocc = u.id_usuario
            )
            WHERE p.materia = ? AND p.tema = ? AND p.nivel = ?
            ORDER BY p.fecha ASC, p.id_progreso ASC";

    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ssi", $materia, $tema, $nivel);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $filas = [];
    while ($fila = $resultado->fetch_assoc()) {
        $filas[] = $fila;
    }
    $stmt->close();

    return $filas;
}

/**
 * Porcentaje de progreso (0 a 100) para las barras de misiones/recompensas.
 */
function porcentaje($actual, $meta)
{
    $meta = (int) $meta;
    if ($meta <= 0) {
        return 0;
    }
    return (int) round(min(100, (max(0, (int) $actual) / $meta) * 100));
}

<?php
/**
 * API de XP a la que llama java/auth.js.
 *  - accion=completar : registra el nivel completado y suma XP (con subida de nivel).
 *  - accion=estado    : devuelve el estado del usuario para el banner del inicio.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header("Content-Type: application/json; charset=utf-8");

require_once __DIR__ . "/db.php";

/** Limpia materias/temas que vengan desde el cliente. */
function texto_limpio($valor, $por_defecto)
{
    $valor = preg_replace('/[^A-Za-z0-9_\- ]/', '', (string) $valor);
    $valor = trim(substr($valor, 0, 50));
    return $valor === "" ? $por_defecto : $valor;
}

$usuario_logueado = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;
$accion = isset($_POST["accion"]) ? (string) $_POST["accion"] : "";

$respuesta = ["ok" => false, "motivo" => "accion_invalida"];

if ($accion === "estado") {
    if ($usuario_logueado) {
        $usuario_id = (int) $_SESSION["id"];
        $estado = fila_xp($usuario_id);
        $respuesta = [
            "ok" => true,
            "logueado" => true,
            "username" => (string) $_SESSION["username"],
            "nivel" => (int) $estado["nivel"],
            "xp_necesaria" => (int) $estado["xp_necesaria"],
            "xp_ganada" => (int) $estado["xp_ganada"],
            "completados" => count(niveles_completados($usuario_id)),
        ];
    } else {
        $respuesta = ["ok" => true, "logueado" => false];
    }
} elseif ($accion === "completar") {
    if (!$usuario_logueado) {
        $respuesta["motivo"] = "no_sesion";
    } else {
        $usuario_id = (int) $_SESSION["id"];
        $materia = texto_limpio(isset($_POST["materia"]) ? $_POST["materia"] : "", MATERIA);
        $tema = texto_limpio(isset($_POST["tema"]) ? $_POST["tema"] : "", TEMA);
        $nivel = isset($_POST["nivel"]) ? (int) $_POST["nivel"] : 0;
        $xp = isset($_POST["xp"]) ? max(0, (int) $_POST["xp"]) : XP_POR_NIVEL;

        if ($nivel < 1 || $nivel > TOTAL_NIVELES * 20) {
            $respuesta["motivo"] = "nivel_invalido";
        } else {
            // La XP del nivel solo se otorga la primera vez que se completa
            $primera_vez = registrar_completado($usuario_id, $materia, $tema, $nivel);

            if ($primera_vez) {
                $estado = sumar_xp($usuario_id, $xp);
                $xp_otorgada = $xp;
            } else {
                $estado = fila_xp($usuario_id);
                $xp_otorgada = 0;
            }

            $respuesta = [
                "ok" => true,
                "primera_vez" => $primera_vez,
                "xp_otorgada" => $xp_otorgada,
                "nivel" => (int) $estado["nivel"],
                "xp_necesaria" => (int) $estado["xp_necesaria"],
                "xp_ganada" => (int) $estado["xp_ganada"],
                "subio_de_nivel" => isset($estado["subio_de_nivel"]) ? (bool) $estado["subio_de_nivel"] : false,
                "completados" => count(niveles_completados($usuario_id, $materia, $tema)),
            ];
        }
    }
}

echo json_encode($respuesta);

<?php
/**
 * Se incluye desde cada nivel (nivel1.php ... nivel6.php).
 *
 * Antes de incluirlo, el nivel debe definir:
 *   $materia      ej: "matematica"
 *   $tema         ej: "Basico"
 *   $numero_nivel ej: 1
 *
 * Este archivo deja listos los datos y la función panel_completadores()
 * para pintar quiénes completaron el nivel, con su nivel y su XP.
 *
 * La XP NO se puede añadir a mano: solo se otorga al completar ejercicios
 * (java/auth.js -> includes/api_xp.php, usando la sesión del usuario).
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/db.php";

$usuario_logueado = isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true;

// --- Datos para el panel (solo lectura) ---
$completaron_este_nivel = quien_completo($materia, $tema, $numero_nivel);

/**
 * Pinta la ficha "¿Quién completó este nivel?" con el nivel y la XP
 * actual de cada usuario que lo terminó.
 *
 * Está OCULTA por defecto: el usuario ve un botón "Ver quiénes lo
 * completaron (n)" que la despliega. Los datos se siguen registrando
 * igual (progreso_nivel) aunque nadie abra la ficha.
 */
function panel_completadores()
{
    global $completaron_este_nivel;

    $cantidad = count($completaron_este_nivel);

    $salida = '<button type="button" id="completaron-toggle" aria-expanded="false" aria-controls="completaron">';
    $salida .= 'Ver quiénes lo completaron (' . $cantidad . ')</button>';

    $salida .= '<section id="completaron" hidden>';
    $salida .= '<h2>¿Quién completó este nivel?</h2>';
    $salida .= '<p class="completaron__bajada">Cada usuario que lo terminó, con su nivel y su XP actual.</p>';

    if (empty($completaron_este_nivel)) {
        $salida .= '<p class="completaron__vacio">Todavía nadie completó este nivel. ¡Sé el primero!</p>';
    } else {
        foreach ($completaron_este_nivel as $fila) {
            $nombre = htmlspecialchars($fila["username"], ENT_QUOTES, "UTF-8");
            $salida .= '<div class="completador">';
            $salida .= '<span class="completador__nombre">' . $nombre . '</span>';
            $salida .= '<span class="completador__dato">Nivel ' . (int) $fila["nivel"] . '</span>';
            $salida .= '<span class="completador__dato">XP ' . (int) $fila["xp_ganada"]
                . ' / ' . (int) $fila["xp_necesaria"] . '</span>';
            $salida .= '</div>';
        }
    }

    $salida .= '</section>';

    // Mostrar / ocultar la ficha sin recargar la página
    $salida .= '<script>(function(){'
        . 'var b=document.getElementById("completaron-toggle");'
        . 'var s=document.getElementById("completaron");'
        . 'if(!b||!s)return;'
        . 'b.addEventListener("click",function(){'
        . 'if(s.hasAttribute("hidden")){'
        . 's.removeAttribute("hidden");b.setAttribute("aria-expanded","true");'
        . 'b.textContent="Ocultar quiénes lo completaron";'
        . '}else{'
        . 's.setAttribute("hidden","");b.setAttribute("aria-expanded","false");'
        . 'b.textContent="Ver quiénes lo completaron ("+b.getAttribute("data-cantidad")+")";'
        . '}});'
        . 'b.setAttribute("data-cantidad","' . $cantidad . '");'
        . '})();</script>';

    echo $salida;
}

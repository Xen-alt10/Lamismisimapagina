// Sesión y XP de la web.
//  - define window.sumarXP(cantidad): la llaman los niveles al acertar.
//  - si el usuario está logueado, muestra su nombre y nivel en el banner del inicio.
(function () {
    "use strict";

    // La ruta de este script es la misma desde cualquier página (scr/java/auth.js),
    // así que la API se resuelve a partir de la ubicación del propio script.
    var api = "includes/api_xp.php";
    if (document.currentScript && document.currentScript.src) {
        api = new URL("../includes/api_xp.php", document.currentScript.src).href;
    }

    var cuerpo = document.body || document.documentElement;
    var nivel = parseInt(cuerpo.getAttribute("data-nivel"), 10) || 0;
    var materia = cuerpo.getAttribute("data-materia") || "matematica";
    var tema = cuerpo.getAttribute("data-tema") || "Basico";

    function llamarApi(datos) {
        var peticion = new URLSearchParams();
        Object.keys(datos).forEach(function (clave) {
            peticion.append(clave, datos[clave]);
        });

        return fetch(api, {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
            body: peticion.toString(),
            credentials: "same-origin"
        }).then(function (respuesta) {
            if (!respuesta.ok) {
                throw new Error("HTTP " + respuesta.status);
            }
            return respuesta.json();
        });
    }

    /**
     * Suma XP al usuario logueado y registra que completó este nivel.
     * Uso: sumarXP(100)
     */
    window.sumarXP = function (cantidad) {
        if (!nivel) {
            return Promise.resolve(null);
        }

        return llamarApi({
            accion: "completar",
            nivel: nivel,
            materia: materia,
            tema: tema,
            xp: typeof cantidad === "number" ? cantidad : 100
        })
            .then(function (datos) {
                if (datos && datos.ok) {
                    // Las páginas pueden escuchar este evento para refrescar barras
                    document.dispatchEvent(new CustomEvent("xp-actualizada", { detail: datos }));
                }
                return datos;
            })
            .catch(function (error) {
                console.warn("No se pudo registrar la XP:", error);
                return null;
            });
    };

    // Banner del inicio: "Hola, usuario · Nivel 3 · ver mi cuenta"
    var banner = document.getElementById("user-banner");
    if (banner) {
        llamarApi({ accion: "estado" })
            .then(function (datos) {
                if (!datos || !datos.ok || !datos.logueado) {
                    return;
                }

                banner.appendChild(document.createTextNode(
                    "Hola, " + datos.username + " · Nivel " + datos.nivel + " · "
                ));

                var enlace = document.createElement("a");
                enlace.href = "perfil.php";
                enlace.textContent = "ver mi perfil";
                banner.appendChild(enlace);
            })
            .catch(function () {
                // sin sesión o sin conexión: el banner se queda vacío
            });
    }
})();

document.addEventListener("DOMContentLoaded", function () {

    const botonMenu = document.getElementById("boton-menu");
    const menuPrincipal = document.getElementById("menu-principal");
    const botonModo = document.getElementById("btn-modo");

    /* =====================================================
       MENÚ RESPONSIVE
    ===================================================== */

    if (botonMenu && menuPrincipal) {

        botonMenu.addEventListener("click", function () {

            const abierto = menuPrincipal.classList.toggle("abierto");

            botonMenu.setAttribute(
                "aria-expanded",
                abierto ? "true" : "false"
            );

            botonMenu.textContent = abierto ? "✕" : "☰";

        });

        const enlacesMenu = menuPrincipal.querySelectorAll("a");

        enlacesMenu.forEach(function (enlace) {

            enlace.addEventListener("click", function () {

                menuPrincipal.classList.remove("abierto");

                botonMenu.setAttribute("aria-expanded", "false");

                botonMenu.textContent = "☰";

            });

        });

        window.addEventListener("resize", function () {

            if (window.innerWidth > 900) {

                menuPrincipal.classList.remove("abierto");

                botonMenu.setAttribute("aria-expanded", "false");

                botonMenu.textContent = "☰";

            }

        });

    }

    /* =====================================================
       MODO OSCURO
    ===================================================== */

    const modoGuardado = localStorage.getItem("modo");

    if (modoGuardado === "oscuro") {

        document.body.classList.add("dark");

        if (botonModo) {
            botonModo.textContent = "☀️";
        }

    }

    if (botonModo) {

        botonModo.addEventListener("click", function () {

            document.body.classList.toggle("dark");

            const modoOscuro = document.body.classList.contains("dark");

            localStorage.setItem(
                "modo",
                modoOscuro ? "oscuro" : "claro"
            );

            botonModo.textContent = modoOscuro ? "☀️" : "🌙";

        });

    }

}); 
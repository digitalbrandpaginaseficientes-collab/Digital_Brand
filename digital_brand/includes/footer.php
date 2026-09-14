</main>

<footer class="pie-pagina">

    <div class="contenedor pie-contenido">

        <div>

            <h2>Digital Brand</h2>

            <p>
                Páginas eficientes para empresas que rinden.
            </p>

        </div>

        <div>

            <h3>Contacto</h3>

            <p>
                Copacabana, Antioquia
            </p>

           <a href="https://api.whatsapp.com/send/?phone=573332843241&text=Hola%2C+quiero+m%C3%A1s+informaci%C3%B3n+sobre+Digital+Brand&type=phone_number&app_absent=0">
                +57 333 284 3241
            </a>

            <a href= "https://www.instagram.com/somosdigitalbrand?utm_source=ig_web_button_share_sheet&stkn=ZDNlZDc0MzIxNw==">
                @somosdigitalbrand
            </a>

            <p>
                DigitalBrandpaginaseficientes@gmail.com
            </p>

        </div>

        <div>

            <h3>Enlaces</h3>

            <a href="<?php echo APP_URL; ?>/nosotros.php">
                Nosotros
            </a>

            <a href="<?php echo APP_URL; ?>/portafolio.php">
                Portafolio
            </a>

            <a href="<?php echo APP_URL; ?>/contacto.php">
                Contacto
            </a>

        </div>

    </div>

    <div class="pie-inferior">

        <p>
            © <?php echo date('Y'); ?> Digital Brand.
            Proyecto de media técnica en Programación de Software.
        </p>

    </div>

</footer>

<a
    href="https://wa.me/573332843241?text=Hola,%20quiero%20más%20información%20sobre%20Digital%20Brand"
    class="boton-whatsapp"
    target="_blank"
    rel="noopener noreferrer"
    aria-label="Contactar por WhatsApp"
>
    WhatsApp
</a>

<?php

require_once __DIR__ . '/chatbot.php';

?>

<script src="<?php echo APP_URL; ?>/assets/js/script.js"></script>
<script src="<?php echo APP_URL; ?>/assets/js/chatbot.js"></script>

</body>
</html>
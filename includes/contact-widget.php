<?php

require_once __DIR__ . '/../config/developer.php';

?>


<div class="contact-widget">


    <button 
        class="contact-toggle"
        title="Contact Developer">

        <i class="fa-solid fa-user-tie"></i>

    </button>



    <div class="contact-panel">


        <div class="contact-header">

            <h3>
                <i class="fa-solid fa-code"></i>
                Contact Developer
            </h3>

            <p>
                <?= DEV_NAME ?>
            </p>

        </div>



        <a href="https://wa.me/<?= DEV_WHATSAPP ?>"
           target="_blank">

            <i class="fa-brands fa-whatsapp"></i>

            WhatsApp

        </a>




        <a href="<?= DEV_LINKEDIN ?>"
           target="_blank">

            <i class="fa-brands fa-linkedin"></i>

            LinkedIn

        </a>




        <a href="mailto:<?= DEV_EMAIL ?>">

            <i class="fa-solid fa-envelope"></i>

            Email

        </a>




        <a href="<?= DEV_RESUME ?>"
           download>


            <i class="fa-solid fa-file-pdf"></i>

            Download Resume


        </a>





        <a href="<?= DEV_GITHUB ?>"
           target="_blank">


            <i class="fa-brands fa-github"></i>

            GitHub Profile


        </a>


    </div>


</div>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>IOT</title>
        <link rel="stylesheet" href="css/general.css"/>
        <link rel="stylesheet" href="css/addDevice.css"/>
        <script src="js/fetchUtils.js"></script>
        <script src="js/addModule.js"></script>
        <script src="js/mainNotifications.js"></script>
    </head>

    <body>
        <?php
            /**
             * le constant CONTENT_PAGE va stocker le nom de la la page qui a 
             * le code html de la content, ce variable faut etre toujours definie
             * avec l'utilisation de generalForm.php
             * generalForm.php : page qui contient le code qui est commun pour tout les pages.
             */
            
            define("CONTENT_PAGE", "addDeviceForm.php");
            require("views/generalForm.php");
        ?>
    </body>
</html>
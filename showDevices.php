<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
    <head>
        <meta charset="UTF-8" />
        <title>IOT</title>
        <link rel="stylesheet" href="css/general.css"/>
        <link rel="stylesheet" href="css/showDevices.css"/>
        <script src="js/fetchUtils.js"></script>
        <script src="js/mainNotifications.js"></script>
        <script src="js/showInfo.js"></script>
    </head>

    <body>
        <?php
            /**
             * le constant CONTENT_PAGE va stocker le nom de la la page qui a 
             * le code html de la content, ce variable faut etre toujours definie
             * avec l'utilisation de generalForm.php
             * generalForm.php : page qui contient le code qui est commun pour tout les pages.
             * 
             * L'array modules_data ca contenir les informations des modules, 
             * cette array va etre utiliser dans le script showDevicesForm.php
             */

            spl_autoload_register(function ($className) {
                include ("lib/{$className}.class.php");
            });
            date_default_timezone_set ('Europe/Paris');

            try{
                $modules_data = array();
                $data = new DataLayer();
                if(isset($_GET["warning"])){
                    $modules_data = $data->getModuleWithWarning();
                }
                else{
                    $modules_data = $data->getModuleSmallInfos();
                }
                define("CONTENT_PAGE", "showDevicesForm.php");
                require("views/generalForm.php");
 
            } catch (Exception $e){
                echo $e->getMessage();
                exit("error !");
            }  
            
        ?>
    </body>
</html>
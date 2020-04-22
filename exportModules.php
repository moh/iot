<?php

    spl_autoload_register(function ($className) {
        include ("lib/{$className}.class.php");
    });
    date_default_timezone_set ('Europe/Paris');


    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header("Content-Disposition: attachment;filename={$filename}");
        header("Content-Transfer-Encoding: binary");
    }

    // transforme un array php en csv file
    function array2csv(array &$array){
       if (count($array) == 0) {
         return null;
       }
       ob_start();
       $df = fopen("php://output", 'w');
       fputcsv($df, array_keys(reset($array)));
       foreach ($array as $row) {
          fputcsv($df, $row);
       }
       fclose($df);
       return ob_get_clean();
    }
    
    try{
        $data = new DataLayer();
        if(isset($_GET["id"])){
            if(is_numeric($_GET["id"])){
                $id = $_GET["id"];
                $modul_datas = $data->getEventHistory($id);
                $filename = "dataModule$id.csv";
            }
            else{
                echo "id pas valide";
                die();
            }
        }
        else{
            $modul_datas = $data->getAllModulesDatas();
            $filename = "allModules.csv";
        }
        download_send_headers($filename);
        echo array2csv($modul_datas);
        die();

    } catch(Exception $e){
        echo $e->getMessage();
        die();
    }


?>
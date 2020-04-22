<?php

    include 'DataLayer.class.php';


    function download_send_headers($filename) {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download  
        header("Content-Type: application/force-download; charset=UTF-8'");
        header("Content-Type: application/octet-stream; charset=UTF-8'");
        header("Content-Type: application/download; charset=UTF-8'");

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
        $modul_datas =$data->getFamilies();
        download_send_headers("test.csv");
        echo array2csv($modul_datas);
        die();

    } catch(Exception $e){
        echo $e->getMessage();
        die();
    }


?>
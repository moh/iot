<?php

    /**
    * Ce script est un service qui prend rien en parametre mais 
    * qui renvoie les ids de tout les modules sauvegarder dans le 
    * base de donner.
    * le output est forme d'un code, ok si c'est effectuer sans probleme,
    * ou error sinon. Dans le cas de ok, les resultat sont stockes avec le cle result,
    * dans le cas d'erreur, le cle est message.
    * Ici le result va etre un array d'un dimension.
    */


    spl_autoload_register(function ($className) {
        include ("lib/{$className}.class.php");
    });
    date_default_timezone_set ('Europe/Paris');

    set_include_path('..');

    header('Content-type: application/json; charset=UTF-8');

    /**
     * fonction pour produire un message json
     */
    function produceError($message){
        echo json_encode(['status'=>'error','message'=>$message]);
    }
    function produceResult($result){
        echo json_encode(['status'=>'ok','result'=>$result]);
    }

    try{
        $data = new DataLayer();
        $ids = $data->getModulesId();
        produceResult($ids);
    } catch (PDOException $e){
        produceError($e->getMessage());
    }   
    

?>

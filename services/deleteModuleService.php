<?php

    /**
    * Ce script est un service qui prend en parametre le id d'un module, 
    * et envoie tous les informations de ce module, avec l'historique de fonctionnement,
    * pour tout les evenements qui sont pas deja vu par l'utilisateur.
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

    if(isset($_GET["id"])){
        if(is_numeric($_GET["id"]))
            try{
                $data = new DataLayer();
                $id = $_GET["id"];

                /**
                 * supprimer le record de fonctionnement, puis le module
                 * L'ORDRE EST IMPORTANT POUR PAS AVOIR DES ERREURS!
                 */
                $data->deleteModulRecodLog($id);
                if(!$data->deleteModuleData($id)){
                    throw new Exception("Id n'existe pas");
                }
                produceResult("module est bien supprime");

            } catch (PDOException $e){
                produceError($e->getMessage());
            }
            catch (Exception $e){
                produceError($e->getMessage());
            }
        else{
            produceError("id n'est pas numeric");    
        }
    }
    else{
        produceError("id n'est pas donne");
    }

?>
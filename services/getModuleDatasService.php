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

    $before_date = date("Y-m-d H:i:s"); // recupere le temps qu'on a executer ce script

    if(isset($_GET["id"])){
        if(is_numeric($_GET["id"]))
            try{
                $data = new DataLayer();
                $id = $_GET["id"];

                $basicData = $data->getBasicInfoById($id); // recupere les basic datas de ce module
                if(empty($basicData)){
                    throw new Exception("Id $_GET[id] n'existe pas ");
                }
                $warningMsg = $data->getUnseenWarningMsg($id); // recupere les warning qui sont pas deja vu pour ce module
                $okMsg = $data->getUnseenOkMsg($id); // recupere les ok msg qui sont pas encore deja vu pour ce module
                $data->changeToSeen($id, $before_date); // les informations qui sont inscrit avant l'execution de ce script sont maintenant deja vu 'seen'

                $result = ["basicData" => $basicData, "warningMsg" => $warningMsg, "okMsg" => $okMsg];
                produceResult($result);

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

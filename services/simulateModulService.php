<?php

    /**
    * Ce script est un service qui prend rien en parametre, le but 
    * pour simuler un module, il va envoyer la status de la message 
    * fournie par le module.
    * A chaque appele de ce service, on va choisir un module en hazard,
    * et puis on va fournir des message en hazard qui vont jouer le role
    * des messages envoyer par le module.
    * les messages ont deux type : warning et ok.
    * le type warning : dans le cas de dysfonctionnement d'une variable de module.
    * dans cette cas on va envoye un notification a l'utilisateur.
    * Apres obtenir quelque informations, on va le stocker dans le base de donnes.
    *
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

    /**
     * cette fonction est pour simuler un module,
     * elle produit des valeurs aleatoires qui sont envoye par le module,
     * et elle stock ces valeurs dans le base de donnes.
     * elle produit aussi un seul message par appel de type aleatoire: ok ou warning.
     * et elle le stock dans le table de historique de fonctionnement "module_log_data"
     */
    function simulate_modul($data_sql, $id){
        global $warning_msg, $ok_msg;
        
        
        $datas = $data_sql->getBasicInfoById($id)[0];
        // recupere les donnes temperature. nb data envoye ...
        $temperature = $datas["temp_data"];
        $fonc_time = $datas["fonc_time_data"];
        $send_nb = $datas["send_data"];
        $recv_nb = $datas["recv_data"];
        $work_cond = $datas["work_cond_data"];

        // produit des nombre au hazard pour l'ajouter au info au dessus

        $temperature += rand(-5,5);
        $fonc_time += strtotime("now") - strtotime($datas["update_date"]);
        $send_nb += rand(0,100);
        $recv_nb += rand(0,100);

        $updated_data = ["temp_data"=>$temperature, "fonc_time_data"=>$fonc_time,
        "send_data"=>$send_nb, "recv_data"=>$recv_nb, "work_cond_data"=>$work_cond];

        if(!$data_sql->updateBasicData($updated_data, $id)){
            throw new Exception("Data not updated");
        }

        $logInfo = array();
        // envoie un warning si temperature est plus que 30 C
        if($temperature > 30){
            $logInfo = ['msg_type'=>'warning', 'msg_data'=>"temperature eleve",
            "msg_seen_status"=>"unseen"];
            if(!$data_sql->addModuleLog($logInfo, $id)){
                throw new Exception("Data log not updated");
            }
            produceResult(['type'=>"warning", "id"=>$id,'message'=>"temperature eleve"]);
            return;
        }

        // envoie des messages par le modules
        // probabilite de warning est 10%
        $p = rand(0,100);
        if($p <= 10){
            $msg = $warning_msg[array_rand($warning_msg, 1)];
            $logInfo = ['msg_type'=>'warning', 'msg_data'=>$msg,
            "msg_seen_status"=>"unseen"];
            if(!$data_sql->addModuleLog($logInfo, $id)){
                throw new Exception("Data log not updated");
            }
            produceResult(['type'=>"warning", "id"=>$id,'message'=>$msg]);
            return;
        } 

        else{
            $msg = $ok_msg[array_rand($ok_msg, 1)];
            $logInfo = ['msg_type'=>'ok', 'msg_data'=>$msg,
            "msg_seen_status"=>"unseen"];
            if(!$data_sql->addModuleLog($logInfo, $id)){
                throw new Exception("Data log not updated");
            }
            produceResult(['type'=>"ok", "id"=>$id,'message'=>$msg]);
            return;
        }
    }
    

    // les options pour le type de message.
    $type_options = ["ok", "warning"];

    // un list qui contient des messages de type warning
    $warning_msg = ["mauvaise connexion", "sensor erreur"];

    // un liste pour les messages de type ok
    $ok_msg = ["fonctionne bien", "bon temperature", "pas de problemes!"];


    

    try{
        $data = new DataLayer();
        $ids = $data->getModulesId();
        
        if(empty($ids)){
            return produceResult(['type'=>'ok' ,'message'=>"pas de module"]);
        }
        $id_rand = $ids[array_rand($ids, 1)]["m_id"]; // obtenir un module en hazard
        simulate_modul($data, $id_rand);
        
    } catch (PDOException $e){
        produceError($e->getMessage());
    }   
    

?>

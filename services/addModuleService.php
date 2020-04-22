<?php

    /**
    * Ce script est un service qui va obtenir les parametres en GET,
    * ou il y'a les informations a propos de la module a ajouter.
    * Ce script produit un message json, qui va etre analyser 
    * par un fichier javascript et afficher les resultats
    * a le client.
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
     * cette fonction va verifie les parametre de get qui 
     * sont suppose de type string, et ne sont pas empty.
     */
    function check_param($name){
        global $validArg, $basic_info, $error_msg;
        $data = filter_input(INPUT_GET, $name, FILTER_SANITIZE_STRING);
        if($data == NULL){
            $validArg = FALSE;
            $error_msg = "$name, valeur absente";
            return FALSE;
        }
        elseif(trim($data) == ""){
            $validArg = FALSE;
            $error_msg = "$name, valeur vide";
            return FALSE;
        }
        else{
            $basic_info[$name] = $data;
            return TRUE;
        }
    }

    /**
     * cette fonction va stocker le valeur des choix multiples dans le tableau datas.
     * si le valeur existe dans get alors on stock true, sinon on met false.
     */
    function check_choice($name){
        global $datas;
        if(isset($_GET[$name])){
            if($_GET[$name] == "on"){
                $datas[$name] = TRUE;
            }
            else{
                $datas[$name] = FALSE;
            }
        }
        else{
            $datas[$name] = FALSE;
        }
    }

    /**
     * la fonction validateGet va verifier tout les parametres de get
     * qui sont necessaire pour enregistrer le module.
     * s'il y a un erreur, cet erreur va etre stocker dans $error_msg et 
     * la fonction va s'arreter.
     * Si y'on a pas des erreurs, la fonction va stocker les valeurs dans 
     * deux tableaux $basic_info et $datas
     */
    function validateGet(){
        global $validArg, $basic_info, $datas, $error_msg;
        if(!check_param("nom")){return;}
        if(!check_param("type")){return;}

        $data = filter_input(INPUT_GET, "id", FILTER_SANITIZE_NUMBER_INT);
        if($data == NULL){
            $validArg = FALSE;
            $error_msg = "le valeur de ID n'est pas valide ou n'existe pas";
            return;
        }
        $basic_info["id"] = $data;
        
        if(isset($_GET["description"])){ 
            $basic_info["description"] = $_GET["description"];
        }
        else{
            $basic_info["description"] = "";
        }

        check_choice(temperature);
        check_choice(fonction_time);
        check_choice(send_data);
        check_choice(receive_data);
        check_choice(working_cond);
    }

    $validArg = TRUE;
    $basic_info = array();
    $datas = array();
    $error_msg = "";

    validateGet();

    if ($validArg)
        try{
            $log_datas = array("msg_type"=>"create", 
                            "msg_data"=> "add : ".$basic_info["nom"].", ".$basic_info["type"],
                            "msg_seen_status" => "seen");

            $data = new DataLayer();
            if(!$data->addModule($basic_info, $datas)){ // verifie si c bien enregistre
                throw new Exception("Le module n'est pas enregistre !");
            }
            
            // ajouter l'evenement de creation de module.
            if(!$data->addModuleLog($log_datas, $basic_info["id"])){
                throw new Exception("Le module log n'est pas enregistre !");
            }

            produceResult("le module était bien enregistré !!");
        
        } catch (PDOException $e){
            produceError($e->getMessage());
        } catch (Exception $e){
            produceError($e->getMessage());
        }  
    else
        produceError($error_msg);


?>

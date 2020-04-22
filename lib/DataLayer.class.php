<?php
require_once("lib/db_parms.php");

/**
 * le class DataLayer est un class pour communiquer avec le database.
 * Dans db_parms.php on peut trouver le nom,password de la compte ainsi
 * que le database a utiliser.
 */

 Class DataLayer{
    private $connexion;
    public function __construct(){

            $this->connexion = new PDO(
                       DB_DSN, DB_USER, DB_PASSWORD,
                       [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                       ]
                     );

    }

    /*
    ---------------------------------------------------
      Fonctions pour ajouter et modifier les donnes
    ---------------------------------------------------
    */

    /**
     * addModule methode pour ajouter un module a database,
     * le parametre basic_info est un array avec cle: id,nom,type,description
     * avec les valeur donne par l'utilisateur.
     * 
     * datas est un array qui contient le choix de l'utilisateur,
     * les cles sont : temperature, fonction_time, send_data, receive_data, working_cond
     * 
     */
    function addModule($basic_info, $datas){
      $sql = <<<EOD
      insert into basic_info (m_id, m_nom, m_type, m_descr, m_date, temp_show,
      fonc_time_show, send_data_show, recv_data_show, work_cond_show, update_date)
      values
      (:id, :nom, :type, :description, now(), :temp, :fonc_time, :send_data, :receive_data, :working_cond, now())
EOD;
      $stmt = $this->connexion->prepare($sql);
      
      $stmt->bindValue(':id', $basic_info["id"], PDO::PARAM_INT);
      $stmt->bindValue(':nom', $basic_info["nom"], PDO::PARAM_STR);
      $stmt->bindValue(':type', $basic_info["type"], PDO::PARAM_STR);
      $stmt->bindValue(':description', $basic_info["description"], PDO::PARAM_STR);

      $stmt->bindValue(':temp', $datas["temperature"], PDO::PARAM_INT);
      $stmt->bindValue(':fonc_time', $datas["fonction_time"], PDO::PARAM_INT);
      $stmt->bindValue(':send_data', $datas["send_data"], PDO::PARAM_INT);
      $stmt->bindValue(':receive_data', $datas["receive_data"], PDO::PARAM_INT);
      $stmt->bindValue(':working_cond', $datas["working_cond"], PDO::PARAM_INT);

      $stmt->execute();
      return $stmt->rowCount() == 1;
    }


    /**
     * update les donnes de modules, temperature, temps de fonctionnement
     * , le nombre de data qu'il a recu, nombre de data envoye et l'etat de marche
     * 
     * Les donne sont passe en parametre sous forme d'un tableau associative,
     * ou les cles sont temp_data, fonc_time_data, send_data, recv_data et work_cond_data
     * 
     * Le module affecte est celle qui a l'id passe en parametre.
     * 
     */
    function updateBasicData($datas, $id){
      $sql = <<<EOD
      update basic_info
      set temp_data = :temp_data, fonc_time_data = :fonc_time_data, send_data = :send_data,
      recv_data = :recv_data, work_cond_data = :work_cond_data, update_date = now()
      where m_id = :id
EOD;

      $stmt = $this->connexion->prepare($sql);

      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->bindValue(':temp_data', $datas["temp_data"], PDO::PARAM_INT);
      $stmt->bindValue(':fonc_time_data', $datas["fonc_time_data"], PDO::PARAM_INT);
      $stmt->bindValue(':send_data', $datas["send_data"], PDO::PARAM_INT);
      $stmt->bindValue(':recv_data', $datas["recv_data"], PDO::PARAM_INT);
      $stmt->bindValue(':work_cond_data', $datas["work_cond_data"], PDO::PARAM_STR);
      
      $stmt->execute();
      return $stmt->rowCount() == 1;
    }

    /**
     * Ajouter le historique de fonctionnement a database.
     * les informations sont passe en parametre sous forme d'un 
     * array associative, ou les cles sont : msg_type qui est le type
     * de message, et msg_data qui represente le contenu de message,
     * et msg_seen_status si le message est vu ou non
     * 
     * Ces informations correspond au module de l'id passe en parametre.
     * 
     */
    function addModuleLog($datas, $id){
      $sql = <<<EOD
      insert into module_log_data (m_id, msg_type, msg_data, 
      msg_seen_status, ins_date)
      values
      (:m_id, :msg_type, :msg_data, :msg_seen_status, now())
EOD;
      $stmt = $this->connexion->prepare($sql);
      
      $stmt->bindValue(':m_id', $id, PDO::PARAM_INT);
      $stmt->bindValue(':msg_type', $datas["msg_type"], PDO::PARAM_STR);
      $stmt->bindValue(':msg_data', $datas["msg_data"], PDO::PARAM_STR);
      $stmt->bindValue(':msg_seen_status', $datas["msg_seen_status"], PDO::PARAM_STR);
      $stmt->execute();
      return $stmt->rowCount() == 1;
    }

    /**
     * Changer tout le status des evennement envoye a seen 
     * par l'utilisateur qui ont deja ecrit en un date inferieur
     * que celle passe en parametre.
     * 
     */
    function changeToSeen($id, $before_date){
      $sql = <<<EOD
      update module_log_data 
      set msg_seen_status='seen'
      where m_id=:id and ins_date < :before_date
EOD;
      $stmt = $this->connexion->prepare($sql);
            
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->bindValue(':before_date', $before_date, PDO::PARAM_STR);
      $stmt->execute();
    }




    /*
    ---------------------------------------------------------
      Fonctions pour recuperer les donnes 
    ---------------------------------------------------------
    */


    /**
     * Recupere une liste de tout les id de modules dans le database.
     */
    function getModulesId(){
      $sql = <<<EOD
      select m_id from basic_info
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * Recupere les informations necessaire (id, nom, type) pour identifier un module, 
     * pour tout les modules enregistre.
     *  
     */
    function getModuleSmallInfos(){
      $sql = <<<EOD
      select m_id, m_nom, m_type
      from basic_info
      order by m_date desc
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * Recupere les informations initiales de module d'un id specifique passer 
     * en parametre dans le database
     */
    function getBasicInfoById($id){
      $sql = <<<EOD
      select * from basic_info where m_id=:id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * Recupere tout les modules qui ont des messages de type warning,
     * et qui sont pas encore deja vu par l'utilisateur.
     */
    function getModuleWithWarning(){
      $sql = <<<EOD
      SELECT 
      m_id, m_nom, m_type 
      from basic_info 
      where basic_info.m_id in 
      ( select module_log_data.m_id 
      from module_log_data 
      where module_log_data.msg_type='warning' and module_log_data.msg_seen_status='unseen')
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();

    }

    /**
     * recupere tout les evenements de type warning, 
     * et qui sont pas encore deja vu par l'utilisateur,
     * pour un module de l'id passe en parametre.
     */
    function getUnseenWarningMsg($id){
      $sql = <<<EOD
      SELECT * 
      FROM module_log_data 
      WHERE m_id=:id and msg_type = 'warning' and msg_seen_status='unseen'
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * recupere tout les evenements de type ok, 
     * et qui sont pas encore deja vu par l'utilisateur,
     * pour un module de l'id passe en parametre.
     */
    function getUnseenOkMsg($id){
      $sql = <<<EOD
      SELECT * 
      FROM module_log_data 
      WHERE m_id=:id and msg_type = 'ok' and msg_seen_status='unseen'
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /*
    ----------------------------------------------------------
      PART: obtenir des infomrations pour exporter
    ----------------------------------------------------------
    */

    /**
     * obtenir tout les modules enregistres.
     */
    function getAllModulesDatas(){
      $sql = <<<EOD
      SELECT m_id as ID, m_nom as Nom, m_type as Type,
       m_descr as Description, m_date as 'date enregistrement',
      temp_data as temperature, fonc_time_data as 'duree fonctionnement',
       send_data as 'nombre data envoye', recv_data as 'nombre data recu',
        work_cond_data as 'etat de marche', update_date as 'date update' 
        FROM basic_info
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /**
     * obtenir touts les evenement de module d'un 
     * id passer en parametre.
     */
    function getEventHistory($id){
      $sql = <<<EOD
      SELECT m_id as ID, msg_type as 'type de message',
       msg_data as 'Message', msg_seen_status as "Status", 
       ins_date as "Date de message"
      FROM module_log_data
      WHERE m_id=:id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();
    }

    /*
    ----------------------------------------------------------
      PART : supprimer les donnes
    ----------------------------------------------------------
    */


    /**
     * supprimer l'historique de fonctionnement d'un module 
     * d'un id passe en parametre.
     * Cette fonction va le supprimer completement, sans faire une copie.
     */
    function deleteModulRecodLog($id){
      $sql = <<<EOD
      delete 
      FROM module_log_data 
      WHERE m_id=:id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
    }

    /**
     * supprimer les data qui forme le module, comme : Id, nom ...
     * Cet fonction va supprimer ces informations completement sans faire un copie.
     * 
     * Cette fonction va donner un erreur si le module de cet id a encore des evnement
     * dans le tableau de log data.
     * 
     * Il faut executer deleteModulRecordLog avant d'executer cette fonction.
     * 
     */
    function deleteModuleData($id){
      $sql = <<<EOD
      delete 
      FROM basic_info
      WHERE m_id=:id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->rowCount() == 1;
    }

}
?>

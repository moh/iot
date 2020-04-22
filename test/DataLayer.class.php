<?php
require_once("db_parms.php");

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
                        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                       ]
                     );

    }


    // fonctions pour recuperer les donnes 



    
    function getFamilies(){
      $sql = <<<EOD
      select * from basic_info
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->execute();
      return $stmt->fetchAll();
    }


    
    function getNbMemberById($id){
      $sql = <<<EOD
      select count(id) as nb
	  from details_family where id_spec=:id
EOD;
      $stmt = $this->connexion->prepare($sql);
      $stmt->bindValue(':id', $id, PDO::PARAM_INT);
      $stmt->execute();
      return $stmt->fetchAll();
    }

}
?>

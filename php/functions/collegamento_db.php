<?php
	//per funzionare è necessario cambiare le credenziali di accesso con le vostre 
	function pdoDB(){
      try{
        $hostname = "localhost";
        $dbname = "my_pietroonofriojuniormaiel"; //da modificare
        $user = "pietroonofriojuniormaiel"; //da modificare
        $pass = ""; //da modificare se si possiede una password per l'accesso al db
        $db = new PDO("mysql:host=$hostname;dbname=$dbname", $user, $pass);
        return $db;
      }catch(PDOException $e){
      		echo "Errore:" . $e->getMessage();
            die();
      }
    }
?>

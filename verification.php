<?php
    require_once('php/functions/collegamento_db.php');
    require_once('php/functions/utente.php');
    require_once('php/verificationById.php');


	if(isset($_GET['id'])){
        $pdo = pdoDB();
        $id = $_GET['id'];
        $utente = getUtenteById($pdo,$id);
        
        if($utente -> rowCount() > 0){
        	if(verificationById($pdo,$id))
            	echo 'Email verificata!';
     		else
            	echo "Errore durante la verifica";
        }
        else
        	echo "Email o username inesistente";
    }


?>

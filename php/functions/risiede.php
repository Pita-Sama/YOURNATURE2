<?php
	function getRisiede($pdo,$id_user){
    	$sql = "SELECT *
        		FROM risiede
                WHERE id_user = :id_user";
        
        $query = $pdo -> prepare($sql);
    	$query -> bindParam(":id_user",$id_user);
        $query -> execute();
        return $query;
    }
    
    function setRisiede($pdo,$id_user,$id_indirizzo){
    	$sql = "INSERT INTO risiede(id_user,id_indirizzo)
        			VALUES(:id_user,:id_indirizzo)";
        
        $query = $pdo -> prepare($sql);
    	$query -> bindParam(":id_user",$id_user);
        $query -> bindParam(":id_indirizzo",$id_indirizzo);
        return $query -> execute();
    }

?>
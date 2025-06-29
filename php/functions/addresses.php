<?php
	function getAddressById($pdo,$id_address){
    	$sql = "SELECT *
            	FROM indirizzi
                WHERE id = :id_address";
                    
       	$query = $pdo -> prepare($sql);
       	$query -> bindParam(':id_address', $id_address);
       	$query -> execute();
        
        return $query;
    }
    
    function getAddressByViaCittaCap($pdo,$via,$citta,$cap){
    	$sql = "SELECT *
            	FROM indirizzi
                WHERE via = :via AND citta = :citta AND cap = :cap";
                    
       	$query = $pdo -> prepare($sql);
        $query -> bindParam(":via",$via);
        $query -> bindParam(":citta",$citta);
        $query -> bindParam(":cap",$cap);
       	
        $query -> execute();
        
        return $query;
    }
    
    function getAddress($pdo,$nome,$via,$citta,$cap){
    	$sql = "SELECT *
            	FROM indirizzi
                WHERE nome = :nome AND via = :via AND citta = :citta AND cap = :cap";
                    
       	$query = $pdo -> prepare($sql);
       	$query -> bindParam(":nome",$nome);
        $query -> bindParam(":via",$via);
        $query -> bindParam(":citta",$citta);
        $query -> bindParam(":cap",$cap);
       	
        $query -> execute();
        
        return $query;
    }
    
    function setAddress($pdo,$nome,$via,$citta,$cap){
    	$sql = "INSERT INTO indirizzi(nome,via,citta,cap)
        			VALUES(:nome,:via,:citta,:cap)";
                    
        $query = $pdo -> prepare($sql);
        $query -> bindParam(":nome",$nome);
        $query -> bindParam(":via",$via);
        $query -> bindParam(":citta",$citta);
        $query -> bindParam(":cap",$cap);
        
        return $query -> execute();
    }
?>
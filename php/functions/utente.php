<?php
	function getUtente($pdo,$username,$email){
    	$sql = "SELECT * 
        		FROM users 
                WHERE username=:username || email=:email";
        
        $stm = $pdo -> prepare($sql);
        $stm -> bindParam(":username",$username);
        $stm -> bindParam(":email",$email);
        $stm -> execute();
        return $stm;
    }
    
    function getUtenteByUsername($pdo,$username){
    	$sql = "SELECT * 
        		FROM users 
                WHERE username=:username";
        
        $query = $pdo -> prepare($sql);
        $query -> bindParam(":username",$username);
        $query -> execute();
        return $query;
    }
    
	function getUtenteById($pdo,$id){
    	$sql = "SELECT * 
        		FROM users 
                WHERE id=:id";
        
        $stm = $pdo -> prepare($sql);
        $stm -> bindParam(":id",$id);
        $stm -> execute();
        return $stm;
    }
    
    function setUtente($pdo,$username,$email,$password,$random_salt,$punti,$verifica){
    	$query = "INSERT INTO users(username, email, pass, salt,punti,verifica) 
        			VALUES(:username, :email, :password, :salt,:punti,:verifica)";
        
        $newUtente = $pdo->prepare($query);
        $newUtente->bindParam(':username', $username);
        $newUtente->bindParam(':email', $email);
        $newUtente->bindParam(':password', $password);
        $newUtente->bindParam(':salt', $random_salt);
        $newUtente->bindParam(':punti', $punti);
        $newUtente->bindParam(':verifica', $verifica);
        
        return $newUtente -> execute();
    }

?>

<?php
	function verificationById($pdo,$id){
    	$sql = "UPDATE users
        		SET verifica = 1
                WHERE id=:id";
                
       	$stm = $pdo -> prepare($sql);
        $stm -> bindParam(':id',$id);
        return $stm -> execute();
    }
?>

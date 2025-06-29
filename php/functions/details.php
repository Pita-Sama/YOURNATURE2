<?php
	
	//OTTIENI QUANTITA DI UN PRODOTTO BASANDOSI SU ID_ORDINE E ID_PRODOTTO
	function getDetail_quantity($pdo,$id_ordine,$id_prodotto){
    	$sql = "SELECT quantita
            	FROM dettagli
                WHERE id_ordine = :id_ordine AND id_prodotto = :id_prodotto";
                    
       	$query = $pdo -> prepare($sql);
       	$query -> bindParam(':id_ordine', $id_ordine);
        $query -> bindParam(':id_prodotto', $id_prodotto);
       	$query -> execute();
        
        return $query;
    }
    
    function getDetail($pdo,$id_ordine){
    	$sql = "SELECT *
            	FROM dettagli
                WHERE id_ordine = :id_ordine";
                    
       	$query = $pdo -> prepare($sql);
       	$query -> bindParam(':id_ordine', $id_ordine);
       	$query -> execute();
        
        return $query;
    }

	function setDetail($pdo,$id_ordine,$id_prodotto,$quantita){
    	$sql = "INSERT INTO dettagli(id_ordine,id_prodotto,quantita)
                	VALUES(:id_ordine,:id_prodotto,:quantita)";
        
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':id_ordine', $id_ordine);
        $query -> bindParam(':id_prodotto', $id_prodotto);
        $query -> bindParam(':quantita', $quantita);
        
        return $query -> execute();
    }
    
    function updateQuantityDetail($pdo,$id_ordine,$id_prodotto,$quantita){
    	$sql = "UPDATE dettagli
                SET quantita = :quantita
                WHERE id_ordine = :id_ordine AND id_prodotto = :id_prodotto";
        
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':id_ordine', $id_ordine);
        $query -> bindParam(':id_prodotto', $id_prodotto);
       	$query -> bindParam(':quantita', $quantita);
        return $query -> execute();
    }
?>
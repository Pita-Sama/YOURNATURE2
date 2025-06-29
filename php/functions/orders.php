<?php
	function get_onGoingOrder($pdo,$user_id){
    	$sql = "SELECT *
            	FROM ordini 
                WHERE user_id=:user_id AND stato = 'in corso'";
       	
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':user_id', $user_id);
        $query -> execute();
        return $query;
    }
    
    function getPayedOrder($pdo,$user_id){
    	$sql = "SELECT *
            	FROM ordini 
                WHERE user_id=:user_id AND stato='pagato'";
       	
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':user_id', $user_id);
        $query -> execute();
        return $query;
    }
    
    function getOrder($pdo,$user_id){
    	$sql = "SELECT *
            	FROM ordini 
                WHERE user_id=:user_id";
       	
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':user_id', $user_id);
        $query -> execute();
        return $query;
    }
    
    function setOrder($pdo,$user_id){
    	$sql = "INSERT INTO ordini(totale,stato,data_spedizione,data_consegna,user_id,id_indirizzo)
                		VALUES(0,'in corso',NULL,NULL,:user_id,NULL)";
                        
        $query = $pdo -> prepare($sql);
        $query -> bindParam(':user_id', $user_id);
        return $query -> execute();
    }
    
    function updateOrder($pdo,$order_id,$totale){
    	$sql = "UPDATE ordini
        		SET totale = totale + :totale
                WHERE id = :order_id";
                
        $query = $pdo -> prepare($sql);
        
        $query -> bindParam(':totale', $totale);
        $query -> bindParam(':order_id', $order_id);
        return $query -> execute();    
    }
    
    function setAddressOrder($pdo,$order_id,$address_id){
    	$sql = "UPDATE ordini
        		SET id_indirizzo = :address_id
                WHERE id = :order_id";
                
        $query = $pdo -> prepare($sql);
        
        $query -> bindParam(':address_id', $address_id);
        $query -> bindParam(':order_id', $order_id);
        
        return $query -> execute(); 
    }
    
    function setOrderStatus($pdo,$order_id,$status){
    	$sql = "UPDATE ordini
        		SET stato = :status
                WHERE id = :order_id";
                
        $query = $pdo -> prepare($sql);
        
        $query -> bindParam(':status', $status);
        $query -> bindParam(':order_id', $order_id);
        return $query -> execute(); 
    }
?>
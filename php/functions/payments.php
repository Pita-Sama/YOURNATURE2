<?php
	function setPayment($pdo,$totale,$data_pagamento,$transaction_id,$stato,$metodo,$valuta,$id_ordine){
    	$sql = "INSERT INTO pagamenti(totale,data_pagamento,transaction_id,stato,metodo,valuta,id_ordine)
        			VALUES(:totale,:data_pagamento,:transaction_id,:stato,:metodo,:valuta,:id_ordine)";
                    
        $query = $pdo -> prepare($sql);
        $query ->bindParam(':totale',$totale);
        $query ->bindParam(':data_pagamento',$data_pagamento);
    	$query ->bindParam(':transaction_id',$transaction_id);
        $query ->bindParam(':stato',$stato);
        $query ->bindParam(':metodo',$metodo);
        $query ->bindParam(':valuta',$valuta);
        $query ->bindParam(':id_ordine',$id_ordine);
        
        return $query -> execute();
    }
    
    function getPaymentsByOrderId($pdo,$order_id){
    	$sql = "SELECT *
        		FROM pagamenti
                WHERE id_ordine = :order_id";
                
       
       	$query = $pdo -> prepare($sql);
        $query -> bindParam(":order_id",$order_id);
        $query -> execute();
        return $query;
    	
    }
?>
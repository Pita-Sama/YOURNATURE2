<?php
	function invioMail($mailDest, $name, $id){
	        $url = "https://pietroonofriojuniormaiel.altervista.org/yourNATURE/verification.php?id=$id";
	    	$nomeMittente = "YOURnature";
	        $mailMitt = "no-reply@YOURnature.it";
	        
	        $mailOgg = "Verifica il tuo account";
	        $mailMess = "Ciao $name, clicca qui per verificare l'account:$url";
	        
	        $mailHeaders = "From: " . $nomeMittente . " <" . $mailMitt . ">\r\n";
	        $mailHeaders .= "X-Mailer: PHP/" . phpversion() . "\r\n";
	        $mailHeaders .= "MIME-Version: 1.0\r\n";
	        $mailHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";
	
	        mail($mailDest,$mailOgg,$mailMess,$mailHeaders);
	}

?>

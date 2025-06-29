<?php
	require_once "functions/collegamento_db.php";
    require_once "functions/products.php";
    require_once "functions/orders.php"; 
    require_once "functions/details.php"; 
	session_start();
    
    if (!isset($_SESSION['user'])) {
      header('Location: ../login.php');
      echo json_encode([]);
      exit();
	}
    
    
    $user_id = $_SESSION['user'];
    $action = $_POST['action'] ?? 'get';

try {   
   	$pdo = pdoDB();
    
    switch($action){
		case 'add':
        	$product_name = $_POST['product'] ?? '';
        	$quantita = $_POST['quantity'] ?? 1;
            
        	//controllare se esiste già un carrello
        	$order = get_onGoingOrder($pdo,$user_id);
            
            if($order -> rowCount() <= 0){
            	$setOrder = setOrder($pdo,$user_id);
                $order = getOrder($pdo,$user_id);
            }
            	
            $order = $order -> fetch(PDO::FETCH_ASSOC);
            
            $id_ordine = $order['id'];
            
            $product = getProductByName($pdo,$product_name);
            $product = $product -> fetch(PDO::FETCH_ASSOC);
			$id_prodotto = $product['id'];
            
            //controllare se l'utente ha già inserito precedentemente il prodotto nel carrello
            $result = getDetail_quantity($pdo,$id_ordine,$id_prodotto);
            if($result -> rowCount() <= 0)
            	//creazione nuova tabella
            	$result = setDetail($pdo,$id_ordine,$id_prodotto,$quantita);
            
            else
            	//aggiunta nel caso il prodotto già esiste nel carrello
            	$result = updateQuantityDetail($pdo,$id_ordine,$id_prodotto,$quantita);
            
            
            $update = updateOrder($pdo,$id_ordine,$product['prezzo']);
            
            echo json_encode(['success' => true, '$setOrder' => $setOrder, 'order' => $order, 'user_id' => $user_id, 'action' => $action]);
    		break;
        
        case 'remove':
        
        	$product_name = $_POST['product'] ?? '';
        
        	//prendo l'id del prodotto 
        	$product = getProductByName($pdo,$product_name);
            $product = $product -> fetch(PDO::FETCH_ASSOC);
			$id_prodotto = $product['id'];
            
            //prendo l'id dell'ordine
            $order = getOrder($pdo,$user_id);
            $order = $order -> fetch(PDO::FETCH_ASSOC);
            $id_ordine = $order['id'];
            
            //ricalcolo del costo totale
            $quantita = getDetail_quantity($pdo,$id_ordine,$id_prodotto) ->fetchColumn();;
            
            $resto = -($product['prezzo'] * $quantita);
            
            $risultato = deleteProduct($pdo,$id_prodotto,$id_ordine);
        	$update = updateOrder($pdo,$id_ordine,$resto);
            
            echo json_encode(['success' => true, 'quantita' => $quantita]);
            break;
        
        case 'get':
        		//ottengo l'id dell'ordine
                $risultato = array();
                $order = get_onGoingOrder($pdo,$user_id);
                $order = $order -> fetch(PDO::FETCH_ASSOC);
            	$id_ordine = $order['id'];
                
                $details = getDetail($pdo,$id_ordine);
              	$totale = 0;
                while($row = $details ->fetch(PDO::FETCH_ASSOC)){
                   
                   $product = getProductById($pdo, $row['id_prodotto']);
                   $product = $product -> fetch();
                   $risultato[] = setProduct($product['nome'],$product['prezzo'],$row['quantita'],$product['immagine']);
                }
                
                echo json_encode($risultato);
                
        	break;
       	
        default:
       		echo json_encode(['error' => 'Azione non valida']);
    }
    
   } catch (Exception $e) {
    // Logga l'errore sul server
    error_log("Errore in gestioneProdotti.php: " . $e->getMessage());
    // Restituisci un JSON con l'errore
    echo json_encode(['error' => 'Si è verificato un errore']);
}

?>
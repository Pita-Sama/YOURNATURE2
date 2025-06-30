<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../libs/stripe-php-17.4.0-beta.1/init.php';
require_once __DIR__ . '/functions/collegamento_db.php';
require_once __DIR__ . '/functions/orders.php';
require_once __DIR__ . '/functions/payments.php';
session_start();

date_default_timezone_set('Europe/Rome'); // Imposta a Roma

try {
    //VERIFICA L'ESISTENZA DELL'UTENTE
    if (!isset($_SESSION['user'])) {
        throw new Exception('Utente non autenticato');
    }

    //LEGGI E VALIDA IL JSON(per vedere se è corretto)
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('JSON non valido');
    }

	//CONTROLLO SULL'ORDER_ID
    if (empty($input['order_id']) || !is_numeric($input['order_id'])) {
        throw new Exception('ID ordine non valido');
    }

    // RECUPERO ORDINE DAL DB
    $pdo = pdoDB();
    
    $pdo->beginTransaction();
    
    $user_id = $_SESSION['user'];
    $order_id = $input['order_id'];
    $address_id = $input['address_id'];

	

    $stmt = get_onGoingOrder($pdo, $user_id);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
	
    if (!$order || $order['user_id'] != $user_id) {
        throw new Exception('Ordine non trovato');
    }
	
    //TOTALE DELL'ORDINE
    $totale = $order['totale'];
    
    //GESTIONE PAGAMENTO
    if (empty($input['payment_token'])) {
        throw new Exception('Token di pagamento mancante');
    }
	
    $payment_method = $input['payment_method'] ?? '';
    switch ($payment_method) {
        case 'carta':
        
        	//CHIAVE PRIVATA
            \Stripe\Stripe::setApiKey('');

            $intent = \Stripe\PaymentIntent::create([
                'amount' => ($totale * 100),
                'currency' => 'eur',
                'payment_method' => $input['payment_token'],
                'confirm' => true,
                'metadata' => ['order_id' => $order_id],
                'return_url' => 'https://' . $_SERVER['HTTP_HOST'] . '/../ordine_confermato.php'
            ]);

            if ($intent->status === 'requires_action') {
                echo json_encode([
                    'success' => true,
                    'requires_action' => true,
                    'client_secret' => $intent->client_secret
                ]);
                exit;
            }

            if ($intent->status !== 'succeeded') {
                throw new Exception('Pagamento non riuscito');
            }

            $transaction_id = $intent->id;
            break;

        /*case 'paypal':
            $transaction_id = 'paypal_' . $input['payment_token'];
            break;

        case 'bank_transfer':
            $transaction_id = 'bank_waiting_' . uniqid();
            break;*/

        default:
            throw new Exception('Metodo di pagamento non supportato');
    }

   
   	//CAMBIO STATO ORDINE (IN_CORSO -> PAGATO) 
    if(!setOrderStatus($pdo,$order_id,'pagato')){
    	throw new Exception("Errore con l'ordine");
    }
    
    //INSERIMENTO IN DETTAGLI DEL PAGAMENTO
    $dataPagamento = date('Y-m-d H:i:s');
	$valuta = "EUR";
    $stato = 'completato';
    if(!setPayment($pdo,$totale,$dataPagamento,$transaction_id,$stato,$payment_method,$valuta,$order_id)){
    	throw new Exception('Errore con il salvataggio del pagamento totale:'. $totale . " dataPagamento:" . $dataPagamento . " transaction_id:" . $transaction_id );
    }
    
    if(!setAddressOrder($pdo,$order_id,$address_id)){
    	throw new Exception("Errore con il salvataggio dell'indirizzo");
    }
    
    //SALVA NELLA SESSIONE IL TRANSACTION_ID
    $_SESSION['transaction_id'] = $transaction_id;
    
    $pdo->commit();
    
    //INVIA UN MESSAGGIO DI SUCCESSO AL CLIENT
    echo json_encode([
        'success' => true,
        'transaction_id' => $transaction_id,
        'message' => 'Pagamento completato con successo'
    ]);

} catch (\Stripe\Exception\ApiErrorException $e) {
    $response = json_encode(['success' => false, 'error' => 'Errore Stripe: ' . $e->getMessage()]);
    $shouldRollback = true;

} catch (Exception $e) {
    $response = json_encode(['success' => false, 'error' => $e->getMessage()]);
    $shouldRollback = true;
    
} finally{
	if ($shouldRollback && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo $response;
}
?>

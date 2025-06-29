<?php
require_once "functions/collegamento_db.php";
require_once "functions/risiede.php";
require_once "functions/orders.php"; 
require_once "functions/addresses.php"; 
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user'];

$json = file_get_contents('php://input');
$data = json_decode($json, true);

try {
    if (!isset($data['action'])) {
        echo json_encode(['success' => false, 
        				'message' => 'Action parameter missing']);
    	exit();
    }

    $action = $data['action'];
    $pdo = pdoDB();

    switch($action) {
        case 'add':
            if (!isset($data['nome']) || !isset($data['citta']) || !isset($data['via']) || !isset($data['cap'])) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Missing address parameters'
                ]);
                exit();
            }
            
            $nome = trim($data['nome']) ?? null;
            $citta = trim($data['citta']) ?? null;
            $via = trim($data['via']) ?? null;
            $via = preg_replace('/^(via|v\.|v\.le)\s*/i', '', trim($via));
            $cap = trim($data['cap']) ?? null;
            
            // Check if address exists
            $risultato = getAddressByViaCittaCap($pdo,$via,$citta,$cap);
            
            if ($risultato->rowCount() > 0){
            	echo json_encode([
                    'success' => false,
                    'message' => 'Indirizzo già esistente'
                ]);
                exit();
            } 
                
            
            
            setAddress($pdo, $nome,$via, $citta,  $cap);
            $risultato = getAddress($pdo, $nome, $via, $citta, $cap);
            $risultato = $risultato->fetch(PDO::FETCH_ASSOC);
            $risiede = setRisiede($pdo, $user_id, $risultato['id']);

            echo json_encode([
                'success' => true,
                'message' => 'Address added successfully'
            ]);
            exit();
            break;
            
        case 'get':
            $risultato = array();
            $risiede = getRisiede($pdo, $user_id);
            
            $addresses = [];
            while($row = $risiede->fetch(PDO::FETCH_ASSOC)) {
                $address = getAddressById($pdo, $row['id_indirizzo']);
                $address = $address->fetch();
                $addresses[] = [
                    "id" => $row['id_indirizzo'],
                    "nome" => $row['nome'] ?? '',
                    "via" => $address['via'],
                    "citta" => $address['citta'],
                    "cap" => $address['cap'],
                ];
            }
            
            echo json_encode([
                'success' => true,
                'addresses' => $addresses
            ]);
            break;
        
        case 'delete':
        	$addressId = $data['addressId'];
            $result = 
            
            echo json_encode([
                'success' => true,
                'message' => 'The address was deleted successfully';
            ]);
            break;
            
        default:
            echo json_encode([
               'success' => false,
               'message' => 'Invalid action'
            ]);
           exit();
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>
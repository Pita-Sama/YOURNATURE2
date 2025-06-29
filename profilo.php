<!DOCTYPE html>
<?php

require_once "php/functions/collegamento_db.php";
require_once "php/functions/orders.php";
require_once "php/functions/products.php";
require_once "php/functions/details.php";
require_once "php/functions/payments.php";
require_once "php/functions/risiede.php";
require_once "php/functions/addresses.php";
require_once "php/functions/utente.php";

session_start();

// Verifica che l'utente sia loggato
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user'];

$pdo = pdoDB();

//ottengo tutti i dati utente
$user_record = getUtenteById($pdo,$user_id);
$user = $user_record -> fetch(PDO::FETCH_ASSOC);
$username = $user["username"];
$email = $user["email"];

//ottengo tutti gli ordini completati
$orders = getPayedOrder($pdo,$user_id);

//per ottenere la lista degli id indirizzo
$risiede = getRisiede($pdo,$user_id);

?>

<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profilo Utente - YOURnature</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/profilo.css">
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>YOURnature</span>
        </div>
        
        <div class="auth-buttons">
            <a href="home.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Torna allo shop</a>
        </div>
    </header>

    <main>
        <div class="profile-container">
            <!-- Sezione Profilo -->
            <div class="profile-section">
                <div class="profile-header">
                    <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Avatar utente" class="profile-avatar">
                    <h1 class="profile-name"><?php echo $username ?></h1>
                    <p class="profile-email"><?php echo $email?></p>
                    
                    <div class="profile-stats">
                        <div class="stat-item">
                            <div class="stat-number">12</div>
                            <div class="stat-label">Ordini</div>
                        </div>
                    </div>
                    
                    <!--<div class="profile-actions">
                        <button class="btn btn-primary"><i class="fas fa-edit"></i> Modifica profilo</button>
                        <button class="btn btn-outline"><i class="fas fa-lock"></i> Cambia password</button>
                    </div> !-->
                </div>
            </div>
            
            <!-- Sezione Ordini Recenti -->
            <div class="profile-section">
                <h2 class="section-title"><i class="fas fa-shopping-bag"></i> I tuoi ordini recenti</h2>
                
                
                	<?php
                    	
                    
                    	if($orders -> rowCount() > 0){
                        	while($row = $orders -> fetch(PDO::FETCH_ASSOC)){
                            	$orderTable = "";
                                
                                $id_ordine = $row['id'];
                                $totale = $row['totale'];
                                
                                $payment_detail = getPaymentsByOrderId($pdo,$id_ordine);
                                $payment_detail = $payment_detail -> fetch(PDO::FETCH_ASSOC);
                                $payment_date = $payment_detail['data_pagamento'];
                                $payment_status = $payment_detail['stato'];
                                
                                $orderTable .= "<div class='order-card'>";
                                
                                $orderTable .= "<div class='order-header'>
                                                    <span class='order-id'>Ordine #$id_ordine</span>
                                                    <span class='order-date'>$payment_date</span>
                                                    <span class='order-status status-completed'>$payment_status</span>
                                                </div>";
                                
                                $orderTable .= "<div class='order-products'>";
                
                                $details = getDetail($pdo,$id_ordine);
                                 
                                while($row = $details ->fetch(PDO::FETCH_ASSOC)){
                                   $product = getProductById($pdo, $row['id_prodotto']);
                                   $product = $product -> fetch();
                                   
                                   $product_image = $product['immagine'];
                                   $product_name = $product['nome'];
                                   $orderTable .= "<div class='order-product'>
                                                      <img src='$product_image' alt='Prodotto' class='product-thumbnail'>
                                                      <div class='product-name'>$product_name</div>
                                                  </div>";
                                }
                                
                                
                                $orderTable .= "</div>";
                                $orderTable .= "<div class='order-total'>Totale: €$totale</div>";
                                $orderTable .= "</div>";
                                
                                echo $orderTable;
                            }
                        }
                    
                    ?>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="#" class="btn btn-outline">Visualizza tutti gli ordini</a>
                </div>
            </div>
            
            <!-- Sezione Indirizzi -->
            <div class="profile-section">
                <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> I tuoi indirizzi</h2>
                <?php
                	if($risiede -> rowCount() > 0){
                    	$addressTable = "";
                    	while($row = $risiede -> fetch(PDO::FETCH_ASSOC)){
                          $id_address = $row['id_indirizzo'];
                          $address_record = getAddressById($pdo,$id_address);
                          $address = $address_record -> fetch(PDO::FETCH_ASSOC);
                          
                          $name = $address['nome'];
                          $via = $address['via'];
                          $city = $address['citta'];
                          $cap = $address['cap'];
                          
                          $addressTable .= "<div class='address-card'>";
                          $addressTable .= "<h3>$name</h3>";
                          $addressTable .= "<p>$via</p>";
                          $addressTable .= "<p>$city</p>";
                          $addressTable .= "<p>$cap</p>";
                          
                          $addressTable .= "<div class='address-actions'>
                                                <button class='btn btn-outline btn-sm delete-address' data-id='$id_address'><i class='fas fa-trash-alt'></i> Elimina</button>
                                            </div>";
                                            
                          $addressTable .= "</div>";
                        }
                        
                        echo $addressTable;
                    }
                	
                ?>
            </div>
        </div>
        
        <!-- Aggiungi questo codice prima della chiusura del </main> -->
        <div class="modal fade" id="addAddressModal" tabindex="-1" aria-labelledby="addAddressModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addAddressModalLabel">Aggiungi nuovo indirizzo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="addressForm" method="POST">
                            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

                            <div class="mb-3">
                                <label for="addressName" class="form-label">Nome indirizzo (es. Casa, Ufficio)</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>

                            <div class="mb-3">
                                <label for="street" class="form-label">Via/Piazza</label>
                                <input type="text" class="form-control" id="via" name="via" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="city" class="form-label">Città</label>
                                    <input type="text" class="form-control" id="citta" name="citta" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="postalCode" class="form-label">CAP</label>
                                    <input type="text" class="form-control" id="cap" name="cap" required>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-outline" data-bs-dismiss="modal">Annulla</button>
                                <button type="submit" class="btn btn-primary">Salva indirizzo</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modifica il pulsante "Aggiungi nuovo indirizzo" per aprire il modal -->
        <button class="btn btn-primary" style="margin-top: 15px;" data-bs-toggle="modal" data-bs-target="#addAddressModal">
            <i class="fas fa-plus"></i> Aggiungi nuovo indirizzo
        </button>

    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-column">
                <h3>YOURnature</h3>
                <p style="color: #bdbdbd; margin-top: 15px;">Il tuo negozio online per piante e accessori per il giardinaggio.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-pinterest-p"></i></a>
                </div>
            </div>
            <div class="footer-column">
                <h3>Contatti</h3>
                <ul>
                    <li><i class="fas fa-map-marker-alt" style="margin-right: 8px;"></i> Via delle Piante, 123</li>
                    <li><i class="fas fa-phone-alt" style="margin-right: 8px;"></i> 333 2261466</li>
                    <li><i class="fas fa-envelope" style="margin-right: 8px;"></i> info@YOURnature.it</li>
                    <li><i class="fas fa-clock" style="margin-right: 8px;"></i> Lun-Ven: 9:00-18:00</li>
                </ul>
            </div>
        </div>
        <div class="copyright">
            &copy; 2025 YOURnature - Tutti i diritti riservati
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    	document.getElementById('addressForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Raccogli i dati dal form
            const formData = {
                nome: document.getElementById('nome').value,
                via: document.getElementById('via').value,
                citta: document.getElementById('citta').value,
                cap: document.getElementById('cap').value,
                action:  "add"
            };

            try {
                const response = await fetch('php/gestioneIndirizzi.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                });

                const result = await response.json();

                if (result.success) {
                    alert('Indirizzo salvato con successo!');
                    window.location.reload();
                } else {
                    alert('Errore: ' + result.message);
                }
            } catch (error) {
                console.error('Errore:', error);
                alert('Si è verificato un errore di connessione');
            }
       });
        
      // Gestione eliminazione indirizzo
    	document.querySelectorAll('.delete-address').forEach(button => {
        button.addEventListener('click', function() {
            if (!confirm('Sei sicuro di voler eliminare questo indirizzo?')) {
                return;
            }
            
            const addressId = this.getAttribute('data-id');
            
            const formData = {
                addressId: addressId
                action:  "delete"
            };
            
            fetch('php/gestioneIndirizzi.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(formData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    document.getElementById(`address-${addressId}`).remove();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("Si è verificato un errore durante l'eliminazione");
            });
        });
    });  
    
    </script>
</body>
</html>
<?php


session_start();
require_once 'php/functions/collegamento_db.php';
require_once 'php/functions/details.php';
require_once 'php/functions/products.php';
require_once 'php/functions/orders.php';
require_once 'php/functions/risiede.php';
require_once 'php/functions/addresses.php';
require_once __DIR__ . '/libs/stripe-php-17.4.0-beta.1/init.php';

//chiave privata per Stripe DA INSERIRE
\Stripe\Stripe::setApiKey();

// Verifica che l'utente sia loggato
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user'];
$pdo = pdoDB();

// Recupera l'ordine in corso
$ordine = get_onGoingOrder($pdo, $user_id);

if ($ordine->rowCount() <= 0) {
    header('Location: carrello.php');
    exit();
}
 
//estrazione ordine cliente 
$ordine = $ordine->fetch(PDO::FETCH_ASSOC);

//id ordine IMPORTANTE PER IMPOSTARE L'ORDINE COMPLETATO UNA VOLTA COMPLETATO IL PAGAMENTO
$id_ordine = $ordine['id']; 
$dettagli = getDetail($pdo, $id_ordine);

if ($dettagli->rowCount() <= 0) {
    header('Location: carrello.php');
    exit();
}

// Calcola il totale
$totale = 0;
$prodotti = [];
while ($row = $dettagli->fetch(PDO::FETCH_ASSOC)) {
    $prodotto = getProductById($pdo, $row['id_prodotto']);
    $prodotto = $prodotto->fetch(PDO::FETCH_ASSOC);
    $subtotale = $prodotto['prezzo'] * $row['quantita'];
    $totale += $subtotale;
    
    $prodotti[] = [
        'id' => $row['id_prodotto'],
        'nome' => $prodotto['nome'],
        'immagine' => $prodotto['immagine'],
        'prezzo' => $prodotto['prezzo'],
        'quantita' => $row['quantita'],
        'subtotale' => $subtotale
    ];
}

// Recupera gli indirizzi dell'utente
$risiede = getRisiede($pdo,$user_id);
$indirizzi = array();
while($row = $risiede -> fetch(PDO::FETCH_ASSOC)){
	$indirizzo = getAddressById($pdo,$row['id_indirizzo']);
    $indirizzo = $indirizzo -> fetch(PDO::FETCH_ASSOC);
	$indirizzi[] = $indirizzo;
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - YOURnature</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/checkout.css">
    <script src="https://js.stripe.com/v3/"></script>
    
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>YOURnature</span>
        </div>
        
        <div class="auth-buttons">
            <a href="carrello.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Torna al carrello</a>
        </div>
    </header>

    <main>
        <div class="checkout-steps">
            <div class="step active">
                <div class="step-number">1</div>
                <div class="step-text">Spedizione</div>
            </div>
            <div class="step">
                <div class="step-number">2</div>
                <div class="step-text">Pagamento</div>
            </div>
            <div class="step">
                <div class="step-number">3</div>
                <div class="step-text">Conferma</div>
            </div>
        </div>
        
        <div class="checkout-container">
            <div class="checkout-form">
                <form id="shippingForm" novalidate>
                    <!-- Sezione Indirizzo di Spedizione -->
                    <div class="checkout-form-container">
                        <div class="form-header">
                            <h3><i class="fas fa-truck"></i> Indirizzo di Spedizione</h3>
                        </div>
                        
                        <?php if (isset($indirizzi) > 0): ?>
                            <div class="address-options">
                                <?php foreach ($indirizzi as $indirizzo): ?>
                                    <div class="address-option <?php echo $indirizzo['predefinito'] ? 'active' : ''; ?>">
                                        <input type="radio" name="indirizzo_id" id="address-<?php echo $indirizzo['id']; ?>" 
                                               value="<?php echo $indirizzo['id']; ?>" <?php echo $indirizzo['predefinito'] ? 'checked' : ''; ?>>
                                        <div class="address-details">
                                            <h4><?php echo htmlspecialchars($indirizzo['nome']); ?></h4>
                                            <p>
                                                <?php echo htmlspecialchars($indirizzo['via']); ?><br>
                                                <?php echo htmlspecialchars($indirizzo['cap'] . ' ' . $indirizzo['citta']); ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Form per nuovo indirizzo (nascosto di default) -->
                        <div class="add-new-address" onclick="showAddressForm()">
                            <i class="fas fa-plus"></i>
                            <span>Aggiungi un nuovo indirizzo</span>
                        </div>
                        
                        <!-- Form per nuovo indirizzo (nascosto di default) -->
                        <div id="newAddressForm" style="display: none; margin-top: 20px;">
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="nome">Nome indirizzo (es. Casa, Ufficio)</label>
                                    <input type="text" id="nome" name="nome" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="via">Via</label>
                                <input type="text" id="via" name="via" class="form-control">
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="cap">CAP</label>
                                    <input type="text" id="cap" name="cap" class="form-control">
                                </div>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="citta">Città</label>
                                    <input type="text" id="citta" name="citta" class="form-control">
                                </div>
                            </div>
                            
                            <button type="button" class="btn btn-primary" onclick="saveAddress()">Salva Indirizzo</button>
                        </div>
                    </div>
                    
                    <!-- Sezione Metodo di Pagamento -->
                    <div class="checkout-form-container">
                        <div class="form-header">
                            <h3><i class="fas fa-credit-card"></i> Metodo di Pagamento</h3>
                        </div>
                        
                        <div class="payment-methods">
                            <div class="payment-method active">
                                <input type="radio" name="metodo_pagamento" id="credit-card" value="carta" checked>
                                <i class="far fa-credit-card"></i>
                                <div class="payment-details">
                                    <h4>Carta di Credito</h4>
                                    <p>Paga con carta Visa, Mastercard o American Express</p>
                                </div>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" name="metodo_pagamento" id="paypal" value="paypal">
                                <i class="fab fa-paypal"></i>
                                <div class="payment-details">
                                    <h4>PayPal</h4>
                                    <p>Paga in modo sicuro con il tuo account PayPal</p>
                                </div>
                            </div>
                            
                            <div class="payment-method">
                                <input type="radio" name="metodo_pagamento" id="bank-transfer" value="bonifico">
                                <i class="fas fa-university"></i>
                                <div class="payment-details">
                                    <h4>Bonifico Bancario</h4>
                                    <p>Paga con bonifico bancario (consegna dopo l'accredito)</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form per carta di credito -->
                        <div id="creditCardForm" class="card-element active">
                            <div id="card-element" class="stripe-card-element"></div>
                            <div id="card-errors" role="alert" class="text-danger small mt-2"></div>
                        </div>
                        
                        <!--form per paypal !-->
                        <div id="paypalForm" class="card-element" style="display: none;">
                            <div id="paypal-button-container"></div>
                        </div>
                        
                        <!-- form per bonifico bancario !-->
                        <div id="bankTransferForm" class="card-element" style="display: none;">
                          <div class="bank-transfer-details">
                              <h5><i class="fas fa-info-circle"></i> Istruzioni per il pagamento:</h5>
                              <div class="bank-info">
                                  <p><strong>Intestatario:</strong> Fiori Ribelli di Mario Rossi</p>
                                  <p><strong>IBAN:</strong> IT12X1234512345123456789012</p>
                                  <p><strong>Banca:</strong> Banca Example S.p.A.</p>
                                  <p><strong>Importo:</strong> <span id="bank-amount">€<?= number_format($totale, 2) ?></span></p>
                                  <p><strong>Causale:</strong> Ordine #<span id="bank-reference"><?= $id_ordine ?></span></p>
                              </div>
                              <div class="form-check mt-3">
                                  <input class="form-check-input" type="checkbox" id="confirm-transfer" required>
                                  <label class="form-check-label" for="confirm-transfer">
                                      Confermo di aver preso nota dei dati per il bonifico
                                  </label>
                              </div>
                          </div>
                      </div>
                      
                      
                    </div>
                </form>
            </div>
            
            <!-- Riepilogo Ordine -->
            <div class="order-summary">
                <h3><i class="fas fa-receipt"></i> Riepilogo Ordine</h3>

                <div class="order-items">
                    <?php foreach ($prodotti as $prodotto): ?>
                        <div class="order-item">
                            <div class="order-item-image-container">
                                <img src="<?php echo htmlspecialchars($prodotto['immagine']); ?>" 
                                     alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" 
                                     class="order-item-image">
                            </div>
                            <div class="order-item-details">
                                <div class="order-item-name"><?php echo htmlspecialchars($prodotto['nome']); ?></div>
                                <div class="order-item-meta">
                                    <span class="order-item-price">€<?php echo number_format($prodotto['prezzo'], 2); ?></span>
                                    <span class="order-item-quantity">× <?php echo $prodotto['quantita']; ?></span>
                                </div>
                                <div class="order-item-subtotal">€<?php echo number_format($prodotto['subtotale'], 2); ?></div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    
          	<div class="order-totals">
                    <span>Totale</span>
                    <span>€<?php echo number_format($totale, 2); ?></span>
                </div>
            </div>  
            <button type="button" id="submitForm" class="place-order-btn">
               	<i class="fas fa-lock"></i> Completa l'ordine
            </button>
                
            <div class="secure-checkout">
                <i class="fas fa-lock"></i>
                    <span>Checkout sicuro - i tuoi dati sono protetti</span>
                </div>
            </div>
        </div>
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
    
    	document.getElementById('submitForm').addEventListener('click', handlePaymentSubmit);
    	//chiave pubblica test DA INSERIRE 
        const stripe = Stripe();
    	
        // Inizializza Elements di Stripe
        const elements = stripe.elements();
        const cardElement = elements.create('card', {
            style: {
                base: {
                    fontSize: '16px',
                    color: '#32325d',
                    '::placeholder': {
                        color: '#aab7c4'
                    }
                },
                invalid: {
                    color: '#fa755a'
                }
            }
        });
        cardElement.mount('#card-element');
        
    	function initPayPalButton() {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: {
                                value: '<?= number_format($totale, 2) ?>',
                                currency_code: 'EUR'
                            }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Invia i dati al server
                        completePayment('paypal', data.orderID);
                    });
                }
            }).render('#paypal-button-container');
        }
        
        //gestione pagamento
        async function handlePaymentSubmit() {
        	const paymentMethod = document.querySelector('input[name="metodo_pagamento"]:checked').value;
            const selectedAddress = document.querySelector('input[name="indirizzo_id"]:checked');        
            const newAddressForm = document.getElementById('newAddressForm');

    
            if (!selectedAddress && newAddressForm.style.display === 'none') {
                showAlert('error', 'Seleziona un indirizzo di spedizione o aggiungine uno nuovo');
                return;
            }
            
            if (paymentMethod === 'carta') {
                const { paymentMethod, error } = await stripe.createPaymentMethod({
                    type: 'card',
                    card: cardElement
                });

                if (error) {
                    document.getElementById('card-errors').textContent = error.message;
                    return;
                }

                completePayment('carta', paymentMethod.id);
            } 
        }
        
        // Funzione per completare il pagamento
        async function completePayment(method, token = null) {
        	const selectedAddressId = document.querySelector('input[name="indirizzo_id"]:checked').value;
        
            const paymentData = {
                payment_method: method,         // Es. "credit_card", "paypal", ecc.
                order_id: <?php echo $id_ordine; ?>, // ID ordine da PHP
                address_id : selectedAddressId
            };

            // Aggiungo il token solo se esiste
            if (token) {
                paymentData.payment_token = token;
            }

            // Invio i dati come JSON
            const response = await fetch('php/gestionePagamento.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json', // Specifico che invio JSON
                },
                body: JSON.stringify(paymentData) // Converto l'oggetto in stringa JSON
            });

            const result = await response.json();
            
            if (result.success) {
                window.location.href = 'ordine_confermato.php';
            } else {
                alert(result.error || 'Errore nel pagamento');
            }
        }
        
    	function showAlert(type, message, duration = 5000) {
            // Crea il container principale
            const alertDiv = document.createElement('div');
            alertDiv.className = `stripe-alert stripe-alert-${type}`;
            alertDiv.style.cssText = `
                position: fixed;
                top: 20px;
                left: 50%;
                transform: translateX(-50%) translateY(-30px);
                max-width: 90%;
                width: 100%;
                max-width: 500px;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                display: flex;
                align-items: center;
                justify-content: space-between;
                z-index: 9999;
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.68, -0.55, 0.27, 1.55);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            `;

            // Icona dinamica
            let iconClass;
            switch(type) {
                case 'success':
                    iconClass = 'fa-circle-check';
                    alertDiv.style.backgroundColor = '#f6ffed';
                    alertDiv.style.border = '1px solid #b7eb8f';
                    alertDiv.style.color = '#52c41a';
                    break;
                case 'error':
                    iconClass = 'fa-circle-exclamation';
                    alertDiv.style.backgroundColor = '#fff2f0';
                    alertDiv.style.border = '1px solid #ffccc7';
                    alertDiv.style.color = '#ff4d4f';
                    break;
                case 'warning':
                    iconClass = 'fa-triangle-exclamation';
                    alertDiv.style.backgroundColor = '#fffbe6';
                    alertDiv.style.border = '1px solid #ffe58f';
                    alertDiv.style.color = '#faad14';
                    break;
                default:
                    iconClass = 'fa-circle-info';
                    alertDiv.style.backgroundColor = '#e6f7ff';
                    alertDiv.style.border = '1px solid #91d5ff';
                    alertDiv.style.color = '#1890ff';
            }

            // Contenuto dell'alert
            alertDiv.innerHTML = `
                <div style="display: flex; align-items: center; gap: 12px;">
                    <i class="fas ${iconClass}" style="font-size: 20px;"></i>
                    <span style="font-weight: 500; font-size: 15px;">${message}</span>
                </div>
                <button class="stripe-alert-close" style="
                    background: transparent;
                    border: none;
                    color: inherit;
                    cursor: pointer;
                    font-size: 16px;
                    padding: 0 0 0 15px;
                ">
                    <i class="fas fa-times"></i>
                </button>
            `;

            // Aggiungi al DOM
            document.body.appendChild(alertDiv);

            // Animazione di entrata
            setTimeout(() => {
                alertDiv.style.opacity = '1';
                alertDiv.style.transform = 'translateX(-50%) translateY(0)';
            }, 10);

            // Pulsante di chiusura
            const closeBtn = alertDiv.querySelector('.stripe-alert-close');
            closeBtn.addEventListener('click', () => {
                dismissAlert(alertDiv);
            });

            // Auto-dismiss dopo il timeout
            if (duration) {
                setTimeout(() => {
                    dismissAlert(alertDiv);
                }, duration);
            }

            // Funzione per chiudere l'alert con animazione
            function dismissAlert(element) {
                element.style.opacity = '0';
                element.style.transform = 'translateX(-50%) translateY(-30px)';
                setTimeout(() => {
                    element.remove();
                }, 400);
            }
        }

    
        // Mostra/nascondi form nuovo indirizzo
        function showAddressForm() {
            const form = document.getElementById('newAddressForm');
            form.style.display = form.style.display === 'none' ? 'block' : 'none';
        }
        
        
        // Salva nuovo indirizzo con async/await
        async function saveAddress() {
            // Recupera i valori dal form
            const nome = document.getElementById('nome').value;
            const via = document.getElementById('via').value;
            const cap = document.getElementById('cap').value;
            const citta = document.getElementById('citta').value;

            // Validazione di base
            if (!nome || !via || !cap || !citta) {
                showAlert('error', 'Per favore compila tutti i campi obbligatori');
                return;
            }

            // Crea l'oggetto con i dati dell'indirizzo
            const addressData = {
                nome: nome,
                via: via,
                citta: citta,
                cap: cap,
                action: 'add',
            };

            // Mostra un loader durante la richiesta
            const saveBtn = document.querySelector('#newAddressForm button');
            const originalBtnText = saveBtn.innerHTML;
            saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvataggio...';
            saveBtn.disabled = true;

            try {
                // Invia la richiesta AJAX
                const response = await fetch('php/gestioneIndirizzi.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(addressData)
                });
				
                const data = await response.json();
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Errore nel salvataggio');
                }

                // Ricarica la lista degli indirizzi
                await loadAddresses();
                
                // Resetta il form
                document.getElementById('nome').innerHTML = '';
            	document.getElementById('via').innerHTML = '';
            	document.getElementById('cap').innerHTML = '';
            	document.getElementById('citta').innerHTML = '';
                // Nascondi il form
                document.getElementById('newAddressForm').style.display = 'none';
                // Mostra messaggio di successo
                showAlert('success', 'Indirizzo salvato con successo!');
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', error.message || 'Si è verificato un errore durante il salvataggio');
            } finally {
                // Ripristina il pulsante
                saveBtn.innerHTML = originalBtnText;
                saveBtn.disabled = false;
            }
        }

        // Funzione per caricare gli indirizzi con async/await
        async function loadAddresses() {
            try {
                const action = 'get';
                
                const response = await fetch('php/gestioneIndirizzi.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(action)
                });

                const data = await response.json();
                
                if (!response.ok || !data.success) {
                    throw new Error(data.message || 'Errore nel caricamento degli indirizzi');
                }

                updateAddressList(data.addresses);
            } catch (error) {
                console.error('Error:', error);
                showAlert('error', 'Si è verificato un errore nel caricamento degli indirizzi');
            }
        }
      
      // Funzione per aggiornare la lista degli indirizzi nel DOM
      function updateAddressList(addresses) {
          const addressContainer = document.querySelector('.address-options');
          addressContainer.innerHTML = '';

          addresses.forEach(address => {
              const addressElement = document.createElement('div');
              addressElement.className = `address-option ${address.predefinito ? 'active' : ''}`;
              addressElement.innerHTML = `
                  <input type="radio" name="indirizzo_id" id="address-${address.id}" 
                         value="${address.id}" ${address.predefinito ? 'checked' : ''}>
                  <div class="address-details">
                      <h4>${address.nome}</h4>
                      <p>
                          ${address.via}<br>
                          ${address.cap} ${address.citta}<br>
                      </p>
                  </div>
              `;
              addressContainer.appendChild(addressElement);
          });

          // Aggiungi il pulsante "Aggiungi nuovo indirizzo"
          const addNewBtn = document.createElement('div');
          addNewBtn.className = 'add-new-address';
          addNewBtn.innerHTML = `
              <i class="fas fa-plus"></i>
              <span>Aggiungi un nuovo indirizzo</span>
          `;
          addNewBtn.onclick = showAddressForm;
          addressContainer.appendChild(addNewBtn);
      }
        
        // Cambia metodo di pagamento
        document.querySelectorAll('input[name="metodo_pagamento"]').forEach(radio => {
            radio.addEventListener('change', function() {
                document.querySelectorAll('.card-element').forEach(el => {
                    el.style.display = 'none';
                });
                
                if (this.id === 'credit-card') {
                    document.getElementById('creditCardForm').style.display = 'block';
                } else if (this.id === 'paypal') {
                    document.getElementById('paypalForm').style.display = 'block';
                    initPayPalButton();
                } else if (this.id === 'bank-transfer') {
                    document.getElementById('bankTransferForm').style.display = 'block';
                }
            });
        });
    
    </script>
</body>
</html>
<?php
session_start();
require_once 'php/functions/collegamento_db.php';
require_once 'php/functions/details.php';
require_once 'php/functions/products.php';
require_once 'php/functions/orders.php';

// Verifica che l'utente sia loggato
if (!isset($_SESSION['user'])) {
    header('Location: login.php?redirect=checkout.php');
    exit();
}

$user_id = $_SESSION['user'];

$pdo = pdoDB();
$ordine = get_onGoingOrder($pdo, $user_id);

if ($ordine->rowCount() <= 0) {
    $empty_cart = true;
} else {
    $ordine = $ordine->fetch(PDO::FETCH_ASSOC);
    $id_ordine = $ordine['id'];
    $dettagli = getDetail($pdo, $id_ordine);
    
    if ($dettagli->rowCount() <= 0) {
        $empty_cart = true;
    } else {
        $empty_cart = false;
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
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrello - YOURnature</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/carrello.css">
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>YOURnature</span>
        </div>
        
        <div class="auth-buttons">
            <a href="home.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Torna allo shopping</a>
        </div>
    </header>

    <main>
        <div class="cart-container">
            <div class="cart-items-container">
                <div class="cart-header">
                    <h2><i class="fas fa-shopping-cart"></i> Il tuo carrello</h2>
                    <span><?php echo isset($prodotti) ? count($prodotti) : 0; ?> articoli</span>
                </div>
                
                <?php if ($empty_cart): ?>
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h3>Il tuo carrello è vuoto</h3>
                        <p>Sembra che tu non abbia ancora aggiunto nessun prodotto al carrello.</p>
                        <a href="home.php" class="btn btn-primary" style="padding: 12px 30px;">
                            <i class="fas fa-store"></i> Inizia a fare acquisti
                        </a>
                    </div>
                <?php else: ?>
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Prodotto</th>
                                <th>Prezzo</th>
                                <th>Quantità</th>
                                <th>Totale</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($prodotti as $prodotto): ?>
                                <tr>
                                    <td data-label="Prodotto">
                                        <div style="display: flex; align-items: center; gap: 15px;">
                                            <img src="<?php echo htmlspecialchars($prodotto['immagine']); ?>" alt="<?php echo htmlspecialchars($prodotto['nome']); ?>" class="cart-item-image">
                                            <span class="cart-item-name"><?php echo htmlspecialchars($prodotto['nome']); ?></span>
                                        </div>
                                    </td>
                                    <td data-label="Prezzo" class="cart-item-price">€<?php echo number_format($prodotto['prezzo'], 2); ?></td>
                                    <td data-label="Quantità">
                                        <div class="quantity-control">
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $prodotto['id']; ?>, -1)">-</button>
                                            <input type="text" class="quantity-input" value="<?php echo $prodotto['quantita']; ?>" readonly>
                                            <button class="quantity-btn" onclick="updateQuantity(<?php echo $prodotto['id']; ?>, 1)">+</button>
                                        </div>
                                    </td>
                                    <td data-label="Totale" class="cart-item-price">€<?php echo number_format($prodotto['subtotale'], 2); ?></td>
                                    <td>
                                        <span class="remove-item" onclick="removeItem(<?php echo $prodotto['id']; ?>)">
                                            <i class="fas fa-trash-alt"></i> Rimuovi
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <div class="cart-summary">
                        <div class="summary-row">
                            <span>Subtotale</span>
                            <span>€<?php echo number_format($totale, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Spedizione</span>
                            <span>Gratuita</span>
                        </div>
                        <div class="summary-row summary-total">
                            <span>Totale</span>
                            <span>€<?php echo number_format($totale, 2); ?></span>
                        </div>
                        
                        <button class="checkout-btn" onclick="window.location.href='checkout.php'">
                            <i class="fas fa-credit-card"></i> Procedi al checkout
                        </button>
                    </div>
                <?php endif; ?>
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
        function updateQuantity(productId, change) {
            // Implementa la logica per aggiornare la quantità
            console.log('Aggiorna quantità:', productId, change);
            // Invia una richiesta AJAX al server per aggiornare la quantità
            // Ricarica la pagina o aggiorna solo gli elementi necessari
        }
        
        function removeItem(productId) {
            // Implementa la logica per rimuovere l'articolo
            if (confirm('Sei sicuro di voler rimuovere questo articolo dal carrello?')) {
                console.log('Rimuovi articolo:', productId);
                // Invia una richiesta AJAX al server per rimuovere l'articolo
                // Ricarica la pagina o aggiorna solo gli elementi necessari
            }
        }
    </script>
</body>
</html>
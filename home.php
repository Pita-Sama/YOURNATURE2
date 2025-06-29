<?php
require_once "php/functions/collegamento_db.php";
require_once "php/functions/products.php";
require_once "php/functions/categories.php";

session_start();

$datiSessione = [
   'id' => $_SESSION['user'],
   'start_time' => $_SESSION['start_time']
]; 

?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiori Ribelli - Piante e Accessori per il Giardinaggio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/home2.css">
</head>
<body>
    <header>
        <div class="logo">
            <i class="fas fa-leaf"></i>
            <span>YOURnature</span>
        </div>
        
        <div class="contact-info">
            <span onclick="copyToClipboard(this)"><i class="fas fa-envelope"></i> info@YOURnature.it</span>
            <span><i class="fas fa-phone-alt"></i> 333 2261466</span>
        </div>
    
        <?php if (isset($_SESSION['user'])): ?>
            <!-- Menu a tendina se l'utente è loggato -->
            <div class="dropdown">
                <button class="btn btn-outline dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['username']); ?>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="profilo.php"><i class="fas fa-user"></i> Profilo</a></li>
                    <li><a class="dropdown-item" href="carrello.php"><i class="fas fa-shopping-bag"></i> Ordini</a></li>
                    <li><a class="dropdown-item" href="php/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <!-- Se non c'è sessione, mostra login -->
            <div class="auth-buttons">
                <a href="login.php" class="btn btn-outline"><i class="fas fa-sign-in-alt"></i> Accedi</a>
                <a href="register.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Registrati</a>
            </div>
        <?php endif; ?>
    </header>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Scopri il Mondo delle Piante</h1>
        <p>Piante uniche, accessori e tutto ciò di cui hai bisogno per il tuo spazio verde</p>
        <a href="#products" class="btn btn-primary" style="padding: 12px 30px;">Esplora la Collezione</a>
    </section>

    <!-- Search Bar -->
    <div class="search-container">
        <div class="search-bar">
            <input type="text" id="ricercaProdotti" placeholder="Cerca piante, vasi, accessori..." onkeyup="filter_byName(this.value)">
            <i class="fas fa-search"></i>
        </div>
    </div>

    <main>        
        <!-- Categories Section -->
        <h2 class="section-title">Le Nostre Categorie</h2>
        <div class="categories">
            <?php
              // Connessione al database
              $pdo = pdoDB();

              // Query per ottenere le categorie uniche
              $categories = getCategories($pdo);
              
              if ($categories->rowCount() > 0) {
                  while ($row = $categories->fetch(PDO::FETCH_ASSOC)) {
                      echo '<div class="category">';
                      echo '<i class="fas fa-seedling"></i>';
                      echo htmlspecialchars($row['nome']);
                      echo '</div>';
                  }
              }
            ?>
        </div>

        <!-- Products Section -->
        <h2 id="products" class="section-title">I Nostri Prodotti</h2>
        <div class="products-grid">
            <?php
              // Query per ottenere tutti i prodotti
              $result_prodotti = getProduct($pdo);

              if ($result_prodotti->rowCount() > 0) {
                  while ($row = $result_prodotti->fetch(PDO::FETCH_ASSOC)) {
                      echo '<div class="product-card">';
                      echo '<span class="product-badge">Novità</span>';
                      echo '<div class="product-image-container">';
                      echo '<img src="' . htmlspecialchars($row['immagine']) . '" alt="' . htmlspecialchars($row['nome']) . '" class="product-image">';
                      echo '</div>';
                      echo '<div class="product-info">';
                      echo '<h3 class="product-name">' . htmlspecialchars($row['nome']) . '</h3>';
                      echo '<p class="product-description">' . htmlspecialchars($row['descrizione']) . '</p>';
                      echo '<div class="product-price">€' . number_format($row['prezzo'], 2) . '</div>';
                      echo '<div class="product-actions">';
                      echo '<button class="add-to-cart-btn" onclick="addToCart(' . $row['id'] . ', \'' . htmlspecialchars($row['nome']) . '\', ' . $row['prezzo'] . ', \'' . htmlspecialchars($row['immagine']) . '\')">';
                      echo '<i class="fas fa-cart-plus"></i> Aggiungi';
                      echo '</button>';
                      echo '<button class="wishlist-btn"><i class="far fa-heart"></i></button>';
                      echo '</div>';
                      echo '</div>';
                      echo '</div>';
                  }
              }
            ?>
        </div>

        <!-- Features Section -->
        <h2 class="section-title">Perché Scegliere Noi</h2>
        <div class="features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-truck"></i>
                </div>
                <h3 class="feature-title">Spedizione Veloce</h3>
                <p class="feature-text">Consegna in tutta Italia in 2-3 giorni lavorativi con imballaggio speciale per proteggere le tue piante.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-seedling"></i>
                </div>
                <h3 class="feature-title">Piante di Qualità</h3>
                <p class="feature-text">Coltiviamo con passione ogni pianta per garantirti prodotti sani e rigogliosi.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-headset"></i>
                </div>
                <h3 class="feature-title">Supporto Esperto</h3>
                <p class="feature-text">Il nostro team di esperti è sempre disponibile per consigli sulla cura delle tue piante.</p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-recycle"></i>
                </div>
                <h3 class="feature-title">Imballaggi Sostenibili</h3>
                <p class="feature-text">Utilizziamo materiali riciclati e biodegradabili per ridurre il nostro impatto ambientale.</p>
            </div>
        </div>
    </main>

    <!-- Cart Icon -->
    <div class="cart-icon" onclick="toggleCart()">
        <i class="fas fa-shopping-cart"></i>
        <span class="cart-count" id="cartCount">0</span>
    </div>

    <!-- Sidebar Carrello -->
    <div class="cart-sidebar" id="cartSidebar">
        <div class="cart-header">
            <h2><i class="fas fa-shopping-cart"></i> Il tuo carrello</h2>
            <button class="close-cart" onclick="toggleCart()">×</button>
        </div>
        <div class="cart-items" id="cartItems">
            <!-- Gli elementi del carrello verranno aggiunti qui dinamicamente -->
            <div class="empty-cart-message" style="text-align: center; padding: 40px 0;">
                <i class="fas fa-shopping-cart" style="font-size: 3rem; color: #bdbdbd; margin-bottom: 20px;"></i>
                <p style="color: #757575;">Il tuo carrello è vuoto</p>
                <a href="#products" class="btn btn-primary" style="margin-top: 20px;" onclick="toggleCart()">Inizia ad acquistare</a>
            </div>
        </div>
        <div class="cart-total" style="display: none;">
            Totale: <span id="cartTotal">€0,00</span>
        </div>
        <button class="checkout-btn" style="display: none;" onclick="goToCheckout()">Procedi all'acquisto</button>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/js-cookie@3/dist/js.cookie.min.js"></script>
    <script>
    // Variabile globale per il carrello
    const SESSION_DATA = <?php echo json_encode($datiSessione, JSON_HEX_TAG); ?>;
    let cart = [];
    let cartTotal = 0;
    let firstTime = true;

    (async () => {
        cart = await getCart();
        if (cart.length !== 0) {
            updateCartDisplay();
        }
    })();

    async function getCart() {
        try {
            if (!SESSION_DATA?.id) {
                const COOKIES = Cookies.get('cart');
                return JSON.parse(COOKIES || '[]');
            } else {
                const response = await setProductDatabase('get');
                return response || '[]';
            }
        } catch (error) {
            console.error("Errore nel recupero del carrello:", error);
            return [];
        }
    }

    function setCookie() {
        var jsonBody = JSON.stringify(cart);

        try {
            Cookies.set('cart', jsonBody, { 
                expires: 7, 
                secure: true,
                sameSite: 'strict' 
            });
            console.log("Cookie salvato correttamente!");
        } catch (error) {
            console.error("Errore nel salvataggio:", error);
        }
    }

    function toggleCart() {
        document.getElementById('cartSidebar').classList.toggle('active');
    }

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const menuToggle = document.querySelector('.menu-toggle');
        const overlay = document.getElementById('overlay');

        sidebar.classList.toggle('active');
        menuToggle.classList.toggle('active');
        overlay.classList.toggle('active');
    }

    async function addToCart(name, price, image) {
        const existingItem = cart.find(item => item.name === name);

        if (existingItem) {
            existingItem.quantity += 1;
        } else {
            cart.push({
                name: name,
                price: price,
                quantity: 1,
                image: image
            });
        }

        cartTotal += price;
        updateCartDisplay();
        updateCartCount();
        showAddToCartFeedback(name);

        try {
            if (!SESSION_DATA?.id) {
                setCookie();
            } else {
                await setProductDatabase('add', name, existingItem);
            }
        } catch (error) {
            console.error("Errore nel salvataggio:", error);
        }
    }

    async function setProductDatabase(action, name = null, existingItem = null) {
        const data = {
            user_id: SESSION_DATA.id,
            action: action
        };

        if(action !== 'get')
            data.product = name;

        if (action === 'add')
            data.quantity = existingItem ? existingItem.quantity : 1;

        try {
            const response = await fetch('php/gestioneProdotti.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams(data).toString()
            });

            if (!response.ok) throw new Error("Errore nel server");
            const responseText = await response.text();
            const result = JSON.parse(responseText);
            return result;
        } catch (error) {
            console.error("Errore durante l'operazione:", error);
            throw error;
        }
    }

    function showAddToCartFeedback(productName) {
        const feedback = document.createElement('div');
        feedback.className = 'add-to-cart-feedback';
        feedback.textContent = `${productName} aggiunto al carrello!`;
        document.body.appendChild(feedback);

        // Animazione
        setTimeout(() => {
            feedback.style.opacity = '1';
            feedback.style.bottom = '100px';
        }, 10);

        // Rimuovi dopo 3 secondi
        setTimeout(() => {
            feedback.style.opacity = '0';
            feedback.style.bottom = '80px';
            setTimeout(() => {
                document.body.removeChild(feedback);
            }, 300);
        }, 3000);
    }

    function updateCartDisplay() {
        const cartItemsContainer = document.getElementById('cartItems');
        const cartTotalElement = document.getElementById('cartTotal');

        // Svuota il contenitore
        cartItemsContainer.innerHTML = '';

        // Aggiungi ogni elemento del carrello
        cart.forEach((item, index) => {
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.innerHTML = `
                <img src="${item.image}" alt="${item.name}" class="cart-item-image">
                <div class="cart-item-details">
                    <div class="cart-item-name">${item.name}</div>
                    <div class="cart-item-price">€${item.price}</div>
                    <div class="cart-item-quantity">
                        <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">-</button>
                        <input type="text" class="quantity-input" value="${item.quantity}" readonly>
                        <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                    <span class="remove-item" onclick="removeItem(${index})">Rimuovi</span>
                </div>
            `;
            if(firstTime){
                cartTotal += item.price * item.quantity;
                updateCartCount();
            }
            cartItemsContainer.appendChild(itemElement);
        });

        firstTime = false;

        // Aggiorna il totale
        cartTotalElement.textContent = `€${cartTotal.toFixed(2)}`;
    }

    function updateCartCount() {
        const count = cart.reduce((total, item) => total + item.quantity, 0);
        const cartCountElement = document.getElementById('cartCount');

        cartCountElement.textContent = count;
        cartCountElement.style.display = count > 0 ? 'flex' : 'none';
    }

    async function changeQuantity(index, change) {
        const item = cart[index];
        const newQuantity = item.quantity + change;

        if (newQuantity < 1) {
            removeItem(index);
            return;
        }

        // Aggiorna il totale
        cartTotal += change * item.price;

        // Aggiorna la quantità
        item.quantity = newQuantity;

        // Aggiorna la visualizzazione
        updateCartDisplay();
        updateCartCount();

        if (!SESSION_DATA?.id) 
            setCookie();
        else 
            await setProductDatabase('add', item.name, item);
    }

    async function removeItem(index) {
        const item = cart[index];

        // Aggiorna il totale
        cartTotal -= item.price * item.quantity;

        // Rimuovi l'elemento dall'array
        cart.splice(index, 1);

        // Aggiorna la visualizzazione
        updateCartDisplay();
        updateCartCount();

        // Se il carrello è vuoto, chiudilo
        if (cart.length === 0) {
            toggleCart();
        }

        if (!SESSION_DATA?.id) {
            setCookie();
        } else {
            var result = await setProductDatabase('remove', item.name);
            result = JSON.parse(result)
            console.log(result.quantita);
        }
    }

    function goToCheckout() {
      if (cart.length === 0) {
          alert('Il carrello è vuoto!');
          return;
      }

    	// Reindirizzamento alla pagina di checkout
      window.location.href = 'checkout.php';
    }

    function copyToClipboard(element) {
        const text = element.textContent;
        navigator.clipboard.writeText(text)
            .then(() => {
                const originalColor = element.style.color;
                element.style.color = "#a0a0a0";
                setTimeout(() => {
                    element.style.color = originalColor;
                }, 500);
            });
    }

    async function filterByCategory(category) {
        document.querySelector('.products-grid').innerHTML = '<p>Caricamento prodotti...</p>';

        try {
            const response = await fetch('php/filter_products_category.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'category=' + encodeURIComponent(category)
            });

            if (!response.ok) throw new Error("Errore nel server");
            document.querySelector('.products-grid').innerHTML = await response.text();
        } catch (error) {
            console.error("Errore nel filtro:", error);
            document.querySelector('.products-grid').innerHTML = '<p>Errore nel caricamento dei prodotti</p>';
        }
    }

    async function filter_byName(name) {
        try {
            const response = await fetch("php/filter_byName.php", {
                method: "POST",
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'name=' + encodeURIComponent(name)
            });

            if (!response.ok) throw new Error("Errore nel server");
            document.querySelector('.products-grid').innerHTML = await response.text();
        } catch (error) {
            console.error("Errore nella ricerca:", error);
            document.querySelector('.products-grid').innerHTML = '<p>Errore nel caricamento dei prodotti</p>';
        }
    }

    document.addEventListener('DOMContentLoaded', async function() {
        const categories = document.querySelectorAll('.category');
        categories.forEach(category => {
            category.addEventListener('click', async function() {
                categories.forEach(c => c.classList.remove('active'));
                this.classList.add('active');
                await filterByCategory(this.textContent);
            });
        });

        await filterByCategory('all');
    });
</script>
</body>
</html>
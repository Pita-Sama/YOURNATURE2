<?php
require_once("functions/collegamento_db.php");
require_once("functions/products.php");


try {
    $pdo = pdoDB();
    
    // Ottieni la categoria dalla richiesta POST
    $category = $_POST['category'] ?? 'all';
    $prodotticategoria = prodottiPerCategoria($pdo,$category);
    $tabellaProdotti = getTableProduct($prodotticategoria);
    echo $tabellaProdotti;
    
} catch (PDOException $e) {
    echo '<p>Errore nel caricamento dei prodotti: ' . htmlspecialchars($e->getMessage()) . '</p>';
}


?>

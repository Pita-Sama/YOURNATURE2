<?php
    require_once ('functions/collegamento_db.php');
	require_once ('functions/products.php');

    if(isset($_POST["name"])){
        try {
        	$pdo = pdoDb();
        	$nome = $_POST["name"];
            $prodotti = searchProductsByName($pdo,$nome);
            $tabella = getTableProduct($prodotti);
            echo $tabella;
        } catch (PDOException $e) {
            echo "Errore nel database: " . $e->getMessage();
        }
    }
    
?>

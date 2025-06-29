<?php
function getProductById($pdo, $productId) {
    $query = $pdo->prepare("SELECT * FROM prodotti WHERE id = ?");
    $query->execute([$productId]);
    return $query;
}

function getProduct($pdo){
	$query = $pdo->query("SELECT * FROM prodotti");
    return $query;
}

function getProductByName($pdo, $productName) {
    $query = $pdo->prepare("SELECT * FROM prodotti WHERE nome = ?");
    $query->execute([$productName]);
    return $query;
}

function getProductByCategory($pdo,$category){
	$sql = "SELECT * 
        		FROM prodotti
                WHERE id_categoria = (SELECT id
                						FROM categorie 
                                        WHERE nome = :category)";
    $query = $pdo->prepare($sql);
    $query->bindParam(":category", $category);
    $query -> execute();
    return $query;
}

function getTableProduct($prodotti){
		if($prodotti -> rowCount() > 0){
          $tabella = "";

          while($row = $prodotti->fetch(PDO::FETCH_ASSOC)) {
              $tabella .= '
              <div class="product-card">
                  <img src="' . htmlspecialchars($row["immagine"]) . '" alt="' . htmlspecialchars($row["nome"]) . '" class="product-image">
                  <div class="product-info">
                      <div class="product-name">' . htmlspecialchars($row["nome"]) . '</div>
                      <div class="product-description">' . htmlspecialchars($row["descrizione"]) . '</div>
                      <div class="product-price">€' . number_format($row["prezzo"], 2, ',', '.') . '</div>
                      <button class="add-to-cart-btn" onclick="addToCart(\'' . addslashes($row["nome"]) . '\', ' . $row["prezzo"] . ', \'' . addslashes($row["immagine"]) . '\')">Aggiungi al carrello</button>
                  </div>
              </div>';
          }
        }
        
        else
        	$tabella = "nessun prodotto trovato";
            
        return $tabella;
}

function setProduct($nome,$prezzo,$quantity,$image){
    $prodotto_array = array(
        "name" => $nome,
        "price" => $prezzo,
        "quantity" => intval($quantity),
        "image" => $image
    );

    return $prodotto_array;
}

function searchProductsByName($pdo,$nome){
         
        $sql = "SELECT *
                FROM prodotti
                WHERE nome LIKE :nome";

        $query = $pdo->prepare($sql);
        $query->execute(['nome' => $nome . '%']);
        $pdo = null;
        return $query;
}

function prodottiPerCategoria($pdo,$category){
	
    // Prepara la query SQL
    if ($category === 'all') 
        $result = getProduct($pdo);
        
    else 
        $result = getProductByCategory($pdo,$category);
    
    
    $pdo = null;
    return $result;
}

function deleteProduct($pdo,$product_id,$order_id){
	$sql = "DELETE FROM dettagli
    		WHERE id_prodotto = :product_id AND id_ordine = :order_id";
	
    $query = $pdo -> prepare($sql);
    $query -> bindParam(':product_id',$product_id);
    $query -> bindParam(':order_id',$order_id);
    return $query -> execute();
}


?>

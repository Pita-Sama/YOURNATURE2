<?php
function getCategories($pdo){
	$sql = "SELECT DISTINCT nome FROM categorie";
    $query = $pdo->query($sql);
    
    return $query; 
}	

function getTableCategories($categories){
	$table = '';

	while($row = $categories->fetch(PDO::FETCH_ASSOC))
    	$table .= '<div class="category">' . htmlspecialchars($row["nome"]) . '</div>';
    
    echo $table;
}

?>
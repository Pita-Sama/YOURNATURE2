<?php
// Avvia la sessione
session_start();

// Distrugge tutte le variabili di sessione
$_SESSION = array();

// Infine, distrugge la sessione
session_destroy();

// Reindirizza alla pagina di login
header("Location: ../home.php");
exit();

?>
<?php
session_start();
require_once 'php/functions/collegamento_db.php';
require_once 'php/functions/utente.php';
require_once 'php/mail.php';

if(isset($_SESSION['users'])) {
    header("Location: home.php");
    exit;
}

$error = '';
$success = '';

if(isset($_POST['Submit'])){
    
    
    
    $pdo = pdoDB();
    $punti = 0;
    $verifica = 0;
    
    
    $username = trim($_POST['user']);
    $email = trim($_POST['email']);
    $password = trim($_POST['pass']);
   	

    if(empty($username) || empty($email) || empty($password)) {
        $error = 'Tutti i campi sono obbligatori!';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Inserisci un indirizzo email valido!';
    } else {
        $utente = getUtente($pdo,$username,$email);
 
        if($utente -> rowCount() > 0)
        	$error = "email o username già registrati!";
    	
    	else{
        	$random_salt = bin2hex(random_bytes(16));
            $password = password_hash($password . $random_salt, PASSWORD_DEFAULT);

			$newUtente = setUtente($pdo,$username,$email,$password,$random_salt,$punti,$verifica);
            
            if($newUtente){
                $utente = getUtente($pdo,$username,$email);
                $utente = $utente -> fetch();
                
                invioMail($email, $username, $utente['id']);
                header("Location:login.php");
                exit();

            } else{
            	$error = "Errore durante la registrazione. Riprova più tardi";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrazione - Fiori Ribelli</title>
    <link rel="stylesheet" href="css/register.css">
</head>
<body>
    <div class="form">
        <h1>Registrazione</h1>
       
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
       
        <form action="register.php" method="POST" novalidate>
            <div class="input-group">
                <input type="text" name="user" placeholder="Username">
            </div>
                   
            <div class="input-group">
                <input type="email" name="email" placeholder="Email">
            </div>
                   
            <div class="input-group">
                <input type="password" name="pass" id="passwordField" placeholder="Password" required minlength="8">
                <img src="eye-closed.png" class="toggle-password" id="togglePassword" alt="Mostra password">
            </div>
                   
            <button type="submit" name="Submit">Registrati</button>
           
            <a id="reindirizza" href="login.php">Hai già un account? Accedi qui</a>
        </form>
    </div>

    <script>
        	document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordField = document.getElementById('passwordField');
            const toggleIcon = this;
           
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.src = 'eye-open.jpg';
            } else {
                passwordField.type = 'password';
                toggleIcon.src = 'eye-closed.png';
            }
        });
    </script>
</body>
</html>

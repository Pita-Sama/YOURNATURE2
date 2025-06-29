<?php
require_once 'php/functions/collegamento_db.php';
require_once 'php/functions/utente.php';

session_start();



if(isset($_SESSION['user'])) {
    header("Location: home.php");
    exit;
}

$error = '';
$success = '';

if(isset($_POST['submit'])){
    
    $pdo = pdoDB();
   
    $username = trim($_POST['user']);
    $password = trim($_POST['pass']);
   	

    if(empty($username) || empty($password)) {
        $error = 'Tutti i campi sono obbligatori!';
    } else {
    	
        $utente = getUtenteByUsername($pdo,$username);
        if($utente -> rowCount() > 0){
        	$utente = $utente -> fetch(PDO::FETCH_ASSOC);
            if(!$utente["verifica"])
            	$error= "Email non verificata";
            
            elseif(password_verify($password . $utente["salt"], $utente["pass"])){
            	$id = $utente['id'];
            	$_SESSION['user'] = $id;
                $_SESSION['start_time'] = time();
                header("Location: home.php");
                exit();
            }
            else{
            	$error= "username o password inesistente";
            }
        }
        
        else {
        	$error = 'username o password inesistente';
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
        <h1>Login</h1>
       
        <?php if($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
       
        <form action="login.php" method="POST" novalidate>
            <div class="input-group">
                <input type="text" name="user" placeholder="Username">
            </div>
                   
            <div class="input-group">
                <input type="password" name="pass" id="passwordField" placeholder="Password" required minlength="8">
                <img src="eye-closed.png" class="toggle-password" id="togglePassword" alt="Mostra password">
            </div>
                   
            <button type="submit" name="submit">Accedi</button>
           
            <a id="reindirizza" href="register.php">Non hai un account? Registrati qui</a>
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

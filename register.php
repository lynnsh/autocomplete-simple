<?php
require 'DAOUser.php';
$title = 'Register';
$header = "<h2  class='header-reg'>Welcome to the Registration!</h2>";
$back = "<div class='btn btn-back-reg'> <a href='index.php'>Back</a></div>";
$input = 'txt-input-reg';
include_once 'form.html.php';
register();

/**
 * Performs the necessary actions to register the user.
 * For registration, the username should be unique and
 * the password length should be at least 8 characters.
 */
function register() {
    $dao = new DAOUser();
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $userForm = trim($_POST['user']);
        if(!empty($userForm) && strlen($userForm) < 50 && 
                !($dao -> findUser($userForm)['username'])) { 
            $user = htmlentities($userForm);
			$passForm = trim($_POST['pwd']);
            if(!empty($passForm) && strlen($passForm) >= 8 && 
                    strlen($passForm) < 255) {
                $password = htmlentities($passForm);
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $dao -> insertUser($user, $hash);
                session_start();
                session_regenerate_id();
                $_SESSION['user'] = $user;
                header('location: index.php');
            }
            else 
                echo "<p class='error'>Invalid password is provided "
                   . "(the length is too short or too long).</p>"; 
        }  
        else
            echo '<p class="error">Invalid username. '
               . 'Please try another one.</p>'; 
    }
}   
?>
    </body>
</html>
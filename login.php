<?php
require 'DAOUser.php';
$title = 'Login';
$header = "<h2  class='header-login'>Welcome to the Login Page!</h2>";
$back = "<div class='btn btn-back-login'> <a href='index.php'>Back</a></div>";
$input = 'txt-input-login';
include_once 'form.html.php';
login();

/**
 * Performs the necessary actions to login the user.
 * If the user tries to login unsuccessfully more than 5 times,
 * (s)he is blocked.
 */
function login() {
    $dao = new DAOUser();
    $invalidMsg = "<p class='error'>Invalid username or password. "
                . "Please try again.</p>";
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        if(!empty($_POST['user']) && !empty($_POST['pwd'])) { 
            $user = htmlentities($_POST['user']);
            $counter = $dao -> getCount($user);
            if($counter >= 5)
                echo "<p class='error'>Too many login attempts. "
                    . 'Unfortunately you are blocked.</p>';
            else {       
                $password = htmlentities($_POST['pwd']);
                if(password_verify($password, $dao -> getPassword($user))) {
                    $dao -> updateCount($user, 0);
                    session_start();
                    session_regenerate_id();
                    $_SESSION['user'] = $user;
                    header('location: index.php');
                } 
                else {
                    echo $invalidMsg; 
                    $dao -> updateCount($user, $counter+1);
                }
            }
        }  
        else
            echo $invalidMsg; 
    }   
}
?>
    </body>
</html>


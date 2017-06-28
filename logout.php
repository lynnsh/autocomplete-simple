<?php
logout();

/**
 * Logs out the user and destroys the session.
 */
function logout() {
    session_start();
    session_regenerate_id();
    if(isset($_SESSION['user'])) {
        setcookie(session_name(), "", time()-42000);
        $_SESSION = [];
        session_destroy();
        header('location: index.php');
    }
}
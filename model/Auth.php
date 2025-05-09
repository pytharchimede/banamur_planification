<?php
class Auth
{
    public static function logout()
    {
        session_start();
        session_unset();
        session_destroy();
        header('Location: login.php');
    }

    public static function isLoggedIn()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return isset($_SESSION['user_id']);
    }
}
